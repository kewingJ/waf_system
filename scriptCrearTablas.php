<link href="css/flags16-both.css" rel="stylesheet" type="text/css">
<link href="css/flags32-both.css" rel="stylesheet" type="text/css">

<style type="text/css">
            .skin-blue .main-header .navbar{
                background-image: url('img/Heater_waf-2022.png');
            }

            .flag.deprecated { color: silver; }
            .flag.island { color: navy; }
</style>
<?php
include_once 'includes/config.php';
include_once 'includes/security.php';
include_once 'geoIp/geoiploc.php';


$ip_ataque = "23.180.120.244";

$codigo_ip = getCountryFromIP($ip_ataque, "code");
$codigo_ip3 = getCountryFromIP($ip_ataque, "AbBr");

echo "Código de país (ISO2): " . $codigo_ip . "<br>";
echo "Código de país (ISO3): " . $codigo_ip3 . "<br>";

$bandera = '<span class="f16"><i class="flag '.strtolower($codigo_ip).' icono-bandera"></i></span>';
echo "Bandera: " . $bandera . "<br>";

?>