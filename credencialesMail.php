<?php
    include_once 'includes/config.php';
    include_once 'includes/security.php';
    include_once 'geoIp/geoiploc.php';

    date_default_timezone_set('America/Managua');
    
    session_start();
    $id         = $_SESSION['id_u'];
    $nombre     = $_SESSION['nombre'];
    $apellido   = $_SESSION['apellido'];
    $activo     = $_SESSION['activo'];
    $tipo       = $_SESSION['tipo_usuario'];
    
    /*optener solo el primer nombre y el primer apellido del profesor*/
    $nombre = explode(' ', $nombre);
    @$nombre = $nombre[0];
    
    $apellido = explode(' ', $apellido);
    @$apellido = $apellido[0];
    
    $consult = mysqli_query($link,"SELECT * FROM usuario WHERE id_usuario = '$id'");
    $row = mysqli_fetch_array($consult);
    
    if (empty($id) || empty($activo) || $tipo != 1) {
        header("Location: index.php");
    } else {
        $fechaGuardada = $_SESSION["ultimoAcceso"];
        $ahora = date("Y-n-j H:i:s");
        $tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));
        
        //echo $tiempo_transcurrido;
        //comparamos el tiempo transcurrido
        if($tiempo_transcurrido >= 1200) {
            //si pasaron 20 minutos o más
            session_destroy(); // destruyo la sesión
            session_start();
            $_SESSION['nombre_usuario']     = $nombre;
            $_SESSION['apellido_usuario']   = $apellido;
            $_SESSION['correo_usuario']     = $row['email_u'];

            header("Location: lockscreen.php"); //envío al usuario a la pag. de autenticación
            //sino, actualizo la fecha de la sesión
        } else {
            $_SESSION["ultimoAcceso"] = $ahora;
        }
    }
    ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Configuración de correo</title>
        <!-- Favicons -->
        <link rel="shortcut icon" href="img/logo_icono.png">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="assets/bower_components/font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="assets/bower_components/Ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="assets/dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
            folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="assets/dist/css/skins/_all-skins.min.css">
        <!-- Pace style -->
        <link rel="stylesheet" href="assets/plugins/pace/pace.min.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        
        <link rel="stylesheet" href="css/cerulean/bootstrap.css" media="screen">

        <!-- DataTables -->
        <link rel="stylesheet" href="assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
        <script type="text/javascript" src="js/notifIt.js"></script>
        <link rel="stylesheet" type="text/css" href="css/notifIt.css">

        <link rel="stylesheet" type="text/css" href="assets/helpers/boilerplate.css">
        <link rel="stylesheet" type="text/css" href="assets/helpers/grid.css">
        <link rel="stylesheet" type="text/css" href="assets/helpers/utils.css">
        <!-- ICONS -->
        <link rel="stylesheet" type="text/css" href="assets/icons/fontawesome/fontawesome.css">
        <!-- SNIPPETS -->
        <link rel="stylesheet" type="text/css" href="assets/snippets/user-profile.css">
        <link rel="stylesheet" type="text/css" href="assets/snippets/mobile-navigation.css">
        <!-- Frontend theme -->
        <link rel="stylesheet" type="text/css" href="assets/themes/frontend/layout.css">
        <!-- <link rel="stylesheet" type="text/css" href="assets/themes/frontend/color-schemes/default.css"> -->
        <!-- Components theme -->
        <link rel="stylesheet" type="text/css" href="assets/themes/components/default.css">
        <!-- Frontend responsive -->
        <link rel="stylesheet" type="text/css" href="assets/helpers/frontend-responsive.css">

        <script>
            function not1(){
                notif({
                    msg: "Se guardo correctamente",
                    type: "success",
                    position: "center"
                });
            }
        </script>
        <script>
            function not2(){
                notif({
                    msg: "Algunos campos estan vacios",
                    type: "error",
                    position: "center"
                });
            }
        </script>
        <script>
            function not3(){
                notif({
                    msg: "Los datos se actualizarón correctamente",
                    type: "success",
                    position: "center"
                });
            }
        </script>
        <script>
            function not4(){
                notif({
                    msg: "Se elimino correctamente",
                    type: "success",
                    position: "center"
                });
            }
        </script>
        <script>
            function not5(){
                notif({
                    msg: "Error! algo salio mal",
                    type: "error",
                    position: "center"
                });
            }
        </script>
        <script>
            function not7(){
                notif({
                    msg: "Correo Enviado!",
                    type: "error",
                    position: "center"
                });
            }
        </script>

        <style type="text/css">
            .skin-blue .main-header .navbar{
                background-image: url('img/Heater_waf-2022.png');
            }

            .main-header .header-logo {
                background: url(img/logo_icono.png) left 50% no-repeat;
            }
        </style>

        <style type="text/css">
            .table {
                color: #495057 !important;
            }
            /* Fonts weight */
            .tabs-nav li a,
            .main-header .header-nav > li > a,
            .hero-heading,
            .hero-text,
            h1, h2, h3, h4, h5, h6,
            .main-header .header-nav > li > a,
            .hero-heading,
            .hero-text {
                font-family: "Raleway", "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-weight: 300;
            }

            /* Top bar menu */
            .bg-topbar {
                background: #fff;
                border-bottom-color: #eee;
            }
            /* Main header */

            .bg-header {
                background: #fff;
            }
            .sticky-active .main-header {
                box-shadow: 0 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            /* Header subnav menu */

            .main-header .header-nav > li > ul {
                background: #253035;
            }
            .main-header .header-nav > li > ul li a:hover {
                background: rgba(255,255,255,0.05);
                color: #dce4e8;
            }
        </style>

        <style type="text/css">
            .table {
                color: #495057 !important;
            }
            /* Fonts weight */
            .tabs-nav li a,
            .main-header .header-nav > li > a,
            .hero-heading,
            .hero-text,
            h1, h2, h3, h4, h5, h6,
            .main-header .header-nav > li > a,
            .hero-heading,
            .hero-text {
                font-family: "Raleway", "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-weight: 300;
            }

            /* Top bar menu */
            .bg-topbar {
                background: #fff;
                border-bottom-color: #eee;
            }
            /* Main header */

            .bg-header {
                background: #fff;
            }
            .sticky-active .main-header {
                box-shadow: 0 0 1px 2px rgba(0, 0, 0, 0.05);
            }
            /* Header subnav menu */

            .main-header .header-nav > li > ul {
                background: #253035;
            }
            .main-header .header-nav > li > ul li a:hover {
                background: rgba(255,255,255,0.05);
                color: #dce4e8;
            }
        </style>
    </head>
    <body class="hold-transition skin-blue layout-top-nav">
        <!-- Site wrapper -->
        <div class="wrapper">
            <?php include 'includes/navbar.php'; ?>
            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Credenciales
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="credencialesMail.php"><i class="fa fa-envelope"></i> Email</a></li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                    <!-- Default box -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"></h3>
                        </div>
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Dominio</th>
                                        <th>Host</th>
                                        <th>Email</th>
                                        <th>Smtp secure</th>
                                        <th>Port</th>
                                        <th class="text-center">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        //optener todos los usuarios
                                        $query = mysqli_query($link, "SELECT * FROM credentials_email");
                                        while($row = mysqli_fetch_array($query)){
                                        echo '
                                        <tr>
                                          <td>'.$row['dominio_mail'].'</td>
                                          <td>'.$row['host_mail'].'</td>
                                          <td>'.$row['user_email'].'</td>
                                          <td>'.$row['smtp_secure'].'</td>
                                          <td>'.$row['port'].'</td>
                                          <td class="text-center">
                                            <div class="btn-group">
                                              <button type="button" class="btn btn-primary">Opciones</button>
                                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                              </button>
                                              <ul class="dropdown-menu" role="menu">
                                                <li>
                                                  <a href="#0"
                                                    data-toggle="modal"
                                                    data-target="#ModalEU" 
                                                    data-dominio="'.$row['dominio_mail'].'"
                                                    data-nombre="'.$row['user_email'].'" 
                                                    data-host="'.$row['host_mail'].'" 
                                                    data-smtp="'.$row['smtp_secure'].'"
                                                    data-port="'.$row['port'].'"
                                                    data-id="'.$row['id_credential'].'" class="btn1">
                                                    <i class="fa fa-pencil"></i> Editar
                                                  </a>
                                                </li>
                                                <li>
                                                  <a href="#0"
                                                    data-toggle="modal"
                                                    data-target="#ModalEnviarMail"
                                                    data-id="'.$row['id_credential'].'" class="btnTest">
                                                    <i class="fa fa-send"></i> Test correo
                                                  </a>
                                                </li>
                                              </ul>
                                            </div>
                                          </td>
                                        </tr>';
                                        }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                        </div>
                        <!-- /.box-footer-->
                    </div>
                    <!-- /.box -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <!-- Modal editar usuario-->
            <div class="modal fade" id="ModalEU" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Editar Credenciales</h5>
                        </div>
                        <div class="modal-body">
                            <form id="Form1" class="FormU" action="" method="" autocomplete="off">

                                <div class="form-group col-md-6">
                                    <label>Dominio</label>
                                    <input type="text" class="form-control" id="dominio" name="dominio" placeholder="Host">
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label>Host</label>
                                    <input type="text" class="form-control" id="host" name="host" placeholder="Host">
                                    <input type="hidden" name="id_credential" id="id_credential">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>User email</label>
                                    <input type="email" class="form-control" id="nombre" name="nombre" placeholder="User Email">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Password user</label>
                                        <input type="password" id="pass" name="pass" placeholder="Password" class="form-control">
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label>Smtp secure</label>
                                    <input type="text" class="form-control" id="smtp" name="smtp" placeholder="Smtp secure">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Port</label>
                                    <input type="text" class="form-control" id="port" name="port" placeholder="Port">
                                </div>

                                <div class="text-center col-md-12">
                                    <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancel</button>
                                    <button class="btn btn-primary" type="button" id="btnA"><i class="fa fa-save"></i> Save</button>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div>

            <!-- Modal editar usuario-->
            <div class="modal fade" id="ModalEnviarMail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"></h5>
                        </div>
                        <div class="modal-body">
                            <form id="Form1" class="FormEmail" action="" method="" autocomplete="off">

                                <div class="form-group col-md-12">
                                    <label>Email</label>
                                    <input type="mail" class="form-control" id="email" name="email" placeholder="Email">
                                    <input type="hidden" class="form-control" id="id_credencial" name="id_credencial">
                                </div>

                                <div class="text-center col-md-12">
                                    <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancel</button>
                                    <button class="btn btn-primary" type="button" id="btnEnviarTest"><i class="fa fa-send"></i> Enviar</button>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div>
            
            <footer class="main-footer" style="background-image: url('img/Footer_waf-2022.png'); padding: 60px;border-top: 0px solid #d2d6de;background-color: #ecf0f5;border-top: 0px solid #d2d6de; background-color: #ecf0f5;background-size: cover; background-repeat: no-repeat;">
                <div class="pull-right hidden-xs" style="margin-top: 20px;">
                    <strong>
                        <a style="color: #ffffff;" target="_blank" href="https://www.netsoluciones.com">
                        <h5 style="margin-top: 2px;">Diseñado por Netsoluciones</h5>
                        </a>
                    </strong>
                </div>
            </footer>
        </div>
        <!-- ./wrapper -->
        <!-- jQuery 3 -->
        <script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- DataTables -->
        <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <!-- SlimScroll -->
        <script src="assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="assets/bower_components/fastclick/lib/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="assets/dist/js/adminlte.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="assets/dist/js/demo.js"></script>
        <!-- PACE -->
        <script src="assets/bower_components/PACE/pace.min.js"></script>

        <!-- Skrollr -->
        <script type="text/javascript" src="assets/widgets/skrollr/skrollr.js"></script>

        <!-- HG sticky -->
        <script type="text/javascript" src="assets/widgets/sticky/sticky.js"></script>

        <!-- WOW -->
        <script type="text/javascript" src="assets/widgets/wow/wow.js"></script>

        <!-- Theme layout -->
        <script type="text/javascript" src="assets/themes/frontend/layout.js"></script>

        
        <script type="text/javascript">
            // $(document).ajaxStart(function () {
           //      Pace.restart()
           // });
        </script>
        <script>
            $(document).ready(function () {
              $('.sidebar-menu').tree()
            })
        </script>
        <!-- page script -->
        <script>
            $(function () {
              $('#example1').DataTable()
              $('#example2').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : false,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : false
              })
            })
        </script>
        <script type="text/javascript">
            $(document).ready(function(){

                //mostrar modal
                $(document).on('click', '.btnTest', function(e){
                    e.preventDefault();
                    var id_credential = $(this).data('id');
                    $('#id_credencial').val(id_credential);
                });

                //enviar mail
                $(document).on('click', '#btnEnviarTest', function(e){
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'controller/ajax_send_testing.php',
                        data: $('.FormEmail').serialize(),
                        success: function(data) {
                            if (data == 'bien') {
                                not7();
                                setTimeout("location.href = 'credencialesMail.php'",3000);
                            } else {
                                // alert(data);
                                not2();
                            }
                        }
                    });   
                });

                //mostrar la info
                $(document).on('click', '.btn1', function(e){
                  e.preventDefault();
                  var nombre = $(this).data('nombre');
                  var dominio = $(this).data('dominio');
                  var host = $(this).data('host');
                  var smtp = $(this).data('smtp');
                  var port = $(this).data('port');
                  var id_credential = $(this).data('id');

                  $('#dominio').val(dominio);
                  $('#nombre').val(nombre);
                  $('#host').val(host);
                  $('#smtp').val(smtp);
                  $('#port').val(port);
                  $('#id_credential').val(id_credential);
                });
            
                //editar usuario
                $(document).on('click', '#btnA', function(e){
                  e.preventDefault();
                  $.ajax({
                    type: 'POST',
                    url: 'controller/a_credenciales.php',
                    data: $('.FormU').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not3();
                        setTimeout("location.href = 'credencialesMail.php'",3000);
                      }else{
                        not2();
                      }
                    }
                  });      
                });
              });
              
        </script>
    </body>
</html>