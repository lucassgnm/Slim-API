<?php

class View {
	//Titulo da view (pagina)
	public $title="Titulo";
	
    function __construct() {

    }
	//renderiza a p�gina com o nome da view passado no parametro
    public function render($name, $noInclude = false)
    {
        require 'views/' . $name . '.php';    
    }

}