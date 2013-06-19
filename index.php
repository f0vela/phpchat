<?php
include('init.php');
include('includes/conn.php');
//fSession::setPath('/home/didea/public_html/crmchat/tmp');
fSession::open();
			if(isset($_POST['ingresar']) && $_POST['ingresar'] == 'Ingresar')
			{
				$email 	= fRequest::get('email');
				$pass 	= fRequest::get('password');
				$recordar = fRequest::get('recordarme');
				
				//echo fCryptography::hashPassword($pass);
				if(isset($email) && isset($pass))
				{
					$res = $mysql_db->query("SELECT * FROM chat_admin WHERE email = '".$email."'");
					foreach($res as $r)
					{
						$hash = $r['password'];
						if(fCryptography::checkPasswordHash($pass, $hash))
						{
							fSession::set('userid',$r['userID']);
							fSession::set('user',$r['login']);
							fSession::set('email',$r['email']);
							fSession::set('name',$r['name']);
							fSession::set('tipo',$r['usertype']);
							fSession::set('lex',0);
							fSession::set('lan',0);
							if(isset($recordarme) && $recordarme == 1)
							{
								fCookie::set('crmchat',$r['email'],'+1 week');
							}else{
								fCookie::set('crmchat',$r['email'],'-1 week');
							}
							
							header('location: main.php?p=crm');
						}

					}

				}
			}
?>
<html>
<head>
	<title>CRM :: Chat Interactivo</title>
	<link rel="stylesheet" href="css/default.css" type="text/css" />
	<script src="js/jquery-1.4.2.min.js" type="text/javascript" charset="utf-8"></script>
	
</head>
<body>
	<div id="dash_container">
		<div id="dash_content">
			<br /><br />
			<div id="login">
			<div id="logo" style="text-align:center;">
				<img src="images/logo.gif" border="0" />
			</div>
			<div id="loginform">
			<form action="index.php" name="ingreso de usuarios" method="post">
					<label for="email">Email:</label>
					<input type="text" name="email" id="email" value="<?php echo fCookie::get('crmchat'); ?>" tabindex="1" />
					<label for="password">Contrase&ntilde;a:</label>
					<input type="password" name="password" id="password" tabindex="2" />
					<input type="checkbox" name="recordarme" value="1" id="recordarme" <?php if(fCookie::get('crmchat')){ echo 'checked="checked"'; } ?> > <strong>Recordar mi correo?</strong>
					
					<input type="hidden" name="p" value="login"/><br />
					<input type="submit" name="ingresar" id="loginformsubmit" value="Ingresar" tabindex="3"/>
			</form>
			</div>
			</div>
			<br /><br /><br />
		</div>
		<div class="clear">&nbsp;</div>
		<?php include('footer.php'); ?>
	</div>
</body>
</html>