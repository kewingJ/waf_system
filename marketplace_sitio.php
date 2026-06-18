<?php
    include_once 'includes/config.php';
    include_once 'includes/security.php';
    date_default_timezone_set('America/Managua');
    
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
        <title>Marketplace</title>
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

        <!-- Select2 -->
        <link rel="stylesheet" href="assets/bower_components/select2/dist/css/select2.min.css">

        <!-- bootstrap datepicker -->
        <link rel="stylesheet" href="assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

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

        <!-- bootstrap wysihtml5 - text editor -->
        <link rel="stylesheet" href="assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

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
        <!-- <link rel="stylesheet" type="text/css" href="assets/themes/components/default.css"> -->

        <link rel="stylesheet" href="https://res.cloudinary.com/dcylypyc6/raw/upload/v1571529281/switch-input-all.min_byyosh.css">
        <link rel="stylesheet" href="css/switch-input-blue_ltmhn2.css">

        <!-- Animate CSS for the css animation support if needed -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />

        <!-- Include SmartWizard CSS -->
        <link href="css/demo.css" rel="stylesheet" type="text/css" />
        <link href="css/smart_wizard_all.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">

        <style>
            .table,
            .chosen-disabled .chosen-single,
            div.selector,
            .content-box-header.bg-default > .ui-tabs-nav li > a,
            .content-box-header.bg-gray > .ui-tabs-nav li > a,
            .content-box-header.bg-white > .ui-tabs-nav li > a,
            .content-box-header > .ui-tabs-nav li.ui-tabs-active > a,
            body .content-box-header > .ui-tabs-nav li.ui-tabs-active > a:hover,
            .pagination > li > a,
            .pagination > li > span,
            .btn-link,
            a {
                color: #8da0aa;
            }
        </style>

        <!-- Frontend responsive -->
        <link rel="stylesheet" type="text/css" href="assets/helpers/frontend-responsive.css">

        <link rel="stylesheet" type="text/css" href="assets/elements/buttons.css">
        <link rel="stylesheet" type="text/css" href="assets/elements/content-box.css">
        <link rel="stylesheet" type="text/css" href="assets/elements/menus.css">
        <link rel="stylesheet" type="text/css" href="assets/elements/social-box.css">

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

        <link rel="stylesheet" type="text/css" href="assets/widgets/multi-select/multiselect.css">


        <style type="text/css">
            .skin-blue .main-header .navbar{
                background-image: url('img/Heater_waf-2022.png');
            }

            .select2-container--default .select2-selection--single{
                border-radius: 0px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered{
                line-height: 22px !important;
            }
        </style>

        <style type="text/css">
            .stepwizard-step p {
                margin-top: 10px;
            }

            .stepwizard-row {
                display: table-row;
            }

            .stepwizard {
                display: table;
                width: 100%;
                position: relative;
            }

            .stepwizard-step button[disabled] {
                opacity: 1 !important;
                filter: alpha(opacity=100) !important;
            }

            .stepwizard-row:before {
                top: 14px;
                bottom: 0;
                position: absolute;
                content: " ";
                width: 100%;
                height: 1px;
                background-color: #ccc;
                z-order: 0;

            }

            .stepwizard-step {
                display: table-cell;
                text-align: center;
                position: relative;
            }

            .btn-circle {
              width: 30px;
              height: 30px;
              text-align: center;
              padding: 6px 0;
              font-size: 12px;
              line-height: 1.428571429;
              border-radius: 15px;
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
                        
                    </h1>
                    <ol class="breadcrumb">
                    </ol>
                </section>
                <!-- Main content -->
                <section class="content">
                     <!-- Custom Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_1" data-toggle="tab">
                                <i class="fa fa-navicon"></i> Menu
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_1">

                            <!-- Sparklines charts -->

                                <script type="text/javascript" src="assets/widgets/charts/sparklines/sparklines.js"></script>
                                <script type="text/javascript" src="assets/widgets/charts/sparklines/sparklines-demo.js"></script>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="profile-box content-box">
                                            <a href="sitio_jitsi.php">
                                                <div class="content-box-header clearfix bg-default">
                                                    <img src="img/jitsi.png" alt="" width="54" class="img-bordered border-green img-circle">
                                                    <div class="user-details">
                                                        Conferencias Jitsi
                                                        <span>Proteger conferencias Jitsi</span>
                                                        <!-- <a href="#" class="btn btn-abs btn-sm btn-sm-abs text-transform-upr font-bold font-size-11 btn-success" title="">Crear nueva</a> -->
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="profile-box content-box">
                                        <a href="zimbraProtection.php">
                                                <div class="content-box-header clearfix bg-default">
                                                    <img src="img/zimbra.png" alt="" width="54" class="img-bordered border-green img-circle">
                                                    <div class="user-details">
                                                        Zimbra protección
                                                        <span>Zimbra protección</span>
                                                        <!-- <a href="#" class="btn btn-abs btn-sm btn-sm-abs text-transform-upr font-bold font-size-11 btn-success" title="">Crear nueva</a> -->
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

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
        <!-- Select2 -->
        <script src="assets/bower_components/select2/dist/js/select2.full.min.js"></script>
        <!-- InputMask -->
        <script src="assets/bower_components/input-mask/jquery.inputmask.js"></script>
        <script src="assets/bower_components/input-mask/jquery.inputmask.date.extensions.js"></script>
        <script src="assets/bower_components/input-mask/jquery.inputmask.extensions.js"></script>
        <!-- PACE -->
        <script src="assets/bower_components/PACE/pace.min.js"></script>

        <!-- bootstrap datepicker -->
        <script src="assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <!-- Bootstrap WYSIHTML5 -->
        <script src="assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

        <!-- Include SmartWizard JavaScript source -->
        <script type="text/javascript" src="js/jquery.smartWizard.min.js"></script>

        <script type="text/javascript" src="js/demo.js"></script>

        <!-- Skrollr -->
        <script type="text/javascript" src="assets/widgets/skrollr/skrollr.js"></script>

        <!-- HG sticky -->
        <script type="text/javascript" src="assets/widgets/sticky/sticky.js"></script>

        <!-- WOW -->
        <script type="text/javascript" src="assets/widgets/wow/wow.js"></script>

        <!-- Theme layout -->
        <script type="text/javascript" src="assets/themes/frontend/layout.js"></script>

        <!--<link rel="stylesheet" type="text/css" href="assets/widgets/multi-select/multiselect.css">-->
        <script type="text/javascript" src="assets/widgets/multi-select/multiselect.js"></script>
        
        <script type="text/javascript">
            // $(document).ajaxStart(function () {
           //      Pace.restart()
           // });

            //Date picker
            $('#datepicker').datepicker({
                autoclose: true,
                format: 'dd/mm/yyyy',
                startDate: '+7d',
            });

            $(function() { "use strict";
                $(".multi-select").multiSelect();
                $(".ms-container").append('<i class="glyph-icon icon-exchange"></i>');
            });
        </script>

        <script>
            $(document).ready(function () {
              $('.sidebar-menu').tree();
              $('#example1').DataTable();
            })
        </script>

        <!-- wizarr -->
        <script type="text/javascript">
            $(document).ready(function () {
                var navListItems = $('div.setup-panel div a'),
                        allWells = $('.setup-content'),
                        allNextBtn = $('.nextBtn');

                allWells.hide();

                navListItems.click(function (e) {
                    e.preventDefault();
                    var $target = $($(this).attr('href')),
                            $item = $(this);

                    if (!$item.hasClass('disabled')) {
                        navListItems.removeClass('btn-primary').addClass('btn-default');
                        $item.addClass('btn-primary');
                        allWells.hide();
                        $target.show();
                        $target.find('input:eq(0)').focus();
                    }
                });

                allNextBtn.click(function(){
                    var curStep = $(this).closest(".setup-content"),
                        curStepBtn = curStep.attr("id"),
                        nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                        curInputs = curStep.find("input[type='radio'],input[type='text'],input[type='url']"),
                        isValid = true;

                    $(".form-group").removeClass("has-error");
                    for(var i=0; i<curInputs.length; i++){
                        if (!curInputs[i].validity.valid){
                            isValid = false;
                            $(curInputs[i]).closest(".form-group").addClass("has-error");
                        }
                    }

                    if (isValid)
                        nextStepWizard.removeAttr('disabled').trigger('click');
                });

                $('div.setup-panel div a.btn-primary').trigger('click');
            });
        </script>

        <!-- Guardar -->
        <script type="text/javascript">
            $(document).ready(function(){
                $(document).on('click', '.btnGsitio', function(e){
                    e.preventDefault();
                    var DataString = $('#form-1').serialize()+'&'+
                              $('#form-2').serialize()+'&'+
                              $('#form-3').serialize()+'&'+
                              $('#form-4').serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'controller/g_sitio_jitsi.php',
                        data: DataString,
                        success: function(data) {
                            if (data == 'bien') {
                                not1();
                                setTimeout("location.href = 'sitio_jitsi.php'",3000);
                            } else {
                                not2();
                                alert(data);
                            }
                        }
                    });      
                });
            });
        </script>

        <!-- Delete -->
        <script type="text/javascript">
            $(document).ready(function(){
                //Eliminar la usuario
                $(document).on('click', '.btn2', function(e){
                  e.preventDefault();
                  var id_sitio = $(this).data('id');
                  var parametro = {
                    "id_sitio" : id_sitio
                  }
                  $.ajax({
                    type: 'POST',
                    url: 'controller/e_sitio_balancer.php',
                    data: parametro,
                    success: function(data) {
                      if (data == 'bien') {
                          not4();
                          setTimeout("location.href = 'sitio_jitsi.php'", 2000);
                      } else{
                        not5();
                      }
                    }
                  });      
                });
            });
        </script>

        <script type="text/javascript">
            function showConfirm() {
                $('#smartwizard').smartWizard("fixHeight"); 
            }

            $(function() {
                // Leave step event is used for validating the forms
                $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIdx, nextStepIdx, stepDirection) {
                    // Validate only on forward movement  
                    if (stepDirection == 'forward') {
                    let form = document.getElementById('form-' + (currentStepIdx + 1));
                    if (form) {
                        if (!form.checkValidity()) {
                        form.classList.add('was-validated');
                        $('#smartwizard').smartWizard("setState", [currentStepIdx], 'error');
                        $("#smartwizard").smartWizard('fixHeight');
                        return false;
                        }
                        $('#smartwizard').smartWizard("unsetState", [currentStepIdx], 'error');
                    }
                    }
                });

                // Step show event
                $("#smartwizard").on("showStep", function(e, anchorObject, stepIndex, stepDirection, stepPosition) {
                    $("#prev-btn").removeClass('disabled').prop('disabled', false);
                    $("#next-btn").removeClass('disabled').prop('disabled', false);
                    if(stepPosition === 'first') {
                        $("#prev-btn").addClass('disabled').prop('disabled', true);
                    } else if(stepPosition === 'last') {
                        $("#next-btn").addClass('disabled').prop('disabled', true);
                    } else {
                        $("#prev-btn").removeClass('disabled').prop('disabled', false);
                        $("#next-btn").removeClass('disabled').prop('disabled', false);
                    }

                    // Get step info from Smart Wizard
                    let stepInfo = $('#smartwizard').smartWizard("getStepInfo");
                    $("#sw-current-step").text(stepInfo.currentStep + 1);
                    $("#sw-total-step").text(stepInfo.totalSteps);

                    if (stepPosition == 'last') {
                    showConfirm();
                    $("#btnFinish").prop('disabled', false);
                    } else {
                    $("#btnFinish").prop('disabled', true);
                    }

                    // Focus first name
                    if (stepIndex == 1) {
                    setTimeout(() => {
                        $('#first-name').focus();
                    }, 0);
                    }
                });

                // Smart Wizard
                $('#smartwizard').smartWizard({
                    selected: 0,
                    // autoAdjustHeight: false,
                    theme: 'arrows', // basic, arrows, square, round, dots
                    transition: {
                    animation:'none'
                    },
                    toolbar: {
                    showNextButton: true, // show/hide a Next button
                    showPreviousButton: true, // show/hide a Previous button
                    position: 'bottom', // none/ top/ both bottom
                    extraHtml: `<button class="btn btn-success btnGsitio" id="btnFinish" disabled>Guardar Datos</button>`
                    },
                    anchor: {
                        enableNavigation: true, // Enable/Disable anchor navigation 
                        enableNavigationAlways: false, // Activates all anchors clickable always
                        enableDoneState: true, // Add done state on visited steps
                        markPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                        unDoneOnBackNavigation: true, // While navigate back, done state will be cleared
                        enableDoneStateNavigation: true // Enable/Disable the done state navigation
                    },
                });

                $("#state_selector").on("change", function() {
                    $('#smartwizard').smartWizard("setState", [$('#step_to_style').val()], $(this).val(), !$('#is_reset').prop("checked"));
                    return true;
                });

                $("#style_selector").on("change", function() {
                    $('#smartwizard').smartWizard("setStyle", [$('#step_to_style').val()], $(this).val(), !$('#is_reset').prop("checked"));
                    return true;
                });

            });
        </script>

    </body>
</html>