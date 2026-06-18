<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

if (!empty($_POST['id_usuario']) && !empty($_POST['nombre']) && !empty($_POST['nombre2']) && !empty($_POST['email'])) {
	$id_usuario = $_POST['id_usuario'];
	$nombre = clean(mysqli_real_escape_string($link,$_POST['nombre']));
	$apellido = clean(mysqli_real_escape_string($link,$_POST['nombre2']));
	$email = clean(mysqli_real_escape_string($link,$_POST['email']));
	$pass = clean(mysqli_real_escape_string($link,$_POST['pass']));
	$id_host = clean(mysqli_real_escape_string($link,$_POST['id_host']));

	//validar tipo de usuario
	$queryUser = mysqli_query($link, "SELECT * FROM usuario
                                          WHERE usuario.id_usuario = '$id_usuario'");
    $rowUser = mysqli_fetch_array($queryUser);
    $tipo_usuario = $rowUser['tipo_usuario'];

	if ($tipo_usuario == 1) {
		if (empty($pass)) {
			//actualizamos la informacion del usuario
			$query = mysqli_query($link,"UPDATE usuario SET nombre_u = '$nombre',
															apellido_u = '$apellido',
															email_u = '$email' 
															WHERE id_usuario = '$id_usuario'") or die(mysqli_error());
		} else {
			//encriptar contraseña
			$opciones = [
			'cost' => 12
			];
			$passw = password_hash($pass,PASSWORD_BCRYPT,$opciones);
			//actualizamos la informacion del usuario
			$query = mysqli_query($link,"UPDATE usuario SET nombre_u = '$nombre',
															apellido_u = '$apellido',
															email_u = '$email',
															password_encrip = '$passw' 
															WHERE id_usuario = '$id_usuario'") or die(mysqli_error());
		}

		if (!empty($id_host)) {
        	//cambiamos el tipo de usuario
        	$query = mysqli_query($link,"UPDATE usuario SET tipo_usuario = 2
										WHERE id_usuario = '$id_usuario'") or die(mysqli_error());

        	$fecha_r = date('Y-m-d');
        	//guardamos los datos del usuario con su host
        	$query2 = mysqli_query($link,"INSERT INTO usuario_host VALUES (0,'$id_usuario','$id_host','$fecha_r')") or die(mysqli_error($link));
        }
	} 
	else { 
		if (empty($pass)) {
			//actualizamos la informacion del usuario
			$query = mysqli_query($link,"UPDATE usuario SET nombre_u = '$nombre',
															apellido_u = '$apellido',
															email_u = '$email' 
															WHERE id_usuario = '$id_usuario'") or die(mysqli_error());
		} else {
			//encriptar contraseña
			$opciones = [
			'cost' => 12
			];
			$passw = password_hash($pass,PASSWORD_BCRYPT,$opciones);
			//actualizamos la informacion del usuario
			$query = mysqli_query($link,"UPDATE usuario SET nombre_u = '$nombre',
															apellido_u = '$apellido',
															email_u = '$email',
															password_encrip = '$passw' 
															WHERE id_usuario = '$id_usuario'") or die(mysqli_error());
		}

        if (empty($id_host)) {
        	//cambiamos el tipo de usuario
        	$query = mysqli_query($link,"UPDATE usuario SET tipo_usuario = 1
										WHERE id_usuario = '$id_usuario'") or die(mysqli_error());

        	//eliminamos info del usuario en relacion de host
        	$queryDelete = mysqli_query($link,"DELETE FROM usuario_host
										WHERE id_usuario = '$id_usuario' ") or die(mysqli_error($link));
        } else {
        	//actualizar el host
        	$query = mysqli_query($link,"UPDATE usuario_host SET id_host = '$id_host'
										WHERE id_usuario = '$id_usuario'") or die(mysqli_error());
        }
	}

	echo "bien";
}else{
	echo "mal";
}
?>