<?php
if (!empty($_POST['ip_host_scan'])) {
    $rawHost = $_POST['ip_host_scan'];
    $vulnDir = __DIR__ . '/../vuln';
    $file = $vulnDir . '/host.txt';
    $lastHost = null;

    if (!is_array($rawHost)) {
        $rawHost = [$rawHost];
    }

    foreach ($rawHost as $value) {
        $host = trim($value);
        $isIp   = filter_var($host, FILTER_VALIDATE_IP);
        $isName = preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $host);
        if (!$isIp && !$isName) {
            continue;
        }
        $lastHost = $host;
    }

    if ($lastHost === null) {
        die('Host o IP no válido.');
    }

    if (!is_dir($vulnDir) && !mkdir($vulnDir, 0775, true)) {
        die('No se pudo crear el directorio vuln.');
    }

    $bytes = file_put_contents($file, $lastHost . PHP_EOL, LOCK_EX);
    if ($bytes === false) {
        die('No se pudo escribir el archivo host.txt.');
    }

    $_POST['scan_host_history'] = $lastHost;

    //llamar a archivo ajax_vulnerability.php y pasar el host a ajax_vulnerability.php
    include 'ajax_vulnerability.php';
} else {
    echo "error: no se recibió la IP o host.";
}
?>
