<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

	if (!empty($_POST['nombre'])) {
		$nombre_host = clean(mysqli_real_escape_string($link,$_POST['nombre']));
		$opcion = $_POST['opcion'];
		$fecha_r = date('Y-m-d');

		mysqli_begin_transaction($link);

		$query2 = mysqli_query($link,"DELETE FROM grafica_visitas WHERE dominio = '$nombre_host'");
		$query3 = mysqli_query($link,"DELETE FROM visita_dominio_group WHERE dominio = '$nombre_host'");
		$guardar = mysqli_query(
			$link,
			"INSERT INTO host_visita_borrados (id_host_visita_borrados, nombre_host, fecha_r)
			 VALUES (0,'$nombre_host','$fecha_r')"
		);

		if ($query2 === false || $query3 === false || $guardar === false) {
			mysqli_rollback($link);
			echo "mal";
			exit;
		}

		mysqli_commit($link);

		echo "bien";
	} else {
		echo "mal";
	}
?>
