<?php
/**
 * Visão geral do painel.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';

exigir_login();

$indicadores = [
    ['mensagens.php?status=nova', 'SELECT COUNT(*) FROM mensagens WHERE status = \'nova\'', 'Mensagens novas'],
    ['produtos.php', 'SELECT COUNT(*) FROM produtos WHERE ativo = 1', 'Produtos ativos'],
    ['fotos.php', 'SELECT COUNT(*) FROM fotos_galeria WHERE ativo = 1', 'Fotos na galeria'],
    ['depoimentos.php', 'SELECT COUNT(*) FROM depoimentos WHERE ativo = 1', 'Depoimentos'],
];

$recentes = consulta('SELECT nome, telefone, mensagem, status, criado_em FROM mensagens ORDER BY criado_em DESC, id DESC LIMIT 5');
$situacoes = ['nova' => 'Nova', 'em_atendimento' => 'Em atendimento', 'concluida' => 'Concluída'];

admin_topo('Visão geral');
?>
<div class="indicadores">
    <?php foreach ($indicadores as [$link, $sql, $rotulo]): ?>
        <a class="indicador" href="<?= e($link) ?>">
            <strong><?= (int) consulta_valor($sql) ?></strong>
            <span><?= e($rotulo) ?></span>
        </a>
    <?php endforeach; ?>
</div>

<section class="cartao">
    <h2 class="cartao-titulo">Últimas mensagens</h2>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Nome</th><th>Telefone</th><th>Mensagem</th><th>Situação</th><th>Recebida em</th></tr></thead>
            <tbody>
                <?php foreach ($recentes as $item): ?>
                    <tr>
                        <td><?= e($item['nome']) ?></td>
                        <td class="text-nowrap"><?= e($item['telefone']) ?></td>
                        <td><?= e(mb_strimwidth($item['mensagem'], 0, 70, '…')) ?></td>
                        <td><?= e($situacoes[$item['status']]) ?></td>
                        <td class="text-nowrap"><?= e(date('d/m/Y H:i', strtotime($item['criado_em']))) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recentes): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma mensagem recebida ainda.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php admin_rodape();
