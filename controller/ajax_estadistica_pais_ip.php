<?php
    $baseDir = dirname(__DIR__);

    include_once $baseDir . '/includes/config.php';
    include_once $baseDir . '/includes/security.php';
    include_once $baseDir . '/geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';

    $cronLock = waf_acquire_cron_lock('ajax_estadistica_pais_ip');

    /**
     * OPTIMIZACIÓN: Se elimina la resolución GeoIP en PHP y el uso de cláusulas IN masivas.
     * Ahora usamos la agregación directa por la columna 'codigo_pais' ya poblada.
     */

    // 1. Obtener conteos de bloqueos agrupados por país directamente de la DB
    // Según db_waf.sql, la tabla bloqueo_ip no tiene columna 'activo_bloqueo_ip',
    // así que contamos todos los registros.
    $stats = [];
    $query_stats = mysqli_query($link, "
        SELECT codigo_pais, COUNT(*) as total
        FROM bloqueo_ip
        GROUP BY codigo_pais
    ");

    if ($query_stats) {
        while($row = mysqli_fetch_assoc($query_stats)) {
            $stats[$row['codigo_pais']] = $row['total'];
        }
    }

    // 2. Obtener lista de países y actualizar bloqueo_ip_pais
    $consult = mysqli_query($link, "SELECT id_pais, iso FROM paises");

    while ($rows = mysqli_fetch_array($consult)) {
        $iso2    = $rows['iso'];
        $id_pais = (int) $rows['id_pais'];
        $total   = isset($stats[$iso2]) ? (int)$stats[$iso2] : 0;

        // Mapear ISO a ISO3 usando el diccionario de la librería GeoIP
        $iso3 = isset($GLOBALS['geoipcntry'][$iso2]) ? $GLOBALS['geoipcntry'][$iso2] : $iso2;

        mysqli_query($link, "
            UPDATE bloqueo_ip_pais
            SET total_bloqueo_ip_pais = '$total', iso3 = '$iso3'
            WHERE id_pais = '$id_pais'
        ") or die(mysqli_error($link));
    }
?>
