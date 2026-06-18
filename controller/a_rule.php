<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

if (!empty($_POST['id_rule']) && !empty($_POST['nombre'])) {
	
	$id_rule = $_POST['id_rule'];
	$nombre = clean(mysqli_real_escape_string($link,$_POST['nombre']));
	$inicio = clean(mysqli_real_escape_string($link,$_POST['inicio']));
	$fin = clean(mysqli_real_escape_string($link,$_POST['fin']));

	//actualizamos la informacion
	$query = mysqli_query($link,"UPDATE rules SET nombre_rule = '$nombre',
															inicio_rule = '$inicio',
															fin_rule = '$fin'
															WHERE id_rule = '$id_rule'") or die(mysql_error());
	echo "bien";
} else { 
	echo "mal";
}
?>