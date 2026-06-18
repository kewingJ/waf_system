<?php
require("../../includes/config.php");
require('../../geoIp/geoiploc.php');

//optener el host
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$id_usuario = $obj['id_usuario'];
$tipo_usuario = $obj['tipo_usuario'];

$consultMaster = mysqli_query($link,"SELECT * FROM usuario
                                INNER JOIN usuario_host
                                ON usuario.id_usuario = usuario_host.id_usuario
                                INNER JOIN host
                                ON usuario_host.id_host = host.id_host
                                WHERE usuario.id_usuario = '$id_usuario'");
$rowMaster = mysqli_fetch_array($consultMaster);
//optener el host del usuario
$nombre_host_master = $rowMaster['nombre_host'];

$consultGeneral = "";
//optener la data de mysql
if ($tipo_usuario == "admin") {
    $consultGeneral = mysqli_query($link,"SELECT * FROM bloqueo
                               WHERE (tipo_ataque = 'rfi' OR  tipo_ataque = 'RFI')
                               ORDER BY id_bloqueo 
                               DESC LIMIT 100");
} else {
    $consultGeneral = mysqli_query($link,"SELECT * FROM bloqueo
                               WHERE (tipo_ataque = 'rfi' OR  tipo_ataque = 'RFI')
                               AND server = '$nombre_host_master'
                               ORDER BY id_bloqueo 
                               DESC LIMIT 100");
}

$j = 0;
while ($row = mysqli_fetch_array($consultGeneral)) {
    //antes de cualquier operacion verificar los datos a optener
    $ip_ataque = $row["ip"];
    if (filter_var($ip_ataque, FILTER_VALIDATE_IP)) {
        $nombrePais = '';
        $codigo_pais = '';
        $bandera = '';
        
        //
        $server_aux = '';
        $server_aux = $row["server"];
        $server = recortar_texto($server_aux, 14);
        //optener el nombre del pais del ataque
        $ip_ataque = $row["ip"];
        $codigo_ip = getCountryFromIP($ip_ataque, "code");

        //optener la url de la bandera del pais
        $bandera = "https://flagcdn.com/24x18/".strtolower($codigo_ip).".png";

        if (strtolower($codigo_ip) == "zz") {
            $codigo_ip = "NI";
        }

        //
        // $consultPais = mysqli_query($link,"SELECT * FROM paises WHERE iso = '$codigo_ip'");
        // $rowPais = mysqli_fetch_array($consultPais);
        // $nombrePais = $rowPais['nombre'];
        // $codigo_pais = $rowPais['iso'];

        //formato de fecha
        $myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $row['fecha_bloqueo']);
        $formatmyDateTime = $myDateTime->format('d/m/Y');

        $arr_waf[$j] = array('pais'=>$nombrePais,
                            'codigo_pais'=>$codigo_ip,
                            'url_bandera'=>$bandera,
                            'server'=>$server,
                            'ip_bloqueo'=> $ip_ataque,
                            'fecha_bloqueo'=> $formatmyDateTime);
        $j++;
    }
}
die(json_encode($arr_waf)); 

function recortar_texto($texto, $maxcaracteres){
    if(!empty($texto)){
        $texto = trim($texto);
        if(strlen($texto) > $maxcaracteres){
            $texto = substr($texto, 0, ($maxcaracteres - 3)).'...';
        }
        return $texto;
    }
}
?>