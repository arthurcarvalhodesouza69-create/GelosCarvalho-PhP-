<?php
/**
 * Módulo de produtos. As listas de tópicos ficam na tabela produto_itens (1:N).
 */
require_once __DIR__ . '/includes/crud.php';

const TIPOS_ITEM = ['destaque' => 'itens_destaque', 'caracteristica' => 'itens_caracteristica', 'indicacao' => 'itens_indicacao'];

$simNao = ['1' => 'Ativos', '0' => 'Inativos'];
$metade = 'col-md-6';
$terco = 'col-md-4';

crud_executar([
    'tabela'    => 'produtos',
    'titulo'    => 'Produtos',
    'singular'  => 'produto',
    'ordenacao' => 'ordem, id',
    'busca'     => ['nome', 'descricao_curta'],
    'filtros'   => ['ativo' => ['rotulo' => 'Situação', 'coluna' => 'ativo', 'opcoes' => $simNao]],
    'colunas'   => [
        'imagem' => ['rotulo' => 'Imagem', 'tipo' => 'imagem'],
        'nome'   => ['rotulo' => 'Nome'],
        'preco'  => ['rotulo' => 'Preço', 'tipo' => 'moeda'],
        'ordem'  => ['rotulo' => 'Ordem'],
        'ativo'  => ['rotulo' => 'Ativo', 'tipo' => 'booleano'],
    ],
    'campos' => [
        'nome'            => ['rotulo' => 'Nome', 'obrigatorio' => true, 'max' => 100, 'unico' => true, 'largura' => $metade],
        'subtitulo'       => ['rotulo' => 'Subtítulo', 'max' => 100, 'largura' => $metade],
        'icone'           => ['rotulo' => 'Ícone (emoji)', 'max' => 16, 'largura' => $terco],
        'preco'           => ['rotulo' => 'Preço (R$)', 'tipo' => 'decimal', 'obrigatorio' => true, 'largura' => $terco],
        'ordem'           => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => $terco],
        'descricao_curta' => ['rotulo' => 'Descrição curta (página inicial)', 'tipo' => 'textarea', 'obrigatorio' => true, 'max' => 255],
        'descricao'       => ['rotulo' => 'Descrição completa (página de serviços)', 'tipo' => 'textarea', 'obrigatorio' => true, 'max' => 2000],
        'imagem'          => ['rotulo' => 'Imagem', 'tipo' => 'imagem', 'obrigatorio' => true],
        'itens_destaque'       => ['rotulo' => 'Destaques (página inicial)', 'tipo' => 'linhas', 'virtual' => true, 'ajuda' => 'Um item por linha.', 'largura' => $terco],
        'itens_caracteristica' => ['rotulo' => 'Características', 'tipo' => 'linhas', 'virtual' => true, 'ajuda' => 'Um item por linha.', 'largura' => $terco],
        'itens_indicacao'      => ['rotulo' => 'Indicado para', 'tipo' => 'linhas', 'virtual' => true, 'ajuda' => 'Um item por linha.', 'largura' => $terco],
        'tamanho'         => ['rotulo' => 'Tamanho', 'max' => 80, 'largura' => $terco],
        'tipo_gelo'       => ['rotulo' => 'Tipo de gelo', 'max' => 80, 'largura' => $terco],
        'agua'            => ['rotulo' => 'Água utilizada', 'max' => 80, 'largura' => $terco],
        'embalagem'       => ['rotulo' => 'Embalagem', 'max' => 80, 'largura' => $terco],
        'ideal_para'      => ['rotulo' => 'Ideal para', 'max' => 80, 'largura' => $terco],
        'higiene'         => ['rotulo' => 'Higiene', 'max' => 80, 'largura' => $terco],
        'disponibilidade' => ['rotulo' => 'Disponibilidade', 'max' => 80, 'largura' => $terco],
        'em_destaque'     => ['rotulo' => 'Botão dourado (produto em destaque)', 'tipo' => 'booleano', 'largura' => $metade],
        'ativo'           => ['rotulo' => 'Exibir no site', 'tipo' => 'booleano', 'padrao' => 1, 'largura' => $metade],
    ],

    // Preenche os campos de tópicos ao abrir a edição.
    'ao_carregar' => function (array $produto): array {
        foreach (TIPOS_ITEM as $tipo => $campo) {
            $textos = consulta('SELECT texto FROM produto_itens WHERE produto_id = ? AND tipo = ? ORDER BY ordem, id', [$produto['id'], $tipo]);
            $produto[$campo] = implode("\n", array_column($textos, 'texto'));
        }
        return $produto;
    },

    // Regrava os tópicos na mesma transação do produto.
    'ao_salvar' => function (int $id, array $dados): void {
        db()->prepare('DELETE FROM produto_itens WHERE produto_id = ?')->execute([$id]);
        $inserir = db()->prepare('INSERT INTO produto_itens (produto_id, tipo, texto, ordem) VALUES (?, ?, ?, ?)');

        foreach (TIPOS_ITEM as $tipo => $campo) {
            $linhas = array_values(array_filter(array_map('trim', preg_split('/\R/u', $dados[$campo])), 'strlen'));
            foreach ($linhas as $ordem => $texto) {
                $inserir->execute([$id, $tipo, mb_substr($texto, 0, 160), $ordem + 1]);
            }
        }
    },
]);
