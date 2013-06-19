<?php

class ChatClass
{
	function noBadWords($str, $censored, $replacement = '')
	{
		/**
		 * noBadWords
		 * Limpia el chat de las malas palabras
		*/
		if (!is_array($censored))
		{
			return $str;
		}

		$str = ' '.$str.' ';
			
		foreach ($censored as $badword)
		{
			if ($replacement != '')
			{
				$str = preg_replace("/\b(".str_replace('\*', '\w*?', preg_quote($badword)).")\b/i", $replacement, $str);
			}
			else
			{
				$str = preg_replace("/\b(".str_replace('\*', '\w*?', preg_quote($badword)).")\b/ie", "str_repeat('#', strlen('\\1'))", $str);
			}
		}

		return trim($str);
	}
	
	function RowColor($n = 0,$c1 = '#ffffff',$c2 = '#f0f0f0')
	{
		$mo = ($n%2);
		
		if($mo == 0)
		{
			$ret = $c1; 
		}
		else
		{
			$ret = $c2;
		}
		
		return $ret;	
	
	}
	
	function getNewSession($screen_name)
	{
		/**
		 * getNewSession
		 * Crea una nueva session de chat y regresa el ID de esta nueva sesion
		*/
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("INSERT INTO chatsessions (screen_name) VALUES ('$screen_name')");
		$lastID = mysql_insert_id($link);
		
		return $lastID;
	}
	
	function getOpenSession($deptID,$screen_name,$email,$site)
	{
		/**
		 * getOpenSession
		 * Trae la sesion abierta para el cliente
		*/
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$ress = mysql_query("SELECT * FROM chatrequests WHERE deptID = $deptID AND from_screen_name = '$screen_name' AND email = '$email' AND status in (0,1)");
 
		$af = mysql_affected_rows($link);
		
		
		
		if($af > 0)
		{
			while($ra = mysql_fetch_assoc($ress))
			{
				$sesID = $ra['sessionID']; 
			}
		}else{
		 $sesID = '';
		}		
		return $sesID;
	}
	
	function getChat($sessionID,$deptID,$userID)
	{
		/**
		 * getChat
		 * trae el chat de la sesión requerida
		*/
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT * from chattalklog WHERE userID = $userID AND deptID = $deptID AND chat_session = $sessionID ORDER BY created ASC");
		
		return $res;
		
	}
	
	function getChatTranscript($sessionID,$deptID,$userID,$requestID)
	{
		/**
		 * getChatTranscript
		 * trae el transcript del chat seleccionado
		*/
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT * from chattranscripts WHERE userID = $userID AND deptID = $deptID AND chat_session = $sessionID");
		
		return $res;
		
	}
	
	function getClientData($sessionID, $deptID, $userID, $requestID)
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT * FROM chatrequests WHERE userID = $userID AND deptID = $deptID AND sessionID = $sessionID AND requestID = $requestID");
		$cdata = array();
		
		while($c = mysql_fetch_assoc($res))
		{
			$cdata['name'] = $c['from_screen_name'];
			$cdata['email'] = $c['email'];
			$cdata['browser_type'] = $c['browser_type'];
			$cdata['ip_address'] = $c['ip_address'];
			$cdata['telephone'] = $c['telephone'];
		}
		
		return $cdata;
	}
	
	function generateTranscript($sessionID,$deptID,$userID,$requestID)
	{
		/**
		 * generateTranscript
		 * Genera el Transcript de la sesion requerida
		*/

		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);

		$ch = $this->getChat($sessionID,$deptID,$userID);
		$ch2 = $this->getChat($sessionID,$deptID,$userID);
		$user_name = "";
		$email = "";
		$rs = mysql_query("SELECT * FROM chat_admin WHERE userID = $userID");
		while($r = mysql_fetch_assoc($rs))
		{
			$from_name = $r['name'];
		}
		
		$rss = mysql_query("SELECT * FROM chatrequests WHERE userID = $userID AND deptID = $deptID AND sessionID = $sessionID AND requestID = $requestID");
		while($r = mysql_fetch_assoc($rss))
		{
			$user_name = $r['from_screen_name'];
			$email = $r['email'];			
		}
		
		$ctrans = "";
		$ctransp = "";
		while($c = mysql_fetch_assoc($ch))
		{
			if($c['from_screen_name'] == $from_name) 
				{
					$ctrans .= '
					<div class="optalk ui-widget ui-widget-header ui-corner-all">
						<div class="optalkid">'.$from_name.' dice: </div>
						'.$c['formatted'].'
					</div><br/>';
				
				}elseif($c['from_screen_name'] == 'system'){
					$ctrans .= '<div class="chatwelcome">'.$c['formatted'].'</div><div class="clear"></div><br/>';
				}elseif($c['from_screen_name'] == $user_name){
					$ctrans .= '<div class="usertalk ui-widget ui-state-highlight ui-corner-all">
							<div class="usertalkid">'.$user_name.' dice: </div>
							'.$c['formatted'].'
						  </div>
						<br/>';
				}
		}
		
		while($c = mysql_fetch_assoc($ch2))
		{
			if($c['from_screen_name'] == $from_name) 
				{
					$ctransp .= ''.$from_name.' dice: '.$c['plain'].'<br/>';
				
				}elseif($c['from_screen_name'] == 'system'){
					$ctransp .= '<hr>'.$c['plain'].'<hr><br/>';
				}elseif($c['from_screen_name'] == $user_name){
					$ctransp .= ''.$user_name.' dice: '.$c['plain'].'<br/>';
				}
		}
		
		mysql_query("INSERT INTO chattranscripts (chat_session,userID,from_screen_name,email,deptID,plain,formatted) VALUES ($sessionID,$userID,'$user_name','$email',$deptID,'$ctransp','$ctrans')");

		mysql_query("DELETE FROM chattalklog WHERE chat_session = $sessionID AND userID = $userID AND deptID = $deptID");
		
		
	}

	function getSiteId($deptID)
	{
		/**
		 * getSiteId
		 * Trae el id del sitio dependiendo del departamento
		*/
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT cs.* FROM chatsites cs INNER JOIN chatdepartments cd WHERE cs.siteID = cd.siteID AND cd.deptID = $deptID");
		while($r = mysql_fetch_assoc($res))
		{
			$siteID = $r['siteID'];
		}
		
		return $siteID;
	}
	
	function getChatLogoSite($siteID)
	{
		/**
		 * getChatLogoSite
		 * Trae el logo del cliente dependiendo del ID del sitio
		*/

		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);

		$rs = mysql_query("SELECT name FROM chatsites WHERE siteID = $siteID");
		while($r = mysql_fetch_assoc($rs))
		{
			$name = $r['name'];
		}
		
		return "logo_".strtolower($name).'.gif';
	}
	
	function getSiteName($siteID)
	{
		/**
		 * getChatLogoSite
		 * Trae el logo del cliente dependiendo del ID del sitio
		*/

		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);

		$rs = mysql_query("SELECT name FROM chatsites WHERE siteID = $siteID");
		while($r = mysql_fetch_assoc($rs))
		{
			$name = $r['name'];
		}
		
		return $name;
	}
	
	function getChatLogo($deptID)
	{
		/**
		 * getChatLogo
		 * Trae el logo del chat dependiendo del departamento
		*/
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT cs.* FROM chatsites cs INNER JOIN chatdepartments cd WHERE cs.siteID = cd.siteID AND cd.deptID = $deptID");
		while($r = mysql_fetch_assoc($res))
		{
			$name = $r['name'];
		}
		
		return "logo_".strtolower($name).'.gif';
	}

	function getDepartmentList()
	{
		/**
		 * getDepartmentList
		 * Trae la lista de departamentos existentes
		*/
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT * from chatdepartments ORDER BY name");
		
		return $res;
	}
	
	function getOperatorList()
	{
		/**
		 * getOperatorList
		 * Trae la lista de operadores existentes y activos del sistema
		*/
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT * from chat_admin ORDER BY name");
		
		return $res;
	
	}
	
	function getQuestion($sessionID,$requestID)
	{
			/**
			 * getQuestion
			 * Trae la pregunta que realizo el cliente
			*/

			$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
			mysql_select_db(SQLDB,$link);

			$rs = mysql_query("SELECT question FROM chatrequests WHERE sessionID = $sessionID AND requestID = $requestID");
			while($r = mysql_fetch_assoc($rs))
			{
				$question = $r['question'];
			}

			return utf8_encode($question);
	}
	
	function getRequestData($sessionID,$requestID)
	{
			/**
			 * getRequestData
			 * Trae la informacion de la solicitud segun su número y sesión
			*/

			$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
			mysql_select_db(SQLDB,$link);

			$rs = mysql_query("SELECT * FROM chatrequests WHERE sessionID = $sessionID AND requestID = $requestID");

			return $rs;
	}
}

?>