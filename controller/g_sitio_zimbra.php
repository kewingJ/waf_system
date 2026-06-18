<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';
        include_once '../main.php';
		include_once 'command_security.php';
		
		if (!empty($_POST['optionsDominio']) && 
            !empty($_POST['optionsZimbra']) && 
			!empty($_POST['nombre']) && 
			!empty($_POST['ip']) && 
            !empty($_POST['optionshttp']) && 
			!empty($_POST['optionsCertificado'])) {

            $objeto = new metodosWaf();
            //verificar si el dominio ya existe
            $nombre 			= clean(mysqli_real_escape_string($link,$_POST['nombre']));    
            $consulta = mysqli_query($link, "SELECT * FROM sitio WHERE nombre_sitio = '$nombre' AND activo_sitio = 1");
            $total = mysqli_num_rows($consulta);
            if($total > 0){
                echo 'mal1';
            } else {
			
            $tipo_zimbra      = clean(mysqli_real_escape_string($link,$_POST['optionsZimbra']));
			$tipo_sitio 		= clean(mysqli_real_escape_string($link,$_POST['optionsDominio']));
			$nombre 			= clean(mysqli_real_escape_string($link,$_POST['nombre']));    
            $tipo_certificado 	= clean(mysqli_real_escape_string($link,$_POST['optionsCertificado']));
            $optionshttp 		= clean(mysqli_real_escape_string($link,$_POST['optionshttp']));
            $ip 				= clean(mysqli_real_escape_string($link,$_POST['ip']));
            $puerto_sitio 	    = clean(mysqli_real_escape_string($link,$_POST['puerto']));

            //formato de fecha
            if(!empty($_POST['fecha_expiracion'])){
                $fecha_texto        = $_POST['fecha_expiracion'];
                $fecha_timestamp    = DateTime::createFromFormat('d/m/Y', $fecha_texto);
                $fecha_expiracion   = $fecha_timestamp->format('Y-m-d');
            } else {
                $fechaActualAux     = new DateTime(date("Y-m-d"));
                $fechaActualAux     = $fechaActualAux->add(new DateInterval('P3Y'));
                $fecha_expiracion   = $fechaActualAux->format('Y-m-d');
            }

			$fecha_r = date('Y-m-d');

            $query = mysqli_query($link,"INSERT INTO sitio VALUES (0,'$tipo_sitio','zimbra','$tipo_zimbra','$nombre','$ip','$puerto_sitio','$tipo_certificado',1,'$fecha_r', '$fecha_expiracion','Online', 0)") 
			or die(mysqli_error($link));

			$id_sitio = mysqli_insert_id($link);

			//crear archivo
			$nombre_archivo = $nombre.'.vhost';
			$archivo = fopen('../siteconfig/'.$nombre_archivo, 'a');
			chmod('../siteconfig/'.$nombre_archivo, 0777);

			//parsear valores
			$server_name = "";
			$server_nameC = "";
			if ($tipo_sitio == 'dominio') {
				if (strpos($nombre, 'www.') !== false) {
					$nombre2 = str_replace("www.", "", $nombre);
					$server_name = $nombre.' '.$nombre2;
					$server_nameC = $nombre;
				} else {
					$server_name = 'www.'.$nombre.' '.$nombre;
					$server_nameC = $nombre;
				}
			} else {
				if (strpos($nombre, 'www.') !== false) {
					$nombre2 = str_replace("www.", "", $nombre);
				    $server_name = $nombre2;
					$server_nameC = $nombre2;
				} else {
					$server_name = $nombre;
					$server_nameC = $nombre;
				}
			}

			//opciones de checkbox
            if(!empty($_POST['checkuno'])){
                $checkuno = 'include /etc/nginx/bots.d/blockbots.conf;';
            } else {
                $checkuno = '#include /etc/nginx/bots.d/blockbots.conf;';
            }

            if(!empty($_POST['checkdos'])){
                $checkdos = 'include /etc/nginx/bots.d/ddos.conf;';
            } else {
                $checkdos = '#include /etc/nginx/bots.d/ddos.conf;';
            }

			//verificar el tipo de certificado
			$certificado = "";
			if($tipo_certificado == 'letsencript'){
				$certificado = 
				'ssl_certificate /etc/letsencrypt/live/'.$server_nameC.'/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/'.$server_nameC.'/privkey.pem; # managed by Certbot
    ssl_trusted_certificate /etc/letsencrypt/live/'.$server_nameC.'/chain.pem;';
			} else {
                if (strpos($server_nameC, 'www.') !== false) {
					$server_nameC = str_replace("www.", "", $server_nameC);
				}
                $respuestaDominio = $objeto->obtenerDominioPrincipal($server_nameC, $tipo_sitio);
				$certificado = 
                'ssl_certificate /etc/nginx/ssl/bundle.crt;
    ssl_certificate_key /etc/nginx/ssl/'.$respuestaDominio.'.key;';
			}

            //datos de string
            $host = '$host';
            $scheme = '$scheme';
            $proxy_add_x_forwarded_for = '$proxy_add_x_forwarded_for';
            $remote_addr = '$remote_addr';
            $http_referer = '$http_referer';

            $contenido = "";
            if($tipo_zimbra == 'Zimbra Admin'){
$contenido = "
server {
    listen 443 ssl;
    ".$checkuno."
    ".$checkdos."
    server_name  ".$server_name.";
    #... your common https site configuration

    location / { 
    include /etc/nginx/wafnaxsi/whitelists/ip_naxsi_whitelist;
    include /etc/nginx/wafnaxsi/whitelists/naxsi_whitelist.rules;
    include /etc/nginx/naxsi.rules;
    ".$checkuno."
    ".$checkdos."
    proxy_set_header Host ".$host.":7071;
    proxy_set_header X-Forwarded-For ".$proxy_add_x_forwarded_for.";
    proxy_set_header X-Forwarded-Proto ".$scheme.";
    proxy_redirect ".$optionshttp."".$host.":7071/ ".$optionshttp."".$host."/;
    proxy_pass ".$optionshttp."".$_POST['ip'].":".$_POST['puerto'].";
    }   
    ".$certificado."
}
";
            } else {
$contenido = "
server {
    listen 443 ssl;
    http2 on;
    ".$checkuno."
    ".$checkdos."
    server_name  ".$server_name.";

    location / {
    include /etc/nginx/wafnaxsi/whitelists/ip_naxsi_whitelist;
    include /etc/nginx/wafnaxsi/whitelists/naxsi_whitelist.rules;
    include /etc/nginx/naxsi.rules;
    ".$checkuno."
    ".$checkdos."
    proxy_set_header Host ".$host.";
    proxy_set_header Referer ".$http_referer.";
    proxy_set_header X-Forwarded-For ".$proxy_add_x_forwarded_for.";
    proxy_set_header X-Forwarded-Proto ".$scheme.";
    proxy_set_header X-Real-IP ".$remote_addr.";
    proxy_set_header X-Frame-Options SAMEORIGIN;
    proxy_pass ".$optionshttp."".$_POST['ip'].":/8443;
    }
    ".$certificado."
}
";
            }

            //pasar el contenido
			fputs($archivo, $contenido);
			fclose($archivo);

			// Copiar vhost a nginx sites-available
			$copyOutput = array();
			$copyCode   = 0;
			if (!waf_copy_site_vhost($nombre, $copyOutput, $copyCode)) {
				error_log('g_sitio_zimbra.php: fallo al copiar vhost '.$nombre.' (codigo '.$copyCode.'): '.implode(' | ', $copyOutput));
				echo "mal vhost";
				exit;
			}

			// Recargar Nginx para aplicar el nuevo vhost
			$reloadOutput = array();
			$reloadCode   = 0;
			if (!waf_reload_nginx($reloadOutput, $reloadCode)) {
				error_log('g_sitio_zimbra.php: fallo al recargar nginx (codigo '.$reloadCode.'): '.implode(' | ', $reloadOutput));
				echo "mal nginx";
				exit;
			}

			echo "bien";
        }
		} else {
			echo "mal";
		}
			
		
?>