<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

if (!empty($_POST['dominio']) &&
	!empty($_POST['id_credential']) &&
	!empty($_POST['host']) && 
	!empty($_POST['nombre']) && 
	!empty($_POST['smtp']) &&
	!empty($_POST['port'])) {
	
	$id_credential = $_POST['id_credential'];

	$dominio = clean(mysqli_real_escape_string($link,$_POST['dominio']));
	$host = clean(mysqli_real_escape_string($link,$_POST['host']));
	$nombre = clean(mysqli_real_escape_string($link,$_POST['nombre']));
	$smtp = clean(mysqli_real_escape_string($link,$_POST['smtp']));
	$port = clean(mysqli_real_escape_string($link,$_POST['port']));

	if (empty($_POST['pass'])) {
		//actualizamos la informacion
		$query = mysqli_query($link,"UPDATE credentials_email SET dominio_mail = '$dominio',
																  host_mail = '$host',
																  user_email = '$nombre',
																  smtp_secure = '$smtp',
																  port = '$port'
																  WHERE id_credential = '$id_credential'") or die(mysql_error());
	} else {
		$pass = clean(mysqli_real_escape_string($link,$_POST['pass']));

		//actualizamos la informacion
		$query = mysqli_query($link,"UPDATE credentials_email SET dominio_mail = '$dominio',
																  host_mail = '$host',
																  user_email = '$nombre',
																  smtp_secure = '$smtp',
																  port = '$port',
																  password_mail = '$pass'
																  WHERE id_credential = '$id_credential'") or die(mysql_error());		
	}

	echo "bien";
} else { 
	echo "mal";
}
?>