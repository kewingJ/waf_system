<?php
	session_start();
	if(!empty($_POST['id_rule']))
	{
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_rule = $_POST['id_rule'];
		
		//eliminamos la regla
		$query = mysqli_query($link,"UPDATE rules SET activo_rule = 0 WHERE id_rule = '$id_rule'") or die(mysql_error());

		echo "bien";
	}
	else {
		echo "mal";
	}
?>