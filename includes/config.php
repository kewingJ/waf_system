<?php
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'db_waf');
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD,DB_DATABASE) or die(mysqli_error($link));
@mysqli_query($link,"SET NAMES 'utf8'");

// Configuración de la conexión a Redis
$redis_host = '127.0.0.1';
$redis_port = 6379;

$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db'   => 'db_waf',
    'host' => '127.0.0.1'
);

// token de api de ipinfo
$token_ipinfo = 'b4319613b42b76';

// Activacion de lisense
$lisense = 'Yes'; //Yes | No
?>