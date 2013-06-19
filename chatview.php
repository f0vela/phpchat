<?php
include('init.php');
include('includes/conn.php');
include('includes/chatclass.php');

$sessionID = fRequest::get('sesid');
$userID = fRequest::get('uid');
$deptID = fRequest::get('did');
$requestID = fRequest::get('reqid');
$chat = new ChatClass();

$cliente = $chat->getClientData($sessionID,$deptID,$userID,$requestID);
$conv = $chat->getChatTranscript($sessionID,$deptID,$userID,$requestID);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>CRM :: Chat Interactivo</title>
	<meta name="content-type" content="text/html;" http-equiv="content-type" charset="utf-8">
	<link rel="stylesheet" href="css/default.css" type="text/css" />
	<link rel="stylesheet" href="css/start/jquery-ui-1.8.5.custom.css" type="text/css" />
	<script type="text/javascript" charset="utf-8" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="js/jquery-ui.1.8.5.custom.min.js"></script>
</head>
<body>
	<div id="dash_container">
		<div id="dash_header">
			<div id="dash_logo">
				<a href="#lastline">
					<img src="images/<?php echo $chat->getChatLogo($deptID); ?>" border="0" />
				</a>
			</div>
		</div>
	<div id="dash_content">
		<div id="chatquestion">
			<?php echo $chat->getQuestion($sessionID,$requestID); ?>
		</div>
		<div id="chatwindow">
			<div id="chattext">
				<?php while($c = mysql_fetch_assoc($conv)){
					echo $c['formatted'];
				}
				?>
			</div>
		</div>
		<div id="chatmiddle">
			<div id="chatmenu">
				<button id="terminarchat" onclick="self.close();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover ui-state-active" role="button" aria-disabled="false"><span class="ui-button-text">Cerrar Ventana</span></button>
			</div>
		</div>
		<div id="chatuserinfo">
			<table border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr class="tableheader"><td colspan="2"><strong>Datos del Cliente:</strong></td></tr>
				<tr><td><strong>Nombre:</strong></td><td><?php echo $cliente['name']; ?></td></tr>
				<tr><td><strong>Correo:</strong></td><td><?php echo $cliente['email']; ?></td></tr>
				<tr><td><strong>Tel&eacute;fono:</strong></td><td><?php echo $cliente['telephone']; ?></td></tr>
				<tr><td><strong>Navegador:</strong></td><td><?php echo $cliente['browser_type']; ?></td></tr>
			</table>
		</div>
	</div>
	<div id="dash_footer">
		Derechos Reservados &copy; ICU Publicidad 2010
	</div>
</div>