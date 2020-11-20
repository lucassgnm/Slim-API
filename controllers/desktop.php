<?php

class Desktop extends Controller {

    function __construct() {
        parent::__construct();
        //Auth::autentica();
        //$this->view->js = array('clientes/js/clientes.js');
    }
    
    function index() {
        $this->view->title = '';
		$this->view->render('header');
        $this->view->render('desktop/index');
		$this->view->render('footer');
    }

    function administradorExiste()
    {
        $this->model->administradorExiste();
    }

    function todosProdutos()
    {
        $this->model->todosProdutos();
    }

    function todosProdutosPorNome()
    {
        $this->model->todosProdutosPorNome();
    }

    function adicionarProduto()
    {
        $this->model->adicionarProduto();
    }

    function excluirProduto()
    {
        $this->model->excluirProduto();
    }

    function produtoPorId()
    {
        $this->model->produtoPorId();
    }

    function editarProduto()
    {
        $this->model->editarProduto();
    }

    function todosClientes()
    {
        $this->model->todosClientes();
    }

    function todosClientesPorNome()
    {
        $this->model->todosClientesPorNome();
    }

    function clientePorId()
    {
        $this->model->clientePorId();
    }

    function editarCliente()
    {
        $this->model->editarCliente();
    }

    function excluirCliente()
    {
        $this->model->excluirCliente();
    }

    function todasVendas()
    {
        $this->model->todasVendas();
    }

    function todasVendasPorId()
    {
        $this->model->todasVendasPorId();
    }

    function enviarEmail()
    {
        $this->model->enviarEmail();
    }

    function todasUltimasVendas()
    {
        $this->model->todasUltimasVendas();
    }

    function getInformacoesDash()
    {
        $this->model->getInformacoesDash();
    }

    function todosProdutosXml()
    {
        $this->model->todosProdutosXml();
    }
}