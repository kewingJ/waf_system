<?php
    include_once 'includes/config.php';
    include_once 'includes/security.php';
    include_once 'geoIp/geoiploc.php';
    include_once 'includes/php_file_tree.php';
    
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
    
    $consult = mysqli_query($link,"SELECT * FROM usuario WHERE id_usuario = '$id'");
    $row = mysqli_fetch_array($consult);
    
    if (empty($id) || empty($activo) || $tipo != 1) {
        header("Location: index.php");
    }
    ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Bienvenido <?php echo $nombre.' '.$apellido; ?></title>
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
        <link href="css/flags32-both.css" rel="stylesheet" type="text/css">
        
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

        <script src="js/jquery.min.js" type="text/javascript"></script>
        <script src="js/php_file_tree_jquery.js" type="text/javascript"></script>
    </head>
    
    <body class="hold-transition skin-blue layout-top-nav" onload="loadServer()">
        <!-- Site wrapper -->
        <div class="wrapper">
            <header class="main-header">
                <nav class="navbar navbar-static-top">
                    <div class="container">
                        <div class="navbar-header">
                            <a href="home.php" class="navbar-brand">
                                <b><img src="img/logo_icono.png" style="margin-top: -7px;">
                            </a>
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                            </button>
                        </div>
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                            <ul class="nav navbar-nav">
                                <li class="active">
                                    <a href="home.php">
                                    <i class="fa fa-home"></i> 
                                    Inicio
                                    </a>
                                </li>

                                <li>
                                    <a href="user.php">
                                    <i class="fa fa-users"></i>
                                    Usuarios
                                    </a>
                                </li>

                                <li>
                                    <a href="rules.php">
                                    <i class="fa fa-code"></i>
                                    Reglas(Tipos de ataque)
                                    </a>
                                </li>

                                <li>
                                    <a href="credencialesMail.php">
                                    <i class="fa fa-envelope"></i>
                                    Configuración de correo
                                    </a>
                                </li>

                                <!-- <li>
                                    <a href="sitio.php">
                                    <i class="fa fa-globe"></i>
                                    Nuevo sitio
                                    </a>
                                </li> -->
                                
                                <li>
                                    <a href="whiteList.php">
                                        <i class="fa fa-list"></i>
                                        White List
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- /.navbar-collapse -->
                        <!-- Navbar Right Menu -->
                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav">
                                <!-- User Account Menu -->
                                <li class="dropdown user user-menu">
                                    <!-- Menu Toggle Button -->
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <!-- The user image in the navbar-->
                                        <img src="assets/dist/img/avatar5.png" class="user-image" alt="User Image">
                                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                        <span class="hidden-xs"><?php echo $nombre.' '.$apellido; ?></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <!-- The user image in the menu -->
                                        <li class="user-header">
                                            <img src="assets/dist/img/avatar5.png" class="img-circle" alt="User Image">
                                            <p>
                                                <?php echo $nombre.' '.$apellido; ?>
                                                <small>Usuario</small>
                                            </p>
                                        </li>
                                        <!-- Menu Footer-->
                                        <li class="user-footer">
                                            <div class="pull-right">
                                                <a href="salir.php" class="btn btn-default btn-flat">Salir</a>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!-- /.navbar-custom-menu -->
                    </div>
                    <!-- /.container-fluid -->
                </nav>
            </header>
            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Server
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="home.php"><i class="fa fa-server"></i> Server</a></li>
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                    <!-- Custom Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">

                            <li class="active">
                                <a href="#tab_7" data-toggle="tab">
                                <i class="fa fa-asterisk"></i> Estadistica server
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">

                            <div class="tab-pane active" id="tab_7">
                                <?php
                                    $query = mysqli_query($link,"SELECT * FROM datos_server ORDER BY id_server DESC LIMIT 1");
                                    $rows = mysqli_fetch_array($query);

                                    $m = $rows['memoria'];
                                    $d = $rows['disco'];
                                    $c = $rows['cpu'];

                                    $query = mysqli_query($link,"SELECT * FROM conexiones ORDER BY id_conexion DESC LIMIT 1");
                                    $rows = mysqli_fetch_array($query);

                                    $conexiones = $rows['cantidad'];

                                    //optener desde el archivo
                                    $fp = fopen("connect.txt", "r");
                                    while ($animalinfo = fscanf($fp, "%s\t%s")){
                                        $linea = $animalinfo[0];
                                    }

                                    $porcentaje = $linea;
                                ?>
                                  <!-- TABLE: LATEST ORDERS -->
                                  <div class="box box-info">
                                    <!-- /.box-header -->
                                    <div class="box-body">

                                      <div class="table-responsive col-md-6">
                                        <table class="table no-margin">
                                          <thead>
                                          <tr>
                                            <th></th>
                                            <th></th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <tr>
                                            <td style="width: 10%;">
                                            <i class="fa fa-tasks"></i> Memoria
                                            </td>
                                            <td>
                                              <!-- Progress bars -->
                                              <div class="clearfix">
                                                <small class="pull-right"><?php echo $m.'%'; ?></small>
                                              </div>
                                              <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: <?php echo $m.'%'; ?>;"></div>
                                              </div>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>
                                                <i class="fa fa-database"></i> Disco
                                            </td>
                                            <td>
                                              <!-- Progress bars -->
                                              <div class="clearfix">
                                                <small class="pull-right"><?php echo $d.'%'; ?></small>
                                              </div>
                                              <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: <?php echo $d.'%'; ?>;"></div>
                                              </div>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>
                                                <i class="fa fa-dashboard"></i> CPU
                                            </td>
                                            <td>
                                              <!-- Progress bars -->
                                              <div class="clearfix">
                                                <small class="pull-right"><?php echo $c.'%'; ?></small>
                                              </div>
                                              <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: <?php echo $c.'%'; ?>;"></div>
                                              </div>
                                            </td>
                                          </tr>

                                          <tr>
                                            <td>
                                                <i class="fa fa-exchange"></i> Conexiones
                                            </td>
                                            <td>
                                              <!-- Progress bars -->
                                              <div class="clearfix">
                                                <small class="pull-right"><?php echo $porcentaje; ?></small>
                                              </div>
                                              <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: <?php echo $porcentaje.'%'; ?>;"></div>
                                              </div>
                                            </td>
                                          </tr>
                                          </tbody>
                                        </table>
                                      </div>

                                      <div class="col-md-6">
                                            <canvas id="barChart" style="height:230px"></canvas>
                                      </div>

                                      <div class="col-md-6">
                                            <canvas id="barChart2" style="height:230px"></canvas>
                                      </div>

                                      <!-- /.box -->

                                      <!-- /.table-responsive -->
                                    </div>
                                  </div>
                                  <!-- /.box -->
                            </div>

                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- nav-tabs-custom -->
                </section>
                <!-- /.content -->

            </div>
            <!-- /.content-wrapper
            style="padding: 0px;border-top: 0px solid #d2d6de;background: #ecf0f5;" -->
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

        <!-- Bootstrap WYSIHTML5 -->
        <script src="assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

        <script type="text/javascript">
            function loadServer() {
                var bar = $('#barChart');
                new Chart(bar, {
                    type: 'bar',
                    data: {
                      labels: ["% Memoria", "% Disco", "% CPU"],
                      datasets: [
                        {
                          label: "6 Horas",
                          backgroundColor: "#9dd0f8",
                          data: [<?php echo $m; ?>,<?php echo $d; ?>,<?php echo $c; ?>]
                        }, {
                          label: "24 Horas",
                          backgroundColor: "#f8d49d",
                          data: [<?php echo $m+5; ?>,<?php echo $d-5; ?>,<?php echo $c+3; ?>]
                        }, {
                          label: "48 Horas",
                          backgroundColor: "#f4a9bb",
                          data: [<?php echo $m+7; ?>,<?php echo $d-7; ?>,<?php echo $c+2; ?>]
                        }
                      ]
                    },
                    options: {
                      title: {
                        display: true,
                        text: 'Server'
                      }
                    }
                });

                var bar = $('#barChart2');
                new Chart(bar, {
                    type: 'bar',
                    data: {
                      labels: ["% Conexiones"],
                      datasets: [
                        {
                          label: "6 Horas",
                          backgroundColor: "#9dd0f8",
                          data: [<?php echo $conexiones; ?>]
                        }, {
                          label: "24 Horas",
                          backgroundColor: "#f8d49d",
                          data: [<?php echo $conexiones+1; ?>]
                        }, {
                          label: "48 Horas",
                          backgroundColor: "#f4a9bb",
                          data: [<?php echo $conexiones+2; ?>]
                        }
                      ]
                    },
                    options: {
                      title: {
                        display: true,
                        text: 'Conexiones'
                      }
                    }
                });
            }
        </script>
        
        <script type="text/javascript">
            // $(document).ajaxStart(function () {
           //      Pace.restart()
           // });
        </script>

        <script>
            $(document).ready(function () {
              $('.sidebar-menu').tree()
            });
        </script>

        <script>
            $(document).ready(function () {
                $.ajax({
                  url: 'ajax_server.php',
                })
            });
        </script>

        <script src="js/highcharts.js"></script>
        <script src="js/exporting.js"></script>
        <script src="js/export-data.js"></script>       

    </body>
</html>