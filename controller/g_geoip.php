<?php
	session_start();
	if(!empty($_POST['pais']))
	{
		include_once '../includes/config.php';
		include_once '../includes/security.php';

        $query = mysqli_query($link,"TRUNCATE geoippais") or die(mysqli_error($link));
        // 
		$paises       = $_POST['pais'];
        $fecha_r      = date('Y-m-d');
        foreach ($paises as $pais){
            $query = mysqli_query($link,"INSERT INTO geoippais VALUES (0,'$pais',1,'$fecha_r')") or die(mysqli_error($link));
        }  

        $consult = mysqli_query($link,"SELECT * FROM geoippais");
        $list_pais = "";
        while ($row = mysqli_fetch_array($consult)) {
            $iso_pais = strtoupper($row['iso_pais']);
            $aux_pais = "";
            //
            $aux_pais = $iso_pais.' no;';
            $list_pais .= $aux_pais."\n";
        }
        //
        $json_string = $list_pais;

	    $file = '../config/geo_country.map';
	    file_put_contents($file, $json_string);

		echo "bien";
	} else {
		echo "mal";
	}
?>