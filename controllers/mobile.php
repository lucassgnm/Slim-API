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
                    "Result"         => "ok",
                    "Nome"           => $resultBuscaCliente[0]->nome,
                    "Id"             => $resultBuscaCliente[0]->id,
                    "Cpf"            => $resultBuscaCliente[0]->cpf,
                    "Telefone"       => $resultBuscaCliente[0]->telefone,
                    "Email"          => $resultBuscaCliente[0]->email,
                    "Genero"         => $resultBuscaCliente[0]->genero,
                    "DataNascimento" => $resultBuscaCliente[0]->data_nasc,
                    "Cep"            => $resultBuscaCliente[0]->cep,
                    "Rua"            => $resultBuscaCliente[0]->rua,
                    "Numero"         => $resultBuscaCliente[0]->numero,
                    "Cidade"         => $resultBuscaCliente[0]->cidade,
                    "Estado"         => $resultBuscaCliente[0]->estado,
                    "Rua"            => $resultBuscaCliente[0]->rua,
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
                    $result['Tipo']      = "ok";
                    $result['Registros'] = $resultVendaCliente;
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

                // Verifica a quantidade de itens da venda
                if (count($resultVendaCliente) > 0) {
                    // Retorna os itens
                    $result['Tipo']      = "ok";
                    $result['Registros'] = $resultVendaCliente;

                } else {
                    // Monta a resposta
                    $result = array(
                        "Tipo" => "notOk"
                    );
                }
            } else {
                // Monta a resposta
                $result = array(
                    "Tipo" => "notOk"
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
     * Função responsável por excluir um produto de uma venda
     *
     */
    function excluiProdutoVenda()
    {
        try {
            // Recupera os dados
            $idCliente  = $_POST['idCliente'];
            $idProduto  = $_POST['idProduto'];
            // Inicializa a váriavel
            $result = null;

            // Exclui o item da venda
            $resultExclusaoProduto = $this->model->qryExcluiProdutoVenda(trim($idProduto));
            // Verifica a resposta da exclusão
            if ($resultExclusaoProduto == 'SUCESSO') {
                // Busca se o usuario possui alguma venda em aberto
                $resultVendaAbertaUsuario = $this->model->qryBuscaVendaAbertaCliente(trim($idCliente));

                // Verifica se foi encontrada alguma venda aberta
                if (count($resultVendaAbertaUsuario[0]) > 0) {
                    // Se possuir uma venda aberta recupera o id da mesma
                    $idVenda = $resultVendaAbertaUsuario[0]->id;

                    // Realiza a busca da Venda e seus dados
                    $resultVendaCliente = $this->model->qryBuscaVendaCliente($idVenda, $idCliente);

                    // Verifica a quantidade de itens da venda
                    if (count($resultVendaCliente) > 0) {
                        // Retorna os itens
                        $result['Tipo']      = "ok";
                        $result['Registros'] = $resultVendaCliente;

                    } else {
                        // Monta a resposta
                        $result = array(
                            "Tipo" => "notOk"
                        );
                    }
                }
            } else {
                // Monta a resposta
                $result = array(
                    "Tipo" => "notOk"
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
     * Função responsável por excluir um produto de uma venda
     *
     */
    function atualizaQtdeProdutoVenda()
    {
        try {
            // Recupera os dados
            $idCliente  = $_POST['idCliente'];
            $quantidade = $_POST['quantidade'];
            $idProduto  = $_POST['idProduto'];
            // Inicializa a váriavel
            $result = null;

            // Exclui o item da venda
            $resultado = $this->model->qryAtualizaQtdProdutoVendaPorID(trim($idProduto), trim($quantidade));

            // Verifica a resposta da exclusão
            if ($resultado == 'SUCESSO') {
                // Busca se o usuario possui alguma venda em aberto
                $resultVendaAbertaUsuario = $this->model->qryBuscaVendaAbertaCliente(trim($idCliente));

                // Verifica se foi encontrada alguma venda aberta
                if (count($resultVendaAbertaUsuario[0]) > 0) {
                    // Se possuir uma venda aberta recupera o id da mesma
                    $idVenda = $resultVendaAbertaUsuario[0]->id;

                    // Realiza a busca da Venda e seus dados
                    $resultVendaCliente = $this->model->qryBuscaVendaCliente($idVenda, $idCliente);

                    // Verifica a quantidade de itens da venda
                    if (count($resultVendaCliente) > 0) {
                        // Retorna os itens
                        $result['Tipo']      = "ok";
                        $result['Registros'] = $resultVendaCliente;

                    } else {
                        // Monta a resposta
                        $result = array(
                            "Tipo" => "notOk"
                        );
                    }
                }
            } else {
                // Monta a resposta
                $result = array(
                    "Tipo" => "notOk"
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
     * Função responsável por buscar os cartões de Crédito do Cliente
     *
     */
    function buscaCartaoCreditoCliente()
    {
        // Recupera os dados
        $idCliente  = $_POST['idCliente'];

        // Exclui o item da venda
        $resultBuscaCartoes = $this->model->qryBuscaCartoesCliente(trim($idCliente));

        // Inicializa a váriavel
        $result = null;

        // Verifica a quantidade de itens da venda
        if (count($resultBuscaCartoes) > 0) {
            // Retorna os itens
            $result['Tipo']      = "ok";
            $result['Registros'] = $resultBuscaCartoes;
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

    /**
     * Função responsável por Salvar um novo Cartão de Crédito do Cliente
     *
     */
    function salvaNovoCartao()
    {
        // Recupera os dados
        $idCliente  = $_POST['idCliente'];
        $bandeira   = $_POST['bandeira'];
        $imagem     = $_POST['imagem'];
        $numero     = $_POST['numero'];
        $nome       = $_POST['nome'];
        $validade   = $_POST['validade'];
        $codigo     = $_POST['codigo'];

        // Salva um novo cartão no DB
        $resultNovoCartao = $this->model->qrySalvaNovoCartaoCliente(
            trim($idCliente),
            trim($bandeira),
            trim($imagem),
            trim($numero),
            trim($nome),
            trim($validade),
            trim($codigo)
        );

        // Verifica a resposta
        if ($resultNovoCartao == trim('SUCESSO')) {
            // Monta a resposta
            $result = array(
                "Tipo" => "ok"
            );
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

    /**
     * Função responsável por Excluir um Cartão de Crédito do Cliente
     *
     */
    function excluiCartao()
    {
        // Recupera os dados
        $idCliente  = $_POST['idCliente'];
        $idCartao   = $_POST['idCartao'];

        // Exclui o Cartão do cliente
        $resultNovoCartao = $this->model->qryExcluiCartao(
            trim($idCliente),
            trim($idCartao)
        );

        // Verifica a resposta
        if ($resultNovoCartao == trim('SUCESSO')) {
            // Monta a resposta
            $result = array(
                "Tipo" => "ok"
            );
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

    /**
     * Função responsável por finalizar a Compra do Cliente
     *
     */
    function finalizaCompraCliente()
    {
        // Recupera os dados
        $idCliente   = $_POST['idCliente'];
        $idCartao    = $_POST['idCartao'];
        $idCompra    = $_POST['idCompra'];
        $valorCompra = $_POST['valorCompra'];

        // Formata o valor para o padrão do DB
        $valorCompra = str_replace(",", ".", $valorCompra);

        // Finaliza a compra do Cliente
        $resultNovoCartao = $this->model->qryFechaCompraCliente(
            trim($idCompra),
            trim($idCliente),
            trim($valorCompra),
            trim($idCartao)
        );

        // Verifica a resposta
        if ($resultNovoCartao == trim('SUCESSO')) {
            // Monta a resposta
            $result = array(
                "Tipo" => "ok"
            );
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

    /**
     * Função responsável por buscar as últimas compras do Cliente
     *
     */
    function buscaUltimasComprasCliente()
    {
        // Recupera os dados
        $idCliente   = $_POST['idCliente'];

        // Busca as compras finalizadas do Cliente
        $resultVendas = $this->model->qryBuscaUltimasComprasCliente(
            trim($idCliente)
        );

        // Inicializa a váriavel
        $result = null;

        // Verifica a quantidade de itens da venda
        if (count($resultVendas) > 0) {
            // Retorna os itens
            $result['Tipo']      = "ok";
            $result['Registros'] = $resultVendas;
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

    
    /**
     * Função responsável por buscar os itens da Compra selecionada
     *
     */
    function buscaItensCompra()
    {
        // Recupera os dados
        $idCompra   = $_POST['idCompra'];

        // Busca os itens da Compra pelo Id
        $resultItens = $this->model->qryBuscaItensCompra(
            trim($idCompra)
        );

        // Inicializa a váriavel
        $result = null;

        // Verifica a quantidade de itens da venda
        if (count($resultItens) > 0) {
            // Retorna os itens
            $result['Tipo']      = "ok";
            $result['Registros'] = $resultItens;
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

    /**
     * Função responsável por buscar os estados cadastrados no DB
     *
     */
    function buscaEstados()
    {
        // Busca os Estados
        $resultEstados = $this->model->qryBuscaEstados();

        // Inicializa a váriavel
        $result = null;

        // Verifica a quantidade de itens da venda
        if (count($resultEstados) > 0) {
            // Retorna os itens
            $result['Tipo']      = "ok";
            $result['Registros'] = $resultEstados;
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

    /**
     * Função responsável por buscar as cidades pelo Id do Estado selecionado
     *
     */
    function buscaCidadesEstado()
    {
        // Recupera os dados
        $idEstado   = $_POST['idEstado'];

        // Busca os Estados
        $resultEstados = $this->model->qryBuscaCidadesEstado($idEstado);

        // Inicializa a váriavel
        $result = null;

        // Verifica a quantidade de itens da venda
        if (count($resultEstados) > 0) {
            // Retorna os itens
            $result['Tipo']      = "ok";
            $result['Registros'] = $resultEstados;
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

    /**
     * Função responsável por atualizar os Dados do Cliente
     *
     */
    function atualizaDadosCliente()
    {
        // Recupera os dados
        $idCliente   = $_POST['idCliente'];
        $nomeCliente = $_POST['nomeCliente'];
        $telefone    = limpa_Telefone($_POST['telefone']);
        $nomeRua     = $_POST['nomeRua'];
        $cep         = $_POST['cep'];
        $idEstado    = $_POST['idEstado'];
        $idCidade    = $_POST['idCidade'];
        $numeroLogra = $_POST['numeroLogra'];

        // Busca o Cliente pelo Id
        $resultCliente = $this->model->qryBuscaClienteId($idCliente);

        // Busca os Estados
        $resultEstados = $this->model->qryAtualizaDadosCliente(
            $idCliente,
            $nomeCliente,
            $telefone,
            ($nomeRua     ? strtoupper($nomeRua)     : $resultCliente[0]->rua),
            ($cep         ? $cep                     : $resultCliente[0]->cep),
            ($idEstado    ? $idEstado                : $resultCliente[0]->estado),
            ($idCidade    ? $idCidade                : $resultCliente[0]->cidade),
            ($numeroLogra ? strtoupper($numeroLogra) : $resultCliente[0]->numero)
        );

        // Inicializa a váriavel
        $result = null;

        // Verifica a resposta
        if ($resultEstados == trim('SUCESSO')) {
            // Monta a resposta
            $result = array(
                "Tipo" => "ok"
            );
        } else {
            // Monta a resposta
            $result = array(
                "Tipo" => "notOk"
            );
        }

        // Retorna a resposta
        echo json_encode($result);
    }

}