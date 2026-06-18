<?php
require("../../includes/config.php");

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
    $consultGeneral = mysqli_query($link,"SELECT DISTINCT(server) FROM bloqueo");
} else {
    $consultGeneral = mysqli_query($link,"SELECT DISTINCT(server) FROM bloqueo
                               WHERE server = '$nombre_host_master'");
}

$j = 0;
while ($row = mysqli_fetch_array($consultGeneral)) {
    $nombre_hosting = $row['server'];
    $total_bloqueo = 0;

    //if(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|](\.)[a-z]{2}/i",$nombre_hosting))
    //{
    if (!empty($nombre_hosting)) {
        $consultTotal = mysqli_query($link,"SELECT * FROM bloqueo
                                            WHERE server = '$nombre_hosting'");
        $total_bloqueo = mysqli_num_rows($consultTotal);

        //ajustar el texto del nombre de hosting
        $nombre_hosting = recortar_texto($nombre_hosting, 25);

        if($total_bloqueo > 1) {
            $arr_waf[$j] = array('nombre_hosting'=>$nombre_hosting,
                            'total_bloqueo'=>$total_bloqueo);
            $j++;
        }
    }
    //}
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