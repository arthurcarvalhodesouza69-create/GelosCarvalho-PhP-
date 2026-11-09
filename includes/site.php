<?php
/**
 * Inicialização das páginas públicas e consultas de conteúdo.
 */
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

/** Devolve as configurações da empresa no formato chave => valor. */
function config_site(): array
{
    static $config = null;
    if ($config === null) {
        $config = array_column(consulta('SELECT chave, valor FROM configuracoes'), 'valor', 'chave');
    }
    return $config;
}

/** Lê uma configuração específica. */
function cfg(string $chave): string
{
    return config_site()[$chave] ?? '';
}

/** Devolve os produtos ativos com seus tópicos agrupados por tipo. */
function produtos_ativos(): array
{
    static $produtos = null;
    if ($produtos !== null) {
        return $produtos;
    }

    $produtos = [];
    foreach (consulta('SELECT * FROM produtos WHERE ativo = 1 ORDER BY ordem, id') as $produto) {
        $produto['itens'] = ['destaque' => [], 'caracteristica' => [], 'indicacao' => []];
        $produtos[$produto['id']] = $produto;
    }
    foreach (consulta('SELECT produto_id, tipo, texto FROM produto_itens ORDER BY ordem, id') as $item) {
        if (isset($produtos[$item['produto_id']])) {
            $produtos[$item['produto_id']]['itens'][$item['tipo']][] = $item['texto'];
        }
    }
    return $produtos;
}

function diferenciais_da_secao(string $secao): array
{
    return consulta('SELECT * FROM diferenciais WHERE ativo = 1 AND secao = ? ORDER BY ordem, id', [$secao]);
}

function estatisticas_da_pagina(string $pagina): array
{
    return consulta('SELECT * FROM estatisticas WHERE pagina = ? ORDER BY ordem, id', [$pagina]);
}
