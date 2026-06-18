<?php
    session_start();
    if (!empty($_POST['id_host'])) {
        include_once '../includes/config.php';
        include_once '../includes/security.php';

        $data = $_POST['id_host'];

        // Crear una única transacción
        mysqli_autocommit($link, false);

        try {
            $eliminarServidores = [];
            $fecha_r = date('y-m-d');

            foreach ($data as $dominio) {
                $nombre_host = clean(mysqli_real_escape_string($link, $dominio));
                $eliminarServidores[] = $nombre_host;

                // Guardar en la tabla de servidores eliminados
                mysqli_query($link, "INSERT INTO host_borrados VALUES (0, '$nombre_host', '$fecha_r')");
            }

            // Eliminar de la tabla bloqueo en una sola consulta
            $eliminarServidoresStr = "'" . implode("', '", $eliminarServidores) . "'";
            mysqli_query($link, "DELETE FROM bloqueo WHERE server IN ($eliminarServidoresStr)");

            // Hacer commit si todas las consultas se ejecutan sin problemas
            mysqli_commit($link);
            echo "bien";
        } catch (Exception $e) {
            // Revertir cambios si hay algún error
            mysqli_rollback($link);
            echo "mal";
            exit;
        } finally {
            mysqli_autocommit($link, true);
        }
    } else {
        echo "mal";
        exit;
    }
?>
