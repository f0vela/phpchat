<?php
include('includes/opclass.php');
$op = new OpClass();

if(isset($_GET['ax']) && $_GET['ax'] == 'alta')
{
	include('init.php');
	include('includes/conn.php');
	$op->changeOpStatus(array('status'=>'A','userID'=>fRequest::get('uid')));
	header('location: main.php?p=config&sp=admusuarios&msg=Usuario dado de alta!');
}

if(isset($_GET['ax']) && $_GET['ax'] == 'baja')
{
	include('init.php');
	include('includes/conn.php');
	$op->changeOpStatus(array('status'=>'B','userID'=>fRequest::get('uid')));
	header('location: main.php?p=config&sp=admusuarios&msg=Usuario dado de alta!');
}


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
		$op->saveData($data);
		echo "<script>window.location = 'main.php?p=config&sp=admusuarios&msg=Usuario%20editado%20con%20exito!';</script>";
	}else{
		echo 'error';
	}
}

$dat = $op->getMyData(fRequest::get('uid'));
while($d = mysql_fetch_assoc($dat))
{
?>
<form action="main.php" method="post" accept-charset="utf-8">
	<fieldset id="perfil_del_usuario" class="">
		<legend>Perfil del usuario</legend>
		<label for="nombre:">Nombre:</label><input type="text" name="nombre" value="<?php echo $d['name']; ?>" id="nombre" size="30">
		<label for="email">Email:</label><input type="text" name="email" value="<?php echo $d['email']; ?>" id="email" size="30">
		<label for="contrasenia">Contrase&ntilde;a:</label><input type="password" name="contrasenia" value="" id="contrasenia" size="30">
		<label for="confirmar">Confirmar Contrase&ntilde;a:</label><input type="password" name="confirmar" value="" id="confirmar" size="30">
		
		<label for="tipo">Tipo:</label>
			<select name="tipo" id="tipo">
				<option value="admin" <?php if($d['usertype'] == 'admin') echo 'selected="selected"'; ?>>Administrador</option>
				<option value="operator" <?php if($d['usertype'] == 'operator') echo 'selected="selected"'; ?>>Operador</option>
			</select>
		<label for="estatus">Estatus:</label>
			<select name="estatus" id="estatus">
				<option value="A" <?php if($d['status'] == 'A') echo 'selected="selected"'; ?>>Alta</option>
				<option value="B" <?php if($d['status'] == 'B') echo 'selected="selected"'; ?>>Baja</option>
			</select>
		<input type="hidden" name="p" value="config" id="p">
		<input type="hidden" name="sp" value="usuariosedit" id="p">
		<input type="hidden" name="ax" value="guardar" id="ax">
		<input type="hidden" name="uid" value="<?php echo $d['userID']; ?>" id="uid">	
		<p>
			<input type="submit" value="Guardar Cambios">
			<input type="button" value="Cancelar Cambios" onclick="history.back(-1);">
		</p>
	</fieldset>
</form>
<?php } ?>
<br />