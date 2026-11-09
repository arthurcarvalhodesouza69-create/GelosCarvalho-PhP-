<?php
/**
 * Módulo de categorias da galeria.
 */
require_once __DIR__ . '/includes/crud.php';

crud_executar([
    'tabela'    => 'categorias_galeria',
    'titulo'    => 'Categorias da galeria',
    'singular'  => 'categoria',
    'ordenacao' => 'ordem, id',
    'busca'     => ['nome'],
    'colunas'   => [
        'nome'  => ['rotulo' => 'Nome'],
        'ordem' => ['rotulo' => 'Ordem'],
    ],
    'campos' => [
        'nome'  => ['rotulo' => 'Nome', 'obrigatorio' => true, 'max' => 60, 'unico' => true, 'largura' => 'col-md-8'],
        'ordem' => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => 'col-md-4'],
    ],
]);
