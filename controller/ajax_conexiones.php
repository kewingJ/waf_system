<?php
	$baseDir = dirname(__DIR__);

 	include_once $baseDir . '/includes/config.php';
    include_once $baseDir . '/includes/security.php';
	
	$fp = fopen(dirname(__DIR__) . "/connect.txt", "r");
	while ($animalinfo = fscanf($fp, "%s\t%s")){
	    $linea = $animalinfo[0];
	}
	
	//
	$fecha_r = date('Y-m-d');

	//sumar las conexiones
	$consult = mysqli_query($link,"SELECT * FROM conexiones");
	$row = mysqli_fetch_array($consult);
	$cantidad = $row['cantidad'];

	$total = $cantidad + $linea;

	$query = mysqli_query($link,"UPDATE conexiones SET cantidad = '$total'") or die(mysqli_error($link));

	//echo $cantidad;

	fclose($fp);
?>