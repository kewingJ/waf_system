<?php
	session_start();
	if(!empty($_POST['id_usuario']))
	{
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_usuario = $_POST['id_usuario'];
		
		//eliminamos al usuario
		$query = mysqli_query($link,"UPDATE usuario SET activo_u = 0 WHERE id_usuario = '$id_usuario'") or die(mysql_error());

		echo "bien";
	}
	else {
		echo "mal";
	}
?>