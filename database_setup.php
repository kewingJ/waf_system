<?php
require_once('includes/config.php');

/**
 * database_setup.php
 * Sincroniza el esquema de la base de datos con las optimizaciones de rendimiento.
 * Compatible con MySQL 5.7+ y MariaDB.
 */

function columnExists($link, $table, $column) {
    $result = mysqli_query($link, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return mysqli_num_rows($result) > 0;
}

function indexExists($link, $table, $index) {
    $result = mysqli_query($link, "SHOW INDEX FROM `$table` WHERE Key_name = '$index'");
    return mysqli_num_rows($result) > 0;
}

echo "--- Iniciando actualización de esquema ---" . PHP_EOL;

// 1. Columnas de caché GeoIP
$tables_to_patch = [
    'visita_dominio' => 'ip_visita',
    'bloqueo_ip'     => 'ip_bloqueada'
];

foreach ($tables_to_patch as $table => $after) {
    if (!columnExists($link, $table, 'codigo_pais')) {
        echo "Añadiendo columna 'codigo_pais' a '$table' (Modo Instantáneo) ... ";
        // MariaDB 10.4+ soporta ADD COLUMN ALGORITHM=INSTANT incluso con AFTER
        $sql = "ALTER TABLE `$table` ADD COLUMN `codigo_pais` CHAR(2) DEFAULT 'ZZ' AFTER `$after`";
        if (mysqli_query($link, $sql)) echo "OK" . PHP_EOL;
        else {
            echo "ERROR: " . mysqli_error($link) . ". Reintentando con algoritmo INPLACE... ";
            $sql = "ALTER TABLE `$table` ADD COLUMN `codigo_pais` CHAR(2) DEFAULT 'ZZ' AFTER `$after`, ALGORITHM=INPLACE, LOCK=NONE";
            if (mysqli_query($link, $sql)) echo "OK" . PHP_EOL;
            else echo "ERROR FINAL: " . mysqli_error($link) . PHP_EOL;
        }
    } else {
        echo "Columna 'codigo_pais' ya existe en '$table'." . PHP_EOL;
    }
}

// 2. Deduplicación y UNIQUE KEY en visita_dominio_group
echo "Preparando 'visita_dominio_group' para restricción UNIQUE ... " . PHP_EOL;
// Primero eliminamos duplicados manteniendo el más reciente (mayor ID)
mysqli_query($link, "
    DELETE t1 FROM visita_dominio_group t1
    INNER JOIN visita_dominio_group t2
    WHERE t1.id_visita_group < t2.id_visita_group
      AND t1.ip_visita = t2.ip_visita
");

if (!indexExists($link, 'visita_dominio_group', 'uk_group_ip_visita')) {
    echo "Añadiendo UNIQUE KEY a 'visita_dominio_group' ... ";
    // En MariaDB ALTER TABLE usa comas para separar especificaciones
    if (mysqli_query($link, "ALTER TABLE visita_dominio_group ADD UNIQUE KEY `uk_group_ip_visita` (ip_visita), ALGORITHM=INPLACE, LOCK=NONE")) {
        echo "OK" . PHP_EOL;
    } else {
        echo "ERROR: " . mysqli_error($link) . PHP_EOL;
    }
}

// 3. Índices de rendimiento
$indices = [
    ['visita_dominio', 'idx_stats_opt_pais', '(activo_visita, codigo_pais)'],
    ['bloqueo_ip',     'idx_stats_opt_pais', '(codigo_pais)'],
    ['visita_dominio', 'idx_incremental_group', '(activo_visita, id_visita, ip_visita)'],
    ['visita_dominio', 'idx_cleanup_dominio', '(dominio)'],
    ['visita_dominio', 'idx_lookup_ip', '(ip_visita)'],
    ['bloqueo_ip',     'idx_lookup_ip', '(ip_bloqueada)']
];

foreach ($indices as $idx) {
    list($table, $name, $cols) = $idx;
    if (!indexExists($link, $table, $name)) {
        echo "Creando índice '$name' en '$table' (Operación en segundo plano) ... ";
        // Usamos ALTER TABLE con comas para separar especificaciones (requerido en MariaDB)
        $sql = "ALTER TABLE `$table` ADD INDEX `$name` $cols, ALGORITHM=INPLACE, LOCK=NONE";
        if (mysqli_query($link, $sql)) {
            echo "OK" . PHP_EOL;
        } else {
            echo "ERROR: " . mysqli_error($link) . PHP_EOL;
        }
    } else {
        echo "Índice '$name' ya existe en '$table'." . PHP_EOL;
    }
}

echo "--- Actualización completada con éxito ---" . PHP_EOL;
?>
