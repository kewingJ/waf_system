<?php
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    
	//optener la data de mysql y pasarla a JSON
	$consult = mysqli_query($link,"SELECT * FROM bloqueo");
	$j = 0;
	while ($row = mysqli_fetch_array($consult)) {

		/************* GUARDAMOS LOS DATOS EN ARRAY PARA PASARLOS A JSON. ************/
		$arr_waf[$j] = array('id'=>$j,
							 'fecha_bloqueo'=> $row['fecha_bloqueo'],
							 'ip'=> $row['ip'],
							 'server'=> $row['server'],
							 'url'=>$row['url'],
							 'learning'=> $row['learning'],
							 'vers'=> $row['vers'],
							 'total_processed'=>$row['total_processed'],
							 'total_blocked'=>$row['total_blocked'],
							 'zoneN'=>$row['zoneN'],
							 'idN'=>$row['idN'],
							 'var_nameN'=>$row['var_nameN'],
							 'cscoreN'=>$row['cscoreN'],
							 'scoreN'=>$row['scoreN'],
							 'metodo'=>$row['metodo'],
							 'fecha_registro'=>$row['fecha_r']);
	    $j++;
	}
	/************* CREAMOS EL JSON Y GUARDAMOS LOS DATOS. ************/
	$json_string = json_encode($arr_waf);
	$file = '../bloqueo.json';
	file_put_contents($file, $json_string);

?>