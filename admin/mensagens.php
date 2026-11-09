<?php
/**
 * Módulo de mensagens e pedidos recebidos pelo site ou por telefone.
 */
require_once __DIR__ . '/includes/crud.php';

$situacoes = ['nova' => 'Nova', 'em_atendimento' => 'Em atendimento', 'concluida' => 'Concluída'];
$tipos = ['produto' => 'Produto específico', 'ambos' => 'Ambos os tamanhos', 'atacado' => 'Compra em atacado', 'duvida' => 'Dúvida / Informação'];
$produtos = crud_opcoes('SELECT id, nome AS rotulo FROM produtos ORDER BY ordem, id');

crud_executar([
    'tabela'    => 'mensagens',
    'titulo'    => 'Mensagens e pedidos',
    'singular'  => 'mensagem',
    'consulta'  => 'SELECT m.*, p.nome AS produto FROM mensagens m LEFT JOIN produtos p ON p.id = m.produto_id',
    'ordenacao' => 'm.criado_em DESC, m.id DESC',
    'busca'     => ['m.nome', 'm.telefone', 'm.email', 'm.mensagem'],
    'filtros'   => ['status' => ['rotulo' => 'Situação', 'coluna' => 'm.status', 'opcoes' => $situacoes]],
    'colunas'   => [
        'criado_em'  => ['rotulo' => 'Recebida em', 'tipo' => 'data'],
        'nome'       => ['rotulo' => 'Nome'],
        'telefone'   => ['rotulo' => 'Telefone'],
        'tipo'       => ['rotulo' => 'Tipo', 'tipo' => 'opcao', 'opcoes' => $tipos],
        'produto'    => ['rotulo' => 'Produto'],
        'quantidade' => ['rotulo' => 'Qtd.'],
        'status'     => ['rotulo' => 'Situação', 'tipo' => 'opcao', 'opcoes' => $situacoes],
    ],
    'campos' => [
        'nome'       => ['rotulo' => 'Nome', 'obrigatorio' => true, 'max' => 120, 'largura' => 'col-md-6'],
        'telefone'   => ['rotulo' => 'Telefone / WhatsApp', 'obrigatorio' => true, 'max' => 30, 'largura' => 'col-md-6'],
        'email'      => ['rotulo' => 'E-mail', 'tipo' => 'email', 'max' => 160, 'largura' => 'col-md-6'],
        'tipo'       => ['rotulo' => 'Tipo de pedido', 'tipo' => 'select', 'opcoes' => $tipos, 'largura' => 'col-md-6'],
        'produto_id' => ['rotulo' => 'Produto', 'tipo' => 'select', 'opcoes' => $produtos, 'largura' => 'col-md-6'],
        'quantidade' => ['rotulo' => 'Quantidade de pacotes', 'tipo' => 'numero', 'min' => 1, 'max' => 100000, 'largura' => 'col-md-6'],
        'mensagem'   => ['rotulo' => 'Mensagem', 'tipo' => 'textarea', 'obrigatorio' => true, 'max' => 2000],
        'status'     => ['rotulo' => 'Situação', 'tipo' => 'select', 'obrigatorio' => true, 'opcoes' => $situacoes, 'padrao' => 'nova', 'largura' => 'col-md-6'],
    ],
]);
