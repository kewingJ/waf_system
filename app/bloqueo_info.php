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

$consult = mysqli_query($link,"SELECT * FROM rules WHERE rules.activo_rule = 1");
//optener el total de reglas
$total = mysqli_num_rows($consult);
$i = 0;
$total_bloqueos = 0;
$response = array();
while($rows = mysqli_fetch_array($consult))
{
    $i++;
    $id_rule = $rows['id_rule'];
    //optener totales de bloqueos por reglas
    $consult2 = mysqli_query($link,"SELECT * FROM bloqueo 
                                    INNER JOIN detalle_rule 
                                    ON bloqueo.idN = detalle_rule.numero_rule_detalle 
                                    INNER JOIN rules 
                                    ON detalle_rule.id_rule = rules.id_rule 
                                    WHERE bloqueo.activo_bloqueo = 1 AND rules.id_rule = '$id_rule'");
    $total_bloqueos += mysqli_num_rows($consult2);
}

$response = $total_bloqueos;         
echo json_encode($response);
mysqli_close($link)
?>