<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

	//optener desde el archivo
     $fp = fopen("../clamscan.log", "r");
     while(!feof($fp)) 
     {   
         $linea = fgets($fp);
         //buscar en cada liena y separar los parametros
         $data = explode('\n', $linea);
         if (strpos($linea, 'files:')) {
             //
             $lineaAux = strstr($linea, 'Scanned files: ');
             if (!empty($lineaAux)) 
             {
                 $totalArchivos = explode('Scanned files: ', $lineaAux);
                 $totalArchivos = $totalArchivos[1];
             }

             //
             $lineaAux = strstr($linea, 'Infected files: ');
             if (!empty($lineaAux)) 
             {
                 $totalInfectados = explode('Infected files: ', $lineaAux);
                 $totalInfectados = $totalInfectados[1];
             }
         }
     }

    $totalAtaques = 0;
    //optener total ataques
    $consultAtaque = mysqli_query($link,"SELECT SUM(total_bloqueo) AS total_bloqueo FROM grafica_bloqueo");
    $rowAtaques = mysqli_fetch_array($consultAtaque);
    $totalAtaques = $rowAtaques['total_bloqueo'];

    $totalBloqueoIp = 0;
    //optener total ip bloqueados
    $consultBloqueoIp = mysqli_query($link,"SELECT SUM(total_bloqueo_ip) AS total_bloqueo_ip FROM grafica_bloqueo_ip");
    $rowBloqueoIp = mysqli_fetch_array($consultBloqueoIp);
    $totalBloqueoIp = $rowBloqueoIp['total_bloqueo_ip'];

    if(empty($totalAtaques)){
        $totalAtaques = 0;
    }

    if(empty($totalBloqueoIp)){
        $totalBloqueoIp = 0;
    }

	$query = mysqli_query($link,"UPDATE resumen_datos SET   paquetes = '$totalArchivos',
															virus = '$totalInfectados',
															bloqueo_ip = '$totalBloqueoIp',
															bloqueo_waf = '$totalAtaques'") or die(mysqli_error($link));
?>