<?php
	require("../../includes/config.php");

	$json = file_get_contents('php://input');
	$obj = json_decode($json, true);
	$ip_bloqueada = $obj['ip_bloqueada'];

	if (!empty($ip_bloqueada)) {
		/*guardo*/
		$ip = $ip_bloqueada;
		$fecha_r = date('Y-m-d');

		$query = mysqli_query($link,"INSERT INTO whitelist VALUES (0,'$ip',1,'$fecha_r')") or die(mysqli_error($link));

		include_once '../../controller/ajax_whitelist.php';
			
		$InvalidMSG = 'bien';
		$InvalidMSGJSon = json_encode($InvalidMSG);
		echo $InvalidMSGJSon ;
	} else {
		$InvalidMSG = 'mal';
		$InvalidMSGJSon = json_encode($InvalidMSG);
		echo $InvalidMSGJSon ;
	}
?>