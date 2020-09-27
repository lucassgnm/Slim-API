
		<h1>Cadastro de Clientes</h1>
		<form name="frmCadCli" id="frmCadCli" method="post" role="form" action="">
			<div class="row">
				<div class="form-group col-md-2 col-lg-1">
				    <label>Código:</label>
				    <input type="text" class="form-control" id="txtcod" name="txtcod" placeholder="Codigo" maxlength="4">
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-2 col-lg-2">
				    <label>CPF:</label>
				    <input type="text" class="form-control" id="txtcpf" name="txtcpf" placeholder="CPF" maxlength="14">
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-8 col-lg-6">
				    <label>Nome:</label>
				    <input type="text" class="form-control" id="txtnome" name="txtnome" placeholder="Nome" maxlength="40">
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-8 col-lg-6">
				    <label>Endereço:</label>
				    <input type="text" class="form-control" id="txtend" name="txtend" placeholder="Endereço" maxlength="40">
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-2 col-lg-2">
				    <label>Telefone:</label>
				    <input type="text" class="form-control" id="txtfone" name="txtfone" placeholder="Telefone" maxlength="16">
				</div>
			</div>
			<div class="row">
				<div class="form-group col-md-2 col-lg-2">
				    <label>Senha:</label>
				    <input type="password" class="form-control" id="txtsenha" name="txtsenha" placeholder="Senha" maxlength="10">
				</div>
			</div>
			<div id="botaocad">
				<button type="submit" id="btnCad" name="btnCad" class="btn btn-default">
					Cadastrar
				</button>
			</div>
			<div id="botoesedit">
				<button type="button" id="btnSave" name="btnSave" class="btn btn-default">
					Gravar
				</button>
				<button type="button" name="btnCancel" id="btnCancel" class="btn btn-default">
					Cancelar
				</button>
			</div>
		</form>
		<br>
		<div>
		
			<table class="table table-hover" id="tabres">
				<thead>
					<tr>
						<th>CPF</th>
						<th>Nome</th>
						<th>Endereço</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody id="lsclientes">
				</tbody>
			</table>
		</div>

