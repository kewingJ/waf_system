<?php
    session_start();
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once 'command_security.php';

    waf_require_admin_session('Error al ejecutar el comando.');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo "Error al ejecutar el comando.";
        exit;
    }

    $output = array();
    $return_var = 0;
    waf_reload_nginx($output, $return_var);

    // Verificar si se ejecutó correctamente
    if ($return_var === 0) {
        echo "El comando se ejecutó correctamente.";
    } else {
        echo "Error al ejecutar el comando.";
    }
?>
