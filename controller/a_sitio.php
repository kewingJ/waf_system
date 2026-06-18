<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';
		
		if (!empty($_POST['informacion']) &&
			!empty($_POST['nombre'])) {
			
			$informacion 			= $_POST['informacion'];
			$direccion_archivo 		= clean(mysqli_real_escape_string($link,$_POST['nombre']));
			//abrir archivo
			$archivo = fopen($direccion_archivo, 'w+');

			fwrite($archivo, $informacion);
			fclose($archivo);

			echo "bien";
		} else {
			echo "mal";
		}
			
		
?>