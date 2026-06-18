<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';
        include_once '../main.php';
		include_once 'command_security.php';
		
		if (!empty($_POST['optionsDominio']) && 
			!empty($_POST['nombre']) && 
			!empty($_POST['ip']) && 
			!empty($_POST['puerto']) &&
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
			
			$tipo_sitio = clean(mysqli_real_escape_string($link,$_POST['optionsDominio']));
            $ipSitio 	= clean(mysqli_real_escape_string($link,$_POST['ip']));
            $puerto     = clean(mysqli_real_escape_string($link,$_POST['puerto']));
			$nombre		= clean(mysqli_real_escape_string($link,$_POST['nombre']));    
            $tipo_certificado 	= clean(mysqli_real_escape_string($link,$_POST['optionsCertificado']));
            $optionshttp 		= clean(mysqli_real_escape_string($link,$_POST['optionshttp']));

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

			$fecha_r    = date('Y-m-d');

            $query = mysqli_query($link,"INSERT INTO sitio VALUES (0,'$tipo_sitio','conferencia','','$nombre','$ipSitio','$puerto','$tipo_certificado',1,'$fecha_r', '$fecha_expiracion','Online', 0)") 
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
			$id_sitio = $id_sitio + 999;


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

            // 
            $request_uri = 'https://$server_name$request_uri';
            $remote_addr = '$remote_addr';
            $http_host = '$http_host';
            $http_upgrade = '$http_upgrade';
            $host = '$host';


$contenido = "
upstream backend".$id_sitio." {  
    server ".$ipSitio.":".$puerto."; ## servidor web windows
    keepalive 2;
}
server {
	listen 80;
        server_name ".$server_name."; #YourIP or domain
        return 301 ".$request_uri.";  # redirect all to use ssl
        ".$checkuno."
        ".$checkdos."
    }

server {
    listen 443 ssl;
    http2 on;
    server_name ".$server_name.";
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;
    add_header Cache-Control \"max-age=3600,  public\";
    ".$certificado."
    ssl_protocols TLSv1.3 TLSv1.2;
    ssl_ciphers 'TLS13+AESGCM+AES128:TLS13+AESGCM+AES256:TLS13+CHACHA20:EECDH+AESGCM:EECDH+CHACHA20';
    ssl_conf_command Ciphersuites TLS_CHACHA20_POLY1305_SHA256:TLS_AES_256_GCM_SHA384;
    ssl_conf_command Options ServerPreference,PrioritizeChaCha,NoRenegotiation,NoResumptionOnRenegotiation;
    ssl_dhparam /etc/nginx/dhparams.pem;
    ssl_prefer_server_ciphers on;
    add_header Strict-Transport-Security \"max-age=31557600; includeSubDomains\";
    add_header X-Xss-Protection \"1; mode=block\" always;	
    add_header X-Frame-Options \"SAMEORIGIN\";
    add_header X-Content-Type-Options \"nosniff\" always;
    more_set_headers \"Referrer-Policy : strict-origin-when-cross-origin\";
    more_set_headers \"Server : Follow the white rabbit.\";
    keepalive_requests 1000;
    keepalive_timeout  75 75;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    brotli on;
    brotli_comp_level 4;
    brotli_types text/plain text/css application/json application/javascript application/x-javascript text/xml application/xml application/xml+rss text/javascript;

location / {
	ssi on;
        include /etc/nginx/naxsi.rules;  
        include /etc/nginx/wafnaxsi/whitelists/ip_naxsi_whitelist;
	include /etc/nginx/wafnaxsi/whitelists/naxsi_whitelist.rules;
	proxy_set_header X-Forwarded-For $remote_addr;
        proxy_set_header Host $http_host;
        proxy_pass ".$optionshttp."backend".$id_sitio.";

    }
# BOSH
    location /http-bind {
        proxy_pass http://".$ipSitio.":5280/http-bind;
        proxy_set_header X-Forwarded-For $remote_addr;
        proxy_set_header Host $http_host;

}

# xmpp websockets
    location /xmpp-websocket {
        proxy_pass              http://".$ipSitio.":5280/xmpp-websocket;
        proxy_http_version      1.1;
        proxy_set_header        Upgrade $http_upgrade;
        proxy_set_header        Connection \"upgrade\";
        proxy_set_header        Host $host;
        tcp_nodelay             on;
    }
}
";

            //pasar el contenido
			fputs($archivo, $contenido);
			fclose($archivo);

			// Copiar vhost a nginx sites-available
			$copyOutput = array();
			$copyCode   = 0;
			if (!waf_copy_site_vhost($nombre, $copyOutput, $copyCode)) {
				error_log('g_sitio_jitsi.php: fallo al copiar vhost '.$nombre.' (codigo '.$copyCode.'): '.implode(' | ', $copyOutput));
				echo "mal vhost";
				exit;
			}

			// Recargar Nginx para aplicar el nuevo vhost
			$reloadOutput = array();
			$reloadCode   = 0;
			if (!waf_reload_nginx($reloadOutput, $reloadCode)) {
				error_log('g_sitio_jitsi.php: fallo al recargar nginx (codigo '.$reloadCode.'): '.implode(' | ', $reloadOutput));
				echo "mal nginx";
				exit;
			}

			echo "bien";
        }
		} else {
			echo "mal";
		}
			
		
?>