<?php
/**
 * Dados gerais da empresa exibidos no rodapé e na página de contato.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';

exigir_login();

$campos = [
    'telefone_exibicao'    => ['Telefone exibido', 'Exemplo: +55 77 92054-5000'],
    'telefone_link'        => ['Telefone para links e WhatsApp', 'Somente números, com DDI e DDD.'],
    'endereco'             => ['Endereço', ''],
    'endereco_rodape'      => ['Endereço no rodapé', ''],
    'regiao'               => ['Região', ''],
    'horario_resumo'       => ['Horário resumido', ''],
    'horario_domingo'      => ['Observação de domingo', ''],
    'area_entrega'         => ['Área de entrega', ''],
    'area_entrega_detalhe' => ['Detalhe da área de entrega', ''],
    'descricao_rodape'     => ['Descrição no rodapé', ''],
];

$valores = array_column(consulta('SELECT chave, valor FROM configuracoes'), 'valor', 'chave');
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf();

    foreach ($campos as $chave => [$rotulo]) {
        $valor = is_string($_POST[$chave] ?? null) ? trim($_POST[$chave]) : '';
        $valores[$chave] = $valor;

        if ($valor === '') {
            $erros[$chave] = "Preencha o campo \"$rotulo\".";
        } elseif (mb_strlen($valor) > 500) {
            $erros[$chave] = 'Use no máximo 500 caracteres.';
        }
    }

    if (!isset($erros['telefone_link']) && !preg_match('/^\d{10,15}$/', $valores['telefone_link'])) {
        $erros['telefone_link'] = 'Use apenas números (10 a 15 dígitos).';
    }

    if (!$erros) {
        $stmt = db()->prepare('INSERT INTO configuracoes (chave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
        foreach (array_keys($campos) as $chave) {
            $stmt->execute([$chave, $valores[$chave]]);
        }
        flash('success', 'Dados da empresa atualizados com sucesso.');
        redirecionar('configuracoes.php');
    }
}

admin_topo('Dados da empresa');
?>
<section class="cartao cartao-form">
    <form method="post" novalidate>
        <?= csrf_campo() ?>
        <div class="row g-3">
            <?php foreach ($campos as $chave => [$rotulo, $ajuda]): ?>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="<?= $chave ?>"><?= e($rotulo) ?> <span class="text-danger">*</span></label>
                    <input class="form-control<?= isset($erros[$chave]) ? ' is-invalid' : '' ?>" id="<?= $chave ?>" name="<?= $chave ?>" value="<?= e($valores[$chave] ?? '') ?>">
                    <?php if (isset($erros[$chave])): ?><div class="invalid-feedback"><?= e($erros[$chave]) ?></div><?php endif; ?>
                    <?php if ($ajuda): ?><div class="form-text"><?= e($ajuda) ?></div><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="acoes-form">
            <button class="btn btn-primary">Salvar</button>
        </div>
    </form>
</section>
<?php admin_rodape();
