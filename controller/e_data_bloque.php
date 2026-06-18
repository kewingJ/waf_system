<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

	$query = mysqli_query($link,"UPDATE resumen_datos SET   paquetes = 0,
															virus = 0,
															bloqueo_ip = 0,
															bloqueo_waf = 0") or die(mysql_error());
?>