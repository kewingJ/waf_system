<?php
 	$baseDir = dirname(__DIR__);

 	include_once $baseDir . '/includes/config.php';
    include_once $baseDir . '/includes/security.php';
    include_once $baseDir . '/geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';
	date_default_timezone_set('America/Managua');

    $cronLock = waf_acquire_cron_lock('ajax_convertion_ddos');
	
	$i = 1;
	$logState = waf_open_incremental_log(dirname(__DIR__) . "/ddos.log", "ddos.log");
	if ($logState === false) {
		exit('No se pudo abrir ddos.log' . PHP_EOL);
	}
    list($fp, $stateFile) = $logState;

	while(!feof($fp)) 
	{
		$linea = fgets($fp);
        if ($linea === false) {
            continue;
        }

		//buscar en cada liena y separar los parametros
		$data = explode(' ', $linea);

		//verificar
		if (strpos($linea, ' banned ')) {
            // cambiar formato de fecha
            $fechaAux = @$data[0].' '.@$data[1];
            $fecha = str_replace('[', '', $fechaAux);
            $fecha = str_replace(']', '', $fecha);
            // otros datos
            $ip = @$data[3];
            $conexiones = @$data[5];

            // optener pais de la ip
            $codigoPais = getCountryFromIP($ip, "code");

            $total_bloqueo = 0;
			$consult = mysqli_query($link,"SELECT 1 FROM bloqueo_ddos
                                            WHERE fecha_ddos = '$fecha' AND ip_ddos = '$ip'
                                            LIMIT 1") or die(mysqli_error($link));
			$total_bloqueo = mysqli_num_rows($consult);
			if ($total_bloqueo == 0) {
                //verificar si la ip ya esta en la lista blanca
                $consult = mysqli_query($link,"SELECT 1 FROM bloqueo_ddos
                                                WHERE ip_ddos = '$ip' AND lista_blanca = 1
                                                LIMIT 1") or die(mysqli_error($link));
                $bandera = mysqli_num_rows($consult);
                $lista_blanca = 0;
                if ($bandera > 0) {
                    $lista_blanca = 1;
                }
                // guardar datos
                $query = mysqli_query($link,"INSERT INTO bloqueo_ddos VALUES (0, '$ip', '$codigoPais', '$fecha', '$conexiones', '$lista_blanca')") or die(mysqli_error($link));
            }
        }
	}
    waf_save_incremental_log_state($fp, $stateFile);
	fclose($fp);
?>
