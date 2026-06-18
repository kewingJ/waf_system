<?php
require("../../includes/config.php");

//
$consult = mysqli_query($link,"SELECT DISTINCT(server) FROM bloqueo");
$total = 0;
while ($row = mysqli_fetch_array($consult)) {
    $nombre_hosting = $row['server'];
    if(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|](\.)[a-z]{2}/i",$nombre_hosting))
    {
        $consultTotal = mysqli_query($link,"SELECT * FROM bloqueo
                                            WHERE server = '$nombre_hosting'");
        $total += mysqli_num_rows($consultTotal);
    }
}

//echo $total;


//optener la data de mysql
$consult = mysqli_query($link,"SELECT DISTINCT(server) FROM bloqueo");
$j = 0;
while ($row = mysqli_fetch_array($consult)) {
    $nombre_hosting = $row['server'];
    $total_bloqueo = 0;

    if(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|](\.)[a-z]{2}/i",$nombre_hosting))
    {
        $consultTotal = mysqli_query($link,"SELECT * FROM bloqueo
                                            WHERE server = '$nombre_hosting'");
        $total_bloqueo = mysqli_num_rows($consultTotal);

        //ajustar el texto del nombre de hosting
        $nombre_hosting = recortar_texto($nombre_hosting, 25);

        if ($total_bloqueo > 0) {
            //calcular % de total
            $porcentaje = ((float)$total_bloqueo * 100) / $total; // Regla de tres
            $porcentaje = number_format(round($porcentaje, 2), 1);  // Quitar los decimales
            $arr_waf[$j] = array('nombre_hosting'=>$nombre_hosting,
                                'total_bloqueo'=>$total_bloqueo,
                                'porcentaje'=>$porcentaje);
            $j++;
        }
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