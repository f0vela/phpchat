<?php
include('includes/opclass.php');
include('includes/adminclass.php');
$op = new OpClass();
$adm = new AdminClass();

if(isset($_POST['ax']) && $_POST['ax'] == 'guardar')
{
	$data = array(
			'siteID' => fRequest::get('sid'),
			'name' => fRequest::get('nombre'),
			'email' => fRequest::get('email'),
			'visible' => fRequest::get('visible')
		);
	$adm->createDepartmentData($data);
	echo "<script>window.location = 'main.php?p=config&sp=admdeptos&sid=".fRequest::get('sid')."&msg=Departamento%20creado%20con%20exito!';</script>";
}

?>
<form action="main.php" method="post" accept-charset="utf-8">
	<fieldset id="perfil_del_usuario" class="">
		<legend>Datos del Departamento</legend>
		<label for="nombre:">Nombre:</label><input type="text" name="nombre" value="" id="nombre" size="30">
		<label for="email">Email:</label><input type="text" name="email" value="" id="email" size="30">		
		<label for="visible">Visible:</label>
			<select name="visible" id="visible">
				<option value="1" selected="selected">Visible</option>
				<option value="0">Escondido</option>
			</select>
		<input type="hidden" name="p" value="config" id="p">
		<input type="hidden" name="sp" value="deptosadd" id="p">
		<input type="hidden" name="sid" value="<?php echo fRequest::get('sid'); ?>" id="sid">
		<input type="hidden" name="ax" value="guardar" id="ax">
		<p>
			<input type="submit" value="Guardar Cambios">
			<input type="button" value="Cancelar Cambios" onclick="history.back(-1);">
		</p>
	</fieldset>
</form>
<br />