<?php
/**
 * Estrutura visual comum às páginas do painel.
 */

function admin_menu(): array
{
    $menu = [
        'Geral' => [
            'index.php'     => ['📊', 'Visão geral'],
            'mensagens.php' => ['✉️', 'Mensagens e pedidos'],
        ],
        'Conteúdo do site' => [
            'produtos.php'     => ['🧊', 'Produtos'],
            'fotos.php'        => ['🖼️', 'Fotos da galeria'],
            'categorias.php'   => ['🏷️', 'Categorias da galeria'],
            'equipe.php'       => ['👥', 'Equipe'],
            'depoimentos.php'  => ['⭐', 'Depoimentos'],
            'faqs.php'         => ['❓', 'Perguntas frequentes'],
            'diferenciais.php' => ['💎', 'Diferenciais'],
            'estatisticas.php' => ['🔢', 'Números'],
            'horarios.php'     => ['🕐', 'Horários'],
            'configuracoes.php' => ['⚙️', 'Dados da empresa'],
        ],
    ];

    if (usuario_atual()['papel'] === 'admin') {
        $menu['Administração'] = ['usuarios.php' => ['🔐', 'Usuários']];
    }
    return $menu;
}

function admin_topo(string $titulo): void
{
    $atual   = basename($_SERVER['SCRIPT_NAME']);
    $usuario = usuario_atual();
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title><?= e($titulo) ?> | Painel Gelos Carvalho</title>
    <link rel="icon" type="image/png" href="../img/Gelos_Carvalho_Logo_Mestre.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="painel">
    <aside class="painel-lateral offcanvas-lg offcanvas-start" tabindex="-1" id="menuLateral">
        <a href="index.php" class="painel-marca">
            <img src="../img/Gelos_Carvalho_Logo_Mestre.png" alt="">
            <span>Gelos Carvalho</span>
        </a>
        <nav>
            <?php foreach (admin_menu() as $grupo => $itens): ?>
                <h2><?= e($grupo) ?></h2>
                <?php foreach ($itens as $arquivo => [$icone, $rotulo]): ?>
                    <a href="<?= e($arquivo) ?>" class="<?= $arquivo === $atual ? 'ativo' : '' ?>"><span><?= $icone ?></span><?= e($rotulo) ?></a>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="painel-corpo">
        <header class="painel-topo">
            <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-label="Abrir menu">☰</button>
            <h1><?= e($titulo) ?></h1>
            <div class="painel-usuario">
                <a href="../index.php" target="_blank" class="btn btn-link btn-sm d-none d-md-inline">Ver site</a>
                <span class="d-none d-sm-inline"><?= e($usuario['nome']) ?></span>
                <form method="post" action="logout.php">
                    <?= csrf_campo() ?>
                    <button class="btn btn-outline-secondary btn-sm">Sair</button>
                </form>
            </div>
        </header>
        <main class="painel-conteudo">
            <?php foreach (flash_consumir() as $aviso): ?>
                <div class="alert alert-<?= e($aviso['tipo']) ?>" role="alert"><?= e($aviso['texto']) ?></div>
            <?php endforeach; ?>
    <?php
}

function admin_rodape(): void
{
    ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/admin.js"></script>
</body>
</html>
    <?php
}

/** Exibe a navegação de páginas preservando filtros e busca da URL. */
function admin_paginacao(int $paginaAtual, int $totalPaginas): void
{
    if ($totalPaginas <= 1) {
        return;
    }
    $link = fn (int $pagina) => '?' . e(http_build_query(array_merge($_GET, ['pagina' => $pagina])));
    ?>
    <nav aria-label="Paginação">
        <ul class="pagination pagination-sm justify-content-end mb-0">
            <li class="page-item <?= $paginaAtual <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $link(max(1, $paginaAtual - 1)) ?>">Anterior</a></li>
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= $i === $paginaAtual ? 'active' : '' ?>"><a class="page-link" href="<?= $link($i) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $paginaAtual >= $totalPaginas ? 'disabled' : '' ?>"><a class="page-link" href="<?= $link(min($totalPaginas, $paginaAtual + 1)) ?>">Próxima</a></li>
        </ul>
    </nav>
    <?php
}

/** Botão de exclusão enviado por POST com token CSRF e confirmação. */
function admin_botao_excluir(int $id, string $descricao): void
{
    ?>
    <form method="post" class="d-inline" data-confirmar="Excluir <?= e($descricao) ?>? Esta ação não pode ser desfeita.">
        <?= csrf_campo() ?>
        <input type="hidden" name="operacao" value="excluir">
        <input type="hidden" name="id" value="<?= $id ?>">
        <button class="btn btn-outline-danger btn-sm">Excluir</button>
    </form>
    <?php
}
