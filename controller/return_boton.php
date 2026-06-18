<?php
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $cadena = $_POST['ip_bloqueo'];
    $ip_bandera = trim($cadena); //limpiar espacios en blanco
    // verificar si la ip esta en la lista blanca
    $consult = mysqli_query($link,"SELECT * FROM whitelist 
                                WHERE whitelist.activo_ip = 1
                                AND whitelist.ip_white = '$ip_bandera'");
    $rows = mysqli_fetch_array($consult);
    if (!empty($rows['id_white'])) {
        echo $rows['id_white'];
    } else {
    	echo 0;
    }
?>