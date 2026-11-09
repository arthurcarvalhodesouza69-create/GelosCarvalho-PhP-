<?php
/**
 * Funções utilitárias usadas pelo site e pelo painel.
 */

const IMAGEM_TAMANHO_MAXIMO = 5242880; // 5 MB
const IMAGEM_TIPOS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

/** Escapa um valor para exibição segura em HTML. */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function moeda($valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function redirecionar(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}

function sessao_iniciar(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_name('GELOSCMS');
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
        session_start();
    }
}

// ── Proteção contra CSRF ──

function csrf_token(): string
{
    sessao_iniciar();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valido(): bool
{
    $enviado = $_POST['csrf'] ?? '';
    return is_string($enviado) && hash_equals(csrf_token(), $enviado);
}

// ── Mensagens de retorno entre redirecionamentos ──

function flash(string $tipo, string $texto): void
{
    sessao_iniciar();
    $_SESSION['flash'][] = ['tipo' => $tipo, 'texto' => $texto];
}

function flash_consumir(): array
{
    sessao_iniciar();
    $mensagens = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensagens;
}

// ── Upload de imagens ──

/** Valida o arquivo enviado e devolve a mensagem de erro, ou null se estiver correto. */
function imagem_validar(array $arquivo): ?string
{
    if ($arquivo['error'] === UPLOAD_ERR_INI_SIZE || $arquivo['error'] === UPLOAD_ERR_FORM_SIZE) {
        return 'A imagem excede o tamanho máximo de 5 MB.';
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($arquivo['tmp_name'])) {
        return 'Não foi possível receber a imagem.';
    }
    if ($arquivo['size'] > IMAGEM_TAMANHO_MAXIMO) {
        return 'A imagem excede o tamanho máximo de 5 MB.';
    }
    // O tipo é lido do conteúdo do arquivo, e não do nome informado pelo navegador.
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    if (!isset(IMAGEM_TIPOS[$tipo])) {
        return 'Formato inválido. Envie JPG, PNG ou WebP.';
    }
    return null;
}

/** Move a imagem já validada para uploads/ com nome aleatório e devolve o caminho relativo. */
function imagem_salvar(array $arquivo): string
{
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    $nome = bin2hex(random_bytes(16)) . '.' . IMAGEM_TIPOS[$tipo];

    if (!move_uploaded_file($arquivo['tmp_name'], __DIR__ . '/../uploads/' . $nome)) {
        throw new RuntimeException('Falha ao gravar a imagem em uploads/.');
    }
    return 'uploads/' . $nome;
}

/** Remove uma imagem enviada pelo painel. As imagens originais de img/ são preservadas. */
function imagem_remover(?string $caminho): void
{
    if ($caminho && preg_match('#^uploads/[a-f0-9]{32}\.(jpg|png|webp)$#', $caminho)) {
        @unlink(__DIR__ . '/../' . $caminho);
    }
}
