<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';

    $consultGrafica = mysqli_query($link, "SELECT MAX(fecha_visita) AS ultima_fecha FROM grafica_visitas_dominio");
    $rowGrafica = mysqli_fetch_assoc($consultGrafica);
    $ultima_fecha = !empty($rowGrafica['ultima_fecha'])
        ? date('Y-m-d', strtotime($rowGrafica['ultima_fecha']))
        : null;

    // Recalcular desde la ultima fecha generada evita datos duplicados y captura visitas tardias.
    $fecha_inicio = !empty($ultima_fecha) ? $ultima_fecha : '1970-01-01';

    mysqli_begin_transaction($link);

    $deleteGrafica = mysqli_query(
        $link,
        "DELETE FROM grafica_visitas_dominio
         WHERE fecha_visita >= '{$fecha_inicio} 00:00:00'"
    );

    $insertGrafica = mysqli_query(
        $link,
        "INSERT INTO grafica_visitas_dominio (fecha_visita, total_visita)
         SELECT
            v.fecha_visita_date AS fecha_visita,
            COUNT(*) AS total_visita
         FROM visita_dominio v
         WHERE v.activo_visita = 1
           AND v.fecha_visita_date >= '{$fecha_inicio}'
         GROUP BY v.fecha_visita_date
         ORDER BY v.fecha_visita_date"
    );

    if ($deleteGrafica === false || $insertGrafica === false) {
        mysqli_rollback($link);
    } else {
        mysqli_commit($link);
    }
?>
