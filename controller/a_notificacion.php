<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

if (!empty($_POST['id_notificacion'])) {
	
	$id_notificacion = $_POST['id_notificacion'];

	//actualizamos la informacion
	$query = mysqli_query($link,"UPDATE notificacion_regla SET activa_notificacion = 0
						WHERE id_notificacion = '$id_notificacion'") or die(mysqli_error($link));
	echo "bien";
} else { 
	echo "mal";
}
?>