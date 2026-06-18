<?php
// controller/cron_rebuild_visitas_group.php
// Reconstrucción COMPLETA de visita_dominio_group.
// EJECUTAR SOLO en horas de bajo tráfico (ej. madrugada, 3am).
// Agrega al cron del servidor con baja frecuencia (ej. semanal):
//   0 3 * * 0  php /var/www/.../controller/cron_rebuild_visitas_group.php

$baseDir = dirname(__DIR__);
include_once $baseDir . '/includes/config.php';
include_once __DIR__ . '/cron_helpers.php';

$cronLock = waf_acquire_cron_lock('cron_rebuild_visitas_group');

// Limpiar la tabla de resumen completamente
mysqli_query($link, "TRUNCATE TABLE visita_dominio_group");
if (mysqli_errno($link)) {
    error_log("[rebuild_group] Error TRUNCATE: " . mysqli_error($link));
    exit(1);
}

// Reconstruir en lotes de 500,000 IDs para no saturar MySQL de golpe
$minId = 1;
$batchSize = 500000;

$maxRow = mysqli_fetch_assoc(mysqli_query($link, "SELECT MAX(id_visita) AS m FROM visita_dominio"));
$maxId  = (int)($maxRow['m'] ?? 0);

if ($maxId === 0) {
    echo "No hay registros en visita_dominio.\n";
    exit(0);
}

$iterations = 0;
while ($minId <= $maxId) {
    $batchEnd = $minId + $batchSize - 1;

    $ok = mysqli_query($link, "
        INSERT INTO visita_dominio_group (id_visita, fecha_visita, ip_visita, dominio, total)
        SELECT
            MAX(id_visita)    AS id_visita,
            MAX(fecha_visita) AS fecha_visita,
            ip_visita,
            MAX(dominio)      AS dominio,
            COUNT(*)          AS total
        FROM visita_dominio
        WHERE activo_visita = 1
          AND id_visita >= $minId
          AND id_visita <= $batchEnd
        GROUP BY ip_visita
        ON DUPLICATE KEY UPDATE
            total        = total + VALUES(total),
            fecha_visita = GREATEST(fecha_visita, VALUES(fecha_visita)),
            id_visita    = GREATEST(id_visita, VALUES(id_visita))
    ");

    if (!$ok) {
        error_log("[rebuild_group] Error en lote $minId-$batchEnd: " . mysqli_error($link));
    }

    $minId += $batchSize;
    $iterations++;

    // Pequeño respiro entre lotes para no ahogar el disco
    usleep(200000); // 200ms
}

// Resetear el watermark incremental para que el script diario arranque desde aquí
$stateFile = waf_cron_state_dir() . '/visitas_group_watermark.json';
file_put_contents($stateFile, json_encode([
    'last_id'    => $maxId,
    'updated_at' => date('Y-m-d H:i:s'),
    'full_rebuild' => true,
]));

echo "OK. Reconstrucción completa en $iterations lotes hasta id_visita=$maxId\n";
?>
