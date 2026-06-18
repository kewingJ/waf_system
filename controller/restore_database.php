<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    if(!empty($_POST['id_respaldo']))
	{
        // Configuración de la base de datos
        $dbHost     = $sql_details['host'];
        $dbUsername = $sql_details['user'];
        $dbPassword = $sql_details['pass'];
        $dbName     = $sql_details['db'];

        $id_respaldo = $_POST['id_respaldo'];
        $consult = mysqli_query($link,"SELECT * FROM respaldos WHERE id_respaldo = '$id_respaldo'");
        $row = mysqli_fetch_array($consult);
        $ruta_archivo = $row['ruta_respaldo'];

        // Leer el contenido del archivo de respaldo
        $backupContent = file_get_contents($ruta_archivo);

        // Conectar a la base de datos
        $mysqli = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

        if ($mysqli->connect_error) {
            die("Error: no se pudo conectar a la base de datos: " . $mysqli->connect_error);
        }

        // Desactivar la verificación de claves foráneas para permitir la importación
        $mysqli->query("SET FOREIGN_KEY_CHECKS = 0");

        // Ejecutar las consultas del archivo de respaldo
        $queries = explode(";\n", $backupContent);
        foreach ($queries as $query) {
            if (!empty(trim($query))) {
                $mysqli->query($query);
            }
        }

        // Reactivar la verificación de claves foráneas
        $mysqli->query("SET FOREIGN_KEY_CHECKS = 1");

        echo "bien";
    } else {
        echo "mal";
    }
?>
