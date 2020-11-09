<?php

require 'config.php';

// funcao que carrega as classes automaticamente
function __autoload($class) {
    require LIBS . $class .".php";
}
// carrega o bootstrap - inicializador
$bootstrap = new Bootstrap();
// caminhos opcionais
//$bootstrap->setControllerPath();
//$bootstrap->setModelPath();
//$bootstrap->setDefaultFile();
//$bootstrap->setErrorFile();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization");
$bootstrap->init();