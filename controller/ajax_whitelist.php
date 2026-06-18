<?php
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    
	//optener la data de mysql y pasarla a el archivo whitelist
	$consult = mysqli_query($link,"SELECT * FROM whitelist WHERE whitelist.activo_ip = 1");
	$list_ip = "";
	while ($row = mysqli_fetch_array($consult)) {
		$ip_white = $row['ip_white'];
		$aux_ip = "";
		//verificar que tipo de ip es
		if(strpos($ip_white, "/")){
			$aux_ip = 'IgnoreCIDR "'.$ip_white.'";';
		} else {
			$aux_ip = 'IgnoreIP '.$ip_white.';';
		}
		$list_ip .= $aux_ip."\n";
	}
	/************* CREAMOS EL JSON Y GUARDAMOS LOS DATOS. ************/
	$json_string = $list_ip;
	$file = '../config/ip_naxsi_whitelist';
	file_put_contents($file, $json_string);

	//ejecutar comando
	// $comando = shell_exec('systemctl reload nginx');
?>