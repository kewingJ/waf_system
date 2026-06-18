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
        <title>Reglas(Metodos de ataque)</title>
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
            function not6(){
                notif({
                    msg: "Datos actualizados!!",
                    type: "success",
                    position: "center"
                });
            }
        </script>
        
        <link rel="stylesheet" type="text/css" href="css/dropzone.css" />
        <script type="text/javascript" src="js/dropzone.js"></script>
        <style type="text/css">
            .skin-blue .main-header .navbar{
                background-image: url('img/Heater_waf-2022.png');
            }
            .file_upload{
                border: 4px dashed #292929;
            }

            .main-header .header-logo {
                background: url(img/logo_icono.png) left 50% no-repeat;
            }

            td.details-control {
                background: url('img/details_open.png') no-repeat center center;
                cursor: pointer;
            }
            tr.details td.details-control {
                background: url('img/details_close.png') no-repeat center center;
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
                        Reglas
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="rules.php"><i class="fa fa-code"></i> Reglas</a></li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_1" data-toggle="tab">
                                <i class="fa fa-list"></i> Reglas
                                </a>
                            </li>

                            <li>
                                <a href="#tab_2" data-toggle="tab">
                                <i class="fa fa-shield"></i> Procesar Log
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">

                        <div class="tab-pane active" id="tab_1">
                            <div>
                                <div class="box-header with-border">
                                    <h3 class="box-title">Lista de reglas</h3>
                                    <button data-toggle="modal" data-target="#ModalNU" type="button" class="btn btn-success pull-right">
                                    <i class="fa fa-plus"></i> Nueva regla
                                    </button>

                                    <button data-toggle="modal" style="margin: 0px 10px;" data-target="#ModalNRegla" type="button" class="btn btn-success pull-right">
                                    <i class="fa fa-plus"></i> Procesar Reglas
                                    </button>
                                </div>
                                <div class="box-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th>Rango</th>
                                                <th class="text-center">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $query = mysqli_query($link,"SELECT * FROM rules WHERE rules.activo_rule = 1");
                                                $i = 1;
                                                while($row = mysqli_fetch_array($query))
                                                {
                                                echo '
                                                <tr>
                                                    <td class="details-control" data-id="'.$row['id_rule'].'"></td>
                                                    <td>'.$i.'</td>
                                                    <td>'.$row['nombre_rule'].'</td>
                                                    <td>'.$row['inicio_rule'].' - '.$row['fin_rule'].'</td>
                                                    <td class="text-center">
                                                        <div class="btn-group dropup">
                                                            <button type="button" class="btn btn-primary">Opciones</button>
                                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                                                <span class="caret"></span>
                                                                <span class="sr-only">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li>
                                                                <a href="#0"
                                                                    data-toggle="modal"
                                                                    data-target="#ModalER" 
                                                                    data-nombre="'.$row['nombre_rule'].'" 
                                                                    data-inicio="'.$row['inicio_rule'].'" 
                                                                    data-fin="'.$row['fin_rule'].'"
                                                                    data-id="'.$row['id_rule'].'" class="btn1">
                                                                    <i class="fa fa-pencil"></i> Editar
                                                                </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#0" data-id="'.$row['id_rule'].'" class="btnE"><i class="fa fa-trash"></i> Eliminar</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>';
                                                $i++;
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
                        </div>
                        <div class="tab-pane" id="tab_2">
                            <div class="box-header with-border">
                                <h3 class="box-title">Procesar log</h3>
                                <button data-toggle="modal" data-target="#ModalNLog" type="button" class="btn btn-success pull-right">
                                <i class="fa fa-plus"></i> Subir archivo
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            <!-- Modal nuevo log-->
            <div class="modal fade" id="ModalNLog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Subir Log</h5>
                        </div>
                        <div class="modal-body">
                            <div class="file_upload">
                                <form action="file_upload.php" class="dropzone">
                                    <div class="dz-message needsclick">
                                        <strong>Arrastra archivos a cualquier lugar para subirlos.</strong><br /><br />
                                        <span class="note needsclick">
                                            <span class="glyphicon glyphicon-open" aria-hidden="true" style="font-size:60px;"></span>
                                        </span>
                                    </div>
                                </form>		
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="text-center col-md-12">
                                <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                <button class="btn btn-primary" type="button" id="btnProcesar"><i class="fa fa-save"></i> Procesar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal nueva reglas desde el log-->
            <div class="modal fade" id="ModalNRegla" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Subir Regla</h5>
                        </div>
                        <div class="modal-body">
                            <div class="file_upload">
                                <form action="file_upload.php" class="dropzone">
                                    <div class="dz-message needsclick">
                                        <strong>Arrastra archivos a cualquier lugar para subirlos.</strong><br /><br />
                                        <span class="note needsclick">
                                            <span class="glyphicon glyphicon-open" aria-hidden="true" style="font-size:60px;"></span>
                                        </span>
                                    </div>
                                </form>		
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="text-center col-md-12">
                                <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                <button class="btn btn-primary" type="button" id="btnProcesarRules"><i class="fa fa-save"></i> Procesar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal nueva regla-->
            <div class="modal fade" id="ModalNU" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Nueva regla</h5>
                        </div>
                        <div class="modal-body">
                            <form id="Form1" class="FormUa" action="" method="" autocomplete="off">
                                <div class="form-group col-md-12">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" name="nombre" placeholder="Nombre">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Inicio</label>
                                    <input type="text" class="form-control" name="inicio" placeholder="Inicio">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fin</label>
                                    <input type="text" class="form-control" name="fin" placeholder="Fin">
                                </div>
                                <div class="text-center col-md-12">
                                    <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                    <button class="btn btn-primary" type="button" id="btnG"><i class="fa fa-save"></i> Guardar</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div>

            <!-- Modal editar rule-->
            <div class="modal fade" id="ModalER" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Editar Regla</h5>
                        </div>
                        <div class="modal-body">
                            <form id="Form2" class="FormUb" action="" method="" autocomplete="off">
                                <div class="form-group col-md-12">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" id="nombre_rule" name="nombre" placeholder="Nombre">
                                    <input type="hidden" id="id_rule" name="id_rule">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Inicio</label>
                                    <input type="text" class="form-control" id="inicio_rule" name="inicio" placeholder="Inicio">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fin</label>
                                    <input type="text" class="form-control" id="fin_rule" name="fin" placeholder="Fin">
                                </div>
                                <div class="text-center col-md-12">
                                    <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                    <button class="btn btn-primary" type="button" id="btnA"><i class="fa fa-save"></i> Guardar</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div>

            <!-- Modal editar detalle de regla-->
            <div class="modal fade" id="ModalEDetalleRegla" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Editar Detalle de Regla</h5>
                        </div>
                        <div class="modal-body">
                            <form id="Form3" class="FormDR" action="" method="" autocomplete="off">
                                <div class="form-group col-md-6">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" id="nombre_detalle_rule" name="nombreDetalle" placeholder="Nombre">
                                    <input type="hidden" id="id_detalle_rule" name="id_detalle_rule">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Codigo</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Codigo">
                                </div>
                                <div class="text-center col-md-12">
                                    <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                    <button class="btn btn-primary" type="button" id="btnActualizarDetalle"><i class="fa fa-save"></i> Guardar</button>
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
            var table = $('#example1').DataTable();

            function format (callback, d) {
                //return d[0];
                tbody = '';
                var parametro = {
                    "id_regla" : d
                }
                $.ajax({
                    url:  'controller/return_lista_detalle_regla.php', 
                    type: 'POST',
                    data: parametro,
                    dataType: 'html'
                })
                .done(function(result){
                    tbody = result;
                    callback($('<table id="exampleDetalle" class="table table-bordered table-striped" width="100%"><thead><tr><th>#</th><th>Nombre</th><th>Rango</th><th>Editar</th></tr></thead><tbody>' + tbody + '</tbody></table>')).show();
                })
                .fail(function(){
                    tbody = "";
                });

                //callback($('<table id="exampleDetalle" class="table table-bordered table-striped" width="100%"><thead><tr><th>#</th><th>Nombre</th><th>Rango</th></tr></thead><tbody>' + tbody + '</tbody></table>')).show();
            }

            
            var detailRows = [];
            $('#example1 tbody').on( 'click', 'tr td.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = table.row( tr );
                    var idx = $.inArray( tr.attr('id'), detailRows );
                    var id_id = $(this).data('id');
                    //alert(id_id);
 
                    if ( row.child.isShown() ) {
                        tr.removeClass( 'details' );
                        row.child.hide();
                     
                        // Remove from the 'open' array
                        detailRows.splice( idx, 1 );
                    }
                    else {
                        format(row.child, id_id);
                        tr.addClass( 'details' );

                        // Add to the 'open' array
                        if ( idx === -1 ) {
                            detailRows.push( tr.attr('id') );
                        }
                    }
                });
            })
        </script>
        
        <script type="text/javascript">
            $(document).ready(function(){
                //Eliminar regla
                $(document).on('click', '.btnE', function(e){
                  e.preventDefault();
                  var id_rule = $(this).data('id');
                  var parametro = {
                    "id_rule" : id_rule
                  }
                  $.ajax({
                    type: 'POST',
                    url: 'controller/e_rule.php',
                    data: parametro,
                    success: function(data) {
                      if (data == 'bien') {
                          not4();
                          setTimeout("location.href = 'rules.php'",2000);
                      } else{
                        not5();
                      }
                    }
                  });      
                });
            
                //guardar nueva regla
                $(document).on('click', '#btnG', function(e){
                  e.preventDefault();
                  $.ajax({
                    type: 'POST',
                    url: 'controller/g_rule.php',
                    data: $('.FormUa').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not1();
                        setTimeout("location.href = 'rules.php'",3000);
                      } else{
                        not2();
                      }
                    }
                  });      
                });

                //mostrar la info de regla principal
                $(document).on('click', '.btn1', function(e){
                  e.preventDefault();
                  var nombre = $(this).data('nombre');
                  var inicio = $(this).data('inicio');
                  var fin = $(this).data('fin');
                  var id_rule = $(this).data('id');
                  $('#nombre_rule').val(nombre);
                  $('#inicio_rule').val(inicio);
                  $('#fin_rule').val(fin);
                  $('#id_rule').val(id_rule);
                });

                //editar regla principal
                $(document).on('click', '#btnA', function(e){
                  e.preventDefault();
                  $.ajax({
                    type: 'POST',
                    url: 'controller/a_rule.php',
                    data: $('.FormUb').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not3();
                        setTimeout("location.href = 'rules.php'",3000);
                      }else{
                        alert(data);
                        not2();
                      }
                    }
                  });      
                });

                //mostrar del detalle de la regla
                $(document).on('click', '#btnEditarDetalleRegla', function(e){
                  e.preventDefault();
                  var nombre_detalle_rule = $(this).data('nombre');
                  var codigo = $(this).data('codigo');
                  var id_detalle_rule = $(this).data('id');
                  $('#nombre_detalle_rule').val(nombre_detalle_rule);
                  $('#codigo').val(codigo);
                  $('#id_detalle_rule').val(id_detalle_rule);
                });

                //editar detalle de regla
                $(document).on('click', '#btnActualizarDetalle', function(e){
                  e.preventDefault();
                  $.ajax({
                    type: 'POST',
                    url: 'controller/a_detalle_rule.php',
                    data: $('.FormDR').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        //alert(data);
                        not3();
                        setTimeout("location.href = 'rules.php'",3000);
                      }else{
                        //alert(data);
                        not2();
                      }
                    }
                  });      
                });

              });
              
        </script>
        
        <script type="text/javascript">
            $(document).ready(function(){
                //procesar archivo
                $(document).on('click', '#btnProcesar', function(e){
                    e.preventDefault();
                    $.ajax({
                        url: 'controller/ajax_convertion_dinamico.php',
                        success: function (result) {
                            //alert(result);
                            not6();
                            setTimeout("location.href = 'rules.php'",3000);
                        }
                    }); 
                });
            });
        </script>      

        <script type="text/javascript">
            $(document).ready(function(){
                //procesar archivo
                $(document).on('click', '#btnProcesarRules', function(e){
                    e.preventDefault();
                    $.ajax({
                        url: 'controller/ajax_rules.php',
                        success: function (result) {
                            //alert(result);
                            not6();
                            setTimeout("location.href = 'rules.php'",3000);
                        }
                    }); 
                });
            });
        </script>      
    </body>
</html>