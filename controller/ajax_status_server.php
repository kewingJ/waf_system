<?php
// error_reporting(E_ERROR | E_PARSE);
// ini_set('display_errors', 'off');
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';

    function checkServerStatus($ip, $port) {
        $timeout = 1; // Tiempo de espera en segundos
    
        $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);
    
        if (!$socket) {
            // No se pudo establecer la conexión
            return 'Offline';
        } else {
            // La conexión se estableció correctamente
            fclose($socket);
            return 'Online';
        }
    }

    $consult = mysqli_query($link,"SELECT * FROM sitio WHERE activo_sitio = 1 AND tipo_config <> 'load'");
    while ($row = mysqli_fetch_array($consult)) {
        $respuesta = checkServerStatus($row['ip_sitio'], $row['puerto_sitio']);
        // print($respuesta);
        $id_sitio = $row['id_sitio'];
        $query = mysqli_query($link,"UPDATE sitio SET status = '$respuesta' WHERE id_sitio = '$id_sitio'") or die(mysqli_error($link));
    }
?>
