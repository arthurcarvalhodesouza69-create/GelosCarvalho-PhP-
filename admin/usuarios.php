<?php
/**
 * CRUD de usuários do painel (restrito a administradores).
 * Tem página própria porque envolve regras de senha e proteção de contas.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';

exigir_admin();

const USUARIOS_POR_PAGINA = 10;
const SENHA_TAMANHO_MINIMO = 8;
const PAPEIS = ['admin' => 'Administrador', 'editor' => 'Editor'];

$acao = $_GET['acao'] ?? 'listar';
$id = (int) ($_GET['id'] ?? 0);
$erros = [];
$dados = ['nome' => '', 'email' => '', 'papel' => 'editor'];

function ultimo_admin(int $id): bool
{
    return (int) consulta_valor("SELECT COUNT(*) FROM usuarios WHERE papel = 'admin' AND id <> ?", [$id]) === 0;
}

function buscar_usuario(int $id): ?array
{
    return consulta('SELECT id, nome, email, papel FROM usuarios WHERE id = ?', [$id])[0] ?? null;
}

function campo_texto(string $nome): string
{
    return is_string($_POST[$nome] ?? null) ? trim($_POST[$nome]) : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf();

    // ── Exclusão ──
    if (($_POST['operacao'] ?? '') === 'excluir') {
        $alvo = buscar_usuario((int) ($_POST['id'] ?? 0));

        if ($alvo === null) {
            flash('danger', 'Usuário não encontrado.');
        } elseif ((int) $alvo['id'] === (int) usuario_atual()['id']) {
            flash('danger', 'Você não pode excluir o próprio usuário.');
        } elseif ($alvo['papel'] === 'admin' && ultimo_admin((int) $alvo['id'])) {
            flash('danger', 'O sistema precisa de pelo menos um administrador.');
        } else {
            db()->prepare('DELETE FROM usuarios WHERE id = ?')->execute([$alvo['id']]);
            flash('success', 'Usuário excluído com sucesso.');
        }
        redirecionar('usuarios.php');
    }

    // ── Validação do cadastro/edição ──
    $existente = $id > 0 ? buscar_usuario($id) : null;
    if ($id > 0 && $existente === null) {
        flash('danger', 'Usuário não encontrado.');
        redirecionar('usuarios.php');
    }

    $dados = ['nome' => campo_texto('nome'), 'email' => mb_strtolower(campo_texto('email')), 'papel' => campo_texto('papel')];
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
    $confirmacao = is_string($_POST['confirmacao'] ?? null) ? $_POST['confirmacao'] : '';

    if ($dados['nome'] === '') {
        $erros['nome'] = 'Preencha o nome.';
    } elseif (mb_strlen($dados['nome']) > 120) {
        $erros['nome'] = 'O nome aceita no máximo 120 caracteres.';
    }

    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($dados['email']) > 160) {
        $erros['email'] = 'Informe um e-mail válido.';
    } elseif ((int) consulta_valor('SELECT COUNT(*) FROM usuarios WHERE email = ? AND id <> ?', [$dados['email'], $id]) > 0) {
        $erros['email'] = 'Este e-mail já está cadastrado.';
    }

    if (!isset(PAPEIS[$dados['papel']])) {
        $erros['papel'] = 'Selecione um perfil válido.';
    } elseif ($existente && $existente['papel'] === 'admin' && $dados['papel'] !== 'admin' && ultimo_admin($id)) {
        $erros['papel'] = 'O sistema precisa de pelo menos um administrador.';
    }

    // Na edição, a senha só muda se algo for digitado.
    if ($id === 0 || $senha !== '' || $confirmacao !== '') {
        if (strlen($senha) < SENHA_TAMANHO_MINIMO) {
            $erros['senha'] = 'A senha deve ter pelo menos ' . SENHA_TAMANHO_MINIMO . ' caracteres.';
        } elseif ($senha !== $confirmacao) {
            $erros['confirmacao'] = 'As senhas não coincidem.';
        }
    }

    // ── Gravação ──
    if (!$erros) {
        try {
            if ($id > 0) {
                db()->prepare('UPDATE usuarios SET nome = ?, email = ?, papel = ? WHERE id = ?')
                    ->execute([$dados['nome'], $dados['email'], $dados['papel'], $id]);
                if ($senha !== '') {
                    db()->prepare('UPDATE usuarios SET senha_hash = ? WHERE id = ?')
                        ->execute([password_hash($senha, PASSWORD_DEFAULT), $id]);
                }
                flash('success', 'Usuário atualizado com sucesso.');
            } else {
                db()->prepare('INSERT INTO usuarios (nome, email, senha_hash, papel) VALUES (?, ?, ?, ?)')
                    ->execute([$dados['nome'], $dados['email'], password_hash($senha, PASSWORD_DEFAULT), $dados['papel']]);
                flash('success', 'Usuário cadastrado com sucesso.');
            }
            redirecionar('usuarios.php');
        } catch (PDOException $erro) {
            if ($erro->getCode() !== '23000') {
                throw $erro;
            }
            // Cobre dois cadastros simultâneos com o mesmo e-mail.
            $erros['email'] = 'Este e-mail já está cadastrado.';
        }
    }
} elseif ($acao === 'editar') {
    $dados = buscar_usuario($id);
    if ($dados === null) {
        flash('danger', 'Usuário não encontrado.');
        redirecionar('usuarios.php');
    }
}

// ── Formulário ──
if ($acao === 'novo' || $acao === 'editar') {
    $classe = fn (string $campo) => isset($erros[$campo]) ? ' is-invalid' : '';
    $mensagem = fn (string $campo) => isset($erros[$campo]) ? '<div class="invalid-feedback">' . e($erros[$campo]) . '</div>' : '';

    admin_topo($acao === 'editar' ? 'Editar usuário' : 'Novo usuário');
    ?>
    <section class="cartao cartao-form">
        <form method="post" novalidate>
            <?= csrf_campo() ?>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="nome">Nome <span class="text-danger">*</span></label>
                    <input class="form-control<?= $classe('nome') ?>" id="nome" name="nome" value="<?= e($dados['nome']) ?>">
                    <?= $mensagem('nome') ?>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="email">E-mail <span class="text-danger">*</span></label>
                    <input class="form-control<?= $classe('email') ?>" type="email" id="email" name="email" value="<?= e($dados['email']) ?>">
                    <?= $mensagem('email') ?>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="papel">Perfil <span class="text-danger">*</span></label>
                    <select class="form-select<?= $classe('papel') ?>" id="papel" name="papel">
                        <?php foreach (PAPEIS as $valor => $rotulo): ?>
                            <option value="<?= $valor ?>" <?= $dados['papel'] === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= $mensagem('papel') ?>
                </div>
                <div class="w-100 m-0"></div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="senha">Senha <?= $acao === 'novo' ? '<span class="text-danger">*</span>' : '' ?></label>
                    <input class="form-control<?= $classe('senha') ?>" type="password" id="senha" name="senha" autocomplete="new-password">
                    <?= $mensagem('senha') ?>
                    <div class="form-text"><?= $acao === 'editar' ? 'Deixe em branco para manter a senha atual. ' : '' ?>Mínimo de <?= SENHA_TAMANHO_MINIMO ?> caracteres.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="confirmacao">Confirmar senha</label>
                    <input class="form-control<?= $classe('confirmacao') ?>" type="password" id="confirmacao" name="confirmacao" autocomplete="new-password">
                    <?= $mensagem('confirmacao') ?>
                </div>
            </div>
            <div class="acoes-form">
                <a href="usuarios.php" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </section>
    <?php
    admin_rodape();
    exit;
}

// ── Listagem ──
$busca = is_string($_GET['busca'] ?? null) ? trim($_GET['busca']) : '';
$where = '';
$parametros = [];
if ($busca !== '') {
    $termo = '%' . addcslashes($busca, '%_\\') . '%';
    $where = ' WHERE nome LIKE ? OR email LIKE ?';
    $parametros = [$termo, $termo];
}

$total = (int) consulta_valor("SELECT COUNT(*) FROM usuarios$where", $parametros);
$totalPaginas = max(1, (int) ceil($total / USUARIOS_POR_PAGINA));
$pagina = min($totalPaginas, max(1, (int) ($_GET['pagina'] ?? 1)));
$inicio = ($pagina - 1) * USUARIOS_POR_PAGINA;
$usuarios = consulta("SELECT id, nome, email, papel, criado_em FROM usuarios$where ORDER BY nome LIMIT " . USUARIOS_POR_PAGINA . " OFFSET $inicio", $parametros);

admin_topo('Usuários');
?>
<section class="cartao">
    <form class="barra-filtros" method="get">
        <input type="search" class="form-control" name="busca" value="<?= e($busca) ?>" placeholder="Buscar por nome ou e-mail..." aria-label="Buscar">
        <button class="btn btn-outline-secondary">Filtrar</button>
        <a href="?acao=novo" class="btn btn-primary ms-auto">Novo usuário</a>
    </form>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Cadastro</th><th class="text-end">Ações</th></tr></thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= e($usuario['nome']) ?></td>
                        <td><?= e($usuario['email']) ?></td>
                        <td><?= e(PAPEIS[$usuario['papel']]) ?></td>
                        <td class="text-nowrap"><?= e(date('d/m/Y', strtotime($usuario['criado_em']))) ?></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-outline-primary btn-sm" href="?acao=editar&amp;id=<?= (int) $usuario['id'] ?>">Editar</a>
                            <?php admin_botao_excluir((int) $usuario['id'], 'este usuário'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$usuarios): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Nenhum usuário encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="rodape-tabela">
        <span class="text-muted small"><?= $total ?> registro(s)</span>
        <?php admin_paginacao($pagina, $totalPaginas); ?>
    </div>
</section>
<?php admin_rodape();
