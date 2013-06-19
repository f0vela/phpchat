<?php
include('includes/opclass.php');
include('includes/chatclass.php');
include('includes/adminclass.php');

$op = new OpClass();
$chat = new ChatClass();
$adm = new AdminClass();

$userID = fSession::get('userid');
$nombre = fRequest::get('nombre');
$email  = fRequest::get('email');
$filtros = array(
			'nombre'=>$nombre,
			'email'=>$email
			);
$userlist = $adm->getUserList($filtros);

?>
<br />
<table width="70%" cellpadding="3" cellspacing="0" align="center">
	<tr>
		<td colspan="4">
			<form action="main.php" method="get" accept-charset="utf-8">
			<table border="0" cellspacing="0" cellpadding="5" width="60%" align="center">
				<tr class="tablefilters">
					<?php if($isAdmin){?>
					<td>Operador:</td>
					<td>
						<select name="operador" id="operador">
							<option value="">Seleccione...</option>
							<?php while($o = mysql_fetch_assoc($ops)){ ?>
							<option value="<?php echo $o['userID']; ?>" <?php if(fRequest::get('operador') == $o['userID']){ echo 'selected="selected"'; }?>><?php echo $o['name']; ?></option>
							<?php } ?>							
						</select>
					</td>
					<?php } ?>
					<td>Nombre:</td>
					<td>
						<input type="text" name="nombre" id="nombre" value="<?php echo fRequest::get('nombre');?>" />
					</td>
					<td>Email:</td>
					<td>
						<input type="text" name="email" id="email" value="<?php echo fRequest::get('email');?>" />
					</td>
					<td>
						<input type="hidden" name="p" value="config" id="p">
						<input type="hidden" name="sp" value="admusuarios" id="sp">
						<input type="submit" name="Buscar" value="Buscar" id="Buscar">
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td colspan="4" align="right">
		<a href="main.php?p=config&sp=usuariosadd" class="addbutton">Agregar Usuario <img src="images/User_Crear.png" border="0" alt="Agregar Nuevo Sitio"></a>
		</td>
	</tr>
	<tr class="tableheader">
		<td>Nombre</td>
		<td>Email</td>
		<td>Tipo</td>
		<td>Acciones</td>
	</tr>
	<?php 
		$scc = 0;
		while($u = mysql_fetch_assoc($userlist)){
		$scc++;
	?>
	<tr class="<?php echo $chat->RowColor($scc,'normal','even'); ?>">
		<td><?php echo $u['name']; ?></td>
		<td><?php echo $u['email']; ?></td>
		<td><?php echo $u['usertype']; ?></td>
		<td>
			<a href="main.php?p=config&sp=usuariosedit&uid=<?php echo $u['userID']; ?>"><img src="images/User_Edit.png" border="0" width="24" alt="Editar" /></a>
			<?php if($u['status'] == 'A'){ ?>
			<a href="adminusuarios_edit.php?ax=baja&uid=<?php echo $u['userID']; ?>"><img src="images/User_Baja.png" width="24" border="0" alt="Baja" /></a>
			<?php }else{ ?>
			<a href="adminusuarios_edit.php?ax=alta&uid=<?php echo $u['userID']; ?>"><img src="images/User_Alta.png" width="24" border="0" alt="Alta" /></a>
			<?php } ?>
		</td>
	</tr>
	<?php } ?>
</table>
<br />