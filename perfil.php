<?php
include('includes/opclass.php');
$op = new OpClass();

if(isset($_POST['ax']) && $_POST['ax'] == 'guardar')
{
	$data = array(
			'userID' => fRequest::get('uid'),
			'name' => fRequest::get('nombre'),
			'email' => fRequest::get('email'),
			'password' => fRequest::get('contrasenia')
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
		echo "<script>window.location = 'main.php?p=userprofile&uid=".$data['userID']."';</script>";
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
		<input type="hidden" name="p" value="userprofile" id="p">
		<input type="hidden" name="ax" value="guardar" id="ax">
		<input type="hidden" name="uid" value="<?php echo $d['userID']; ?>" id="uid">	
		<p><input type="submit" value="Guardar Cambios"></p>
	</fieldset>
</form>
<?php } ?>