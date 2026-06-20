<?php
    include_once 'includes/config.php';
    include_once 'includes/security.php';
    include_once 'includes/lisense_cleanup.php';
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

    if (empty($_SESSION['power_action_token'])) {
        $_SESSION['power_action_token'] = bin2hex(random_bytes(32));
    }

    function lisense_parse_conf($path)
    {
        $data = array(
            'type' => '',
            'code' => '',
            'days' => 0,
            'model' => '',
            'status' => '',
            'expires_at' => ''
        );

        if (!is_readable($path)) {
            return $data;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return $data;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '//') === 0 || strpos($line, '#') === 0) {
                continue;
            }

            if (!preg_match('/^([A-Z_]+)\s*=\s*[\'"]?(.*?)[\'"]?\s*;?\s*$/', $line, $matches)) {
                continue;
            }

            $key = strtoupper(trim($matches[1]));
            $value = trim($matches[2]);

            if ($key === 'LISENSE_TYPE') {
                $data['type'] = $value;
            } elseif ($key === 'LISENSE_CODE') {
                $data['code'] = $value;
            } elseif ($key === 'LISENSE_DAYS') {
                $data['days'] = (int) $value;
            } elseif ($key === 'LISENSE_MODEL') {
                $data['model'] = $value;
            } elseif ($key === 'LISENSE_STATUS') {
                $data['status'] = $value;
            } elseif ($key === 'LISENSE_EXPIRES_AT') {
                $data['expires_at'] = str_replace('/', '-', $value);
            }
        }

        if ($data['days'] < 0) {
            $data['days'] = 0;
        }

        return $data;
    }

    function lisense_create_date($dateValue)
    {
        if (empty($dateValue)) {
            return false;
        }

        $dateValue = str_replace('/', '-', trim((string) $dateValue));
        return DateTime::createFromFormat('Y-m-d', $dateValue);
    }

    function lisense_get_days_left($expiresAt)
    {
        if (empty($expiresAt)) {
            return 0;
        }

        $today = new DateTime(date('Y-m-d'));
        $expiry = lisense_create_date($expiresAt);

        if (!$expiry) {
            return 0;
        }

        $expiry->setTime(0, 0, 0);
        if ($expiry < $today) {
            return 0;
        }

        return ((int) $today->diff($expiry)->format('%a')) + 1;
    }

    function lisense_format_display_date($dateValue)
    {
        $date = lisense_create_date($dateValue);
        if (!$date) {
            return '-';
        }

        return $date->format('Y/m/d');
    }

    function lisense_mask_code($code)
    {
        $code = trim((string) $code);
        $len = strlen($code);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }

        return str_repeat('*', max(0, $len - 4)) . substr($code, -4);
    }

    $lisenseConfPath = __DIR__ . '/.lisense.conf';
    $lisenseStatePath = __DIR__ . '/includes/lisense_state.json';
    $lisenseConfigData = lisense_parse_conf($lisenseConfPath);
    $lisenseStateData = array(
        'active' => false,
        'activation_key' => '',
        'model' => '',
        'status' => '',
        'activated_at' => '',
        'expires_at' => '',
        'type' => '',
        'days_total' => (int) $lisenseConfigData['days'],
        'cleanup_executed' => false,
        'cleanup_at' => '',
        'cleanup_error' => ''
    );

    if (is_readable($lisenseStatePath)) {
        $stateRaw = file_get_contents($lisenseStatePath);
        if ($stateRaw !== false) {
            $stateDecoded = json_decode($stateRaw, true);
            if (is_array($stateDecoded)) {
                $lisenseStateData = array_merge($lisenseStateData, $stateDecoded);
            }
        }
    }

    $lisenseDaysLeft = lisense_get_days_left(isset($lisenseStateData['expires_at']) ? $lisenseStateData['expires_at'] : '');

    $lisenseExpired = !empty($lisenseStateData['active']) && $lisenseDaysLeft <= 0;
    if ($lisenseExpired) {
        $lisenseStateData['active'] = false;

        if (empty($lisenseStateData['cleanup_executed'])) {
            $cleanup = lisense_cleanup_all_data($link);
            $lisenseStateData['cleanup_executed'] = !empty($cleanup['success']);
            $lisenseStateData['cleanup_at'] = date('Y-m-d H:i:s');
            $lisenseStateData['cleanup_error'] = !empty($cleanup['success']) ? '' : (isset($cleanup['error']) ? $cleanup['error'] : 'Error al limpiar datos');
        }

        @file_put_contents($lisenseStatePath, json_encode($lisenseStateData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    $lisenseIsActive = !empty($lisenseStateData['active']) && $lisenseDaysLeft > 0;
    $lisenseTypeLabel = !empty($lisenseStateData['type']) ? $lisenseStateData['type'] : $lisenseConfigData['type'];
    $lisenseActivationKey = !empty($lisenseStateData['activation_key']) ? $lisenseStateData['activation_key'] : $lisenseConfigData['code'];
    $lisenseModelLabel = !empty($lisenseStateData['model']) ? $lisenseStateData['model'] : $lisenseConfigData['model'];
    $lisenseStatusLabel = !empty($lisenseStateData['status']) ? $lisenseStateData['status'] : ($lisenseIsActive ? (!empty($lisenseConfigData['status']) ? $lisenseConfigData['status'] : 'Active') : 'Inactive');
    $lisenseActivatedAt = !empty($lisenseStateData['activated_at']) ? $lisenseStateData['activated_at'] : '-';
    $lisenseExpiresAt = !empty($lisenseStateData['expires_at']) ? $lisenseStateData['expires_at'] : '-';
    $lisenseExpiresAtDisplay = lisense_format_display_date($lisenseExpiresAt);
    $lisenseMaskedCode = lisense_mask_code($lisenseConfigData['code']);
    $lisenseLastChecked = date('Y-m-d H:i:s');

    $lisenseModalState = array(
        'active' => $lisenseIsActive,
        'status' => $lisenseStatusLabel,
        'type' => $lisenseTypeLabel,
        'activation_key' => $lisenseActivationKey,
        'model' => $lisenseModelLabel,
        'masked_code' => $lisenseMaskedCode,
        'days_left' => $lisenseDaysLeft,
        'total_days' => (int) $lisenseConfigData['days'],
        'activated_at' => $lisenseActivatedAt,
        'expires_at' => $lisenseExpiresAt,
        'expires_at_display' => $lisenseExpiresAtDisplay,
        'last_checked' => $lisenseLastChecked
    );
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

            .header-nav a[title="Nuevas reglas"] {
                position: relative;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                min-height: 34px;
            }

            .header-nav a[title="Nuevas reglas"] .fa.fa-bell {
                display: inline-block;
                font-family: FontAwesome !important;
                font-style: normal;
                font-weight: normal;
                font-size: 14px;
                line-height: 1;
                color: #6b7584;
                text-rendering: auto;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
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

            .main-header .container {
                width: 100%;
                max-width: none;
                padding-left: 18px;
                padding-right: 18px;
            }

            .main-header .header-logo {
                margin-right: 10px;
            }

            .main-header .header-nav {
                float: left;
            }

            .main-header .header-nav > li {
                float: left;
            }

            .main-header .header-nav > li > a {
                float: left;
                display: block;
                margin: 0 5px;
                padding: 0 4px;
                white-space: nowrap;
                font-size: 12px;
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
            .lisense-help {
                color: #5b6773;
                margin-bottom: 12px;
            }

            .lisense-card {
                background: #f7f9fb;
                border: 1px solid #e2e8ef;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 8px 24px rgba(31, 42, 54, 0.08);
            }

            .lisense-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 16px 18px;
                background: #ffffff;
                border-bottom: 1px solid #e2e8ef;
            }

            .lisense-card-title {
                margin: 0;
                color: #202a35;
                font-size: 17px;
                font-weight: 700;
                letter-spacing: 0;
            }

            .lisense-card-icon {
                width: 34px;
                height: 34px;
                border-radius: 8px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #e9f6ef;
                color: #12844f;
                flex: 0 0 auto;
            }

            .lisense-card-title-wrap {
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 0;
            }

            .lisense-detail-table {
                width: 100%;
                border-collapse: collapse;
                background: transparent;
            }

            .lisense-detail-table tr td {
                border-bottom: 1px solid #e5ebf0;
                padding: 12px 18px;
                font-size: 14px;
                vertical-align: middle;
            }

            .lisense-detail-table tr:last-child td {
                border-bottom: 0;
            }

            .lisense-detail-table td:first-child {
                width: 36%;
                color: #5a6673;
                font-weight: 600;
            }

            .lisense-detail-table td:last-child {
                color: #182333;
                font-weight: 600;
                word-break: break-word;
            }

            .lisense-status-active {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 9px;
                background: #e2943b;
                color: #ffffff !important;
                border: 1px solid #e2943b;
                border-radius: 8px;
                font-weight: 700;
            }

            .lisense-status-active:before {
                content: "";
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: #12844f;
            }

            .lisense-key-cell {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .lisense-copy-btn {
                flex: 0 0 auto;
                border: 1px solid #cfd8e3;
                border-radius: 6px;
                background: #ffffff;
                color: #344454;
                padding: 5px 9px;
                font-size: 12px;
                line-height: 1;
            }

            .lisense-copy-btn:hover,
            .lisense-copy-btn:focus {
                border-color: #94a3b8;
                color: #182333;
                background: #f8fafc;
            }

            @media (max-width: 520px) {
                .lisense-card-header {
                    padding: 14px 14px;
                }

                .lisense-detail-table tr,
                .lisense-detail-table td {
                    display: block;
                    width: 100% !important;
                }

                .lisense-detail-table tr td {
                    padding: 8px 14px;
                    border-bottom: 0;
                }

                .lisense-detail-table tr {
                    padding: 8px 0;
                    border-bottom: 1px solid #e5ebf0;
                }

                .lisense-detail-table tr:last-child {
                    border-bottom: 0;
                }

                .lisense-key-cell {
                    align-items: flex-start;
                }
            }
        </style>

        <style type="text/css">
            /* Contenedor */
            .severity-list{
            list-style:none;
            margin:0;
            padding:8px 10px;
            }

            /* Item base */
            .severity-item{
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:14px 16px;
            margin:8px 0;
            border:1px solid #eceff4;
            border-radius:12px;
            background: linear-gradient(180deg,#fbfbfe, #f6f7fb);
            transition: box-shadow .15s ease, transform .15s ease, background .15s ease, border-color .15s ease;
            }

            /* Resaltado (el del medio en tu imagen) */
            .severity-item.is-active{
            background:#fff;
            border-color:#e7e4ff;
            box-shadow: 0 10px 22px rgba(79,70,229,.08);
            transform: translateY(-1px);
            }

            /* Lado izquierdo (flecha + número) */
            .severity-left{
            display:flex;
            align-items:center;
            gap:10px;
            }

            /* Icono redondito con flecha */
            .severity-icon{
            width:28px;
            height:28px;
            border-radius:50%;
            border:1px solid #e3e7ee;
            background:#fff;
            color:#9aa0ae;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            font-size:14px;
            }

            /* Número grande */
            .severity-value{
            font-weight:700;
            font-size:26px;      /* ajusta a tu gusto */
            letter-spacing:.5px;
            color:#0f1222;
            }
            .severity-item.is-active .severity-value{ font-size:32px; }

            /* Chips de severidad (tipo "label pill" en Bootstrap 3) */
            .tag{
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            font-size:12px;
            line-height:1.2;
            border:1px solid rgba(0,0,0,.06);
            background:#f2f4f8;
            color:#5b6270;
            }
            .tag-danger{
            background:rgba(215,57,37,.12);
            border-color:rgba(215,57,37,.2);
            color:#d73925; /* AdminLTE danger */
            }
            .tag-warning{
            background:rgba(243,156,18,.12);
            border-color:rgba(243,156,18,.2);
            color:#f39c12; /* AdminLTE warning */
            }
            .tag-success{
            background:rgba(0,166,90,.12);
            border-color:rgba(0,166,90,.2);
            color:#00a65a; /* AdminLTE success */
            }

            /* Hover opcional */
            .severity-item:hover{
            box-shadow:0 8px 20px rgba(15,18,34,.06);
            background:#fff;
            }
        </style>

        <!-- <link rel="stylesheet" type="text/css" href="//github.com/downloads/lafeber/world-flags-sprite/flags32.css"> -->

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
                    <!-- Custom Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">

                            <li class="active">
                                <a href="#tab_1" data-toggle="tab">
                                <img src="img/dashboard.png" style="width: 20px;"> Dashboard
                                </a>
                            </li>

                            <li id="ataquesGrafica">
                                <a href="#tab_2" data-toggle="tab">
                                <img src="img/ip bloqueadas por waf.png" style="width: 20px;"> Ip Bloqueadas WAF
                                </a>
                            </li>

                            <li>
                                <a href="#tab_3" data-toggle="tab" id="grafico">
                                <img src="img/tipo de ataqu.png" style="width: 20px;"> Tipos de ataques
                                </a>
                            </li>

                            <li>
                                <a href="#tab_4" data-toggle="tab" id="graficoP">
                                <img src="img/ataques por paises.png" style="width: 20px;"> Ataques por paises
                                </a>
                            </li>

                            <!-- <li>
                                <a href="#tab_5" data-toggle="tab" id="graficoSemana">
                                <i class="fa fa-pie-chart"></i> Ataques ultima semana
                                </a>
                            </li> -->

                            <!-- <li>
                                <a href="#tab_6" data-toggle="tab" id="graficoMes">
                                <i class="fa fa-pie-chart"></i> Ataques ultimo mes
                                </a>
                            </li> -->

                            <li>
                                <a href="#tab_7" data-toggle="tab" id="graficoDominio">
                                <img src="img/ataques a dominios.png" style="width: 20px;"> Ataques Dominios
                                </a>
                            </li>

                            <li>
                                <a href="#tab_8" data-toggle="tab">
                                <img src="img/busqueda.png" style="width: 20px;"> Busqueda
                                </a>
                            </li>

                            <li>
                                <a href="#tab_9" data-toggle="tab" id="graficoVisita">
                                <img src="img/visitas por dominio.png" style="width: 20px;"> Visitas Por Dominio
                                </a>
                            </li>

                            <li>
                                <a href="#tab_10" data-toggle="tab" id="graficoDdos">
                                <img src="img/bloqueo_ddos.png" style="width: 20px;"> Ataques DDOS
                                </a>
                            </li>

                            <li>
                                <a href="#tab_11" data-toggle="tab" id="graficoDdos">
                                <img src="img/vulnerabilidad.jpeg" style="width: 20px;"> Vulnerabilidades <span class="label label-warning" style="margin-left: 4px;">BETA</span>
                                </a>
                            </li>

                            <li>
                                <a href="#tab_12" data-toggle="tab" id="graficoThreat">
                                <img src="img/threat.jpeg" style="width: 20px;"> Ip Threat <span class="label label-warning" style="margin-left: 4px;">BETA</span>
                                </a>
                            </li>

                            <li class="dropdown pull-right">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                  Opciones <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" id="update" href="#0">
                                            <small> <i class="fa fa-refresh"></i> Actualizar Bloqueos</small>
                                        </a>
                                    </li>

                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" id="update2" href="#0">
                                            <small> <i class="fa fa-refresh"></i> Actualizar Ip Bloqueadas</small>
                                        </a>
                                    </li>

                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" id="update4" href="#0">
                                            <small> <i class="fa fa-refresh"></i> Actualizar Visitas</small>
                                        </a>
                                    </li>

                                    <!-- <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalE">
                                            <small> <i class="fa fa-trash"></i> Eliminar</small>
                                        </a>
                                    </li> -->

                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" id="update3" href="#0">
                                            <small> <i class="fa fa-refresh"></i> Actualizar Datos</small>
                                        </a>
                                    </li>

                                    <!-- <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalEliminar">
                                            <small> <i class="fa fa-trash"></i> Eliminar Datos</small>
                                        </a>
                                    </li> -->

                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalEliminarHost">
                                            <small> <i class="fa fa-server"></i> Lista Host</small>
                                        </a>
                                    </li>

                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalEliminarHostVisita">
                                            <small> <i class="fa fa-server"></i> Lista Host Visitas</small>
                                        </a>
                                    </li>

                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalApikeyOpenAi">
                                            <small> <i class="fa fa-key"></i> Activar GPT-Search</small>
                                        </a>
                                    </li>

                                    <li role="presentation">
                                        <a role="menuitem" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalDeleteAll">
                                            <small> <i class="fa fa-trash"></i> Eliminar Todo</small>
                                        </a>
                                    </li>

                                </ul>
                            </li>

                            <li class="dropdown pull-right">
                                <a role="dropdown-toggle" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalReporte">
                                  Reportes <span class="fa fa-file"></span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">

                            <div class="tab-pane active" id="tab_1">

                                <!-- grafica de ataques -->
                                <div class="box box-solid col-md-12">
                                    <div class="col-md-12">
                                        <style>
                                        .skeleton-container {
                                            display: flex;
                                            flex-direction: column;
                                            gap: 20px;
                                            padding: 30px;
                                            height: 400px;
                                            justify-content: center;
                                        }
                                        .skeleton-bar-wrapper {
                                            display: flex;
                                            align-items: center;
                                            gap: 15px;
                                        }
                                        .skeleton-label {
                                            width: 15%;
                                            height: 15px;
                                            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                                            background-size: 200% 100%;
                                            animation: shimmer 1.5s infinite;
                                            border-radius: 4px;
                                        }
                                        .skeleton-bar {
                                            height: 25px;
                                            background: linear-gradient(90deg, #e4eaf1 25%, #d1d8e0 50%, #e4eaf1 75%);
                                            background-size: 200% 100%;
                                            animation: shimmer 1.5s infinite;
                                            border-radius: 4px;
                                        }
                                        @keyframes shimmer {
                                            0% { background-position: 200% 0; }
                                            100% { background-position: -200% 0; }
                                        }
                                        .skeleton-bar.w-80 { width: 80%; }
                                        .skeleton-bar.w-60 { width: 60%; }
                                        .skeleton-bar.w-90 { width: 90%; }
                                        .skeleton-bar.w-40 { width: 40%; }
                                        .skeleton-bar.w-70 { width: 70%; }
                                        .skeleton-bar.w-50 { width: 50%; }
                                        </style>
                                        <div id="webAttacksChart" style="min-width: 400px; height: 400px; max-width: 650px; margin: 0 auto">
                                            <!-- Skeleton Loader (Se reemplaza al pintar Highcharts) -->
                                            <div class="skeleton-container">
                                                <div class="skeleton-bar-wrapper"><div class="skeleton-label"></div><div class="skeleton-bar w-80"></div></div>
                                                <div class="skeleton-bar-wrapper"><div class="skeleton-label"></div><div class="skeleton-bar w-60"></div></div>
                                                <div class="skeleton-bar-wrapper"><div class="skeleton-label"></div><div class="skeleton-bar w-90"></div></div>
                                                <div class="skeleton-bar-wrapper"><div class="skeleton-label"></div><div class="skeleton-bar w-40"></div></div>
                                                <div class="skeleton-bar-wrapper"><div class="skeleton-label"></div><div class="skeleton-bar w-70"></div></div>
                                                <div class="skeleton-bar-wrapper"><div class="skeleton-label"></div><div class="skeleton-bar w-50"></div></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <!-- Date range -->
                                        <form id="FormRango" class="FormRango" method="post" autocomplete="off" enctype="multipart/form-data">
                                            <div class="pull-right" style="width: 20%;">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" class="form-control pull-right" id="rangoGraficaUno">
                                                </div>
                                                <!-- /.input group -->
                                            </div>
                                        </form>
                                        <!-- /.form group -->

                                        <div class="box-header">
                                            <i class="fa fa-shield"></i>
                                            <h3 class="box-title">Ataques</h3>
                                        </div>
                                        <div class="box-body border-radius-none" id="chartUno">
                                            <div class="chart" id="revenue-chart2" style="position: relative; height: 300px;"></div>
                                        </div>
                                        
                                        <div class="overlay" id="overlayGraficaPrincipal" style="display: none;">
                                            <i class="fa fa-refresh fa-spin"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-md-12 text-center" style="margin-bottom: 20px;">
                                        <h3 class="box-title">Rango de ataques</h3>
                                        <div class="btn-group" role="group" id="rangoAtaquesTabs">
                                            <button type="button" class="btn btn-primary" data-target="hoy">HOY</button>
                                            <button type="button" class="btn btn-default" data-target="semana">ÚLTIMA SEMANA</button>
                                            <button type="button" class="btn btn-default" data-target="mes">ÚLTIMO MES</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div id="d3_custom_chart_container" style="width: 100%; height: 500px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); background: #ffffff;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_2">
                                <div class="text-center">
                                    <select id="dropdown1" class="select2">
                                        <option value="">Lista de Host</option>
                                        <?php
                                            // OPTIMIZACIÓN: Usar la tabla summary 'host' en lugar de escanear 'bloqueo' (62M)
                                            $consult = mysqli_query($link,"SELECT
                                                                            LOWER(TRIM(IF(LOWER(TRIM(nombre_host)) LIKE 'www.%', SUBSTRING(TRIM(nombre_host), 5), TRIM(nombre_host)))) AS host_key,
                                                                            MAX(CASE WHEN LOWER(TRIM(nombre_host)) LIKE 'www.%' THEN TRIM(nombre_host) ELSE nombre_host END) AS host_label
                                                                        FROM host
                                                                        GROUP BY host_key
                                                                        ORDER BY host_label");
                                            while($row = mysqli_fetch_array($consult)){
                                                echo '<option value="'.htmlspecialchars($row['host_key'], ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($row['host_label'], ENT_QUOTES, 'UTF-8').'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div id="div_tipo_ataque_4" class="col-md-12" style="display: none;">
                                    <!-- Date range -->
                                    <form id="FormRangoTipoAtaqueDomini" class="FormRangoTipoAtaqueDomini" method="post" autocomplete="off" enctype="multipart/form-data">
                                        <div class="pull-right" style="width: 20%;">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" class="form-control pull-right" id="rangoGraficaTipoAtaqueDominio">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </form>
                                    <!-- /.form group -->
                                </div>

                                <div id="div_tipo_ataque_3" class="col-md-12 box box-solid" style="display: none;">
                                    <div id="containerPastelTipoAtaqueDomino" style="min-width: 400px; height: 500px; max-width: 650px; margin: 0 auto"></div>

                                    <div class="overlay" id="overlayListaWafAtaquesDominios" style="display: none;">
                                        <i class="fa fa-refresh fa-spin"></i>
                                    </div>
                                </div>

                                <div style="overflow: auto;" class="box box-solid">
                                    <div class="box-body no-padding">
                                        <table id="example1" class="table table-bordered table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Log</th>
                                                    <th>#</th>
                                                    <th>Fecha de bloqueo</th>
                                                    <th>Origen</th>
                                                    <th>Host Destino</th>
                                                    <th>Url</th>
                                                    <th>Tipo de ataque</th>
                                                    <th>Desbloquear</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="overlay" id="overlayListaWaf" style="display: none;">
                                        <i class="fa fa-refresh fa-spin"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_3">
                                <div class="row">
                                    <!-- Date range -->
                                    <form id="FormRango" class="FormRango" method="post" autocomplete="off" enctype="multipart/form-data">
                                        <div class="pull-right" style="width: 20%;">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" class="form-control pull-right" id="rangoGraficaTipoAtaque">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </form>
                                    <!-- /.form group -->

                                    <div id="div_tipo_ataque_1" class="col-md-12" style="display: block;">
                                        <div class="col-md-4">
                                            <div id="" class="chart1"></div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div id="" class="chart2"></div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div id="" class="chart3"></div>
                                        </div>
                                    </div>

                                    <div id="div_tipo_ataque_2" class="col-md-12" style="display: none;">
                                        <div style="display: none;" id="" class="chart_tipoAtaque"></div>
                                        <div id="containerPastelTipoAtaque" style="min-width: 400px; height: 500px; max-width: 650px; margin: 0 auto"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_4">
                                <div class="example-box-wrapper clearfix">

                                    <!-- grafica de ataques -->
                                    <div class="box box-solid">

                                        <!-- Date range -->
                                        <form id="FormRangoTres" class="FormRangoTres" method="post" autocomplete="off" enctype="multipart/form-data">
                                            <div class="pull-right" style="width: 20%;">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" class="form-control pull-right" id="rangoGraficaTres">
                                                </div>
                                                <!-- /.input group -->
                                            </div>
                                        </form>
                                        <!-- /.form group -->

                                        <div class="box-header">
                                            <i class="fa fa-bolt"></i>
                                            <h3 class="box-title"></h3>
                                        </div>
                                        <div class="box-body border-radius-none">
                                            <div class="chart" id="line-chart3" style="height: 200px;"></div>
                                        </div>
                                    </div>
                                    <!-- /.box -->

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div style="display: none;" id="" class="chart_tipoAtaque"></div>
                                            <div id="container3" style="min-width: 400px; height: 500px; max-width: 650px; margin: 0 auto"></div>
                                        </div>

                                        <div class="col-md-6" id="listaPaises">
                                            <div id="container4" style="min-width: 400px; height: 500px; max-width: 650px; margin: 0 auto"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane" id="tab_7">
                                <!-- Date range -->
                                <form style="float: right;" id="FormRangoCuatro" class="FormRangoCuatro" method="post" autocomplete="off" enctype="multipart/form-data">
                                    <div class="pull-right" style="width: 20%;">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" id="rangoGraficaCuatro">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                </form>
                                <!-- /.form group -->

                                <div class="example-box-wrapper clearfix">
                                    <div id="containerBar" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_8">

                                <form style="text-align: center;margin: 6px 0px;">
                                    <p></p>

                                    <input class="form-control" style="width: 15%; display: initial; font-size: 11px; height: 25px;" type="text" id="busqueda1" placeholder="Origen"/>

                                    <select class="form-control" style="width: 15%; display: initial; font-size: 10px; height: 27px;" id="busqueda2">
                                        <option value="">Tipo Ataque</option>
                                        <?php
                                            $queryTipo = mysqli_query($link,"SELECT DISTINCT(tipo_ataque) AS ataque FROM bloqueo_master");
                                            while($rowTipo = mysqli_fetch_array($queryTipo)){
                                                
                                                $nombre_ataque = $rowTipo['ataque'];

                                                if ($nombre_ataque == 'ssh-failed') {
                                                    $tipo_ataque = 'Brute force attack ssh';
                                                } else if($nombre_ataque == 'nginx-naxsi'){
                                                    $tipo_ataque = 'Brute force attack web';
                                                } else {
                                                    $tipo_ataque = $rowTipo['ataque'];
                                                }

                                                if (!empty($tipo_ataque)) {
                                                    echo '<option value="'.$rowTipo['ataque'].'">'.$tipo_ataque.'</option>';
                                                }
                                            }
                                        ?>
                                    </select>

                                </form>

                                <table id="example2" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha de bloqueo</th>
                                            <th>Origen</th>
                                            <th>Tipo de ataque</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="tab_9">
                                <div class="row">

                                    <div class="col-md-12" id="estadisticaRango">
                                        <!-- solid sales graph -->
                                        <div class="box box-solid bg-oscuro-gradient">
                                            <div class="box-header">
                                            <i class="fa fa-line-chart"></i>
                                            <h3 class="box-title">Grafica timeline visitas</h3>
                                            <div class="box-tools pull-right">
                                            </div>
                                            </div>
                                            <div class="box-body border-radius-none">
                                                <!-- Date range -->
                                                <form id="FormRangoVisitas" class="FormRangoVisitas" method="post" autocomplete="off" enctype="multipart/form-data">
                                                    <div class="pull-right" style="width: 20%;">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" class="form-control pull-right" id="rangoGraficaVisita">
                                                            <input type="hidden" id="nombredominio">
                                                        </div>
                                                        <!-- /.input group -->
                                                    </div>
                                                </form>
                                                <!-- /.form group -->
                                            <div class="chart" id="line-chart4" style="height: 250px;"></div>
                                            </div><!-- /.box-body -->

                                            <div class="overlay" id="overlayGraficaVisita" style="display: none;">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </div>
                                            
                                            <div class="box-footer no-border">
                                            <div class="row">
                                                <?php
                                                    // //optener rango de fechas por dia
                                                    // $actual = date("Y-m-d H:i:s");
                                                    // $pasadoAux = date("Y-m-d");
                                                    // $pasado = $pasadoAux.' 00:00:00';
                                                    
                                                    // //total de visitas dia
                                                    // $consultDia = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$pasado' AND '$actual'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalDia = mysqli_num_rows($consultDia);

                                                    // //optener rango de fechas por semana
                                                    // $hoy = date("Y-m-d H:i:s");
                                                    // $semana = date("Y-m-d H:i:s",strtotime($hoy."- 1 week"));
                                                    
                                                    // //total visitas semana
                                                    // $consultSemana = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio 
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$semana' AND '$hoy'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalSemana = mysqli_num_rows($consultSemana);

                                                    // //optener rango de fechas por mes
                                                    // $meshoy = date("Y-m-d H:i:s");
                                                    // $mes = date("Y-m-d H:i:s",strtotime($hoy."- 1 month"));
                                                    
                                                    // //total visita por mes
                                                    // $consultMes = mysqli_query($link,"SELECT DISTINCT ip_visita FROM visita_dominio
                                                    //                             WHERE visita_dominio.fecha_visita BETWEEN '$mes' AND '$meshoy'
                                                    //                             AND visita_dominio.activo_visita = 1");
                                                    // $totalMes = mysqli_num_rows($consultMes);
                                                ?>
                                                <!-- <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="<?php echo $totalDia; ?>" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Dia</div>
                                                </div>
                                                
                                                <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="<?php echo $totalSemana; ?>" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Semana</div>
                                                </div>

                                                <div class="col-xs-4 text-center">
                                                    <input type="text" class="knob" data-max="" data-angleOffset="90" data-linecap="round" data-readonly="true" value="<?php echo $totalMes; ?>" data-width="60" data-height="60" data-fgColor="#39CCCC">
                                                    <div class="knob-label">Total Visitas Mes</div>
                                                </div> -->
                                            </div><!-- /.row -->
                                            </div><!-- /.box-footer -->
                                        </div><!-- /.box -->
                                    </div>

                                    <div class="col-md-12">
                                        <!-- Custom tabs (Charts with tabs)-->
                                        <div class="nav-tabs-custom">
                                            <!-- Tabs within a box -->
                                            <ul class="nav nav-tabs pull-right">
                                                <li class="active">
                                                    <a href="#sales-chart" data-toggle="tab"><i class="fa fa-map-o"></i></a>
                                                </li>
                                                <li class="pull-left header">
                                                </li>
                                            </ul>
                                            <div class="tab-content no-padding">
                                                <!-- <form style="float: right;" id="FormRangoCinco" class="FormRangoCinco" method="post" autocomplete="off" enctype="multipart/form-data">
                                                    <div class="pull-right" style="width: 20%;">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" class="form-control pull-right" id="rangoGraficaCinco">
                                                        </div>
                                                    </div>
                                                </form> -->

                                                <div class="example-box-wrapper clearfix">
                                                    <div id="containerBarDominio" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
                                                </div>
                                            </div>
                                        </div><!-- /.nav-tabs-custom -->
                                    </div>

                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <select id="dropdown4" class="select2">
                                                <option value="">Lista de Dominios</option>
                                                <?php
                                                    // OPTIMIZACIÓN: Usar la tabla de resumen visita_dominio_group
                                                    $consult = mysqli_query($link,"SELECT
                                                                                    LOWER(TRIM(IF(LOWER(TRIM(dominio)) LIKE 'www.%', SUBSTRING(TRIM(dominio), 5), TRIM(dominio)))) AS dominio_key,
                                                                                    MAX(CASE WHEN LOWER(TRIM(dominio)) LIKE 'www.%' THEN TRIM(dominio) ELSE dominio END) AS dominio_label
                                                                                FROM visita_dominio_group
                                                                                GROUP BY dominio_key
                                                                                ORDER BY dominio_label");
                                                    while($row = mysqli_fetch_array($consult)){
                                                        echo '<option value="'.htmlspecialchars($row['dominio_key'], ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($row['dominio_label'], ENT_QUOTES, 'UTF-8').'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <table id="example4" class="table table-bordered table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Fecha de Visita</th>
                                                    <th>Ip de Visita</th>
                                                    <th>Dominio</th>
                                                    <th>Total visita por IP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_10">
                                <div class="row">
                                    <div class="col-md-12">
                                            <!-- Custom tabs (Charts with tabs)-->
                                            <div class="nav-tabs-custom">
                                                <!-- Tabs within a box -->
                                                <ul class="nav nav-tabs pull-right">
                                                    <li class="active">
                                                        <a href="#sales-chart" data-toggle="tab"><i class="fa fa-map-o"></i></a>
                                                    </li>
                                                    <li class="pull-left header">
                                                    </li>
                                                </ul>
                                                <div class="tab-content no-padding">
                                                    <!-- Date range -->
                                                    <form style="float: right;" id="FormRangoSeis" class="FormRangoSeis" method="post" autocomplete="off" enctype="multipart/form-data">
                                                        <div class="pull-right" style="width: 20%;">
                                                            <div class="input-group">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-calendar"></i>
                                                                </div>
                                                                <input type="text" class="form-control pull-right" id="rangoGraficaSeis">
                                                            </div>
                                                            <!-- /.input group -->
                                                        </div>
                                                    </form>
                                                    <!-- /.form group -->

                                                    <div class="col-md-12">
                                                        <div style="display: none;" id="" class="chart_tipoAtaque"></div>
                                                        <div id="containerPastelDdos" style="min-width: 400px; height: 500px; max-width: 650px; margin: 0 auto"></div>
                                                    </div>
                                                </div>
                                            </div><!-- /.nav-tabs-custom -->
                                    </div>

                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <button class="btn btn-primary">
                                                <a href="#0" data-toggle="modal" data-target="#ModalNewDdos" style="color: #FFFFFF;" id="">
                                                <i class="fa fa-unlock"></i>Agregar nueva ip</a>
                                            </btutton>
                                        </div>

                                        <div style="overflow: auto;" class="box box-solid">
                                            <div class="box-body no-padding">
                                                <table id="example5" class="table table-bordered table-striped" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Fecha</th>
                                                            <th>IP</th>
                                                            <th>Pais</th>
                                                            <th>Total conexiones</th>
                                                            <th>Opciones</th>
                                                            <th style="display:none">lista_blanca</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_11">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Custom tabs (Charts with tabs)-->
                                    </div>

                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <a href="#0" class="btn btn-primary" data-toggle="modal" data-target="#ModalNewEscan" style="color: #FFFFFF;" id="">
                                            <i class="fa fa-refresh"></i> Agregar nueva host o ip a escanear</a>

                                            <a href="#0" class="btn btn-danger" data-toggle="modal" data-target="#ModalDeleteScan" style="color: #FFFFFF;" id="">
                                            <i class="fa fa-trash"></i> Eliminar datos de vulnerabilidades</a>

                                            <form action="controller/g_reporte_vulnerabilidades_pdf.php" method="post" target="_blank" style="display:inline-block; margin-left: 6px;">
                                                <button type="submit" class="btn btn-info" style="color: #FFFFFF;">
                                                    <i class="fa fa-file-pdf-o"></i> Generar reporte
                                                </button>
                                            </form>
                                        </div>

                                        <div class="text-center" style="float: right;">
                                            <a class="btn btn-success" href="#0" data-toggle="modal" data-target="#ModalHistoria" style="color: #FFFFFF;" id="">
                                            <i class="fa fa-list"></i> Historico de escaneos</a>
                                        </div>
                                        <br>

                                        <div style="overflow: auto;" class="box box-solid">
                                            <div class="box-body no-padding">
                                                <table id="example6" class="table table-bordered table-striped" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Host</th>
                                                            <th>Port</th>
                                                            <th>Vulnerabilidad</th>
                                                            <!-- <th>Descripción</th> -->
                                                            <th>Fecha de analisis</th>
                                                            <th class="text-center">Severidad</th>
                                                            <th class="text-center">Ver más información</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="overlay" id="overlayListaWafNuevo" style="display: none; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 14px;">
                                                <i class="fa fa-refresh fa-spin" style="font-size: 42px; display: block;"></i>
                                                <span style="font-size: 24px; font-weight: 600; display: block;margin-top: 100px;">Buscando vulnerabilidades...</span>
                                            </div>

                                            <div class="overlay" id="overlayListaWafNuevoDelete" style="display: none; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 14px;">
                                                <i class="fa fa-refresh fa-spin" style="font-size: 42px; display: block;"></i>
                                                <span style="font-size: 24px; font-weight: 600; display: block;margin-top: 100px;">Eliminando vulnerabilidades...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab_12">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="col-md-8">
                                            <div id="" class="chart_ip_threat"></div>
                                        </div>
                                        <?php
                                            // OPTIMIZACIÓN: COUNT(*) en lugar de cargar todas las filas (evita OOM)
                                            $resBots = mysqli_query($link,"SELECT COUNT(*) FROM threat_ip_list WHERE source_file = 'bots.txt'");
                                            $totalBots = (int)mysqli_fetch_row($resBots)[0];

                                            $resApache = mysqli_query($link,"SELECT COUNT(*) FROM threat_ip_list WHERE source_file <> 'bots.txt'");
                                            $totalApache = (int)mysqli_fetch_row($resApache)[0];

                                            $totalIp = $totalBots + $totalApache;
                                        ?>
                                        <div class="col-md-2">
                                            <!-- small box -->
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                    <h3><?php echo $totalIp; ?></h3>
                                                    <p>Total Ip</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <!-- small box -->
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                    <h3><?php echo $totalApache; ?></h3>
                                                    <p>Total Webs</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <!-- small box -->
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                    <h3><?php echo $totalBots; ?></h3>
                                                    <p>Total Bots</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <section class="content">
                                            <div class="row">
                                                <div>
                                                <div class="box box-default">
                                                    <div class="box-header with-border">
                                                    <h3 class="box-title">Top Paises</h3>
                                                    </div>

                                                    <div class="box-body no-padding">
                                                        <ul class="severity-list">
                                                            <?php
                                                                $consult = mysqli_query($link,"SELECT
                                                                                                            t.codigo_pais,
                                                                                                            COALESCE(c.nombre, 'Otro')     AS pais,
                                                                                                            COUNT(*)                        AS total_rows,
                                                                                                            COUNT(DISTINCT t.ip)            AS unique_ips,
                                                                                                            SUM(COALESCE(t.hits,0))         AS total_hits,
                                                                                                            MIN(t.first_seen)               AS first_seen,
                                                                                                            MAX(t.last_seen)                AS last_seen
                                                                                                            FROM threat_ip_list t
                                                                                                            LEFT JOIN paises c
                                                                                                                ON c.iso = t.codigo_pais
                                                                                                            GROUP BY t.codigo_pais, c.nombre
                                                                                                            ORDER BY total_rows DESC
                                                                                                            LIMIT 4");
                                                                while($row = mysqli_fetch_array($consult)){
                                                                    echo '<li class="severity-item">
                                                                        <span class="severity-left">
                                                                            <span class="severity-icon">
                                                                                <i class="fa fa-long-arrow-right"></i>
                                                                            </span>
                                                                            <span class="severity-value">'.$row['pais'].'</span>
                                                                        </span>
                                                                        <span class="tag tag-danger">'.$row['total_rows'].'</span>
                                                                    </li>';
                                                                }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div><!-- /.box -->
                                                </div>
                                            </div>
                                            </section>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="text-right" style="margin: 10px 0;">
                                            <a href="ajax_table/download_threat_ips.php" class="btn btn-primary">
                                                <i class="fa fa-download"></i> Descargar IPs
                                            </a>
                                        </div>
                                        <div style="overflow: auto;" class="box box-solid">
                                            <div class="box-body no-padding">
                                                <table id="example7" class="table table-bordered table-striped" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>IP</th>
                                                            <th>Total</th>
                                                            <th>Tipo</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- nav-tabs-custom -->
                </section>
                <!-- /.content -->

                <div class="modal fade" id="modalLisense">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"><i class="fa fa-check-circle"></i> Licencia</h4>
                            </div>
                            <div class="modal-body">
                                <div id="lisenseMessage" class="alert" style="display: none;"></div>

                                <div id="lisenseFormSection" <?php echo $lisenseIsActive ? 'style="display:none;"' : ''; ?>>
                                    <p class="lisense-help">Ingresa el codigo de licencia para activar el proyecto.</p>
                                    <div class="form-group">
                                        <label for="lisenseInputCode">Codigo de licencia</label>
                                        <input type="text" class="form-control" id="lisenseInputCode" placeholder="Ejemplo: NFW-7K9Q2M4XA1" autocomplete="off">
                                    </div>
                                </div>

                                <div id="lisenseActiveSection" <?php echo !$lisenseIsActive ? 'style="display:none;"' : ''; ?>>
                                    <div class="lisense-card">
                                        <div class="lisense-card-header">
                                            <div class="lisense-card-title-wrap">
                                                <span class="lisense-card-icon"><i class="fa fa-shield"></i></span>
                                                <h4 class="lisense-card-title">License Information</h4>
                                            </div>
                                        </div>
                                        <table class="lisense-detail-table">
                                            <tr>
                                                <td>Activation Key:</td>
                                                <td>
                                                    <div class="lisense-key-cell">
                                                        <span id="lisenseActivationKeyLabel"><?php echo htmlspecialchars($lisenseActivationKey, ENT_QUOTES, 'UTF-8'); ?></span>
                                                        <!-- <button type="button" class="lisense-copy-btn" id="btnCopyLisenseKey">
                                                            <i class="fa fa-copy"></i> Copy
                                                        </button> -->
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Model:</td>
                                                <td id="lisenseModelLabel"><?php echo htmlspecialchars($lisenseModelLabel, ENT_QUOTES, 'UTF-8'); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Status:</td>
                                                <td><span id="lisenseStatusLabel" class="<?php echo $lisenseIsActive ? 'lisense-status-active' : ''; ?>"><?php echo htmlspecialchars($lisenseStatusLabel, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                            </tr>
                                            <tr>
                                                <td>Expiration Date:</td>
                                                <td id="lisenseExpirationDateLabel"><?php echo htmlspecialchars($lisenseExpiresAtDisplay, ENT_QUOTES, 'UTF-8'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-primary" id="btnCheckLisense" <?php echo $lisenseIsActive ? 'style="display:none;"' : ''; ?>>Comprobar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalE">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Eliminar Data</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form1" class="FormB" action="" method="" autocomplete="off">
                                    <h4 class="text-center">¿Esta seguro de limpiar la tabla?</h4>
                                    <div class="form-group col-md-12">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-list"></i>
                                            </div>
                                            <select class="form-control" name="opc">
                                                <option value="1">Las primeras 10 Filas</option>
                                                <option value="2">Las primeras 20 Filas</option>
                                                <option value="3">Las primeras 30 Filas</option>
                                                <option value="4">Las primeras 40 Filas</option>
                                                <option value="5">Las primeras 50 Filas</option>
                                                <option value="6">Todas las filas</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-danger" type="button" id="btnE"><i class="fa fa-trash"></i> Elimimar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <div class="modal fade" id="modalEliminar">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Eliminar Data</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form2" class="FormC" action="" method="" autocomplete="off">
                                    <h4 class="text-center">¿Esta seguro de eliminar los datos?</h4>
                                    
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-danger" type="button" id="btnEliminar"><i class="fa fa-trash"></i> Elimimar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <div class="modal fade" id="modalUrl">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Url</h5>
                            </div>
                            <div class="modal-body" style="overflow: auto;">
                                <p id="textoUrl"></p>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <div class="modal fade" id="modalInfo">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">INTERNAL RULES</h5>
                            </div>
                            <div class="modal-body" style="overflow: auto;">
                                <p>
                                   Las reglas internas son reglas que pueden ser activadas por  el waf,cuando una solicitud es incorrecta o extremadamente inusual, el waf no puede analizar la solicitud (es decir, tipo de contenido desconocido).
                                   <br><br>
                                   Tenga en cuenta que esas reglas no establecen un puntaje interno, sino que generalmente establecen el indicador de bloqueo. 
                                </p>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <div class="modal fade" id="modalEliminarHost">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Lista de Host</h5>
                            </div>
                            <div class="modal-body">
                                <div style="text-align: center;">
                                    <button class="btn btn-danger" id="eliminarSeleccionadosDos">Eliminar Seleccionados</button>
                                </div>
                                <table id="example3" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th><input class="text-center" type="checkbox" id="seleccionarTodoDos"></th>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Host</th>
                                            <th class="text-center">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            // OPTIMIZACIÓN: Usar la tabla 'host'
                                            $consult = mysqli_query($link,"SELECT nombre_host FROM host ORDER BY nombre_host ASC");
                                            $i = 1;
                                            while($row = mysqli_fetch_array($consult)){
                                                $nombre_limpio = $row['nombre_host'];
                                                echo '<tr>
                                                        <td class="text-center">
                                                            <input id="scales3" class="editor-active scales3-'.$i.' seleccionar2" type="checkbox" name="scales2" data-id="'.htmlspecialchars($nombre_limpio).'">
                                                        </td>
                                                        <td class="text-center">'.$i.'</td>
                                                        <td class="text-center">'.htmlspecialchars($nombre_limpio).'</td>
                                                        <td class="text-center">
                                                            <a href="#0" type="'.htmlspecialchars($nombre_limpio).'" id="btnEHost" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</a>
                                                        </td>
                                                      </tr>';
                                                $i++;
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <div class="modal fade" id="modalEliminarHostVisita">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Lista de Host Visitas</h5>
                            </div>
                            <div class="modal-body">
                                <div style="text-align: center;">
                                    <button class="btn btn-danger" id="eliminarSeleccionados">Eliminar Seleccionados</button>
                                </div>
                                <table id="exampleVisita" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th><input class="text-center" type="checkbox" id="seleccionarTodo"></th>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Host</th>
                                            <th class="text-center">Eliminar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            // OPTIMIZACIÓN: Usar la tabla summary (GROUP BY ya manejado por los índices de la tabla)
                                            $consult = mysqli_query($link,"SELECT dominio FROM visita_dominio_group GROUP BY dominio ORDER BY dominio ASC");
                                            $i = 1;
                                            while($row = mysqli_fetch_array($consult)){
                                                $nombre_limpio = $row['dominio'];
                                                echo '<tr>
                                                        <td class="text-center">
                                                            <input id="scales2" class="editor-active scales2-'.$i.' seleccionar" type="checkbox" name="scales" data-id="'.htmlspecialchars($nombre_limpio).'">
                                                        </td>
                                                        <td class="text-center">'.$i.'</td>
                                                        <td class="text-center">'.htmlspecialchars($nombre_limpio).'</td>
                                                        <td class="text-center">
                                                            <a href="#0" type="'.htmlspecialchars($nombre_limpio).'" id="btnEHostVisita" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</a>
                                                        </td>
                                                      </tr>';
                                                $i++;
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <div class="modal fade" id="modalConformarEliminar">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Eliminar Data</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form2" class="FormC" action="" method="" autocomplete="off">
                                    <h4 class="text-center">¿Eliminar datos permanentemente?</h4>
                                    <input type="hidden" name="nombre_hostVisita" id="nombre_hostVisita">
                                    
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" type="button" id="btnEliminar1">No</button>
                                        <button class="btn btn-danger" type="button" id="btnEliminar2">Si</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <!-- Modal editar ip-->
                <div class="modal fade" id="ModalDesbloquear" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Desbloquear IP</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form2" class="FormUb" action="" method="" autocomplete="off">
                                    <div class="form-group col-md-12">
                                        <label>Dirección ip</label>
                                        <input type="text" class="form-control" id="ip_edit" name="ip" placeholder="Dirección ip">
                                    </div>
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-primary" type="button" id="btnDesbloqueo"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>
                <!-- /.modal -->

                <div class="modal fade" id="modalApikeyOpenAi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel"></h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form2" class="FormOpenAi" action="" method="" autocomplete="off">
                                    <div class="form-group col-md-12">
                                        <label>Api key</label>
                                        <input type="text" class="form-control" id="api_key" name="api_key" placeholder="Api key">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Activar</label>
                                        <select class="form-control select2" style="width: 100%" name="id_activo">
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-primary" type="button" id="btnActivarOpenAi"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalReporte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel"></h5>
                            </div>
                            <div class="modal-body">
                                <form id="FormReporte" class="FormReporte" action="controller/g_reporte_pdf.php" method="post" target="_blank" autocomplete="off">
                                    <div class="form-group col-md-12">
                                        <label>Nombre del reporte</label>
                                        <input type="text" class="form-control" id="nombre_reporte" name="nombre_reporte" placeholder="Reporte" required>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Tipo de reporte</label>
                                        <select class="form-control select2" style="width: 100%" name="tipo_reporte" required>
                                            <option value="R2">Reporte de IP bloqueadas</option>
                                            <option value="R3">Reporte de tipo de ataques</option>
                                            <option value="R4">Reporte de ataques por dominios</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <label>Rango de tiempo de reporte</label>
                                        <select class="form-control select2" style="width: 100%" name="rango_reporte" required>
                                            <option value="T1">1 Semana</option>
                                            <option value="T2">1 Mes</option>
                                            <option value="T3">2 Meses</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12" id="grupoDominioReporte" style="display: none;">
                                        <label>Dominio</label>
                                        <select class="form-control select2" style="width: 100%" name="dominio_reporte" id="dominio_reporte">
                                            <option value="">Todos los dominios</option>
                                            <?php
                                                $consult = mysqli_query($link,"SELECT
                                                                                LOWER(
                                                                                    TRIM(
                                                                                        IF(
                                                                                            LOWER(TRIM(server)) LIKE 'www.%',
                                                                                            SUBSTRING(TRIM(server), 5),
                                                                                            TRIM(server)
                                                                                        )
                                                                                    )
                                                                                ) AS host_key,
                                                                                MAX(CASE WHEN LOWER(TRIM(server)) LIKE 'www.%' THEN TRIM(server) ELSE '' END) AS host_www,
                                                                                MAX(CASE WHEN LOWER(TRIM(server)) NOT LIKE 'www.%' THEN TRIM(server) ELSE '' END) AS host_plain
                                                                            FROM bloqueo
                                                                            WHERE server IS NOT NULL
                                                                            AND TRIM(server) <> ''
                                                                            GROUP BY host_key
                                                                            ORDER BY host_key");
                                                while($row = mysqli_fetch_array($consult)){
                                                    if(!empty($row['host_key'])){
                                                        $host_label = !empty($row['host_www']) ? $row['host_www'] : $row['host_plain'];
                                                        echo '<option value="'.htmlspecialchars($row['host_key'], ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars($host_label, ENT_QUOTES, 'UTF-8').'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>


                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-primary" type="submit" id="btnActivarReporte"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal desbloquear ip ddos-->
                <div class="modal fade" id="ModalDesbloquearDdos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Desbloquear IP</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form2" class="FormUbb" action="" method="" autocomplete="off">
                                    <div class="form-group col-md-12">
                                        <label>IP</label>
                                        <input type="text" class="form-control" id="ip_edit_ddos" name="ip_edit_ddos" placeholder="IP">
                                    </div>
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-primary" type="button" id="btnDesbloqueoDdos"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>
                <!-- /.modal -->

                <!-- Modal agregar nueva ip-->
                <div class="modal fade" id="ModalNewDdos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Nueva IP whiteList</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form23" class="FormUbbNew" action="" method="" autocomplete="off">
                                    <div class="form-group col-md-12">
                                        <label>IP</label>
                                        <input type="text" class="form-control" id="ip_new_ddos" name="ip_new_ddos" placeholder="IP">
                                    </div>
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-primary" type="button" id="btnNewDdos"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>
                <!-- /.modal -->

                <!-- modal eliminar traking -->
                <div class="modal fade" id="modalDeleteAll">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Eliminar toda la data</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form2" class="FormC" action="" method="" autocomplete="off">
                                    <h4 class="text-center">¿Esta seguro de eliminar los datos?</h4>
                                        
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-danger" type="button" id="btn_eliminar_all"><i class="fa fa-trash"></i> Elimimar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                
                <!-- modal eliminar datos de vulnerabilidad -->
                <div class="modal fade" id="ModalDeleteScan">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Eliminar toda la data</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form2" class="FormC" action="" method="" autocomplete="off">
                                    <h4 class="text-center">¿Esta seguro de eliminar los datos de vulnerabilidades y historial de vulnerabilidades?</h4>
                                        
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-danger" type="button" id="btn_eliminar_all_scan"><i class="fa fa-trash"></i> Elimimar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

                <!-- Modal agregar nueva ip-->
                <div class="modal fade" id="ModalNewEscan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Nueva escaneo</h5>
                            </div>
                            <div class="modal-body">
                                <form id="Form24" class="FormUbbNewScan" action="" method="" autocomplete="off">
                                    <div class="form-group col-md-12">
                                        <label>IP o Host</label>
                                        <div class="inputFormRow col-md-12">
                                            <div class="form-group col-md-12">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-server"></i>
                                                    </div>
                                                    <input type="text" class="form-control" id="ip_host_scan" name="ip_host_scan[]" placeholder="">
                                                </div>
                                            </div>

                                            <!-- <div class="col-md-6"> 
                                                <button id="removeRow" type="button" class="btn btn-danger">Borrar</button>
                                            </div> -->
                                        </div>

                                        <div id="newRow"></div>
                                    </div>

                                    <div class="text-center col-md-12">
                                        <button class="btn btn-default" data-dismiss="modal" type="reset"><i class="fa fa-close"></i> Cancelar</button>
                                        <button class="btn btn-primary" type="button" id="btnNewEscan"><i class="fa fa-save"></i> Guardar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>
                <!-- /.modal -->

                <!-- modal de historico -->
                <div class="modal fade" id="ModalHistoria">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h5 class="modal-title">Lista de escaneos</h5>
                            </div>
                            <div class="modal-body">
                                <table id="example8" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Host</th>
                                            <th class="text-center">Resultado</th>
                                            <th class="text-center">Fecha de escaneo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>

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
                $('.select2').select2();
            });

            /* jQueryKnob */
            $(".knob").knob();

            $('.datepickerDate1').datepicker({
                autoclose: true,
                dateFormat: 'dd/mm/yy'
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function(){

                var lisenseState = <?php echo json_encode($lisenseModalState); ?>;

                function setLisenseMessage(type, message) {
                    var $alert = $('#lisenseMessage');
                    $alert.removeClass('alert-success alert-danger alert-warning')
                        .addClass(type)
                        .text(message)
                        .show();
                }

                function hideLisenseMessage() {
                    $('#lisenseMessage').hide().text('').removeClass('alert-success alert-danger alert-warning');
                }

                function renderLisenseState() {
                    if (lisenseState.active) {
                        $('#lisenseFormSection').hide();
                        $('#btnCheckLisense').hide();
                        $('#lisenseActivationKeyLabel').text(lisenseState.activation_key || '-');
                        $('#lisenseModelLabel').text(lisenseState.model || '-');
                        $('#lisenseStatusLabel')
                            .text(lisenseState.status || 'Active')
                            .toggleClass('lisense-status-active', !!lisenseState.active);
                        $('#lisenseExpirationDateLabel').text(lisenseState.expires_at_display || lisenseState.expires_at || '-');
                        $('#lisenseActiveSection').show();
                    } else {
                        $('#lisenseActiveSection').hide();
                        $('#lisenseFormSection').show();
                        $('#btnCheckLisense').show();
                    }
                }

                renderLisenseState();

                $('#modalLisense').on('shown.bs.modal', function(){
                    hideLisenseMessage();
                    $('#lisenseInputCode').val('').focus();
                    renderLisenseState();
                });

                $(document).on('keypress', '#lisenseInputCode', function(e){
                    if (e.which === 13) {
                        e.preventDefault();
                        $('#btnCheckLisense').trigger('click');
                    }
                });

                $(document).on('click', '#btnCopyLisenseKey', function(e){
                    e.preventDefault();
                    var key = $.trim($('#lisenseActivationKeyLabel').text());

                    if (!key.length || key === '-') {
                        return;
                    }

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(key).then(function(){
                            setLisenseMessage('alert-success', 'Clave de licencia copiada.');
                        });
                        return;
                    }

                    var $temp = $('<input>');
                    $('body').append($temp);
                    $temp.val(key).select();
                    document.execCommand('copy');
                    $temp.remove();
                    setLisenseMessage('alert-success', 'Clave de licencia copiada.');
                });

                $(document).on('click', '#btnCheckLisense', function(e){
                    e.preventDefault();
                    var code = $.trim($('#lisenseInputCode').val());

                    hideLisenseMessage();
                    if (!code.length) {
                        setLisenseMessage('alert-warning', 'Debes ingresar el codigo de licencia.');
                        return;
                    }

                    var $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Comprobando...');

                    $.ajax({
                        url: 'controller/verify_lisense.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { lisense_code: code }
                    }).done(function(response){
                        if (response && response.success) {
                            lisenseState.active = true;
                            lisenseState.status = response.status || 'Active';
                            lisenseState.type = response.type || lisenseState.type;
                            lisenseState.activation_key = response.activation_key || lisenseState.activation_key;
                            lisenseState.model = response.model || lisenseState.model;
                            lisenseState.masked_code = response.masked_code || lisenseState.masked_code;
                            lisenseState.days_left = parseInt(response.days_left, 10) || 0;
                            lisenseState.total_days = parseInt(response.total_days, 10) || lisenseState.total_days || 0;
                            lisenseState.expires_at = response.expires_at || lisenseState.expires_at;
                            lisenseState.expires_at_display = response.expires_at_display || lisenseState.expires_at_display;
                            lisenseState.last_checked = response.last_checked || lisenseState.last_checked;
                            renderLisenseState();
                            setLisenseMessage('alert-success', response.message || 'Proyecto activado correctamente.');
                            $('#lisenseInputCode').val('');
                        } else {
                            setLisenseMessage('alert-danger', (response && response.message) ? response.message : 'El codigo no es correcto.');
                        }
                    }).fail(function(){
                        setLisenseMessage('alert-danger', 'No se pudo verificar la licencia. Intenta nuevamente.');
                    }).always(function(){
                        $btn.prop('disabled', false).html('Comprobar');
                    });
                });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function(){
                var table1 = $('#example3').DataTable({
                    "columnDefs": [
                        { "orderable": false, "targets": 0 }
                    ]
                });

                var table2 = $('#exampleVisita').DataTable({
                    "columnDefs": [
                        { "orderable": false, "targets": 0 }
                    ]
                });

                let idEspejo = [];
                let idEspejo2 = [];

                //tabla host visita
                $(document).on('change', '#scales2', function(e){
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
                    console.log(idEspejo);
                });

                //tabla host
                $(document).on('change', '#scales3', function(e){
                    e.preventDefault();
                    var id_cdr = $(this).data('id');
                    cb = $(this).prop('checked');
                    if(cb == true) {
                        if(idEspejo2.includes(id_cdr) == false){
                            idEspejo2.push(id_cdr);
                        }
                    } else {
                        let pos = idEspejo2.indexOf(id_cdr);
                        idEspejo2.splice(pos, 1);
                    }
                    console.log(idEspejo2);
                });

                 //
                 $(document).on('click', '#eliminarSeleccionados', function(e){
                    e.preventDefault();
                    var id_cdr = idEspejo;
                    var parametro = {
                        "id_host" : id_cdr
                    }
                    //alert(id_cdr);
                    $.ajax({
                        type: 'POST',
                        url: 'controller/e_host_visita_multiple.php',
                        data: parametro,
                        success: function(data) {
                            if (data == 'bien') {
                                //alert(data);
                                not4();
                                setTimeout("location.href = 'home.php'",2000);
                            }else {
                                //alert(data);
                                not5();
                            }
                        }
                    }); 
                });


                //
                $(document).on('click', '#eliminarSeleccionadosDos', function(e){
                    e.preventDefault();
                    var id_cdr = idEspejo2;
                    var parametro = {
                        "id_host" : id_cdr
                    }
                    //alert(id_cdr);
                    $.ajax({
                        type: 'POST',
                        url: 'controller/e_host_multiple.php',
                        data: parametro,
                        success: function(data) {
                            if (data == 'bien') {
                                //alert(data);
                                not4();
                                setTimeout("location.href = 'home.php'",2000);
                            }else {
                                //alert(data);
                                not5();
                            }
                        }
                    }); 
                });

                // Manejar clic en el botón "Seleccionar Todo"
                $('#seleccionarTodo').on('click', function() {
                    // Obtener el estado del checkbox "Seleccionar Todo"
                    var seleccionarTodo = this.checked;

                    // Seleccionar/deseleccionar todos los checkboxes en la tabla
                    $('.seleccionar').each(function() {
                        this.checked = seleccionarTodo;
                    });

                    idEspejo = obtenerIDsSeleccionados();
                    console.log('IDs seleccionados:', idEspejo);
                });


                // Manejar clic en el botón "Seleccionar Todo"
                $('#seleccionarTodoDos').on('click', function() {
                    // Obtener el estado del checkbox "Seleccionar Todo"
                    var seleccionarTodo = this.checked;

                    // Seleccionar/deseleccionar todos los checkboxes en la tabla
                    $('.seleccionar2').each(function() {
                        this.checked = seleccionarTodo;
                    });

                    idEspejo2 = obtenerIDsSeleccionadosDos();
                    console.log('IDs seleccionados:', idEspejo2);
                });

                function obtenerIDsSeleccionados() {
                    var idsSeleccionados = [];
                    $('.seleccionar:checked').each(function() {
                        var id = $(this).data('id');
                        idsSeleccionados.push(id);
                    });
                    return idsSeleccionados;
                }

                function obtenerIDsSeleccionadosDos() {
                    var idsSeleccionados = [];
                    $('.seleccionar2:checked').each(function() {
                        var id = $(this).data('id');
                        idsSeleccionados.push(id);
                    });
                    return idsSeleccionados;
                }
            });
        </script>

        <!-- eliminar host bloqueo -->
        <script type="text/javascript">
            $(document).ready(function(){
                $(document).on('click', '#btnEHost', function(e){
                    e.preventDefault();
                    var nombre_host = $(this).attr('type');
                    //alert(nombre_host);
                    $.ajax({
                        url: 'controller/e_host.php',
                        type: 'POST',
                        data: 'nombre='+nombre_host,
                        dataType: 'html',
                        success: function(data) {
                            if (data == 'bien') {
                                not4();
                                setTimeout("location.href = 'home.php'",3000);
                            } else {
                                not5();
                            }
                        }
                    });    
                });
            });
        </script>

        <!-- eliminar host visitas -->
        <script type="text/javascript">
            $(document).ready(function(){
                $(document).on('click', '#btnEHostVisita', function(e){
                    e.preventDefault();
                    $('#modalConformarEliminar').modal('show');
                    var nombre_host = $(this).attr('type');
                    $("#nombre_hostVisita").val(nombre_host);   
                });

                $(document).on('click', '#btnEliminar2', function(e){
                    e.preventDefault();
                    var nombre_host = $("#nombre_hostVisita").val();
                    //alert(nombre_host);
                    $.ajax({
                        url: 'controller/e_host_visita.php',
                        type: 'POST',
                        data: 'nombre='+nombre_host+'&opcion=1',
                        dataType: 'html',
                        success: function(data) {
                            if (data == 'bien') {
                                not4();
                                setTimeout("location.href = 'home.php'",3000);
                            } else {
                                not5();
                            }
                        }
                    });    
                });

                $(document).on('click', '#btnEliminar1', function(e){
                    e.preventDefault();
                    $('#modalConformarEliminar').modal('hide');
                });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function(){
                //mostrar la info
                $(document).on('click', '#btnViewData', function(e){
                  e.preventDefault();
                  var ip = $(this).data('ip');
                  $('#ip_edit').val(ip);
                });

                // bloquear ip
                $(document).on('click', '#btnBloquearIp', function(e){
                    e.preventDefault();
                    var id_ip = $(this).data('ip');
                    //   alert(ip);
                    $("#overlayListaWaf").css("display", "block");
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
                            $("#overlayListaWaf").css("display", "none");
                            setTimeout("location.href = 'home.php'",2000);
                        } else{
                            not5();
                            $("#overlayListaWaf").css("display", "none");
                        }
                        }
                    }); 
                });

                // agregar a whitelist
                $(document).on('click', '#btnWhiteList', function(e){
                    e.preventDefault();
                    var id_bloqueo = $(this).data('id');
                    //alert(id_bloqueo);
                    $("#overlayListaWaf").css("display", "block");
                    var parametro = {
                        "id_bloqueo" : id_bloqueo
                    }
                    $.ajax({
                        type: 'POST',
                        url: 'controller/g_whiteListBloqueo.php',
                        data: parametro,
                        success: function(data) {
                            if (data == 'bien') {
                                not4();
                                $("#overlayListaWaf").css("display", "none");
                                //alert(data);
                                setTimeout("location.href = 'home.php'",2000);
                            } else{
                                //alert(data);
                                not5();
                                $("#overlayListaWaf").css("display", "none");
                            }
                        }
                    }); 
                });

                //guardar la ip
                $(document).on('click', '#btnDesbloqueo', function(e){
                  e.preventDefault();
                  $("#overlayListaWaf").css("display", "block");
                  //alert('entro');
                  $.ajax({
                    type: 'POST',
                    url: 'controller/g_white_ip.php',
                    data: $('.FormUb').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not1();
                        $("#overlayListaWaf").css("display", "none");
                        setTimeout("location.href = 'home.php'",3000);
                      }else{
                        //alert(data);
                        not2();
                        $("#overlayListaWaf").css("display", "none");
                      }
                    }
                  });      
                });

              });
        </script>

        <script type="text/javascript">
            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip({html:true, container:'body'});
            });
        </script>

        <!-- table principal ataques del waf-->
        <script type="text/javascript">
            $(document).ready(function(){
                var table = $('#example1').DataTable({
                     "order": [[2, 'desc']],
                     "processing": true,
                     "serverSide": true,
                     "ajax": {
                        "url": "ajax_table/ajax_table.php",
                        "data": function (d) {
                            d.host_key = $('#dropdown1').val();
                        }
                     },
                     "createdRow": function ( row, data, index ) {
                        //
                        if(data[0]){
                            $('td', row).eq(0).addClass("details-control");
                            $('td', row).eq(0).html('');
                        }

                        //
                        if (data[2]) {
                            //$('td', row).eq(2).append('<i class="fa fa-clock-o icono-relog"></i>');
                        }

                        //
                        if (data[4]) {
                            $('td', row).eq(4).append('<i class="fa fa-server icono-relog"></i>');
                        }

                        // 
                        if (data[3]) {
                            // Extraer IP desde el texto HTML
                            let textoConHtml = data[3];  
                            let ip_busqueda = textoConHtml.match(/\b\d{1,3}(?:\.\d{1,3}){3}\b/);
                            ip_busqueda = ip_busqueda ? ip_busqueda[0] : "";

                            let cell = $('td', row).eq(3);

                            if(ip_busqueda){        
                                $.ajax({
                                    url: `https://stat.ripe.net/data/whois/data.json?resource=${ip_busqueda}`,
                                    method: 'GET',
                                    success: function(respuesta){
                                        let contenido = parseRipeData1(respuesta);
                                        
                                        cell.attr('data-toggle', 'tooltip')
                                            .attr('data-html', 'true')
                                            .attr('title', contenido)
                                            .tooltip({html:true, container:'body'});
                                    },
                                    error: function(){
                                        // Manejo cuando la petición falla (Error 500 u otros)
                                        let contenido = '<ul><li>No se encontraron datos WHOIS para esta IP.</li></ul>';

                                        cell.attr('data-toggle', 'tooltip')
                                            .attr('data-html', 'true')
                                            .attr('title', contenido)
                                            .tooltip({html:true, container:'body'});
                                    }
                                });
                            } else {
                                // En caso que la IP no se pueda extraer
                                let contenido = '<ul><li>IP no válida o no disponible.</li></ul>';

                                cell.attr('data-toggle', 'tooltip')
                                    .attr('data-html', 'true')
                                    .attr('title', contenido)
                                    .tooltip({html:true, container:'body'});
                            }
                        }

                        //
                        if (data[7]) {
                            //saber si la ip esta bloqueada
                            var ip = data[7];
                            var parametro = {
                                "ip_bloqueo" : ip
                            }
                            $.ajax({
                                url:  'controller/return_boton.php', 
                                type: 'POST',
                                data: parametro,
                                dataType: 'html'
                            })
                            .done(function(result){
                                $('td', row).eq(7).html('');
                                if(result > 0){
                                    $('td', row).eq(7).html('<button class="btn btn-danger"><a href="#0" data-ip="'+result+'" style="color: #FFFFFF;" id="btnBloquearIp"><i class="fa fa-lock"></i> Bloquear IP</a></btutton>');
                                } else {
                                    $('td', row).eq(7).html('<button class="btn btn-primary"><a href="#0" data-toggle="modal" data-target="#ModalDesbloquear" data-ip="'+data[7]+'" style="color: #FFFFFF;" id="btnViewData"><i class="fa fa-unlock"></i> Desbloquear IP</a></btutton>');
                                }
                            })
                            .fail(function(){
                                $('td', row).eq(7).append('');
                            });
                        }

                        //
                        if (data[6]) {
                            $('td', row).eq(6).html('');
                            var ataque = data[6].toLowerCase();

                            if (ataque == 'xss' || ataque == 'Cross Site Scripting') {
                                $('td', row).eq(6).html('<a href="https://es.wikipedia.org/wiki/Cross-site_scripting" target="_black">'+data[6]+'</a>');
                            } else if(ataque == 'sqli' || ataque == 'sql injections'){
                                $('td', row).eq(6).html('<a href="https://es.wikipedia.org/wiki/Inyecci%C3%B3n_SQL" target="_black">'+data[6]+'</a>');
                            } else if(ataque == 'directory traversal'){
                                $('td', row).eq(6).html('<a href="https://en.wikipedia.org/wiki/Directory_traversal_attack" target="_black">'+data[6]+'</a>');
                            } else if(ataque == 'internal rules'){
                                $('td', row).eq(6).html('<a role="menuitem" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalInfo">'+data[6]+'</a>');
                            } else if(ataque == 'rfi'){
                                $('td', row).eq(6).html('<a href="https://es.wikipedia.org/wiki/Remote_File_Inclusion" target="_black">'+data[6]+'</a>');
                            } else {
                                $('td', row).eq(6).html('<a href="#">'+data[6]+'</a>');
                            }
                        }

                        //
                        if (data[5]) {
                            $('td', row).eq(5).html('');
                            var url = data[5];
                            var size = url.length;

                            if (size <= 24) {
                                $('td', row).eq(5).html(url);
                            } else {
                                $('td', row).eq(5).html('<a role="menuitem" tabindex="-1" href="#0" data-toggle="modal" data-target="#modalUrl" id="btnUrl" data-url="'+data[5]+'">Ver mas <i class="fa fa-plus"></i></a>');
                            }
                        }
                    }
                });

                $('#dropdown1').on('change',function(){
                        table.ajax.reload();
                        //actualizar grafica
                        var server_name = this.value;
                        var server_label = server_name === '' ? 'Todos los host' : $('#dropdown1 option:selected').text();
                        // $('#line-chart2').html('');
                        $('#overlayListaWaf').css("display", "block");
                        $('#containerPastelTipoAtaqueDomino').html('');
                        $("#div_tipo_ataque_3").css("display", "none");
                        $("#div_tipo_ataque_4").css("display", "none");
                        var parametro = {
                            "server_name" : server_name,
                            "server_label" : server_label
                        }
                        $.ajax({
                            url:  'controller/new_grafica_tres.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            // $('#line-chart2').html(''); 
                            $('#containerPastelTipoAtaqueDomino').html('');
                            $("#div_tipo_ataque_3").css("display", "none");
                            $("#div_tipo_ataque_4").css("display", "none");
                            // $('#line-chart2').html(data); // mostrar la data
                            $("#div_tipo_ataque_3").css("display", "block");
                            $("#div_tipo_ataque_4").css("display", "block");
                            $('#containerPastelTipoAtaqueDomino').html(data);
                            $('#overlayListaWaf').css("display", "none");
                        })
                        .fail(function(){
                            // $('#line-chart2').html('');
                            $("#div_tipo_ataque_3").css("display", "none");
                            $("#div_tipo_ataque_4").css("display", "none");
                            $('#containerPastelTipoAtaqueDomino').html('');
                            $('#overlayListaWaf').css("display", "none");
                        });
                });

                function format (callback, d) {
                    //return d[0];
                    tbody = '';

                    tbody += '<tr style="background: aliceblue;"><td>' + d[0] + '</td></tr>';
                    tbody += '<tr style="text-align: center;"><td> <button class="btn btn-info"><a href="#0" data-id="'+d[1]+'" style="color: #FFFFFF;" id="btnWhiteList"><i class="fa fa-unlock"></i> Add Whitelist rules</a></btutton> </td></tr>';

                    callback($('<table id="example" class="table table-bordered table-striped" width="100%">' + tbody + '</table>')).show();
                }

                // Array to track the ids of the details displayed rows
                var detailRows = [];

                $('#example1 tbody').on( 'click', 'tr td.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = table.row( tr );
                    var idx = $.inArray( tr.attr('id'), detailRows );
 
                    if ( row.child.isShown() ) {
                        tr.removeClass( 'details' );
                        row.child.hide();
                     
                        // Remove from the 'open' array
                        detailRows.splice( idx, 1 );
                    }
                    else {
                        format(row.child, row.data());
                        tr.addClass( 'details' );

                        // Add to the 'open' array
                        if ( idx === -1 ) {
                            detailRows.push( tr.attr('id') );
                        }
                    }
                });

                $(document).on('click', '#btnUrl', function(e){
                    e.preventDefault();
                    var url = $(this).data('url');
                    //alert(url);
                    $('#textoUrl').html('');
                    $('#textoUrl').html(url);
                });

            });

            function parseRipeData1(respuesta) {
                let html = '<ul style="padding-left:15px; text-align:left;">';

                if(respuesta.data && respuesta.data.records && respuesta.data.records.length > 0){
                    let records = respuesta.data.records[0];

                    if(records.length > 0){
                        records.forEach(function(record) {
                            html += `<li><strong>${record.key}:</strong> ${record.value}</li>`;
                        });
                    } else {
                        html += '<li>No se encontraron datos WHOIS para esta IP.</li>';
                    }

                } else {
                    html += '<li>No se encontraron datos WHOIS para esta IP.</li>';
                }

                html += '</ul>';
                return html;
            }
        </script>

        <!-- table de busqueda -->
        <script type="text/javascript">
            $(document).ready(function(){
                $('#example2').DataTable({
                     "ordering":false,
                     "processing": true,
                     "serverSide": true,
                     "searching": true,
                     "ajax": "ajax_table/ajax_table_master.php",
                     "createdRow": function ( row, data, index ) {
                        //
                        if (data[1]) {
                            $('td', row).eq(1).append('<i class="fa fa-clock-o icono-relog"></i>');
                        }

                        //
                        // if (data[2]) {
                        //     $('td', row).eq(2).append('<span class="f16"><i class="flag ac icono-bandera"></i></span>');
                        //     //optener la bandera del pais
                        //     var ip = data[2];
                        //     var parametro = {
                        //         "ip_bandera" : ip
                        //     }
                        //     $.ajax({
                        //         url:  'controller/return_badera.php', 
                        //         type: 'POST',
                        //         data: parametro,
                        //         dataType: 'html'
                        //     })
                        //     .done(function(result){
                        //         $('td', row).eq(2).html('');
                        //         $('td', row).eq(2).html(result+' '+data[2]);
                        //     })
                        //     .fail(function(){
                        //         $('td', row).eq(2).append('<span class="f16"><i class="flag ac icono-bandera"></i></span>');
                        //     });
                        // }

                        //
                        if (data[3]) {
                            var nombre = "";
                            if(data[3] == "ssh-failed"){
                                nombre = "Brute force attack ssh";
                            } else if(data[3] == "nginx-naxsi"){
                                nombre = "WAF_BLOCK";
                            } else {
                                nombre = data[3];
                            }
                            $('td', row).eq(3).html('<i class="fa fa-shield icono-relog"></i> '+nombre);
                        }
                    }
                });

                var table2 = $('#example2').DataTable();

                $('#busqueda1', this).on( 'keyup change', function () {
                    if ( table2.column(2).search() !== this.value ) {
                        table2
                        .column(2)
                        .search( this.value )
                        .draw();
                    }
                });

                $('#busqueda2', this).on( 'keyup change', function () {
                    if ( table2.column(3).search() !== this.value ) {
                        table2
                        .column(3)
                        .search( this.value )
                        .draw();
                    }
                });
            });
        </script>

        <!-- actualizar datos -->
        <script type="text/javascript">
            $(document).ready(function(){
                //actualizar bloqueos waf
                $('#update').click(function () {
                    $.ajax({
                      url: 'controller/ajax_convertion.php', 
                        success: function (result) {
                            not6();
                            setTimeout("location.href = 'home.php'",3000);
                        }
                    })
                });

                //actualizar bloqueos fuerza bruta
                $('#update2').click(function () {
                    $.ajax({
                      url: 'controller/ajax_convertion_bloqueo_ip.php', 
                        success: function (result) {
                            not6();
                            setTimeout("location.href = 'home.php'",3000);
                        }
                    })
                });

                //actualizar datos de resumen
                $('#update3').click(function () {
                    $.ajax({
                      url: 'controller/update_datos_resumen.php', 
                        success: function (result) {
                            not6();
                            setTimeout("location.href = 'home.php'",2000);
                        }
                    })
                });

                //actualizar visitas
                $('#update4').click(function () {
                    //alert('ss');
                    $.ajax({
                      url: 'controller/ajax_convertion_visitas.php',
                        success: function (result) {
                            not6();
                            setTimeout("location.href = 'home.php'",3000);
                        }
                    })
                });
            });
        </script>

        <!-- eliminar bloqueos -->
        <script type="text/javascript">
            $('#btnE').click(function () {
                $.ajax({
                  url: 'controller/e_bloqueo.php',
                  type: 'POST',
                  data: $('.FormB').serialize(),
                    success: function (result) {
                        not4();
                        setTimeout("location.href = 'home.php'",3000);
                    }
                })
            });
        </script>

         <!-- eliminar datos -->
        <script type="text/javascript">
            $('#btnEliminar').click(function () {
                $.ajax({
                  url: 'controller/e_data_bloque.php',
                  type: 'POST',
                  data: $('.FormC').serialize(),
                    success: function (result) {
                        not4();
                        setTimeout("location.href = 'home.php'",3000);
                    }
                })
            });
        </script>

        <!-- gradica de ip bloqueadas por fechas Uno-->
        <script type="text/javascript">
            $(document).ready(function(){
                $.getJSON('controller/ajax_home_charts.php?action=timeline_principal', function(data) {
                    var area = new Morris.Area({
                        element: 'revenue-chart2',
                        resize: true,
                        data: data,
                        xkey: 'y',
                        ykeys: ['item1', 'item2'],
                        labels: ['Total Fuerza Bruta', 'Total WAF'],
                        lineColors: ['#a0d0e0', '#3c8dbc'],
                        hideHover: 'auto'
                    });
                });
            });
        </script>

        <!-- gradica de ip bloqueadas por fechas Dos-->
        <script type="text/javascript">
            // $(document).ready(function(){
            //     $(document).on('click', '#ataquesGrafica', function(e){
            //         e.preventDefault();
            //         var line = new Morris.Line({
            //             element          : 'line-chart2',
            //             resize           : true,
            //             data             : [
            //              <
            //                 $consult = mysqli_query($link,"SELECT * FROM grafica_bloqueo");
                            
            //                 while($rows = mysqli_fetch_array($consult))
            //                 {
            //                     $fecha_bloqueo = $rows['fecha_bloqueo'];
            //                     $total_bloqueo = $rows['total_bloqueo'];

            //                     echo '{ y: "'.$fecha_bloqueo.'", item1: '.$total_bloqueo.'},';
            //                 }
            //             ?>
            //             ],
            //             xkey             : 'y',
            //             ykeys            : ['item1'],
            //             labels           : ['Total Ataques'],
            //             lineColors       : ['#0a63a4'],
            //             lineWidth        : 2,
            //             hideHover        : 'auto',
            //             gridTextColor    : "#888888",
            //             gridStrokeWidth  : 0.4,
            //             pointSize        : 4,
            //             pointStrokeColors: ["#0a63a4"],
            //             gridLineColor    : "#888888",
            //             gridTextFamily   : 'Open Sans',
            //             gridTextSize     : 10
            //         });
            //     });
            // });
        </script>

        <!-- grafica de tipos de ataques en la pestaña de paises-->
        <script>
            $(document).ready(function(){
                $(document).on('click', '#graficoP', function(e){
                    $(".chart_tipoAtaque").attr("id","chart_tipoAtaque");
                    $("#chart_tipoAtaque").html('<div class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i><br>Cargando reglas...</div>');
                    
                    $.getJSON('controller/ajax_home_charts.php?action=tipos_ataque_global', function(data) {
                        var chart_tipoAtaque = c3.generate({
                            bindto: '#chart_tipoAtaque',
                            data: {
                                columns: data,
                                type : 'donut'
                            },
                        donut: {
                            title: "Tipos Ataques",
                            label: {
                                format: function (value, ratio, id) {
                                    return d3.format(',')(value);
                                }
                            },
                        },
                        tooltip: {
                            grouped: false,
                            format: {
                                value: function (value, ratio, id) {
                                    return d3.format(',')(value);
                                }
                            }
                        },
                        legend: {
                            show: true
                        },
                    });
                    $("#chart_tipoAtaque").html(chart_tipoAtaque.element);
                });
            });
        </script>

        <!-- graficas de ataques del waf y ataque de fuerza bruta -->
        <script>
        var chartDataSources = null;

        $(document).ready(function() {
            // Mostrar spinner de carga
            $("#chart_rangoAtaques").html('<div style="text-align:center; padding: 50px;"><i class="fa fa-spinner fa-spin fa-3x" style="color:#0173ed"></i><br><br>Cargando gráfica...</div>');
            
            $.ajax({
                url: 'controller/ajax_chart_rango_ataques.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    chartDataSources = {
                        hoy: [
                            { label: "WAF", value: response.hoy.waf, color: "#0173ed", icon: "img/grafica/bloqueo_waf.png" },
                            { label: "Bots", value: response.hoy.bots, color: "#00ccab", icon: "img/grafica/bloqueo_bot.png" },
                            { label: "Fuerza Bruta", value: response.hoy.fuerza, color: "#ff4a47", icon: "img/grafica/bloqueo_ip.png" }
                        ],
                        semana: [
                            { label: "WAF", value: response.semana.waf, color: "#0173ed", icon: "img/grafica/bloqueo_waf.png" },
                            { label: "Bots", value: response.semana.bots, color: "#00ccab", icon: "img/grafica/bloqueo_bot.png" },
                            { label: "Fuerza Bruta", value: response.semana.fuerza, color: "#ff4a47", icon: "img/grafica/bloqueo_ip.png" }
                        ],
                        mes: [
                            { label: "WAF", value: response.mes.waf, color: "#0173ed", icon: "img/grafica/bloqueo_waf.png" },
                            { label: "Bots", value: response.mes.bots, color: "#00ccab", icon: "img/grafica/bloqueo_bot.png" },
                            { label: "Fuerza Bruta", value: response.mes.fuerza, color: "#ff4a47", icon: "img/grafica/bloqueo_ip.png" }
                        ]
                    };

                    // Inicializar botones
                    $("#rangoAtaquesTabs .btn").off('click').click(function() {
                        if (!chartDataSources) return;
                        $("#rangoAtaquesTabs .btn").removeClass("btn-primary").addClass("btn-default");
                        $(this).removeClass("btn-default").addClass("btn-primary");
                        
                        var target = $(this).data("target");
                        renderCustomD3Chart(target);
                    });

                    // Renderizar el de "HOY" por defecto
                    renderCustomD3Chart("hoy");
                    
                    // Re-render en resize
                    $(window).off('resize.d3chart').on('resize.d3chart', function() {
                        if (!chartDataSources) return;
                        var current = $("#rangoAtaquesTabs .btn-primary").data("target");
                        if (current) renderCustomD3Chart(current);
                    });
                },
                error: function() {
                    $("#chart_rangoAtaques").html('<div style="text-align:center; padding: 50px; color:red;">Error al cargar datos.</div>');
                }
            });
        });

        function renderCustomD3Chart(range) {
            var data = chartDataSources[range];
            var total = d3.sum(data, function(d) { return d.value; });
            
            data.forEach(function(d) {
                d.percentage = total > 0 ? Math.round((d.value / total) * 100) : 0;
            });

            var container = d3.select("#d3_custom_chart_container");
            container.html(""); // limpiar

            var width = container.node().getBoundingClientRect().width || 900;
            var height = 500;
            
            var isMobile = width < 700;
            
            var cx = isMobile ? width / 2 : width * 0.70;
            var cy = isMobile ? height * 0.65 : height / 2;
            var radius = isMobile ? width * 0.35 : Math.min(width * 0.45, height) / 2 * 0.8;
            var innerRadius = radius * 0.35;

            var svg = container.append("svg")
                .attr("width", "100%")
                .attr("height", height);

            var pie = d3.pie()
                .value(function(d) { return d.value; })
                .sort(null)
                .padAngle(0.015);

            var arc = d3.arc()
                .innerRadius(innerRadius)
                .outerRadius(radius);

            var arcHover = d3.arc()
                .innerRadius(innerRadius)
                .outerRadius(radius * 1.05);

            var arcs = pie(data);

            var chartGroup = svg.append("g")
                .attr("transform", "translate(" + cx + "," + cy + ")");

            // Dibujar Slices
            chartGroup.selectAll("path")
                .data(arcs)
                .enter().append("path")
                .attr("d", arc)
                .attr("fill", function(d) { return d.data.color; })
                .style("cursor", "pointer")
                .on("mouseover", function(d) {
                    d3.select(this).transition().duration(200).attr("d", arcHover);
                })
                .on("mouseout", function(d) {
                    d3.select(this).transition().duration(200).attr("d", arc);
                })
                .on("click", function(d) {
                    // Click redirige a ip_bloqueadas
                    var tipo_id = d.data.label === "WAF" ? "Bloqueos WAF" : (d.data.label === "Bots" ? "Bloqueos Bots" : "Bloqueos Fuerza Bruta");
                    var r = range === "hoy" ? "HOY" : (range === "semana" ? "SEMANA" : "MES");
                    location.href = "ip_bloqueadas.php?tipo!=" + tipo_id + "&rango!=" + r;
                });

            // Iconos en el pastel
            chartGroup.selectAll("image")
                .data(arcs)
                .enter().append("image")
                .attr("href", function(d) { return d.data.icon; })
                .attr("width", 32)
                .attr("height", 32)
                .attr("x", function(d) { return arc.centroid(d)[0] - 16; })
                .attr("y", function(d) { return arc.centroid(d)[1] - 16; })
                .style("filter", "brightness(0) invert(1) drop-shadow(0px 2px 4px rgba(0,0,0,0.5))");

            // Textos a la izquierda
            var textX = isMobile ? width * 0.1 : width * 0.15;
            var startY = isMobile ? 40 : height * 0.25;
            var spacingY = isMobile ? 60 : height * 0.25;

            var polylineOuterArc = d3.arc()
                .innerRadius(radius)
                .outerRadius(radius);

            data.forEach(function(d, i) {
                var arcData = arcs[i];
                if(arcData.value === 0) return; // Omitir si no hay datos
                
                var currentY = startY + (i * spacingY);
                var legendGroup = svg.append("g");

                var textElem = legendGroup.append("text")
                    .attr("x", textX)
                    .attr("y", currentY)
                    .attr("fill", d.color)
                    .attr("font-size", isMobile ? "20px" : "40px")
                    .attr("font-family", "Arial, sans-serif")
                    .attr("font-weight", "bold")
                    .text(d.label);
                    
                var bbox = textElem.node().getBBox();
                
                legendGroup.append("text")
                    .attr("x", textX + bbox.width + (isMobile ? 10 : 25))
                    .attr("y", currentY)
                    .attr("fill", d.color)
                    .attr("font-size", isMobile ? "20px" : "40px")
                    .attr("font-family", "Arial, sans-serif")
                    .style("opacity", 0.7)
                    .text(d.percentage + "%");
                    
                legendGroup.append("text")
                    .attr("x", textX)
                    .attr("y", currentY + (isMobile ? 20 : 30))
                    .attr("fill", "#a0aab5")
                    .attr("font-size", isMobile ? "12px" : "15px")
                    .attr("font-family", "Arial, sans-serif")
                    .text("Total de bloqueos: " + d.value);

                if (!isMobile) {
                    var lineStartX = textX + bbox.width + 120; 
                    var lineStartY = currentY - (isMobile ? 5 : 12);
                    
                    var c = polylineOuterArc.centroid(arcData);
                    var targetX = cx + c[0];
                    var targetY = cy + c[1];
                    
                    var polylineX2 = width * 0.45;
                    
                    var points = lineStartX + "," + lineStartY + " " + polylineX2 + "," + lineStartY + " " + targetX + "," + targetY;
                    
                    svg.append("polyline")
                        .attr("points", points)
                        .attr("fill", "none")
                        .attr("stroke", "#4b5c6e")
                        .attr("stroke-width", 1.5)
                        .style("opacity", 0.6);
                }
            });
        }
        </script>
        <script>
            $(document).ready(function(){
        </script>

        <!-- grafica de tipos de ataques -->
        <script>
            $(document).on('click','#grafico',function() {
                $(".chart1").attr("id","chart1");
                $(".chart2").attr("id","chart2");
                $(".chart3").attr("id","chart3");
                
                $(".chart1, .chart2, .chart3").html('<div class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i></div>');

                $.getJSON('controller/ajax_home_charts.php?action=tipos_ataque_global', function(data) {
                    // Reutilizamos el mismo JSON para las 3 donuts (o puedes crear acciones separadas para cada rango)
                    var common_config = {
                        data: {
                            columns: data,
                            type : 'donut'
                        },
                        donut: {
                            label: { format: function (value) { return d3.format(',')(value); } }
                        },
                        tooltip: { format: { value: function (value) { return d3.format(',')(value); } } }
                    };

                    c3.generate(Object.assign({bindto: '#chart1', donut: {title: "Global"}}, common_config));
                    c3.generate(Object.assign({bindto: '#chart2', donut: {title: "Histórico"}}, common_config));
                    c3.generate(Object.assign({bindto: '#chart3', donut: {title: "Total"}}, common_config));
                });
            });
        </script>
        <!-- <script src="js/highcharts.js"></script>
        <script src="js/exporting.js"></script>
        <script src="js/export-data.js"></script> -->

        <script src="js/highcharts/highmaps.js"></script>
        <script src="js/highcharts/data.js"></script>
        <script src="js/highcharts/exporting.js"></script>
        <script src="js/highcharts/offline-exporting.js"></script>
        <script src="js/highcharts/world.js"></script>

        <!-- grafica de barra de dominios -->
        <script type="text/javascript">
            $(document).on('click','#graficoDominio',function() {
                $("#containerBar").html('<div class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i><br>Cargando datos de 62M registros...</div>');
                $.getJSON('controller/ajax_home_charts.php?action=ataques_por_dominio', function(data) {
                    Highcharts.chart('containerBar', {
                        chart: { type: 'column' },
                        title: { text: 'Ataques por dominios (Top 20)' },
                        xAxis: {
                            type: 'category',
                            title: { text: 'Dominios' }
                        },
                        yAxis: { title: { text: 'Total ataques' } },
                        legend: { enabled: false },
                        series: [{
                            name: 'Ataques',
                            colorByPoint: true,
                            data: data
                        }]
                    });
                });
            });
        </script>

        <!-- grafica de ataques por paises -->
        <script>
            $(document).ready(function(){
                $(document).on('click', '#graficoP', function(e){
                    e.preventDefault();
                    var line = new Morris.Line({
                        element          : 'line-chart3',
                        resize           : true,
                        data             : [
                         <?php
                            $consult = mysqli_query($link,"SELECT * FROM grafica_bloqueo");
                            
                            while($rows = mysqli_fetch_array($consult))
                            {
                                $fecha_bloqueo = $rows['fecha_bloqueo'];
                                $total_bloqueo = $rows['total_bloqueo'];

                                echo '{ y: "'.$fecha_bloqueo.'", item1: '.$total_bloqueo.'},';
                            }
                        ?>
                        ],
                        xkey             : 'y',
                        ykeys            : ['item1'],
                        labels           : ['Total Ataques'],
                        lineColors       : ['#0a63a4'],
                        lineWidth        : 2,
                        hideHover        : 'auto',
                        gridTextColor    : "#888888",
                        gridStrokeWidth  : 0.4,
                        pointSize        : 4,
                        pointStrokeColors: ['#0a63a4'],
                        gridLineColor    : "#888888",
                        gridTextFamily   : 'Open Sans',
                        gridTextSize     : 10
                    });
                });
            });
        </script>

        <!-- botoneria de seleccion -->
        <script type="text/javascript">
            $(document).on('change','#dropdown2',function() {
                var opc = $("#dropdown2").val();

                if (opc == 'tipo1') {
                    $('#container3').show();
                    $('#container4').hide();
                } else {
                    $('#container3').hide();
                    $('#container4').show();
                }
            });
        </script>
        
        <!-- grafica de ataque ultima semana  1-->
        <script>
            $(document).on('click','#graficoP',function() {
                $("#container3").html('<div class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i><br>Cargando...</div>');
                $.getJSON('controller/ajax_home_charts.php?action=tipos_ataque_global', function(raw_data) {
                    var formatted_data = raw_data.map(function(item) {
                        return { name: item[0], y: item[1] };
                    });
                    Highcharts.chart('container3', {
                      chart: { type: 'pie' },
                      title: { text: 'Grafico de tipos de ataques' },
                      series: [{
                        name: 'Ataques',
                        colorByPoint: true,
                        data: formatted_data
                      }]
                    });
                });
            });
        </script>

        <!-- grafica de ataques ultima semana 2 -->
        <script>
            $(document).on('click','#graficoP',function() {
                $("#container4").html('<div class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i><br>Cargando...</div>');
                $.getJSON('controller/ajax_home_charts.php?action=ataques_por_pais', function(data) {
                    Highcharts.chart('container4', {
                      chart: { type: 'pie' },
                      title: { text: 'Grafico de tipos de ataques por pais' },
                      series: [{
                        name: 'Países',
                        colorByPoint: true,
                        data: data
                      }]
                    });
                });
            });
        </script>

        <!-- botoneria de seleccion -->
        <script type="text/javascript">
            $(document).on('change','#dropdown3',function() {
                var opc = $("#dropdown3").val();

                if (opc == 'tipo3') {
                    $('#container5').show();
                    $('#container6').hide();
                } else {
                    $('#container5').hide();
                    $('#container6').show();
                }
            });
        </script>

        <!-- rango de fechas -->
        <script>
            $(function () {
                //Date range picker
                $('#reservation').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');
                        $('#ccontainer5').html('');  
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2
                        }
                        $.ajax({
                            url:  'controller/new_grafica.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $('#container5').html('');    
                            $('#container5').html(data); // mostrar la data
                        })
                        .fail(function(){
                            $('#container5').html('');
                        });
                    });
            })
        </script>

        <!-- rango de fechas Grafica Uno-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaUno').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');
                        $("#overlayGraficaPrincipal").css("display", "block");
                        $('#revenue-chart2').html('');  
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2
                        }
                        //alert(parametro);
                        $.ajax({
                            url:  'controller/new_grafica_principal.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $("#overlayGraficaPrincipal").css("display", "none");
                            $('#revenue-chart2').html('');    
                            $('#revenue-chart2').html(data); // mostrar la data
                        })
                        .fail(function(){
                            $("#overlayGraficaPrincipal").css("display", "none");
                            $('#revenue-chart2').html('');
                        });
                    });
            })
        </script>

        <!-- rango de fechas Grafica Dos-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaDos').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');
                        $('#line-chart2').html('');  
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2
                        }
                        $.ajax({
                            url:  'controller/new_grafica_dos.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $('#line-chart2').html('');    
                            $('#line-chart2').html(data); // mostrar la data
                        })
                        .fail(function(){
                            $('#line-chart2').html('');
                        });
                    });
            })
        </script>

        <!-- rango de fechas Grafica Tres-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaTres').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');
                        //alert(fecha1+' '+fecha2);
                        $('#listaPaises').html('');
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2
                        }
                        $.ajax({
                            url:  'controller/new_grafica_ataque_pais.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $('#listaPaises').html('');    
                            $('#listaPaises').html(data); // mostrar la data
                        })
                        .fail(function(){
                            $('#listaPaises').html('');
                        });
                    });
            })
        </script>

        <!-- rango de fechas Grafica Cuatro-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaCuatro').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');
                        //alert(fecha1+' '+fecha2);
                        $('#containerBar').html('');
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2
                        }
                        $.ajax({
                            url:  'controller/new_grafica_dominios.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $('#containerBar').html('');    
                            $('#containerBar').html(data); // mostrar la data
                        })
                        .fail(function(){
                            $('#containerBar').html('');
                        });
                    });
            })
        </script>

        <!-- rango de fechas Grafica visitas-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaVisita').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');

                        var fechaUno = start.format('YYYY-MM-DD');
                        var fechaDos = end.format('YYYY-MM-DD');

                        var nombredominio = $('#nombredominio').val();
                        //alert(nombredominio);
                        $("#overlayGraficaVisita").css("display", "block");
                        // $('#estadisticaRango').html('');
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2,
                            "nombredominio": nombredominio,
                        }
                        $.ajax({
                            url:  'controller/new_grafica_visita.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $('#estadisticaRango').html('');    
                            $('#estadisticaRango').html(data); // mostrar la data
                            $("#overlayGraficaVisita").css("display", "none");
                            GraficaDominio(fechaUno, fechaDos);
                        })
                        .fail(function(){
                            $('#estadisticaRango').html('');
                            $("#overlayGraficaVisita").css("display", "none");
                        });
                    });
            })
        </script>

        <!-- grafica de line a de tiempo de visitas -->
        <script>
            $(document).ready(function(){
                $(document).on('click', '#graficoVisita', function(e){
                    e.preventDefault();
                    $("#line-chart4").html('<div class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i></div>');
                    $.getJSON('controller/ajax_home_charts.php?action=timeline_visitas', function(data) {
                        $("#line-chart4").empty();
                        var line = new Morris.Line({
                            element          : 'line-chart4',
                            resize           : true,
                            data: data,
                            xkey: 'y',
                            ykeys: ['item1'],
                            labels: ['Total visitas'],
                            lineColors: ['#0a63a4'],
                            lineWidth: 2,
                            hideHover: 'auto',
                            gridTextColor: "#888888",
                            gridStrokeWidth: 0.4,
                            pointSize: 4,
                            pointStrokeColors: ["#0a63a4"],
                            gridLineColor: "#888888",
                            gridTextFamily: "Open Sans",
                            gridTextSize: 10
                        });
                    });
                });
            });
        </script>

        <!-- grafica de barra de visita por dominios -->
        <script type="text/javascript">
            $(document).on('click','#graficoVisita',function() {
                $("#containerBarDominio").html('<div class="loading"><i class="fa fa-refresh fa-spin fa-2x"></i><br>Cargando datos de 62M registros...</div>');
                $.getJSON('controller/ajax_home_charts.php?action=visitas_por_dominio', function(data) {
                    Highcharts.chart('containerBarDominio', {
                        chart: { type: 'column' },
                        title: { text: 'Visitas por dominio (Top 20)' },
                        xAxis: {
                            type: 'category',
                            title: { text: 'Dominios' }
                        },
                        yAxis: { title: { text: 'Total visitas' } },
                        legend: { enabled: false },
                        series: [{
                            name: 'Visitas',
                            colorByPoint: true,
                            data: data
                        }]
                    });
                });
            });
        </script>

        <!-- rango de fechas Grafica de visita por paises-->
        <script>
            function GraficaDominio(fecha1, fecha2) {
                // alert(fecha1+' - '+fecha2);
                $('#containerBarDominio').html('');
                var parametro = {
                    "fecha1" : fecha1,
                    "fecha2" : fecha2
                }
                $.ajax({
                    url:  'controller/new_grafica_visita_dominio.php', 
                    type: 'POST',
                    data: parametro,
                    dataType: 'html'
                })
                .done(function(data){  
                    $('#containerBarDominio').html('');    
                    $('#containerBarDominio').html(data); // mostrar la data
                })
                .fail(function(){
                    $('#containerBarDominio').html('');
                });
            }
        </script>

        <!-- tabla visitas -->
        <script type="text/javascript">
            $(document).ready(function(){
                var table4 = $('#example4').DataTable({
                     "order": [[1, 'desc']],
                     "processing": true,
                     "serverSide": true,
                     "ajax": {
                        "url": "ajax_table/ajax_table_visitas.php",
                        "data": function (d) {
                            d.domain_key = $('#dropdown4').val();
                        }
                     },
                     "createdRow": function ( row, data, index ) {
                        //
                        if (data[1]) {
                            $('td', row).eq(1).append('<i class="fa fa-clock-o icono-relog"></i>');
                        }

                        //
                        // if (data[2]) {
                        //     $('td', row).eq(2).append('<span class="f16"><i class="flag ac icono-bandera"></i></span>');
                        //     //optener la bandera del pais
                        //     var ip = data[2];
                        //     var parametro = {
                        //         "ip_bandera" : ip
                        //     }
                        //     $.ajax({
                        //         url:  'controller/return_badera.php', 
                        //         type: 'POST',
                        //         data: parametro,
                        //         dataType: 'html'
                        //     })
                        //     .done(function(result){
                        //         $('td', row).eq(2).html('');
                        //         $('td', row).eq(2).html(result+' '+data[2]);
                        //     })
                        //     .fail(function(){
                        //         $('td', row).eq(2).append('<span class="f16"><i class="flag ac icono-bandera"></i></span>');
                        //     });
                        // }
                    }
                });

                $('#dropdown4').on('change',function(){
                    table4.ajax.reload();

                    //actualizar campos de graficas
                    var dominio = this.value;
                    $("#overlayGraficaVisita").css("display", "block");
                    $('#nombredominio').val(dominio);
                    $('#estadisticaRango').html('');
                    var parametro = {
                        "fecha1" : "",
                        "fecha2" : "",
                        "nombredominio" : dominio
                    }
                    $.ajax({
                        url:  'controller/new_grafica_visita.php', 
                        type: 'POST',
                        data: parametro,
                        dataType: 'html'
                    })
                    .done(function(data){  
                        $('#estadisticaRango').html('');    
                        $('#estadisticaRango').html(data); // mostrar la data
                        $("#overlayGraficaVisita").css("display", "none");
                    })
                    .fail(function(){
                        $('#estadisticaRango').html('');
                        $("#overlayGraficaVisita").css("display", "none");
                    });
                });
                  
            });
        </script>

        <!-- rango de fechas Grafica tipo ataque-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaTipoAtaque').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');
                        // $("#overlayGraficaPrincipal").css("display", "block");
                        $('#containerPastelTipoAtaque').html('');  
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2
                        }
                        // alert(parametro);
                        $.ajax({
                            url:  'controller/new_grafica_tipo_ataque.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $("#div_tipo_ataque_1").css("display", "none");
                            $("#div_tipo_ataque_2").css("display", "block");
                            $('#containerPastelTipoAtaque').html('');    
                            $('#containerPastelTipoAtaque').html(data); // mostrar la data
                        })
                        .fail(function(){
                            $("#div_tipo_ataque_1").css("display", "block");
                            $("#div_tipo_ataque_2").css("display", "none");
                            $('#containerPastelTipoAtaque').html('');
                        });
                    });
            })
        </script>
        
        <!-- grafica de chatgpt -->
        <script>
            $(document).ready(function(){
                var parametro = {
                    "fecha1" : "",
                }
                var datos;
                $.ajax({
                    url:  'controller/ajax_chatgpt.php', 
                    type: 'POST',
                    data: parametro,
                    dataType: 'json'
                })
                .done(function(data){
                    if(data != "vacio"){
                        grafica(data);
                    } else {
                        $("#webAttacksChart").css("display", "none");
                    }
                })
                .fail(function(data){
                    // alert(data);
                    $("#webAttacksChart").css("display", "none");
                });

                function grafica(datos)
                {
                    // El JSON proporcionado
                    var data = datos;

                    // Extraer datos para la gráfica
                    var labels = data.top_10_attacks.map(function(item) {
                        return item.attack;
                    });

                    var ranks = data.top_10_attacks.map(function(item) {
                        return item.frequency;
                    });

                    Highcharts.chart('webAttacksChart', {
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: 'Ranking de ataques mas frecuentes',
                        align: 'left'
                    },
                    subtitle: {
                        text: '',
                        align: 'left'
                    },
                    xAxis: {
                        categories: labels,
                        title: {
                            text: null
                        },
                        gridLineWidth: 1,
                        lineWidth: 0
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        },
                        gridLineWidth: 0
                    },
                    tooltip: {
                        valueSuffix: ' '
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: '50%',
                            dataLabels: {
                                enabled: true
                            },
                            groupPadding: 0.1
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'top',
                        x: -40,
                        y: 80,
                        floating: true,
                        borderWidth: 1,
                        backgroundColor:
                            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                        shadow: true
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                            colorByPoint: true,
                        data: ranks
                    }]
                    });
                }
            });
        </script>

        <script>
            $(document).ready(function(){
                //guardar la ip
                $(document).on('click', '#btnActivarOpenAi', function(e){
                  e.preventDefault();
                  $.ajax({
                    type: 'POST',
                    url: 'controller/g_apikey_openai.php',
                    data: $('.FormOpenAi').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not1();
                        setTimeout("location.href = 'home.php'",3000);
                      }else{
                        not2();
                      }
                    }
                  });      
                });
            });
        </script>

        <!-- tabla ddos -->
        <script type="text/javascript">
            $(document).ready(function(){
                $('#example5').DataTable({
                     "order": [[1, 'desc']],
                     "processing": true,
                     "serverSide": true,
                     "ajax": "ajax_table/ajax_table_bloqueo_ddos.php",
                     "createdRow": function ( row, data, index ) {
                        //
                        if (data[1]) {
                            $('td', row).eq(1).append('<i class="fa fa-clock-o icono-relog"></i>');
                        }

                        // 
                        if (data[2]) {
                            // Extraer IP desde el texto HTML
                            let textoConHtml = data[2];  
                            let ip_busqueda = textoConHtml.match(/\b\d{1,3}(?:\.\d{1,3}){3}\b/);
                            ip_busqueda = ip_busqueda ? ip_busqueda[0] : "";

                            let cell = $('td', row).eq(2);

                            if(ip_busqueda){        
                                $.ajax({
                                    url: `https://stat.ripe.net/data/whois/data.json?resource=${ip_busqueda}`,
                                    method: 'GET',
                                    success: function(respuesta){
                                        let contenido = parseRipeData1(respuesta);
                                        
                                        cell.attr('data-toggle', 'tooltip')
                                            .attr('data-html', 'true')
                                            .attr('title', contenido)
                                            .tooltip({html:true, container:'body'});
                                    },
                                    error: function(){
                                        // Manejo cuando la petición falla (Error 500 u otros)
                                        let contenido = '<ul><li>No se encontraron datos WHOIS para esta IP.</li></ul>';

                                        cell.attr('data-toggle', 'tooltip')
                                            .attr('data-html', 'true')
                                            .attr('title', contenido)
                                            .tooltip({html:true, container:'body'});
                                    }
                                });
                            } else {
                                // En caso que la IP no se pueda extraer
                                let contenido = '<ul><li>IP no válida o no disponible.</li></ul>';

                                cell.attr('data-toggle', 'tooltip')
                                    .attr('data-html', 'true')
                                    .attr('title', contenido)
                                    .tooltip({html:true, container:'body'});
                            }
                        }


                        if (data[5]) {
                            //saber si la ip esta bloqueada
                            var listaBlanca = data[6];
                            if(listaBlanca > 0){
                                $('td', row).eq(5).html('<button class="btn btn-danger"><a href="#0" data-ip="'+data[2]+'" style="color: #FFFFFF;" id="btnBloquearIpDdos"><i class="fa fa-lock"></i> Bloquear IP</a></btutton>');
                            } else {
                                $('td', row).eq(5).html('<button class="btn btn-primary"><a href="#0" data-toggle="modal" data-target="#ModalDesbloquearDdos" data-ip="'+data[2]+'" style="color: #FFFFFF;" id="btnViewDataDdos"><i class="fa fa-unlock"></i> Desbloquear IP</a></btutton>');
                            }
                        }

                        if(data[5]){
                            $('td', row).eq(6).css('display', 'none');
                        }
                    }
                });

                // 
                $(document).on('click', '#btnViewDataDdos', function(e){
                  e.preventDefault();
                  var ip = $(this).data('ip');
                  $('#ip_edit_ddos').val(ip);
                });

                //guardar la ip
                $(document).on('click', '#btnDesbloqueoDdos', function(e){
                  e.preventDefault();
                  $("#overlayListaWaf").css("display", "block");
                  //alert('entro');
                  $.ajax({
                    type: 'POST',
                    url: 'controller/g_white_ip_ddos.php',
                    data: $('.FormUbb').serialize(),
                    success: function(data) {
                      if (data == 'bien') {
                        not1();
                        $("#overlayListaWaf").css("display", "none");
                        setTimeout("location.href = 'home.php'",3000);
                      }else{
                        //alert(data);
                        not2();
                        $("#overlayListaWaf").css("display", "none");
                      }
                    }
                  });      
                });

                // bloquear ip
                $(document).on('click', '#btnBloquearIpDdos', function(e){
                    e.preventDefault();
                    var id_ip = $(this).data('ip');
                    //   alert(ip);
                    $("#overlayListaWaf").css("display", "block");
                    var parametro = {
                        "id_ip" : id_ip
                    }
                    $.ajax({
                        type: 'POST',
                        url: 'controller/e_white_ip_ddos.php',
                        data: parametro,
                        success: function(data) {
                        if (data == 'bien') {
                            not4();
                            $("#overlayListaWaf").css("display", "none");
                            setTimeout("location.href = 'home.php'",2000);
                        } else{
                            not5();
                            $("#overlayListaWaf").css("display", "none");
                        }
                        }
                    }); 
                });

                //guardar nueva ip
                $(document).on('click', '#btnNewDdos', function(e){
                  e.preventDefault();
                    //$("#overlayListaWaf").css("display", "block");
                    //alert('entro');
                    $.ajax({
                        type: 'POST',
                        url: 'controller/g_new_white_ip_ddos.php',
                        data: $('.FormUbbNew').serialize(),
                        success: function(data) {
                            if (data == 'bien') {
                                not1();
                                // $("#overlayListaWaf").css("display", "none");
                                setTimeout("location.href = 'home.php'",3000);
                            } else {
                                //alert(data);
                                notif({
                                    msg: data,
                                    type: "error",
                                    position: "center"
                                });
                                // $("#overlayListaWaf").css("display", "none");
                            }
                        }
                    });      
                });
            });
        </script>

        <!-- grafica de ataque ddos-->
        <script>
            $(document).on('click','#graficoDdos',function() {
                Highcharts.chart('containerPastelDdos', {
                      chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                      },
                      title: {
                        text: 'Grafico de ddos por pais'
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
                            format: '<b>{point.name}</b>: {point.y:.0f}',
                            style: {
                              color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                          }
                        }
                      },
                      series: [{
                        name: '',
                        colorByPoint: true,
                        data: [
                        <?php
                            $consult = mysqli_query($link,"SELECT * FROM bloqueo_ddos_pais
                                                    INNER JOIN paises
                                                    ON bloqueo_ddos_pais.id_pais = paises.id_pais");
                            
                            while($rows = mysqli_fetch_array($consult))
                            {
                                $nombre_pais = $rows['nombre'];
                                $total_bloqueos = $rows['total_bloqueo_ddos_pais'];
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

        <!-- rango de fechas Grafica seis-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaSeis').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');
                        $("#overlayGraficaPrincipal").css("display", "block");
                        $('#containerPastelDdos').html('');  
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2
                        }
                        //alert(parametro);
                        $.ajax({
                            url:  'controller/new_grafica_ddos.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){  
                            $("#overlayGraficaPrincipal").css("display", "none");
                            $('#containerPastelDdos').html('');    
                            $('#containerPastelDdos').html(data); // mostrar la data
                        })
                        .fail(function(){
                            $("#overlayGraficaPrincipal").css("display", "none");
                            $('#containerPastelDdos').html('');
                        });
                    });
            })
        </script>

        <!-- para eliminar toda la informacion -->
        <script type="text/javascript">
            $(document).ready(function(){
                $(document).on('click', '#btn_eliminar_all', function(e){
                    e.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'controller/e_all_data.php',
                        success: function(data) {
                            if (data == 'bien') {
                                //alert(data);
                                not4();
                                setTimeout("location.href = 'home.php'",2000);
                            } else {
                                //alert(data);
                                not5();
                            }
                        }
                    }); 
                });
            });
        </script>

        <!-- para guardar los host de scan -->
        <script type="text/javascript">
            $(document).ready(function(){

                var table8 = $('#example8').DataTable({
                     "processing": true,
                     "serverSide": true,
                     "ajax": "ajax_table/ajax_table_history.php",
                     "createdRow": function ( row, data, index ) {
                        //
                        if (data[3]) {
                            $('td', row).eq(3).append('<i class="fa fa-clock-o icono-relog"></i>');
                        }
                    }
                });
                
                var table6 = $('#example6').DataTable({
                     "order": [[1, 'desc']],
                     "processing": true,
                     "serverSide": true,
                     "ajax": "ajax_table/ajax_table_vulnerability.php",
                     "createdRow": function ( row, data, index ) {
                        //
                        if (data[4]) {
                            $('td', row).eq(4).append('<i class="fa fa-clock-o icono-relog"></i>');
                        }

                        if (data[5]) {
                            $('td', row).eq(5).css('text-align', 'center');
                        }
                    }
                });

                // crear nuevo escaneo
                $(document).on('click', '#btnNewEscan', function(e){
                    e.preventDefault();
                    $("#overlayListaWafNuevo").css("display", "block");
                    $('#ModalNewEscan').modal('hide');
                    $.ajax({
                        type: 'POST',
                        url: 'controller/g_new_host_scan.php',
                        data: $('.FormUbbNewScan').serialize(),
                        success: function(data) {
                            //alert(data);
                            // notif({
                            //     msg: data,
                            //     type: "success",
                            //     position: "center"
                            // });
                            setTimeout(function() {
                                $("#overlayListaWafNuevo").css("display", "none");
                                $('#ip_host_scan').val('');
                                table6.ajax.reload(null, false);
                                table8.ajax.reload(null, false);
                            }, 5000);
                        }
                    });
                });

                // eliminar datos de historico y escaneos
                $(document).on('click', '#btn_eliminar_all_scan', function(e){
                    e.preventDefault();
                    $("#overlayListaWafNuevoDelete").css("display", "block");
                    $('#ModalDeleteScan').modal('hide');
                    $.ajax({
                        type: 'POST',
                        url: 'controller/e_all_scan.php',
                        success: function(data) {
                            //alert(data);
                            notif({
                                msg: data,
                                type: "success",
                                position: "center"
                            });
                            $("#overlayListaWafNuevoDelete").css("display", "none");
                            table6.ajax.reload(null, false);
                            table8.ajax.reload(null, false);
                        }
                    }); 
                });
            });
        </script>

        <!-- agregar mas de una ip o host -->
        <script type="text/javascript">
            // agregar registro
            $("#addRow").click(function () {
                var html = '';
                html += '<div class="col-md-12" id="inputFormRow">';
                html += '<div class="form-group col-md-6">';
                html += '<div class="input-group">';
                html += '<div class="input-group-addon">';
                html += '<i class="fa fa-server"></i>';
                html += '</div>';
                html += '<input type="text" class="form-control" name="ip_host_scan[]" placeholder="">';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-md-6">';
                html += '<button id="removeRow" type="button" class="btn btn-danger">Borrar</button>';
                html += '</div>';
                html += '</div>';

                $('#newRow').append(html);
                $(".tab-content").css('height', 'auto');
            });
            // borrar registro
            $(document).on('click', '#removeRow', function () {
                $(this).closest('#inputFormRow').remove();
                $(".tab-content").css('height', 'auto');
            });
        </script>

        <!-- tabla ddos -->
        <script type="text/javascript">
            $(document).ready(function(){
                $('#example7').DataTable({
                     "order": [[0, 'asc']],
                     "processing": true,
                     "serverSide": true,
                     "ajax": "ajax_table/ajax_table_ip_threat.php",
                     "createdRow": function ( row, data, index ) {
                        //
                        // if (data[5]) {
                        //     $('td', row).eq(5).append('<i class="fa fa-clock-o icono-relog"></i>');
                        // }
                    }
                });
            });
        </script>

        <!-- grafica para ip threat -->
        <script>
            $(document).ready(function(){
                $(document).on('click','#graficoThreat',function() {
                    $(".chart_ip_threat").attr("id","chart_ip_threat");
                    
                    //grafica para hoy
                    var chart_ip_threat = c3.generate({
                        data: {
                            columns: [
                            <?php
                                //
                                echo '["Ataques Bot", '.$totalBots.' ],';
                                echo '["Ataques Web", '.$totalApache.' ],';
                                
                                ?>
                            ],
                            type : 'donut',
                            onclick: function (d, i) { 
                                //alert(d.id);
                                //setTimeout("location.href = 'tipo_bloqueo.php?tipo!="+d.id+"'",100);
                                // setTimeout("location.href = 'ip_bloqueadas.php?tipo!="+d.id+"&rango!=HOY'",100);
                            },
                        },
                        donut: {
                            title: "Tipos de ataques",
                            label: {
                                format: function (value, ratio, id) {
                                    return d3.format(',')(value);
                                }
                            },
                        },
                        tooltip: {
                            grouped: false,
                            format: {
                                value: function (value, ratio, id) {
                                    return d3.format(',')(value);
                                }
                            }
                        },
                        legend: {
                            show: true
                        },
                    });
                    $("#chart_ip_threat").html(chart_ip_threat.element);
                });
            });
        </script>

        <!-- reporte ataques por dominios -->
        <script>
            $(document).ready(function(){
                function toggleReporteDominio() {
                    var tipo = $('select[name="tipo_reporte"]').val();
                    if (tipo === 'R2' || tipo === 'R3' || tipo === 'R4') {
                        $('#grupoDominioReporte').css("display", "block");
                    } else {
                        $('#dominio_reporte').val('').trigger('change.select2');
                        $('#grupoDominioReporte').css("display", "none");
                    }
                }

                $(document).on('change', 'select[name="tipo_reporte"]', function(){
                    toggleReporteDominio();
                });

                $('#modalReporte').on('shown.bs.modal', function(){
                    toggleReporteDominio();
                });

                $('#modalReporte').on('hidden.bs.modal', function(){
                    $('#dominio_reporte').val('').trigger('change.select2');
                    $('#grupoDominioReporte').css("display", "none");
                });

                toggleReporteDominio();
            });
        </script>

        <!-- rango de fechas Grafica tipo ataque por dominios-->
        <script>
            $(function () {
                //Date range picker
                $('#rangoGraficaTipoAtaqueDominio').daterangepicker({},
                    function(start, end, label) {
                        //alert("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                        var fecha1 = start.format('YYYY-MM-DD h:mm:ss');
                        var fecha2 = end.format('YYYY-MM-DD h:mm:ss');
                        //obtener el nombre del dominio del dropdown1
                        var server_name = $("#dropdown1").val();
                        var server_label = server_name === '' ? 'Todos los host' : $('#dropdown1 option:selected').text();

                        // $("#overlayGraficaPrincipal").css("display", "block");
                        $('#overlayListaWafAtaquesDominios').css("display", "block");
                        $('#containerPastelTipoAtaqueDomino').html('');
                        var parametro = {
                            "fecha1" : fecha1,
                            "fecha2" : fecha2,
                            "server_name" : server_name,
                            "server_label" : server_label
                        }
                        // alert(parametro);
                        $.ajax({
                            url:  'controller/new_grafica_tres.php', 
                            type: 'POST',
                            data: parametro,
                            dataType: 'html'
                        })
                        .done(function(data){
                            $("#div_tipo_ataque_3").css("display", "none");
                            $("#div_tipo_ataque_4").css("display", "none");
                            $('#containerPastelTipoAtaqueDomino').html('');
                            $("#div_tipo_ataque_3").css("display", "block");
                            $("#div_tipo_ataque_4").css("display", "block");
                            $('#containerPastelTipoAtaqueDomino').html(data); // mostrar la data
                            $('#overlayListaWafAtaquesDominios').css("display", "none");
                        })
                        .fail(function(){
                            $("#div_tipo_ataque_3").css("display", "none");
                            $("#div_tipo_ataque_4").css("display", "none");
                            $('#containerPastelTipoAtaqueDomino').html('');
                            $('#overlayListaWafAtaquesDominios').css("display", "none");
                        });
                    });
            })
        </script>

    </body>
</html>
