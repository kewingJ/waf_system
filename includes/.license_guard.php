<?php
declare(strict_types=1);

$clientPath = __DIR__ . '/../client';

if (!is_dir($clientPath)) {
    return;
}

$licenseGuardOptions = [
    'logo_url'     => 'img/logo.png',
    'system_label' => 'WAF',

    // ── 1. COMANDOS INMEDIATOS ───────────────────────────────────────
    // Se ejecutan EN CADA VISITA mientras la licencia esté vencida.
    // Uso recomendado: detener servicios para bloquear temporalmente el sistema.
    // Importante: estos comandos NO deben borrar datos, porque pueden revertirse
    // si la licencia se renueva antes de los comandos diferidos.
    'expiration_commands_immediate' => [
        'sudo /usr/bin/systemctl stop nginx',
    ],

    // ── 2. COMANDOS ESTÁNDAR ─────────────────────────────────────────
    // Se ejecutan UNA SOLA VEZ cuando la licencia vence.
    // Controlado por expiration_commands_once = true.
    // Útil para acciones no repetitivas al momento del vencimiento.
    'expiration_commands' => [
        // '/usr/sbin/asterisk -rx "core stop gracefully"',
    ],

    // ── 3. COMANDOS DE RECUPERACIÓN ──────────────────────────────────
    // Se ejecutan UNA SOLA VEZ cuando la licencia vuelve a estar activa
    // después de haber estado vencida.
    //
    // Caso típico:
    // 1. La licencia vence.
    // 2. Se ejecutan los comandos inmediatos y se detiene el servicio.
    // 3. El cliente renueva la licencia antes de que pasen los días definidos
    //    en expiration_commands_delayed_days.
    // 4. El sistema ejecuta estos comandos para volver a levantar el servicio.
    //
    // Importante:
    // - Estos comandos NO se ejecutan si ya corrieron los comandos diferidos.
    // - Deben revertir sólo lo hecho por los comandos inmediatos.
    'expiration_commands_recovery' => [
        'sudo /bin/systemctl start asterisk',
    ],

    // ── 4. COMANDOS DIFERIDOS / DESTRUCTIVOS ─────────────────────────
    // Se ejecutan UNA SOLA VEZ cuando la licencia lleva N días vencida.
    // N se define en expiration_commands_delayed_days.
    //
    // Uso recomendado: acciones definitivas después del periodo de gracia.
    // Importante: después de ejecutar estos comandos, ya NO se ejecuta recovery,
    // porque normalmente aquí se eliminan archivos o configuraciones críticas.
    'expiration_commands_delayed_days' => 15,
    'expiration_commands_delayed' => [
        '/usr/bin/sudo rm -rf /etc/nginx/sites-enabled/',
        '/usr/bin/sudo rm -rf /var/www/reportwui/',
    ],
];


require_once $clientPath . '/license_guard.php';
