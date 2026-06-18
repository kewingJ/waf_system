<?php
require("../includes/config.php");
require("../includes/security.php");
require_once("../tcpdf/tcpdf.php");

date_default_timezone_set('America/Managua');
session_start();
@ini_set('memory_limit', '256M');
@set_time_limit(300);

$id     = isset($_SESSION['id_u']) ? $_SESSION['id_u'] : '';
$activo = isset($_SESSION['activo']) ? $_SESSION['activo'] : '';
$tipo   = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : '';
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
$apellido = isset($_SESSION['apellido']) ? $_SESSION['apellido'] : '';

if (empty($id) || empty($activo) || $tipo != 1) {
    header("Location: ../index.php");
    exit;
}

$fechaGuardada = isset($_SESSION["ultimoAcceso"]) ? $_SESSION["ultimoAcceso"] : '';
$ahora = date("Y-n-j H:i:s");
$tiempo_transcurrido = (strtotime($ahora) - strtotime($fechaGuardada));

if ($tiempo_transcurrido >= 3600 || empty($fechaGuardada)) {
    $consult = mysqli_query($link,"SELECT * FROM usuario WHERE id_usuario = '$id'");
    $row = mysqli_fetch_array($consult);

    $nombre_parts = explode(' ', (string)$nombre);
    $apellido_parts = explode(' ', (string)$apellido);
    $nombre_first = isset($nombre_parts[0]) ? $nombre_parts[0] : '';
    $apellido_first = isset($apellido_parts[0]) ? $apellido_parts[0] : '';

    session_destroy();
    session_start();
    $_SESSION['nombre_usuario']     = $nombre_first;
    $_SESSION['apellido_usuario']   = $apellido_first;
    $_SESSION['correo_usuario']     = isset($row['email_u']) ? $row['email_u'] : '';

    header("Location: ../lockscreen.php");
    exit;
} else {
    $_SESSION["ultimoAcceso"] = $ahora;
}

class ReportPDF extends TCPDF
{
    public function Header()
    {
        $logo = __DIR__ . '/../img/logo.png';
        if (file_exists($logo)) {
            // Logo en la esquina superior izquierda
            $this->Image($logo, 15, 6, 24, 0, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

function dibujar_grafico_pastel($pdf, $data, $xc, $yc, $r, $legend_x, $legend_y)
{
    $total = array_sum($data);
    if ($total <= 0) {
        return $legend_y;
    }

    arsort($data);

    $palette = array(
        array(52, 152, 219),
        array(231, 76, 60),
        array(46, 204, 113),
        array(241, 196, 15),
        array(155, 89, 182),
        array(26, 188, 156),
        array(230, 126, 34),
        array(149, 165, 166)
    );

    $start = 0;
    $index = 0;
    foreach ($data as $label => $value) {
        $angle = ($value / $total) * 360;
        if ($angle <= 0) {
            continue;
        }
        $rgb = $palette[$index % count($palette)];
        $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
        $pdf->SetDrawColor(255, 255, 255);
        $pdf->PieSector($xc, $yc, $r, $start, $start + $angle, 'FD', false, 0, 2);
        $start += $angle;
        $index++;
    }

    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Ellipse($xc, $yc, $r, $r, 0, 0, 360);

    $pdf->SetFont('dejavusans', '', 9);
    $line_height = 6;
    $box = 4;
    $index = 0;
    foreach ($data as $label => $value) {
        $rgb = $palette[$index % count($palette)];
        $percent = ($value / $total) * 100;
        $text = $label . ' (' . $value . ' - ' . number_format($percent, 1) . '%)';

        $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
        $pdf->Rect($legend_x, $legend_y, $box, $box, 'F');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Text($legend_x + $box + 2, $legend_y - 0.5, $text);
        $legend_y += $line_height;
        $index++;
    }

    return $legend_y;
}

function truncate_text($text, $max = 220)
{
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') <= $max) {
            return $text;
        }
        return mb_substr($text, 0, $max - 3, 'UTF-8') . '...';
    }

    if (strlen($text) <= $max) {
        return $text;
    }

    return substr($text, 0, $max - 3) . '...';
}

$tipo_reporte = isset($_POST['tipo_reporte']) ? $_POST['tipo_reporte'] : '';
$rango_reporte = isset($_POST['rango_reporte']) ? $_POST['rango_reporte'] : '';
$nombre_reporte = isset($_POST['nombre_reporte']) ? trim($_POST['nombre_reporte']) : '';
$dominio_reporte = isset($_POST['dominio_reporte']) ? trim($_POST['dominio_reporte']) : '';

$nombre_reporte = clean($nombre_reporte);
$dominio_reporte = clean($dominio_reporte);
if ($nombre_reporte === '') {
    $nombre_reporte = 'Reporte';
}

$dominio_reporte_key = strtolower(trim($dominio_reporte));
if (strpos($dominio_reporte_key, 'www.') === 0) {
    $dominio_reporte_key = substr($dominio_reporte_key, 4);
}
if ($dominio_reporte_key !== '' && !preg_match('/^[a-z0-9.-]+$/', $dominio_reporte_key)) {
    $dominio_reporte_key = '';
}

$where_dominio = '';
$dominio_reporte_label = '';
if ($dominio_reporte_key !== '') {
    $dominio_reporte_sql = mysqli_real_escape_string($link, $dominio_reporte_key);
    $where_dominio = " AND (LOWER(TRIM(server)) = '".$dominio_reporte_sql."' OR LOWER(TRIM(server)) = 'www.".$dominio_reporte_sql."')";

    $dominio_reporte_label = $dominio_reporte_key;
    $query_dominio_label = "SELECT
                            MAX(CASE WHEN LOWER(TRIM(server)) LIKE 'www.%' THEN TRIM(server) ELSE '' END) AS dominio_www,
                            MAX(CASE WHEN LOWER(TRIM(server)) NOT LIKE 'www.%' THEN TRIM(server) ELSE '' END) AS dominio_plain
                            FROM bloqueo
                            WHERE (LOWER(TRIM(server)) = '".$dominio_reporte_sql."' OR LOWER(TRIM(server)) = 'www.".$dominio_reporte_sql."')";
    $result_dominio_label = mysqli_query($link, $query_dominio_label);
    if ($result_dominio_label) {
        $row_dominio_label = mysqli_fetch_assoc($result_dominio_label);
        if (!empty($row_dominio_label['dominio_www'])) {
            $dominio_reporte_label = $row_dominio_label['dominio_www'];
        } elseif (!empty($row_dominio_label['dominio_plain'])) {
            $dominio_reporte_label = $row_dominio_label['dominio_plain'];
        }
    }
}

$now = new DateTime();
$start = clone $now;
$rango_label = '1 Semana';

switch ($rango_reporte) {
    case 'T2':
        $start->modify('-1 month');
        $rango_label = '1 Mes';
        break;
    case 'T3':
        $start->modify('-2 months');
        $rango_label = '2 Meses';
        break;
    case 'T1':
    default:
        $start->modify('-7 days');
        $rango_label = '1 Semana';
        break;
}

$start->setTime(0, 0, 0);
$end = clone $now;
$end->setTime(23, 59, 59);

$start_sql = $start->format('Y-m-d H:i:s');
$end_sql = $end->format('Y-m-d H:i:s');

$reporte_titulo = 'Reporte';
$columnas = array();
$filas = array();
$grafico_data = array();
$mensaje_vacio = 'Sin registros para el rango seleccionado.';
$r4_detalle = false;
$max_filas_pdf = 2000;
$filas_recortadas = false;
$total_ip_bloqueadas = 0;

if ($tipo_reporte === 'R2') {
    $reporte_titulo = 'Reporte de IP bloqueadas';
    $columnas = array('#', 'Fecha de bloqueo', 'Origen', 'Host Destino', 'Url', 'Tipo de ataque');

    $query_total_ips = "SELECT COUNT(DISTINCT ip) AS total_ips
              FROM bloqueo
              WHERE fecha_bloqueo BETWEEN '$start_sql' AND '$end_sql' $where_dominio";
    $result_total_ips = mysqli_query($link, $query_total_ips);
    if ($result_total_ips) {
        $row_total_ips = mysqli_fetch_assoc($result_total_ips);
        $total_ip_bloqueadas = isset($row_total_ips['total_ips']) ? (int)$row_total_ips['total_ips'] : 0;
    }

    $query = "SELECT fecha_bloqueo, ip, server, url, tipo_ataque
              FROM bloqueo
              WHERE fecha_bloqueo BETWEEN '$start_sql' AND '$end_sql' $where_dominio
              ORDER BY fecha_bloqueo DESC
              LIMIT " . ($max_filas_pdf + 1);
    $result = mysqli_query($link, $query);

    if ($result) {
        $loaded = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            if ($loaded >= $max_filas_pdf) {
                $filas_recortadas = true;
                break;
            }
            $filas[] = array(
                $row['fecha_bloqueo'],
                $row['ip'],
                $row['server'],
                truncate_text($row['url'], 220),
                $row['tipo_ataque']
            );
            $loaded++;
        }
    }
} elseif ($tipo_reporte === 'R3') {
    $reporte_titulo = 'Reporte de tipo de ataques';
    $columnas = array('#', 'Tipo de ataque', 'Numero bloqueos');

    $query = "SELECT tipo_ataque, COUNT(*) AS total
              FROM bloqueo
              WHERE fecha_bloqueo BETWEEN '$start_sql' AND '$end_sql' $where_dominio
              GROUP BY tipo_ataque
              ORDER BY total DESC";
    $result = mysqli_query($link, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $filas[] = array(
                $row['tipo_ataque'],
                $row['total']
            );
        }
    }
} elseif ($tipo_reporte === 'R4') {
    $reporte_titulo = 'Reporte de ataques por dominios';
    $r4_detalle = ($dominio_reporte_key !== '');
    if ($r4_detalle) {
        $columnas = array('#', 'Dominio', 'Tipo de ataque');
    } else {
        $columnas = array('#', 'Dominio', 'Total ataques');
    }

    if ($r4_detalle) {
        $query_grafico = "SELECT tipo_ataque, COUNT(*) AS total
            FROM bloqueo
            WHERE activo_bloqueo = 1
            AND fecha_bloqueo BETWEEN '$start_sql' AND '$end_sql'
            AND tipo_ataque <> ''
            $where_dominio
            GROUP BY tipo_ataque
            ORDER BY total DESC";
        $result_grafico = mysqli_query($link, $query_grafico);
        if ($result_grafico) {
            while ($row_grafico = mysqli_fetch_assoc($result_grafico)) {
                $tipo = trim($row_grafico['tipo_ataque']);
                $total = (int)$row_grafico['total'];
                if ($tipo !== '' && $total > 0) {
                    $grafico_data[$tipo] = $total;
                }
            }
        }
    }
    if ($r4_detalle) {
        $query = "SELECT tipo_ataque
            FROM bloqueo
            WHERE activo_bloqueo = 1
            AND fecha_bloqueo BETWEEN '$start_sql' AND '$end_sql'
            AND tipo_ataque <> ''
            $where_dominio
            GROUP BY tipo_ataque
            ORDER BY tipo_ataque
            LIMIT " . ($max_filas_pdf + 1);
        $result = mysqli_query($link, $query);

        if ($result) {
            $loaded = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($loaded >= $max_filas_pdf) {
                    $filas_recortadas = true;
                    break;
                }
                $filas[] = array(
                    $dominio_reporte_label,
                    $row['tipo_ataque']
                );
                $loaded++;
            }
        }
    } else {
        $query = "SELECT
            LOWER(
                TRIM(
                    IF(
                        LOWER(TRIM(server)) LIKE 'www.%',
                        SUBSTRING(TRIM(server), 5),
                        TRIM(server)
                    )
                )
            ) AS dominio_key,
            MAX(CASE WHEN LOWER(TRIM(server)) LIKE 'www.%' THEN TRIM(server) ELSE '' END) AS dominio_www,
            MAX(CASE WHEN LOWER(TRIM(server)) NOT LIKE 'www.%' THEN TRIM(server) ELSE '' END) AS dominio_plain,
            COUNT(*) AS total
            FROM bloqueo
            WHERE activo_bloqueo = 1
            AND fecha_bloqueo BETWEEN '$start_sql' AND '$end_sql'
            GROUP BY dominio_key
            ORDER BY total DESC
            LIMIT " . ($max_filas_pdf + 1);
        $result = mysqli_query($link, $query);

        if ($result) {
            $loaded = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($loaded >= $max_filas_pdf) {
                    $filas_recortadas = true;
                    break;
                }
                $dominio_label = !empty($row['dominio_www']) ? $row['dominio_www'] : $row['dominio_plain'];
                if ($dominio_label === '') {
                    $dominio_label = $row['dominio_key'];
                }
                $filas[] = array(
                    $dominio_label,
                    $row['total']
                );
                $loaded++;
            }
        }
    }
} else {
    $columnas = array('Mensaje');
    $mensaje_vacio = 'Tipo de reporte no valido.';
}

$pdf = new ReportPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('WAF');
$pdf->SetAuthor('WAF');
$pdf->SetTitle($reporte_titulo);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetMargins(15, 36, 15);
$pdf->SetHeaderMargin(8);
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 8, $reporte_titulo, 0, 1, 'C');
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(0, 6, $nombre_reporte, 0, 1, 'C');
$pdf->Cell(0, 6, 'Rango: ' . $rango_label . ' (' . $start->format('Y-m-d') . ' a ' . $end->format('Y-m-d') . ')', 0, 1, 'C');
if ($tipo_reporte === 'R2') {
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(0, 6, 'Total de IP bloqueadas: ' . number_format($total_ip_bloqueadas), 0, 1, 'C');
    $pdf->SetFont('dejavusans', '', 10);
}
$pdf->Ln(4);

if ($tipo_reporte === 'R4' && $r4_detalle && count($grafico_data) > 0) {
    $pdf->SetFont('dejavusans', 'B', 11);
    $pdf->Cell(0, 6, 'Grafico de tipos de ataque - ' . $dominio_reporte_label, 0, 1, 'C');

    $chart_top = $pdf->GetY();
    $pie_r = 30;
    $pie_xc = 15 + $pie_r;
    $pie_yc = $chart_top + $pie_r + 8;
    $legend_x = $pie_xc + $pie_r + 15;
    $legend_y = $chart_top + 6;

    $legend_end = dibujar_grafico_pastel($pdf, $grafico_data, $pie_xc, $pie_yc, $pie_r, $legend_x, $legend_y);
    $chart_bottom = max($pie_yc + $pie_r, $legend_end + 2);
    $pdf->SetY($chart_bottom + 4);
}

$table_open = '<table border="1" cellpadding="4" cellspacing="0">';
$table_open .= '<thead><tr style="background-color:#f0f0f0;font-weight:bold;">';

if ($tipo_reporte === 'R2') {
    $widths = array('5%', '20%', '15%', '20%', '25%', '15%');
} elseif ($tipo_reporte === 'R3') {
    $widths = array('10%', '60%', '30%');
} elseif ($tipo_reporte === 'R4') {
    if ($r4_detalle) {
        $widths = array('10%', '45%', '45%');
    } else {
        $widths = array('10%', '65%', '25%');
    }
} else {
    $widths = array('100%');
}

foreach ($columnas as $index => $col) {
    $width = isset($widths[$index]) ? $widths[$index] : '';
    $table_open .= '<th>' . htmlspecialchars($col, ENT_QUOTES, 'UTF-8') . '</th>';
}

$table_open .= '</tr></thead><tbody>';
$table_close = '</tbody></table>';
$rows_per_chunk = 250;

$pdf->SetFont('dejavusans', '', 9);
if (count($filas) === 0) {
    $colspan = count($columnas);
    $body = '<tr><td colspan="' . $colspan . '" align="center">' . htmlspecialchars($mensaje_vacio, ENT_QUOTES, 'UTF-8') . '</td></tr>';
    $pdf->writeHTML($table_open . $body . $table_close, true, false, true, false, '');
} else {
    $contador = 1;
    $rows_html = '';
    $rows_in_chunk = 0;

    foreach ($filas as $fila) {
        $rows_html .= '<tr>';
        $rows_html .= '<td align="center">' . $contador . '</td>';

        if ($tipo_reporte === 'R2') {
            $rows_html .= '<td>' . htmlspecialchars(date('Y-m-d H:i', strtotime($fila[0])), ENT_QUOTES, 'UTF-8') . '</td>';
            $rows_html .= '<td>' . htmlspecialchars($fila[1], ENT_QUOTES, 'UTF-8') . '</td>';
            $rows_html .= '<td>' . htmlspecialchars($fila[2], ENT_QUOTES, 'UTF-8') . '</td>';
            $rows_html .= '<td>' . htmlspecialchars($fila[3], ENT_QUOTES, 'UTF-8') . '</td>';
            $rows_html .= '<td>' . htmlspecialchars($fila[4], ENT_QUOTES, 'UTF-8') . '</td>';
        } elseif ($tipo_reporte === 'R3') {
            $rows_html .= '<td>' . htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8') . '</td>';
            $rows_html .= '<td align="right">' . htmlspecialchars($fila[1], ENT_QUOTES, 'UTF-8') . '</td>';
        } elseif ($tipo_reporte === 'R4') {
            $rows_html .= '<td>' . htmlspecialchars($fila[0], ENT_QUOTES, 'UTF-8') . '</td>';
            if ($r4_detalle) {
                $rows_html .= '<td>' . htmlspecialchars($fila[1], ENT_QUOTES, 'UTF-8') . '</td>';
            } else {
                $rows_html .= '<td align="right">' . htmlspecialchars($fila[1], ENT_QUOTES, 'UTF-8') . '</td>';
            }
        }

        $rows_html .= '</tr>';
        $contador++;
        $rows_in_chunk++;

        if ($rows_in_chunk >= $rows_per_chunk) {
            $pdf->writeHTML($table_open . $rows_html . $table_close, true, false, true, false, '');
            $rows_html = '';
            $rows_in_chunk = 0;
        }
    }

    if ($rows_html !== '') {
        $pdf->writeHTML($table_open . $rows_html . $table_close, true, false, true, false, '');
    }
}

if ($filas_recortadas) {
    $pdf->Ln(2);
    $pdf->SetFont('dejavusans', 'I', 8);
    $pdf->Cell(0, 6, 'Nota: se muestran los primeros ' . $max_filas_pdf . ' registros para proteger rendimiento del servidor.', 0, 1, 'L');
}

$filename = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($nombre_reporte));
if ($filename === '') {
    $filename = 'reporte';
}
$filename .= '_' . date('Ymd_His') . '.pdf';

$pdf->Output($filename, 'I');
exit;
