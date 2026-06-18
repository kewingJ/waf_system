<?php
	session_start();
 	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';
	
	error_reporting(E_ALL);
    ini_set('display_errors', '1');

	//ini_set('max_execution_time', '300');
    $cronLock = waf_acquire_cron_lock('ajax_convertion');

	$i = 1;
	$logState = waf_open_incremental_log(dirname(__DIR__) . "/waf.log", "waf.log");
	if ($logState === false) {
		exit('No se pudo abrir waf.log' . PHP_EOL);
	}
    list($fp, $stateFile) = $logState;
    $hayCambios = false;

    // Pre-cargar todas las reglas activas en memoria (evita N+1 queries por linea de log)
    $rulesMap = [];
    $rsRules = mysqli_query($link, "SELECT dr.numero_rule_detalle, r.nombre_rule FROM detalle_rule dr INNER JOIN rules r ON r.id_rule = dr.id_rule WHERE dr.activo_r = 1");
    while ($rRow = mysqli_fetch_assoc($rsRules)) {
        $rulesMap[(int)$rRow['numero_rule_detalle']] = $rRow['nombre_rule'];
    }
    mysqli_free_result($rsRules);

	while(!feof($fp)) 
	{
		$linea = fgets($fp);
		if ($linea === false) {
			continue;
		}

		//buscar en cada liena y separar os parametros
		$data = explode(' ', $linea);

		$datos = explode('\n', $linea);

		//verificar que los parametros a esa linea sea un ataque
		if (strpos($linea, 'NAXSI_FMT:') !== false) {
			//optener la infomacion del ataque

			//optener la fecha y hora del bloqueo
			$fecha_bloqueo = @$data[0].' '.@$data[1];

			$log_texto_aux = @$datos[0];
			$log_texto = str_replace("NAXSI_FMT", "WAF_BLOCK", $log_texto_aux);
			$log_texto = mysqli_real_escape_string($link,$log_texto);

			//echo '<br>';
			//optener la ip
			$lineaAux = strstr($linea, 'ip=');
			if (!empty($lineaAux)) 
			{
				$ip = explode('&', $lineaAux);
				$ip = explode('ip=', $ip[0]);
				$ip = $ip[1];
			} else {
				$ip = '';
			}
			$lineaAux = '';
			//echo $ip.'<br>';

			//optener el server
			$lineaAux = strstr($linea, 'server=');
			if (!empty($lineaAux)) 
			{
				$server = explode('&', $lineaAux);
				$server = explode('server=', $server[0]);
				$server = $server[1];
			} else {
				$server = '';
			}
			$lineaAux = '';
			//echo $server.'<br>';

			/********. filtar data antes de guardar. *******/
			$consult = mysqli_query($link,"SELECT 1 FROM bloqueo 
                                            WHERE bloqueo.fecha_bloqueo = '$fecha_bloqueo' AND bloqueo.server = '$server'
                                            LIMIT 1");
			$total_bloqueo = mysqli_num_rows($consult);
			
			//verificar si es un host que ya se elimino mas de 2 veces
			$consultHost = mysqli_query($link,"SELECT 1 FROM host_borrados WHERE nombre_host = '$server' LIMIT 2");
			$total_borrado_host = mysqli_num_rows($consultHost);
			if ($total_bloqueo == 0 && $total_borrado_host < 2) 
			{

				//optener la url
				$lineaAux = strstr($linea, 'uri=');
				if (!empty($lineaAux)) 
				{
					$url = explode('&', $lineaAux);
					$url = explode('uri=', $url[0]);
					$url = $url[1];
					// escapar comillas
					$url = mysqli_real_escape_string($link,$url);
				} else {
					$url = '';
				}
				$lineaAux = '';
				// echo $url.'<br>';

				//optener la learning
				$lineaAux = strstr($linea, 'learning=');
				if (!empty($lineaAux)) 
				{
					$learning = explode('&', $lineaAux);
					$learning = explode('learning=', $learning[0]);
					$learning = $learning[1];
				} else {
					$learning = '';
				}
				$lineaAux = '';
				//echo $learning.'<br>';

				//optener la vers
				$lineaAux = strstr($linea, 'vers=');
				if (!empty($lineaAux)) 
				{
					$vers = explode('&', $lineaAux);
					$vers = explode('vers=', $vers[0]);
					$vers = $vers[1];
				} else {
					$vers = '';
				}
				$lineaAux = '';
				//echo $vers.'<br>';


				//optener la total_processed
				$lineaAux = strstr($linea, 'total_processed=');
				if (!empty($lineaAux)) 
				{
					$total_processed = explode('&', $lineaAux);
					$total_processed = explode('total_processed=', $total_processed[0]);
					$total_processed = $total_processed[1];
				} else {
					$total_processed = '';
				}
				$lineaAux = '';
				//echo $total_processed.'<br>';

				//optener la total_blocked
				$lineaAux = strstr($linea, 'total_blocked=');
				if (!empty($lineaAux)) 
				{
					$total_blocked = explode('&', $lineaAux);
					$total_blocked = explode('total_blocked=', $total_blocked[0]);
					$total_blocked = $total_blocked[1];
				} else {
					$total_blocked = '';
				}
				$lineaAux = '';
				//echo $total_blocked.'<br>';

				//optener la zone0
				$lineaAux = strstr($linea, 'zone0=');
				if (!empty($lineaAux)) 
				{
					$zone0 = explode('&', $lineaAux);
					$zone0 = explode('zone0=', $zone0[0]);
					$zone0 = $zone0[1];
				} else {
					$zone0 = '';
				}
				$lineaAux = '';
				//echo $zone0.'<br>';

				//optener la id0
				$lineaAux = strstr($linea, 'id0=');
				if (!empty($lineaAux)) 
				{
					$id0 = explode('&', $lineaAux);
					$id0 = explode('id0=', $id0[0]);
					$id0 = $id0[1];
				} else {
					$id0 = 0;
				}
				$lineaAux = '';
				//echo $id0.'<br>';

				/*************** obtener el nombre del tipo de ataque ************/
	            $nombre_rule = isset($rulesMap[(int)$id0]) ? $rulesMap[(int)$id0] : $id0;

				//optener la cscore0
				$lineaAux = strstr($linea, 'cscore0=');
				if (!empty($lineaAux)) 
				{
					$cscore0 = explode('&', $lineaAux);
					$cscore0 = explode('cscore0=', $cscore0[0]);
					$cscore0 = $cscore0[1];
				} else {
					$cscore0 = '';
				}
				$lineaAux = '';
				//echo $cscore0.'<br>';

				//optener la score0
				$lineaAux = strstr($linea, 'score0=');
				if (!empty($lineaAux)) 
				{
					$score0 = explode('&', $lineaAux);
					$score0 = explode('score0=', $score0[0]);
					$score0 = $score0[1];
				} else {
					$score0 = '';
				}
				$lineaAux = '';
				//echo $score0.'<br>';

				//optener la var_name0
				$lineaAux = strstr($linea, 'var_name0=');
				if (!empty($lineaAux)) 
				{
					$var_name0 = explode(',', $lineaAux);
					$var_name0 = explode('var_name0=', $var_name0[0]);
					$var_name0 = $var_name0[1];
				} else {
					$var_name0 = '';
				}
				$lineaAux = '';
				//echo $var_name0.'<br>';

				//optener la metodo
				$lineaAux = strstr($linea, 'request: "');
				if (!empty($lineaAux)) 
				{
					$metodo = explode(' /', $lineaAux);
					$metodo = explode('request: "', $metodo[0]);
					$metodo = $metodo[1];
				} else {
					$metodo = '';
				}
				$lineaAux = '';
				//echo $metodo.'<br>';

				//optener la configuracion
				$lineaAux = strstr($linea, 'config=');
				if (!empty($lineaAux)) 
				{
					$config = explode('&', $lineaAux);
					$config = explode('config=', $config[0]);
					$config = $config[1];
				} else {
					$config = '';
				}
				$lineaAux = '';
				//echo $config.'<br>';
				//si la ip esta ignorada pasarla a whitelist
				if ($config == 'ignore') {
			
					/*guardo*/
					$ip_ignorada = $ip;
					$fecha_r_ip = date('Y-m-d');

					$id_usuario = 1;
					if(!empty($_SESSION['id_u'])){
						$id_usuario = $_SESSION['id_u'];
					}

					//verificar que no es una ip repetida
					$queryIP = mysqli_query($link,"SELECT 1 FROM whitelist WHERE whitelist.ip_white = '$ip_ignorada' LIMIT 1");
					$total_whitelist = mysqli_num_rows($queryIP);
					if($total_whitelist == 0){
						$queryIp = mysqli_query($link,"INSERT INTO whitelist VALUES (0,'$id_usuario','$ip_ignorada',1,'$fecha_r_ip')") or die(mysqli_error($link));
						include_once 'ajax_whitelist.php';
					}
				}

				// verificar si pertenece a la regla 1501
				$regla_nueva = "";
				if($id0 == 1501){
					$regla_nueva = 'BasicRule wl:'.$id0.' "mz:$URL:'.$url.'|'.$zone0.'";';
					$documento = '../config/naxsi_whitelist.rules';
					//verificar si la regla existe
					$palabraBuscar = $regla_nueva;
					$contenido = file_get_contents($documento);
					$posicion = strpos($contenido, $palabraBuscar);

					if ($posicion !== false) {
					} else {
						file_put_contents($documento, $regla_nueva . PHP_EOL, FILE_APPEND);
					}
				}

				/************* GUARDAMOS LOS DATOS EN MYSQL. ************/
				$fecha_r = date('y-m-d');
				$guardar = mysqli_query($link,"INSERT INTO bloqueo VALUES (0,'$fecha_bloqueo','$ip','$server','$url','$learning','$vers','$total_processed','$total_blocked','$zone0','$id0','$nombre_rule','$var_name0','$cscore0','$score0','$metodo',1,'$fecha_r','$log_texto')");


				$id_bloqueo_a = mysqli_insert_id($link);
				$hayCambios = true;

				// GUARDAMOS EN LA TABLA MASTER BLOQUEO
				$guardar = mysqli_query($link,"INSERT INTO bloqueo_master VALUES (0,'$id_bloqueo_a',
																					'bloqueo',
																					'$ip',
																					'$fecha_bloqueo',
																					'$nombre_rule')");
			}
		}
	}

	waf_save_incremental_log_state($fp, $stateFile);
	fclose($fp);

	if ($hayCambios || waf_should_process_full_log()) {
    	include_once 'ajax_estadistica_pais.php';
	}
?>
