<?php
	session_start();
	include_once '../includes/config.php';
	include_once '../includes/security.php';
	include_once 'command_security.php';

	if (!empty($_POST['dato'])) {
		waf_require_admin_session();

	    $command = 'sudo systemctl reload nginx';
	    $output = [];
	    $return_var = 0;
	    $is_debug = !empty($_POST['debug']);

	    waf_reload_nginx($output, $return_var);

	    if ($return_var !== 0) {
	        $message = sprintf(
	            'ajax_cambios.php: fallo al ejecutar "%s" (codigo %d): %s',
	            $command,
	            $return_var,
	            implode(' | ', $output)
	        );
	        error_log($message);

	        if ($is_debug) {
	            header('Content-Type: application/json');
	            echo json_encode([
	                'status' => 'error',
	                'exit_code' => $return_var,
	                'output' => $output,
	            ]);
	            exit;
	        }
	    } else if ($is_debug) {
	        header('Content-Type: application/json');
	        echo json_encode([
	            'status' => 'ok',
	            'exit_code' => $return_var,
	            'output' => $output,
	        ]);
	        exit;
	    }

	    echo $return_var === 0 ? 'bien' : 'mal';
	} else {
		echo "mal";
	}
?>
