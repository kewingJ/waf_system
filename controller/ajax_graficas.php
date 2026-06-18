<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';

    $cronLock = waf_acquire_cron_lock('ajax_graficas');

    mysqli_query($link, "TRUNCATE grafica_bloqueo_ip") or die(mysqli_error($link));
    mysqli_query($link, "TRUNCATE grafica_bloqueo") or die(mysqli_error($link));
    mysqli_query($link, "TRUNCATE grafica_principal") or die(mysqli_error($link));

    mysqli_query(
        $link,
        "INSERT INTO grafica_bloqueo_ip (fecha_bloqueo_ip, total_bloqueo_ip)
         SELECT fecha_bloqueo_ip2, COUNT(*)
         FROM bloqueo_ip
         GROUP BY fecha_bloqueo_ip2"
    ) or die(mysqli_error($link));

    mysqli_query(
        $link,
        "INSERT INTO grafica_bloqueo (fecha_bloqueo, total_bloqueo)
         SELECT DATE(fecha_bloqueo), COUNT(*)
         FROM bloqueo
         GROUP BY DATE(fecha_bloqueo)"
    ) or die(mysqli_error($link));

    mysqli_query(
        $link,
        "INSERT INTO grafica_principal (fecha_bloqueo, total_waf, total_fuerza)
         SELECT fechas.fecha_bloqueo,
                COALESCE(waf.total_waf, 0),
                COALESCE(fuerza.total_fuerza, 0)
         FROM (
             SELECT DATE(fecha_bloqueo) AS fecha_bloqueo FROM bloqueo
             UNION
             SELECT fecha_bloqueo_ip2 AS fecha_bloqueo FROM bloqueo_ip
         ) fechas
         LEFT JOIN (
             SELECT DATE(fecha_bloqueo) AS fecha_bloqueo, COUNT(*) AS total_waf
             FROM bloqueo
             GROUP BY DATE(fecha_bloqueo)
         ) waf ON waf.fecha_bloqueo = fechas.fecha_bloqueo
         LEFT JOIN (
             SELECT fecha_bloqueo_ip2 AS fecha_bloqueo, COUNT(*) AS total_fuerza
             FROM bloqueo_ip
             GROUP BY fecha_bloqueo_ip2
         ) fuerza ON fuerza.fecha_bloqueo = fechas.fecha_bloqueo
         ORDER BY fechas.fecha_bloqueo ASC"
    ) or die(mysqli_error($link));
?>
