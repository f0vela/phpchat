<?php
include('init.php');
include('includes/conn.php');
include('includes/opclass.php');
include('includes/chatclass.php');

fSession::open();
$sessionID = fRequest::get('sesid');
$userID = fRequest::get('uid');
$deptID = fRequest::get('did');
$requestID = fRequest::get('reqid');
$op = new OpClass();
$chat = new ChatClass();

$cl = $chat->getClientData($sessionID, $deptID, $userID, $requestID);

$operador = false;
if(isset($_GET['op']) && $_GET['op'] == true)
{
	$operador = true;
}
?>
<html>
<head>
	<title>CRM :: Chat Interactivo</title>
	<link rel="stylesheet" href="css/default.css" type="text/css" />
	<link rel="stylesheet" href="js/jquery.rating.css" type="text/css" media="screen" title="no title" charset="utf-8">
	<script type="text/javascript" charset="utf-8" src="js/jquery-1.4.2.min.js"></script>
	<script src="js/jquery.MetaData.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.rating.pack.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
	<div id="dash_container">
		<div id="dash_header">
			<div id="dash_logo">
			<img src="images/<?php echo $chat->getChatLogo($deptID); ?>" border="0" />
			</div>
		</div>
	<div id="dash_content">
		<div style="text-align:center"><br /><br />
			<?php if($operador){ 
			?>
				<input type="button" name="Cerrar" value="Cerrar Ventana" id="Cerrar" onclick="self.close();">
				<br/><br/>
			<?php
			}else{
			?>
			<h3>Gracias por preferirnos.</h3>
			<p>porfavor tome un momento para calificar nuestro soporte.</p>
				<form action="chatprocess.php" method="get">
					<div style="display:block; margin: 0 auto; width:110px ">
						<input name="star1" type="radio" class="star" value="1"/>
						<input name="star1" type="radio" class="star" value="2"/>
						<input name="star1" type="radio" class="star" value="3"/>
						<input name="star1" type="radio" class="star" value="4"/>
						<input name="star1" type="radio" class="star" value ="5"/>
					</div>
				<br/><br/>
				<input type="hidden" name="ax" value="calificar" id="calificar" />
				<input type="hidden" name="sesid" value="<?php echo $sessionID; ?>"/>
				<input type="hidden" name="reqid" value="<?php echo $requestID; ?>"/>
				<input type="hidden" name="did" value="<?php echo $deptID; ?>"/>
				<input type="hidden" name="uid" value="<?php echo $userID; ?>"/>
				<br />
				<table width="380px" border="0" align="center" cellspacing="0" cellpadding="4" style="margin:0 auto; background-color:#ffffcc; padding:4px">
					<tr>
						<td><label for="desea_recibir_copia_de_la_sesi&oacute;n?">Deseo recibir una copia:</label></td>
						<td><input type="checkbox" name="copia" value="1" id="copia"></td>
					</tr>
					<tr>
						<td><label for="email">Email:</label></td>
						<td><input type="text" name="email" value="<?php echo $cl['email']; ?>" id="email" size="35"></td>
					</tr>
				</table>
				<br />
				<input type="submit" value="Calificar" id="calificar" name="calificar" />
				<br/><br/>
				</form>
				<?php } ?>
		</div>
	</div>
	<div id="dash_footer">
		Derechos Reservados &copy; ICU Publicidad 2010
	</div>
</div>