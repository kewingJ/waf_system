<?php
	session_start();
	if(!empty($_POST['id_bloqueo']))
	{
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_bloqueo = $_POST['id_bloqueo'];
        $consult    = mysqli_query($link,"SELECT * FROM bloqueo WHERE bloqueo.id_bloqueo = '$id_bloqueo'");
	    $row        = mysqli_fetch_array($consult);

        $zoneN  = $row['zoneN'];
        $idN    = $row['idN'];

        $aux_string = 'BasicRule wl:'.$idN.' "mz:'.$zoneN.'";';

        $file_handle = fopen("../config/naxsi_whitelist.rules", "a");
        fwrite ($file_handle, $aux_string."\n");
        fclose ($file_handle);

		//ejecutar comando
		// $comando = shell_exec('systemctl reload nginx');

		echo "bien";
	} else {
		echo "mal";
	}
?>