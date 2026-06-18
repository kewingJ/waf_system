<?php

	$baseDir = dirname(__DIR__);

	include_once $baseDir . '/includes/config.php';
	include_once $baseDir . '/includes/security.php';
    include_once $baseDir . '/geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';

	error_reporting(E_ALL);
    ini_set('display_errors', '1');

    date_default_timezone_set('America/Managua');
    $cronLock = waf_acquire_cron_lock('ajax_convertion_visitas');
	
	$i = 1;
	$logState = waf_open_incremental_log(dirname(__DIR__) . "/access.log", "access.log");
	if ($logState === false) {
		exit('No se pudo abrir access.log' . PHP_EOL);
	}
    list($fp, $stateFile) = $logState;
    $dominiosBorrados = array();
    $consultBorrados = mysqli_query($link, "SELECT nombre_host FROM host_visita_borrados");
    while ($rowBorrado = mysqli_fetch_array($consultBorrados)) {
        $dominiosBorrados[] = $rowBorrado['nombre_host'];
    }

    $insert_values = array();
    $batch_size = 1000;

	while(!feof($fp)) 
	{
		$linea = fgets($fp);
        if ($linea === false) {
            continue;
        }

		//buscar en cada liena y separar los parametros
		$data = explode(' ', $linea);

		// Procesar solo lineas que no sean registros del encabezado X-Forwarded-For.
		if (stripos($linea, 'forwarded for') === false) 
        {
			$fechaVisita = '';

            //obtener ip
            $ip_visita = @$data[0];
			//echo $ip_visita.'<br>';

			if(!empty($ip_visita))
			{
				//obtener la fecha de visita
				$auxFecha = @$data[3];
				$auxFecha = str_replace('[', '', $auxFecha);
				$auxFecha = explode(':', $auxFecha);
				$fechaVisita .= str_replace('/', '-', $auxFecha[0]).' '.$auxFecha[1].':'.$auxFecha[2].':'.$auxFecha[3];
				//cambiar formato de fecha
				$newDate = date('Y-m-d H:i:s', strtotime($fechaVisita));
				//echo $newDate.'<br>';

				//optener datos del .log con rango de hora
				$fechaFin = date("Y-m-d H:i:s");
				$fechaInicio = date("Y-m-d H:i:s",strtotime($fechaFin."- 10080 minutes"));
				//echo $fechaInicio.'<br>';
				
				if(!empty($newDate) && $newDate >= $fechaInicio && $newDate <= $fechaFin) 
				{
					//obtener dominio https://
					$lineaAux = strstr($linea, 'https://');
					if (!empty($lineaAux)) 
					{
						$dominio = explode('" ', $lineaAux);
						$dominio = explode('https://', $dominio[0]);
						$dominio = explode('/', $dominio[1]);
						$dominio = $dominio[0];
					} else {
						$dominio = '';
					}
					$lineaAux = '';

					//obtener dominio http://
					if(empty($dominio)) {
						$lineaAux = strstr($linea, 'http://');
						if (!empty($lineaAux)) 
						{
							$dominio = explode('" ', $lineaAux);
							$dominio = explode('http://', $dominio[0]);
							$dominio = explode('/', $dominio[1]);
							$dominio = $dominio[0];
						} else {
							$dominio = '';
						}
					}
					$lineaAux = '';
					//echo $dominio.'<br>';
					
					if (esFQDN($dominio)) {
						if(!empty($dominio) && !empty($ip_visita) && !empty($newDate)) {
							// $query = mysqli_query($link,"SELECT * FROM visita_dominio 
												// WHERE fecha_visita = '$newDate' AND ip_visita = '$ip_visita'");
							// $total = mysqli_num_rows($query);
							// if($total == 0) {
								//verificar si es un dominio que no se requiere guardar
                                $dominioBorrado = false;
                                foreach ($dominiosBorrados as $dominioBorradoActual) {
                                    if ($dominioBorradoActual !== '' && strpos($dominio, $dominioBorradoActual) !== false) {
                                        $dominioBorrado = true;
                                        break;
                                    }
                                }
								if(!$dominioBorrado){
                                    $ip_visita = mysqli_real_escape_string($link, $ip_visita);
                                    $dominio = mysqli_real_escape_string($link, $dominio);

                                    // Resolver país en tiempo de ingesta para evitar cálculos pesados en estadísticas
                                    $codigo_pais = getCountryFromIP($ip_visita, 'code');
                                    if (empty($codigo_pais)) {
                                        $codigo_pais = 'ZZ';
                                    }
									
                                    $insert_values[] = "('$newDate', '$ip_visita', '$codigo_pais', '$dominio', 1)";
                                    
                                    if (count($insert_values) >= $batch_size) {
                                        $query = "INSERT INTO visita_dominio (fecha_visita, ip_visita, codigo_pais, dominio, activo_visita) VALUES " . implode(',', $insert_values);
                                        mysqli_query($link, $query);
                                        $insert_values = array();
                                    }
								}
							// }
						}
					}
				}
			}

		}
	}

    // Insertar los registros restantes que no alcanzaron el tamaño del lote
    if (count($insert_values) > 0) {
        $query = "INSERT INTO visita_dominio (fecha_visita, ip_visita, codigo_pais, dominio, activo_visita) VALUES " . implode(',', $insert_values);
        mysqli_query($link, $query);
        $insert_values = array();
    }

	function esFQDN($cadena) {
        static $cache = array();
        if (isset($cache[$cadena])) {
            return $cache[$cadena];
        }

		// Expresión regular para validar un FQDN
		$patron = '/^((?!-)[A-Za-z0-9\-]{1,63}(?<!-)\.)+[A-Za-z]{2,6}$/';
	
		// Verifica si la cadena coincide con el patrón de un FQDN
		if (preg_match($patron, $cadena)) {
            // Ya no realizamos checkdnsrr() porque colapsa la ingesta si hay latencia.
            // Si tiene el formato correcto y está en el access.log, es válido.
            $cache[$cadena] = true;
            return true;
		} else {
            $cache[$cadena] = false;
			return false; // No es un FQDN válido
		}
	}

    waf_save_incremental_log_state($fp, $stateFile);
	fclose($fp);
?>
