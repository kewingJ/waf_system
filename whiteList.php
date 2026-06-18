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
        <title>White List</title>
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
                        Lista blanca
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="whiteList.php"><i class="fa fa-list"></i></a></li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                    <!-- Default box -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Lista de IP</h3>
                            <button data-toggle="modal" data-target="#ModalNU" type="button" class="btn btn-success pull-right">
                                <i class="fa fa-plus"></i> Nueva IP
                            </button>

                            <button style="margin: 0px 10px;" id="aplicarCambios" type="button" class="btn btn-success pull-right">
                                <i class="fa fa-code"></i> Aplicar cambios
                            </button>
                        </div>
                        <div class="box-body">
                            <div class="text-center">
                                <div class="btn-group">
                                    <a href="#0" id="btn_eliminar" class="btn btn-danger btnDelete" style="display: none;">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </a>
                                    <a href="#0" id="btn_eliminar_todos" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> Eliminar todos
                                    </a>
                                </div>
                            </div>
                            <table id="example1" class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <input id="scales_check" class="editor-active" type="checkbox" name="scales_check" data-id="1">
                                        </th>
                                        <th class="text-center">#</th>
                                        <th class="text-center">IP</th>
                                        <th class="text-center">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                        </div>
                        <!-- /.box-footer-->
                        <div class="overlay" id="overlayListaIP" style="display: none;">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </div>
                    <!-- /.box -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            
            <!-- Modal nueva ip-->
            <div class="modal fade" id="ModalNU" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Nueva ip</h5>
                        </div>
                        <div class="modal-body">
                            <form id="Form1" class="FormUa" action="" method="" autocomplete="off">
                                <div class="form-group col-md-12">
                                    <label>Dirección ip</label>
                                    <input type="text" class="form-control" name="ip" placeholder="Dirección ip">
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

            <!-- Modal editar ip-->
            <div class="modal fade" id="ModalER" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Editar ip</h5>
                        </div>
                        <div class="modal-body">
                            <form id="Form2" class="FormUb" action="" method="" autocomplete="off">
                                <div class="form-group col-md-12">
                                    <label>Dirección ip</label>
                                    <input type="text" class="form-control" id="ip_edit" name="ip" placeholder="Dirección ip">
                                    <input type="hidden" id="id_ip" name="id_ip">
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

        <!-- table principal -->
        <script type="text/javascript">
            $(document).ready(function(){
                var contador = 0;
                var columns = [
                    {
                        "targets": 0,
                        "render": function (data, type, row, meta) {
                            return '<input id="scales" class="editor-active scales-'+ contador++ +'" type="checkbox" name="scales" data-id="'+row[0]+'">';
                        },
                        className: "dt-body-center text-center"
                    }, {
                        "targets": 1,
                        "render": function (data, type, row, meta) {
                            return row[1];
                        },
                        className: "dt-body-center text-center"
                    }, {
                        "targets": 2,
                        "render": function (data, type, row, meta) {
                            return row[2];
                        },
                        className: "dt-body-center text-center"
                    }, {
                        "targets": 3,
                        "render": function (data, type, row, meta) {
                            return '<div class="btn-group dropup"><button type="button" class="btn btn-primary">Opciones</button><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu" role="menu"><li><a href="#0" data-toggle="modal" data-target="#ModalER" data-ip="'+row[2]+'" data-id="'+row[3]+'" class="btn1"><i class="fa fa-pencil"></i> Editar</a></li><li><a href="#0" data-id="'+row[3]+'" class="btnE"><i class="fa fa-trash"></i> Eliminar</a></li></ul></div>';
                        },
                        className: "dt-body-center text-center"
                    }
                ];

                let idEspejo = [];
                
                //check las primeras 10 filas
                $(document).on('change', '#scales_check', function(e){
                    e.preventDefault();

                    checked = $(this).prop('checked');

                    var cols = table1.column(0).nodes(),
                        state = this.checked;

                    if(checked == true) {

                        for (var i = 0; i < cols.length; i ++) {
                            cols[i].querySelector("#scales").checked = state;
                            var id_cdr = $('.scales-'+i).data('id');
                            
                            if(idEspejo.includes(id_cdr) == false){
                                idEspejo.push(id_cdr);
                            }
                        }

                    } else {
                        for (var i = 0; i < cols.length; i ++) {
                            cols[i].querySelector("#scales").checked = false;
                            var id_cdr = $('.scales-'+i).data('id');
                            
                            let pos = idEspejo.indexOf(id_cdr);
                            idEspejo.splice(pos, 1);
                        }
                    }

                    //mostra boton eliminar
                    if(idEspejo.length > 0) {
                        $('.btnDelete').css("display", "block");
                    } else {
                        $('.btnDelete').css("display", "none");
                    }
                    //console.log(id_cdr);
                    console.log(idEspejo);
                });

                $(document).on('change', '#scales', function(e){
                    e.preventDefault();
                    var id_cdr = $(this).data('id');
                    cb = $(this).prop('checked');
                    if(cb == true) {
                        if(idEspejo.includes(id_cdr) == false){
                            idEspejo.push(id_cdr);
                        }
                    } else {
                        let pos = idEspejo.indexOf(id_cdr);
                        idEspejo.splice(pos, 1);
                    }
                    //mostra boton eliminar
                    if(idEspejo.length > 0) {
                        $('.btnDelete').css("display", "block");
                    } else {
                        $('.btnDelete').css("display", "none");
                    }
                    // console.log(cb);
                    // console.log(id_cdr);
                    // console.log(idEspejo);
                });

                //opcion eliminar
                $(document).on('click', '#btn_eliminar', function(e){
                    e.preventDefault();
                    var id_ip = idEspejo;
                    var parametro = {
                        "id_ip" : id_ip
                    }
                    //alert(id_ip);
                    $.ajax({
                        type: 'POST',
                        url: 'controller/e_lista_ip.php',
                        data: parametro,
                        success: function(data) {
                            if (data == 'bien') {
                                //alert(data);
                                not4();
                                setTimeout("location.href = 'whiteList.php'",2000);
                            }else {
                                //alert(data);
                                not5();
                            }
                        }
                    }); 
                });

                var table1 = $('#example1').DataTable({
                     "fixedHeader": true,
                     "searching": true,
                     "columns": columns,
                     "processing": true,
                     "serverSide": true,
                     "ajax": "ajax_table/ajax_table_white_ip.php",
                });
            });
        </script>

        <!-- para eliminar todos -->
        <script type="text/javascript">
            $(document).ready(function(){
                $(document).on('click', '#btn_eliminar_todos', function(e){
                    e.preventDefault();
                    //alert(id_cdr);
                    $.ajax({
                        type: 'POST',
                        url: 'controller/e_all_white_ip.php',
                        success: function(data) {
                            if (data == 'bien') {
                                //alert(data);
                                not4();
                                setTimeout("location.href = 'whiteList.php'",2000);
                            }else {
                                //alert(data);
                                not5();
                            }
                        }
                    }); 
                });
            });
        </script>
        
        <script type="text/javascript">
            $(document).ready(function(){
                //Eliminar la ip
                $(document).on('click', '.btnE', function(e){
                  e.preventDefault();
                  var id_ip = $(this).data('id');
                  var parametro = {
                    "id_ip" : id_ip
                  }
                  $("#overlayListaIP").css("display", "block");
                  $.ajax({
                    type: 'POST',
                    url: 'controller/e_white_ip.php',
                    data: parametro,
                    success: function(data) {
                      if (data == 'bien') {
                          not4();
                          $("#overlayListaIP").css("display", "none");
                          setTimeout("location.href = 'whiteList.php'",2000);
                      } else{
                        not5();
                        $("#overlayListaIP").css("display", "none");
                      }
                    }
                  });      
                });
            
                //guardar la ip
                $(document).on('click', '#btnG', function(e){
                  e.preventDefault();
                  $("#overlayListaIP").css("display", "block");
                  $.ajax({
                    type: 'POST',
                    url: 'controller/g_white_ip.php',
                    data: $('.FormUa').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not1();
                        $("#overlayListaIP").css("display", "none");
                        setTimeout("location.href = 'whiteList.php'",3000);
                      } else{
                        not2();
                        $("#overlayListaIP").css("display", "none");
                      }
                    }
                  });      
                });

                //mostrar la info
                $(document).on('click', '.btn1', function(e){
                  e.preventDefault();
                  var ip = $(this).data('ip');
                  var id_ip = $(this).data('id');
                  $('#ip_edit').val(ip);
                  $('#id_ip').val(id_ip);
                });

                //editar
                $(document).on('click', '#btnA', function(e){
                  e.preventDefault();
                  $("#overlayListaIP").css("display", "block");
                  $.ajax({
                    type: 'POST',
                    url: 'controller/a_white_ip.php',
                    data: $('.FormUb').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not3();
                        $("#overlayListaIP").css("display", "none");
                        setTimeout("location.href = 'whiteList.php'",3000);
                      }else{
                        //alert(data);
                        $("#overlayListaIP").css("display", "none");
                        not2();
                      }
                    }
                  });      
                });

                //ejecutar cambios
                $(document).on('click', '#aplicarCambios', function(e){
                    e.preventDefault();
                    var dato = 'aplicar';
                    var parametro = {
                        "dato" : dato
                    }
                    $("#overlayListaIP").css("display", "block");
                    $.ajax({
                        type: 'POST',
                        url: 'controller/ajax_cambios.php',
                        data: parametro,
                        success: function(data) {
                        if (data == 'bien') {
                            not3();
                            $("#overlayListaIP").css("display", "none");
                            setTimeout("location.href = 'whiteList.php'",3000);
                        }else{
                            //alert(data);
                            $("#overlayListaIP").css("display", "none");
                            not2();
                        }
                        }
                    });      
                    });

              });
              
        </script>
    </body>
</html>