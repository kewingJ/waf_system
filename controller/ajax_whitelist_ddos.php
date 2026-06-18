<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../includes/config.php';
include_once '../includes/security.php';
include_once 'command_security.php';

waf_require_admin_session();

$file = '../config/ignore.ip.list';

// Obtener todas las IPs de la base de datos con su estado
$consult = mysqli_query($link, "SELECT ip_whitelist_ddos, activo FROM whitelist_ddos");
$ips_db_activas = [];
$ips_db_inactivas = [];

while ($row = mysqli_fetch_assoc($consult)) {
    if ($row['activo'] == 1) {
        $ips_db_activas[] = trim($row['ip_whitelist_ddos']);
    } else {
        $ips_db_inactivas[] = trim($row['ip_whitelist_ddos']);
    }
}

// Leer las IPs ya registradas en el archivo
$ips_file = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

// Determinar las nuevas IPs que no están en el archivo
$nuevas_ips = array_diff($ips_db_activas, $ips_file);

// Determinar las IPs que deben eliminarse del archivo
$ips_a_eliminar = array_intersect($ips_db_inactivas, $ips_file);

// Si hay IPs para eliminar, reescribir el archivo sin ellas
if (!empty($ips_a_eliminar)) {
    $ips_file = array_diff($ips_file, $ips_a_eliminar);
    file_put_contents($file, implode("\n", $ips_file) . "\n", LOCK_EX);
}

// Si hay nuevas IPs para añadir, escribirlas sin sobrescribir las existentes
if (!empty($nuevas_ips)) {
    file_put_contents($file, implode("\n", $nuevas_ips) . "\n", FILE_APPEND | LOCK_EX);
}

$output = array();
$return_var = 0;
$copied = waf_run_command(
    array('/usr/bin/sudo', '-n', '/usr/bin/cp', '-f', '/var/www/reportwui/config/ignore.ip.list', '/etc/ddos/'),
    $output,
    $return_var,
    30
);

if (!$copied) {
    error_log('ajax_whitelist_ddos.php: fallo al copiar ignore.ip.list (codigo '.$return_var.'): '.implode(' | ', $output));
}

?>
