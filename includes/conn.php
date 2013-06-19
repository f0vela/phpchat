<?php
DEFINE('SQLSERVER','localhost');
DEFINE('SQLDB','didea_mazdachat');
DEFINE('SQLUSER','root');
DEFINE('SQLPASSWORD','root');
DEFINE('SQLPORT','3306');

$mysql_db = new fDatabase('mysql', SQLDB, SQLUSER, SQLPASSWORD, SQLSERVER,SQLPORT);
?>