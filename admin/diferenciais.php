<?php
/**
 * Módulo dos cartões de diferenciais e valores.
 */
require_once __DIR__ . '/includes/crud.php';

$secoes = ['inicio' => 'Página inicial', 'servicos' => 'Serviços', 'sobre' => 'Sobre (valores)'];

crud_executar([
    'tabela'    => 'diferenciais',
    'titulo'    => 'Diferenciais',
    'singular'  => 'diferencial',
    'ordenacao' => 'secao, ordem, id',
    'busca'     => ['titulo', 'descricao'],
    'filtros'   => ['secao' => ['rotulo' => 'Seção', 'coluna' => 'secao', 'opcoes' => $secoes]],
    'colunas'   => [
        'icone'  => ['rotulo' => 'Ícone'],
        'titulo' => ['rotulo' => 'Título'],
        'secao'  => ['rotulo' => 'Seção', 'tipo' => 'opcao', 'opcoes' => $secoes],
        'ordem'  => ['rotulo' => 'Ordem'],
        'ativo'  => ['rotulo' => 'Ativo', 'tipo' => 'booleano'],
    ],
    'campos' => [
        'titulo'    => ['rotulo' => 'Título', 'obrigatorio' => true, 'max' => 100, 'largura' => 'col-md-6'],
        'icone'     => ['rotulo' => 'Ícone (emoji)', 'obrigatorio' => true, 'max' => 32, 'largura' => 'col-md-6'],
        'secao'     => ['rotulo' => 'Seção', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $secoes, 'largura' => 'col-md-6'],
        'ordem'     => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => 'col-md-6'],
        'descricao' => ['rotulo' => 'Descrição', 'tipo' => 'textarea', 'obrigatorio' => true, 'max' => 400],
        'ativo'     => ['rotulo' => 'Exibir no site', 'tipo' => 'booleano', 'padrao' => 1],
    ],
]);
