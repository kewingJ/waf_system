<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");        
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

require("../includes/config.php");
$queryBloqueo = mysqli_query($link,"SELECT SUM(total_bloqueo) AS sumaBloqueo FROM bloqueo_pais");
$resBloqueo = mysqli_fetch_array($queryBloqueo);
$totalBloqueoPais = $resBloqueo['sumaBloqueo'];


$consult = mysqli_query($link,"SELECT * FROM bloqueo_pais
                                INNER JOIN paises
                                ON bloqueo_pais.id_pais = paises.id_pais");
$i = 0;
$response = array();
while($rows = mysqli_fetch_array($consult))
{
    $nombre_pais = $rows['nombre'];

    $totaPorcentaje = ($rows['total_bloqueo'] * 100) / $totalBloqueoPais;
    $total_bloqueos = round($totaPorcentaje, 0, PHP_ROUND_HALF_ODD);


    if ($total_bloqueos > 0) {
        $i++;
        array_push($response, array("id"=>$total_bloqueos,
                                    "state"=>'ON',
                                    "total"=>$rows['total_bloqueo'],
                                    "pais"=>$nombre_pais,
                                    "porcentaje"=>"".$total_bloqueos.""));
    }
}

rsort($response);


echo json_encode(array("server_response"=> $response));
mysqli_close($link)
?>