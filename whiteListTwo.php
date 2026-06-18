<?php
    include_once 'includes/config.php';
    include_once 'includes/security.php';
    
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
        </style>
    </head>
    <body class="hold-transition skin-blue layout-top-nav">
        <!-- Site wrapper -->
        <div class="wrapper">
            <header class="main-header">
                <nav class="navbar navbar-static-top">
                    <div class="container">
                        <div class="navbar-header">
                            <a href="inicio.php" class="navbar-brand">
                                <b><img src="img/logo_icono.png" style="margin-top: -7px;">
                            </a>
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                            </button>
                        </div>
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                            <ul class="nav navbar-nav">
                                <li>
                                    <a href="inicio.php">
                                    <i class="fa fa-home"></i> 
                                    Inicio
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="whiteListTwo.php">
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
                        Lista blanca
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="whiteListTwo.php"><i class="fa fa-list"></i></a></li>
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
                        </div>
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">IP</th>
                                        <th class="text-center">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $query = mysqli_query($link,"SELECT * FROM whitelist 
                                            WHERE whitelist.activo_ip = 1
                                            AND whitelist.id_usuario = '$id' ");
                                        $i = 1;
                                        while($row = mysqli_fetch_array($query))
                                        {
                                        echo '
                                        <tr>
                                            <td class="text-center">'.$i.'</td>
                                            <td class="text-center">'.$row['ip_white'].'</td>
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
                                                            data-ip="'.$row['ip_white'].'" 
                                                            data-id="'.$row['id_white'].'" class="btn1">
                                                            <i class="fa fa-pencil"></i> Editar
                                                          </a>
                                                        </li>
                                                        <li>
                                                            <a href="#0" data-id="'.$row['id_white'].'" class="btnE"><i class="fa fa-trash"></i> Eliminar</a>
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
                //Eliminar la ip
                $(document).on('click', '.btnE', function(e){
                  e.preventDefault();
                  var id_ip = $(this).data('id');
                  var parametro = {
                    "id_ip" : id_ip
                  }
                  $.ajax({
                    type: 'POST',
                    url: 'controller/e_white_ip.php',
                    data: parametro,
                    success: function(data) {
                      if (data == 'bien') {
                          not4();
                          setTimeout("location.href = 'whiteListTwo.php'",2000);
                      } else{
                        not5();
                      }
                    }
                  });      
                });
            
                //guardar la ip
                $(document).on('click', '#btnG', function(e){
                  e.preventDefault();
                  $.ajax({
                    type: 'POST',
                    url: 'controller/g_white_ip.php',
                    data: $('.FormUa').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not1();
                        setTimeout("location.href = 'whiteListTwo.php'",3000);
                      } else{
                        not2();
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
                  $.ajax({
                    type: 'POST',
                    url: 'controller/a_white_ip.php',
                    data: $('.FormUb').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not3();
                        setTimeout("location.href = 'whiteListTwo.php'",3000);
                      }else{
                        alert(data);
                        not2();
                      }
                    }
                  });      
                });


              });
              
        </script>
    </body>
</html>