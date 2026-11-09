<?php
/**
 * Credenciais do banco de dados.
 * Os valores padrão atendem ao XAMPP; em outro ambiente, defina as
 * variáveis de ambiente DB_HOST, DB_NAME, DB_USER e DB_PASS.
 */
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'gelos_carvalho');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
