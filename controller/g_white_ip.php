<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_usuario = $_SESSION['id_u'];

		if (!empty($_POST['ip'])) {
			
			/*guardo*/
			$ip = clean(mysqli_real_escape_string($link,$_POST['ip']));
			$fecha_r = date('Y-m-d');

			$query = mysqli_query($link,"INSERT INTO whitelist VALUES (0,'$id_usuario','$ip',1,'$fecha_r')") or die(mysql_error());

			include_once 'ajax_whitelist.php';
			
			echo "bien";
		} else {
			echo "mal";
		}
?>