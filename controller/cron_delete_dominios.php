<?php
// controller/cron_delete_dominios.php
// Borra en lotes los registros históricos de 'visita_dominio' para dominios eliminados.
// Optimizado para evitar bloqueos de metadatos en tablas de gran volumen (62M+ registros).

$baseDir = dirname(__DIR__);
include_once $baseDir . '/includes/config.php';
include_once __DIR__ . '/cron_helpers.php';

// LOCK: Solo UNA instancia puede ejecutarse a la vez.
$cronLock = waf_acquire_cron_lock('cron_delete_dominios');

// PASO 1: Obtener un dominio pendiente de limpieza.
$row = mysqli_fetch_assoc(
    mysqli_query($link, "SELECT id_host_visita_borrados, nombre_host FROM host_visita_borrados WHERE visita_limpia = 0 LIMIT 1")
);

if (!$row) {
    exit(0);
}

$id_row  = (int)$row['id_host_visita_borrados'];
$dominio = mysqli_real_escape_string($link, $row['nombre_host']);

// PASO 2: Identificar las IPs que vamos a procesar en este lote.
// Esto nos permite limpiar la tabla de resumen de forma quirúrgica sin escaneos globales.
$batchSize = 5000;
$res = mysqli_query($link, "SELECT id_visita, ip_visita FROM visita_dominio WHERE dominio = '$dominio' LIMIT $batchSize");

$ids = [];
$ips = [];
while ($r = mysqli_fetch_assoc($res)) {
    $ids[] = $r['id_visita'];
    $ips[$r['ip_visita']] = true;
}

if (empty($ids)) {
    // Si no hay más registros para este dominio, lo marcamos como limpio.
    // Mantenemos el registro en host_visita_borrados como "lista negra" según requerimiento.
    mysqli_query($link, "UPDATE host_visita_borrados SET visita_limpia = 1 WHERE id_host_visita_borrados = $id_row");
    echo "Dominio '$dominio' marcado como limpio." . PHP_EOL;
} else {
    $idList = implode(',', $ids);

    // PASO 3: Borrar el lote de registros de visita_dominio.
    $delete = mysqli_query($link, "DELETE FROM visita_dominio WHERE id_visita IN ($idList)");

    if (!$delete) {
        error_log("[cron_delete_dominios] Error MySQL en DELETE: " . mysqli_error($link));
        exit(1);
    }

    $affected = mysqli_affected_rows($link);
    echo "Borrados $affected registros de '$dominio'." . PHP_EOL;

    // PASO 4: Limpieza quirúrgica de visita_dominio_group.
    // Solo comprobamos las IPs que acabamos de borrar. Gracias al índice idx_lookup_ip, esto es instantáneo.
    $uniqueIps = array_keys($ips);
    $ipListSql = "'" . implode("','", array_map(function($ip) use ($link) { return mysqli_real_escape_string($link, $ip); }, $uniqueIps)) . "'";

    mysqli_query($link, "
        DELETE vdg FROM visita_dominio_group vdg
        LEFT JOIN visita_dominio vd ON vdg.ip_visita = vd.ip_visita AND vd.activo_visita = 1
        WHERE vdg.ip_visita IN ($ipListSql)
          AND vd.ip_visita IS NULL
    ");
}

exit(0);
?>
