<?php
	include('init.php');
	include('includes/conn.php');
	fSession::destroy();
	header('location: index.php');
?>