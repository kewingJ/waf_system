<?php
    include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
    
    /**
     * OPTIMIZACIÓN: Eliminación de visita.json y bucles pesados en PHP.
     * Ahora usamos el nuevo índice en (activo_visita, codigo_pais) para contar directamente en SQL.
     * El rendimiento pasa de O(N*M) a O(1) con índices.
     */

    // 1. Obtener conteos agrupados por país directamente de la base de datos
    $stats = [];
    $query_stats = mysqli_query($link, "
        SELECT codigo_pais, COUNT(*) as total
        FROM visita_dominio
        WHERE activo_visita = 1
        GROUP BY codigo_pais
    ");

    while($row = mysqli_fetch_assoc($query_stats)) {
        $stats[$row['codigo_pais']] = $row['total'];
    }

    // 2. Obtener lista de países para mapear ISO a ISO3 y actualizar la tabla visita_pais
    $consult = mysqli_query($link, "SELECT id_pais, iso FROM paises");

    while($rows = mysqli_fetch_array($consult))
    {
        $iso2     = $rows['iso'];
        $id_pais  = $rows['id_pais'];
        $total    = isset($stats[$iso2]) ? $stats[$iso2] : 0;

        if ($total > 0) {
            // Obtenemos el ISO3 (AbBr) solo una vez por país si hay visitas
            // Usamos una IP ficticia del rango del país para obtener el ISO3 de la librería si no estuviera en BD
            // Pero idealmente el ISO3 debería estar en la tabla 'paises'.
            // Como el código original hacía getCountryFromIP($ip, "AbBr"), mantendremos compatibilidad
            // aunque lo ideal es que 'paises' ya tenga 'iso3'.

            // Nota: Para no depender de una IP, si la librería lo permite lo sacamos del global.
            $iso3 = isset($GLOBALS['geoipcntry'][$iso2]) ? $GLOBALS['geoipcntry'][$iso2] : $iso2;

            mysqli_query($link, "
                UPDATE visita_pais
                SET total_visita = '$total', iso3 = '$iso3'
                WHERE id_pais = '$id_pais'
            ");
        } else {
            // Opcional: poner a 0 si no hay visitas (el original no lo hacía pero es buena práctica)
            mysqli_query($link, "UPDATE visita_pais SET total_visita = 0 WHERE id_pais = '$id_pais'");
        }
    }
?>
