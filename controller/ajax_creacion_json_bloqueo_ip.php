<?php
    $baseDir = dirname(__DIR__);

	include_once $baseDir . '/includes/config.php';
    include_once $baseDir . '/includes/security.php';
    include_once $baseDir . '/geoIp/geoiploc.php';
    
	//optener la data de mysql y pasarla a JSON
	$consult = mysqli_query($link,"SELECT * FROM bloqueo_ip");
	$j = 0;
	while ($row = mysqli_fetch_array($consult)) {

		/************* GUARDAMOS LOS DATOS EN ARRAY PARA PASARLOS A JSON. ************/
		$arr_waf[$j] = array('id'=>$j,
							 'fecha_bloqueo'=> $row['fecha_bloqueo_ip'],
							 'ip'=> $row['ip_bloqueada'],
							 'tipo_ataque'=>$row['tipo_ataque_ip'],
							 'fecha_bloqueo_2'=>$row['fecha_bloqueo_ip2']);
	    $j++;
	}
	/************* CREAMOS EL JSON Y GUARDAMOS LOS DATOS. ************/
	$json_string = json_encode($arr_waf);
	$file = $baseDir . '/bloqueo_ip.json';
	file_put_contents($file, $json_string);

?>
