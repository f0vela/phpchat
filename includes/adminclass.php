<?php
class AdminClass
{

	function isAdmin($userID)
	{
	
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$res = mysql_query("SELECT usertype FROM chat_admin WHERE userID = $userID");
		$afectados = mysql_affected_rows($res);
		$isadmin = false;
		
		if($afectados > 0){
			while($a = mysql_fetch_assoc($res))
			{
				if($a['usertype'] == 'admin'){ $isadmin = true; }
			}
		}
		
		return $isadmin;
	}

	function getUserList($fil = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$qry .= "SELECT * FROM chat_admin WHERE userID != '' ";
		
		if($fil['nombre'] != '')
		{
			$qry .= " AND name like '%".$fil['nombre']."%'";
		}
		
		if($fil['email'] != '')
		{
			$qry .= " AND email like '%".$fil['email']."%'";
		}
		
		$qry .= "ORDER BY name";
		
		$res = mysql_query($qry);
		
		return $res;
	}
	
	function getSiteList($fil = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$qry = "SELECT * FROM chatsites WHERE siteID != ''";
		if($fil['siteID'] != '')
		{
			$qry .= " AND siteID = ".$fil['siteID'];
		}
		if($fil['url'] != '')
		{
			$qry .= " AND url like'%".$fil['siteID']."%'";
		}	
		if($fil['name'] != '')
		{
			$qry .= " AND name '%".$fil['name']."%'";
		}	
		$qry .= " ORDER BY name";
		
		$res = mysql_query($qry);
		
		return $res;
	}
	
	function getSiteData($siteID)
	{
	
		return $this->getSiteList(array('siteID' => $siteID));
	
	}
	
	function changeSiteStatus($data = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		$query = "UPDATE chatsites SET status = '".$data['status'] ."' ";		
		$query .= " WHERE siteID = ". $data['siteID'];
		
		$re = mysql_query($query);

	}
	
	function saveSiteData($data = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		$query = "UPDATE chatsites SET name = '".$data['name']."'";
		
		if($data['url'] != ''){ $query .= ", url = '". $data['url'] ."'"; }
		if($data['status'] != ''){ $query .= ", status = '". $data['status'] ."'"; }
		
		$query .= " WHERE siteID = ". $data['siteID'];
		
		$re = mysql_query($query);		
		return $re;	
	}

	function createSiteData($data = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);

		$query = "INSERT INTO chatsites (name,url,status) VALUES ('".$data['name']."', '".$data['url']."','". $data['status'] ."')";
		mysql_query($query);
		$ret = mysql_insert_id($link);
		return $ret;

	}
	
	function getDepartmentData($siteID, $deptID)
	{
	
		return $this->getDepartmentList(array('siteID' => $siteID,'deptID' => $deptID));
	
	}
	
	function getDepartmentList($fil)
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		
		$qry = "SELECT * FROM chatdepartments WHERE deptID != '' AND siteID = ".$fil['siteID'];
		if($fil['deptID'] != '')
		{
			$qry .= " AND deptID = ".$fil['deptID'];
		}
		if($fil['name'] != '')
		{
			$qry .= " AND name like '%".$fil['name']."%'";
		}	
		$qry .= " ORDER BY name";
		
		$res['data'] = mysql_query($qry);
		$res['rows'] = mysql_affected_rows($link);
		return $res;
	}

	function changeDepartmentStatus($data = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		$query = "UPDATE chatdepartments SET visible = '".$data['visible'] ."' ";		
		$query .= " WHERE deptID = ". $data['deptID'];
		
		$re = mysql_query($query);

	}
	
	function saveDepartmentData($data = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);
		$query = "UPDATE chatdepartments SET name = '".$data['name']."'";
		
		if($data['email'] != ''){ $query .= ", email = '". $data['email'] ."'"; }
		if($data['visible'] != ''){ $query .= ", visible = '". $data['visible'] ."'"; }
		
		$query .= " WHERE deptID = ". $data['deptID'];
		
		$re = mysql_query($query);		
		return $re;	
	}

	function createDepartmentData($data = array())
	{
		$link = mysql_connect(SQLSERVER.":".SQLPORT,SQLUSER,SQLPASSWORD);
		mysql_select_db(SQLDB,$link);

		$query = "INSERT INTO chatdepartments (name,visible,email,siteID) VALUES ('".$data['name']."','".$data['visible']."', '".$data['email']."','". $data['siteID'] ."')";
		mysql_query($query);
		$ret = mysql_insert_id($link);
		return $ret;

	}
}

?>