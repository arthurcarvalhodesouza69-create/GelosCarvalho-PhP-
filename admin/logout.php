<?php
/**
 * Encerra a sessão. Aceita apenas POST com token CSRF.
 */
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_valido()) {
    redirecionar('index.php');
}

logout_efetuar();
redirecionar('login.php');
