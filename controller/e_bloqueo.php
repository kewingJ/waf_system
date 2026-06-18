<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';

	$opcion = $_POST['opc'];

	switch ($opcion) {
		case 1:
			$query = mysqli_query($link,"DELETE FROM bloqueo LIMIT 10") or die(mysql_error());
			break;
		case 2:
			$query = mysqli_query($link,"DELETE FROM bloqueo LIMIT 20") or die(mysql_error());
			break;
		case 3:
			$query = mysqli_query($link,"DELETE FROM bloqueo LIMIT 30") or die(mysql_error());
			break;
		case 4:
			$query = mysqli_query($link,"DELETE FROM bloqueo LIMIT 40") or die(mysql_error());
			break;
		case 5:
			$query = mysqli_query($link,"DELETE FROM bloqueo LIMIT 50") or die(mysql_error());
			break;
		case 6:
			$query = mysqli_query($link,"TRUNCATE bloqueo") or die(mysql_error());

			$query2 = mysqli_query($link,"UPDATE bloqueo_pais SET total_bloqueo = 0") or die(mysql_error());
			break;
	}
?>