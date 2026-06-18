<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';
	include_once 'command_security.php';

	waf_require_admin_session();

	if (!empty($_POST['optionsTipoTemplate']) &&
		!empty($_POST['optionsProtocolo']) &&
		!empty($_POST['ip']) &&
		!empty($_POST['puerto'])) {

		$tipo_template = clean(mysqli_real_escape_string($link, $_POST['optionsTipoTemplate']));
		$protocolo = clean(mysqli_real_escape_string($link, $_POST['optionsProtocolo']));

		if ($tipo_template != 'dns' || $protocolo != 'udp') {
			echo 'mal';
			exit;
		}

		$nombre = 'dns-lb';
		$nombre_archivo = $nombre.'.conf';
		$ruta_archivo = '../siteconfig/'.$nombre_archivo;

		$consulta = mysqli_query($link, "SELECT * FROM sitio WHERE nombre_sitio = '$nombre' AND activo_sitio = 1");
		if (mysqli_num_rows($consulta) > 0) {
			echo 'mal1';
			exit;
		}

		$servidores = '';
		foreach (array_keys($_POST['ip']) as $key) {
			$ip = trim($_POST['ip'][$key] ?? '');
			$puerto = trim($_POST['puerto'][$key] ?? '');

			if (!filter_var($ip, FILTER_VALIDATE_IP)) {
				echo 'mal';
				exit;
			}

			if (!ctype_digit($puerto) || (int)$puerto < 1 || (int)$puerto > 65535) {
				echo 'mal';
				exit;
			}

			$servidores .= '    server '.$ip.':'.$puerto.' max_fails=2 fail_timeout=3s;'."\n";
		}

		if ($servidores == '') {
			echo 'mal';
			exit;
		}

		$remote_addr = '$remote_addr';
		$time_local = '$time_local';
		$protocol = '$protocol';
		$status = '$status';
		$bytes_sent = '$bytes_sent';
		$bytes_received = '$bytes_received';
		$session_time = '$session_time';
		$upstream_addr = '$upstream_addr';
		$upstream_connect_time = '$upstream_connect_time';

		$contenido = "log_format dns_basic '$remote_addr [$time_local] $protocol $status '
                    '$bytes_sent $bytes_received $session_time '
                    '\"$upstream_addr\" \"$upstream_connect_time\"';

access_log /var/log/nginx/stream-dns-access.log dns_basic buffer=64k flush=5s;


upstream dns_cache {
    zone dns_cache 64k;
    hash $remote_addr consistent;
$servidores}


server {
    listen 53 udp reuseport default_server;
    proxy_responses   1;
    proxy_timeout     1s;
    proxy_pass        dns_cache;
}


server {
    listen 53 default_server;
    proxy_connect_timeout 1s;
    proxy_timeout         10s;
    proxy_pass            dns_cache;
}
";

		if (file_put_contents($ruta_archivo, $contenido, LOCK_EX) === false) {
			echo 'mal';
			exit;
		}

		chmod($ruta_archivo, 0777);

		$output = array();
		$return_var = 0;
		if (!waf_copy_stream_conf($nombre, $output, $return_var)) {
			error_log('g_load_balancer_udp.php: fallo al copiar '.$nombre_archivo.' a stream-enabled (codigo '.$return_var.'): '.implode(' | ', $output));
			echo 'mal';
			exit;
		}

		$tipo_sitio = clean(mysqli_real_escape_string($link, $_POST['optionsDominio'] ?? 'dns'));
		$fecha_r = date('Y-m-d');
		$fechaActualAux = new DateTime(date("Y-m-d"));
		$fechaActualAux = $fechaActualAux->add(new DateInterval('P3Y'));
		$fecha_expiracion = $fechaActualAux->format('Y-m-d');
		$ip_sitio = clean(mysqli_real_escape_string($link, implode(',', array_map('trim', (array)$_POST['ip']))));
		$puerto_sitio = clean(mysqli_real_escape_string($link, implode(',', array_map('trim', (array)$_POST['puerto']))));

		$query = mysqli_query($link, "INSERT INTO sitio VALUES (0,'$tipo_sitio','load_udp','dns','$nombre','$ip_sitio','$puerto_sitio','$protocolo',1,'$fecha_r','$fecha_expiracion','Online',0)") or die(mysqli_error($link));

		echo $query ? 'bien' : 'mal';
	} else {
		echo 'mal';
	}
?>
