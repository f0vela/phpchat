<?php
include('init.php');
include('includes/conn.php');
include('includes/opclass.php');
include('includes/chatclass.php');

$sID = fRequest::get('sid');
$deptID = fRequest::get('did');
$nombre = fRequest::get('nombre');
$email = fRequest::get('email');
$consulta = fRequest::get('consulta');
$motivo = fRequest::get('motivo');
$modelo = fRequest::get('modelo');
$direccion = fRequest::get('direccion');
$telefono = fRequest::get('telefono');

$op = new OpClass();
$chat = new ChatClass();

if(isset($_POST['email']) && $_POST['email'] != ''){
	
	$mysql_db->execute("INSERT INTO chatmessages (siteID,deptID,from_screen_name,email,question) VALUES ($sID,$deptID,'$nombre','$email','$consulta')");
	
	$mail = new fEmail();
	
	$smtp = new fSMTP('mail.mazdaesdidea.com');
	$smtp->authenticate('crm@mazdaesdidea.com','wzcOE1bu?VcL');	

	$mail->setFromEmail('crm@mazdaesdidea.com');
	$mail->addRecipient('seguimientoscrm@grupotecun.com');
	$mail->addCCRecipient('f0vela@gmail.com');
	$siteName = $chat->getSiteName($sID);
	
	$mail->setSubject('Seguimiento desde '.$siteName.' ');
	
	$mailBody = '<strong>Nombre:</strong> '.$nombre.' <br />';
	$mailBody .= '<strong>Telefono:</strong> '.$telefono.' <br />';
	$mailBody .= '<strong>Direccion:</strong> '.$direccion.' <br />';
	$mailBody .= '<strong>Email:</strong> '.$email.' <br />';
	$mailBody .= '<strong>Modelo:</strong> '.$modelo.' <br />';
	$mailBody .= '<strong>Motivo del mensaje:</strong> '.$motivo.' <br /><hr />';
	$mailBody .= '<strong>Mensaje:</strong> <br />'.$consulta.' <br />';
	
	
	$mail->setBody('Ver en HTML');	
	
	$mail->setHTMLBody($mailBody);
	
	$message_id = $mail->send($smtp);
	$smtp->close();
}

?>
<html>
<head>
	<title>CRM :: Chat Interactivo</title>
	<link rel="stylesheet" href="css/default.css" type="text/css" />
	<script charset="utf-8" src="js/jquery-1.4.2.min.js"></script>
	<script>self.close();</script>
</head>
<body>
	<div id="dash_container">
		<div id="dash_header">
			<div id="dash_logo">
			<img src="images/<?php echo $chat->getChatLogoSite($sID); ?>" border="0" />
			</div>
		</div>
	<div id="dash_content">
			<fieldset>
			<h3>Gracias por comunicarse con nosotros</h3>
			<p><input type="submit" value="Cerrar" onclick="self.close(); Shadowbox.close();"></p>
			</fieldset>
	</div>
	<div id="dash_footer">
		Derechos Reservados &copy; ICU Publicidad 2010
	</div>
</div>