<?php
$baseDir = dirname(__DIR__);

include_once $baseDir . '/includes/config.php';
include_once $baseDir . '/geoIp/geoiploc.php';
date_default_timezone_set('America/Managua');

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(600);

/** CONFIG **/
$folder = $baseDir . '/Ipthreat';
$files  = ['apache.txt', 'bots.txt']; // puedes añadir más aquí
$jsonFile = $folder . '/ips.json';
$txtFile  = $folder . '/ips.txt';

if (!$folder) {
    die("ERROR: No encuentro la carpeta Ipthreat\n");
}

// Filtrar solo los archivos legibles que existan
$files = array_values(array_filter($files, function ($f) use ($folder) {
    return is_readable($folder . DIRECTORY_SEPARATOR . $f);
}));

if (empty($files)) {
    die("ERROR: No hay archivos legibles en Ipthreat (apache.txt, bots.txt)\n");
}

/**
 * Precargar y contar apariciones de IPs en un archivo.
 * Devuelve un array asociativo con [IP => count]
 */
function preload_ip_counts(string $filepath): array {
    if (!is_readable($filepath)) return [];
    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) return [];
    $lines = array_map('trim', $lines);
    return array_count_values($lines);
}

// Precargar archivos de texto en memoria una sola vez
$file_ip_counts = [];
foreach ($files as $fname) {
    $path = $folder . DIRECTORY_SEPARATOR . $fname;
    $file_ip_counts[$fname] = preload_ip_counts($path);
}

// Saber si la tabla threat_ip_list tiene datos para saber desde donde arrancar
$queryBandera = mysqli_query($link,"SELECT * FROM threat_ip_list_master_date");
$condicion = mysqli_fetch_array($queryBandera);
$fecha = $condicion['fecha_master'];

// Lee IPs desde bloqueo_master
$sql = "SELECT id_bloqueo_referencia, ip_bloqueda, fecha_bloqueo, tipo_ataque
        FROM bloqueo_master
        WHERE ip_bloqueda IS NOT NULL 
        AND ip_bloqueda <> ''
        AND fecha_bloqueo > '$fecha'";
$rs = mysqli_query($link, $sql) or die("MySQL error: ".mysqli_error($link));

$total_ips = 0;
$total_inserts = 0;
$ips_export = [];

if (is_readable($txtFile)) {
    $lines = file($txtFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $l) {
        $l = trim($l);
        if ($l !== '') $ips_export[$l] = true;
    }
}
if (is_readable($jsonFile)) {
    $arr = json_decode(@file_get_contents($jsonFile), true);
    if (is_array($arr)) {
        foreach ($arr as $l) {
            $l = trim($l);
            if ($l !== '') $ips_export[$l] = true;
        }
    }
}

while ($row = mysqli_fetch_assoc($rs)) {
    $id_ref = (int)$row['id_bloqueo_referencia'];
    $ip     = trim($row['ip_bloqueda']);
    $fecha  = $row['fecha_bloqueo'];
    if ($ip === '') continue;

    // verficar que no se repite la ip
    $query = mysqli_query($link,"SELECT * FROM threat_ip_list WHERE ip = '$ip'");
    $bandera = mysqli_num_rows($query);
    if ($bandera == 0) {
        $total_ips++;
        $found_in_any = false;

        foreach ($files as $fname) {
            $hits = isset($file_ip_counts[$fname][$ip]) ? $file_ip_counts[$fname][$ip] : 0;

            if ($hits > 0) {
                $found_in_any = true;
                $ip_sql   = mysqli_real_escape_string($link, $ip);
                $file_sql = mysqli_real_escape_string($link, $fname);
                $now      = $fecha;

                //codigo pais
                $codigo_ip = getCountryFromIP($ip_sql, "code");

                // Guarda/actualiza por (id_ref, ip, archivo)
                $q = "
                    INSERT INTO threat_ip_list
                        (id_bloqueo_referencia, ip, source_file, hits, first_seen, last_seen, codigo_pais)
                    VALUES
                        ($id_ref, '$ip_sql', '$file_sql', $hits, '$now', '$now', '$codigo_ip')
                    ON DUPLICATE KEY UPDATE
                        hits = VALUES(hits),
                        last_seen = VALUES(last_seen)
                ";
                $ok = mysqli_query($link, $q);
                if (!$ok) {
                    // Si quieres continuar y solo loguear el error:
                    error_log('MySQL error: '.mysqli_error($link).' | Query: '.$q);
                } else {
                    $total_inserts += mysqli_affected_rows($link) > 0 ? 1 : 0;
                    $ips_export[$ip_sql] = true;
                }
            }
        }

        // --- LÓGICA NUEVA: Forzar bots.txt si el tipo es botsearch/badbots y no se encontró ---
        $tipo_limpio = strtolower(trim($row['tipo_ataque']));
        if (!$found_in_any && ($tipo_limpio === 'nginx-botsearch' || $tipo_limpio === 'nginx-badbots')) {
            $ip_sql   = mysqli_real_escape_string($link, $ip);
            $file_sql = mysqli_real_escape_string($link, 'bots.txt');
            $now      = $fecha;
            $codigo_ip = getCountryFromIP($ip_sql, "code");

            $q = "
                INSERT INTO threat_ip_list
                    (id_bloqueo_referencia, ip, source_file, hits, first_seen, last_seen, codigo_pais)
                VALUES
                    ($id_ref, '$ip_sql', '$file_sql', 1, '$now', '$now', '$codigo_ip')
                ON DUPLICATE KEY UPDATE
                    hits = VALUES(hits),
                    last_seen = VALUES(last_seen)
            ";
            $ok = mysqli_query($link, $q);
            if (!$ok) {
                error_log('MySQL error: '.mysqli_error($link).' | Query: '.$q);
            } else {
                $total_inserts += mysqli_affected_rows($link) > 0 ? 1 : 0;
                $ips_export[$ip_sql] = true;
            }
        }
    }

    //actualizar la fecha en la tabla
    $query = mysqli_query($link,"UPDATE threat_ip_list_master_date SET fecha_master = '$fecha' WHERE id = 1") or die(mysqli_error($link));

}

mysqli_free_result($rs);
mysqli_close($link);

/* ------- Escribir unión (lo viejo + lo nuevo, sin duplicados) ------- */
$ips = array_keys($ips_export);        // deduplicado
natsort($ips);                         // orden natural opcional
$ips = array_values($ips);             // reindexa

// JSON (se reescribe con la unión; no se pierde info)
file_put_contents($jsonFile, json_encode($ips, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX);

// TXT (una IP por línea; idem, unión completa)
file_put_contents($txtFile, implode(PHP_EOL, $ips) . PHP_EOL, LOCK_EX);

echo "OK. IPs procesadas: $total_ips | Filas insert/actualizadas: $total_inserts\n";
