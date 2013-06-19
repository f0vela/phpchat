<?php
include('init.php');
include('includes/conn.php');
include('includes/opclass.php');
include('includes/chatclass.php');

$sessionID = fRequest::get('sesid');
$userID = fRequest::get('uid');
$deptID = fRequest::get('did');
$requestID = fRequest::get('reqid');

$op = new OpClass();
$chat = new ChatClass();

	$cSessionRequest = fRequest::get('chatrequest');
	$deptID = fRequest::get('did');
	$screen_name = fRequest::get('nombre');			
	$email = fRequest::get('email');
	$question = fRequest::get('consulta');
	$site = fRequest::get('site');
	$telefono = fRequest::get('telefono');
	$sessionID = $chat->getOpenSession($deptID,$screen_name,$email,$site);
	$nowDate = date('Y-m-d H:i:s');
	
	if($cSessionRequest == 1 && $sessionID == '')
	{
		$userID = $op->getFreeOperator($deptID);
		$sessionID = $chat->getNewSession($screen_name);
		$userIP = $_SERVER['REMOTE_ADDR'];
		$userBrowser = $_SERVER['HTTP_USER_AGENT'];
		$res = $mysql_db->query("INSERT INTO chatrequests (userID, deptID, aspID, from_screen_name, sessionID, status, tstatus, tflag, ip_address, browser_type, display_resolution,  email, question,telephone,clientactivity, operatoractivity) VALUES ($userID, $deptID, 1, '$screen_name',$sessionID, 0, 0, 0, '$userIP', '$userBrowser', '1024x768', '$email', '$question', '$telefono','$nowDate','$nowDate')");
		$requestID = $res->getAutoIncrementedValue();
		
	}else{
		$res = $mysql_db->query('SELECT * FROM chatrequests WHERE sessionID = %s',$sessionID);
		foreach($res as $r)
		{
			$userID = $r['userID'];
			$requestID = $r['requestID'];
		}
	}

?>
<html>
<head>
	<title>CRM :: Chat Interactivo</title>
	<link rel="stylesheet" href="css/default.css" type="text/css" />
	<script type="text/javascript" charset="utf-8" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/jquery.countdown.pack.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.countdown-es.js" charset="utf-8"></script>
	<script>
		$(function(){
			var fechainicio = new Date();
			$("#tiempoespera").countdown({
				since: fechainicio,
				format: 'HMS',
				layout: 'Espera: {hnn}:{mnn}:{snn}'
			});
		});
	</script>
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
			<h3>Bienvenido(a) por favor espere un momento.<br />Si un representante no responde en unos instantes, deje su mensaje.</h3>
			<img src="images/ajax-loader.gif" border="0" />
			<script type="text/javascript" charset="utf-8">
				var scanid = window.setInterval('checkRequest()',2000);
				var scanid2 = setTimeout('leaveMessage()',60000);
				var scanid3 = window.setInterval('changeOperator()',20000);
				
				function changeOperator(){
					$.ajax({
						url: 'chatprocess.php',
						data: 'ax=changeop&reqid=<?php echo $requestID; ?>&did=<?php echo $deptID; ?>&sesid=<?php echo $sessionID; ?>&rand='+(Math.floor(Math.random()*99999)),
						success: function(data){
							//alert(data);
							$("#userID").val(data);
						}
					});

				
				
				}
				
				function leaveMessage(){
					var userID = $('#userID').val();
					
					$.ajax({
						url: 'chatprocess.php',
						data: 'ax=cancelRequest&uid='+userID+'&did=<?php echo $deptID?>&sesid=<?php echo $sessionID; ?>&reqid=<?php echo $requestID; ?>&rand='+(Math.floor(Math.random()*99999)),
						success: function(){
							window.location = 'chatmessage.php?from=request&uid=<?php echo $userID; ?>&did=<?php echo $deptID; ?>&sid=<?php echo $site; ?>&sesid=<?php echo $sessionID; ?>&reqid=<?php echo $requestID; ?>&telefono=<?php echo $telefono; ?>&rand='+(Math.floor(Math.random()*99999));
						}						
					});
				}
				function checkRequest(){
					var userID = $('#userID').val();
					$.ajax({
						url: 'chatprocess.php',
						data: 'ax=checkrequest&reqid=<?php echo $requestID; ?>&sesid=<?php echo $sessionID; ?>&rand='+(Math.floor(Math.random()*99999)),
						success: function(data){
							//alert(data);
							if(data == 'tomada'){
								clearInterval(scanid);
								window.location = 'chatwindow.php?did=<?php echo $deptID; ?>&reqid=<?php echo $requestID; ?>&sesid=<?php echo $sessionID; ?>&uid='+userID+'&rand='+(Math.floor(Math.random()*99999));
							}
							if(data == 'cerrada'){
								//alert(data);
								window.location = 'chatmessage.php?from=request&uid='+userID+'&did=<?php echo $deptID; ?>&sid=<?php echo $site; ?>&sesid=<?php echo $sessionID; ?>&reqid=<?php echo $requestID; ?>&rand='+(Math.floor(Math.random()*99999));
							}
						}
					});
				}
			</script>
			<br /><br />
			<div id="tiempoespera">00:00:00</div>
			<input type="hidden" name="userID" id="userID" value="<?php echo $userID?>" />
		</div>
	</div>
	<div id="dash_footer">
		Derechos Reservados &copy; ICU Publicidad 2010
	</div>
</div>