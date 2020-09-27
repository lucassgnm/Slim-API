<?php

class Clientes_Model extends Model {

    public function __construct() {
        parent::__construct();
    }
    
	public function lista() 
    {  
        $result=$this->db->select('select * from cidade');
		
		//print_r($result);
		
		$result = json_encode($result);
		
		
		echo $result;
    }
	

    public function insert() 
    {
        $codigo = $_POST['txtcod'];
        $cpf    = $_POST['txtcpf'];
        $nome   = $_POST['txtnome'];
        $end    = $_POST['txtend'];
        $fone   = $_POST['txtfone'];
        $senha  = $_POST['txtsenha'];
        
        $this->db->insert('cliente', array('codigo' =>$codigo,'cpf'=>$cpf,'nome'=>$nome,'endereco'=>$end,'telefone'=>$fone,'senha'=>hash('sha256',$senha)));
       echo "Dados Inseridos com Sucesso";
    }
	
	public function del() 
    {  
		//o codigo deve ser um inteiro
		$cod=(int)$_POST['cod'];
		
        $this->db->delete('cliente',"codigo='$cod'");
	}
	
	public function loadData() 
    {  
		//o codigo deve ser um inteiro
		$cod=(int)$_POST['cod'];
		
         $result=$this->db->select('select codigo,trim(cpf)as cpf,nome,endereco,telefone from cliente where codigo=:cod',array(":cod"=>$cod));
		$result = json_encode($result);
		echo($result);
	}
	
	
	public function save() 
    {
        $codigo = $_POST['txtcod'];
        $cpf    = $_POST['txtcpf'];
        $nome   = $_POST['txtnome'];
        $end    = $_POST['txtend'];
        $fone   = $_POST['txtfone'];
        $senha  = $_POST['txtsenha'];
		
		$dadosSave=array('cpf'=>$cpf,'nome'=>$nome,'endereco'=>$end,'telefone'=>$fone);
		
		
		if($senha!=""){
			$senha=hash('sha256',$senha);
			$dadosSave['senha']=$senha;
		}
        
       $this->db->update('cliente', $dadosSave,"codigo='$codigo'");
       echo "Dados gravados com Sucesso";
	   
    }
}