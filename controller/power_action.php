<?php
	session_start();
	header('Content-Type: application/json; charset=utf-8');
	header('X-Content-Type-Options: nosniff');

	include_once '../includes/config.php';
	include_once '../includes/security.php';

	function power_response($ok, $message)
	{
		echo json_encode(array(
			'ok' => $ok,
			'message' => $message
		));
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		http_response_code(405);
		power_response(false, 'Metodo no permitido.');
	}

	$id = $_SESSION['id_u'] ?? '';
	$activo = $_SESSION['activo'] ?? '';
	$tipo = $_SESSION['tipo_usuario'] ?? '';

	if (empty($id) || empty($activo) || $tipo != 1) {
		http_response_code(403);
		power_response(false, 'Sesion no autorizada.');
	}

	$token = $_POST['token'] ?? '';
	$sessionToken = $_SESSION['power_action_token'] ?? '';

	if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
		http_response_code(403);
		power_response(false, 'Token de seguridad invalido.');
	}

	$action = $_POST['action'] ?? '';
	if (!is_string($action)) {
		http_response_code(400);
		power_response(false, 'Accion invalida.');
	}

	$commands = array(
		'reboot' => array(
			'label' => 'reboot',
			'argv'  => array('/usr/bin/sudo', '-n', '/opt/scripts/reboots.sh')
		),
		'shutdown' => array(
			'label' => 'shutdown',
			'argv'  => array('/usr/bin/sudo', '-n', '/opt/scripts/powerof.sh')
		)
	);

	if (!isset($commands[$action])) {
		http_response_code(400);
		power_response(false, 'Accion invalida.');
	}

	$command = $commands[$action]['argv'];
	$env = array(
		'PATH' => '/usr/sbin:/usr/bin:/sbin:/bin',
		'LC_ALL' => 'C'
	);

	$descriptors = array(
		0 => array('pipe', 'r'),
		1 => array('pipe', 'w'),
		2 => array('pipe', 'w')
	);

	$process = proc_open($command, $descriptors, $pipes, null, $env);

	if (!is_resource($process)) {
		http_response_code(500);
		power_response(false, 'No se pudo iniciar el comando.');
	}

	fclose($pipes[0]);
	$output = stream_get_contents($pipes[1]);
	fclose($pipes[1]);
	$error = stream_get_contents($pipes[2]);
	fclose($pipes[2]);

	$returnCode = proc_close($process);

	if ($returnCode === 0) {
		power_response(true, 'Comando enviado correctamente.');
	}

	error_log('Power action failed: '.$commands[$action]['label'].' return='.$returnCode.' output='.$output.' error='.$error);
	http_response_code(500);
	power_response(false, 'No se pudo ejecutar. Revisa permisos sudoers.');
?>
