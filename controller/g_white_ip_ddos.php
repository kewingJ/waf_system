<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_usuario = $_SESSION['id_u'];

		if (!empty($_POST['ip_edit_ddos'])) {
			
			/*guardo*/
			$ip = clean(mysqli_real_escape_string($link,$_POST['ip_edit_ddos']));

			$query1 = mysqli_query($link,"UPDATE bloqueo_ddos SET lista_blanca = 1 WHERE ip_ddos = '$ip'") or die(mysqli_error($link));
			
			$fecha_r = date('Y-m-d');
			$query2 = mysqli_query($link,"INSERT INTO whitelist_ddos VALUES (0,'$ip',1,'$fecha_r')") or die(mysqli_error($link));

			// Guardar en lista blanca
			include_once 'ajax_whitelist_ddos.php';

			echo "bien";
		} else {
			echo "mal";
		}
?>