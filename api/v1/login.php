<?php
require("../../includes/config.php");

// Getting the received JSON into $json variable.
$json = file_get_contents('php://input');

// Decoding the received JSON and store into $obj variable.
$obj = json_decode($json, true);

// Getting User email from JSON $obj array and store into $email.
$email = $obj['email'];
 
// Getting Password from JSON $obj array and store into $password.
$password = $obj['password'];

//Applying User Login query with email and password.
$loginQuery = "SELECT * FROM usuario WHERE email_u = '$email'";
 
    // Executing SQL Query.
    $row = mysqli_fetch_array(mysqli_query($link, $loginQuery));
    if(is_numeric($row['id_usuario']) AND $row['id_usuario'] > 0) 
    {
        $passactual = $row['password_encrip'];
        $passenviada = $password;
        if(password_verify($passenviada, $passactual))
        {
            $id = $row['id_usuario'];
            $tipo_usuario = $row['tipo_usuario'];
            $nombre = $row['nombre_u'];
            $apellido = $row['apellido_u'];
            
            // Successfully Login Message.
            $onLoginSuccess = [
                "id_usuario"  =>  $id,
                "tipo_usuario"  =>  $tipo_usuario
            ];
         
            // Converting the message into JSON format.
            $SuccessMSG = json_encode($onLoginSuccess);
         
            // Echo the message.
            echo $SuccessMSG; 
        } else {
            // If Email and Password did not Matched.
            $InvalidMSG = 'Invalid Email or Password Please Try Again' ;
         
            // Converting the message into JSON format.
            $InvalidMSGJSon = json_encode($InvalidMSG);
         
            // Echo the message.
            echo $InvalidMSGJSon ;
        }
    } else {
        // If Email and Password did not Matched.
            $InvalidMSG = 'Invalid Email or Password Please Try Again' ;
         
            // Converting the message into JSON format.
            $InvalidMSGJSon = json_encode($InvalidMSG);
         
            // Echo the message.
            echo $InvalidMSGJSon ;
    }
?>