<?php
require("../../includes/config.php");
require('../../geoIp/geoiploc.php');

//optener la data de mysql
$consult = mysqli_query($link,"SELECT * FROM whitelist WHERE whitelist.activo_ip = 1");
$j = 0;
while ($row = mysqli_fetch_array($consult)) {
    
    $ip_ataque = $row["ip_white"];
    $codigo_ip = getCountryFromIP($ip_ataque, "code");

    if (strtolower($codigo_ip) == "zz") {
        $codigo_ip = "NI";
    }

    //formato de fecha
    $myDateTime = DateTime::createFromFormat('Y-m-d', $row['fecha_r_ip']);
    $formatmyDateTime = $myDateTime->format('d/m/Y');

    //optener la url de la bandera del pais
    $bandera = "https://flagcdn.com/24x18/".strtolower($codigo_ip).".png";

    $arr_waf[$j] = array('codigo_pais'=>$codigo_ip,
                            'url_bandera'=>$bandera,
                            'ip_desbloqueo'=> $ip_ataque,
                            'fecha_desbloqueo'=> $formatmyDateTime);
    $j++;

}

if(!empty($arr_waf)){
    die(json_encode($arr_waf));
}
else{
    $InvalidMSG = 'mal';
    $InvalidMSGJSon = json_encode($InvalidMSG);
    echo $InvalidMSGJSon;
}
?>