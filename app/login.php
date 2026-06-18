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
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        exit(0);
}

require("../includes/config.php");
$data = file_get_contents("php://input");

if (isset($data)) {
    $request = json_decode($data);
    $username = $request->username;
    $password = $request->password;
}

    $username= mysqli_real_escape_string($link,$username);
    $password = mysqli_real_escape_string($link,$password);

    $username = stripslashes($username);
    $password = stripslashes($password);

    //bandera
    $count = 0;

    $sql = mysqli_query($link,"SELECT * FROM usuario WHERE email_u = '$username'");
    $row = mysqli_fetch_array($sql);
    if(is_numeric($row['id_usuario']) AND $row['id_usuario'] > 0) 
    {
        $passactual = $row['password_encrip'];
        $passenviada = $password;
        if(password_verify($passenviada,$passactual))
        {
            $id = $row['id_usuario'];
            $nombre = $row['nombre_u'];
            $apellido = $row['apellido_u'];
            $count = 1;
        }
    }

    $response = array();
    // If result matched myusername and mypassword, table row must be 1 row                    
    if($count == 1) {
        array_push($response, array("id"=>$id,
                                    "nombre"=>$nombre,
                                    "apellido"=>$apellido,
                                    "correo"=>$username));
    } else {
        $response = "";         
    }

    echo json_encode($response);
?>