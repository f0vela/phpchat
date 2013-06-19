<?php
header('Content-Type: text/html; charset=UTF-8');
include('init.php');
include('includes/conn.php');
include('includes/opclass.php');
include('includes/chatclass.php');

$op = new OpClass();
$chat = new ChatClass();
$ax = fRequest::get('ax');

if(isset($_REQUEST['uid']) && $_REQUEST['uid'] != ''){
	$userID = $_REQUEST['uid'];
	
	
	$od = $op->getMyData($userID);
	while($oda = mysql_fetch_assoc($od))
	{
		$opdata['name'] = $oda['name'];
		$opdata['email'] = $oda['email'];
	}
}

switch($ax)
{
	case 'calificar':
	/**
	* case: 'calificar'
	* 
	* Guarda la calificacion que le dio el usuario al operador por la sesión de apoyo
	*
	*/

		$sessionID = fRequest::get('sesid');
		$deptID = fRequest::get('did');
		$requestID = fRequest::get('reqid');
		$rate = fRequest::get('star1');
		$copia = fRequest::get('copia');
		$email = fRequest::get('email');
		$sID = $chat->getSiteId($deptID);
		$nowdate = date('Y-m-d H:i:s');
		
		try{
			$res = $mysql_db->execute("INSERT INTO chat_adminrate (userID,sessionID,rating,deptID,aspID,rated) VALUES ($userID,$sessionID,$rate,$deptID,1,'$nowdate')");
		}catch(fSQLException $e){
			
		}
		
		/* AQUI VA EL CODIGO PARA ENVIO DEL TRANSCRIPT */
		if(isset($copia)){
	
		$mysql_db->execute("UPDATE chatrequests SET senttranscript = 1 WHERE deptID = '$deptID' AND requestID = '$requestID' AND sessionID = '$sessionID'");
	
		$mail = new fEmail();
	
		$smtp = new fSMTP('mail.mazdaesdidea.com');
		$smtp->authenticate('crm@mazdaesdidea.com','wzcOE1bu?VcL');
	
		$mail->setFromEmail('crm@mazdaesdidea.com','Servicio al cliente');
		$mail->addRecipient($email);
		$mail->setSubject('Transcripcion de chat '.$chat->getSiteName($sID).' ');
	
		$trans = $chat->getChatTranscript($sessionID,$deptID,$userID,'');
		
		$mailBody = '';
		while($c = mysql_fetch_assoc($trans)){
			$mailBody .= $c['plain'];
		}

		if($sID == 1){
			$mailBody .= '<strong>DIDEA</strong><br><br>
		
		ACLARACI&Oacute;N: El contenido del presente correo electr&oacute;nico y/o documentos adjuntos NO podr&aacute; interpretarse  como una oferta de contrato, por lo que el mismo no vincula ni obliga en forma alguna a Distribuidora de Autom&oacute;viles S.A. Es entendido que Distribuidora de Autom&oacute;viles S.A. utiliza el env&iacute;o de correos electr&oacute;nicos &uacute;nicamente con fines de facilitar la comunicaci&oacute;n con el p&uacute;blico, sin que dichas transmisiones electr&oacute;nicas puedan interpretarse con fines contractuales, lo cual es aceptado y entendido por todo los destinatarios que reciban un correo electr&oacute;nico que termine con el dominio @didea.com.gt, en virtud Distribuidora de Autom&oacute;viles S.A. Formaliza sus relaciones contractuales exclusivamente mediante contratos firmados f&iacute;sicamente por el Representante Legal de la Compa&ntilde;&iacute;a, contenidos en documentos privados con firmas legalizadas y/o en escrituras p&uacute;blicas.<br><br>';
		}
		if($sID == 2){
			$mailBody .= '<strong>HYUNDAI</strong><br><br>
		
		ACLARACION: El contenido del presente correo electrónico y/o documentos adjuntos NO podr&aacute; interpretarse  como una oferta de contrato, por lo que el mismo no vincula ni obliga en forma alguna a Universal de Autos S.A. Es entendido que Universal de Autos S.A. utiliza el env&iacute;o de correos electr&oacute;nicos &uacute;nicamente con fines de facilitar la comunicaci&oacute;n con el publico, sin que dichas transmisiones electr&oacute;nicas puedan interpretarse con fines contractuales, lo cual es aceptado y entendido por todo los destinatarios que reciban un correo electr&oacute;nico que termine con el dominio @uniauto.com.gt, en virtud que Universal de Autos S.A. Formaliza sus relaciones contractuales exclusivamente mediante contratos firmados f&iacute;sicamente por el Representante Legal de la Compa&ntilde;&iacute;a, contenidos en documentos privados con firmas legalizadas y/o en escrituras publicas.<br><br>';
		}
		if($sID == 3){
			$mailBody .= '<strong>PEUGEOT</strong><br><br>
		
		ACLARACION: El contenido del presente correo electr&oacute;nico y/o documentos adjuntos NO podr&aacute; interpretarse  como una oferta de contrato, por lo que el mismo no vincula ni obliga en forma alguna a Autos Europa S.A. Es entendido que Autos Europa S.A. utiliza el env&iacute;o de correos electr&oacute;nicos &uacute;nicamente con fines de facilitar la comunicaci&oacute;n con el publico, sin que dichas transmisiones electr&oacute;nicas puedan interpretarse con fines contractuales, lo cual es aceptado y entendido por todo los destinatarios que reciban un correo electr&oacute;nico que termine con el dominio @peugeot.com.gt, en virtud que  Autos Europa S.A. Formaliza sus relaciones contractuales exclusivamente mediante contratos firmados f&iacute;sicamente por el Representante Legal de la Compa&ntilde;&iacute;a, contenidos en documentos privados con firmas legalizadas y/o en escrituras publicas.<br><br>';
		}
		
			
		$mail->setBody('Ver en HTML');
		
		$mail->setHTMLBody($mailBody);
		
		$message_id = $mail->send($smtp);
		$smtp->close();
		}

		echo '<script>self.close();</script>';
		echo '<script>parent.Shadowbox.close();</script>';
		
	break;
	case 'changeop':
		
		$sessionID = fRequest::get('sesid');
		$requestID = fRequest::get('reqid');
		$deptID = fRequest::get('did');
		
		$auid = $mysql_db->query("SELECT userID FROM chatrequests WHERE requestID = $requestID and sessionID = $sessionID");
		foreach($auid as $au)
		{
			$auID = $au['userID'];
		}
		

		$cuserID = $op->getFreeOperator($deptID,$auID);
		$cores = $mysql_db->execute("UPDATE chatrequests SET userID = $cuserID WHERE requestID = $requestID and sessionID = $sessionID");
		
		echo $cuserID;
		
	break;
	case 'isAlive':
	
		$sessionID = fRequest::get('sesid');
		$requestID = fRequest::get('reqid');
		$deptID = fRequest::get('did');
		$operador = fRequest::get('op');
		$nowDate = date('Y-m-d H:i:s');
		$nd_1 = explode(' ',$nowDate);
		$nd_2 = explode(':',$nd_1['1']);
		
		if($operador)
		{
			$iae = $mysql_db->execute("UPDATE chatrequests SET operatoractivity = '$nowDate' WHERE requestID = $requestID");
			$ia = $mysql_db->query("SELECT clientactivity FROM chatrequests WHERE requestID = $requestID");
			foreach($ia as $au)
			{
				$and 	= $au['clientactivity'];
			}
			$and_1 	= explode(' ',$and);
			$and_2 	= explode(':',$and_1['1']);
			
			if(($nd_2[1] - $and_2[1]) >= 2)
			{
				header('Location: chatprocess.php?ax=closechat&sesid='.$sessionID.'&did='.$deptID.'&uid='.$userID.'&reqid='.$requestID);
				//header('Location: chatprocess.php?ax=closechat&sesid='.$sessionID.'&did='.$deptID.'&reqid='.$requestID.'&uid='.$userID.'&op=1');
			}			
			
			
		}else{
			$iae = $mysql_db->execute("UPDATE chatrequests SET clientactivity = '$nowDate' WHERE requestID = $requestID");
			$ia = $mysql_db->query("SELECT operatoractivity FROM chatrequests WHERE requestID = $requestID");
			foreach($ia as $au)
			{
				$and 	= $au['operatoractivity'];
			}
			$and_1 	= explode(' ',$and);
			$and_2 	= explode(':',$and_1['1']);
			
			if(($nd_2[1] - $and_2[1]) >= 2)
			{
				//header('Location: chatprocess.php?ax=closechat&sesid='.$sessionID.'&did='.$deptID.'&uid='.$userID.'&reqid='.$requestID);
				header('Location: chatprocess.php?ax=closechat&sesid='.$sessionID.'&did='.$deptID.'&reqid='.$requestID.'&uid='.$userID.'&op=true');
			}	
		}
	
	break;
	case 'closechat':
	/**
	* case: 'closechat'
	* 
	* Proceso para el cierre de la sesión del chat
	*
	*/
		$sessionID = fRequest::get('sesid');
		$deptID = fRequest::get('did');
		$requestID = fRequest::get('reqid');
		$operador = fRequest::get('op');
		$screen_name = 'system';
		$email = fSession::get('email');
		$sID = $chat->getSiteId($deptID);
		
		if($email == ''){ $email = fSession::get('user'); }
		$plain = utf8_encode("La otra persona ha abandonado la sesi&oacute;n");
		$normal = utf8_encode($plain);
		
		$ch = $mysql_db->execute("INSERT INTO chattalklog (chat_session,userID,from_screen_name,email,deptID,plain,formatted,aspID) VALUES ($sessionID,$userID,'$screen_name','$email',$deptID,'$plain','$normal',1)");
		
		$chat->generateTranscript($sessionID,$deptID,$userID,$requestID);
		 
		$mysql_db->execute("UPDATE chatrequests SET status = 2 where sessionID = %s AND userID = %s AND deptID = %s AND requestID = %s",$sessionID,$userID,$deptID,$requestID);		
		
		if($operador)
		{
			header("location: chatend.php?sid=$sID&sesid=$sessionID&reqid=$requestID&did=$deptID&uid=$userID&op=true");	
		}else{
			header("location: chatend.php?sid=$sID&sesid=$sessionID&reqid=$requestID&did=$deptID&uid=$userID");
		}
		
	break;
	case 'on':
	/**
	* case: 'on'
	* 
	* Cambia el estatus del operador de fuera de linea a "EN LINEA"
	*
	*/
		$mysql_db->execute('UPDATE chat_admin SET available_status = 1 where userID = %s',$userID);
	break;
	case 'off':
	/**
	* case: 'off'
	* 
	* Cambia el estatus del operador de en linea a "FUERA DE LINEA"
	*
	*/
		$mysql_db->execute('UPDATE chat_admin SET available_status = 0 where userID = %s',$userID);
	break;
	case 'checkrequest':
	/**
	* case: 'checkrequest'
	* 
	* Permite, a la ventana de petición de soporte, saber si la peticion de soporte fue tomada por el operador al que le fue asignada
	*
	*/
		$sessionID = fRequest::get('sesid');
		$requestID = fRequest::get('reqid');
		
		$rea = $mysql_db->query("SELECT status FROM chatrequests WHERE requestID = %s",$requestID);
		$status = 0;
		$req = 'notomada';

		foreach($rea as $ra)
		{
			$status = $ra['status'];
		}

		if($status == 1){ $req = 'tomada';}
		if($status == 2){ $req = 'cerrada';}
		
		echo $req;
	break;
	case 'atenderchat':
	/**
	* case: 'atenderchat'
	* 
	* Proceso para atender inicialmente el chat por el operador, cambia el estaus de la solicitud a 1 ('Atendida') y crea el primer insert
	* en la tabla de chattalklog dandole la bienvenida al usuario
	*
	*/
		$sessionID = fRequest::get('sesid');
		$deptID = fRequest::get('did');
		$requestID = fRequest::get('reqid');
		
		$plain = 'Bienvenido(a) le atiende '.$opdata['name'].' de Atenci&oacute;n al Cliente.';
		$normal = $plain;
		$screen_name = 'system';
		$email = 'system@chatgrupotecun.com';
		
		$res = $mysql_db->execute('UPDATE chatrequests SET status = 1, attended = CURRENT_TIMESTAMP WHERE requestID = %s AND sessionID = %s',$requestID,$sessionID);
		$mysql_db->execute("INSERT INTO chattalklog (chat_session,userID,from_screen_name,email,deptID,plain,formatted,aspID) VALUES ($sessionID,$userID,'$screen_name','$email',$deptID,'$plain','$normal',1)");
		
		echo 'atendiendo';
		
		
	break;
	case 'getchat':
	/**
	* case: 'getchat'
	* 
	* Trae todo el chat que se está llevando a cabo por el operador y el usuario en linea.
	* aqui se deberá poner el código para el estatus del chat.
	*
	*/
		$sessionID = fRequest::get('sesid');
		$deptID = fRequest::get('did');
		$clientname = fRequest::get('cname');
		$requestID = fRequest::get('reqid');
		
		$chat = $mysql_db->query('SELECT * FROM chattalklog WHERE chat_session = %s AND userID = %s AND deptID = %s ORDER BY created ASC',$sessionID,$userID,$deptID);
		$ch = $mysql_db->query('SELECT * FROM chatrequests WHERE sessionID = %s AND userID = %s AND deptID = %s AND requestID = %s',$sessionID,$userID,$deptID,$requestID);

		foreach($ch as $c)
		{
			$stat = $c['status'];
		}
		//if($stat == 1){
			$co = 0;
			foreach($chat as $c)
			{
			$co++;
			
				if($c['from_screen_name'] == $opdata['name']) 
				{
					echo '
					<div class="optalk ui-widget ui-widget-header ui-corner-all">
						<div class="optalkid">'.$c['from_screen_name'].' dice: </div>
						'.$c['formatted'].'
					</div><br/>';
				
				}elseif($c['from_screen_name'] == 'system'){
					echo '<div class="chatwelcome">'.$c['formatted'].'</div><div class="clear"></div><br/>';
				}elseif($c['from_screen_name'] == $clientname){
					echo '<div class="usertalk ui-widget ui-state-highlight ui-corner-all">
							<div class="usertalkid">'.$clientname.' dice: </div>
							'.$c['formatted'].'
						  </div>
						<br/>';
				}
			}
			
			if($stat == 2){
				$chattrans = $mysql_db->query('SELECT * FROM chattranscripts WHERE chat_session = %s AND userID = %s AND deptID = %s ORDER BY created ASC',$sessionID,$userID,$deptID);
				foreach($chattrans as $text)
				{
					echo $text['formatted'];
				}
			}
		//}else{
			//echo "<script> window.location = 'chatend.php?sesid=$sessionID&did=$deptID&uid=$userID';</script>";
			//echo '<div class="chatwelcome">La otra persona ha abandonado la sesi&oacute;n</div><div class="clear"></div><br/>';
		//}
	break;
	case 'savechat':
	/**
	* case: 'savechat'
	* 
	* Permite guardar lo que el cliente o el operador estan escribiendo luego del enter
	*
	*/
			$sessionID = fRequest::get('sesid');
			$deptID = fRequest::get('did');
			$requestID = fRequest::get('reqid');
			$operator = fRequest::get('op');
			$screen_name = $opdata['name'];
			$email = $opdata['email'];
			
			if(!$operator){
				$cliente = $chat->getClientData($sessionID,$deptID,$userID,$requestID);
				$screen_name = $cliente['name'];
				$email = $cliente['email'];
			}
			
			/* Limpiar Texto de malas palabras */
			$censuradas = array('putas','puta','mierda','mierdas','mierd@','mierd@s','m|erd@','mi3rd@','mierdo','caca','cerot3','cerot@','cer0t3','cer0t@','c3r0t3','c3r0t@','ceroton','cerotona','comehuevo','come huevos','lameculos','lameculo','lamehuevos','lamehuevo','lame huevos','lame huevo','lame culos','lame culo','come huevo','come huevos','cerot','cerote','cerota','puto','put@','maldito','maldita','malditos','malditas','maldit@s','maldit@','m@ldit@','coma huevo','com@ huevo','coma huev0','com@ huev0','coma mas huevo','coma + huevo');
			$texto = $chat->noBadWords($_GET['consulta'],$censuradas);
			
			$normal = $texto;
			$plain = strip_tags($normal);
			
			$chat = $mysql_db->execute("INSERT INTO chattalklog (chat_session,userID,from_screen_name,email,deptID,plain,formatted,aspID) VALUES ($sessionID,$userID,'$screen_name','$email',$deptID,'$plain','$normal',1)");
			
	break;
	case 'setwritingstatus':
	/**
	* case: 'setwritingstatus'
	* 
	* Guarda el estatus del cliente o el operador cuando està escribiendo
	*
	*/
		$sessionID = fRequest::get('sesid');
		$deptID = fRequest::get('did');
		$field = fRequest::get('fi');
		$status = fRequest::get('status');
		
		$mysql_db->execute("UPDATE chatrequests SET $field = $status WHERE sessionID = $sessionID AND userID = $userID AND deptID = $deptID");
		
	break;
	case 'getwritingstatus':
	/**
	* case: 'getwritingstatus'
	* 
	* Trae el estatus del cliente o el operador si está o no escribiendo
	*
	*/
		$sessionID = fRequest::get('sesid');
		$deptID = fRequest::get('did');
		$field = fRequest::get('fi');
		
		$crs = $mysql_db->query("SELECT $field FROM chatrequests WHERE sessionID = $sessionID AND userID = $userID AND deptID = $deptID");
		foreach($crs as $s)
		{
			$st = $s[$field];
		}
		
		if($st == 1) $st = 'Escribiendo mensaje...';
		if($st == 0) $st = '';
		echo $st;
		
	break;
	case 'atendermensaje':
	/**
	* case: 'atendermensaje'
	* 
	* Cambia el estado del mensaje fuera de linea a 1 ('Atendido')
	*
	*/
		$deptID = fRequest::get('did');
		$userID = fRequest::get('uid');
		$created = fRequest::get('created');
		$now = date('Y-m-d H:i:s');
		
		$mysql_db->execute("UPDATE chatmessages SET status = 1, taked_by = $userID, taked_date = '$now' WHERE created = '$created' AND deptID = $deptID");
		
	break;
	case 'rechazarchat':
	/**
	* case: 'rechazarchat'
	* 
	* Rechaza el chat por medio del operador, busca un operador nuevo y si no hay un operador disponible
	* lo pone en estatus 2 ('Cerrado')
	*
	*/
		$deptID = fRequest::get('did');
		$sessionID = fRequest::get('sesid');
		$requestID = fRequest::get('reqid');
		$olduserID = $userID;
		$userID = $op->getFreeOperator($deptID,$olduserID);
		
		if($userID != 0)
		{
			$mysql_db->execute("UPDATE chatrequests SET userID = $userID WHERE sessionID = $sessionID AND userID = $olduserID AND deptID = $deptID AND requestID = $requestID");
		}else{
			$mysql_db->execute("UPDATE chatrequests SET status = 2 WHERE sessionID = $sessionID AND userID = $olduserID AND deptID = $deptID AND requestID = $requestID");
		}
		
	break;
	case 'cancelRequest':
		$deptID = fRequest::get('did');
		$sessionID = fRequest::get('sesid');
		$requestID = fRequest::get('reqid');
		
		$mysql_db->execute("UPDATE chatrequests SET status = 2, attended = '1900-01-01 11:59:59' WHERE sessionID = $sessionID AND userID = $userID AND deptID = $deptID AND requestID = $requestID");
		
	break;
	case 'cerrarmensaje':
		$deptID = fRequest::get('did');
		$userID = fRequest::get('uid');
		$created = fRequest::get('created');
		$now = date('Y-m-d H:i:s');
		
		$mysql_db->execute("UPDATE chatmessages SET status = 2, closed_by = $userID, closed_date = '$now' WHERE created = '$created' AND deptID = $deptID");
		
	break;
	case 'scanMessages':
	/**
	* case: 'scanMessages'
	* 
	* Escanea por mensajes fuera de linea nuevos
	*
	*/	
		echo '<table border="0" cellspacing="0" cellpadding="3" width="100%">
			<tr class="tableheader">
				<td>Cuenta</td>
				<td>Nombre</td>
				<td>Fecha y Hora</td>
				<td>Estado</td>
				<td>Acci&oacute;n</td>
			</tr>';
		$msg = $op->getMessages();
		while($m = mysql_fetch_assoc($msg))
		{
		echo '<tr>';
		echo '<td>'.$m['deptName'].'</td>';
		echo '<td>'.$m['from_screen_name'].'</td>';
		echo '<td>'.$m['created'].'</td>';
		echo '<td>';
		switch($m['status']){
					case 0:
						echo 'Pendiente';
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
		if($m['status'] == 0){
			echo '<a href="javascript: void(0);" onclick="atenderMensaje(\''.$m['deptID'].'\',\''.$m['deptName'].'\',\''.$m['created'].'\',\''.$m['email'].'\');">Atender</a>';
			echo ' | ';
		}
		if($m['status'] <= 1){
			echo '<a href="javascript: void(0);" onclick="cerrarMensaje(\''.$m['deptID'].'\',\''.$m['deptName'].'\',\''.$m['created'].'\',\''.$m['email'].'\');">Cerrar</a>';
		}
		echo '</td>';
		echo '</tr>';
		}
		echo '</table>';
		
	break;
	case 'scanCalls':
	/**
	* case: 'scanCalls'
	* 
	* Escanea por nuevas llamadas entrantes para el usuario
	*
	*/
		/* SI HAY NUEVOS MENSAJES CORRE EL SONIDO DE LLAMADA */
		$lan = fSession::get('lan');
		$lex = fSession::get('lex');
		$exi = $mysql_db->query("SELECT COUNT(*) as total FROM chatrequests WHERE status = 0 AND userID = $userID");

		foreach($exi as $e)
		{
			$lex = $e['total'];
		}
		if($lex > $lan)
		{
			fSession::set('lan',$lex);
			fSession::set('lex',$lex);
			echo "<script>stopCall();</script>";
			echo "<script>playCall();</script>";
		}
		
		if($lex > 0)
		{
			echo '<div class="callCounter">'.$lex.'</div>';
			echo "<script>document.title = 'CRM :: Chat Interactivo (".$lex.")'; </script>";
		}
		if($lex == 0)
		{
			echo '<div class="callCounter" style="display:none">'.$lex.'</div>';
			echo "<script>document.title = 'CRM :: Chat Interactivo'; </script>";
		}
		
		echo '<table border="0" cellspacing="0" cellpadding="3" width="100%">
			<tr class="tableheader">
				<td>Cuenta</td>
				<td>Nombre</td>
				<td>Fecha y Hora</td>
				<td>Estado</td>
				<td>Acci&oacute;n</td>
			</tr>';
			$llamadas = $op->getMyCalls($userID); 
			$scc = 0;
			while($l = mysql_fetch_assoc($llamadas))
			{
				$scc++;
				if($scc == 1) {
					echo '<tr class="firstinline">';
				}else{
					echo '<tr class="'.$chat->RowColor($cc,'normal','even').'">';
				}
				echo '<td>'.$l['deptName'].'</td>';
				echo '<td>'.$l['from_screen_name'].'</td>';
				echo '<td>'.($l['created']).'</td>';
				echo '<td>';
				switch($l['status']){
					case 0:
						echo 'En Espera';
						break;
					case 1:
						echo 'Atendida';
						break;
				}
				echo '</td>';
			
				switch($l['status'])
				{
					case 0:
						echo '<td><a href="javascript: void(0);" onclick="atenderChat(\''.$l['sessionID'].'\',\''.$l['deptID'].'\',\''.$l['userID'].'\',\''.$l['requestID'].'\');"><img src="images/Discussion.png" border="0" alt="Atender" /></a> <a href="javascript: void(0);" onclick="rechazarChat(\''.$l['sessionID'].'\',\''.$l['deptID'].'\',\''.$l['userID'].'\',\''.$l['requestID'].'\');"><img src="images/Red Ball.png" border="0" alt="Rechazar" /></a></td>';
						break;
					case 1:
						echo '<td><a href="javascript: void(0);" onclick="reatenderChat(\''.$l['sessionID'].'\',\''.$l['deptID'].'\',\''.$l['userID'].'\',\''.$l['requestID'].'\');"><img src="images/Discussion.png" border="0" /></a></td>';
						break;
				}
				echo '</tr>';
			}
			if($scc == 0){
				echo '<tr><td colspan="5"><strong>Sin llamadas en espera.</strong></td></tr>';
			}
		
	break;
}

?>