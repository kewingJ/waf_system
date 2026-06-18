<?php
	include_once 'includes/config.php';

	// sql Crea la tabla usando Lenguaje PHP
	$sql1 = "ALTER TABLE `vulnerability` ADD UNIQUE KEY `uniq_host_port_vuln_fecha` (`host`, `port`, `vulnerability`, `fecha_analisis`);";

	$sql2 = "
	CREATE TABLE `history_vulnerability` (
		`id_history` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		`host` varchar(200) NOT NULL,
		`resultado` varchar(200) NOT NULL,
		`fecha_escaneo` datetime NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

	// Se verifica si la tabla ha sido creado
	if ($link->query($sql1) === TRUE && $link->query($sql2) === TRUE) {
    	echo "las tablas Graficas han sido creadas";
    	echo '<br>';
	} else {
		echo "Hubo un error al crear las tablas graficas: " . $link->error;
	}
	// Cerramos la conexión
	$link->close();
?>