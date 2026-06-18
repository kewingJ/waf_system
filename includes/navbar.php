<?php
/**
 * includes/navbar.php
 * -------------------------------------------------
 * Menú de navegación compartido para todas las vistas.
 *
 * Variables esperadas antes del include:
 *   $link            — conexión MySQLi activa (de includes/config.php)
 *   $licenciaStatus  — bool: si se debe mostrar el ítem de Licencia
 *
 * Si $licenciaStatus no está definida en la vista que incluye este archivo
 * (vistas distintas a home.php), la declaramos en false para que no rompa.
 */
if (!isset($licenciaStatus)) {
    $licenciaStatus = false;
}

// Cargar helpers y banner SOLAMENTE si la carpeta client existe
$clientDir = dirname(__DIR__) . '/client';
if (is_dir($clientDir) && file_exists($clientDir . '/license_helpers.php')) {
    require_once $clientDir . '/license_helpers.php';
    if (function_exists('license_client_render_modal')) {
        // Al imprimir esto aquí (en el <body>), evitamos el error "headers already sent"
        license_client_render_modal();
        $licenciaStatus = true;
    }
}

/* Token de seguridad para acciones de Power (reboot / shutdown) */
if (empty($_SESSION['power_action_token'])) {
    $_SESSION['power_action_token'] = bin2hex(random_bytes(32));
}

/* Notificaciones activas */
$consultNoti = mysqli_query($link, "SELECT * FROM notificacion_regla
                                    WHERE activa_notificacion = 1");
$totalNoti   = mysqli_num_rows($consultNoti);

/* Página activa (para marcar el ítem del menú) */
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Estilos unificados del menú para todas las vistas -->
<style type="text/css">
    .main-header .header-logo {
        background: url(img/logo_icono.png) left 50% no-repeat;
        margin-right: 10px;
    }
    .main-header .container {
        width: 100%;
        max-width: none;
        padding-left: 18px;
        padding-right: 18px;
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

    /* Overlay Power Action */
    #powerActionOverlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 36, 0.95);
        z-index: 99999;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-family: 'Open Sans', 'Inter', sans-serif;
        text-align: center;
        backdrop-filter: blur(5px);
    }
    #powerActionOverlay .spinner {
        width: 70px;
        height: 70px;
        border: 4px solid rgba(255, 255, 255, 0.1);
        border-top: 4px solid #00a8ff;
        border-right: 4px solid #00a8ff;
        border-radius: 50%;
        animation: spinPower 1s cubic-bezier(0.55, 0.15, 0.45, 0.85) infinite;
        margin-bottom: 25px;
    }
    #powerActionOverlay h2 {
        font-size: 32px;
        font-weight: 300;
        margin: 0;
        letter-spacing: 1.5px;
    }
    #powerActionOverlay p {
        font-size: 16px;
        color: #8c97a8;
        margin-top: 12px;
    }
    @keyframes spinPower {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<!-- ============================================================ -->
<!-- BARRA DE NAVEGACIÓN PRINCIPAL                               -->
<!-- ============================================================ -->
<div class="main-header bg-header wow fadeInDown animated animated" style="visibility: visible;">
    <div class="container" style="display:flex; justify-content:center; align-items:center;">
        <a href="home.php" class="header-logo" style="width: 40px;"></a>

        <ul class="header-nav collapse">

            <!-- Inicio -->
            <li class="<?= ($currentPage === 'home.php') ? 'active' : '' ?>">
                <a href="home.php" title="">
                    <i class="fa fa-home"></i>
                    Inicio
                </a>
            </li>

            <!-- Usuarios -->
            <li class="<?= ($currentPage === 'user.php') ? 'active' : '' ?>">
                <a href="user.php" title="">
                    <img src="img/user.png" style="width: 20px;"/>
                    Usuarios
                </a>
            </li>

            <!-- Reglas -->
            <li class="<?= ($currentPage === 'rules.php') ? 'active' : '' ?>">
                <a href="rules.php" title="">
                    <img src="img/tipo de ataqu.png" style="width: 20px;"/>
                    Reglas(Tipos de ataque)
                </a>
            </li>

            <!-- Configuración de correo -->
            <li class="<?= ($currentPage === 'credencialesMail.php') ? 'active' : '' ?>">
                <a href="credencialesMail.php" title="">
                    <img src="img/configuracion de correo.png" style="width: 20px;"/>
                    Configuración de correo
                </a>
            </li>

            <!-- Configuraciones (dropdown) -->
            <?php
            $configPages = [
                'sitiossl.php', 'loadBalancer.php', 'loadBalancer_tcpudp.php',
                'sitio.php', 'whiteList.php', 'whitelistrule.php',
                'configuracionextra.php', 'marketplace_sitio.php'
            ];
            $isConfigActive = in_array($currentPage, $configPages);
            ?>
            <li class="<?= $isConfigActive ? 'active' : '' ?>">
                <a href="#" title="Configuraciones">
                    <img src="img/config.png" style="width: 20px;"/> Configuraciones
                    <i class="glyph-icon icon-angle-down"></i>
                </a>
                <ul>
                    <li class="<?= ($currentPage === 'sitiossl.php') ? 'active' : '' ?>">
                        <a href="sitiossl.php" title="">
                            <span>
                                <img src="img/ssl.png" style="width: 20px;"/> Sitio SSL
                            </span>
                        </a>
                    </li>

                    <li class="<?= ($currentPage === 'loadBalancer.php') ? 'active' : '' ?>">
                        <a href="loadBalancer.php" title="">
                            <span>
                                <img src="img/load.png" style="width: 20px;"/> Load balancer high availability
                            </span>
                        </a>
                    </li>

                    <li class="<?= ($currentPage === 'loadBalancer_tcpudp.php') ? 'active' : '' ?>">
                        <a href="loadBalancer_tcpudp.php" title="">
                            <span>
                                <img src="img/load_tcp.png" style="width: 20px;"/> Load balancer TCP/UDP
                            </span>
                        </a>
                    </li>

                    <li class="<?= ($currentPage === 'sitio.php') ? 'active' : '' ?>">
                        <a href="sitio.php" title="">
                            <span>
                                <img src="img/sitio.png" style="width: 20px;"/> Proteger Sitio
                            </span>
                        </a>
                    </li>

                    <li class="<?= ($currentPage === 'whiteList.php') ? 'active' : '' ?>">
                        <a href="whiteList.php" title="">
                            <span>
                                <img src="img/ip.png" style="width: 20px;"/> IP WhiteList
                            </span>
                        </a>
                    </li>

                    <li class="<?= ($currentPage === 'whitelistrule.php') ? 'active' : '' ?>">
                        <a href="whitelistrule.php" title="">
                            <span>
                                <img src="img/rules.png" style="width: 20px;"/> WhiteList Rules
                            </span>
                        </a>
                    </li>

                    <li class="<?= ($currentPage === 'configuracionextra.php') ? 'active' : '' ?>">
                        <a href="configuracionextra.php" title="">
                            <span>
                                <img src="img/settings.png" style="width: 20px;"/> Configuración Extras
                            </span>
                        </a>
                    </li>

                    <li class="<?= ($currentPage === 'marketplace_sitio.php') ? 'active' : '' ?>">
                        <a href="marketplace_sitio.php" title="">
                            <span>
                                <img src="img/shop.png" style="width: 20px;"/> Marketplace de Sitios
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Power (dropdown) -->
            <li class="">
                <a href="#" title="Power">
                    <img src="img/plug.png" style="width: 20px;"/> Power
                    <i class="glyph-icon icon-angle-down"></i>
                </a>
                <ul>
                    <li>
                        <a href="#0" title="" class="btnPowerAction" data-action="reboot">
                            <span>
                                <img src="img/reboot.png" style="width: 20px;"/> Reboot
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="#0" title="" class="btnPowerAction" data-action="shutdown">
                            <span>
                                <img src="img/power_off.png" style="width: 20px;"/> Power Off
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Licencia (solo si está activa) -->
            <?php if ($licenciaStatus): ?>
            <li class="">
                <a href="#modalLicense" data-toggle="modal">
                    <img src="img/lisencia.png" style="width: 20px;"/>
                    Licencia
                </a>
            </li>
            <?php endif; ?>

            <!-- Salir -->
            <li class="">
                <a href="salir.php" title="">
                    <img src="img/salir.png" style="width: 20px;"/>
                    Salir
                </a>
            </li>

            <!-- Notificaciones (campana) -->
            <li class="">
                <a href="timelinerule.php" title="Nuevas reglas" class="dropdown-toggle" style="position:relative;">
                    <i class="fa fa-bell"></i>
                    <?php if ($totalNoti > 0): ?>
                    <span class="label label-danger"
                          style="position:absolute; top:20px; right:-7px;">
                        <?= $totalNoti ?>
                    </span>
                    <?php endif; ?>
                </a>
            </li>

        </ul><!-- .header-nav -->
    </div><!-- .container -->
</div>

<!-- ============================================================ -->
<!-- JavaScript compartido: Botones de Power (Reboot / Shutdown)  -->
<!-- Se incluye aquí para que funcione en TODAS las vistas.       -->
<!-- ============================================================ -->
<div id="powerActionOverlay">
    <div class="spinner"></div>
    <h2 id="powerActionText">Procesando...</h2>
    <p>Por favor, espere. El servidor dejará de responder temporalmente.</p>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var powerActionToken = <?php echo json_encode($_SESSION['power_action_token']); ?>;

        $(document).on('click', '.btnPowerAction', function(e){
            e.preventDefault();

            var action = $(this).data('action');
            var label  = action === 'reboot' ? 'reiniciar' : 'apagar';

            if (!confirm('¿Esta seguro que desea ' + label + ' el servidor?')) {
                return;
            }

            var textAction = action === 'reboot' ? 'Reiniciando el servidor...' : 'Apagando el servidor...';
            $('#powerActionText').text(textAction);
            $('#powerActionOverlay').css('display', 'flex');

            $.ajax({
                type:     'POST',
                url:      'controller/power_action.php',
                dataType: 'json',
                data: {
                    action: action,
                    token:  powerActionToken
                },
                success: function(response) {
                    if (response && response.ok) {
                        $('#powerActionOverlay .spinner').hide();
                        $('#powerActionText').html('<i class="fa fa-check-circle" style="color: #4cd137; font-size: 60px; margin-bottom: 20px;"></i><br>' + (response.message || 'Comando enviado con éxito.'));
                        notif({
                            msg:      response.message || 'Comando enviado correctamente.',
                            type:     'success',
                            position: 'center'
                        });
                    } else {
                        $('#powerActionOverlay').fadeOut();
                        notif({
                            msg:      (response && response.message) ? response.message : 'Error! algo salio mal',
                            type:     'error',
                            position: 'center'
                        });
                    }
                },
                error: function(xhr) {
                    $('#powerActionOverlay').fadeOut();
                    var message = 'Error! algo salio mal';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    notif({
                        msg:      message,
                        type:     'error',
                        position: 'center'
                    });
                }
            });
        });
    });
</script>
