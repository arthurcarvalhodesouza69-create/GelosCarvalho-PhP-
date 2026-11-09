<?php
/**
 * Módulo de membros da equipe.
 */
require_once __DIR__ . '/includes/crud.php';

crud_executar([
    'tabela'    => 'membros_equipe',
    'titulo'    => 'Equipe',
    'singular'  => 'membro',
    'ordenacao' => 'ordem, id',
    'busca'     => ['nome', 'cargo'],
    'colunas'   => [
        'foto'  => ['rotulo' => 'Foto', 'tipo' => 'imagem'],
        'nome'  => ['rotulo' => 'Nome'],
        'cargo' => ['rotulo' => 'Cargo'],
        'ordem' => ['rotulo' => 'Ordem'],
        'ativo' => ['rotulo' => 'Ativo', 'tipo' => 'booleano'],
    ],
    'campos' => [
        'nome'      => ['rotulo' => 'Nome', 'obrigatorio' => true, 'max' => 120, 'largura' => 'col-md-6'],
        'cargo'     => ['rotulo' => 'Cargo', 'obrigatorio' => true, 'max' => 100, 'largura' => 'col-md-6'],
        'descricao' => ['rotulo' => 'Descrição', 'tipo' => 'textarea', 'obrigatorio' => true, 'max' => 400],
        'foto'      => ['rotulo' => 'Foto', 'tipo' => 'imagem', 'obrigatorio' => true],
        'ordem'     => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => 'col-md-6'],
        'ativo'     => ['rotulo' => 'Exibir no site', 'tipo' => 'booleano', 'padrao' => 1, 'largura' => 'col-md-6'],
    ],
]);
