<div id="dash_menu">
	<ul>
		<li <?php if(!isset($_REQUEST['p']) || (isset($_REQUEST['p']) && $_REQUEST['p'] == 'crm')){echo 'class="selected"';} ?>><a href="?p=crm">Inicio</a></li>
		<li <?php if(isset($_REQUEST['p']) && $_REQUEST['p'] == 'historial'){echo 'class="selected"';} ?>><a href="?p=historial">Historial</a></li>
		<?php if(fSession::get('tipo') == 'admin') { ?>
		<li <?php if(isset($_REQUEST['p']) && $_REQUEST['p'] == 'config'){echo 'class="selected"';} ?>><a href="?p=config">Configuracion</a></li>
		<?php } ?>
		<li><a href="logout.php">Cerrar Sesi&oacute;n</a></li>
	</ul>
</div>