<?php
header('Content-Type: application/json; charset=UTF-8');

date_default_timezone_set('America/Managua');
session_start();

if (empty($_SESSION['id_u']) || empty($_SESSION['activo']) || (int) $_SESSION['tipo_usuario'] !== 1) {
    http_response_code(403);
    echo json_encode(array(
        'success' => false,
        'message' => 'No autorizado.'
    ));
    exit;
}

function lisense_parse_conf($path)
{
    $data = array(
        'type' => '',
        'code' => '',
        'days' => 0,
        'model' => '',
        'status' => '',
        'expires_at' => ''
    );

    if (!is_readable($path)) {
        return $data;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $data;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '//') === 0 || strpos($line, '#') === 0) {
            continue;
        }

        if (!preg_match('/^([A-Z_]+)\s*=\s*[\'\"]?(.*?)[\'\"]?\s*;?\s*$/', $line, $matches)) {
            continue;
        }

        $key = strtoupper(trim($matches[1]));
        $value = trim($matches[2]);

        if ($key === 'LISENSE_TYPE') {
            $data['type'] = $value;
        } elseif ($key === 'LISENSE_CODE') {
            $data['code'] = $value;
        } elseif ($key === 'LISENSE_DAYS') {
            $data['days'] = (int) $value;
        } elseif ($key === 'LISENSE_MODEL') {
            $data['model'] = $value;
        } elseif ($key === 'LISENSE_STATUS') {
            $data['status'] = $value;
        } elseif ($key === 'LISENSE_EXPIRES_AT') {
            $data['expires_at'] = str_replace('/', '-', $value);
        }
    }

    if ($data['days'] < 0) {
        $data['days'] = 0;
    }

    return $data;
}

function lisense_create_date($dateValue)
{
    if (empty($dateValue)) {
        return false;
    }

    $dateValue = str_replace('/', '-', trim((string) $dateValue));
    return DateTime::createFromFormat('Y-m-d', $dateValue);
}

function lisense_normalize_code($code)
{
    $code = strtoupper(trim((string) $code));
    return preg_replace('/[^A-Z0-9]/', '', $code);
}

function lisense_get_days_left($expiresAt)
{
    if (empty($expiresAt)) {
        return 0;
    }

    $today = new DateTime(date('Y-m-d'));
    $expiry = lisense_create_date($expiresAt);
    if (!$expiry) {
        return 0;
    }

    $expiry->setTime(0, 0, 0);
    if ($expiry < $today) {
        return 0;
    }

    return ((int) $today->diff($expiry)->format('%a')) + 1;
}

function lisense_format_display_date($dateValue)
{
    $date = lisense_create_date($dateValue);
    if (!$date) {
        return '-';
    }

    return $date->format('Y/m/d');
}

function lisense_mask_code($code)
{
    $code = trim((string) $code);
    $len = strlen($code);
    if ($len <= 4) {
        return str_repeat('*', $len);
    }

    return str_repeat('*', max(0, $len - 4)) . substr($code, -4);
}

$codeInput = isset($_POST['lisense_code']) ? trim($_POST['lisense_code']) : '';
if ($codeInput === '') {
    echo json_encode(array(
        'success' => false,
        'message' => 'Debes ingresar el codigo de licencia.'
    ));
    exit;
}

$confPath = __DIR__ . '/../.lisense.conf';
$statePath = __DIR__ . '/../includes/lisense_state.json';
$conf = lisense_parse_conf($confPath);

if (empty($conf['code']) || (int) $conf['days'] <= 0) {
    echo json_encode(array(
        'success' => false,
        'message' => 'La configuracion de licencia no es valida.'
    ));
    exit;
}

$expectedCode = lisense_normalize_code($conf['code']);
$receivedCode = lisense_normalize_code($codeInput);

if ($expectedCode === '' || $receivedCode !== $expectedCode) {
    echo json_encode(array(
        'success' => false,
        'message' => 'El codigo no es correcto.'
    ));
    exit;
}

$today = new DateTime(date('Y-m-d'));
$daysTotal = max(0, (int) $conf['days']);
$expires = lisense_create_date($conf['expires_at']);
if (!$expires) {
    $expires = clone $today;
    if ($daysTotal > 1) {
        $expires->modify('+' . ($daysTotal - 1) . ' days');
    }
}
$status = !empty($conf['status']) ? $conf['status'] : 'Active';

$state = array(
    'active' => true,
    'activation_key' => $conf['code'],
    'model' => $conf['model'],
    'status' => $status,
    'activated_at' => $today->format('Y-m-d'),
    'expires_at' => $expires->format('Y-m-d'),
    'type' => $conf['type'],
    'days_total' => $daysTotal,
    'cleanup_executed' => false,
    'cleanup_at' => '',
    'cleanup_error' => ''
);

$written = file_put_contents($statePath, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
if ($written === false) {
    echo json_encode(array(
        'success' => false,
        'message' => 'No se pudo guardar el estado de la licencia.'
    ));
    exit;
}

echo json_encode(array(
    'success' => true,
    'message' => 'Proyecto activado correctamente.',
    'status' => $status,
    'type' => $conf['type'],
    'activation_key' => $conf['code'],
    'model' => $conf['model'],
    'masked_code' => lisense_mask_code($conf['code']),
    'days_left' => lisense_get_days_left($state['expires_at']),
    'total_days' => $daysTotal,
    'activated_at' => $state['activated_at'],
    'expires_at' => $state['expires_at'],
    'expires_at_display' => lisense_format_display_date($state['expires_at']),
    'last_checked' => date('Y-m-d H:i:s')
));
