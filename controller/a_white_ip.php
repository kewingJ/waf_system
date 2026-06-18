<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

if (!empty($_POST['ip']) && !empty($_POST['id_ip'])) {
	
	$id_ip = $_POST['id_ip'];
	$ip = clean(mysqli_real_escape_string($link,$_POST['ip']));

	//actualizamos la informacion
	$query = mysqli_query($link,"UPDATE whitelist SET ip_white = '$ip'
								 WHERE id_white = '$id_ip'") or die(mysql_error());

	include_once 'ajax_whitelist.php';
	
	echo "bien";

} else { 
	echo "mal";
}
?>