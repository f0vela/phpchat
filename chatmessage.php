<?php
include('init.php');
include('includes/conn.php');
include('includes/opclass.php');
include('includes/chatclass.php');

$sID = fRequest::get('sid');
$op = new OpClass();
$chat = new ChatClass();
$from = fRequest::get('from');

if($from == 'request')
{
	
	$sesID = fRequest::get('sesid');
	$reqID = fRequest::get('reqid');
	$telefono = fRequest::get('telefono');
	$rdata = $chat->getRequestData($sesID,$reqID);
	
	while($r = mysql_fetch_assoc($rdata))
	{
		$nombre = $r['from_screen_name'];
		$email = $r['email'];
		$consulta = $r['question'];
	}

}
$isopavail = $op->isOperatorAvailable($sID);
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
			var telefono = $('#telefono').val();
			var direccion = $('#direccion').val();
			var email = $('#email').val();
			var modelo = $('#modelo').val();
			var motivo = $('#motivo').val();
			var consulta = $('#consulta').val();
			
			var ret = false;
			var texto = 'Es necesario que:  \n';
			var txt = ''; 
			
			if(nombre == "") 	{ txt = txt+' - Nombre \n'; }
			if(telefono == "") 	{ txt = txt+' - Telefono \n'; }
			//if(direccion == "") { txt = txt+' - Direccion \n'; }
			if(email == "") 	{ txt = txt+' - Email \n'; }
			if(modelo == "")	{ txt = txt+' - Modelo \n'; }
			if(motivo == "") 	{ txt = txt+' - Motivo \n'; }
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
		<form action="chatmessagesave.php" method="post" accept-charset="utf-8" onsubmit="return checkform();">
			<fieldset>
			<h3>Deje un mensaje para nuestros operadores:</h3>
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
			<label for="nombre">Nombre: <span class="required">*</span></label><input type="text" name="nombre" value="<?php echo $nombre; ?>" id="nombre" size="60">
			<label for="nombre">Tel&eacute;fono: <span class="required">*</span></label><input type="text" name="telefono" value="<?php echo $telefono; ?>" id="telefono" size="60">
			<label for="nombre">Direcci&oacute;n: </label><input type="text" name="direccion" value="" id="direccion" size="60">
			<label for="email">Email: <span class="required">*</span></label><input type="text" name="email" value="<?php echo $email; ?>" id="email" size="60">
			
			<label for="modelo">Modelo: <span class="required">*</span></label>
				<select name="modelo" id="modelo">
					<option value="">Seleccione...</option>
					<?php if($sID == 1){ ?>
					<option value="Mazda 2">Mazda 2</option>
					<option value="Mazda 3">Mazda 3</option>
					<option value="Mazda 5">Mazda 5</option>
					<option value="Mazda 6">Mazda 6</option>
					<option value="CX7">CX7</option>
					<option value="CX9">CX9</option>
					<option value="BT-50">BT-50</option>
					<option value="MX5">MX5</option>
					<option value="RX8">RX8</option>
					<?php }
					if ($sId == 2){
					?>
					<option value="Genesis Coupe">Genesis Coupe</option>
					<option value="Genesis Sedan">Genesis Sendan</option>
					<option value="Sonata">Sonata</option>
					<option value="i30">i30</option>
					<option value="Elantra">Elantra</option>
					<option value="Accent">Accent</option>
					<option value="i10">i10</option>
					<option value="Veracruz">Veracruz</option>
					<option value="Santa Fe">Santa Fe</option>
					<option value="Tucson iX">Tucson iX</option>
					<option value="Wagon H1">Wagon H1</option>
					<option value="Atos">Atos</option>
					<option value="Getz">Getz</option>
					<option value="H1 Panel">H1 Panel</option>
					<option value="H100">H100</option>
					<option value="County">County</option>
					<option value="HD-45">HD-45</option>
					<option value="HD-65">HD-65</option>
					<option value="HD-78">HD-78</option>
					<option value="HD-120">HD-120</option>
					<option value="HD-160">HD-160</option>
					<?php } 
					if ($sId == 3){
					?>
					<option value="207 Compacto">207 Compacto</option>
					<option value="207 Sedan">207 Sedan</option>
					<option value="207">207</option>
					<option value="207 cc">207 cc</option>
					<option value="308">308</option>
					<option value="308 cc">308 cc</option>
					<option value="407">407</option>
					<option value="3008">3008</option>
					<option value="Partner">Partner</option>
					<option value="RCZ">RCZ</option>
					<?php } ?>
					<option value="Otros">Otros</option>
				</select>			
			
			<label for="motivo">Motivo de tu mensaje: <span class="required">*</span></label>
				<select name="motivo" id="motivo">
					<option value="">Seleccione...</option>
					<option value="Informacion de Vehiculos">Informaci&oacute;n de Veh&iacute;culos</option>
					<option value="Cotizaciones">Cotizaciones</option>
					<option value="Repuestos">Repuestos</option>
					<option value="Garantias">Garant&iacute;as</option>
					<option value="Seguros">Seguros</option>
					<option value="Informacion General">Informaci&oacute;n General</option>
				</select>
			
			<input type="hidden" name="sid" value="<?php echo fRequest::get('sid'); ?>" id="sid">
			<label for="consulta">Consulta: <span class="required">*</span></label>
			<textarea name="consulta" id="consulta" rows="3" cols="60"><?php echo $consulta?></textarea>

			<p><input type="submit" value="Enviar Mensaje"></p>
			</fieldset>
		</form>
	</div>
	<div id="dash_footer">
		Derechos Reservados &copy; ICU Publicidad 2010
	</div>
</div>