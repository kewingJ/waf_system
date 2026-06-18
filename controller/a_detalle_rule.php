<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

if (!empty($_POST['id_detalle_rule']) && !empty($_POST['nombreDetalle']) && !empty($_POST['codigo'])) {
	
	$id_detalle_rule = $_POST['id_detalle_rule'];
	$nombre = clean(mysqli_real_escape_string($link,$_POST['nombreDetalle']));
	$codigo = clean(mysqli_real_escape_string($link,$_POST['codigo']));

	//actualizamos la informacion
	$query = mysqli_query($link,"UPDATE detalle_rule SET nombre_d_r = '$nombre',
														numero_rule_detalle = '$codigo'
														WHERE id_detalle_r = '$id_detalle_rule'") or die(mysql_error());
	echo "bien";
} else { 
	echo "mal";
}
?>