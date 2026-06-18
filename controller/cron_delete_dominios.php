<?php
// controller/cron_delete_dominios.php
// Borra en lotes los registros históricos de 'visita_dominio' para dominios eliminados.
// Optimizado para servidores de bajos recursos (2 cores).

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

// PASO 2: Borrar un lote de 5,000 registros de visita_dominio.
// El índice idx_cleanup_dominio hace que esta operación sea instantánea.
$delete = mysqli_query($link, "DELETE FROM visita_dominio WHERE dominio = '$dominio' LIMIT 5000");

if (!$delete) {
    error_log("[cron_delete_dominios] Error MySQL: " . mysqli_error($link));
    exit(1);
}

$affected = mysqli_affected_rows($link);

// PASO 3: Si ya no hay más registros para este dominio, marcar como limpio.
if ($affected === 0) {
    mysqli_query($link, "UPDATE host_visita_borrados SET visita_limpia = 1 WHERE id_host_visita_borrados = $id_row");

    /**
     * OPTIMIZACIÓN DE LIMPIEZA DE RESUMEN:
     * En lugar de un DELETE masivo con subconsultas pesadas, simplemente limpiamos las entradas
     * del resumen que ya no tienen registros activos asociados.
     * Dado que el resumen es pequeño comparado con la tabla principal, esto es seguro.
     */
    mysqli_query($link, "
        DELETE vdg FROM visita_dominio_group vdg
        LEFT JOIN visita_dominio vd ON vdg.ip_visita = vd.ip_visita AND vd.activo_visita = 1
        WHERE vd.ip_visita IS NULL
    ");
}

exit(0);
?>
