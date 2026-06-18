<?php
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';

    $cadena = $_POST['ip_bandera'];
    $ip_bandera = trim($cadena); //limpiar espacios en blanco
    if (!empty($ip_bandera)) {
        $codigo_ip = getCountryFromIP($ip_bandera, "code");
    	echo '<span class="f16"><i class="flag '.strtolower($codigo_ip).' icono-bandera"></i></span>';
    } else{
    	echo "";
    }
?>