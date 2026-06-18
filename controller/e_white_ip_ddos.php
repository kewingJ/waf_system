<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_usuario = $_SESSION['id_u'];

		if (!empty($_POST['id_ip'])) {
			
			//guardo
			$ip = clean(mysqli_real_escape_string($link,$_POST['id_ip']));

			// 
			$query1 = mysqli_query($link,"UPDATE bloqueo_ddos SET lista_blanca = 0 WHERE ip_ddos = '$ip'") or die(mysqli_error($link));

			// 
			$query2 = mysqli_query($link,"UPDATE whitelist_ddos SET activo = 0 WHERE ip_whitelist_ddos = '$ip'") or die(mysqli_error($link));
			
			// Guardar en lista blanca
			include_once 'ajax_whitelist_ddos.php';

			echo "bien";
		} else {
			echo "mal";
		}
?>