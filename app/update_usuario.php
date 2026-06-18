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
$data = file_get_contents("php://input");

if (isset($data)) {
    $request = json_decode($data);
    $id_usuario = $request->idU;
    $nombre = $request->nombre;
    $apellido = $request->apellido;
    $correo = $request->username;
}

$response = array();

$sql = "UPDATE usuario SET nombre_u = '$nombre',
                           apellido_u = '$apellido',
                           email_u = '$correo' WHERE id_usuario = '$id_usuario'";

if ($link->query($sql) === TRUE) {
    $response = "Registration successfull";
} else {
    $response = "Error: ";
}

echo json_encode($response);

?>