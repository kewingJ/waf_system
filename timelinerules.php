<?php
    include_once 'includes/config.php';
    include_once 'includes/security.php';
    include_once 'geoIp/geoiploc.php';
    
    session_start();
    $id = $_SESSION['id_u'];
    $nombre = $_SESSION['nombre'];
    $apellido = $_SESSION['apellido'];
    $activo = $_SESSION['activo'];
    $tipo = $_SESSION['tipo_usuario'];
    
    /*optener solo el primer nombre y el primer apellido del profesor*/
    $nombre = explode(' ', $nombre);
    @$nombre = $nombre[0];
    
    $apellido = explode(' ', $apellido);
    @$apellido = $apellido[0];
    
    $consult = mysqli_query($link,"SELECT * FROM usuario
                                INNER JOIN usuario_host
                                ON usuario.id_usuario = usuario_host.id_usuario
                                INNER JOIN host
                                ON usuario_host.id_host = host.id_host
                                WHERE usuario.id_usuario = '$id'");
    $row = mysqli_fetch_array($consult);
    //optener el host del usuario
    $nombre_host = $row['nombre_host'];

    $lineaAux = strstr($nombre_host, 'www.');
	if (!empty($lineaAux)) 
	{
		$host = explode('www.', $lineaAux);
		$nombre_host_master = $host[1];
	} else {
        $nombre_host_master = $nombre_host;
    }
    
    if (empty($id) || empty($activo) || $tipo != 2) {
        header("Location: index.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Notificación</title>
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
        <!-- daterange picker -->
        <link rel="stylesheet" href="assets/bower_components/bootstrap-daterangepicker/daterangepicker.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="assets/dist/css/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
            folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="assets/dist/css/skins/_all-skins.min.css">
        <!-- Pace style -->
        <link rel="stylesheet" href="assets/plugins/pace/pace.min.css">
        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet" href="assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="assets/bower_components/select2/dist/css/select2.min.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <!-- DataTables -->
        <link rel="stylesheet" href="assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="css/cerulean/bootstrap.css" media="screen">
        <script type="text/javascript" src="js/notifIt.js"></script>
        <link rel="stylesheet" type="text/css" href="css/notifIt.css">
        <link rel="stylesheet" type="text/css" href="css/c3.css">
        <!-- <link rel="stylesheet" type="text/css" href="css/default/default.css"> -->
        <link href="css/flags16-both.css" rel="stylesheet" type="text/css">
        <link href="css/flags32-both.css" rel="stylesheet" type="text/css">

        <!-- Morris chart -->
        <link rel="stylesheet" href="assets/bower_components/morris.js/morris.css">

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

        <script>
            function not7(){
                notif({
                    msg: "archivo actualizado!!",
                    type: "success",
                    position: "center"
                });
            }
        </script>

        <style type="text/css">
            .skin-blue .main-header .navbar{
                background-image: url('img/Heater_waf-2022.png');
            }
        </style>

        <style type="text/css">
            .flag.deprecated { color: silver; }
            .flag.island { color: navy; }

            .select2-container--default .select2-selection--single .select2-selection__rendered{
                line-height: 24px;
            }

            .icono-relog{
                float: left;
                font-size: 1.4em;
                padding: 0px 4px;
                color: #212c4c;
            }

            .icono-bandera{
                float: left;
            }
            
            .estiloFila {
                float:left;
                display:inline;
            }
            
            .dosColumnas li {
                width: 50%;
                display: block;
            }

            .tresColumnas li {
                width:33.333%;
            }

            .cuatroColumnas li {
                width:25%;
            }

            .cincoColumnas li {
                width:16.666%;
            }

            .morris-hover-point{
                color: #666 !important;
            }

            .progress{
                margin-bottom: 0px !important;
                width: 18em;
            }

            .small-box>.small-box-footer{
                padding: 0px !important;
            }

            .small-box {
                border-radius: 12px;
            }

            .bg-oscuro-gradient, .bg-teal-gradient {
                background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #ffffff), color-stop(1, #ffffff)) !important;
                background-image: background: -webkit-linear-gradient(bottom, #141e30, #243b55) !important;
                background-image: background: -o-linear-gradient(bottom, #141e30, #243b55) !important;
                background-image: background: linear-gradient(to top, #141e30, #243b55) !important;
                background: -moz-linear-gradient(center bottom, #141e30 0, #243b55 100%) !important;
                background: -o-linear-gradient(#243b55, #141e30) !important;
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#243b55', endColorstr='#141e30', GradientType=0) !important;
                color: #fff;
            }

            #example2_filter {
                display: none !important;
            }

            .nav-tabs-custom>.nav-tabs>li {
                margin-bottom: 0px !important;
                margin-right: 0px !important;
            }

            td.details-control {
                background: url('img/details_open.png') no-repeat center center;
                cursor: pointer;
            }
            tr.details td.details-control {
                background: url('img/details_close.png') no-repeat center center;
            }

            #containerMapa {
                height: 500px; 
                width: 800px; 
                margin: 0 auto; 
            }

            .highcharts-tooltip>span {
                padding: 10px;
                white-space: normal !important;
                width: 200px;
            }

            .loading {
                margin-top: 10em;
                text-align: center;
                color: gray;
            }

            .f32 .flag {
                vertical-align: middle !important;
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

        <link rel="stylesheet" type="text/css" href="//github.com/downloads/lafeber/world-flags-sprite/flags32.css">

        <script src="js/jquery.min.js" type="text/javascript"></script>
        <script src="js/php_file_tree_jquery.js" type="text/javascript"></script>
    </head>
    
    <body class="hold-transition skin-blue layout-top-nav">
        <!-- Site wrapper -->
        <div class="wrapper">
            <?php include 'includes/navbar.php'; ?>
            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Main content -->
                <section class="content">            
                    <!-- row -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- The time line -->
                            <ul class="timeline">
                            <?php
                                $queryNoti = mysqli_query($link,"SELECT * FROM notificacion_regla ORDER BY id_notificacion DESC");
                                while($rows = mysqli_fetch_array($queryNoti))
                                {
                                    $id_rule = $rows['id_regla'];
                                    $activa_noti = $rows['activa_notificacion'];
                                    $id_noti = $rows['id_notificacion'];
                                    //clases de notificacion css
                                    if($activa_noti > 0){
                                        $divDatos = "box-solid";
                                    } else {
                                        $divDatos = "collapsed-box";
                                    }
                                    //datos de las reglas
                                    $queryRule = mysqli_query($link,"SELECT * FROM rules WHERE id_rule = '$id_rule'");
                                    $rowRule = mysqli_fetch_array($queryRule);
                                    echo '
                                    <!-- timeline time label -->
                                    <li class="time-label">
                                        <span class="bg-green">
                                            '.$rows['fecha_registro'].'
                                        </span>
                                    </li>
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    
                                    <li>
                                        <i class="fa fa-code bg-blue"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                            </span>
                                            <h3 class="timeline-header">
                                                <a href="#">Nueva regla</a> '.$rowRule['nombre_rule'].'
                                            </h3>
                                            <div class="timeline-body box '.$divDatos.'">
                                                <div class="box-header with-border">
                                                    <div class="box-tools">';
                                                        if($activa_noti > 0){
                                                            echo '<button class="btn btn-box-tool" style="color: red; font-size: 20px;" data-widget="collapse"><i class="fa fa-minus"></i></button>';
                                                        } else {
                                                            echo '<button class="btn btn-box-tool" style="color: red; font-size: 20px;" data-widget="collapse"><i class="fa fa-plus"></i></button>';
                                                        }
                                                    echo '
                                                    </div><!-- /.box-tools -->
                                                </div>
                                                <div class="box-body no-padding">
                                                    <table class="table table-striped">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nombre</th>
                                                        </tr>';
                                                        
                                                        $queryDetalle = mysqli_query($link,"SELECT * FROM detalle_rule WHERE id_rule = '$id_rule' LIMIT 5");
                                                        while($rowDetalle = mysqli_fetch_array($queryDetalle))
                                                        {
                                                            echo '
                                                            <tr>
                                                                <td>'.$rowDetalle['id_detalle_r'].'</td>
                                                                <td>'.$rowDetalle['nombre_d_r'].'</td>
                                                            </tr>';
                                                        }
                                                    echo '
                                                    </table>
                                                </div><!-- /.box-body -->
                                                <div class="overlay" id="overlay-'.$id_noti.'" style="display: none;">
                                                    <i class="fa fa-refresh fa-spin"></i>
                                                </div>
                                            </div>
                                            <div class="timeline-footer">';
                                                if($activa_noti > 0)
                                                echo '<a class="btn btn-primary btn-xs aplicar-'.$id_noti.'" id="aplicar" data-id="'.$id_noti.'">Aplicar regla</a>';
                                            echo '
                                            </div>
                                        </div>
                                    </li>';
                                }
                            ?>
                                <!-- END timeline item -->
                                <!-- timeline item -->
                                <li>
                                    <i class="fa fa-clock-o bg-gray"></i>
                                </li>
                            </ul>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </section>
                <!-- /.content -->

            </div>
            <!-- /.content-wrapper -->
            
            <footer class="main-footer" style="background-image: url('img/Footer_waf-2022.png'); padding: 100px;border-top: 0px solid #d2d6de;background-color: #ecf0f5;border-top: 0px solid #d2d6de; background-color: #ecf0f5;background-size: cover; background-repeat: no-repeat;">
                <div class="pull-right hidden-xs" style="margin-top: 70px;">
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
        <!-- date-range-picker -->
        <script src="assets/bower_components/moment/min/moment.min.js"></script>
        <script src="assets/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
        <!-- bootstrap datepicker -->
        <script src="assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <!-- SlimScroll -->
        <script src="assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <!-- FastClick -->
        <script src="assets/bower_components/fastclick/lib/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="assets/dist/js/adminlte.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="assets/dist/js/demo.js"></script>
        <!-- charts -->
        <script src="js/d3.v5.min.js" charset="utf-8"></script>
        <script src="js/c3.js"></script>
        <!-- PACE -->
        <script src="assets/bower_components/PACE/pace.min.js"></script>
        <!-- ChartJS -->
        <script src="js/Chart.min.js"></script>
        <!-- Select2 -->
        <script src="assets/bower_components/select2/dist/js/select2.full.min.js"></script>
        <!-- ChartJS -->
        <script src="assets/bower_components/chart.js/Chart.js"></script>
        <!-- jQuery Knob Chart -->
        <script src="assets/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
        <!-- Morris.js charts -->
        <script src="assets/bower_components/raphael/raphael.min.js"></script>
        <script src="assets/bower_components/morris.js/morris.min.js"></script>

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

        <script type="text/javascript">
            $(document).ready(function(){
                $(document).on('click', '#aplicar', function(e){
                    e.preventDefault();
                    var id_noti = $(this).data('id');
                    //alert(id_noti);
                    $("#overlay-"+id_noti).css("display", "block");
                    var parametro = {
                        "id_notificacion" : id_noti
                    }
                    $.ajax({
                        url:  'controller/a_notificacion.php', 
                        type: 'POST',
                        data: parametro,
                        dataType: 'html'
                    })
                    .done(function(data){
                        setTimeout(
                        function() 
                        {
                            $("#overlay-"+id_noti).css("display", "none");
                            $(".aplicar-"+id_noti).css("display", "none");
                        }, 5000);
                        not6();
                        setTimeout("location.href = 'timelinerules.php'",3000);
                    })
                    .fail(function(){
                        setTimeout(
                        function() 
                        {
                            $("#overlay-"+id_noti).css("display", "none");
                            $(".aplicar-"+id_noti).css("display", "none");
                        }, 5000); 
                        not5();
                        setTimeout("location.href = 'timelinerules.php'",3000);
                    });
                });
            });
        </script>   

    </body>
</html>