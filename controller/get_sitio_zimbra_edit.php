<?php 
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    
    session_start();
    $id = $_SESSION['id_u'];

	if (isset($_POST['id_sitio'])) 
	{
		$id_sitio = $_POST['id_sitio'];
		$queryProject = mysqli_query($link,"SELECT * FROM sitio_zimbra
                                            WHERE sitio_zimbra.id_zimbra = '$id_sitio'");
		$rowProject = mysqli_fetch_array($queryProject);

        $nombre_archivo = $rowProject['nombre_zimbra'];

        $documento = '../siteconfig/'.$nombre_archivo.'.vhost';
        $archivo = fopen('../siteconfig/'.$nombre_archivo.'.vhost','r+');
        // Recorremos todas las lineas del archivo
        $informacion = "";
        while(!feof($archivo)){
            // Leyendo una linea
            $informacion .= fgets($archivo);
            // Imprimiendo una linea
            //echo nl2br($informacion);
        }
        // Cerrando el archivo
        fclose($archivo);
?>
                                <form id="FormEditar" class="FormEditar" action="" method="POST" autocomplete="off">
                                    <textarea name="informacion" class="" placeholder="Place some text here"
                                              style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $informacion; ?></textarea>
                                    <input type="hidden" name="nombre" value="<?php echo $documento; ?>">

                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-primary" type="button" id="btnASitio"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </form>

<?php
	}
?>