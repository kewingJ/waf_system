<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

	if (!empty($_POST['nombre'])) {
		$nombre_host = clean(mysqli_real_escape_string($link,$_POST['nombre']));

		$query = mysqli_query($link,"DELETE FROM bloqueo WHERE server = '$nombre_host'") or die(mysqli_error($link));

		//guardar en tabla de host basura
		$fecha_r = date('y-m-d');
		$guardar = mysqli_query($link,"INSERT INTO host_borrados VALUES (0,'$nombre_host','$fecha_r')");
		
		echo "bien";
	} else {
		echo "mal";
	}
?>