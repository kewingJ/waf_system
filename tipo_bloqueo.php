<?php
    include_once 'includes/config.php';
    include_once 'includes/security.php';
    include_once 'geoIp/geoiploc.php';
    
    session_start();
    $id = $_SESSION['id_u'];
    $nombre = $_SESSION['nombre'];
    $apellido = $_SESSION['apellido'];
    $activo = $_SESSION['activo'];
    
    /*optener solo el primer nombre y el primer apellido del profesor*/
    $nombre = explode(' ', $nombre);
    @$nombre = $nombre[0];
    
    $apellido = explode(' ', $apellido);
    @$apellido = $apellido[0];

    //se optiene el nombre del tipo de ataque
    $tipo_ataque = $_GET['tipo!'];
    $fecha1 = $_GET['fecha1!'];
    $fecha2 = $_GET['fecha2!'];
    
    $consult = mysqli_query($link,"SELECT * FROM usuario WHERE id_usuario = '$id'");
    $row = mysqli_fetch_array($consult);
    
    if (empty($id) || empty($activo)) {
        header("Location: index.php");
    }
    ?>
<!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Tipo Bloqueo</title>
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
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php echo $tipo_ataque; ?>
                    </h1>
                </section>
                <!-- Main content -->
                <section class="content">
                    <!-- Custom Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">

                            <li class="active">
                                <a href="#tab_1" data-toggle="tab">
                                <i class="fa fa-list"></i> Lista de bloqueos
                                </a>
                            </li>

                            <li>
                                <a href="#tab_4" data-toggle="tab" id="graficoT">
                                <i class="fa fa-star"></i> Top 10 de ataques por pais
                                </a>
                            </li>

<!--                             <li>
                                <a href="#tab_2" data-toggle="tab" id="grafico">
                                <i class="fa fa-pie-chart"></i> Tipos de ataques
                                </a>
                            </li>

                            <li>
                                <a href="#tab_3" data-toggle="tab" id="graficoP">
                                <i class="fa fa-pie-chart"></i> Ataques por paises
                                </a>
                            </li> -->

                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">
                                <label>Lista de Host</label>
                                <select id="dropdown2">
                                 <option value="">Todos los Host</option>
                                 <?php
                                    $consult = mysqli_query($link,"SELECT DISTINCT server FROM bloqueo
                                                                INNER JOIN detalle_rule 
                                                                ON bloqueo.idN = detalle_rule.numero_rule_detalle 
                                                                INNER JOIN rules 
                                                                ON detalle_rule.id_rule = rules.id_rule 
                                                                WHERE bloqueo.activo_bloqueo = 1 
                                                                AND rules.nombre_rule = '$tipo_ataque'");
                                    while($row = mysqli_fetch_array($consult)){
                                        echo '<option value="'.$row['server'].'">'.$row['server'].'</option>';
                                    }
                                 ?>
                                </select>
                                <table id="example2" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha de bloqueo</th>
                                            <th>ip</th>
                                            <th>Host</th>
                                            <th>Url</th>
                                            <th>Tipo de ataque</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(!empty($fecha1) && !empty($fecha2)){
                                            $query = mysqli_query($link,"SELECT * FROM bloqueo 
                                                                INNER JOIN detalle_rule 
                                                                ON bloqueo.idN = detalle_rule.numero_rule_detalle 
                                                                INNER JOIN rules 
                                                                ON detalle_rule.id_rule = rules.id_rule 
                                                                WHERE bloqueo.activo_bloqueo = 1 
                                                                AND bloqueo.fecha_bloqueo BETWEEN '$fecha1' AND '$fecha2'
                                                                AND rules.nombre_rule = '$tipo_ataque'");
                                        } else {
                                            $query = mysqli_query($link,"SELECT * FROM bloqueo 
                                                                INNER JOIN detalle_rule 
                                                                ON bloqueo.idN = detalle_rule.numero_rule_detalle 
                                                                INNER JOIN rules 
                                                                ON detalle_rule.id_rule = rules.id_rule 
                                                                WHERE bloqueo.activo_bloqueo = 1
                                                                AND rules.nombre_rule = '$tipo_ataque'");
                                        }
                                        $i = 1;
                                        while($rows = mysqli_fetch_array($query))
                                        {
                                            $ip_bandera = $rows['ip'];
                                            if (!empty($ip_bandera)) {
                                                $codigo_ip = getCountryFromIP($ip_bandera, "code");

                                                $bandera = '<span class="f16"><i class="flag '.strtolower($codigo_ip).' icono-bandera"></i></span>';
                                            } else {
                                                $bandera = '<span class="f16"><i class="flag ac icono-bandera"></i></span>';
                                            }

                                            $ataque = strtolower($rows['tipo_ataque']);
                                            $url = "";

                                            if ($ataque == 'xss' || $ataque == 'Cross Site Scripting') {
                                                $url = "https://es.wikipedia.org/wiki/Cross-site_scripting";
                                            } else if($ataque == 'sqli' || $ataque == 'sql injections'){
                                                $url = "https://es.wikipedia.org/wiki/Inyecci%C3%B3n_SQL";
                                            } else if($ataque == 'directory traversal'){
                                                $url = "https://en.wikipedia.org/wiki/Directory_traversal_attack";
                                            } else if($ataque == 'internal rules'){
                                                $url = "#";
                                            } else if($ataque == 'rfi'){
                                                $url = "https://es.wikipedia.org/wiki/Remote_File_Inclusion";
                                            } else {
                                                $url = "#";
                                            }

                                            echo '
                                            <tr>
                                                <td>'.$rows['id_bloqueo'].'</td>
                                                <td><i class="fa fa-clock-o"></i> '.$rows['fecha_bloqueo'].'</td>
                                                <td>'.$bandera.' '.$rows['ip'].'</td>
                                                <td><i class="fa fa-server"></i> '.$rows['server'].'</td>
                                                <td>'.$rows['url'].'</td>
                                                <td><a href="'.$url.'" target="_black">'.$rows['tipo_ataque'].'</a></td>
                                            </tr>';
                                        }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                             <!-- /.tab-pane -->
                            <!-- <div class="tab-pane" id="tab_2">
                                <div class="example-box-wrapper clearfix">
                                    <div id="" class="chart1"></div>
                                </div>
                            </div> -->

                            <!-- /.tab-pane -->
                            <!-- <div class="tab-pane" id="tab_3">
                                <div class="example-box-wrapper clearfix">
                                    <div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                                </div>
                            </div> -->
                            <!-- /.tab-pane -->

                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="tab_4">
                                <div class="example-box-wrapper clearfix">
                                    <!-- <div id="" class="chart2"></div> -->
                                    <div id="container2" style="min-width: 400px; height: 500px; max-width: 650px; margin: 0 auto"></div>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
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

        <script>
            $(document).ready(function () {
              $('.sidebar-menu').tree()
            });
        </script>

        <!-- page script -->
        <script>
            $(document).ready(function () {
                var table = $('#example2').DataTable();

                $('#dropdown2').on('change', function () {
                    table.columns(3).search( this.value ).draw();
                });
            });
        </script>

        <script>
            $(document).on('click','#grafico',function() {
                $(".chart1").attr("id","chart");
                $(".chart2").attr("id","");
                var chart = c3.generate({
                    data: {
                        columns: [
                        <?php
                            $consult = mysqli_query($link,"SELECT * FROM rules WHERE rules.activo_rule = 1");
                            //optener el total de reglas
                            $total = mysqli_num_rows($consult);
                            $i = 1;
                            while($rows = mysqli_fetch_array($consult))
                            {
                                $total_bloqueos = 0;
                                $i++;
                                $id_rule = $rows['id_rule'];
                                //optener totales de bloqueos por reglas
                                $consult2 = mysqli_query($link,"SELECT * FROM bloqueo 
                                                            INNER JOIN detalle_rule 
                                                            ON bloqueo.idN = detalle_rule.numero_rule_detalle 
                                                            INNER JOIN rules 
                                                            ON detalle_rule.id_rule = rules.id_rule 
                                                            WHERE bloqueo.activo_bloqueo = 1 AND rules.id_rule = '$id_rule'");
                                $total_bloqueos = mysqli_num_rows($consult2);
                            
                                echo '["'.$rows['nombre_rule'].'", '.$total_bloqueos.' ],';
                            }
                            ?>
                        ],
                        type : 'pie',
                        onclick: function (d, i) { 
                            //alert(d.id);
                            setTimeout("location.href = 'tipo_bloqueo.php?tipo!="+d.id+"'",100);
                        },
                    },
                    axis: {
                        x: {
                            label: 'Sepal.Width'
                        },
                        y: {
                            label: 'Petal.Width'
                         }
                    }
                });
            });
        </script>

        <script src="js/highcharts.js"></script>
        <script src="js/exporting.js"></script>
        <script src="js/export-data.js"></script>

        <script>
            $(document).on('click','#graficoP',function() {
                Highcharts.chart('container', {
                      chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                      },
                      title: {
                        text: ''
                      },
                      tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                      },
                      plotOptions: {
                        pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                              color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                          }
                        }
                      },
                      series: [{
                        name: 'Paises',
                        colorByPoint: true,
                        point:{
                            events:{
                                click: function (event) {
                                    //alert(this.name);
                                    setTimeout("location.href = 'bloqueo_pais.php?pais!="+this.name+"'",100);
                                }
                            }
                        }, 
                        data: [
                        <?php
                            $consult = mysqli_query($link,"SELECT * FROM bloqueo_pais
                                                    INNER JOIN paises
                                                    ON bloqueo_pais.id_pais = paises.id_pais");
                            
                            while($rows = mysqli_fetch_array($consult))
                            {
                                $nombre_pais = $rows['nombre'];
                                $total_bloqueos = $rows['total_bloqueo'];
                                $codigo_p = $rows['iso'];
                            
                                if ($total_bloqueos > 0) {
                                    echo '{ name: "'.$nombre_pais.'", y:'.$total_bloqueos.'},';
                                }
                            }
                        ?>
                        ]
                      }]
                    });
            });
        </script>

        <script>
            $(document).on('click','#graficoT',function() {
                Highcharts.chart('container2', {
                      chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                      },
                      title: {
                        text: ''
                      },
                      tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                      },
                      plotOptions: {
                        pie: {
                          allowPointSelect: true,
                          cursor: 'pointer',
                          dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                              color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                          }
                        }
                      },
                      series: [{
                        name: 'Top ip bloqueadas',
                        colorByPoint: true,
                        data: [
                        <?php
                            $consult = mysqli_query($link,"SELECT ip, count(ip) as conteo FROM bloqueo GROUP BY ip HAVING COUNT(ip)>1 ORDER BY conteo DESC LIMIT 10");

                            while($rows = mysqli_fetch_array($consult))
                            {
                                $ip_ataque = $rows['ip'];
                                $valor = $rows['conteo'];

                                //
                                $codigo_ip = getCountryFromIP($ip_ataque, "code");
                                
                                $consult_p = mysqli_query($link,"SELECT * FROM paises WHERE iso = '$codigo_ip'");
                                $row = mysqli_fetch_array($consult_p);
                                $nombre_p = $row['nombre'];

                                echo '{ name: "'.$ip_ataque.' ('.$nombre_p.')", y:'.$valor.'},';
                            }
                        ?>
                        ]
                      }]
                    });
            });
        </script>

    </body>
</html>