<?php
/**
 * migrate_geoip_data_optimized.php
 * Script optimizado con PUNTOS DE CONTROL (Checkpoints).
 * Diseñado para procesar millones de registros y poder reanudarse tras un cierre.
 */

require_once 'includes/config.php';
require_once 'geoIp/geoiploc.php';

// Aumentar recursos
ini_set('memory_limit', '1024M');
set_time_limit(0);

// Configuración
$batchSize = 25000;
$sleepTime = 0;
$checkpointDir = dirname(__DIR__) . '/.cron_state';
if (!is_dir($checkpointDir)) @mkdir($checkpointDir, 0775, true);

echo "--- Iniciando migración GeoIP con Checkpoints ---" . PHP_EOL;

// Cargar GeoIP en memoria
echo "Cargando base de datos GeoIP... ";
$geoip_db = [];
global $geoipaddrfrom, $geoipaddrupto, $geoipctry, $geoipcount;
for ($i = 0; $i < $geoipcount; $i++) {
    $geoip_db[] = [
        'from' => (float)$geoipaddrfrom[$i],
        'to'   => (float)$geoipaddrupto[$i],
        'code' => $geoipctry[$i]
    ];
}
echo "OK" . PHP_EOL;

function fastGetCountry($ip, &$db) {
    $ip_long = (float)sprintf("%u", ip2long($ip));
    if (!$ip_long) return 'ZZ';
    $low = 0; $high = count($db) - 1;
    while ($low <= $high) {
        $mid = ($low + $high) >> 1;
        if ($db[$mid]['from'] <= $ip_long && $db[$mid]['to'] >= $ip_long) return $db[$mid]['code'];
        if ($db[$mid]['from'] > $ip_long) $high = $mid - 1; else $low = $mid + 1;
    }
    return 'ZZ';
}

function migrateWithCheckpoints($link, $tableName, $idCol, $ipCol, $batchSize, &$geoip_db, $checkpointDir) {
    echo "Procesando tabla: $tableName..." . PHP_EOL;

    // Cargar punto de control
    $stateFile = $checkpointDir . "/migrate_geoip_{$tableName}.json";
    $lastProcessedId = 0;
    if (file_exists($stateFile)) {
        $state = json_decode(file_get_contents($stateFile), true);
        $lastProcessedId = (int)($state['last_id'] ?? 0);
        echo "Reanudando desde el ID: " . number_format($lastProcessedId, 0, ',', '.') . PHP_EOL;
    }

    // Obtener total para progreso real
    $resTotal = mysqli_query($link, "SELECT COUNT(*) FROM $tableName");
    $grandTotal = mysqli_fetch_row($resTotal)[0];

    // Obtener registros restantes reales (los que son 'ZZ')
    $resPending = mysqli_query($link, "SELECT COUNT(*) FROM $tableName WHERE codigo_pais = 'ZZ' AND $idCol > $lastProcessedId");
    $totalToProcess = mysqli_fetch_row($resPending)[0];

    if ($totalToProcess == 0) {
        echo "No hay registros pendientes por procesar en $tableName." . PHP_EOL;
        return;
    }

    $processedInSession = 0;
    $startTime = time();
    $ip_memory_cache = [];

    while (true) {
        // Buscamos los siguientes 'ZZ' partiendo del último ID procesado (Índice PK = Velocidad luz)
        $query = "SELECT $idCol, $ipCol FROM $tableName WHERE $idCol > $lastProcessedId AND codigo_pais = 'ZZ' LIMIT $batchSize";
        $result = mysqli_query($link, $query);

        if (!$result || mysqli_num_rows($result) === 0) break;

        $updates = [];
        $currentMaxId = $lastProcessedId;

        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row[$idCol];
            $ip = $row[$ipCol];
            if ($id > $currentMaxId) $currentMaxId = $id;

            if (!isset($ip_memory_cache[$ip])) {
                $ip_memory_cache[$ip] = fastGetCountry($ip, $geoip_db);
                if (count($ip_memory_cache) > 100000) $ip_memory_cache = [];
            }
            $updates[$ip_memory_cache[$ip]][] = $id;
        }

        foreach ($updates as $code => $ids) {
            $idList = implode(',', $ids);
            mysqli_query($link, "UPDATE $tableName SET codigo_pais = '$code' WHERE $idCol IN ($idList)");
        }

        $processedInSession += mysqli_num_rows($result);
        $lastProcessedId = $currentMaxId;

        // Guardar progreso cada lote
        file_put_contents($stateFile, json_encode(['last_id' => $lastProcessedId, 'updated_at' => date('Y-m-d H:i:s')]));

        $elapsed = time() - $startTime;
        $speed = $elapsed > 0 ? $processedInSession / $elapsed : 0;
        $eta = $speed > 0 ? ($totalToProcess - $processedInSession) / $speed : 0;

        printf("\rProgreso Sesión: %s/%s (%.2f%%) - Vel: %.0f r/s - ETA: %s    ",
            number_format($processedInSession, 0, ',', '.'),
            number_format($totalToProcess, 0, ',', '.'),
            ($processedInSession / $totalToProcess) * 100,
            $speed,
            $eta > 86400 ? floor($eta/86400)."d ".gmdate("H:i:s", $eta%86400) : gmdate("H:i:s", $eta)
        );
    }
    echo PHP_EOL . "Finalizado para $tableName." . PHP_EOL;
}

migrateWithCheckpoints($link, 'visita_dominio', 'id_visita', 'ip_visita', $batchSize, $geoip_db, $checkpointDir);
migrateWithCheckpoints($link, 'bloqueo_ip', 'id_bloqueo_ip', 'ip_bloqueada', $batchSize, $geoip_db, $checkpointDir);

echo "--- Proceso completado con éxito ---" . PHP_EOL;
?>
