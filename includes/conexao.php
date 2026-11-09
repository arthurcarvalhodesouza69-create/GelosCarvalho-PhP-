<?php
/**
 * Conexão única com o banco de dados via PDO.
 */
require_once __DIR__ . '/../config/database.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            // O detalhe vai para o log; o visitante não vê dados da conexão.
            error_log('Falha na conexão com o banco: ' . $e->getMessage());
            http_response_code(500);
            exit('Não foi possível conectar ao banco de dados. Verifique se ele foi importado e se o MySQL está ativo.');
        }
    }

    return $pdo;
}

/** Executa uma consulta preparada e devolve todas as linhas. */
function consulta(string $sql, array $parametros = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($parametros);
    return $stmt->fetchAll();
}

/** Executa uma consulta preparada e devolve o primeiro campo da primeira linha. */
function consulta_valor(string $sql, array $parametros = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($parametros);
    return $stmt->fetchColumn();
}
