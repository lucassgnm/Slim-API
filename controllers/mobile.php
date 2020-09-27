<?php

include_once 'util/Formatador.php';

class Mobile extends Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->view->title = '';
		$this->view->render('header');
        $this->view->render('mobile/index');
		$this->view->render('footer');
    }

	function listaCidades()
    {
        try {
            $listaCidades = $this->model->qryListaCidades();

            // Retorna a resposta
            echo json_encode($listaCidades);

        } catch (Exception $e) {
            // Retorna o erro
            echo 'Erro'.$e;
        }
    }

    function listaClientes()
    {
        try {
            $listaClientes = $this->model->qryListaClientes();

            // Retorna a resposta
            echo json_encode($listaClientes);

        } catch (Exception $e) {
            // Retorna o erro
            echo 'Erro'.$e;
        }
    }

    /**
     * Função responsável por realizar o login do Cliente
     *
     */
    function realizaLogin()
    {
        try {
            // Recupera os dados
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            // Realiza a busca do usuário
            $resultBuscaCliente = $this->model->qryBuscaUsuarioLogin($email, $senha);

            // Verifica se o cliente foi encontrado
            if (count($resultBuscaCliente[0]) < 1){
                // Monta a resposta
                $result = array(
                    "Result" => "notOk"
                );
            } else {
                // Monta a resposta
                $result = array(
                    "Result"   => "ok",
                    "Nome"     => $resultBuscaCliente[0]->nome,
                    "Id"       => $resultBuscaCliente[0]->id,
                    "Cpf"      => $resultBuscaCliente[0]->cpf,
                    "Telefone" => $resultBuscaCliente[0]->telefone
                );
            }

            // Retorna a resposta
            echo json_encode($result);

        } catch (Exception $e) {
            // Retorna o erro
            echo 'Erro'.$e;
        }
    }

    /**
     * Função responsável por realizar o Cadastro do Cliente
     *
     */
    function cadastraCliente()
    {
        try {
            // Recupera os dados
            $nome     = $_POST['nome'];
            $cpf      = $_POST['cpf'];
            $telefone = $_POST['telefone'];
            $email    = $_POST['email'];
            $senha    = $_POST['senha'];

            // Formata o CPF
            $cpfFormatado = limpaCPF_CNPJ($cpf);
            // Formata o Telefone
            $telefoneFormatado = limpa_Telefone($telefone);

            // Realiza busca se já possui o e-mail cadastrado no DB
            $resultBuscaClienteEmail = $this->model->qryBuscaClientePorEmail(trim($email));

            // Verifica se o cliente foi encontrado
            if (count($resultBuscaClienteEmail[0]) > 0){
                // Monta a resposta
                $result = array(
                    "Tipo" => "notOk",
                    "Mensagem" => "Email já cadastrado!"
                );

            // Caso não encontrado nenhum registro no DB
            } else {

                // Salva dados do novo Cliente no BD
                $respostaCadastroCliente = $this->model->qryCadastraNovoClienteApp(
                    strtoupper($nome),
                    $cpfFormatado,
                    $telefoneFormatado,
                    $email,
                    $senha
                );

                // Monta o array de resposta
                $result = array(
                    "Tipo"   => "ok",
                    "Mensagem" => $respostaCadastroCliente
                );
            }

            // Retorna a resposta
            echo json_encode($result);

        } catch (Exception $e) {
            // Retorna o erro
            echo 'Erro'.$e;
        }
    }

    /**
     * Função responsável por Inserir um produto na lista de compras do cliente
     * e também inicializar uma nova venda com o produto escaneado caso o cliente não possuir
     * nenhuma aberta
     *
     */
    function insereProdutoVenda()
    {
        try {
            // Recupera os dados
            $codProduto = $_POST['codProduto'];
            $cpfCliente = $_POST['cpfCliente'];
            $idCliente  = $_POST['idCliente'];

            // Realiza busca do produto
            $resultBuscaProduto = $this->model->qryBuscaProdutoEscaneado(trim($codProduto));

            // Verifica se o Produto foi encontrado
            if (count($resultBuscaProduto[0]) < 1){
                // Monta a resposta
                $result = array(
                    "Tipo"     => "notOk",
                    "Mensagem" => "Produto não Encontrado!"
                );
            } else {

                // Busca se o usuario possui alguma venda em aberto
                $resultVendaAbertaUsuario = $this->model->qryBuscaVendaAbertaCliente(trim($idCliente));

                // Inicializa o id da venda
                $idVenda = null;
                // Verifica se foi encontrada alguma venda aberta
                if (count($resultVendaAbertaUsuario[0]) > 0) {
                    // Se possuir uma venda aberta recupera o id da mesma
                    $idVenda = $resultVendaAbertaUsuario[0]->id;
                } else {
                    // Se não, recupera o novo id da venda
                    $idVenda = $this->model->qryBuscaProxNroVenda();
                    // Insere um novo registro na tabela
                    $resultNovaVenda = $this->model->qryCriaNovaVenda($idVenda, $idCliente);
                }

                // Recupera o id do Produto
                $idProduto = $resultBuscaProduto[0]->id;
                // Busca se a venda já possui esse produto
                $resultBuscaProdutoVenda = $this->model->qryBuscaProdutoVenda($idProduto, $idVenda);

                // Verifica se foi encontrado o mesmo produto na venda
                if (count($resultBuscaProdutoVenda[0]) > 0) {
                    // Acrescenta a quantidade de produto
                    $qtdProduto = $resultBuscaProdutoVenda[0]->qtde + 1;
                    // Atualiza a quantidade de produto na venda
                    $resultVenda = $this->model->qryAtualizaQtdProdutoVenda($idVenda, $idProduto, $qtdProduto);
                } else {
                    $qtdProduto = 1;
                    // Atualiza a quantidade de produto na venda
                    $resultVenda = $this->model->qryInsereNovoProdutoVenda($idVenda, $idProduto, $qtdProduto);
                }

                // Verifica o retorno das querys
                if ($resultVenda === 'SUCESSO') {
                    // Atualiza o estoque
                    $qtdAbaterNoEstoque = 1;
                    $resultAtualizacaoEstoque = $this->model->qryAtualizaQtdeEstoque(
                        $resultBuscaProduto[0]->id,
                        $qtdAbaterNoEstoque
                    );
                }

                // Verifica o retorno da atualização do estoque
                if ($resultAtualizacaoEstoque === 'SUCESSO') {
                    // Realiza a busca da Venda e seus dados
                    $resultVendaCliente = $this->model->qryBuscaVendaCliente($idVenda, $idCliente);
                    // Resposta
                    $result = $resultVendaCliente;
                }
            }

            // Retorna a resposta
            echo json_encode($result);

        } catch (Exception $e) {
            // Retorna o erro
            echo 'Erro'.$e;
        }
    }

    /**
     * Função responsável por buscar se o cliente possui alguma venda em aberto
     *
     */
    function buscaVendaAbertaCliente()
    {
        try {
            // Recupera os dados
            $idCliente  = $_POST['idCliente'];

            $result = null;

            // Busca se o usuario possui alguma venda em aberto
            $resultVendaAbertaUsuario = $this->model->qryBuscaVendaAbertaCliente(trim($idCliente));

            // Verifica se foi encontrada alguma venda aberta
            if (count($resultVendaAbertaUsuario[0]) > 0) {
                // Se possuir uma venda aberta recupera o id da mesma
                $idVenda = $resultVendaAbertaUsuario[0]->id;

                // Realiza a busca da Venda e seus dados
                $resultVendaCliente = $this->model->qryBuscaVendaCliente($idVenda, $idCliente);

                // Resposta
                $result = $resultVendaCliente;
            } else {
                // Monta a resposta
                $result = array(
                    "Tipo"     => "notOk",
                );
            }

            // Retorna a resposta
            echo json_encode($result);

        } catch (Exception $e) {
            // Retorna o erro
            echo 'Erro'.$e;
        }
    }


}