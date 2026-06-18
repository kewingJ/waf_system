<?php
include_once '../includes/config.php';

$filename = 'threat_ips_' . date('Y-m-d') . '.txt';

header('Content-Type: text/plain; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$sql = "SELECT DISTINCT ip FROM threat_ip_list WHERE ip IS NOT NULL AND ip <> '' ORDER BY ip";
$result = mysqli_query($link, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['ip'] . "\n";
    }
}

exit;
