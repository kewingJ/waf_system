<?php
	include_once 'includes/config.php';
    include_once 'includes/security.php';

    $consult = mysqli_query($link,"SELECT * FROM grafica_bloqueo_rango_ip");
   	while($rows = mysqli_fetch_array($consult)){
    	$fecha_bloqueo_actual = $rows['fecha_bloqueo_rango_ip'];
        $totalPorFecha = $rows['total_bloqueo_rango_ip'];
        echo '{ y: "'.$fecha_bloqueo_actual.'", item1: '.$totalPorFecha.'},<br>';
    }
?>