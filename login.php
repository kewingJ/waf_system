<?php
if(!empty($_POST['email']) && !empty($_POST['password'])) 
{
    require("includes/config.php");
	require("includes/security.php");
	/*guardo los datos del usuario y los limpio de cualquier caracter*/
	$email = mysqli_real_escape_string($link,$_POST['email']);
	$pass = mysqli_real_escape_string($link,$_POST['password']);
    /*verificamos en la base de datos si existe el usuario*/
    $consulta = mysqli_query($link, "SELECT * FROM usuario WHERE email_u='{$email}'");
    $row = mysqli_fetch_array($consulta);
    if(is_numeric($row['id_usuario']) AND $row['id_usuario']>0) 
    {
        $passactual = $row['password_encrip'];
        $passenviada = $pass;
        if(password_verify($passenviada,$passactual))
        {
            session_start();
            $id_us = $_SESSION['id_u'] = $row['id_usuario'];
            $_SESSION['nombre'] = $row['nombre_u'];
            $_SESSION['apellido'] = $row['apellido_u'];
            $_SESSION['activo'] = $row['activo_u'];
            $_SESSION['tipo_usuario'] = $row['tipo_usuario'];
            //defino la sesión que demuestra que el usuario está autorizado
            $_SESSION["ultimoAcceso"]= date("Y-n-j H:i:s");

            $tipo = $row['tipo_usuario'];
            switch ($tipo) {
                case 1:
                    header("Location: home.php");
                break;
                case 2:
                    header("Location: inicio.php");
                    //header("Location: index.php");
                break;
            }
            
        }else{
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }
}else {
    header("Location: index.php");
}
?>