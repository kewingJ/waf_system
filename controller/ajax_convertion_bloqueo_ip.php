<?php
    $baseDir = dirname(__DIR__);

 	include_once $baseDir . '/includes/config.php';
    include_once $baseDir . '/includes/security.php';
    include_once $baseDir . '/geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';
	date_default_timezone_set('America/Managua');

    $cronLock = waf_acquire_cron_lock('ajax_convertion_bloqueo_ip');
	
	$i = 1;
	$logState = waf_open_incremental_log($baseDir . "/fail2ban.log", "fail2ban.log");
	if ($logState === false) {
		exit('No se pudo abrir fail2ban.log' . PHP_EOL);
	}
    list($fp, $stateFile) = $logState;
    $hayCambios = false;

	while(!feof($fp)) 
	{
		$linea = fgets($fp);
        if ($linea === false) {
            continue;
        }

		//buscar en cada liena y separar los parametros
		$data = explode(' ', $linea);

		if (strpos($linea, 'Ban') !== false || strpos($linea, 'Unban') !== false) {
			//optener la fecha y hora del bloqueo
			$fecha_b = @$data[0];
			$hora_aux  = @$data[1];
			$hora_b = explode(',', $hora_aux);
			$hora_b = @$hora_b[0];

			$fecha_bloqueo = $fecha_b.' '.$hora_b;

			$fechaFin = date("Y-m-d H:i:s");
            $fechaInicio = date("Y-m-d H:i:s",strtotime($fechaFin."- 72000 minutes"));

			// echo 'fecha inicio= '.$fechaInicio.'<br>';
			// echo 'fecha fin= '.$fechaFin.'<br>';

			if(!empty($fecha_bloqueo) && $fecha_bloqueo >= $fechaInicio && $fecha_bloqueo <= $fechaFin) 
            {
				//optener la ip con Ban
				$lineaAux = strstr($linea, 'Ban ');
				if (!empty($lineaAux)) 
				{
					$ip = explode('Ban ', $lineaAux);
					$ip = $ip[1];
				} else {
					$lineaAuxDos = strstr($linea, 'Unban ');
					if (!empty($lineaAuxDos)) {
						$ip = explode('Unban ', $lineaAuxDos);
						$ip = $ip[1];
					}
					else{
						$ip = '';
					}
				}
				$lineaAux = '';

				//optener tipo ataque
				$lineaAux = strstr($linea, 'NOTICE  ');
				if (!empty($lineaAux)) 
				{
					$tipo_ataque_aux = explode('NOTICE  ', $lineaAux);
					$tipo_ataque_aux = $tipo_ataque_aux[1];

					//limitar el dato a obtener
					$ataque_a = explode('[', $tipo_ataque_aux);
					$ataque_a = @$ataque_a[1];

					//
					$ataque_b = explode(']', $ataque_a);
					$ataque_b = @$ataque_b[0];

					$tipo_ataque = $ataque_b;
				}
				$lineaAux = '';

				/********. filtar data antes de guardar. *******/
				$total_bloqueo = 0;
				$ip = preg_replace("/[\r\n|\n|\r]+/", "", $ip);
				$ip_escaped = mysqli_real_escape_string($link, $ip);
				$consult = mysqli_query($link,"SELECT 1 FROM bloqueo_ip
												WHERE fecha_bloqueo_ip = '$fecha_bloqueo' AND ip_bloqueada = '$ip_escaped'
                                                LIMIT 1");
				$total_bloqueo = mysqli_num_rows($consult);
				if ($total_bloqueo == 0) {
					
					/************* GUARDAMOS LOS DATOS EN MYSQL. ************/
					$guardar = mysqli_query($link,"INSERT INTO bloqueo_ip (ip_bloqueada, fecha_bloqueo_ip, tipo_ataque_ip, fecha_bloqueo_ip2) VALUES ('$ip','$fecha_bloqueo','$tipo_ataque', '$fecha_b')");

					$id_bloqueo_ip = mysqli_insert_id($link);
                    $hayCambios = true;

					//GUARDAMOS EN LA TABLA MASTER BLOQUEO
					$guardar = mysqli_query($link,"INSERT INTO bloqueo_master  VALUES (0,'$id_bloqueo_ip',
																						'bloqueo_ip',
																						'$ip',
																						'$fecha_bloqueo',
																						'$tipo_ataque')");

				} //else {
					//echo 'fecha= '.$fecha_bloqueo.' ip= '.$ip.' tipo ataque= '.$tipo_ataque.'<br>';
				//}
			}

		}
	}
    waf_save_incremental_log_state($fp, $stateFile);
	fclose($fp);

    if ($hayCambios || waf_should_process_full_log()) {
	    include_once __DIR__ . '/ajax_estadistica_pais_ip.php';
    }
?>
