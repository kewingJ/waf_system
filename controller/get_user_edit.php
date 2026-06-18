<?php 
	include_once '../includes/config.php';
    include_once '../includes/security.php';
    
    session_start();
    $id = $_SESSION['id_u'];

	if (isset($_POST['id_usuario'])) 
	{
		$id_usuario = $_POST['id_usuario'];
		$queryProject = mysqli_query($link,"SELECT * FROM usuario
                                            WHERE usuario.id_usuario = '$id_usuario'");
		$rowProject = mysqli_fetch_array($queryProject);

        $nombre = $rowProject['nombre_u'];
        $apellido = $rowProject['apellido_u'];
        $correo = $rowProject['email_u'];

        $tipo_usuario = $rowProject['tipo_usuario'];
        if ($tipo_usuario == 2) {
            $queryHost = mysqli_query($link, "SELECT * FROM usuario_host
                                            INNER JOIN host
                                            ON usuario_host.id_host = host.id_host
                                            WHERE usuario_host.id_usuario = '$id_usuario'");
            $rowHost = mysqli_fetch_array($queryHost);
            $id_host = $rowHost['id_host'];
        } else {
            $id_host = 0;
        }
?>

                            <form id="Form1" class="FormU" action="" method="" autocomplete="off">
                                <div class="form-group col-md-6">
                                    <label>Nombres</label>
                                    <input value="<?php echo $nombre; ?>" type="text" class="form-control" name="nombre" placeholder="Nombres">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Apellidos</label>
                                    <input value="<?php echo $apellido; ?>" type="text" class="form-control" name="nombre2" placeholder="Apellidos">
                                    <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Host</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-list"></i>
                                        </div>
                                        <select class="form-control select2" style="width: 100%" name="id_host">
                                            <option value="">Todos los Host</option>
                                            <?php
                                                $query = mysqli_query($link,"SELECT * FROM host");
                                                while ($rows = mysqli_fetch_array($query)){
                                                    if($id_host == $rows['id_host']) {
                                                            echo '<option selected value="'.$rows['id_host'].'">'.$rows['nombre_host'].'</option>';
                                                        } else {
                                                            echo '<option value="'.$rows['id_host'].'">'.$rows['nombre_host'].'</option>';
                                                        }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label>Correo</label>
                                    <input value="<?php echo $correo; ?>" type="email" class="form-control" name="email" placeholder="Email">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Contraseña</label>
                                    <input type="password" class="form-control" name="pass" placeholder="Contraseña">
                                </div>

                                <div class="text-center col-md-12">
                                    <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                    <button class="btn btn-primary" type="button" id="btnA"><i class="fa fa-save"></i> Guardar</button>
                                </div>
                            </form>


                            <script>
                                $(function () {
                                    $('.select2').select2();
                                });
                            </script>

<?php
	}
?>