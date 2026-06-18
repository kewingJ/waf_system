<?php
	session_start();
    include_once '../includes/config.php';
	include_once '../includes/security.php';

	if($_SESSION['activo'] == 1)
	{
        $id_usuario = $_SESSION['id_u'];

        //limpiar la tabla
        $query = mysqli_query($link,"TRUNCATE ataques") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE bloqueo") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE bloqueo_ddos") or die(mysqli_error($link));

        $query = mysqli_query($link,"UPDATE bloqueo_ddos_pais SET total_bloqueo_ddos_pais = 0,  iso3 = '' WHERE 1");

        $query = mysqli_query($link,"TRUNCATE bloqueo_ddos_pais_rango") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE bloqueo_ip") or die(mysqli_error($link));

        $query = mysqli_query($link,"UPDATE bloqueo_ip_pais SET total_bloqueo_ip_pais = 0,  iso3 = '' WHERE 1");

        $query = mysqli_query($link,"TRUNCATE bloqueo_master") or die(mysqli_error($link));

        $query = mysqli_query($link,"UPDATE bloqueo_pais SET total_bloqueo = 0,  iso3 = '' WHERE 1");

        $query = mysqli_query($link,"TRUNCATE bloqueo_pais_rango") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_bloqueo") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_bloqueo_ip") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_bloqueo_rango") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_bloqueo_rango_ip") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_consulta") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_ddos") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_principal") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_visitas") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE grafica_visitas_dominio") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE host") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE host_borrados") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE host_visita_borrados") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE notificacion_regla") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE reporte") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE respaldos") or die(mysqli_error($link));

        $query = mysqli_query($link,"UPDATE resumen_datos SET paquetes = 0, virus = 0, bloqueo_ip = 0, bloqueo_waf = 0 WHERE 1");

        $query = mysqli_query($link,"TRUNCATE sitio") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE usuario_host") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE visita_dominio") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE visita_dominio_group") or die(mysqli_error($link));

        $query = mysqli_query($link,"UPDATE visita_pais SET total_visita = 0, iso3 = '' WHERE 1");

        $query = mysqli_query($link,"TRUNCATE whitelist") or die(mysqli_error($link));

        $query = mysqli_query($link,"TRUNCATE whitelist_ddos") or die(mysqli_error($link));

        //limipar usuarios
        $query = mysqli_query($link,"DELETE FROM usuario WHERE id_usuario <> '$id_usuario'") or die(mysqli_error($link));

        echo "bien";
	}
	else {
		echo "mal";
	}
?>