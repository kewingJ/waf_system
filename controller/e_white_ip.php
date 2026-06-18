<?php
	session_start();
	if(!empty($_POST['id_ip']))
	{
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_ip = $_POST['id_ip'];
		
		//eliminamos la regla
		$query = mysqli_query($link,"UPDATE whitelist SET activo_ip = 0 WHERE id_white = '$id_ip'") or die(mysql_error());

		include_once 'ajax_whitelist.php';

		echo "bien";
	} else {
		echo "mal";
	}
?>