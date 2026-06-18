<?php
// controller/cron_delete_dominios.php
// Borra en lotes los registros históricos de 'visita_dominio' para dominios eliminados.
// DISEÑO SEGURO:
//   - Lock de archivo: solo UNA instancia a la vez.
//   - Nunca borra de host_visita_borrados (esa tabla es la lista negra permanente de ingesta).
//   - Usa el flag `visita_limpia = 1` para marcar dominios ya procesados y NO repetirlos.

$baseDir = dirname(__DIR__);
include_once $baseDir . '/includes/config.php';
include_once __DIR__ . '/cron_helpers.php';

// ====================================================================
// LOCK: Solo UNA instancia puede ejecutarse a la vez.
// ====================================================================
$cronLock = waf_acquire_cron_lock('cron_delete_dominios');

// ====================================================================
// PASO 1: Obtener UN solo dominio pendiente (visita_limpia = 0).
// Si visita_limpia = 1 ya fue procesado → se salta automáticamente.
// ====================================================================
$row = mysqli_fetch_assoc(
    mysqli_query($link, "SELECT id_host_visita_borrados, nombre_host FROM host_visita_borrados WHERE visita_limpia = 0 LIMIT 1")
);

if (!$row) {
    // Todos los dominios en la lista negra ya están limpios. Nada que hacer.
    exit(0);
}

$id_row  = (int)$row['id_host_visita_borrados'];
$dominio = mysqli_real_escape_string($link, $row['nombre_host']);

// ====================================================================
// PASO 2: Borrar un lote de 3,000 registros de visita_dominio.
// Lote pequeño = transacción rápida = menos presión en disco I/O.
// ====================================================================
$delete = mysqli_query($link, "DELETE FROM visita_dominio WHERE dominio = '$dominio' LIMIT 3000");

if (!$delete) {
    error_log("[cron_delete_dominios] Error MySQL al borrar: " . mysqli_error($link));
    exit(1);
}

$affected = mysqli_affected_rows($link);

// ====================================================================
// PASO 3: Si ya no quedan registros, marcar como limpio y limpiar la
// tabla de resumen. NUNCA se borra de host_visita_borrados (es la
// lista negra que usa ajax_convertion_visitas.php para no re-ingresar).
// ====================================================================
if ($affected === 0) {
    // Marcar como limpio para que no se vuelva a procesar.
    mysqli_query($link, "UPDATE host_visita_borrados SET visita_limpia = 1 WHERE id_host_visita_borrados = $id_row");

    // Limpiar entradas del resumen cuya IP ya no tiene registros activos.
    // (Esto es seguro: el índice idx_visita_dominio_activo_ip hace el DISTINCT eficiente.)
    mysqli_query($link, "
        DELETE vdg
        FROM visita_dominio_group vdg
        LEFT JOIN (
            SELECT DISTINCT ip_visita FROM visita_dominio WHERE activo_visita = 1
        ) AS activas ON vdg.ip_visita = activas.ip_visita
        WHERE activas.ip_visita IS NULL
    ");
}

exit(0);
?>
