<?php
/**
 * Módulo de depoimentos de clientes.
 */
require_once __DIR__ . '/includes/crud.php';

crud_executar([
    'tabela'    => 'depoimentos',
    'titulo'    => 'Depoimentos',
    'singular'  => 'depoimento',
    'ordenacao' => 'ordem, id',
    'busca'     => ['autor', 'localidade', 'texto'],
    'filtros'   => ['ativo' => ['rotulo' => 'Situação', 'coluna' => 'ativo', 'opcoes' => ['1' => 'Ativos', '0' => 'Inativos']]],
    'colunas'   => [
        'autor'      => ['rotulo' => 'Autor'],
        'localidade' => ['rotulo' => 'Localidade'],
        'texto'      => ['rotulo' => 'Depoimento'],
        'nota'       => ['rotulo' => 'Nota'],
        'ativo'      => ['rotulo' => 'Ativo', 'tipo' => 'booleano'],
    ],
    'campos' => [
        'autor'      => ['rotulo' => 'Autor', 'obrigatorio' => true, 'max' => 120, 'largura' => 'col-md-6'],
        'localidade' => ['rotulo' => 'Localidade', 'max' => 100, 'largura' => 'col-md-6'],
        'texto'      => ['rotulo' => 'Depoimento', 'tipo' => 'textarea', 'obrigatorio' => true, 'max' => 600],
        'nota'       => ['rotulo' => 'Nota (1 a 5)', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 1, 'max' => 5, 'padrao' => 5, 'largura' => 'col-md-4'],
        'ordem'      => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => 'col-md-4'],
        'ativo'      => ['rotulo' => 'Exibir no site', 'tipo' => 'booleano', 'padrao' => 1, 'largura' => 'col-md-4'],
    ],
]);
