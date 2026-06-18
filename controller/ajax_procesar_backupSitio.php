<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    if(!empty($_POST['id_respaldo']))
	{
        $id_respaldo = $_POST['id_respaldo'];
        $consult = mysqli_query($link,"SELECT * FROM respaldos WHERE id_respaldo = '$id_respaldo'");
        $row = mysqli_fetch_array($consult);
        $ruta_archivo = $row['ruta_respaldo'];

        $data = file_get_contents($ruta_archivo);
        $sitios = json_decode($data, true);
        foreach ($sitios as $sitio) 
        {
            $id_sitio           = $sitio["id_sitio"];
            $tipo_sitio         = $sitio["tipo_sitio"];
            $tipo_config        = $sitio["tipo_config"];
            $tipo_zimbra        = $sitio["tipo_zimbra"];
            $nombre_sitio       = $sitio["nombre_sitio"];
            $ip_sitio           = $sitio["ip_sitio"];
            $puerto_sitio       = $sitio["puerto_sitio"];
            $tipo_certificado   = $sitio["tipo_certificado"];
            $activo_sitio       = $sitio["activo_sitio"];
            $fecha_r_sitio      = $sitio["fecha_r_sitio"];
            $fecha_vencimiento  = $sitio["fecha_vencimiento"];
            $status             = $sitio["status"];

            $querySitios = mysqli_query($link,"INSERT INTO sitio VALUES (0,'$tipo_sitio','$tipo_config','$tipo_zimbra','$nombre_sitio', '$ip_sitio', '$puerto_sitio', '$tipo_certificado','$activo_sitio','$fecha_r_sitio','$fecha_vencimiento','$status')") or die(mysqli_error($link));

            //crear archivos
            $nombre_archivo = $sitio['documento'];
            $archivo = fopen('../siteconfig/'.$nombre_archivo, 'a');
            chmod('../siteconfig/'.$nombre_archivo, 0777);

            //guardamos datos
            fputs($archivo, $sitio['informacion']);
            fclose($archivo);
        }
        echo 'bien';
    } else {
        echo "mal";
    }
?>