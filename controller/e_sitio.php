<?php
	session_start();
	if(!empty($_POST['id_sitio']))
	{
		include_once '../includes/config.php';
		include_once '../includes/security.php';
		include_once 'command_security.php';

		waf_require_admin_session();

		$id_sitio = $_POST['id_sitio'];
		if (!ctype_digit((string)$id_sitio)) {
			echo "mal id";
			exit;
		}

		//eliminamos el archivo
        $querySelect = mysqli_query($link,"SELECT * FROM sitio WHERE sitio.id_sitio = '$id_sitio'");
		$row = mysqli_fetch_array($querySelect);
			
		//eliminar archivo desde la carpeta
		if(!empty($row['nombre_sitio']))
		{
			if (!waf_validate_site_name($row['nombre_sitio'])) {
				echo "mal nombre";
				exit;
			}

            $documento = '../siteconfig/'.$row['nombre_sitio'].'.vhost';
			if (file_exists($documento)) {
				unlink($documento);
			}

			//lanzar comando para eliminar sitio del servidor "/usr/bin/rm /etc/nginx/sites-available/dominio o host"
			$output = array();
			$return_var = 0;
			if (!waf_remove_site_vhost($row['nombre_sitio'], $output, $return_var)) {
				error_log('e_sitio.php: fallo al eliminar vhost '.$row['nombre_sitio'].' (codigo '.$return_var.'): '.implode(' | ', $output));
				echo "mal vhost";
				exit;
			}
		}
		
		//eliminamos el sitio de la base de datos
		$query = mysqli_query($link,"UPDATE sitio SET activo_sitio = 0 WHERE id_sitio = '$id_sitio'") or die(mysqli_error($link));

		echo "bien";
	}
	else {
		echo "mal";
	}
?>
