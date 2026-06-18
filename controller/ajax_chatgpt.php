<?php
    $baseDir = dirname(__DIR__);

    include_once $baseDir . '/includes/config.php';
    include_once $baseDir . '/includes/security.php';

    header('Content-Type: application/json; charset=utf-8');

    if (!isset($link) || !($link instanceof mysqli)) {
        echo json_encode("vacio");
        exit;
    }

    $consult = mysqli_query($link, "SELECT * FROM openai_api WHERE id_openai_api = 1");
    $row = mysqli_fetch_array($consult);

    if (empty($row['api_key']) || (int) $row['activado'] !== 1) {
        echo json_encode("vacio");
        exit;
    }

    $apiUrl = 'https://api.openai.com/v1/chat/completions';
    $fechaActual = date('Y-m-d');

    $data = array(
        'model' => 'gpt-4o',
        'temperature' => 0.4,
        'response_format' => array(
            'type' => 'json_object'
        ),
        'messages' => array(
            array(
                'role' => 'system',
                'content' => 'Eres un analista de ciberseguridad. Responde solo con JSON valido. No uses markdown. Devuelve exactamente la clave top_10_attacks con una lista de 10 objetos. Cada objeto debe tener attack como texto y frequency como numero entero.'
            ),
            array(
                'role' => 'user',
                'content' => 'Genera un ranking global estimado de los 10 ataques a aplicaciones y sitios web mas frecuentes a nivel mundial para la fecha ' . $fechaActual . '. No uses datos internos del sistema ni repitas ejemplos fijos. Responde estrictamente en formato JSON con esta estructura: {"top_10_attacks":[{"attack":"nombre","frequency":numero}]}.'
            )
        )
    );

    $dataJson = json_encode($data);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataJson);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $row['api_key']
    ));

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $curlError !== '') {
        echo json_encode("vacio");
        exit;
    }

    $responseData = json_decode($response, true);
    if (!isset($responseData['choices'][0]['message']['content'])) {
        echo json_encode("vacio");
        exit;
    }

    $generatedContent = trim($responseData['choices'][0]['message']['content']);
    $generatedContent = str_replace('```json', '', $generatedContent);
    $generatedContent = str_replace('```', '', $generatedContent);
    $generatedContent = trim($generatedContent);

    $jsonOutput = json_decode($generatedContent, true);
    if (json_last_error() !== JSON_ERROR_NONE || !isset($jsonOutput['top_10_attacks']) || !is_array($jsonOutput['top_10_attacks'])) {
        echo json_encode("vacio");
        exit;
    }

    $resultado = array();
    $contador = 0;

    foreach ($jsonOutput['top_10_attacks'] as $item) {
        if ($contador >= 10) {
            break;
        }

        $attack = isset($item['attack']) ? trim((string) $item['attack']) : '';
        $frequency = isset($item['frequency']) ? (int) $item['frequency'] : 0;

        if ($attack === '') {
            continue;
        }

        $resultado[] = array(
            'attack' => $attack,
            'frequency' => $frequency
        );
        $contador++;
    }

    if (count($resultado) === 0) {
        echo json_encode("vacio");
        exit;
    }

    echo json_encode(
        array('top_10_attacks' => $resultado),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
?>
