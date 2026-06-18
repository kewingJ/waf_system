<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once __DIR__ . '/cron_helpers.php';

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    date_default_timezone_set('America/Managua');

    $cronLock = waf_acquire_cron_lock('ajax_grafica_rango');

    if (!function_exists('insertarRangoAgrupado')) {
        function insertarRangoAgrupado($link, $tablaDestino, $columnaFechaDestino, $columnaTotalDestino, $columnaRangoDestino, $tablaOrigen, $columnaFechaOrigen, $inicio, $fin, $rango, $whereExtra = '')
        {
            $inicio = mysqli_real_escape_string($link, $inicio);
            $fin = mysqli_real_escape_string($link, $fin);
            $rango = mysqli_real_escape_string($link, $rango);
            $whereExtraSql = trim($whereExtra);
            if ($whereExtraSql !== '') {
                $whereExtraSql = ' AND ' . $whereExtraSql;
            }

            $sql = "INSERT INTO $tablaDestino ($columnaFechaDestino, $columnaTotalDestino, $columnaRangoDestino)
                    SELECT DATE($columnaFechaOrigen) AS fecha, COUNT(*) AS total, '$rango' AS rango
                    FROM $tablaOrigen
                    WHERE $columnaFechaOrigen BETWEEN '$inicio' AND '$fin'
                    $whereExtraSql
                    GROUP BY DATE($columnaFechaOrigen)
                    ORDER BY fecha ASC";

            return mysqli_query($link, $sql) or die(mysqli_error($link));
        }
    }

    $actual_hoy = date("Y-m-d H:i:s");
    $pasado_hoy = date("Y-m-d") . ' 00:00:00';
    $pasado_semana = date("Y-m-d H:i:s", strtotime($actual_hoy . "- 1 week"));
    $pasado_mes = date("Y-m-d H:i:s", strtotime($actual_hoy . "- 1 month"));

    mysqli_query($link, "TRUNCATE grafica_bloqueo_rango") or die(mysqli_error($link));
    mysqli_query($link, "TRUNCATE grafica_bloqueo_rango_ip") or die(mysqli_error($link));
    mysqli_query($link, "TRUNCATE grafica_bloqueo_bots") or die(mysqli_error($link));

    $rangos = array(
        array('HOY', $pasado_hoy, $actual_hoy),
        array('SEMANA', $pasado_semana, $actual_hoy),
        array('MES', $pasado_mes, $actual_hoy),
    );

    foreach ($rangos as $rangoData) {
        list($rango, $inicio, $fin) = $rangoData;

        insertarRangoAgrupado(
            $link,
            'grafica_bloqueo_rango_ip',
            'fecha_bloqueo_rango_ip',
            'total_bloqueo_rango_ip',
            'rango_bloqueo_ip',
            'bloqueo_ip',
            'fecha_bloqueo_ip',
            $inicio,
            $fin,
            $rango
        );

        insertarRangoAgrupado(
            $link,
            'grafica_bloqueo_rango',
            'fecha_bloqueo_rango',
            'total_bloqueo_rango',
            'rango_bloqueo',
            'bloqueo',
            'fecha_bloqueo',
            $inicio,
            $fin,
            $rango,
            'activo_bloqueo = 1'
        );
    }

    // grafica_bloqueo_bots: la tabla solo tiene (fecha_bloqueo_bot, total_bloqueo_bot),
    // sin columna de rango. Se inserta el periodo MES completo agrupado por dia;
    // las queries de home.php ya filtran por rango de fechas directamente.
    $inicioMes = mysqli_real_escape_string($link, $pasado_mes);
    $finMes    = mysqli_real_escape_string($link, $actual_hoy);
    mysqli_query(
        $link,
        "INSERT INTO grafica_bloqueo_bots (fecha_bloqueo_bot, total_bloqueo_bot)
         SELECT DATE(fecha_bloqueo_ip) AS fecha_bot, COUNT(*) AS total_bot
         FROM bloqueo_ip
         WHERE fecha_bloqueo_ip BETWEEN '$inicioMes' AND '$finMes'
           AND tipo_ataque_ip IN ('nginx-botsearch', 'nginx-badbots')
         GROUP BY DATE(fecha_bloqueo_ip)
         ORDER BY fecha_bot ASC"
    ) or die(mysqli_error($link));
?>
