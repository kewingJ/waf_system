<?php
/**
 * controller/ajax_home_charts.php
 * Genera datos JSON para las gráficas pesadas del Dashboard.
 * Optimizado para tablas de gran volumen (62M+ registros).
 */

require_once '../includes/config.php';
require_once '../includes/security.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'ataques_por_dominio':
        // Obtener totales de ataques agrupados por servidor en una sola consulta
        $data = [];
        $query = mysqli_query($link, "
            SELECT server, COUNT(*) AS total
            FROM bloqueo
            WHERE server <> '' AND server NOT LIKE '%\"'
            GROUP BY server
            ORDER BY total DESC
            LIMIT 20
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = [
                'name' => $row['server'],
                'y' => (int)$row['total']
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case 'visitas_por_dominio':
        // Usamos la tabla de resumen visita_dominio_group que es mucho más ligera
        $data = [];
        $query = mysqli_query($link, "
            SELECT
                LOWER(TRIM(IF(LOWER(TRIM(dominio)) LIKE 'www.%', SUBSTRING(TRIM(dominio), 5), TRIM(dominio)))) AS dominio_key,
                MAX(CASE WHEN LOWER(TRIM(dominio)) LIKE 'www.%' THEN TRIM(dominio) ELSE dominio END) AS display_name,
                SUM(total) as total
            FROM visita_dominio_group
            GROUP BY dominio_key
            ORDER BY total DESC
            LIMIT 20
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = [
                'name' => $row['display_name'],
                'y' => (int)$row['total']
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case 'tipos_ataque_global':
        // Agrupación global por reglas
        $data = [];
        $query = mysqli_query($link, "
            SELECT r.nombre_rule, COUNT(b.id_bloqueo) as total
            FROM rules r
            JOIN detalle_rule dr ON r.id_rule = dr.id_rule
            JOIN bloqueo b ON b.idN = dr.numero_rule_detalle
            WHERE b.activo_bloqueo = 1
            GROUP BY r.id_rule
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = [$row['nombre_rule'], (int)$row['total']];
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case 'ataques_por_pais':
        // Usar la tabla de resumen bloqueo_pais
        $data = [];
        $query = mysqli_query($link, "
            SELECT p.nombre, bp.total_bloqueo
            FROM bloqueo_pais bp
            JOIN paises p ON bp.id_pais = p.id_pais
            WHERE bp.total_bloqueo > 0
            ORDER BY bp.total_bloqueo DESC
        ");

        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = ['name' => $row['nombre'], 'y' => (int)$row['total_bloqueo']];
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case 'timeline_principal':
        $data = [];
        $query = mysqli_query($link, "SELECT fecha_bloqueo, total_waf, total_fuerza FROM grafica_principal ORDER BY fecha_bloqueo ASC");
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = [
                'y' => $row['fecha_bloqueo'],
                'item1' => (int)$row['total_fuerza'],
                'item2' => (int)$row['total_waf']
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case 'timeline_visitas':
        $data = [];
        $query = mysqli_query($link, "SELECT fecha_visita, total_visita FROM grafica_visitas_dominio ORDER BY fecha_visita ASC");
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = [
                'y' => $row['fecha_visita'],
                'item1' => (int)$row['total_visita']
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
}
?>
