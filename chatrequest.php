<?php
include('init.php');
include('includes/conn.php');
include('includes/opclass.php');
include('includes/chatclass.php');

fSession::open();
$sID = fRequest::get('sid');
$op = new OpClass();
$chat = new ChatClass();

$isopavail = $op->isOperatorAvailable($sID);
if($isopavail == 0) header('location: chatmessage.php?sid='.$sID);
?>
<html>
<head>
	<title>CRM :: Chat Interactivo</title>
	<link rel="stylesheet" href="css/default.css" type="text/css" />
	<script type="text/javascript" charset="utf-8" src="js/jquery-1.4.2.min.js"></script>
	<script>
		function checkform()
		{
			var nombre = $('#nombre').val();
			var email = $('#email').val();
			var consulta = $('#consulta').val();
			
			var ret = false;
			var texto = 'Es necesario que:  \n';
			var txt = ''; 
			
			if(nombre == "") 	{ txt = txt+' - Nombre \n'; }
			if(email == "") 	{ txt = txt+' - Email \n'; }
			if(consulta == "") 	{ txt = txt+' - Consulta \n'; }
			
			if(txt != ''){ texto = texto+''+txt; }
			
			texto = texto + ' sean ingresados para poder continuar. ';
			
			if(txt != '') { 
				alert(texto); 
				return false;
			}else{
				alert('Gracias por preferirnos');
				return true;
			}
			
		}
	</script>
</head>
<body>
	<div id="dash_container">
		<div id="dash_header">
			<div id="dash_logo">
			<img src="images/<?php echo $chat->getChatLogoSite($sID); ?>" border="0" />
			</div>
		</div>
	<div id="dash_content">
		<form action="chatrequestlobby.php" method="post" accept-charset="utf-8">
			<fieldset>
			<h3>Solicite una sesion de asistencia llenando estos datos:</h3>
			<label for="area">Area para consulta: <span class="required">*</span></label>
			<ul>
			<?php
			$deptos = $mysql_db->query("SELECT * FROM chatdepartments WHERE siteID = %s AND visible = 1",$sID);
			$dc = 0;
			foreach($deptos as $d)
			{
			?>
				<li><input type="radio" name="did" value="<?php echo $d['deptID']; ?>" id="did" <?php if($dc == 0) echo 'checked="checked"'; ?>> <?php echo $d['name']; ?></li>
			<?php
			$dc++;
			}
			?>
			</ul>			
			<label for="nombre">Nombre: <span class="required">*</span></label><input type="text" name="nombre" value="" id="nombre" size="60">
			<label for="nombre">Tel&eacute;fono: </label><input type="text" name="telefono" value="" id="telefono" size="60">
			<label for="email">Email: <span class="required">*</span></label><input type="text" name="email" value="" id="email" size="60">
			<input type="hidden" name="chatrequest" value="1" id="chatrequest">
			<input type="hidden" name="site" value="<?php echo fRequest::get('sid'); ?>" id="site">
			<label for="nombre">Consulta:</label>
			<textarea name="consulta" rows="3" cols="45"></textarea>

			<p><input type="submit" value="Continue &rarr;"></p>
			</fieldset>
		</form>
	</div>
	<div id="dash_footer">
		Derechos Reservados &copy; Branding Machine 2012
	</div>
</div>