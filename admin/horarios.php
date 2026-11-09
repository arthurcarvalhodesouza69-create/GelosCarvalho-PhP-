<?php
/**
 * Módulo de horários de funcionamento.
 */
require_once __DIR__ . '/includes/crud.php';

crud_executar([
    'tabela'    => 'horarios',
    'titulo'    => 'Horários',
    'singular'  => 'horário',
    'ordenacao' => 'ordem, id',
    'colunas'   => [
        'dia'     => ['rotulo' => 'Dia'],
        'horario' => ['rotulo' => 'Horário'],
        'aberto'  => ['rotulo' => 'Aberto', 'tipo' => 'booleano'],
        'ordem'   => ['rotulo' => 'Ordem'],
    ],
    'campos' => [
        'dia'     => ['rotulo' => 'Dia', 'obrigatorio' => true, 'max' => 30, 'unico' => true, 'largura' => 'col-md-6'],
        'horario' => ['rotulo' => 'Horário', 'obrigatorio' => true, 'max' => 40, 'largura' => 'col-md-6'],
        'ordem'   => ['rotulo' => 'Ordem de exibição', 'tipo' => 'numero', 'obrigatorio' => true, 'min' => 0, 'max' => 9999, 'padrao' => 0, 'largura' => 'col-md-6'],
        'aberto'  => ['rotulo' => 'Aberto (desligado: "Consultar")', 'tipo' => 'booleano', 'padrao' => 1, 'largura' => 'col-md-6'],
    ],
]);
