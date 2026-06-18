<?php
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    
	//optener la data de mysql y pasarla a JSON
	$consult = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio WHERE activo_visita = 1");
	$j = 0;
	while ($row = mysqli_fetch_array($consult)) {

		/************* GUARDAMOS LOS DATOS EN ARRAY PARA PASARLOS A JSON. ************/
		$arr_visita[$j] = array('id'=>$j,
							 'ip_visita'=> $row['ip_visita']);
	    $j++;
	}
	/************* CREAMOS EL JSON Y GUARDAMOS LOS DATOS. ************/
	$json_string = json_encode($arr_visita);
	$file = '../visita.json';
	file_put_contents($file, $json_string);

?>