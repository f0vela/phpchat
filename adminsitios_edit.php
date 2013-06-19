<?php
include('includes/opclass.php');
include('includes/adminclass.php');
$op = new OpClass();
$adm = new AdminClass();

if(isset($_GET['ax']) && $_GET['ax'] == 'alta')
{
	include('init.php');
	include('includes/conn.php');
	$adm->changeSiteStatus(array('status'=>'1','siteID'=>fRequest::get('sid')));
	header('location: main.php?p=config&sp=admsitios&msg=Sitio dado de alta!');
}

if(isset($_GET['ax']) && $_GET['ax'] == 'baja')
{
	include('init.php');
	include('includes/conn.php');
	$adm->changeSiteStatus(array('status'=>'0','siteID'=>fRequest::get('sid')));
	header('location: main.php?p=config&sp=admsitios&msg=Sitio dado de baja!');
}


if(isset($_POST['ax']) && $_POST['ax'] == 'guardar')
{
	$data = array(
			'siteID' => fRequest::get('sid'),
			'name' => fRequest::get('nombre'),
			'status' => fRequest::get('estatus'),
			'url' => fRequest::get('url')
		);
	$adm->saveSiteData($data);
	echo "<script>window.location = 'main.php?p=config&sp=admsitios&msg=Sitio%20editado%20con%20exito!';</script>";
}

$dat = $adm->getSiteData(fRequest::get('sid'));
while($d = mysql_fetch_assoc($dat))
{
?>
<form action="main.php" method="post" accept-charset="utf-8">
	<fieldset id="perfil_del_usuario" class="">
		<legend>Datos del Sitio</legend>
		<label for="nombre:">Nombre:</label><input type="text" name="nombre" value="<?php echo $d['name']; ?>" id="nombre" size="30">
		<label for="email">URL:</label><input type="text" name="url" value="<?php echo $d['url']; ?>" id="url" size="30">		
		<label for="estatus">Estatus:</label>
			<select name="estatus" id="estatus">
				<option value="1" <?php if($d['status'] == '1') echo 'selected="selected"'; ?>>Alta</option>
				<option value="0" <?php if($d['status'] == '0') echo 'selected="selected"'; ?>>Baja</option>
			</select>
		<input type="hidden" name="p" value="config" id="p">
		<input type="hidden" name="sp" value="sitiosedit" id="p">
		<input type="hidden" name="ax" value="guardar" id="ax">
		<input type="hidden" name="sid" value="<?php echo $d['siteID']; ?>" id="sid">	
		<p>
			<input type="submit" value="Guardar Cambios">
			<input type="button" value="Cancelar Cambios" onclick="history.back(-1);">
		</p>
	</fieldset>
</form>
<?php } ?>
<br />