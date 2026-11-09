<?php
/**
 * Módulo de fotos da galeria.
 */
require_once __DIR__ . '/includes/crud.php';

$categorias = crud_opcoes('SELECT id, nome AS rotulo FROM categorias_galeria ORDER BY ordem, id');

crud_executar([
    'tabela'    => 'fotos_galeria',
    'titulo'    => 'Fotos da galeria',
    'singular'  => 'foto',
    'consulta'  => 'SELECT f.*, c.nome AS categoria FROM fotos_galeria f INNER JOIN categorias_galeria c ON c.id = f.categoria_id',
    'ordenacao' => 'f.ordem, f.id',
    'busca'     => ['f.legenda', 'f.texto_alt'],
    'filtros'   => [
        'categoria' => ['rotulo' => 'Categoria', 'coluna' => 'f.categoria_id', 'opcoes' => $categorias],
        'ativo'     => ['rotulo' => 'Situação', 'coluna' => 'f.ativo', 'opcoes' => ['1' => 'Ativas', '0' => 'Inativas']],
    ],
    'colunas' => [
        'imagem'    => ['rotulo' => 'Foto', 'tipo' => 'imagem'],
        'legenda'   => ['rotulo' => 'Legenda'],
        'categoria' => ['rotulo' => 'Categoria'],
        'ordem'     => ['rotulo' => 'Ordem'],
        'ativo'     => ['rotulo' => 'Ativa', 'tipo' => 'booleano'],
    ],
    'campos' => [
        'legenda'      => ['rotulo' => 'Legenda', 'obrigatorio' => true, 'max' => 120, 'largura' => 'col-md-6'],
        'texto_alt'    => ['rotulo' => 'Descrição da imagem (acessibilidade)', 'obrigatorio' => true, 'max' => 160, 'largura' => 'col-md-6'],
        'categoria_id' => ['rotulo' => 'Categoria', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $categorias, 'largura' => 'col-md-6'],
        'ordem'        => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => 'col-md-6'],
        'imagem'       => ['rotulo' => 'Foto', 'tipo' => 'imagem', 'obrigatorio' => true],
        'grande'       => ['rotulo' => 'Exibir em tamanho grande', 'tipo' => 'booleano', 'largura' => 'col-md-6'],
        'ativo'        => ['rotulo' => 'Exibir no site', 'tipo' => 'booleano', 'padrao' => 1, 'largura' => 'col-md-6'],
    ],
]);
