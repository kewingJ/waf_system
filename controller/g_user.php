<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';
		if (!empty($_POST['nombre']) && !empty($_POST['nombre2']) && !empty($_POST['email']) && !empty($_POST['pass'])) {
			
			/*guardo los datos del usuario*/
			$nombre = clean(mysqli_real_escape_string($link,$_POST['nombre']));
			$apellido = clean(mysqli_real_escape_string($link,$_POST['nombre2']));
			$email = clean(mysqli_real_escape_string($link,$_POST['email']));
			$pass = clean(mysqli_real_escape_string($link,$_POST['pass']));
			$id_host = clean(mysqli_real_escape_string($link,$_POST['id_host']));
			//validar tipo de usuario
			if (empty($id_host)) {
				$tipo_usuario = 1;
			} else {
				$tipo_usuario = 2;
			}
			$fecha_r = date('Y-m-d');
			$activo = 1;

			//encriptar contraseña
			$opciones = [
			'cost' => 12
			];
			$passw = password_hash($pass,PASSWORD_BCRYPT,$opciones);
				
			$query = mysqli_query($link,"INSERT INTO usuario VALUES (0,'$nombre','$apellido','$email','$passw','$tipo_usuario','$activo','$fecha_r')") or die(mysqli_error($link));

			$id_usuario = mysqli_insert_id($link);

			if ($tipo_usuario == 2) {
				$query2 = mysqli_query($link,"INSERT INTO usuario_host VALUES (0,'$id_usuario','$id_host','$fecha_r')") or die(mysqli_error($link));
			}
			echo "bien";
		}else{
			echo "mal";
		}
			
		
?>