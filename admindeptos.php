<?php
include('includes/opclass.php');
include('includes/chatclass.php');
include('includes/adminclass.php');

$op = new OpClass();
$chat = new ChatClass();
$adm = new AdminClass();

$nombre = fRequest::get('nombre');
$siteID = fRequest::get('sid');
$filtros = array(
			'siteID'=>$siteID,
			'name'=>$nombre
			);
$deptlist = $adm->getDepartmentList($filtros);

?>
<br />
<table width="70%" cellpadding="3" cellspacing="0" align="center">
	<tr>
		<td colspan="4">
			<form action="main.php" method="get" accept-charset="utf-8">
			<table border="0" cellspacing="0" cellpadding="5" width="60%" align="center">
				<tr class="tablefilters">
					<td>Nombre:</td>
					<td>
						<input type="text" name="nombre" id="nombre" value="<?php echo fRequest::get('nombre');?>" />
					</td>
					<td>
						<input type="hidden" name="p" value="config" id="p">
						<input type="hidden" name="sp" value="admdeptos" id="sp">
						<input type="hidden" name="sid" value="<?php echo fRequest::get('sid')?>" id="sid">
						<input type="submit" name="Buscar" value="Buscar" id="Buscar">
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td colspan="4" align="right">
		<a href="main.php?p=config&sp=deptosadd&sid=<?php echo fRequest::get('sid'); ?>" class="addbutton">Agregar Departamento <img src="images/Deptos_Crear.png" border="0" alt="Agregar Nuevo Departamento"></a>
		</td>
	</tr>
	<tr class="tableheader">
		<td>Nombre</td>
		<td>Email</td>
		<td>Acciones</td>
	</tr>
	<?php 
		$scc = 0;
		if($deptlist['rows'] > 0)
		{
		while($u = mysql_fetch_assoc($deptlist['data'])){
		$scc++;
	?>
	<tr class="<?php echo $chat->RowColor($scc,'normal','even'); ?>">
		<td><?php echo $u['name']; ?></td>
		<td><?php echo $u['email']; ?></td>
		<td>
			<a href="main.php?p=config&sp=deptosedit&sid=<?php echo $u['siteID']; ?>&did=<?php echo $u['deptID']; ?>"><img src="images/Deptos_Edit.png" border="0" width="24" alt="Editar" /></a>
			<?php if($u['visible'] == '1'){ ?>
			<a href="admindeptos_edit.php?ax=baja&sid=<?php echo $u['siteID']; ?>&did=<?php echo $u['deptID']; ?>"><img src="images/Deptos_Baja.png" width="24" border="0" alt="Baja" /></a>
			<?php }else{ ?>
			<a href="admindeptos_edit.php?ax=alta&sid=<?php echo $u['siteID']; ?>&did=<?php echo $u['deptID']; ?>"><img src="images/Deptos_Alta.png" width="24" border="0" alt="Alta" /></a>
			<?php } ?>
		</td>
	</tr>
	<?php }
		}
	?>
</table>
<br />