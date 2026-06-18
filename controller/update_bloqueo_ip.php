<?php
 	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';

    //limpiar la tabla bloqueo ip
   	$queryBloqueoIp = mysqli_query($link,"TRUNCATE bloqueo_ip") or die(mysqli_error($link));

   	//limpiar la tabla bloqueo ip pais
   	$queryBloqueoIpPais = mysqli_query($link,"UPDATE bloqueo_ip_pais SET total_bloqueo_ip_pais = 0 WHERE 1") 
   	or die(mysqli_error($link));

   	include_once 'ajax_convertion_bloqueo_ip.php';
?>