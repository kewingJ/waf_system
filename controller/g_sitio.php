<?php
		session_start();
		include_once '../includes/config.php';
		include_once '../includes/security.php';
		include_once '../main.php';
		include_once 'command_security.php';

		error_reporting(E_ALL);
    	ini_set('display_errors', '1');
		
		if (!empty($_POST['optionsDominio']) && 
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
			
			$tipo_sitio 		= clean(mysqli_real_escape_string($link,$_POST['optionsDominio']));
			$nombre 			= clean(mysqli_real_escape_string($link,$_POST['nombre']));
			$ip_destino 		= clean(mysqli_real_escape_string($link,$_POST['ip']));
			$puerto 			= clean(mysqli_real_escape_string($link,$_POST['puerto']));
			$optionshttp 		= clean(mysqli_real_escape_string($link,$_POST['optionshttp']));
			$tipo_certificado 	= clean(mysqli_real_escape_string($link,$_POST['optionsCertificado']));
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

			$activo_geo = 0;
            if(!empty($_POST['checkgeo'])){
                $activo_geo = 1;
            } else {
                $activo_geo = 0;
            }

			$fecha_r = date('Y-m-d');

			$query = mysqli_query($link,"INSERT INTO sitio VALUES (0,'$tipo_sitio','sitio','','$nombre','$ip_destino','$puerto','$tipo_certificado',1,'$fecha_r','$fecha_expiracion','Online', '$activo_geo')") 
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
					$server_nameC = 'www.'.$nombre;
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

			$lista_ip = '';
			$access_denied1 = '';
            $access_denied2 = '';
			// 1) Toma el array (o uno vacío si no viene)
			$ips = $_POST['ip_validas'] ?? [];
			// 2) Normaliza y filtra: quita espacios, vacíos y duplicados
			$ips = array_map('trim', (array)$ips);
			$ips = array_filter($ips, fn($v) => $v !== '');
			$ips = array_values(array_unique($ips));
			// 3) Si quedan IPs válidas, arma la lista
			if (!empty($ips)) {
				foreach ($ips as $ip) {
					// Permite IPv4/IPv6 simples
					if (filter_var($ip, FILTER_VALIDATE_IP)) {
						$lista_ip .= "allow {$ip};\n";
						continue;
					}
					// Opcional: permitir rangos CIDR IPv4 (ej. 192.168.0.0/24)
					if (preg_match('/^\d{1,3}(?:\.\d{1,3}){3}\/\d{1,2}$/', $ip)) {
						$lista_ip .= "allow {$ip};\n";
					}
					// Si quieres, aquí podrías registrar/ignorar IPs inválidas
				}
				$lista_ip .= "deny all;";
				$access_denied1 = 'location = /denied.html {
                root /var/www/reportwui;
                internal;
                default_type text/html;
                expires -1;
                add_header Cache-Control "no-store, no-cache, must-revalidate" always;
                add_header Pragma "no-cache" always;
                }';

                $access_denied2 = 'error_page 403 /denied.html;';
			}

			//opciones de checkbox
			$condicionUno = '';
            if(!empty($_POST['checkuno'])){
                $checkuno = '#include /etc/nginx/bots.d/blockbots.conf;';
				$condicionUno = 'if ($bad_bot) { return 444; }';
            } else {
                // $checkuno = '#include /etc/nginx/bots.d/blockbots.conf;';
				$checkuno = '';
				$condicionUno = '';
            }

            if(!empty($_POST['checkdos'])){
                $checkdos = 'include /etc/nginx/bots.d/ddos.conf;';
            } else {
                $checkdos = '#include /etc/nginx/bots.d/ddos.conf;';
            }

			// opcion cache
			if(!empty($_POST['checkCache'])){
				$checkCache = "add_header 'Cache-Control' 'no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0';";
				$checkCache_proxy = 'proxy_cache off;';
			} else {
				$checkCache = 'add_header Cache-Control "max-age=86400,  public";';
				$checkCache_proxy = '';
			}

			$proteccion = "";
			if(!empty($_POST['checkgeo'])){
				$proteccion = '
	# Block forbidden country
	if ($allowed_country = no) {
		return 403;
	}';
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
				$certificado = 'ssl_certificate /etc/nginx/ssl/bundle.crt;
        ssl_certificate_key /etc/nginx/ssl/'.$respuestaDominio.'.key;';
			}

			$id_sitio = $id_sitio + 999;
			$request_uri = 'https://$server_name$request_uri';
			$host = '$host';
			$remote_addr = '$remote_addr';
			$proxy_add_x_forwarded_for = '$proxy_add_x_forwarded_for';
			$http_user_agent = '$http_user_agent';
			$bad_bot = '$bad_bot';
			//pasar el contenido
$contenido = "
upstream backend".$id_sitio." {
	server ".$ip_destino.":".$puerto."; ## servidor web windows
	keepalive 16;
	}
	server {
	listen 80;
	server_name ".$server_name."; #YourIP or domain
	".$checkuno."
	".$checkdos."
	return 301 ".$request_uri.";  # redirect all to use ssl
			
	}
			
	server {
	listen 443 ssl;
    http2 on;
	server_name ".$server_name.";
	access_log /var/log/nginx/access.log specialLog;
	error_log /var/log/nginx/error.log;
	".$condicionUno."
	".$checkCache."
	".$certificado."
	ssl_session_cache shared:SSL:10m;
	ssl_session_timeout  1h;
	ssl_session_tickets off;
	ssl_ecdh_curve X25519:sect571r1:secp521r1:secp384r1;
	ssl_protocols TLSv1.3 TLSv1.2;
	ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384;
	ssl_prefer_server_ciphers off;
	ssl_conf_command Ciphersuites TLS_CHACHA20_POLY1305_SHA256:TLS_AES_256_GCM_SHA384;
	ssl_conf_command Options ServerPreference,PrioritizeChaCha,NoRenegotiation,NoResumptionOnRenegotiation;
	ssl_dhparam /etc/nginx/dhparams.pem;
	add_header Strict-Transport-Security \"max-age=31536000; includeSubDomains\" always;
	add_header X-Content-Type-Options \"nosniff\" always;
	add_header X-Frame-Options \"SAMEORIGIN\" always;
	add_header Referrer-Policy \"same-origin\" always;
	keepalive_requests 1000;
	keepalive_timeout  75 75;
	brotli on;
	brotli_comp_level 4;
	brotli_types text/plain text/css application/json application/javascript application/x-javascript text/xml application/xml application/xml+rss text/javascript;
	".$access_denied1."

location / {
	".$proteccion."
	".$access_denied2."
	".$lista_ip."
	include /etc/nginx/wafnaxsi/whitelists/ip_naxsi_whitelist;
	include /etc/nginx/wafnaxsi/whitelists/naxsi_whitelist.rules;
	include /etc/nginx/naxsi.rules;
	include /etc/nginx/wafnaxsi/whitelists/wordpress.rules;
	include /etc/nginx/wafnaxsi/whitelists/zerobin.rules;
	include /etc/nginx/wafnaxsi/whitelists/drupal.rules;
	include /etc/nginx/wafnaxsi/whitelists/iris.rules;
	include /etc/nginx/wafnaxsi/whitelists/rutorrent.rules;
	include /etc/nginx/wafnaxsi/whitelists/dokuwiki.rules;
	include /etc/nginx/wafnaxsi/whitelists/etherpad-lite.rules;
	#include /etc/nginx/bots.d/blockbots.conf;
	#include /etc/nginx/bots.d/ddos.conf;
	proxy_http_version 1.1;
	proxy_set_header Connection \"\";
	proxy_set_header Host $host;
	proxy_set_header X-Real-IP $remote_addr;
	proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
	proxy_connect_timeout 300s;
	proxy_send_timeout 300s;
	proxy_read_timeout 300s;
	proxy_buffer_size 8k;
	proxy_buffers 8 16k;
	proxy_busy_buffers_size 96k;
	proxy_redirect off;
	proxy_max_temp_file_size 0;
	proxy_request_buffering off;
	proxy_buffering off;
	".$checkCache_proxy."
	proxy_pass ".$optionshttp."backend".$id_sitio.";
			
}

	location ~ /\. {
	deny all;
	}
		
	location = /robots.txt {
	log_not_found off;
	access_log off;
	}
		
	location = /favicon.ico {
	log_not_found off;
	access_log off;
	}

	if (".$http_user_agent." ~* \"LWP::Simple|wget|curl|libwww-perl|httrack|pagefreezer|SpiderLing|360Spider|webZIP|qihoobot|Baiduspider|Googlebot|Googlebot-Mobile|Googlebot-Image|Mediapartners-Google|Adsbot-Google|Feedfetcher-Google|Yahoo! Slurp|Yahoo! Slurp China|YoudaoBot|Sosospider|Sogou spider|Sogou web spider|MSNBot|ia_archiver|Tomato Bot|NSPlayer|bingbot|GPTBot|apitool|AI2Bot|Ai2Bot-Dolma|Amazonbot|anthropic-ai|Applebot|Applebot-Extended|Bytespider|CCBot|ChatGPT-User|Claude-Web|ClaudeBot|cohere-ai|Diffbot|DuckAssistBot|FacebookBot|FriendlyCrawler|Google-Extended|GoogleOther|GoogleOther-Image|GoogleOther-Video|GPTBot|iaskspider/2.0|ICC-Crawler|ImagesiftBot|img2dataset|ISSCyberRiskCrawler|Kangaroo Bot|Meta-ExternalAgent|Meta-ExternalFetcher|OAI-SearchBot|omgili|omgilibot|PanguBot|PerplexityBot|PetalBot|Scrapy|Sidetrade indexer bot|Timpibot|VelenPublicWebCrawler|Webzio-Extended|YouBot\") {
    	return 403;
    }
}
";

			fputs($archivo, $contenido);
			fclose($archivo);

			// Copiar vhost a nginx sites-available
			$copyOutput = array();
			$copyCode   = 0;
			if (!waf_copy_site_vhost($nombre, $copyOutput, $copyCode)) {
				error_log('g_sitio.php: fallo al copiar vhost '.$nombre.' (codigo '.$copyCode.'): '.implode(' | ', $copyOutput));
				echo "mal vhost";
				exit;
			}

			// Recargar Nginx para aplicar el nuevo vhost
			$reloadOutput = array();
			$reloadCode   = 0;
			if (!waf_reload_nginx($reloadOutput, $reloadCode)) {
				error_log('g_sitio.php: fallo al recargar nginx (codigo '.$reloadCode.'): '.implode(' | ', $reloadOutput));
				echo "mal nginx";
				exit;
			}

			echo "bien";
		}
		} else {
			echo "mal";
		}
			
		
?>