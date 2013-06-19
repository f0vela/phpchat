<?
include('init.php');
include('includes/conn.php');
include('includes/opclass.php');
include('includes/chatclass.php');
include('includes/adminclass.php');

$op = new OpClass();
$chat = new ChatClass();
$adm = new AdminClass();

$sid = fRequest::get("sid");

$isop = $op->isOperatorAvailable($sid);
	Header( "Content-type: image/gif");
if($isop == 1){
	$imagen_png = file_get_contents('phplive_support_online.gif'); 
	echo $imagen_png;
}else{
	$imagen_png = file_get_contents('phplive_support_offline.gif'); 
	echo $imagen_png;
}
?>