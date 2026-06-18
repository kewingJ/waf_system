<?php
/**
 * migrate_geoip_data.php
 * Script para poblar la columna 'codigo_pais' en datos existentes.
 * Procesa en lotes para evitar saturar el servidor de 2 cores.
 */

require_once 'includes/config.php';
require_once 'geoIp/geoiploc.php';

// Aumentar tiempo de ejecución y memoria para CLI
ini_set('memory_limit', '512M');
set_time_limit(0);

$batchSize = 5000;

function migrateTable($link, $tableName, $idCol, $ipCol, $batchSize) {
    echo "Iniciando migración de la tabla: $tableName ($ipCol)" . PHP_EOL;

    $totalUpdated = 0;

    while (true) {
        // Buscar registros que aún tengan el valor por defecto 'ZZ'
        $query = "SELECT $idCol, $ipCol FROM $tableName WHERE codigo_pais = 'ZZ' LIMIT $batchSize";

        $result = mysqli_query($link, $query);

        if (!$result || mysqli_num_rows($result) === 0) {
            echo "No hay más registros pendientes en $tableName." . PHP_EOL;
            break;
        }

        $updates = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row[$idCol];
            $ip = $row[$ipCol];
            $code = getCountryFromIP($ip, 'code');
            if (empty($code)) $code = 'ZZ';

            $updates[$code][] = $id;
        }

        // Ejecutar actualizaciones agrupadas por código de país
        foreach ($updates as $code => $ids) {
            $idList = implode(',', $ids);
            $updateSql = "UPDATE $tableName SET codigo_pais = '$code' WHERE $idCol IN ($idList)";
            mysqli_query($link, $updateSql);
        }

        $totalUpdated += mysqli_num_rows($result);
        echo "Actualizados $totalUpdated registros..." . PHP_EOL;

        // Pequeña pausa
        usleep(100000);
    }
}

// Migrar visita_dominio
migrateTable($link, 'visita_dominio', 'id_visita', 'ip_visita', $batchSize);

// Migrar bloqueo_ip
migrateTable($link, 'bloqueo_ip', 'id_bloqueo_ip', 'ip_bloqueada', $batchSize);

echo "Migración finalizada con éxito." . PHP_EOL;
?>
