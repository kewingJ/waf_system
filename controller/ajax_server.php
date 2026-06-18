<?php
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';


	$fp = fopen("../load.txt", "r");
	$cont = 0;
	while ($info = fscanf($fp, "%s\t%s\t%s")){

    	$memoria = $info[0];
    	$disco = $info[1];
    	$cpu = $info[2];

    	$memoria = explode('%', $memoria);
    	$disco = explode('%', $disco); 
    	$cpu = explode('%', $cpu);

    	$arr_datos[$cont] = array('memoria'=> $memoria[0],'disco'=> $disco[0],'cpu'=> $cpu[0]);
    	$cont++;
	}
	//
	//print_r(array_values($arr_datos[$cont-1]));

	$a = array_values($arr_datos[$cont-1])[0];
	//echo $a;

	$b = array_values($arr_datos[$cont-1])[1];
	//echo $b;

	$c = array_values($arr_datos[$cont-1])[2];
	//echo $c;

	$fecha_r = date('Y-m-d');

	$query = mysqli_query($link,"INSERT INTO datos_server VALUES (0,'$a','$b','$c','$fecha_r')") or die(mysql_error());

	fclose($fp);
?>