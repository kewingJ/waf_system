<?php
    $baseDir = dirname(__DIR__);

    include_once $baseDir . '/includes/config.php';
    include_once $baseDir . '/includes/security.php';
    include_once $baseDir . '/geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';

    $cronLock = waf_acquire_cron_lock('ajax_estadistica_pais_ip');

    // ---------------------------------------------------------------
    // PASO 1: Cargar paises en RAM (tabla pequeña, 240 filas)
    // ---------------------------------------------------------------
    $paises = array();
    $consult = mysqli_query($link, "SELECT id_pais, iso FROM paises");
    while ($rows = mysqli_fetch_array($consult)) {
        $paises[$rows['iso']] = array(
            'id_pais' => (int) $rows['id_pais'],
            'total'   => 0,
            'iso3'    => '',
        );
    }

    // ---------------------------------------------------------------
    // PASO 2: Obtener SOLO IPs únicas de bloqueo_ip
    // (evita hacer GeoIP millones de veces para IPs repetidas)
    // ---------------------------------------------------------------
    $ipCountryMap = array(); // ip => ['code' => 'US', 'iso3' => 'USA']

    $consultIPs = mysqli_query($link, "SELECT DISTINCT ip_bloqueada FROM bloqueo_ip WHERE ip_bloqueada <> ''");
    while ($row = mysqli_fetch_array($consultIPs)) {
        $ip = $row['ip_bloqueada'];
        $code = getCountryFromIP($ip, 'code');
        $iso3 = getCountryFromIP($ip, 'AbBr');
        $ipCountryMap[$ip] = array('code' => $code, 'iso3' => $iso3);
    }

    // ---------------------------------------------------------------
    // PASO 3: Contar bloqueos por país usando SQL GROUP BY
    // (no cargamos todas las filas en PHP)
    // ---------------------------------------------------------------
    // Construimos un mapa iso => lista de IPs para poder filtrar en SQL
    $countryIpMap = array(); // iso_code => [ip1, ip2, ...]
    foreach ($ipCountryMap as $ip => $info) {
        $code = $info['code'];
        if (!isset($paises[$code])) {
            continue;
        }
        $countryIpMap[$code][] = $ip;
        if (!empty($info['iso3'])) {
            $paises[$code]['iso3'] = $info['iso3'];
        }
    }

    // Para cada país que tiene IPs registradas, contar en SQL
    foreach ($countryIpMap as $isoCode => $ips) {
        if (!isset($paises[$isoCode])) {
            continue;
        }
        // Escapar cada IP y construir el IN (...)
        $escapedIps = array_map(function($ip) use ($link) {
            return "'" . mysqli_real_escape_string($link, trim($ip)) . "'";
        }, $ips);
        $inClause = implode(',', $escapedIps);

        $countResult = mysqli_query(
            $link,
            "SELECT COUNT(*) AS total FROM bloqueo_ip WHERE ip_bloqueada IN ($inClause)"
        );
        if ($countResult) {
            $countRow = mysqli_fetch_array($countResult);
            $paises[$isoCode]['total'] = (int) $countRow['total'];
        }
    }

    // ---------------------------------------------------------------
    // PASO 4: Actualizar bloqueo_ip_pais con los totales calculados
    // ---------------------------------------------------------------
    foreach ($paises as $isoCode => $pais) {
        $idPais       = (int) $pais['id_pais'];
        $totalAtaques = (int) $pais['total'];
        $iso3         = mysqli_real_escape_string($link, $pais['iso3']);

        mysqli_query(
            $link,
            "UPDATE bloqueo_ip_pais
             SET total_bloqueo_ip_pais = '$totalAtaques', iso3 = '$iso3'
             WHERE id_pais = '$idPais'"
        ) or die(mysqli_error($link));
    }
?>
