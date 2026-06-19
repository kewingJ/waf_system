<?php
/**
 * migrate_geoip_data_optimized.php
 * Script optimizado para poblar la columna 'codigo_pais' en datos existentes.
 * Diseñado para procesar millones de registros de forma eficiente.
 */

require_once 'includes/config.php';
require_once 'geoIp/geoiploc.php';

// Aumentar recursos
ini_set('memory_limit', '1024M');
set_time_limit(0);

// Configuración
$batchSize = 25000; // Lote equilibrado
$sleepTime = 0;     // Sin pausa para máxima velocidad, MariaDB manejará la carga

echo "--- Iniciando migración GeoIP ultra-rápida ---" . PHP_EOL;

// Optimización: Pre-cargar y convertir base de datos GeoIP a floats (para evitar overflow de 32 bits)
echo "Cargando base de datos GeoIP en memoria... ";
$geoip_db = [];
global $geoipaddrfrom, $geoipaddrupto, $geoipctry, $geoipcount;
for ($i = 0; $i < $geoipcount; $i++) {
    $geoip_db[] = [
        'from' => (float)$geoipaddrfrom[$i],
        'to'   => (float)$geoipaddrupto[$i],
        'code' => $geoipctry[$i]
    ];
}
echo "OK (" . count($geoip_db) . " rangos)" . PHP_EOL;

/**
 * Búsqueda binaria optimizada usando comparaciones numéricas directas
 */
function fastGetCountry($ip, &$db) {
    $ip_long = (float)sprintf("%u", ip2long($ip));
    if (!$ip_long) return 'ZZ';

    $low = 0;
    $high = count($db) - 1;

    while ($low <= $high) {
        $mid = ($low + $high) >> 1;
        if ($db[$mid]['from'] <= $ip_long && $db[$mid]['to'] >= $ip_long) {
            return $db[$mid]['code'];
        }
        if ($db[$mid]['from'] > $ip_long) {
            $high = $mid - 1;
        } else {
            $low = $mid + 1;
        }
    }
    return 'ZZ';
}

function migrateTableOptimized($link, $tableName, $idCol, $ipCol, $batchSize, $sleepTime, &$geoip_db) {
    echo "Procesando tabla: $tableName..." . PHP_EOL;

    // Obtener total aproximado para progreso
    $res = mysqli_query($link, "SELECT COUNT(*) FROM $tableName WHERE codigo_pais = 'ZZ'");
    $totalPending = mysqli_fetch_row($res)[0];

    if ($totalPending == 0) {
        echo "No hay registros pendientes en $tableName." . PHP_EOL;
        return;
    }

    echo "Registros pendientes: " . number_format($totalPending, 0, ',', '.') . PHP_EOL;

    $updatedGlobal = 0;
    $startTime = time();
    $ip_memory_cache = []; // Cache en memoria para IPs repetidas

    while (true) {
        // Seleccionamos por ID para que sea instantáneo
        $query = "SELECT $idCol, $ipCol FROM $tableName WHERE codigo_pais = 'ZZ' LIMIT $batchSize";
        $result = mysqli_query($link, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            break;
        }

        $updates = [];
        $batchCount = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row[$idCol];
            $ip = $row[$ipCol];

            if (!isset($ip_memory_cache[$ip])) {
                $ip_memory_cache[$ip] = fastGetCountry($ip, $geoip_db);
                // Evitamos que la caché crezca infinito
                if (count($ip_memory_cache) > 100000) {
                    $ip_memory_cache = [];
                }
            }

            $code = $ip_memory_cache[$ip];
            $updates[$code][] = $id;
            $batchCount++;
        }

        // Updates agrupados por código de país (mucho más rápido que uno por uno)
        foreach ($updates as $code => $ids) {
            $idList = implode(',', $ids);
            mysqli_query($link, "UPDATE $tableName SET codigo_pais = '$code' WHERE $idCol IN ($idList)");
        }

        $updatedGlobal += $batchCount;
        $elapsed = time() - $startTime;
        $speed = $elapsed > 0 ? $updatedGlobal / $elapsed : 0;
        $eta = $speed > 0 ? ($totalPending - $updatedGlobal) / $speed : 0;

        printf("\rProgreso: %s/%s (%.2f%%) - Vel: %.0f r/s - ETA: %s    ",
            number_format($updatedGlobal, 0, ',', '.'),
            number_format($totalPending, 0, ',', '.'),
            ($updatedGlobal / $totalPending) * 100,
            $speed,
            $eta > 86400 ? floor($eta/86400)."d ".gmdate("H:i:s", $eta%86400) : gmdate("H:i:s", $eta)
        );

        if ($sleepTime > 0) usleep($sleepTime);
    }
    echo PHP_EOL . "Finalizado para $tableName." . PHP_EOL;
}

// Migrar tablas
migrateTableOptimized($link, 'visita_dominio', 'id_visita', 'ip_visita', $batchSize, $sleepTime, $geoip_db);
migrateTableOptimized($link, 'bloqueo_ip', 'id_bloqueo_ip', 'ip_bloqueada', $batchSize, $sleepTime, $geoip_db);

echo "--- Proceso completado con éxito ---" . PHP_EOL;
?>
