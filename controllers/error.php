<?php

class Error extends Controller {

    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->view->title = 'Erro';
		$this->view->render('header');
        $this->view->render('error/index');
		$this->view->render('footer');
    }
    
}