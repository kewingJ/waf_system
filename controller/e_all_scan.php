<?php
	session_start();
    include_once '../includes/config.php';
	include_once '../includes/security.php';

	if($_SESSION['activo'] == 1)
	{
        $id_usuario = $_SESSION['id_u'];

        //limpiar la tabla
        $query = mysqli_query($link,"TRUNCATE vulnerability") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE history_vulnerability") or die(mysqli_error($link));

        echo "Escaneos y datos de historico eliminados correctamente";
    }
?>