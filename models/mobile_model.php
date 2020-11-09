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
            'cpf'           => $cpf,
            'nome'          => $nome,
            'telefone'      => $telefone,
            'email'         => $email,
            'senha'         => hash('sha256',$senha),
            'data_cadastro' => date("Y-m-d H:i:s")
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
        $result = $this->db->select("select venda.id               as IdCompra,
                                            itemvenda.id           as Id,
                                            itemvenda.qtde         as Qtde,
                                            descricao              as Descricao,
                                            valor                  as ValorUnitario,
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

    /**
     * Query responsável por excluir um produto da venda
     *
     */
    public function qryExcluiProdutoVenda($idProduto)
    {
        // Realiza o Update
        $result = $this->db->delete("itemvenda", "itemvenda.id = " . $idProduto);

        if ($result) {
            // Retorna o resultado
            return 'SUCESSO';
        } else {
            // Retorna o resultado
            return 'ERRO';
        }

    }

    /**
     * Query responsável por atualizar a quantidade de um produto da venda
     *
     */
    public function qryAtualizaQtdProdutoVendaPorID($idProduto, $quantidade)
    {
        // Realiza o Update
        $this->db->update('itemvenda',
                        array('qtde' => $quantidade),
                        "id = ". $idProduto);

        return 'SUCESSO';

    }

    /**
     * Query responsável por buscar os Cartões do Cliente
     *
     */
    public function qryBuscaCartoesCliente($idCliente)
    {
        // Realiza o Select
        $result = $this->db->select("select *
                                     from cliente_cartoes
                                     where id_cliente = " . $idCliente .
                                   " and ativo = 1");

        // Retorna o resultado
        return $result;

    }

    /**
     * Query responsável por Salvar um novo cartão do cliente
     *
     */
    public function qrySalvaNovoCartaoCliente($idCliente, $bandeira, $imagem, $numero, $nome, $validade, $codigo)
    {
        // Realiza o Insert
        $this->db->insert('cliente_cartoes',
                            array(
                                'id_cliente'    => $idCliente,
                                'bandeira'      => $bandeira,
                                'imagem'        => $imagem,
                                'numero'        => $numero,
                                'nome'          => $nome,
                                'validade'      => $validade,
                                'cvv'           => $codigo,
                                'data_cadastro' => date("Y-m-d H:i:s")
                            ));

        return 'SUCESSO';
    }

    /**
     * Query responsável por excluir um produto da venda
     *
     */
    public function qryExcluiCartao($idCliente, $idCartao)
    {
        // Realiza o Update
        $this->db->update(
            // Table
            "cliente_cartoes",
            // Update
            array(
                "ativo" => 0
            ),
            // Where
            "cliente_cartoes.id = ". $idCartao . " and cliente_cartoes.id_cliente = " . $idCliente
        );

        return 'SUCESSO';
    }

    /**
     * Query responsável por fechar a compra de um Cliente
     *
     */
    public function qryFechaCompraCliente($idCompra, $idCliente, $valorCompra, $idCartao)
    {
        // Realiza o Update
        $this->db->update(
            // Table
            "venda",
            // Update
            array(
                "aberta"           => 0,
                "total"            => $valorCompra,
                "cartao_pagamento" => $idCartao
            ),
            // Where
            "venda.id = ". $idCompra . " and venda.idcliente = " . $idCliente
        );

        return 'SUCESSO';
    }

    /**
     * Query responsável por buscar Ultimas compras do Cliente
     *
     */
    public function qryBuscaUltimasComprasCliente($idCliente)
    {
        // Realiza o Select
        $result = $this->db->select("select venda.id                             as IdCompra, 
                                            date_format(datavenda, \"%d/%m/%Y\") as Data,
                                            total                                as TotalCompra,
                                            imagem                               as Imagem,
                                            numero                               as NumeroCartao
                                     from venda
                                     left join cliente_cartoes on cliente_cartoes.id = venda.cartao_pagamento and
                                                                  cliente_cartoes.id_cliente = venda.idcliente 
                                     where idcliente = " . $idCliente . "
                                     and aberta <> 1
                                     order by datavenda desc");

        // Retorna o resultado
        return $result;
    }

     /**
     * Query responsável por buscar Ultimas compras do Cliente
     *
     */
    public function qryBuscaItensCompra($idCompra)
    {
        // Realiza o Select
        $result = $this->db->select("select itemvenda.qtde           as Qtde,
                                            descricao                as Descricao,
                                            valor                    as ValorUnitario,
                                            (valor * itemvenda.qtde) as ValorTotal 
                                    from itemvenda
                                    join produto on produto.id = itemvenda.idproduto 
                                    where idvenda = " . $idCompra);

        // Retorna o resultado
        return $result;
    }

    /**
     * Query responsável por buscar os Estados no DB
     *
     */
    public function qryBuscaEstados()
    {
        // Realiza o Select
        $result = $this->db->select("select id   as Id,
                                            nome as Nome,
                                            uf   as UF
                                    from estado");

        // Retorna o resultado
        return $result;
    }

    /**
     * Query responsável por buscar as Cidades pelo Id do Estado
     *
     */
    public function qryBuscaCidadesEstado($idEstado)
    {
        // Realiza o Select
        $result = $this->db->select("select id   as Id,
                                            nome as Nome
                                     from cidade
                                     where uf = " . $idEstado);

        // Retorna o resultado
        return $result;
    }

    /**
     * Query responsável por buscar um Cliente pelo Id
     *
     */
    public function qryBuscaClienteId($idCliente)
    {
        // Realiza o Select
        $result = $this->db->select("select *
                                     from cliente
                                     where id = " . $idCliente);

        // Retorna o resultado
        return $result;

    }


    /**
     * Query responsável por atualizar os dados do Cliente
     *
     */
    public function qryAtualizaDadosCliente(
        $idCliente,
        $nomeCliente,
        $telefone,
        $nomeRua     = null,
        $cep         = null,
        $idEstado    = null,
        $idCidade    = null,
        $numeroLogra = null
    )
    {
        // Realiza o Update
        $this->db->update(
            // Table
            "cliente",
            // Update
            array(
                "nome"     => strtoupper($nomeCliente),
                "telefone" => $telefone,
                "estado"   => $idEstado,
                "cidade"   => $idCidade,
                "rua"      => $nomeRua,
                "numero"   => $numeroLogra,
                "cep"      => $cep
            ),
            // Where
            "id = " . $idCliente
        );

        return 'SUCESSO';

    }

}