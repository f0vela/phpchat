<?php
include('includes/opclass.php');
$op = new OpClass();

if(isset($_POST['ax']) && $_POST['ax'] == 'guardar')
{
	$data = array(
			'userID' => fRequest::get('uid'),
			'name' => fRequest::get('nombre'),
			'email' => fRequest::get('email'),
			'password' => fRequest::get('contrasenia'),
			'status' => strtoupper(fRequest::get('estatus')),
			'usertype' => fRequest::get('tipo')
		);
	$continuar = false;
	if($data['password'] != '')
	{
		if($data['password'] == fRequest::get('confirmar')){
			if($data['email'] != '' && $data['name'] != ''){
				$continuar = true;
			}
		}
	}else{
		if($data['email'] != '' && $data['name'] != ''){
			$continuar = true;
		}
	}
	if($continuar)
	{
		$op->CreateOp($data);
		echo "<script>window.location = 'main.php?p=config&sp=admusuarios&msg=Usuario%20creado%20con%20exito!';</script>";
	}else{
		echo 'error';
	}
}

?>
<form action="main.php" method="post" accept-charset="utf-8">
	<fieldset id="perfil_del_usuario" class="">
		<legend>Perfil del usuario</legend>
		<label for="nombre:">Nombre:</label><input type="text" name="nombre" value="" id="nombre" size="30">
		<label for="email">Email:</label><input type="text" name="email" value="" id="email" size="30">
		<label for="contrasenia">Contrase&ntilde;a:</label><input type="password" name="contrasenia" value="" id="contrasenia" size="30">
		<label for="confirmar">Confirmar Contrase&ntilde;a:</label><input type="password" name="confirmar" value="" id="confirmar" size="30">
		
		<label for="tipo">Tipo:</label>
			<select name="tipo" id="tipo">
				<option value="admin">Administrador</option>
				<option value="operator" selected="selected">Operador</option>
			</select>
		<label for="estatus">Estatus:</label>
			<select name="estatus" id="estatus">
				<option value="A" selected="selected">Alta</option>
				<option value="B">Baja</option>
			</select>
		<input type="hidden" name="p" value="config" id="p">
		<input type="hidden" name="sp" value="usuariosadd" id="p">
		<input type="hidden" name="ax" value="guardar" id="ax">
		<p>
			<input type="submit" value="Guardar Cambios">
			<input type="button" value="Cancelar Cambios" onclick="history.back(-1);">
		</p>
	</fieldset>
</form>
<br />