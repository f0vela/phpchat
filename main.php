<?php
include('init.php');
include('includes/conn.php');

if(!fSession::get('userid')){ header('location: index.php'); }
$userID = fSession::get('userid');
$res = $mysql_db->query("SELECT available_status FROM chat_admin WHERE userID = %s",$userID);
$status = 0;
foreach($res as $r)
{
	$status = $r['available_status'];
}
?>
<html>
<head>
	<title>CRM :: Chat Interactivo</title>
	<link rel="stylesheet" href="css/default.css" />
	<link rel="stylesheet" href="css/start/jquery-ui-1.8.5.custom.css" type="text/css" />
	<script src="js/jquery-1.4.2.min.js" charset="utf-8"></script>
	<script src="js/jquery.sound.js" charset="utf-8"></script>
	<script charset="utf-8">
		var scanid;
		var callLen = 27;
		function playCall()
		{
			$.fn.soundPlay({url: 'sounds/cellular.wav', playerId: 'embed_player', command: 'play'});			
		}
	
		function stopCall()
		{
			$.fn.soundPlay({playerId: 'embed_player', command: 'stop'});
		}
		
		function cnt(w){
			
			var y = w;
			var r = 0;
			a = y.replace(/\s/g,' ');
			a = a.split(' ');
			for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
			
			return r;
			
		}
		
		function scanCalls()
		{
			$('#enteringcalls').load(
			'chatprocess.php',
			{
				ax: 'scanCalls',
				uid: '<?php echo $userID; ?>'
			},
			function(data)
			{
				var callDataLen = cnt(data);
				if(callDataLen > callLen)
				{
					stopCall();
					playCall();
					callLen = callDataLen;
				}
			}
			);
			
		}

		function startScaning()
		{
			scanid = setInterval("scanCalls()", 3000);
			$('#enteringcalls').html('<h4>Cargando Llamadas.</h4><img src="images/ajax-loader.gif" width="32" height="32" alt="Ajax Loader">');
			//$('#offlinemessages').html('<h4>Cargando Mensajes.</h4><img src="images/ajax-loader.gif" width="32" height="32" alt="Ajax Loader">');
			return scanid;
		}

		function rechazarChat(sesid,deptid,userid,reqid)
		{
			$.ajax({
				url:'chatprocess.php',
				data: 'ax=rechazarchat&did='+deptid+'&uid='+userid+'&sesid='+sesid+'&reqid='+reqid
			});
		}

		function reatenderChat(sesid,deptid,userid,reqid)
		{
			window.open('chatwindow.php?op=true&sesid='+sesid+'&uid='+userid+'&did='+deptid+'&reqid='+reqid,'chatwindowop'+sesid,'width=460px, height=600px');
		}
		
		function atenderChat(sesid,deptid,userid,reqid)
		{
			$.ajax({
				url:'chatprocess.php',
				data: 'ax=atenderchat&did='+deptid+'&uid='+userid+'&sesid='+sesid+'&reqid='+reqid,
				success: function(data){
					if(data == 'atendiendo'){
						//window.location = window.location;
						window.open('chatwindow.php?op=true&sesid='+sesid+'&did='+deptid+'&uid='+userid+'&reqid='+reqid,'chatwindowop'+sesid,'width=460px, height=600px');
					}				
				}
			});
		}

		function onoffline(stat)
		{
			switch(stat)
			{
				//Pasa el estatus del operador de OFFLINE a ONLINE
				case 'on':
					$.ajax({
						url: "chatprocess.php",
						data: "ax="+stat+"&uid="+<?php echo fSession::get('userid'); ?>,
						success: function(){
									$('#online').addClass('statusOnline');
									$('#offline').removeClass('statusOffline');
									$('#offline').addClass('statusNone');
									startScaning();
						}
					});
				break;
				//Pasa el estatus del operador de ONLINE a OFFLINE
				case 'off':
					$.ajax({
						url: "chatprocess.php",
						data: "ax="+stat+"&uid="+<?php echo $userID; ?>,
						success: function(){
									$('#online').removeClass('statusOnline');
									$('#online').addClass('statusNone');
									$('#offline').addClass('statusOffline');
									clearInterval(scanid);
								}
					});
				break;
			}
		}
			
	</script>
	<?php
	if($status == 1)
	{
		?>
		<script charset="utf-8">
			$(function(){
				startScaning();
			});
		</script>
		<?php
	}
	?>
</head>
<body>
<?php if(fRequest::get('msg') != ''){ ?>
<script>

	function fademsg()
	{
		$('#msgbox').fadeOut('slow');
	}

	setTimeout('fademsg()',3000);
</script>
<div id="msgbox" class="msg_alert msg_float"><?php echo fRequest::get('msg'); ?></div>
<?php } ?>
	<div id="dash_container">
		<div id="dash_header">
			<div id="dash_logo">
					<img src="images/logo.gif" border="0" />
			</div>
			<div id="dash_user">
				<strong>Bienvenido(a) <a href="main.php?p=userprofile&uid=<?php echo fSession::get('userid'); ?>"><?php echo fSession::get('name');?></a></strong>&nbsp;&nbsp;&nbsp;&nbsp;(
				<a href="javascript: onoffline('on');" id="online" <?php if($status == 1){ echo 'class="statusOnline"'; }?>>En Linea</a> | 
				<a href="javascript: onoffline('off');" id="offline" <?php if($status == 0){ echo 'class="statusOffline"'; }?>>Fuera de Linea</a>
				)
			</div>
		</div>
		<?php include('menu.php'); ?>
		<div id="dash_content">
			<?php
				$pagina = fRequest::get('p');

				switch($pagina)
				{
					case 'historial':
						include('historial.php');
						break;
					case 'crm':
						include('crm.php');
						break;
					case 'config':
							$inc = 'config.php';
							
							if(fRequest::get('sp') == 'admusuarios') $inc = 'adminusuarios.php';							
							if(fRequest::get('sp') == 'usuariosedit') $inc = 'adminusuarios_edit.php';
							if(fRequest::get('sp') == 'usuariosadd') $inc = 'adminusuarios_create.php';
							
							if(fRequest::get('sp') == 'admsitios') $inc = 'adminsitios.php';
							if(fRequest::get('sp') == 'sitiosedit') $inc = 'adminsitios_edit.php';
							if(fRequest::get('sp') == 'sitiosadd') $inc = 'adminsitios_create.php';
							
							if(fRequest::get('sp') == 'admdeptos') $inc = 'admindeptos.php';
							if(fRequest::get('sp') == 'deptosedit') $inc = 'admindeptos_edit.php';
							if(fRequest::get('sp') == 'deptosadd') $inc = 'admindeptos_create.php';
							//if(fRequest::get('sp') == 'admpermisos') $inc = 'adminpermisos.php';

							
							include($inc);
						break;
					case 'userprofile':
						include('perfil.php');
						break;
					default:
						include('crm.php');
						break;
				}

			?>
		</div>
		<div class="clear">&nbsp;</div>
		<?php include('footer.php'); ?>
	</div>	
</body>
</html>