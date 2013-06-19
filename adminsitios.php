<?php
include('includes/opclass.php');
include('includes/chatclass.php');
include('includes/adminclass.php');

$op = new OpClass();
$chat = new ChatClass();
$adm = new AdminClass();

$userID = fSession::get('userid');
$nombre = fRequest::get('nombre');
$url  = fRequest::get('url');
$filtros = array(
			'nombre'=>$nombre,
			'url'=>$url
			);
$userlist = $adm->getSiteList($filtros);

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
					<td>URL:</td>
					<td>
						<input type="text" name="url" id="url" value="<?php echo fRequest::get('url');?>" />
					</td>
					<td>
						<input type="hidden" name="p" value="config" id="p">
						<input type="hidden" name="sp" value="admsitios" id="sp">
						<input type="submit" name="Buscar" value="Buscar" id="Buscar">
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td colspan="4" align="right">
		<a href="main.php?p=config&sp=sitiosadd" class="addbutton">Agregar Sitio <img src="images/Sites_Crear.png" border="0" alt="Agregar Nuevo Sitio"></a>
		</td>
	</tr>
	<tr class="tableheader">
		<td>Nombre</td>
		<td>URL</td>
		<td>Acciones</td>
	</tr>
	<?php 
		$scc = 0;
		while($u = mysql_fetch_assoc($userlist)){
		$scc++;
	?>
	<tr class="<?php echo $chat->RowColor($scc,'normal','even'); ?>">
		<td><?php echo $u['name']; ?></td>
		<td><?php echo $u['url']; ?></td>
		<td>
			<a href="main.php?p=config&sp=sitiosedit&sid=<?php echo $u['siteID']; ?>"><img src="images/Sites_Editar.png" border="0" width="24" alt="Editar" /></a>
			<?php if($u['status'] == '1'){ ?>
			<a href="adminsitios_edit.php?ax=baja&sid=<?php echo $u['siteID']; ?>"><img src="images/Sites_Baja.png" width="24" border="0" alt="Baja" /></a>
			<?php }else{ ?>
			<a href="adminsitios_edit.php?ax=alta&sid=<?php echo $u['siteID']; ?>"><img src="images/Sites_Alta.png" width="24" border="0" alt="Alta" /></a>
			<?php } ?>
			<a href="main.php?p=config&sp=admdeptos&sid=<?php echo $u['siteID']; ?>"><img src="images/Deptos.png" border="0" width="24" alt="Administrar Departamentos" /></a>
		</td>
	</tr>
	<?php } ?>
</table>
<br />