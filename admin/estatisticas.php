<?php
/**
 * Módulo dos números de destaque.
 */
require_once __DIR__ . '/includes/crud.php';

$paginas = ['inicio' => 'Página inicial', 'sobre' => 'Sobre'];

crud_executar([
    'tabela'    => 'estatisticas',
    'titulo'    => 'Números',
    'singular'  => 'número',
    'ordenacao' => 'pagina, ordem, id',
    'busca'     => ['numero', 'rotulo'],
    'filtros'   => ['pagina_site' => ['rotulo' => 'Página', 'coluna' => 'pagina', 'opcoes' => $paginas]],
    'colunas'   => [
        'numero' => ['rotulo' => 'Número'],
        'rotulo' => ['rotulo' => 'Descrição'],
        'pagina' => ['rotulo' => 'Página', 'tipo' => 'opcao', 'opcoes' => $paginas],
        'ordem'  => ['rotulo' => 'Ordem'],
    ],
    'campos' => [
        'numero' => ['rotulo' => 'Número', 'obrigatorio' => true, 'max' => 20, 'ajuda' => 'Exemplos: 500+, 100%, 24H', 'largura' => 'col-md-6'],
        'rotulo' => ['rotulo' => 'Descrição', 'obrigatorio' => true, 'max' => 80, 'largura' => 'col-md-6'],
        'pagina' => ['rotulo' => 'Página', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $paginas, 'largura' => 'col-md-6'],
        'ordem'  => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => 'col-md-6'],
    ],
]);
