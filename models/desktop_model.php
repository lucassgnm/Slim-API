<?php
include_once 'util/Formatador.php';
include_once 'util/EnviarEmail.php';

class Desktop_Model extends Model
{

    public function __construct()
    {
        parent::__construct();
    }
    /* Início login */
    public function administradorExiste()
    {
        try {
            $msg = array(
                "code" => 0,
                "msg" => "Usuário não existe!"
            );

            $email = trim($_POST['email']);
            $senha = hash('sha256', $_POST['senha']);
            $result = $this->db->select('select * 
                                       from administrador
                                       where email=:email and 
                                       senha=:senha', array(
                ':email' => $email,
                ':senha' => $senha
            ));
            if (count($result) > 0) {
                $msg = array(
                    "code" => 1,
                    "msg" => $result[0]->nome
                );
            }
        } catch (Exception $e) {

            $msg = array(
                "code" => 2,
                "msg" => "Houve um erro: " . $e
            );
        }
        echo json_encode($msg);
    }
    /* Fim login */

    /* Início produto */
    public function todosProdutos()
    {
        try {

            $result = $this->db->select('select id, descricao, valor, qtde 
                                       from produto
                                       where ativo = 1
                                       order by descricao
                                       limit 100');
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function todosProdutosPorNome()
    {
        try {

            $descricao = $_POST['nome'];
            $result = $this->db->select('select id, descricao, valor, qtde  
                                       from produto
                                       where descricao LIKE :descricao and
                                             ativo = 1
                                       order by descricao
                                       limit 100', array(
                ':descricao' => $descricao . '%'
            ));
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function adicionarProduto()
    {
        $msg = array(
            "code" => 1,
            "msg" => "Produto cadastrado com sucesso!"
        );

        try {
            $descricao = trim($_POST['descricao']);
            $valor = $_POST['valorun'];
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
            $qtde = (int)$_POST['qtde'];
            $codbarra = str_replace(' ', '', $_POST['codbarra']);

            $this->db->insert(
                'produto',
                array(
                    'descricao' => $descricao,
                    'valor' => $valor,
                    'qtde' => $qtde,
                    'codbarra' => $codbarra,
                    'ativo' => 1
                )
            );
        } catch (Exception $e) {

            $msg = array(
                "code" => 2,
                "msg" => "Houve um erro: " . $e
            );
        }
        echo json_encode($msg);
    }

    public function excluirProduto()
    {
        $msg = array(
            "code" => 1,
            "msg" => "Produto excluido com sucesso!"
        );

        try {
            $id = (int)$_POST['id'];

            $this->db->update('produto', array('ativo' => 0), 'id = ' . $id);
        } catch (Exception $e) {

            $msg = array(
                "code" => 2,
                "msg" => "Houve um erro: " . $e
            );
        }
        echo json_encode($msg);
    }

    public function produtoPorId()
    {
        try {

            $id = $_POST['id'];
            $result = $this->db->select(
                'select id, descricao, valor, qtde, codbarra 
                                       from produto
                                       where id = :id',
                array(
                    ':id' => $id
                )
            );
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function editarProduto()
    {
        try {
            $msg = array(
                "code" => 1,
                "msg" => "Produto editado com sucesso!"
            );

            $id = (int)$_POST['id'];
            $descricao = trim($_POST['descricao']);
            $valor = $_POST['valorun'];
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
            $qtde = (int)$_POST['qtde'];
            $codbarra = str_replace(' ', '', $_POST['codbarra']);

            $this->db->update(
                'produto',
                array(
                    'descricao' => $descricao,
                    'valor' => $valor,
                    'qtde' => $qtde,
                    'codbarra' => $codbarra
                ),
                'id = ' . $id
            );
        } catch (Exception $e) {
            $msg = array(
                "code" => 2,
                "msg" => "Houve um erro: " . $e
            );
        }
        echo json_encode($msg);
    }
    /* Fim produto */

    /* Início cliente */
    public function todosClientes()
    {
        try {

            $result = $this->db->select('select id, nome, email, cpf, telefone 
                                       from cliente
                                       where ativo = 1
                                       order by nome
                                       limit 100');
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function todosClientesPorNome()
    {
        try {

            $nome = $_POST['nome'];
            $result = $this->db->select('select id, nome, email, cpf, telefone 
                                       from cliente
                                       where nome LIKE :nome and
                                             ativo = 1
                                       order by nome
                                       limit 100', array(
                ':nome' => $nome . '%'
            ));
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function clientePorId()
    {
        try {

            $id = $_POST['id'];
            $result = $this->db->select(
                'select id, nome, email, cpf, telefone 
                                       from cliente
                                       where id = :id',
                array(
                    ':id' => $id
                )
            );
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function editarCliente()
    {
        try {
            $msg = array(
                "code" => 1,
                "msg" => "Cliente editado com sucesso!"
            );

            $id = (int)$_POST['id'];
            $nome = trim($_POST['nome']);
            $email = trim($_POST['email']);
            $cpf = $_POST['cpf'];
            $telefone = $_POST['telefone'];

            $this->db->update(
                'cliente',
                array(
                    'nome' => $nome,
                    'email' => $email,
                    'cpf' => $cpf,
                    'telefone' => $telefone
                ),
                'id = ' . $id
            );
        } catch (Exception $e) {
            $msg = array(
                "code" => 2,
                "msg" => "Houve um erro: " . $e
            );
        }
        echo json_encode($msg);
    }

    public function excluirCliente()
    {
        $msg = array(
            "code" => 1,
            "msg" => "Cliente excluido com sucesso!"
        );

        try {
            $id = (int)$_POST['id'];

            $this->db->update('cliente', array('ativo' => 0), 'id = ' . $id);
        } catch (Exception $e) {

            $msg = array(
                "code" => 2,
                "msg" => "Houve um erro: " . $e
            );
        }
        echo json_encode($msg);
    }
    /* Fim cliente */

    /* Início venda */
    public function todasVendas()
    {
        try {

            $result = $this->db->select('select v.id, c.nome nomecliente, v.total, v.datavenda, aberta status
                                       from cliente c, venda v
                                       where v.idcliente = c.id
                                       order by v.datavenda desc
                                       limit 100');
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function todasVendasPorId()
    {
        try {

            $id = (int)$_POST['id'];
            $result = $this->db->select('select v.id, c.nome nomecliente, v.total, v.datavenda, aberta status
                                         from cliente c, venda v
                                         where v.idcliente = c.id and
                                         v.id = :id
                                         order by v.datavenda desc
                                         limit 100', array(
                ':id' => $id
            ));
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function enviarEmail()
    {
        try {
            $assunto = $_POST['assunto'];
            $txt = $_POST['txt'];

            $msg = array(
                "code" => 0,
                "msg" => "Houve um erro ao enviar os emails."
            );

            if ($assunto == "" || $txt == "") {
                echo json_encode($msg);
                exit;
            }

            $result = $this->db->select('select c.email
                                         from slimdata.cliente c');

            foreach ($result as $dt) {
                enviarEmail($dt->email, $assunto, $txt);
                echo $dt->email . "\n --- OK";
            }
            //var_dump($result); 
            exit;
        } catch (Exception $e) {
            $msg = array(
                "code" => 2,
                "msg" => "Houve um erro: " . $e
            );
        }
    }

    public function todasUltimasVendas()
    {
        try {

            $result = $this->db->select('select v.id, c.nome nomecliente, v.total, v.datavenda, cc.bandeira
                                        from slimdata.venda v, slimdata.cliente c, slimdata.cliente_cartoes cc
                                        where v.idcliente = c.id
                                        and   v.cartao_pagamento = cc.id 
                                        and   v.aberta = 0
                                        order by v.datavenda desc
                                        limit 6');
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function getInformacoesDash()
    {
        try {

            $result = $this->db->select('select (
                select IFNULL(count(*), 0)
                from venda v 
                where v.aberta = 1
                and   cast(v.datavenda as date) = cast(now() as date)
               ) vendashj,
               (
                select IFNULL(count(*), 0)
                from venda v
                where cast(v.datavenda as date) = cast(now() as date)
               ) vendastotaishj,
               (
                select IFNULL(count(*), 0)
                from venda v 
                where cast(v.datavenda as date)
                    between DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY) AND CURRENT_DATE()
               ) vendastotaismes,
               (
                   select IFNULL(avg(v.total), 0)
                from venda v 
                where cast(v.datavenda as date)
                    between DATE_ADD(CURRENT_DATE(), INTERVAL -7 DAY) AND CURRENT_DATE()
               ) ticketmediosemanal,
               (
                select IFNULL(avg(v.total), 0)
                from venda v 
                where cast(v.datavenda as date)
                    between DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY) AND CURRENT_DATE()
               ) ticketmediomensal,
               (
                   select IFNULL(avg(v.total), 0)
                from venda v 
                where cast(v.datavenda as date)
                    between DATE_ADD(CURRENT_DATE(), INTERVAL -365 DAY) AND CURRENT_DATE()
               ) ticketmedioanual
               ');
        } catch (Exception $e) {
        }
        echo json_encode($result);
    }

    public function todosProdutosXml()
    {
        try {

            /* $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML('<h1>Hello world!</h1>');
            $mpdf->Output(); */
            
        } catch (Exception $e) {
            echo $e;
        }
    }
}

/* 
    -- Vendas abertas (HJ)
select count(*) vendashj
from venda v 
where v.aberta = 1
and   cast(v.datavenda as date) = cast(now() as date); 

-- Vendas totais (HJ)
select count(*) vendastotaishj
from venda v
where cast(v.datavenda as date) = cast(now() as date);

-- Vendas totais (MES)
select count(*) vendastotaismes
from venda v 
where cast(v.datavenda as date)
    between DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY) AND CURRENT_DATE()
    
-- Ticket médio semanal
select avg(v.total) as ticketmediosemanal
from venda v 
where cast(v.datavenda as date)
    between DATE_ADD(CURRENT_DATE(), INTERVAL -7 DAY) AND CURRENT_DATE()

-- Ticket médio mensal
select avg(v.total) as ticketmediomensal
from venda v 
where cast(v.datavenda as date)
    between DATE_ADD(CURRENT_DATE(), INTERVAL -30 DAY) AND CURRENT_DATE()

-- Ticket médio anual
select avg(v.total) as ticketmedioanual
from venda v 
where cast(v.datavenda as date)
    between DATE_ADD(CURRENT_DATE(), INTERVAL -365 DAY) AND CURRENT_DATE()

*/
