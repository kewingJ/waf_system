<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';

    $cronLock = waf_acquire_cron_lock('ajax_estadistica_pais');

    // ---------------------------------------------------------------
    // PASO 1: Cargar paises en RAM (tabla pequeña, 240 filas)
    // ---------------------------------------------------------------
    $paises = array();
    $consult = mysqli_query($link, "SELECT id_pais, iso, nombre FROM paises");
    while ($rows = mysqli_fetch_array($consult)) {
        $paises[$rows['iso']] = array(
            'id_pais' => $rows['id_pais'],
            'nombre'  => $rows['nombre'],
        );
    }

    // ---------------------------------------------------------------
    // PASO 2: Solo procesar bloqueos cuya fecha_bloqueo NO está aún
    // en bloqueo_pais_rango (evita re-escanear millones de filas viejas)
    // ---------------------------------------------------------------
    $consultNuevos = mysqli_query(
        $link,
        "SELECT b.idN, b.ip, b.fecha_bloqueo
         FROM bloqueo b
         LEFT JOIN bloqueo_pais_rango bpr
               ON bpr.fecha_bloqueo_pais = b.fecha_bloqueo
         WHERE bpr.fecha_bloqueo_pais IS NULL
           AND b.ip <> ''
         ORDER BY b.fecha_bloqueo ASC"
    );

    while ($bloqueo = mysqli_fetch_array($consultNuevos)) {
        $ipAtaque    = $bloqueo['ip'];
        $fechaAtaque = $bloqueo['fecha_bloqueo'];
        $idN         = (int) $bloqueo['idN'];

        $codigoIp = getCountryFromIP($ipAtaque, 'code');
        if (!isset($paises[$codigoIp])) {
            continue;
        }

        $codigoIp3  = getCountryFromIP($ipAtaque, 'AbBr');
        $idPais     = (int) $paises[$codigoIp]['id_pais'];
        $nombrePais = mysqli_real_escape_string($link, $paises[$codigoIp]['nombre']);
        $isoSql     = mysqli_real_escape_string($link, $codigoIp);
        $iso3Sql    = mysqli_real_escape_string($link, $codigoIp3);
        $fechaSql   = mysqli_real_escape_string($link, $fechaAtaque);

        mysqli_query(
            $link,
            "INSERT INTO bloqueo_pais_rango
                 (id_pais, nombre_pais, iso, iso3, idN, fecha_bloqueo_pais)
             VALUES
                 ('$idPais', '$nombrePais', '$isoSql', '$iso3Sql', '$idN', '$fechaSql')"
        ) or die(mysqli_error($link));
    }

    // ---------------------------------------------------------------
    // PASO 3: Recalcular totales en bloqueo_pais usando SQL puro
    // (evita cargar todas las filas en PHP para contar)
    // ---------------------------------------------------------------
    mysqli_query(
        $link,
        "UPDATE bloqueo_pais bp
         JOIN (
             SELECT id_pais, COUNT(*) AS total, MAX(iso3) AS iso3
             FROM bloqueo_pais_rango
             GROUP BY id_pais
         ) agg ON bp.id_pais = agg.id_pais
         SET bp.total_bloqueo = agg.total,
             bp.iso3          = agg.iso3"
    ) or die(mysqli_error($link));

    // ---------------------------------------------------------------
    // PASO 4: Registrar hosts nuevos en la tabla host
    // ---------------------------------------------------------------
    mysqli_query(
        $link,
        "INSERT INTO host (nombre_host, fecha_r_host)
         SELECT DISTINCT b.server, CURDATE()
         FROM bloqueo b
         LEFT JOIN host h ON h.nombre_host = b.server
         WHERE b.server <> '' AND h.id_host IS NULL"
    ) or die(mysqli_error($link));
?>
