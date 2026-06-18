<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';
		if (!empty($_POST['api_key']) && !empty($_POST['id_activo'])) {
			
			/*guardo los datos del usuario*/
			$api_key = $_POST['api_key'];
			$id_activo = clean(mysqli_real_escape_string($link,$_POST['id_activo']));
			
            //validar tipo de usuario
			if ($id_activo == "No") {
				$id_activo = 0;
			} else {
				$id_activo = 1;
			}
				
            $query = mysqli_query($link,"UPDATE openai_api SET api_key = '$api_key', activado = '$id_activo'
						WHERE id_openai_api = 1") or die(mysqli_error($link));
			echo "bien";
		} else {
			echo "mal";
		}
?>