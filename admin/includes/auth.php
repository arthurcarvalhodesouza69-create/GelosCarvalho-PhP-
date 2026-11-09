<?php
/**
 * Autenticação e controle de acesso do painel.
 * Todas as páginas protegidas incluem este arquivo e chamam exigir_login().
 */
require_once __DIR__ . '/../../includes/conexao.php';
require_once __DIR__ . '/../../includes/funcoes.php';

sessao_iniciar();

/** Devolve o usuário logado. Os dados vêm do banco para refletir alterações imediatamente. */
function usuario_atual(): ?array
{
    static $usuario = false;

    if ($usuario === false) {
        $usuario = null;
        if (!empty($_SESSION['usuario_id'])) {
            $linhas = consulta('SELECT id, nome, email, papel FROM usuarios WHERE id = ?', [$_SESSION['usuario_id']]);
            $usuario = $linhas[0] ?? null;
        }
    }
    return $usuario;
}

/** Redireciona para o login quem não estiver autenticado. */
function exigir_login(): void
{
    if (usuario_atual() === null) {
        redirecionar('login.php');
    }
}

/** Restringe a página a administradores. */
function exigir_admin(): void
{
    exigir_login();
    if (usuario_atual()['papel'] !== 'admin') {
        flash('danger', 'Apenas administradores podem acessar essa área.');
        redirecionar('index.php');
    }
}

/** Interrompe requisições POST sem token CSRF válido. */
function exigir_csrf(): void
{
    if (!csrf_valido()) {
        flash('danger', 'Sua sessão expirou. Tente novamente.');
        redirecionar(basename($_SERVER['SCRIPT_NAME']));
    }
}

function login_efetuar(int $usuarioId): void
{
    // Um novo identificador impede a fixação de sessão.
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuarioId;
}

function logout_efetuar(): void
{
    $_SESSION = [];
    $cookie = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $cookie['path'],
        'secure'   => $cookie['secure'],
        'httponly' => $cookie['httponly'],
        'samesite' => $cookie['samesite'],
    ]);
    session_destroy();
}
