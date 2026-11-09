<?php
/**
 * Tela de autenticação do painel.
 */
require_once __DIR__ . '/includes/auth.php';

if (usuario_atual() !== null) {
    redirecionar('index.php');
}

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';

    if (!csrf_valido()) {
        $erro = 'Sua sessão expirou. Tente novamente.';
    } elseif ($email === '' || $senha === '') {
        $erro = 'Informe e-mail e senha.';
    } else {
        $linhas = consulta('SELECT id, senha_hash FROM usuarios WHERE email = ?', [$email]);
        if ($linhas && password_verify($senha, $linhas[0]['senha_hash'])) {
            login_efetuar((int) $linhas[0]['id']);
            redirecionar('index.php');
        }
        // A mesma mensagem é usada para não revelar quais e-mails existem.
        $erro = 'E-mail ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Entrar | Painel Gelos Carvalho</title>
    <link rel="icon" type="image/png" href="../img/Gelos_Carvalho_Logo_Mestre.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="tela-login">
    <main class="caixa-login">
        <img src="../img/Gelos_Carvalho_Logo_Mestre.png" alt="Logo Gelos Carvalho">
        <h1>Painel administrativo</h1>
        <p class="subtitulo">Entre para gerenciar o site</p>

        <?php if ($erro): ?>
            <div class="alert alert-danger py-2" role="alert"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <?= csrf_campo() ?>
            <div class="mb-3">
                <label class="form-label" for="email">E-mail</label>
                <input class="form-control" type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="username" autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label" for="senha">Senha</label>
                <input class="form-control" type="password" id="senha" name="senha" autocomplete="current-password">
            </div>
            <button class="btn btn-primary w-100">Entrar</button>
        </form>
        <p class="text-center mt-3 mb-0 small"><a href="../index.php">Voltar ao site</a></p>
    </main>
</body>
</html>
