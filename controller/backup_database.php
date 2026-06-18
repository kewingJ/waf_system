<?php
include_once '../includes/config.php';
include_once '../includes/security.php';

// Configuración de la base de datos
$dbHost     = $sql_details['host'];
$dbUsername = $sql_details['user'];
$dbPassword = $sql_details['pass'];
$dbName     = $sql_details['db'];

// Conectar a la base de datos
$mysqli = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($mysqli->connect_error) {
    die("Error: no se pudo conectar a la base de datos: " . $mysqli->connect_error);
}

// Consultar todas las tablas de la base de datos
$tables = [];
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

// Crear el contenido del archivo de respaldo
$backupContent = "";
foreach ($tables as $table) {
    // Obtener la estructura de la tabla
    $result = $mysqli->query("SHOW CREATE TABLE {$table}");
    $row = $result->fetch_assoc();
    $backupContent .= $row['Create Table'] . ";\n\n";

    // Obtener los datos de la tabla
    $result = $mysqli->query("SELECT * FROM {$table}");
    while ($row = $result->fetch_assoc()) {
        $values = array_map([$mysqli, 'real_escape_string'], array_values($row));
        $backupContent .= "INSERT INTO {$table} VALUES ('" . implode("', '", $values) . "');\n";
    }

    $backupContent .= "\n";
}

// Guardar el archivo en una carpeta especifica del proyecto
$backupFile = '../respaldo/'. $dbName . '_backup_' . date('Ymd_His') . '.sql';
$nombre = $dbName . '_backup_' . date('Ymd_His');
file_put_contents($backupFile, $backupContent);
//guardar en base de datos
$fecha_r      = date('Y-m-d');
$query = mysqli_query($link,"INSERT INTO respaldos VALUES (0,'sql','$nombre','$backupFile','$fecha_r',1)") or die(mysqli_error($link));

header("Location: ../configuracionextra.php");
?>