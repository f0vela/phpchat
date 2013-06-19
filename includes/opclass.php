<?php
class OpClass
{
	
	function saveData($data = array()){
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		$query = "UPDATE chat_admin SET name = '".$data['name']."', email = '".$data['email']."'";
		
		if($data['usertype'] != ''){ $query .= ", usertype = '". $data['usertype'] ."'"; }
		if($data['status'] != ''){ $query .= ", status = '". $data['status'] ."'"; }
		
		if($data['password'] != ''){
			
			$newpass = fCryptography::hashPassword($data['password']);
			$query .= ", password = '". $newpass ."'";
		}
		
		$query .= " WHERE userID = ". $data['userID'];

		$re = mysql_query($query);
		
		fSession::set('name',$data['name']);
		fSession::set('email',$data['email']);
		
		return $re;
	}
	
	function CreateOp($data = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		$newpass = fCryptography::hashPassword($data['password']);
		$query = "INSERT INTO chat_admin (name,email,usertype,status,password,aspID) VALUES ('".$data['name']."', '".$data['email']."','". $data['usertype'] ."','". $data['status'] ."','". $newpass ."','1')";
		$re = mysql_query($query);
		$ret = mysql_insert_id($link);
		return $ret;
	}
	
	function changeOpStatus($data)
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		$query = "UPDATE chat_admin SET status = '".$data['status'] ."' ";		
		$query .= " WHERE userID = ". $data['userID'];
		
		$re = mysql_query($query);

	}
	
	function getMyData($userID)
	{
	
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT * FROM chat_admin WHERE userID = $userID");
		return $res;
		
	}
	
	function getRating($userID = 0)
	{
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$qry = "SELECT ca.userID, IFNULL((SELECT SUM(rating) as rate FROM chat_adminrate WHERE userID = ca.userID),0) as sumatotal, (SELECT count(*) FROM chat_adminrate WHERE userID = ca.userID) as totalcalifica, IFNULL((SELECT SUM(rating) as rate FROM chat_adminrate WHERE userID = ca.userID)/(SELECT count(*) FROM chat_adminrate WHERE userID = ca.userID),0) as promedio FROM chat_admin ca ";
		
		if($userID != 0)
		{
			$qry .= "WHERE userID = $userID"; 
		}
		
		$qry .= " ORDER BY totalcalifica DESC, promedio DESC";
		
		$res = mysql_query($qry);		
		return $res;
			
	}
	
	function getFreeOperator($deptID,$userID = 0)
	{
		/**
		 * getFreeOperator
		 *
		 * 	Trae el ID del operador libre al momento de la solicitud de soporte
		 * 	Para esto revisa:
		 * 	* que operadores estan activos y esten asignados al departamento del que esta entrando la llamada
		 *
		 *	-- quitado el 29 de septiembre por Frisley Velasquez 
		 *  -- * que operadores no tienen llamada actual
		 *
		 *	* que operadores no han rechazado la llamada actual
		 *	* selecciona aleatoriamente entre el listado filtrado
		 */
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$query = "SELECT (SELECT count(*) FROM chatrequests WHERE userID = ca.userID AND status = 0) as espera, (SELECT count(*) FROM chatrequests WHERE userID = ca.userID AND status = 1) as activas, ca.userID FROM chat_admin ca INNER JOIN chatuserdeptlist cud ON ca.userID = cud.userID WHERE ca.available_status = 1 AND ca.answercalls = 1 AND cud.deptID = $deptID ";
		// Solo agregar si se necesita que no entre a un usuario ya con llamadas 
		//AND NOT cud.userID IN (SELECT distinct userID FROM chatrequests WHERE status = 1)
		
		if($userID != 0) { $query .= " AND cud.userID <> $userID "; }
		
		$query .= "ORDER BY espera ASC";
		//echo $query;
		$res = mysql_query($query);
		$uID = array();
		$i = 0;
		while($r = mysql_fetch_assoc($res))
		{
			$uID[$i] = $r['userID'];
			$i++;
		}
		$rand = fCryptography::random(0, ($i-1));
		$final = $uID[$rand];
		
		if($final == 0) $final = 0;
		if($final == '' && $userID <> 0) $final = $userID;
		
		return $final;
	}
	
	function isOperatorAvailable($sid)
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT ca.* FROM chat_admin ca INNER JOIN chatuserdeptlist cud ON ca.userID = cud.userID WHERE ca.available_status = 1 AND ca.answercalls = 1 AND cud.deptID IN (SELECT siteID from chatdepartments WHERE siteID = $sid)");
		$userID = array();
		$i = 0;
		while($r = mysql_fetch_assoc($res))
		{
			$i++;
		}
		
		if($i > 0){ $hayoperador = 1; }else{ $hayoperador = 0; }
		
		return $hayoperador;
	}
	
	function getMessages()
	{
		/**
		 * getMessages
		 * 
		 * Regresa un objeto MYSQL de los mensajes para los operadores.
		 *
		 */
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT cs.name as siteName, cd.name as deptName, cm.* FROM chatmessages cm INNER JOIN chatsites cs ON cm.siteID = cs.siteID INNER JOIN chatdepartments cd ON cm.deptID = cd.deptID ORDER BY created DESC");
		
		return $res;	
	}
	
	function getMyCalls($userID)
	{
		/**
		 * getMyCalls
		 * 
		 * Regresa un objeto MYSQL de las llamadas actuales del operador.
		 *
		 */
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT cr.*, cd.name as deptName, cd.siteID FROM chatrequests cr INNER JOIN chatdepartments cd ON cr.deptID = cd.deptID WHERE userID = $userID AND cr.status in (0,1) ORDER BY created ASC");
		
		return $res;
		
	}
	
	function getMyHistoryCalls($userID = 0,$filtros = array())
	{
		/**
		 * getMyHistoryCalls
		 * 
		 * Regresa un objeto MYSQL de las llamadas historicas del operador.
		 *
		 */
		
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$query = "SELECT cr.*, ca.name as operatorName, cd.name as deptName, cd.siteID FROM chatrequests cr INNER JOIN chatdepartments cd ON cr.deptID = cd.deptID LEFT OUTER JOIN chat_admin ca ON ca.userID = cr.userID WHERE cr.status = 2 ";
		
		if($userID != 0){ $query .= " AND ca.userID = $userID "; }
		if($filtros['cuenta'] != '') $query .= " AND cd.deptID = '".$filtros['cuenta']."'";
		
		if($filtros['fecha'] != ''){ 
			$date = explode('-',$filtros['fecha']);
			$query .= " AND cr.created BETWEEN '".$filtros['fecha']." 00:00:00' AND '".$filtros['fecha']." 23:59:59'"; 
		}
		
		$query .= " ORDER BY created DESC";
		$res['data'] = mysql_query($query);
		$res['rows'] = mysql_affected_rows($link);
		
		return $res;
	}
}

?>