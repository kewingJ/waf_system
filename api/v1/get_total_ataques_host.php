<?php
require("../../includes/config.php");

//optener el host
$json = file_get_contents('php://input');
$obj = json_decode($json, true);
$id_usuario = $obj['id_usuario'];

$consult = mysqli_query($link,"SELECT * FROM usuario
                                INNER JOIN usuario_host
                                ON usuario.id_usuario = usuario_host.id_usuario
                                INNER JOIN host
                                ON usuario_host.id_host = host.id_host
                                WHERE usuario.id_usuario = '$id_usuario'");
$row = mysqli_fetch_array($consult);
//optener el host del usuario
$nombre_host_master = $row['nombre_host'];

//
$totalAtaques = 0;
//optener total ataques Waf
$consultAtaque = mysqli_query($link,"SELECT COUNT(*) AS total_bloqueo FROM bloqueo WHERE server = '$nombre_host_master'");
$rowAtaques = mysqli_fetch_array($consultAtaque);
$totalAtaques = $rowAtaques['total_bloqueo'];

//
$totalBloqueoIp = 0;
//optener total ip bloqueados
$consultBloqueoIp = mysqli_query($link,"SELECT SUM(total_bloqueo_ip) AS total_bloqueo_ip FROM grafica_bloqueo_ip");
$rowBloqueoIp = mysqli_fetch_array($consultBloqueoIp);
$totalBloqueoIp = $rowBloqueoIp['total_bloqueo_ip'];

//optener los datos del server
$m = 0;
$d = 0;
$c = 0;
$conexiones = 0;

$queryServer = mysqli_query($link,"SELECT * FROM datos_server ORDER BY id_server DESC LIMIT 1");
$rowServer = mysqli_fetch_array($queryServer);

$m = $rowServer['memoria'];
$d = $rowServer['disco'];
$c = $rowServer['cpu'];

$queryConexion = mysqli_query($link,"SELECT * FROM conexiones ORDER BY id_conexion DESC LIMIT 1");
$rowConexion = mysqli_fetch_array($queryConexion);

$conexiones = $rowConexion['cantidad'];

$Totales = [
    "total_waf"  =>  $totalAtaques,
    "total_fuerza"  =>  $totalBloqueoIp,
    "memoria" => $m.'%',
    "disco" => $d.'%',
    "cpu" => $c.'%',
    "conexiones" => $conexiones
];

die(json_encode($Totales)); 

?>