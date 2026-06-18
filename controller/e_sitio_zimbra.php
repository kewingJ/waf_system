<?php
	session_start();
	if(!empty($_POST['id_sitio']))
	{
		include_once '../includes/config.php';
		include_once '../includes/security.php';

		$id_sitio = $_POST['id_sitio'];

        //eliminamos el archivo
        $querySelect = mysqli_query($link,"SELECT * FROM sitio_zimbra WHERE sitio_zimbra.id_zimbra = '$id_sitio'");
		$row = mysqli_fetch_array($querySelect);
			
		//eliminar archivo desde la carpeta
		if(!empty($row['nombre_zimbra']))
		{
            $documento = '../siteconfig/'.$row['nombre_zimbra'].'.vhost';
			unlink($documento);
		}
		
		//eliminamos el sitio
		$query = mysqli_query($link,"DELETE FROM sitio_zimbra WHERE sitio_zimbra.id_zimbra = '$id_sitio'") or die(mysqli_error($link));

		echo "bien";
	}
	else {
		echo "mal";
	}
?>