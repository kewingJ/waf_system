<?php
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    
	//optener la data de mysql y pasarla a JSON
	$consult = mysqli_query($link,"SELECT * FROM sitio");
	$j = 0;
    $arr_sitio = array();
	while ($row = mysqli_fetch_array($consult)) {

        $nombre_archivo = $row['nombre_sitio'];

        $documento = $nombre_archivo.'.vhost';
        $archivo = fopen('../siteconfig/'.$nombre_archivo.'.vhost','r+');
        // Recorremos todas las lineas del archivo
        $informacion = "";
        while(!feof($archivo)){
            // Leyendo una linea
            $informacion .= fgets($archivo);
            // Imprimiendo una linea
            //echo nl2br($informacion);
        }
        // Cerrando el archivo
        fclose($archivo);

		$arr_sitio[$j] = array('id_sitio'       =>  $row['id_sitio'],
							 'tipo_sitio'       =>  $row['tipo_sitio'],
							 'tipo_config'      =>  $row['tipo_config'],
							 'tipo_zimbra'      =>  $row['tipo_zimbra'],
							 'nombre_sitio'     =>  $row['nombre_sitio'],
							 'ip_sitio'         =>  $row['ip_sitio'],
                             'puerto_sitio'     =>  $row['puerto_sitio'],
							 'tipo_certificado' =>  $row['tipo_certificado'],
							 'activo_sitio'     =>  $row['activo_sitio'],
							 'fecha_r_sitio'    =>  $row['fecha_r_sitio'],
                             'fecha_vencimiento'=>  $row['fecha_vencimiento'],
                             'status'           =>  $row['status'],
                             'informacion'      =>  $informacion,
                             'documento'        =>  $documento
                            );
	    $j++;
	}
	$json_string = json_encode($arr_sitio);
    $file = '../respaldo/list_sitio_backup_' . date('Ymd_His') . '.json';
    $nombre = 'list_sitio_backup_' . date('Ymd_His');
	file_put_contents($file, $json_string);

    $fecha_r      = date('Y-m-d');
    $query = mysqli_query($link,"INSERT INTO respaldos VALUES (0,'sitio','$nombre','$file','$fecha_r',1)") or die(mysqli_error($link));

?>