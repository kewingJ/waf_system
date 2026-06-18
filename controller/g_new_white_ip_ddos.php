<?php
session_start();
include_once '../includes/config.php';
include_once '../includes/security.php';
include_once '../geoIp/geoiploc.php';

$id_usuario = $_SESSION['id_u'];

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!empty($_POST['ip_new_ddos'])) {
    $file = '../config/ignore.ip.list';
    $ip = clean(mysqli_real_escape_string($link, $_POST['ip_new_ddos']));
    $fecha_r = date('Y-m-d');

    // Leer el archivo y verificar si la IP ya está en la lista
    if (file_exists($file)) {
        $ips_file = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (in_array($ip, $ips_file)) {
            echo "La IP ya está en la lista blanca.";
            exit;
        }
    }

    // Verificar si la IP ya existe en la base de datos
    $stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM whitelist_ddos WHERE ip_whitelist_ddos = ?");
    mysqli_stmt_bind_param($stmt, "s", $ip);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($count > 0) {
        echo "La IP ya está en la base de datos.";
        exit;
    }

    // Insertar en la base de datos
    $stmt = mysqli_prepare($link, "INSERT INTO whitelist_ddos (id_whitelist_ddos, ip_whitelist_ddos, activo, fecha_r) VALUES (0, ?, 1, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $ip, $fecha_r);
    $inserted = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // optener pais de la ip
    $codigoPais = getCountryFromIP($ip, "code");

    $query = mysqli_query($link,"INSERT INTO bloqueo_ddos VALUES (0, '$ip', '$codigoPais', '$fecha_r', 0, 1)") or die(mysqli_error($link));

    if ($inserted) {
        // Guardar en lista blanca
        include_once 'ajax_whitelist_ddos.php';
        echo "bien";
    } else {
        echo "Error al guardar la IP.";
    }
} else {
    echo "mal";
}
?>
