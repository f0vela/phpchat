<?php
header('Content-Type: text/html; charset=UTF-8');
include('init.php');
include('includes/conn.php');
include('includes/chatclass.php');

$sessionID = fRequest::get('sesid');
$userID = fRequest::get('uid');
$deptID = fRequest::get('did');
$requestID = fRequest::get('reqid');
$chat = new ChatClass();
$fi = 'tstatus';
$fi2 = 'tstatus2';
$operador = false;

$cliente = $chat->getClientData($sessionID,$deptID,$userID,$requestID);
$request = $chat->getRequestData($sessionID,$requestID);

while($r = mysql_fetch_assoc($request))
{
	$fecha_atendida = $r['attended'];
	$fe = new DateTime($fecha_atendida);

	$anio = $fe->format('Y');
	$mes = number_format($fe->format('m'));
	$dia = number_format($fe->format('d'));
	$hora = number_format($fe->format('H'));
	$min = number_format($fe->format('i'));

}

if(isset($_GET['op']) && $_GET['op'] == TRUE)
{
	$fi = 'tstatus2';
	$fi2 = 'tstatus';
	$operador = true;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>CRM :: Chat Interactivo</title>
	<meta name="content-type" content="text/html;" http-equiv="content-type" charset="utf-8">
	<link rel="stylesheet" href="css/default.css" type="text/css" />
	<link rel="stylesheet" href="css/start/jquery-ui-1.8.5.custom.css" type="text/css" />
	<link rel="stylesheet" href="css/start/jquery.countdown.css" type="text/css" />
	<link rel="stylesheet" href="js/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
	<script type="text/javascript" charset="utf-8" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" charset="utf-8" src="js/jquery-ui.1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.countdown.pack.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.countdown-es.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jwysiwyg/jquery.wysiwyg.js" charset="utf-8"></script>
	<script src="js/jquery.scrollTo-1.4.2-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.localscroll-1.2.7-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.sound.js" charset="utf-8"></script>

	<script type="text/javascript" charset="utf-8">
		var scanid = setInterval('getChat()',2000);
		var isAlive = setInterval('chatAlive()',15000);
		//var fechainicio = new Date(<?php echo $anio; ?>,<?php echo $mes-1; ?>,<?php echo $dia; ?>,<?php echo $hora-1; ?>,<?php echo $min-2; ?>,30,0);
		var fechainicio = new Date();
		var chatlen = 0;
		function playCall()
		{
			$.fn.soundPlay({url: 'sounds/receive.wav', playerId: 'embed_player', command: 'play'});			
		}
	
		function stopCall()
		{
			$.fn.soundPlay({playerId: 'embed_player', command: 'stop'});
		}
		
		function chatAlive(){
		
			$.ajax({
				url: 'chatprocess.php',
				data: 'ax=isAlive&did=<?php echo $deptID; ?>&uid=<?php echo $userID; ?>&sid=<?php echo $siteID; ?>&sesid=<?php echo $sessionID; ?>&reqid=<?php echo $requestID; ?>&op=<?php echo $operador; ?>'
			});
		}
		
		function cnt(w){
			
			var y = w;
			var r = 0;
			a = y.replace(/\s/g,' ');
			a = a.split(' ');
			for (z=0; z<a.length; z++) {if (a[z].length > 0) r++;}
			
			return r;
			
		}

		function getChat()
		{
			$('#chattext').load('chatprocess.php',{
				'ax':'getchat',
				'sesid':'<?php echo $sessionID; ?>',
				'uid':'<?php echo $userID; ?>',
				'did':'<?php echo $deptID; ?>',
				'reqid':'<?php echo $requestID; ?>',
				'cname':'<?php echo $cliente['name']; ?>'},
				function(data){
					var datalen = cnt(data);
					if(datalen > chatlen){
						$('#chattext').scrollTop(10000);
						stopCall();
						playCall();
						chatlen = datalen;
					}
				});

			/*
			$('#chatstatus').load('chatprocess.php',{
				'ax':'getwritingstatus',
				'sesid':'<?php echo $sessionID; ?>',
				'uid':'<?php echo $userID; ?>',
				'did':'<?php echo $deptID; ?>',
				'fi':'<?php echo $fi2; ?>'
				}
				);
			*/
		}

		function updateWritingStatus(status)
		{
			$.ajax({
				url: 'chatprocess.php',
				data: 'ax=setwritingstatus&sesid=<?php echo $sessionID; ?>&uid=<?php echo $userID; ?>&did=<?php echo $deptID; ?>&reqid=<?php echo $requestID; ?>&fi=<?php echo $fi; ?>&status='+status
				});
		}

		function savechat(consulta)
		{
			$.ajax({
				url: 'chatprocess.php',
				data: 'ax=savechat&sesid=<?php echo $sessionID;?>&did=<?php echo $deptID; ?>&uid=<?php echo $userID; ?>&reqid=<?php echo $requestID; ?>&op=<?php echo $operador; ?>&consulta='+encodeURI(consulta),
				success:function(data){
					getChat();
					$('#consulta').val('');
				}
			});
		}

		function checkForEnter(event) {
		  if (event.keyCode == 13) {
			var consulta = document.getElementById('consulta').value;
			if(consulta != ''){
				savechat(consulta);
				//updateWritingStatus(0)
			}
		  }
		}

		function endSession()
		{
			<?php
			$url = 'chatprocess.php?ax=closechat&sesid='.$sessionID.'&did='.$deptID.'&uid='.$userID.'&reqid='.$requestID;
			if($operador) $url = 'chatprocess.php?ax=closechat&sesid='.$sessionID.'&did='.$deptID.'&reqid='.$requestID.'&uid='.$userID.'&op=true';
			?>
			window.location = "<?php echo $url; ?>";
		}

  		var needToConfirm = true;

  		window.onbeforeunload = confirmExit;
  		function confirmExit()
  		{
    		if (needToConfirm)
      		return "Esto cerrara la sesion de chat.";
  		}

		$(function(){
			$('#consulta').keydown(checkForEnter);
			//$('#consulta').keypress(function(){
				//updateWritingStatus(1);
			//});
			getChat();
			//$('#consulta').wysiwyg();
		});
	</script>
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
				Cargando Chat...
			</div>
		</div>
		<div id="chatmiddle">
			<div id="chatstatus">
			<div id="chattimer">Tiempo: 00:00:00</div>
			</div>
			<div id="chatmenu">
				<button id="terminarchat" onclick="endSession();" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover ui-state-active" role="button" aria-disabled="false"><span class="ui-button-text">Terminar Chat</span></button>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div id="chatposting">
			<form action="" method="post" accept-charset="utf-8">
				<textarea name="consulta" id="consulta" style="width:450px; height:50px"></textarea>
			</form>
		</div>
		<?php if($operador) { ?>
		<div id="chatuserinfo">
			<table border="0" cellspacing="0" cellpadding="3">
				<tr class="tableheader"><td colspan="2"><strong>Datos del Cliente:</strong></td></tr>
				<tr><td valign="top"><strong>Nombre:</strong></td><td><?php echo $cliente['name']; ?></td></tr>
				<tr><td valign="top"><strong>Correo:</strong></td><td><?php echo $cliente['email']; ?></td></tr>
				<?php if ($cliente['telephone'] != ''){ ?>
				<tr><td valign="top"><strong>Tel&eacute;fono:</strong></td><td><?php echo $cliente['telephone']; ?></td></tr>
				<?php } ?>
				<tr><td valign="top"><strong>Navegador:</strong></td><td><?php echo $cliente['browser_type']; ?></td></tr>
			</table>
		</div>
		<?php } ?>
	</div>
	<div id="dash_footer">
		Derechos Reservados &copy; ICU Publicidad 2010
	</div>
</div>
<script>
		$(function(){
			$("#chattimer").countdown({
				since: fechainicio,
				format: 'HMS',
				layout: 'Tiempo: {hnn}:{mnn}:{snn}'
			});
		});
</script>