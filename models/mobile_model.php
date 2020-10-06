<?php

class Mobile_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    // Query utilizada na função: 'cadastraCliente'
    // Responsavel por inserir um novo Cliente no Banco de Dados
    public function qryCadastraNovoClienteApp($nome, $cpf, $telefone, $email, $senha)
    {

        // Realiza o Insert
        $this->db->insert('cliente', array(
            'cpf'=>$cpf,
            'nome'=>$nome,
            'telefone'=>$telefone,
            'email'=>$email, 
            'senha'=>hash('sha256',$senha)
        ));

        // Retorna o resultado
        return 'SUCESSO';
    }

    // Query utilizada na função: 'cadastraCliente'
    // Responsavel por buscar o Cliente pelo CPF
    public function qryBuscaClientePorCPF($cpf)
    {

        // Realiza o Select
        $result = $this->db->select('select *
                                     from cliente 
                                     where cpf =' . $cpf);

        // Retorna o resultado
        return $result;
    }

    // Query utilizada na função: 'cadastraCliente'
    // Responsavel por buscar o Cliente pelo Email
    public function qryBuscaClientePorEmail($email)
    {

        // Realiza o Select
        $result = $this->db->select("select *
                                     from cliente
                                     where email ='" .$email. "'");

        // Retorna o resultado
        return $result;
    }

    public function qryBuscaUsuarioLogin($email, $senha)
    {
        // Realiza o Select
        $result = $this->db->select("select * 
                                     from cliente where email ='".$email."' and 
                                     senha='".hash('sha256',$senha)."'");

        // Retorna o resultado
        return $result;
    }

    public function qryBuscaProdutoEscaneado($codProduto)
    {
        // Realiza o Select
        $result = $this->db->select("select *
                                     from produto 
                                     where codbarra ='".$codProduto."'");

        // Retorna o resultado
        return $result;
    }

    public function qryBuscaVendaAbertaCliente($idCliente)
    {
        // Realiza o Select
        $result = $this->db->select("select *
                                     from venda 
                                     where idcliente ='".$idCliente."' and 
                                     aberta = 1");

        // Retorna o resultado
        return $result;
    }

    public function qryBuscaProxNroVenda()
    {
        // Realiza o Select
        $result = $this->db->select("select max(id) as proximoNumero
                                     from venda");

        // Retorna o resultado
        return $result[0]->proximoNumero + 1;
    }

    public function qryBuscaProdutoVenda($idProduto, $idVenda)
    {
        // Realiza o Select
        $result = $this->db->select("select *
                                     from itemvenda 
                                     where idproduto = ".$idProduto." and 
                                     idvenda = ".$idVenda);

        // Retorna o resultado
        return $result;
    }

    public function qryCriaNovaVenda($idVenda, $idCliente)
    {
        // Realiza o Insert
        $this->db->insert('venda', array(
            'id'        => $idVenda,
            'idcliente' => $idCliente,
            'datavenda' => date("Y-m-d H:i:s")
        ));
       
        return 'SUCESSO';
    }

    public function qryAtualizaQtdProdutoVenda($idVenda, $idProduto, $qtdProduto)
    {
        // Realiza o Select
        $this->db->update('itemvenda', 
                           array('qtde' => $qtdProduto),
                           "idvenda = ".$idVenda. " and
                            idproduto = ". $idProduto);
       
        return 'SUCESSO';
    }

    public function qryInsereNovoProdutoVenda($idVenda, $idProduto, $qtdProduto)
    {
        // Realiza o Insert
        $this->db->insert('itemvenda',
                            array(
                                'idvenda'  => $idVenda,
                                'idproduto'=> $idProduto,
                                'qtde'     => $qtdProduto
                            ));
       
        return 'SUCESSO';
    }

    /**
     * Qry responsável por
     * 
     */
    public function qryAtualizaQtdeEstoque($idProduto, $qtdAbaterNoEstoque)
    {
        // Realiza o Update
        $this->db->sql("update produto
                        set qtde = qtde - " . $qtdAbaterNoEstoque .
                      " where id = " . $idProduto);
       
        return 'SUCESSO';
    }

    /**
     * Query responsável por retornar todos os itens e detalhes da venda inteira
     * 
     */
    public function qryBuscaVendaCliente($idVenda, $idCliente)
    {
        // Realiza o Update
        $result = $this->db->select("select itemvenda.qtde as Qtde,
                                            descricao as Descricao,
                                            valor as ValorUnitario,
                                            valor * itemvenda.qtde as ValorTotal,
                                            (select sum(produto.valor * itemvenda.qtde)
                                            from itemvenda
                                            join produto on produto.id = itemvenda.idproduto 
                                            where idvenda = ". $idVenda .") as ValorCompra
                                     from venda
                                     join itemvenda on idvenda = venda.id
                                     join produto  on produto.id = idproduto
                                     where venda.id = ".  $idVenda  ." and
                                     venda.idcliente = ". $idCliente."
                                     order by descricao desc");
        
        // Retorna o resultado
        return $result;
    }

}