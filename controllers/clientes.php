<?php

class Clientes extends Controller {

    function __construct() {
        parent::__construct();
        //Auth::autentica();
        $this->view->js = array('clientes/js/clientes.js');
    }
    
    function index() {
        $this->view->title = 'Cadastro de Clientes';
		$this->view->render('header');
        $this->view->render('clientes/index');
		$this->view->render('footer');
    }
     function insert()
    {
        $this->model->insert();
    }
	function lista()
    {
        $this->model->lista();
    }
	
	function del()
    {
        $this->model->del();
    }
	
	
	function loadData()
    {
        $this->model->loadData();
    }
	
	function save()
    {
        $this->model->save();
    }
}