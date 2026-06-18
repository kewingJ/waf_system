<?php
require("../includes/config.php");
require("../includes/security.php");
require_once("../tcpdf/tcpdf.php");

date_default_timezone_set('America/Managua');
session_start();

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
            $this->Image($logo, 12, 6, 24, 0, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

function severity_color(string $sev): string
{
    $s = strtolower(trim($sev));
    if ($s === 'alto') return '#d9534f';
    if ($s === 'medio') return '#f0ad4e';
    return '#3bb67f';
}

function severity_label_html(string $sev): string
{
    $s = strtolower(trim($sev));
    if ($s === 'alto') {
        return '<span style="background-color:#d9534f;color:#ffffff;padding:2px 10px;border-radius:3px;font-weight:bold;">Alto</span>';
    }
    if ($s === 'medio') {
        return '<span style="background-color:#f0ad4e;color:#ffffff;padding:2px 10px;border-radius:3px;font-weight:bold;">Medio</span>';
    }
    return '<span style="background-color:#3bb67f;color:#ffffff;padding:2px 10px;border-radius:3px;font-weight:bold;">Bajo</span>';
}

$query = "SELECT host, port, vulnerability, fecha_analisis, severidad, more_information
          FROM vulnerability
          WHERE vulnerability LIKE '%CVE%'
          ORDER BY fecha_analisis DESC";
$result = mysqli_query($link, $query);

$rows = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}

$titulo = 'Reporte de Vulnerabilidades';
$fecha_reporte = date('Y-m-d H:i:s');

$pdf = new ReportPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('WAF');
$pdf->SetAuthor('WAF');
$pdf->SetTitle($titulo);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetMargins(12, 36, 12);
$pdf->SetHeaderMargin(8);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();

$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 8, $titulo, 0, 1, 'C');
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(0, 6, 'Generado: ' . $fecha_reporte, 0, 1, 'C');
$pdf->Ln(4);

$table = '<table border="1" cellpadding="4" cellspacing="0">';
$table .= '<thead><tr style="background-color:#f0f0f0;font-weight:bold;">';
$table .= '<th align="center">#</th>';
$table .= '<th>Host</th>';
$table .= '<th>Port</th>';
$table .= '<th>Vulnerabilidad</th>';
$table .= '<th>Fecha de analisis</th>';
$table .= '<th align="center">Severidad</th>';
$table .= '<th align="center">Ver mas informacion</th>';
$table .= '</tr></thead><tbody>';

if (count($rows) === 0) {
    $table .= '<tr><td colspan="7" align="center">Sin registros de vulnerabilidades.</td></tr>';
} else {
    $i = 1;
    foreach ($rows as $row) {
        $host = htmlspecialchars($row['host'], ENT_QUOTES, 'UTF-8');
        $port = htmlspecialchars($row['port'], ENT_QUOTES, 'UTF-8');
        $vuln = htmlspecialchars($row['vulnerability'], ENT_QUOTES, 'UTF-8');
        $fecha = htmlspecialchars($row['fecha_analisis'], ENT_QUOTES, 'UTF-8');
        $sev = htmlspecialchars($row['severidad'], ENT_QUOTES, 'UTF-8');
        $more = trim((string)$row['more_information']);

        $vuln_color = severity_color($row['severidad']);
        $vuln_html = '<span style="color:' . $vuln_color . ';font-weight:bold;">' . $vuln . '</span>';
        $sev_html = severity_label_html($row['severidad']);
        $more_html = $more !== ''
            ? '<a href="' . htmlspecialchars($more, ENT_QUOTES, 'UTF-8') . '" target="_blank">Ver mas informacion</a>'
            : '-';

        $table .= '<tr>';
        $table .= '<td align="center">' . $i . '</td>';
        $table .= '<td>' . $host . '</td>';
        $table .= '<td>' . $port . '</td>';
        $table .= '<td>' . $vuln_html . '</td>';
        $table .= '<td>' . $fecha . '</td>';
        $table .= '<td align="center">' . $sev_html . '</td>';
        $table .= '<td align="center">' . $more_html . '</td>';
        $table .= '</tr>';
        $i++;
    }
}

$table .= '</tbody></table>';

$pdf->SetFont('dejavusans', '', 9);
$pdf->writeHTML($table, true, false, true, false, '');

$filename = 'reporte_vulnerabilidades_' . date('Ymd_His') . '.pdf';
$pdf->Output($filename, 'I');
exit;
