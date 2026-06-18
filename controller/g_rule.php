<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';
		if (!empty($_POST['nombre']) && !empty($_POST['inicio']) && !empty($_POST['fin'])) {
			
			/*guardo los datos de la regla*/
			$nombre = clean(mysqli_real_escape_string($link,$_POST['nombre']));
			$inicio = clean(mysqli_real_escape_string($link,$_POST['inicio']));
			$fin = clean(mysqli_real_escape_string($link,$_POST['fin']));
			$fecha_r = date('Y-m-d');

			$query = mysqli_query($link,"INSERT INTO rules VALUES (0,'$nombre','$inicio','$fin',1,'$fecha_r')") or die(mysql_error());
			echo "bien";
		}else{
			echo "mal";
		}
			
		
?>