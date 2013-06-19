<?php
include('includes/opclass.php');
include('includes/chatclass.php');

$op = new OpClass();
$chat = new ChatClass();
$cuenta = fRequest::get('cuenta');
$fecha = fRequest::get('fecha');
$operador = fRequest::get('operador');
$isAdmin = false;
$filtros = array(
	'cuenta'=>$cuenta,
	'fecha'=>$fecha
	);

// evy palacios = 3

if(fSession::get('tipo') == 'operator' && fSession::get('tipo') != ''){
	$userID = fSession::get('userid');
}else{
	$isAdmin = true;
	$userID = 0;
}

if($operador != ''){ $userID = $operador; }

$llamadas = $op->getMyHistoryCalls($userID,$filtros); 
$depts = $chat->getDepartmentList();
$ops = $chat->getOperatorList();
$scc = 0;
?>
<link rel="stylesheet" href="css/start/jquery-ui-1.8.5.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">
<script src="js/jquery-ui-1.8.5.custom.min.js" type="text/javascript" charset="utf-8"></script>
<script>
 	$(document).ready(function() {
    	$("#fecha").datepicker({dateFormat: 'yy-mm-dd'});
	});

	function verChat(sessionID,deptID,userID,requestID)
	{
		window.open('chatview.php?did='+deptID+'&uid='+userID+'&sesid='+sessionID+'&reqid='+requestID,'verchat','width=460px, height=700px')
	}
  </script>

<br />
<table border="0" cellspacing="0" cellpadding="3" width="80%" align="center">
	<tr>
		<td colspan="5">
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
					<td>Cuenta:</td>
					<td>
						<select name="cuenta" id="cuenta">
							<option value="">Seleccione...</option>
							<?php while($d = mysql_fetch_assoc($depts)){ ?>
							<option value="<?php echo $d['deptID']; ?>" <?php if(fRequest::get('cuenta') == $d['deptID']){ echo 'selected="selected"'; }?>><?php echo $d['name']; ?></option>
							<?php } ?>							
						</select>
					</td>
					<td>Fecha:</td>
					<td><input type="text" name="fecha" value="<?php echo fRequest::get('fecha');?>" id="fecha"></td>
					<td>
						<input type="hidden" name="p" value="historial" id="p">
						<input type="submit" name="Buscar" value="Buscar" id="Buscar">
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr class="tableheader">
		<td>Cuenta</td>
		<td>Operador Asignado</td>
		<td>Nombre</td>
		<td>Fecha y Hora</td>
		<td>Estado</td>
		<td>Ver chat</td>
	</tr>	
<?php
if($llamadas['rows'] > 0)
{
	while($l = mysql_fetch_assoc($llamadas['data']))
	{
		$scc++;
		echo '<tr class="'.$chat->RowColor($scc,'normal','even').'">';
		echo '<td>'.$l['deptName'].'</td>';
		echo '<td>'.$l['operatorName'].'</td>';
		echo '<td>'.$l['from_screen_name'].'</td>';
		echo '<td>'.$l['created'].'</td>';
		echo '<td>';
		switch($l['status']){
			case 0:
				echo 'En Espera';
				break;
			case 1:
				echo 'Atendida';
				break;
			case 2:
				echo 'Cerrada';
			break;
		}
		echo '</td>';
		echo '<td>';
		echo '<a href="javascript: void(0);" onclick="verChat(\''.$l['sessionID'].'\',\''.$l['deptID'].'\',\''.$l['userID'].'\',\''.$l['requestID'].'\');"><img src="images/Discussion.png" border="0" width="24px" alt="Ver Chat" /></a>';
		echo '</td>';
		echo '</tr>';
	}
}else{
	echo '<td colspan="5"><strong>Sin Historial.</strong></td>';
}
?>
</table>
<br />