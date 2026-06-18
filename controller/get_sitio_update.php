<?php
include_once '../includes/config.php';
include_once '../includes/security.php';

session_start();
if (isset($_POST['id_sitio'])) 
{
    $id_sitio = $_POST['id_sitio'];
	$queryProject = mysqli_query($link,"SELECT * FROM sitio
                                WHERE sitio.id_sitio = '$id_sitio'");
	$rowProject = mysqli_fetch_array($queryProject);
    $nombre     = $rowProject['nombre_sitio'];
    $ip         = $rowProject['ip_sitio'];
    $puerto     = $rowProject['puerto_sitio'];

?>                                                            
                                <form id="Form10" class="FormSitioUpdate" action="" method="" autocomplete="off">
                                    <div class="form-group col-md-12">
                                        <label>Nombre sitio</label>
                                        <input value="<?php echo $nombre; ?>" type="text" class="form-control" name="nombre" placeholder="Nombre sitio">
                                        <input value="<?php echo $id_sitio; ?>" type="hidden" name="id_sitio">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Ip sitio</label>
                                        <input value="<?php echo $ip; ?>" type="text" class="form-control" name="ip" placeholder="">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Puerto sitio</label>
                                        <input value="<?php echo $puerto; ?>" type="text" class="form-control" name="puerto" placeholder="">
                                    </div>

                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-primary" type="button" id="btnUpdateSitio"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </form>
<?php
}
?>