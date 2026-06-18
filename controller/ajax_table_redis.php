<?php
    require("../includes/config.php");
    require("../includes/security.php");

    // Conexión a Redis
    $redis = new Redis();
    $redis->connect($redis_host, $redis_port);

    // Obtener datos de la tabla grafica_pais desde MySQL
    $consult = mysqli_query($link,"SELECT id_grafica, fecha_bloqueo, total_waf, total_fuerza FROM grafica_principal");
    
    // Almacenar los datos en Redis
    while($row = mysqli_fetch_array($consult)){
        $key = "id_grafica:" . $row['id_grafica'];
        $value = json_encode([
            'fecha_bloqueo' => $row['fecha_bloqueo'],
            'total_waf' => $row['total_waf'],
            'total_fuerza' => $row['total_fuerza']
        ]);
        // O utilizar un hash (hash) para almacenar los datos en Redis
        $redis->hMSet($key, json_decode($value, true));
}

// Cerrar conexion
$redis->close();
echo "Datos almacenados en Redis correctamente.";
?>
