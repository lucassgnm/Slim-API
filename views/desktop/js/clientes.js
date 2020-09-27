$(document).ready(function(){
	lista();
	$("#botoesedit").hide();

	$("#btnCad").click(function(e){
		e.preventDefault();
		$.post("clientes/insert/",$("#frmCadCli").serialize(),function(data){
			alert(data);
			lista();
		});
		
	});
	
	function lista(){
	
		$.post('clientes/lista/', function(data) {
		  data=$.parseJSON(data);
		 
		  $("#tabres").find("tr:gt(0)").remove();
			for (var i = 0; i < data.length; i++){
				$('#lsclientes').append('<tr><td>' + data[i].cpf + '</td><td>' + data[i].nome + '</td><td>' + data[i].endereco + '</td><td><button class="del btn btn-xs btn-default" valor="'+data[i].codigo+'" type="button"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>&nbsp;<button class="edt btn btn-xs btn-default" valor="'+data[i].codigo+'" type="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></td></tr>');
			}
			
		});
		
	}
	
	function resetForm(){
	
		$("#botoesedit").hide();
		$("#botaocad").show();
		$("#txtcod").prop("readonly",false);
		$('#frmCadCli')[0].reset();
	
	}
	
	$(document).on("click", ".del", function(){
		$.post("clientes/del/",{cod: $(this).attr("valor") },function(data){
			lista();
		});
	

	
	});
	
	
	$(document).on("click", ".edt", function(){
		$.post("clientes/loadData/",{cod: $(this).attr("valor") },function(data){
			data=$.parseJSON(data);
			
			$("#botoesedit").show();
			$("#botaocad").hide();
			
			$("#txtcod").prop("readonly",true);

			$("#txtcod").val(data[0].codigo);
			$("#txtcpf").val(data[0].cpf);
			$("#txtnome").val(data[0].nome);
			$("#txtend").val(data[0].endereco);
			$("#txtfone").val(data[0].telefone);
			
		});
		
	});
	
	$(document).on("click", "#btnCancel", function(){
	
		resetForm();
		
	});
	
	$(document).on("click", "#btnSave", function(){
		$.post("clientes/save/",$("#frmCadCli").serialize(),function(data){
			alert(data);
			lista();
			resetForm();
		});
	   
	});
	
});