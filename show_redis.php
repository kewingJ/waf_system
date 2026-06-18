<?php
    require("includes/config.php");
    require("includes/security.php");

    // Conexión a Redis
    $redis = new Redis();
    $redis->connect($redis_host, $redis_port);

    $keys = $redis->keys('*');

    // Iterar a través de las claves y obtener los datos asociados a cada una
    foreach ($keys as $key) {
        $data = $redis->hGetAll($key);

        // Procesar los datos si es necesario
        echo "<br>";
        echo "Clave: $key";
        echo "<br>";
        print_r($data);
}
// Cerrar conexión
$redis->close();
?>
