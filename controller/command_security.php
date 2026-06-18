<?php
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}

	function waf_is_admin_session()
	{
		return !empty($_SESSION['id_u']) &&
			!empty($_SESSION['activo']) &&
			isset($_SESSION['tipo_usuario']) &&
			$_SESSION['tipo_usuario'] == 1;
	}

	function waf_require_admin_session($message = 'mal')
	{
		if (!waf_is_admin_session()) {
			http_response_code(403);
			echo $message;
			exit;
		}
	}

	function waf_validate_site_name($name)
	{
		$name = trim((string)$name);
		if ($name === '' || basename($name) !== $name) {
			return false;
		}

		return preg_match('/^[a-z0-9.-]+$/i', $name) === 1;
	}

	function waf_run_command(array $argv, &$output = array(), &$returnCode = null, $timeout = 30)
	{
		$output = array();
		$returnCode = 1;

		if (empty($argv)) {
			return false;
		}

		$descriptors = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$env = array(
			'PATH' => '/usr/sbin:/usr/bin:/sbin:/bin',
			'LC_ALL' => 'C'
		);

		$process = proc_open($argv, $descriptors, $pipes, null, $env);
		if (!is_resource($process)) {
			$output[] = 'No se pudo iniciar el comando.';
			return false;
		}

		fclose($pipes[0]);
		stream_set_blocking($pipes[1], false);
		stream_set_blocking($pipes[2], false);

		$stdout = '';
		$stderr = '';
		$startedAt = time();
		$processExitCode = null;

		while (true) {
			$stdout .= (string)stream_get_contents($pipes[1]);
			$stderr .= (string)stream_get_contents($pipes[2]);

			$status = proc_get_status($process);
			if (!$status['running']) {
				if (isset($status['exitcode']) && $status['exitcode'] >= 0) {
					$processExitCode = (int)$status['exitcode'];
				}
				break;
			}

			if ((time() - $startedAt) > $timeout) {
				proc_terminate($process);
				$stderr .= "\nTimeout ejecutando comando.";
				break;
			}

			usleep(100000);
		}

		$stdout .= (string)stream_get_contents($pipes[1]);
		$stderr .= (string)stream_get_contents($pipes[2]);

		fclose($pipes[1]);
		fclose($pipes[2]);

		$closeCode = proc_close($process);
		$returnCode = $processExitCode !== null ? $processExitCode : $closeCode;

		$combined = trim($stdout."\n".$stderr);
		if ($combined !== '') {
			$output = preg_split('/\R/', $combined);
		}

		return $returnCode === 0;
	}

	function waf_reload_nginx(&$output = array(), &$returnCode = null)
	{
		$systemctl = file_exists('/bin/systemctl') ? '/bin/systemctl' : '/usr/bin/systemctl';
		return waf_run_command(array('/usr/bin/sudo', '-n', $systemctl, 'reload', 'nginx'), $output, $returnCode, 30);
	}

	function waf_copy_site_vhost($siteName, &$output = array(), &$returnCode = null)
	{
		if (!waf_validate_site_name($siteName)) {
			$output = array('Nombre de sitio invalido.');
			$returnCode = 1;
			return false;
		}

		$source = '/var/www/reportwui/siteconfig/'.$siteName.'.vhost';
		$destination = '/etc/nginx/sites-available/';

		return waf_run_command(array('/usr/bin/sudo', '-n', '/usr/bin/cp', '-f', $source, $destination), $output, $returnCode, 30);
	}

	function waf_remove_site_vhost($siteName, &$output = array(), &$returnCode = null)
	{
		if (!waf_validate_site_name($siteName)) {
			$output = array('Nombre de sitio invalido.');
			$returnCode = 1;
			return false;
		}

		$target = '/etc/nginx/sites-available/'.$siteName.'.vhost';

		return waf_run_command(array('/usr/bin/sudo', '-n', '/usr/bin/rm', '-f', $target), $output, $returnCode, 30);
	}

	function waf_copy_stream_conf($configName, &$output = array(), &$returnCode = null)
	{
		if (!waf_validate_site_name($configName)) {
			$output = array('Nombre de configuracion invalido.');
			$returnCode = 1;
			return false;
		}

		$source = '/var/www/reportwui/siteconfig/'.$configName.'.conf';
		$destination = '/etc/nginx/stream-enabled/';

		return waf_run_command(array('/usr/bin/sudo', '-n', '/usr/bin/cp', '-f', $source, $destination), $output, $returnCode, 30);
	}
?>
