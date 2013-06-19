<?php
include('includes/opclass.php');
include('includes/adminclass.php');
$op = new OpClass();
$adm = new AdminClass();

if(isset($_GET['ax']) && $_GET['ax'] == 'alta')
{
	include('init.php');
	include('includes/conn.php');
	$adm->changeDepartmentStatus(array('visible'=>'1','siteID'=>fRequest::get('sid'),'deptID'=>fRequest::get('did')));
	header('location: main.php?p=config&sp=admdeptos&sid='.fRequest::get('sid').'&msg=El departamento es ahora visible!');
}

if(isset($_GET['ax']) && $_GET['ax'] == 'baja')
{
	include('init.php');
	include('includes/conn.php');
	$adm->changeDepartmentStatus(array('visible'=>'0','siteID'=>fRequest::get('sid'),'deptID'=>fRequest::get('did')));
	header('location: main.php?p=config&sp=admdeptos&sid='.fRequest::get('sid').'&msg=El departamento esta ahora escondido');
}


if(isset($_POST['ax']) && $_POST['ax'] == 'guardar')
{
	$data = array(
			'siteID' => fRequest::get('sid'),
			'deptID' => fRequest::get('did'),
			'name' => fRequest::get('nombre'),
			'visible' => fRequest::get('visible'),
			'email' => fRequest::get('email')
		);
	$adm->saveDepartmentData($data);
	echo "<script>window.location = 'main.php?p=config&sp=admdeptos&sid=".fRequest::get('sid')."&msg=Departamento%20editado%20con%20exito!';</script>";
}

$dat = $adm->getDepartmentData(fRequest::get('sid'),fRequest::get('did'));
while($d = mysql_fetch_assoc($dat['data']))
{
?>
<form action="main.php" method="post" accept-charset="utf-8">
	<fieldset id="perfil_del_usuario" class="">
		<legend>Datos del Departamento</legend>
		<label for="nombre:">Nombre:</label><input type="text" name="nombre" value="<?php echo $d['name']; ?>" id="nombre" size="30">
		<label for="email">Email:</label><input type="text" name="url" value="<?php echo $d['email']; ?>" id="url" size="30">		
		<label for="visible">Visible:</label>
			<select name="visible" id="visible">
				<option value="1" <?php if($d['visible'] == '1') echo 'selected="selected"'; ?>>Visible</option>
				<option value="0" <?php if($d['visible'] == '0') echo 'selected="selected"'; ?>>Escondido</option>
			</select>
		<input type="hidden" name="p" value="config" id="p">
		<input type="hidden" name="sp" value="deptosedit" id="p">
		<input type="hidden" name="ax" value="guardar" id="ax">
		<input type="hidden" name="sid" value="<?php echo $d['siteID']; ?>" id="sid">	
		<input type="hidden" name="did" value="<?php echo $d['deptID']; ?>" id="did">	
		<p>
			<input type="submit" value="Guardar Cambios">
			<input type="button" value="Cancelar Cambios" onclick="history.back(-1);">
		</p>
	</fieldset>
</form>
<?php } ?>
<br />