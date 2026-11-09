<?php
/**
 * Módulo de perguntas frequentes.
 */
require_once __DIR__ . '/includes/crud.php';

crud_executar([
    'tabela'    => 'faqs',
    'titulo'    => 'Perguntas frequentes',
    'singular'  => 'pergunta',
    'ordenacao' => 'ordem, id',
    'busca'     => ['pergunta', 'resposta'],
    'colunas'   => [
        'pergunta' => ['rotulo' => 'Pergunta'],
        'resposta' => ['rotulo' => 'Resposta'],
        'ordem'    => ['rotulo' => 'Ordem'],
        'ativo'    => ['rotulo' => 'Ativa', 'tipo' => 'booleano'],
    ],
    'campos' => [
        'pergunta' => ['rotulo' => 'Pergunta', 'obrigatorio' => true, 'max' => 200],
        'resposta' => ['rotulo' => 'Resposta', 'tipo' => 'textarea', 'obrigatorio' => true, 'max' => 600],
        'ordem'    => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => 'col-md-6'],
        'ativo'    => ['rotulo' => 'Exibir no site', 'tipo' => 'booleano', 'padrao' => 1, 'largura' => 'col-md-6'],
    ],
]);
