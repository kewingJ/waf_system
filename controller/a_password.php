<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

if (!empty($_POST['id_usuario']) && !empty($_POST['password'])) {
	$id_usuario = $_POST['id_usuario'];
	$password = clean(mysqli_real_escape_string($link,$_POST['password']));

	//encriptar contraseña
	$opciones = [
	    'cost' => 12
	];
	$password = password_hash($password,PASSWORD_BCRYPT,$opciones);
	//actualizamos la informacion del usuario
	$query = mysqli_query($link,"UPDATE usuario SET password_encrip = '$password' 
						WHERE id_usuario = '$id_usuario'") or die(mysqli_error($link));

	echo "bien";
} else {
	echo "mal";
}
?>