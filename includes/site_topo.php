<?php
/**
 * Cabeçalho das páginas públicas.
 * Espera o array $pagina com: titulo, descricao, ativo, audio e, opcionalmente, css.
 */
$menu = [
    'index.php'    => 'Início',
    'sobre.php'    => 'Sobre',
    'servicos.php' => 'Serviços',
    'galeria.php'  => 'Galeria',
    'contato.php'  => 'Contato',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($pagina['descricao']) ?>">
    <meta name="author" content="Arthur Carvalho de Souza">
    <title><?= e($pagina['titulo']) ?></title>
    <link rel="icon" type="image/png" href="img/Gelos_Carvalho_Logo_Mestre.png">
    <link rel="stylesheet" href="css/style.css">
<?php if (!empty($pagina['css'])): ?>
    <link rel="stylesheet" href="<?= e($pagina['css']) ?>">
<?php endif; ?>
</head>
<body>
    <audio autoplay loop> <source src="audio/<?= e($pagina['audio']) ?>" type="audio/mpeg"> </audio>

    <!-- ======== HEADER ======== -->
    <header>
        <div class="header-inner">
            <a href="index.php" class="logo">
                <div class="logo-icone"><img src="img/Gelos_Carvalho_Logo_Mestre.png" alt="logo_gelo"></div>
                <div class="logo-texto">
                    <span class="logo-nome">Gelos Carvalho</span>
                    <span class="logo-slogan">Frescor que você sente</span>
                </div>
            </a>
            <nav>
                <ul>
<?php foreach ($menu as $arquivo => $rotulo): ?>
                    <li><a href="<?= $arquivo ?>"<?= $pagina['ativo'] === $arquivo ? ' class="ativo"' : '' ?>><?= $rotulo ?></a></li>
<?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>
