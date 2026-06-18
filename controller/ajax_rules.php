<?php
 	include_once '../includes/config.php';
    include_once '../includes/security.php';
    include_once '../geoIp/geoiploc.php';
	
	error_reporting(E_ALL);
    ini_set('display_errors', '1');

    //leer todo archivo que termine en .rules
    $thefolder = "../uploads/";
    $llave = 0;
    $arrayArchivos;
    if ($handler = opendir($thefolder)) {
        while (false !== ($file = readdir($handler))) {
            if(strstr($file, '.rules')){
                //echo "$file<br>";
                $arrayArchivos[$llave] = $file;
                $llave++;
            }
        }
        closedir($handler);
    }
    //var_dump($arrayArchivos);

    if($llave > 0)
    {
        foreach ($arrayArchivos as $archivo) {
            if (file_exists('../uploads/'.$archivo) && $archivo != 'naxsi_core.rules') 
            {
                $fp = fopen("../uploads/".$archivo, "r");
                $nombre_regla = '';
                while(!feof($fp)) 
                {
                    $linea = fgets($fp);

                    //obtener el nombre de la regla
                    $lineaAux = strstr($linea, '## ');
                    if (!empty($lineaAux)) 
                    {
                        $nombre_aux = explode(' ids:', $lineaAux);
                        $nombre_aux = explode('## ', $nombre_aux[0]);
                        $nombre_regla = $nombre_aux[1];
                    } else {
                        $nombre_aux = '';
                    }
                    $lineaAux = '';
                }
                //echo $nombre_regla.'<br>';
                fclose($fp);

                //verificar si la regla ya existe
                $query = mysqli_query($link,"SELECT * FROM rules WHERE nombre_rule = '$nombre_regla'");
                $existe_regla = mysqli_num_rows($query);
                $id_regla = 0;

                if($existe_regla > 0)
                {
                    //si la regla existe obtener su id
                    $rows = mysqli_fetch_array($query);
                    $id_regla = $rows['id_rule'];
                } else {
                    //guardar nueva regla
                    $fecha_r = date('y-m-d');

                    $guardar_regla = mysqli_query($link,"INSERT INTO rules VALUES (0,'$nombre_regla',0,0,1,'$fecha_r')");
                    $id_regla = mysqli_insert_id($link);
                }

                //eliminar detalles de reglas para volver a agregarlos
                if(!empty($id_regla)){
                    $queryDelete = mysqli_query($link,"DELETE FROM detalle_rule WHERE id_rule = '$id_regla'");
                }

                //obtener detalle de la regla y guardar
                $fp = fopen("../uploads/".$archivo, "r");
                while(!feof($fp)) 
                {
                    $linea = fgets($fp);

                    //verificar que es un detalle de regla
                    if(strstr($linea, 'MainRule')) {
                        
                        //optener numero de detalle
                        $lineaAux = strstr($linea, 'id:');
                        if (!empty($lineaAux)) 
                        {
                            $numero_detalle = explode(' "', $lineaAux);
                            $numero_detalle = explode('id:', $numero_detalle[0]);
                            $numero_detalle = $numero_detalle[1];
                            if (strpos($numero_detalle, ';') !== false) {
                                $numero_detalle = explode(' ;', $lineaAux);
                                $numero_detalle = explode('id:', $numero_detalle[0]);
                                $numero_detalle = $numero_detalle[1];
                            }
                        } else {
                            $numero_detalle = '';
                        }
                        $lineaAux = '';
                        //echo $numero_detalle.'<br>';

                        //optener nombre de detalle
                        $lineaAux = strstr($linea, '"msg:');
                        if (!empty($lineaAux)) 
                        {
                            $nombre_detalle = explode('";', $lineaAux);
                            $nombre_detalle = explode('"msg:', $nombre_detalle[0]);
                            $nombre_detalle = $nombre_detalle[1];
                            if (strpos($nombre_detalle, '" "') !== false) {
                                $nombre_detalle = explode('" ', $lineaAux);
                                $nombre_detalle = explode('"msg:', $nombre_detalle[0]);
                                $nombre_detalle = $nombre_detalle[1];
                            }
                        } else {
                            $nombre_detalle = '';
                        }
                        $lineaAux = '';
                        //echo $nombre_detalle.'<br>';

                        //verificamos si el detalle existe
                        $queryDetalle = mysqli_query($link,"SELECT * FROM detalle_rule WHERE nombre_d_r = '$nombre_detalle' AND numero_rule_detalle = '$numero_detalle'");
                        $existeDetalle = mysqli_num_rows($queryDetalle);
                        if($existeDetalle == 0)
                        {
                            $fecha_r = date('y-m-d');
                            //Guardar nuevo detalle
                            $guardarDetalle = mysqli_query($link,"INSERT INTO detalle_rule VALUES (0,'$id_regla','$nombre_detalle',1,'$numero_detalle','$fecha_r')");
                        } else {
                            echo $nombre_detalle;
                        }
                    }
                }
                fclose($fp);
            
                //actualizar la notificacion
                $fecha_registro = date('y-m-d');
                $guardar_noti = mysqli_query($link,"INSERT INTO notificacion_regla VALUES (0,'$id_regla',1,'$fecha_registro')");

                //eliminar archivo
                unlink('../uploads/'.$archivo);
            }
        }
        echo 'bien';
    }
?>