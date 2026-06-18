<?php
// controller/ajax_convertion_visitas_group.php
// Mantiene visita_dominio_group actualizado de forma INCREMENTAL.
//
// ANTES: TRUNCATE + GROUP BY sobre 62M filas → 69 segundos, servidor saturado.
// AHORA: Solo procesa filas NUEVAS desde el último id_visita procesado (watermark).
//         Cada ejecución tarda < 1 segundo salvo que haya millones de visitas nuevas.
//
// Requiere: UNIQUE KEY `uk_group_ip_visita` en visita_dominio_group (db_audit_patch_v4.sql)

$baseDir = dirname(__DIR__);

include_once $baseDir . '/includes/config.php';
include_once $baseDir . '/includes/security.php';
include_once __DIR__ . '/cron_helpers.php';

$cronLock = waf_acquire_cron_lock('ajax_convertion_visitas_group');

// ====================================================================
// PASO 1: Leer watermark (último id_visita procesado)
// ====================================================================
$stateFile = waf_cron_state_dir() . '/visitas_group_watermark.json';
$lastId    = 0;

if (is_file($stateFile)) {
    $state  = json_decode(file_get_contents($stateFile), true);
    $lastId = (int)($state['last_id'] ?? 0);
}

// ====================================================================
// PASO 2: Saber hasta qué ID hay datos nuevos
// ====================================================================
$maxRow = mysqli_fetch_assoc(
    mysqli_query($link, "SELECT MAX(id_visita) AS max_id FROM visita_dominio WHERE activo_visita = 1")
);
$maxId = (int)($maxRow['max_id'] ?? 0);

if ($maxId <= $lastId) {
    // No hay registros nuevos que procesar.
    exit(0);
}

// Procesar hasta 300,000 filas nuevas por ejecución para que sea rápido.
// Si hay más, el próximo cron tomará el siguiente lote.
$batchMax = min($maxId, $lastId + 300000);

// ====================================================================
// PASO 3: INSERT incremental con ON DUPLICATE KEY UPDATE.
// Solo agrega los conteos de las filas NUEVAS. El UNIQUE KEY en
// ip_visita garantiza que si la IP ya existe, solo suma el total.
// ====================================================================
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
      AND id_visita > $lastId
      AND id_visita <= $batchMax
    GROUP BY ip_visita
    ON DUPLICATE KEY UPDATE
        total        = total + VALUES(total),
        fecha_visita = GREATEST(fecha_visita, VALUES(fecha_visita)),
        id_visita    = GREATEST(id_visita, VALUES(id_visita))
");

if (!$ok) {
    error_log("[visitas_group] Error MySQL: " . mysqli_error($link));
    exit(1);
}

// ====================================================================
// PASO 4: Guardar nuevo watermark
// ====================================================================
file_put_contents($stateFile, json_encode([
    'last_id'    => $batchMax,
    'updated_at' => date('Y-m-d H:i:s'),
]));
?>
