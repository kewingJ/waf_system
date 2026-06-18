<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') 
{
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");        
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

require("../includes/config.php");

$sql = mysqli_query($link,"SELECT * FROM datos_server ORDER BY id_server DESC LIMIT 1");
$response = array();
$row = mysqli_fetch_array($sql);
    
    $m = $row['memoria'];
    $d = $row['disco'];
    $c = $row['cpu'];

    array_push($response, array("id"=>1,
                                "servicio"=>'Memoria',
                                "cantidad"=>$m));

    array_push($response, array("id"=>2,
                                "servicio"=>'Disco',
                                "cantidad"=>$d));

    array_push($response, array("id"=>3,
                                "servicio"=>'CPU',
                                "cantidad"=>$c));

echo json_encode(array("server_response"=> $response));
mysqli_close($link)
?>