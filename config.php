<?php 
include('includes/opclass.php');
include('includes/chatclass.php');
include('includes/adminclass.php');

$tipo = fSession::get('tipo');
if($tipo == 'admin')
{
?>
<br/>
<div id="configdashboard">
	<ul>
		<li><a href="main.php?p=config&sp=admusuarios"><img src="images/Users.png" border="0" /><span>Administrar Usuarios</span></a></li>
		<li><a href="main.php?p=config&sp=admsitios"><img src="images/Fullscreen.png" border="0" /><span>Administrar Sitios/Departamentos</span></a></li>
		<!--li><a href="main.php?p=config&sp=admpermisos"><img src="images/Menu Item.png" border="0" /><span>Administrar Permisos</span></a></li-->
	</ul>
</div>
<?php }else{ ?>
<br/>
<div id="configdashboard">
	<ul>
		<li><a href="main.php"><img src="images/minus_red_ball.png" border="0" /><span>Acceso no autorizado</span></a></li>
	</ul>
</div>
<?php } ?>