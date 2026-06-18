<?php 
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once 'command_security.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $id = $_SESSION['id_u'];
    waf_require_admin_session();

	if (isset($_POST['id_sitio'])) 
	{
		$id_sitio = $_POST['id_sitio'];
        if (!ctype_digit((string)$id_sitio)) {
            echo "mal";
            exit;
        }
		$queryProject = mysqli_query($link,"SELECT * FROM sitio
                                            WHERE sitio.id_sitio = '$id_sitio'");
		$rowProject = mysqli_fetch_array($queryProject);

        $nombre_archivo = $rowProject['nombre_sitio'];
        if (!waf_validate_site_name($nombre_archivo)) {
            echo "mal";
            exit;
        }

        $documento = '../siteconfig/'.$nombre_archivo.'.vhost';
        // verificar si ya esta activado
        $palabraBuscar = '# Block forbidden country';
        $contenido = file_get_contents($documento);
        $posicion = strpos($contenido, $palabraBuscar);

        if ($posicion !== false) {
        } else {
            $palabraEspecifica = 'location / {';
            $nuevaLinea = '
    # Block forbidden country
    if ($allowed_country = no) {
        return 403;
    }';

            $posicion = strpos($contenido, $palabraEspecifica);
        
            $contenido = substr_replace($contenido, $nuevaLinea . PHP_EOL, $posicion + strlen($palabraEspecifica), 0);
            // Abrir el archivo en modo escritura
            $file = fopen($documento, 'w');
            // Escribir el contenido modificado en el archivo
            fwrite($file, $contenido);
            // Cerrar el archivo
            fclose($file);

            // actualizar campo en base de datos
            $query = mysqli_query($link,"UPDATE sitio SET activo_geo = 1
								WHERE id_sitio = '$id_sitio'") or die(mysqli_error($link));
        }

        $output = array();
        $return_var = 0;
        waf_copy_site_vhost($nombre_archivo, $output, $return_var);

        // Verificar si se ejecutó correctamente
        if ($return_var === 0) {
            waf_reload_nginx($output, $return_var);
            
            echo $return_var === 0 ? "bien" : "mal";
        } else {
            error_log('a_sitio_geo_ip.php: fallo al copiar vhost '.$nombre_archivo.' (codigo '.$return_var.'): '.implode(' | ', $output));
            echo "mal";
        }
	}
?>
