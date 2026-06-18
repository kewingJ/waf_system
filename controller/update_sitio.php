<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';
	include_once '../main.php';
	include_once 'command_security.php';

	waf_require_admin_session();

if (!empty($_POST['nombre']) &&
	!empty($_POST['id_sitio']) &&
	!empty($_POST['ip']) && 
	!empty($_POST['puerto'])) {
	$objeto = new metodosWaf();

	// valores nuevos
	$id_sitio = $_POST['id_sitio'];
	if (!ctype_digit((string)$id_sitio)) {
		echo "mal vacio";
		exit;
	}

	$nombre_update 	= clean(mysqli_real_escape_string($link,$_POST['nombre']));
	$ip_update 		= clean(mysqli_real_escape_string($link,$_POST['ip']));
	$puerto_update 	= clean(mysqli_real_escape_string($link,$_POST['puerto']));
	if (!waf_validate_site_name($nombre_update)) {
		echo "mal vacio";
		exit;
	}
	// $nueva_direccion = $ip_update.':'.$puerto_update;

	// valores anteriores
	$querySitio = mysqli_query($link,"SELECT * FROM sitio WHERE sitio.id_sitio = '$id_sitio'");
	$rowSitio = mysqli_fetch_array($querySitio);
	$nombre_old = $rowSitio['nombre_sitio'];
	if (!waf_validate_site_name($nombre_old)) {
		echo "mal vacio";
		exit;
	}
	$ip_old		= $rowSitio['ip_sitio'];
	$puerto_old = $rowSitio['puerto_sitio'];
	// $antigua_direccion = $ip_old.':'.$puerto_old;
	// echo $ip_old;

	$nombre_archivo_old = $nombre_old;
	$ruta_archivo  = '../siteconfig/'.$nombre_archivo_old.'.vhost';

	// Obtener el contenido del archivo
	$contenido = file_get_contents($ruta_archivo);

	// cambiar ip y puerto y host
	$contenido_modificado = str_replace(
		[$ip_old, $puerto_old, $nombre_old],
		[$ip_update, $puerto_update, $nombre_update],
		$contenido
	);

	// echo $contenido_modificado;
	file_put_contents($ruta_archivo, $contenido_modificado);
	$ruta_archivo_nuevo  = '../siteconfig/'.$nombre_update.'.vhost';

	// Cambiar nombre de archivo
	rename($ruta_archivo, $ruta_archivo_nuevo);

	//actualizar en base de datos
	$query = mysqli_query($link,"UPDATE sitio SET nombre_sitio = '$nombre_update',
												ip_sitio = '$ip_update',
												puerto_sitio = '$puerto_update'
												WHERE id_sitio = '$id_sitio'") or die(mysqli_error($link));
	
	$output = [];
	$return_var = 0;

	if (!waf_copy_site_vhost($nombre_update, $output, $return_var)) {
		error_log('update_sitio.php: fallo al copiar vhost '.$nombre_update.' (codigo '.$return_var.'): '.implode(' | ', $output));
		echo "Error al copiar el archivo: " . implode("\n", $output);
		exit;
	}

	if ($nombre_old !== $nombre_update) {
		$remove_output = array();
		$remove_return = 0;
		if (!waf_remove_site_vhost($nombre_old, $remove_output, $remove_return)) {
			error_log('update_sitio.php: fallo al eliminar vhost anterior '.$nombre_old.' (codigo '.$remove_return.'): '.implode(' | ', $remove_output));
		}
	}

	if (waf_reload_nginx($output, $return_var)) {
		echo "bien";
	} else {
		error_log('update_sitio.php: fallo al recargar nginx (codigo '.$return_var.'): '.implode(' | ', $output));
		echo "Error al recargar nginx: " . implode("\n", $output);
	}
} else { 
	echo "mal vacio";
}
?>
