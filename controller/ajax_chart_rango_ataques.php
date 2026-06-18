<?php
    $baseDir = dirname(__DIR__);
    include_once $baseDir . '/includes/config.php';
    include_once $baseDir . '/includes/security.php';
    date_default_timezone_set('America/Managua');

    // Datos HOY
    $actual = date("Y-m-d H:i:s");
    $pasadoAux = date("Y-m-d");
    $pasado = $pasadoAux." 00:00:00";
    
    $consultWaf = mysqli_query($link,"SELECT SUM(total_bloqueo_rango) AS total_waf FROM grafica_bloqueo_rango WHERE fecha_bloqueo_rango BETWEEN '$pasado' AND '$actual' AND rango_bloqueo = 'HOY'");
    $rowWaf = mysqli_fetch_array($consultWaf);
    $totalWafHoy = $rowWaf['total_waf'] ? (int)$rowWaf['total_waf'] : 0;
    
    $consultFuerza = mysqli_query($link,"SELECT SUM(total_bloqueo_rango_ip) AS total_fuerza FROM grafica_bloqueo_rango_ip WHERE fecha_bloqueo_rango_ip BETWEEN '$pasado' AND '$actual' AND rango_bloqueo_ip = 'HOY'");
    $rowFuerza = mysqli_fetch_array($consultFuerza);
    $totalFuerzaHoy = $rowFuerza['total_fuerza'] ? (int)$rowFuerza['total_fuerza'] : 0;
    
    $consultBots = mysqli_query($link,"SELECT SUM(total_bloqueo_bot) AS total_bots FROM grafica_bloqueo_bots WHERE fecha_bloqueo_bot BETWEEN '$pasado' AND '$actual'");
    $rowBots = mysqli_fetch_array($consultBots);
    $totalBotsHoy = $rowBots['total_bots'] ? (int)$rowBots['total_bots'] : 0;

    // Datos SEMANA
    $semana = date("Y-m-d H:i:s",strtotime($actual."- 1 week"));
    $consultWaf = mysqli_query($link,"SELECT SUM(total_bloqueo_rango) AS total_waf FROM grafica_bloqueo_rango WHERE fecha_bloqueo_rango BETWEEN '$semana' AND '$actual' AND rango_bloqueo = 'SEMANA'");
    $rowWaf = mysqli_fetch_array($consultWaf);
    $totalWafSemana = $rowWaf['total_waf'] ? (int)$rowWaf['total_waf'] : 0;
    
    $consultFuerza = mysqli_query($link,"SELECT SUM(total_bloqueo_rango_ip) AS total_fuerza FROM grafica_bloqueo_rango_ip WHERE fecha_bloqueo_rango_ip BETWEEN '$semana' AND '$actual' AND rango_bloqueo_ip = 'SEMANA'");
    $rowFuerza = mysqli_fetch_array($consultFuerza);
    $totalFuerzaSemana = $rowFuerza['total_fuerza'] ? (int)$rowFuerza['total_fuerza'] : 0;
    
    $consultBots = mysqli_query($link,"SELECT SUM(total_bloqueo_bot) AS total_bots FROM grafica_bloqueo_bots WHERE fecha_bloqueo_bot BETWEEN '$semana' AND '$actual'");
    $rowBots = mysqli_fetch_array($consultBots);
    $totalBotsSemana = $rowBots['total_bots'] ? (int)$rowBots['total_bots'] : 0;

    // Datos MES
    $mes = date("Y-m-d H:i:s",strtotime($actual."- 1 month"));
    $consultWaf = mysqli_query($link,"SELECT SUM(total_bloqueo_rango) AS total_waf FROM grafica_bloqueo_rango WHERE fecha_bloqueo_rango BETWEEN '$mes' AND '$actual' AND rango_bloqueo = 'MES'");
    $rowWaf = mysqli_fetch_array($consultWaf);
    $totalWafMes = $rowWaf['total_waf'] ? (int)$rowWaf['total_waf'] : 0;
    
    $consultFuerza = mysqli_query($link,"SELECT SUM(total_bloqueo_rango_ip) AS total_fuerza FROM grafica_bloqueo_rango_ip WHERE fecha_bloqueo_rango_ip BETWEEN '$mes' AND '$actual' AND rango_bloqueo_ip = 'MES'");
    $rowFuerza = mysqli_fetch_array($consultFuerza);
    $totalFuerzaMes = $rowFuerza['total_fuerza'] ? (int)$rowFuerza['total_fuerza'] : 0;
    
    $consultBots = mysqli_query($link,"SELECT SUM(total_bloqueo_bot) AS total_bots FROM grafica_bloqueo_bots WHERE fecha_bloqueo_bot BETWEEN '$mes' AND '$actual'");
    $rowBots = mysqli_fetch_array($consultBots);
    $totalBotsMes = $rowBots['total_bots'] ? (int)$rowBots['total_bots'] : 0;

    $response = [
        'hoy' => [
            'waf' => $totalWafHoy,
            'bots' => $totalBotsHoy,
            'fuerza' => $totalFuerzaHoy
        ],
        'semana' => [
            'waf' => $totalWafSemana,
            'bots' => $totalBotsSemana,
            'fuerza' => $totalFuerzaSemana
        ],
        'mes' => [
            'waf' => $totalWafMes,
            'bots' => $totalBotsMes,
            'fuerza' => $totalFuerzaMes
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
?>
