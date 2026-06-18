<?php
    session_start();
    if (!empty($_POST['id_host']) && is_array($_POST['id_host'])) {
        include_once '../includes/config.php';
        include_once '../includes/security.php';

        $data = $_POST['id_host'];

        $dominiosUnicos = [];
        foreach ($data as $dominio) {
            $nombre_host = trim(clean($dominio));
            if ($nombre_host === '') {
                continue;
            }

            $dominiosUnicos[$nombre_host] = "'" . mysqli_real_escape_string($link, $nombre_host) . "'";
        }

        if (empty($dominiosUnicos)) {
            echo "mal";
            exit;
        }

        $fecha_r = date('Y-m-d');
        $dominiosSql = array_values($dominiosUnicos);
        $dominiosLista = implode(", ", $dominiosSql);
        $insertAuditoria = [];

        foreach (array_keys($dominiosUnicos) as $nombre_host) {
            $nombreHostSql = mysqli_real_escape_string($link, $nombre_host);
            $insertAuditoria[] = "(0, '$nombreHostSql', '$fecha_r')";
        }

        mysqli_begin_transaction($link);

        $guardarHistorico = mysqli_query(
            $link,
            "INSERT INTO host_visita_borrados (id_host_visita_borrados, nombre_host, fecha_r)
             VALUES " . implode(", ", $insertAuditoria)
        );

        $deleteGrafica = mysqli_query($link, "DELETE FROM grafica_visitas WHERE dominio IN ($dominiosLista)");
        $deleteVisitaDominioGroup = mysqli_query($link, "DELETE FROM visita_dominio_group WHERE dominio IN ($dominiosLista)");

        if (
            $guardarHistorico === false ||
            $deleteGrafica === false ||
            $deleteVisitaDominioGroup === false
        ) {
            mysqli_rollback($link);
            echo "mal";
            exit;
        }

        mysqli_commit($link);

        echo "bien";
    } else {
        echo "mal";
        exit;
    }
?>
