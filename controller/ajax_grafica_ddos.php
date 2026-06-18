<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    include_once __DIR__ . '/cron_helpers.php';

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $cronLock = waf_acquire_cron_lock('ajax_grafica_ddos');

    $paises = array();
    $consult = mysqli_query($link, "SELECT id_pais, iso, nombre FROM paises");
    while ($rows = mysqli_fetch_array($consult)) {
        $paises[$rows['iso']] = array(
            'id_pais' => $rows['id_pais'],
            'nombre' => $rows['nombre'],
            'total' => 0,
            'iso3' => '',
        );
    }

    $fechasRegistradas = array();
    $consultFechas = mysqli_query($link, "SELECT fecha_bloqueo_pais FROM bloqueo_ddos_pais_rango");
    while ($rowFecha = mysqli_fetch_array($consultFechas)) {
        $fechasRegistradas[$rowFecha['fecha_bloqueo_pais']] = true;
    }

    $consultDdos = mysqli_query($link, "SELECT ip_ddos, codigo_pais, fecha_ddos FROM bloqueo_ddos");
    while ($rowDdos = mysqli_fetch_array($consultDdos)) {
        $codigoPais = $rowDdos['codigo_pais'];
        if ($codigoPais === '') {
            $codigoPais = getCountryFromIP($rowDdos['ip_ddos'], "code");
        }

        if (!isset($paises[$codigoPais])) {
            continue;
        }

        $fechaAtaque = $rowDdos['fecha_ddos'];
        $codigoIp3 = getCountryFromIP($rowDdos['ip_ddos'], "AbBr");
        $paises[$codigoPais]['total']++;
        $paises[$codigoPais]['iso3'] = $codigoIp3;

        if (!isset($fechasRegistradas[$fechaAtaque])) {
            $idPais = (int) $paises[$codigoPais]['id_pais'];
            $nombrePais = mysqli_real_escape_string($link, $paises[$codigoPais]['nombre']);
            $codigoPaisSql = mysqli_real_escape_string($link, $codigoPais);
            $codigoIp3Sql = mysqli_real_escape_string($link, $codigoIp3);
            $fechaAtaqueSql = mysqli_real_escape_string($link, $fechaAtaque);

            mysqli_query(
                $link,
                "INSERT INTO bloqueo_ddos_pais_rango
                    (id_pais, nombre_pais, iso, iso3, fecha_bloqueo_pais)
                 VALUES
                    ('$idPais', '$nombrePais', '$codigoPaisSql', '$codigoIp3Sql', '$fechaAtaqueSql')"
            ) or die(mysqli_error($link));

            $fechasRegistradas[$fechaAtaque] = true;
        }
    }

    foreach ($paises as $pais) {
        $idPais = (int) $pais['id_pais'];
        $totalAtaques = (int) $pais['total'];
        $iso3 = mysqli_real_escape_string($link, $pais['iso3']);

        mysqli_query(
            $link,
            "UPDATE bloqueo_ddos_pais
             SET total_bloqueo_ddos_pais = '$totalAtaques', iso3 = '$iso3'
             WHERE id_bloqueo_ddos_pais = '$idPais'"
        ) or die(mysqli_error($link));
    }
?>
