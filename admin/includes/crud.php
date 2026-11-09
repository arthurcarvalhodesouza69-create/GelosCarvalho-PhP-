<?php
/**
 * Motor de CRUD do painel.
 *
 * Cada módulo (produtos.php, faqs.php etc.) descreve sua tabela em um array de
 * configuração e chama crud_executar(). Assim, inserção, listagem, edição,
 * exclusão, validação, paginação e filtros ficam em um único lugar.
 *
 * Nomes de tabelas e colunas vêm apenas dessa configuração, escrita no código;
 * tudo o que o usuário digita é enviado por parâmetros preparados.
 *
 * Chaves da configuração:
 *   tabela, titulo, singular  Identificação do módulo.
 *   consulta                  SELECT da listagem (opcional, útil para JOIN).
 *   ordenacao                 Cláusula ORDER BY da listagem.
 *   busca                     Colunas pesquisadas pelo campo de busca.
 *   filtros                   nome => [rotulo, coluna, opcoes].
 *   colunas                   coluna => [rotulo, tipo?, opcoes?].
 *   campos                    coluna => [rotulo, tipo, obrigatorio?, max?, min?,
 *                             opcoes?, padrao?, ajuda?, unico?, virtual?, largura?].
 *   ao_carregar, ao_salvar    Funções para campos virtuais (fora da tabela).
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/layout.php';

const CRUD_POR_PAGINA = 10;

function crud_executar(array $cfg): void
{
    exigir_login();

    $acao = $_GET['acao'] ?? 'listar';
    $id   = (int) ($_GET['id'] ?? 0);
    $erros = [];
    $enviados = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        exigir_csrf();

        if (($_POST['operacao'] ?? '') === 'excluir') {
            crud_excluir($cfg, (int) ($_POST['id'] ?? 0));
            redirecionar(basename($_SERVER['SCRIPT_NAME']));
        }

        [$erros, $enviados] = crud_salvar($cfg, $id);
    }

    if ($acao === 'novo' || $acao === 'editar') {
        $registro = $acao === 'editar' ? crud_buscar($cfg, $id) : null;
        if ($acao === 'editar' && $registro === null) {
            flash('danger', 'Registro não encontrado.');
            redirecionar(basename($_SERVER['SCRIPT_NAME']));
        }
        crud_formulario($cfg, $registro, $enviados, $erros);
        return;
    }

    crud_listagem($cfg);
}

function crud_buscar(array $cfg, int $id): ?array
{
    $linhas = consulta("SELECT * FROM {$cfg['tabela']} WHERE id = ?", [$id]);
    if (!$linhas) {
        return null;
    }
    $registro = $linhas[0];
    if (isset($cfg['ao_carregar'])) {
        $registro = $cfg['ao_carregar']($registro);
    }
    return $registro;
}

/** Valida o formulário e devolve [erros, dados, arquivos de imagem a gravar]. */
function crud_validar(array $cfg, int $id): array
{
    $erros = [];
    $dados = [];
    $imagens = [];

    foreach ($cfg['campos'] as $nome => $campo) {
        $tipo = $campo['tipo'] ?? 'texto';
        $rotulo = $campo['rotulo'];

        if ($tipo === 'imagem') {
            $arquivo = $_FILES[$nome] ?? null;
            if ($arquivo && $arquivo['error'] !== UPLOAD_ERR_NO_FILE) {
                $erro = imagem_validar($arquivo);
                if ($erro !== null) {
                    $erros[$nome] = $erro;
                } else {
                    $imagens[$nome] = $arquivo;
                }
            } elseif ($id === 0) {
                $erros[$nome] = "Selecione uma imagem para \"$rotulo\".";
            }
            continue;
        }

        if ($tipo === 'booleano') {
            $dados[$nome] = isset($_POST[$nome]) ? 1 : 0;
            continue;
        }

        $valor = is_string($_POST[$nome] ?? null) ? trim($_POST[$nome]) : '';
        $dados[$nome] = $valor;

        if ($valor === '') {
            if (!empty($campo['obrigatorio'])) {
                $erros[$nome] = "Preencha o campo \"$rotulo\".";
            }
            continue;
        }

        if (isset($campo['max']) && $tipo !== 'numero' && $tipo !== 'decimal' && mb_strlen($valor) > $campo['max']) {
            $erros[$nome] = "\"$rotulo\" aceita no máximo {$campo['max']} caracteres.";
            continue;
        }

        switch ($tipo) {
            case 'email':
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    $erros[$nome] = 'Informe um e-mail válido.';
                }
                break;
            case 'numero':
                $opcoes = array_filter(['min_range' => $campo['min'] ?? null, 'max_range' => $campo['max'] ?? null], 'is_int');
                if (filter_var($valor, FILTER_VALIDATE_INT, ['options' => $opcoes]) === false) {
                    $erros[$nome] = "Informe um número válido em \"$rotulo\".";
                }
                break;
            case 'decimal':
                $numero = str_replace(',', '.', $valor);
                if (!is_numeric($numero) || (float) $numero < 0 || (float) $numero > 999999.99) {
                    $erros[$nome] = "Informe um valor válido em \"$rotulo\".";
                } else {
                    $dados[$nome] = number_format((float) $numero, 2, '.', '');
                }
                break;
            case 'select':
                if (!array_key_exists($valor, $campo['opcoes'])) {
                    $erros[$nome] = "Selecione uma opção válida em \"$rotulo\".";
                }
                break;
        }

        if (!isset($erros[$nome]) && !empty($campo['unico'])) {
            $repetido = consulta_valor("SELECT COUNT(*) FROM {$cfg['tabela']} WHERE $nome = ? AND id <> ?", [$valor, $id]);
            if ($repetido > 0) {
                $erros[$nome] = "Já existe um registro com este valor em \"$rotulo\".";
            }
        }
    }

    return [$erros, $dados, $imagens];
}

/** Insere ou atualiza o registro. Em caso de sucesso, redireciona para a listagem. */
function crud_salvar(array $cfg, int $id): array
{
    $anterior = null;
    if ($id > 0) {
        $linhas = consulta("SELECT * FROM {$cfg['tabela']} WHERE id = ?", [$id]);
        $anterior = $linhas[0] ?? null;
        if ($anterior === null) {
            flash('danger', 'Registro não encontrado.');
            redirecionar(basename($_SERVER['SCRIPT_NAME']));
        }
    }

    [$erros, $dados, $imagens] = crud_validar($cfg, $id);
    if ($erros) {
        return [$erros, $dados];
    }

    // Separa as colunas reais; campos opcionais vazios são gravados como NULL.
    $colunas = [];
    foreach ($cfg['campos'] as $nome => $campo) {
        if (empty($campo['virtual']) && array_key_exists($nome, $dados)) {
            $colunas[$nome] = $dados[$nome] === '' ? null : $dados[$nome];
        }
    }

    $novasImagens = [];
    $pdo = db();

    try {
        foreach ($imagens as $nome => $arquivo) {
            $novasImagens[$nome] = imagem_salvar($arquivo);
            $colunas[$nome] = $novasImagens[$nome];
        }

        $pdo->beginTransaction();

        if ($id > 0) {
            $atribuicoes = implode(', ', array_map(fn ($c) => "$c = ?", array_keys($colunas)));
            $pdo->prepare("UPDATE {$cfg['tabela']} SET $atribuicoes WHERE id = ?")
                ->execute([...array_values($colunas), $id]);
        } else {
            $nomes = implode(', ', array_keys($colunas));
            $marcadores = implode(', ', array_fill(0, count($colunas), '?'));
            $pdo->prepare("INSERT INTO {$cfg['tabela']} ($nomes) VALUES ($marcadores)")
                ->execute(array_values($colunas));
            $id = (int) $pdo->lastInsertId();
        }

        if (isset($cfg['ao_salvar'])) {
            $cfg['ao_salvar']($id, $dados);
        }

        $pdo->commit();
    } catch (Throwable $erro) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        array_map('imagem_remover', $novasImagens);
        error_log('Erro ao salvar em ' . $cfg['tabela'] . ': ' . $erro->getMessage());

        $duplicado = $erro instanceof PDOException && $erro->getCode() === '23000';
        return [['_geral' => $duplicado ? 'Já existe um registro com esses dados.' : 'Não foi possível salvar. Tente novamente.'], $dados];
    }

    // A imagem antiga só é apagada depois que o banco confirma a alteração.
    foreach (array_keys($novasImagens) as $nome) {
        imagem_remover($anterior[$nome] ?? null);
    }

    flash('success', ucfirst($cfg['singular']) . ($anterior ? ' atualizado(a) com sucesso.' : ' cadastrado(a) com sucesso.'));
    redirecionar(basename($_SERVER['SCRIPT_NAME']));
    return [];
}

function crud_excluir(array $cfg, int $id): void
{
    $linhas = consulta("SELECT * FROM {$cfg['tabela']} WHERE id = ?", [$id]);
    if (!$linhas) {
        flash('danger', 'Registro não encontrado.');
        return;
    }

    try {
        db()->prepare("DELETE FROM {$cfg['tabela']} WHERE id = ?")->execute([$id]);
    } catch (PDOException $erro) {
        $emUso = $erro->getCode() === '23000';
        flash('danger', $emUso ? 'Este registro está em uso por outros cadastros e não pode ser excluído.' : 'Não foi possível excluir.');
        return;
    }

    foreach ($cfg['campos'] as $nome => $campo) {
        if (($campo['tipo'] ?? '') === 'imagem') {
            imagem_remover($linhas[0][$nome]);
        }
    }
    flash('success', ucfirst($cfg['singular']) . ' excluído(a) com sucesso.');
}

function crud_listagem(array $cfg): void
{
    $consulta = $cfg['consulta'] ?? "SELECT * FROM {$cfg['tabela']}";
    $condicoes = [];
    $parametros = [];

    $busca = is_string($_GET['busca'] ?? null) ? trim($_GET['busca']) : '';
    if ($busca !== '' && !empty($cfg['busca'])) {
        $partes = [];
        foreach ($cfg['busca'] as $coluna) {
            $partes[] = "$coluna LIKE ?";
            $parametros[] = '%' . addcslashes($busca, '%_\\') . '%';
        }
        $condicoes[] = '(' . implode(' OR ', $partes) . ')';
    }

    $filtrosAtuais = [];
    foreach ($cfg['filtros'] ?? [] as $nome => $filtro) {
        $valor = $_GET[$nome] ?? '';
        if (is_string($valor) && $valor !== '' && array_key_exists($valor, $filtro['opcoes'])) {
            $condicoes[] = "{$filtro['coluna']} = ?";
            $parametros[] = $valor;
            $filtrosAtuais[$nome] = $valor;
        }
    }

    $where = $condicoes ? ' WHERE ' . implode(' AND ', $condicoes) : '';
    $total = (int) consulta_valor("SELECT COUNT(*) FROM ($consulta$where) AS resultado", $parametros);
    $totalPaginas = max(1, (int) ceil($total / CRUD_POR_PAGINA));
    $pagina = min($totalPaginas, max(1, (int) ($_GET['pagina'] ?? 1)));
    $inicio = ($pagina - 1) * CRUD_POR_PAGINA;

    $ordem = isset($cfg['ordenacao']) ? " ORDER BY {$cfg['ordenacao']}" : '';
    $registros = consulta("$consulta$where$ordem LIMIT " . CRUD_POR_PAGINA . " OFFSET $inicio", $parametros);

    admin_topo($cfg['titulo']);
    ?>
    <section class="cartao">
        <form class="barra-filtros" method="get">
            <?php if (!empty($cfg['busca'])): ?>
                <input type="search" class="form-control" name="busca" value="<?= e($busca) ?>" placeholder="Buscar..." aria-label="Buscar">
            <?php endif; ?>
            <?php foreach ($cfg['filtros'] ?? [] as $nome => $filtro): ?>
                <select class="form-select" name="<?= e($nome) ?>" aria-label="<?= e($filtro['rotulo']) ?>">
                    <option value=""><?= e($filtro['rotulo']) ?>: todos</option>
                    <?php foreach ($filtro['opcoes'] as $valor => $rotulo): ?>
                        <option value="<?= e($valor) ?>" <?= (string) ($filtrosAtuais[$nome] ?? '') === (string) $valor ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endforeach; ?>
            <?php if (!empty($cfg['busca']) || !empty($cfg['filtros'])): ?>
                <button class="btn btn-outline-secondary">Filtrar</button>
            <?php endif; ?>
            <a href="?acao=novo" class="btn btn-primary ms-auto">Novo cadastro</a>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <?php foreach ($cfg['colunas'] as $coluna): ?>
                            <th><?= e($coluna['rotulo']) ?></th>
                        <?php endforeach; ?>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $registro): ?>
                        <tr>
                            <?php foreach ($cfg['colunas'] as $nome => $coluna): ?>
                                <td><?= crud_celula($coluna, $registro[$nome] ?? null) ?></td>
                            <?php endforeach; ?>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-outline-primary btn-sm" href="?acao=editar&amp;id=<?= (int) $registro['id'] ?>">Editar</a>
                                <?php admin_botao_excluir((int) $registro['id'], 'este registro'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$registros): ?>
                        <tr><td colspan="<?= count($cfg['colunas']) + 1 ?>" class="text-center text-muted py-4">Nenhum registro encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="rodape-tabela">
            <span class="text-muted small"><?= $total ?> registro(s)</span>
            <?php admin_paginacao($pagina, $totalPaginas); ?>
        </div>
    </section>
    <?php
    admin_rodape();
}

/** Formata o conteúdo de uma célula de acordo com o tipo da coluna. */
function crud_celula(array $coluna, $valor): string
{
    switch ($coluna['tipo'] ?? 'texto') {
        case 'imagem':
            return '<img class="miniatura" src="../' . e($valor) . '" alt="">';
        case 'booleano':
            return $valor ? '<span class="badge text-bg-success">Sim</span>' : '<span class="badge text-bg-secondary">Não</span>';
        case 'moeda':
            return e(moeda($valor));
        case 'opcao':
            return e($coluna['opcoes'][$valor] ?? '—');
        case 'data':
            return e(date('d/m/Y H:i', strtotime((string) $valor)));
        default:
            if ($valor === null || $valor === '') {
                return '—';
            }
            $texto = (string) $valor;
            return e(mb_strlen($texto) > 70 ? mb_substr($texto, 0, 70) . '…' : $texto);
    }
}

function crud_formulario(array $cfg, ?array $registro, ?array $enviados, array $erros): void
{
    admin_topo(($registro ? 'Editar ' : 'Novo cadastro: ') . $cfg['singular']);
    ?>
    <section class="cartao cartao-form">
        <?php if (isset($erros['_geral'])): ?>
            <div class="alert alert-danger"><?= e($erros['_geral']) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" novalidate>
            <?= csrf_campo() ?>
            <div class="row g-3">
                <?php foreach ($cfg['campos'] as $nome => $campo):
                    $padrao = $registro[$nome] ?? ($campo['padrao'] ?? '');
                    crud_campo($nome, $campo, $enviados[$nome] ?? ($enviados === null ? $padrao : ''), $registro[$nome] ?? null, $erros[$nome] ?? null, $enviados !== null);
                endforeach; ?>
            </div>
            <div class="acoes-form">
                <a href="<?= e(basename($_SERVER['SCRIPT_NAME'])) ?>" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </section>
    <?php
    admin_rodape();
}

function crud_campo(string $nome, array $campo, $valor, $original, ?string $erro, bool $reenvio): void
{
    $tipo = $campo['tipo'] ?? 'texto';
    $classeErro = $erro ? ' is-invalid' : '';
    $obrigatorio = !empty($campo['obrigatorio']) ? ' <span class="text-danger">*</span>' : '';
    $id = 'campo_' . $nome;
    ?>
    <div class="col-12 <?= e($campo['largura'] ?? '') ?>">
        <?php if ($tipo === 'booleano'): ?>
            <div class="form-check form-switch mt-md-4">
                <input class="form-check-input" type="checkbox" id="<?= $id ?>" name="<?= e($nome) ?>" value="1" <?= $valor ? 'checked' : '' ?>>
                <label class="form-check-label" for="<?= $id ?>"><?= e($campo['rotulo']) ?></label>
            </div>
        <?php else: ?>
            <label class="form-label" for="<?= $id ?>"><?= e($campo['rotulo']) ?><?= $obrigatorio ?></label>
            <?php if ($tipo === 'textarea' || $tipo === 'linhas'): ?>
                <textarea class="form-control<?= $classeErro ?>" id="<?= $id ?>" name="<?= e($nome) ?>" rows="<?= $tipo === 'linhas' ? 5 : 4 ?>"><?= e($valor) ?></textarea>
            <?php elseif ($tipo === 'select'): ?>
                <select class="form-select<?= $classeErro ?>" id="<?= $id ?>" name="<?= e($nome) ?>">
                    <option value="">Selecione...</option>
                    <?php foreach ($campo['opcoes'] as $opcao => $rotulo): ?>
                        <option value="<?= e($opcao) ?>" <?= (string) $valor === (string) $opcao ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($tipo === 'imagem'): ?>
                <?php if ($original): ?>
                    <img class="previa" src="../<?= e($original) ?>" alt="Imagem atual">
                <?php endif; ?>
                <input class="form-control<?= $classeErro ?>" type="file" id="<?= $id ?>" name="<?= e($nome) ?>" accept="image/jpeg,image/png,image/webp">
                <div class="form-text"><?= $original ? 'Envie um arquivo apenas para substituir a imagem atual. ' : '' ?>JPG, PNG ou WebP, até 5 MB.</div>
            <?php else:
                $tipoHtml = ['email' => 'email', 'numero' => 'number'][$tipo] ?? 'text';
                $exibido = ($tipo === 'decimal' && !$reenvio && is_numeric($valor)) ? number_format((float) $valor, 2, ',', '') : $valor;
                ?>
                <input class="form-control<?= $classeErro ?>" type="<?= $tipoHtml ?>" id="<?= $id ?>" name="<?= e($nome) ?>" value="<?= e($exibido) ?>"<?= $tipo === 'decimal' ? ' inputmode="decimal"' : '' ?>>
            <?php endif; ?>
            <?php if ($erro): ?><div class="invalid-feedback"><?= e($erro) ?></div><?php endif; ?>
            <?php if (!empty($campo['ajuda'])): ?><div class="form-text"><?= e($campo['ajuda']) ?></div><?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}

/** Monta um array id => rótulo para campos de seleção e filtros. */
function crud_opcoes(string $sql): array
{
    return array_column(consulta($sql), 'rotulo', 'id');
}
