<?php

function limpaCPF_CNPJ($valor){
    $valor = trim($valor);
    $valor = str_replace(".", "", $valor);
    $valor = str_replace(",", "", $valor);
    $valor = str_replace("-", "", $valor);
    $valor = str_replace("/", "", $valor);
    return $valor;
}

function limpa_Telefone($nrCelular){
    $somenteNumeros = preg_replace('/[^0-9]/', '', $nrCelular);
    return $somenteNumeros;
}