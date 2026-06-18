<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    
    /************* GUARDAMOS LOS DATOS DE VISITAS POR PAIS. ************/
    $consult = mysqli_query($link,"SELECT * FROM paises");

    while($rows = mysqli_fetch_array($consult))
    {
        $codigo_pais    = $rows['iso'];
        $id_pais        = $rows['id_pais'];
        $total_visitas  = 0;
        $codigo_ip3     = '';

        //optener total de ataque por pais
        $data = file_get_contents("../visita.json");
        $visitas = json_decode($data, true);
        foreach ($visitas as $visita) {
            $ip_visita = $visita["ip_visita"];

            $codigo_ip = getCountryFromIP($ip_visita, "code");
            if ($codigo_pais === $codigo_ip) {
                $total_visitas++;
                $codigo_ip3 = getCountryFromIP($ip_visita, "AbBr");
            }
        }

        if ($total_visitas > 0) {
            $query = mysqli_query($link,"UPDATE visita_pais SET total_visita = '$total_visitas',
                iso3 = '$codigo_ip3'
                WHERE id_pais = '$id_pais'");
            // echo "total : ".$total_visitas."<br> codigo : ".$codigo_ip3."<br>";
        }
    }

?>