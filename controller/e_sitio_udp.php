<?php
	session_start();
	if (!empty($_POST['id_sitio'])) {
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_sitio = clean(mysqli_real_escape_string($link, $_POST['id_sitio']));

		$querySelect = mysqli_query($link, "SELECT * FROM sitio WHERE sitio.id_sitio = '$id_sitio'");
		$row = mysqli_fetch_array($querySelect);

		if (!empty($row['nombre_sitio'])) {
			$documento = '../siteconfig/'.$row['nombre_sitio'].'.conf';
			if (file_exists($documento)) {
				unlink($documento);
			}
		}

		$query = mysqli_query($link, "UPDATE sitio SET activo_sitio = 0 WHERE id_sitio = '$id_sitio'") or die(mysqli_error($link));

		echo $query ? 'bien' : 'mal';
	} else {
		echo 'mal';
	}
?>
