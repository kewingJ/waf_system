<?php
// Actualiza Ipthreat/apache.txt e Ipthreat/bots.txt desde lists.blocklist.de

header('Content-Type: application/json; charset=utf-8');

set_time_limit(120);
ini_set('display_errors', '0');

$lists = [
    'apache' => [
        'url'  => 'https://lists.blocklist.de/lists/apache.txt',
        'file' => __DIR__ . '/../Ipthreat/apache.txt',
    ],
    'bots' => [
        'url'  => 'https://lists.blocklist.de/lists/bots.txt',
        'file' => __DIR__ . '/../Ipthreat/bots.txt',
    ],
];

function fetch_remote_text(string $url, ?string &$error): ?string
{
    $error = null;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'WAF-Blocklist-Updater/1.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $body = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($body === false || $http_code < 200 || $http_code >= 300) {
            $error = $curl_error ?: ('HTTP ' . $http_code);
            return null;
        }

        return $body;
    }

    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'header'  => "User-Agent: WAF-Blocklist-Updater/1.0\r\n",
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    if ($body === false) {
        $error = 'No se pudo leer el contenido remoto.';
        return null;
    }

    if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
        $code = (int)$m[1];
        if ($code < 200 || $code >= 300) {
            $error = 'HTTP ' . $code;
            return null;
        }
    }

    return $body;
}

function write_atomic(string $path, string $content, ?string &$error): bool
{
    $error = null;
    $dir = dirname($path);

    if (!is_dir($dir)) {
        $error = 'Directorio no existe: ' . $dir;
        return false;
    }
    if (!is_writable($dir)) {
        $error = 'Directorio sin permisos de escritura: ' . $dir;
        return false;
    }

    $tmp = tempnam($dir, 'blk_');
    if ($tmp === false) {
        $error = 'No se pudo crear archivo temporal.';
        return false;
    }

    $bytes = file_put_contents($tmp, $content, LOCK_EX);
    if ($bytes === false) {
        @unlink($tmp);
        $error = 'No se pudo escribir el archivo temporal.';
        return false;
    }

    if (!@rename($tmp, $path)) {
        // Fallback si rename falla por permisos/FS
        $ok = @copy($tmp, $path);
        @unlink($tmp);
        if (!$ok) {
            $error = 'No se pudo mover el archivo temporal.';
            return false;
        }
    }

    if (!@chmod($path, 0644)) {
        $error = 'No se pudo ajustar permisos (0644) en ' . $path;
        return false;
    }

    return true;
}

$results = [];
$all_ok = true;

foreach ($lists as $name => $cfg) {
    $err = null;
    $body = fetch_remote_text($cfg['url'], $err);
    if ($body === null) {
        $all_ok = false;
        $results[$name] = [
            'ok' => false,
            'error' => $err,
            'url' => $cfg['url'],
        ];
        continue;
    }

    $body = str_replace(["\r\n", "\r"], "\n", $body);
    if ($body !== '' && substr($body, -1) !== "\n") {
        $body .= "\n";
    }

    $err = null;
    if (!write_atomic($cfg['file'], $body, $err)) {
        $all_ok = false;
        $results[$name] = [
            'ok' => false,
            'error' => $err,
            'file' => $cfg['file'],
        ];
        continue;
    }

    $line_count = 0;
    $trimmed = rtrim($body, "\n");
    if ($trimmed !== '') {
        $line_count = substr_count($trimmed, "\n") + 1;
    }

    $results[$name] = [
        'ok' => true,
        'file' => $cfg['file'],
        'bytes' => strlen($body),
        'lines' => $line_count,
    ];
}

if (!$all_ok) {
    http_response_code(500);
}

echo json_encode([
    'ok' => $all_ok,
    'updated_at' => date('c'),
    'results' => $results,
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
