<?php
    require("../includes/config.php");
	require("../includes/security.php");
    include_once '../phpmailer/PHPMailerAutoload.php';
    //optener los sitios
    $querySitio = mysqli_query($link, "SELECT * FROM sitio WHERE activo_sitio = 1");
    while($rowSitio = mysqli_fetch_array($querySitio))
    {
        //calcular dias de expiracion
        $fecha_inicio   = new DateTime();
        $fecha_fin      = new DateTime($rowSitio['fecha_vencimiento']);
        $diferencia     = date_diff($fecha_inicio, $fecha_fin);
        $rango_dias     = $diferencia->days;
        if($rango_dias <= 5)
        {
            $queryUsuario = mysqli_query($link, "SELECT * FROM usuario WHERE tipo_usuario = 1 AND activo_u = 1");
            $rowUsuario = mysqli_fetch_array($queryUsuario);
            $nombre     = $rowUsuario['nombre_u'].' '.$rowUsuario['apellido_u'];
            $email      =  $rowUsuario['email_u'];
            $servidor   = $_SERVER['HTTP_HOST'];

            //MAQUETAR CORREO
            $body = '
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
            <!--[if gte mso 9]>
            <xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
            </xml>
            <![endif]-->
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="x-apple-disable-message-reformatting">
            <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
            <title></title>
            
                <style type="text/css">
                @media only screen and (min-width: 520px) {
            .u-row {
                width: 500px !important;
            }
            .u-row .u-col {
                vertical-align: top;
            }

            .u-row .u-col-100 {
                width: 500px !important;
            }

            }

            @media (max-width: 520px) {
            .u-row-container {
                max-width: 100% !important;
                padding-left: 0px !important;
                padding-right: 0px !important;
            }
            .u-row .u-col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important;
            }
            .u-row {
                width: 100% !important;
            }
            .u-col {
                width: 100% !important;
            }
            .u-col > div {
                margin: 0 auto;
            }
            }
            body {
            margin: 0;
            padding: 0;
            }

            table,
            tr,
            td {
            vertical-align: top;
            border-collapse: collapse;
            }

            p {
            margin: 0;
            }

            .ie-container table,
            .mso-container table {
            table-layout: fixed;
            }

            * {
            line-height: inherit;
            }

            a[x-apple-data-detectors=\'true\'] {
            color: inherit !important;
            text-decoration: none !important;
            }

            table, td { color: #000000; } #u_body a { color: #0000ee; text-decoration: underline; } @media (max-width: 480px) { #u_content_heading_2 .v-text-align { text-align: center !important; } #u_content_text_11 .v-text-align { text-align: left !important; } #u_content_button_2 .v-text-align { text-align: center !important; } }
                </style>
            
            

            <!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet" type="text/css"><link href="https://fonts.googleapis.com/css?family=Cabin:400,700&display=swap" rel="stylesheet" type="text/css"><!--<![endif]-->

            </head>

            <body class="clean-body u_body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #ffffff;color: #000000">
            <!--[if IE]><div class="ie-container"><![endif]-->
            <!--[if mso]><div class="mso-container"><![endif]-->
            <table id="u_body" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #ffffff;width:100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr style="vertical-align: top">
                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #ffffff;"><![endif]-->
                

            <div class="u-row-container" style="padding: 0px;background-image: url(\'images/image-2.png\');background-repeat: no-repeat;background-position: center center;background-color: transparent">
            <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-image: url(\'images/image-2.png\');background-repeat: no-repeat;background-position: center center;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:500px;"><tr style="background-color: transparent;"><![endif]-->
                
            <!--[if (mso)|(IE)]><td align="center" width="500" style="width: 500px;padding: 50px 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
            <div class="u-col u-col-100" style="max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;">
            <div style="height: 100%;width: 100% !important;">
            <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 50px 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->
            
            <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:\'Montserrat\',sans-serif;" align="left">
                    
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="v-text-align" style="padding-right: 0px;padding-left: 0px;" align="center">
                
                <img align="center" border="0" src="images/image-1.png" alt="image" title="image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 68%;max-width: 326.4px;" width="326.4"/>
                
                </td>
            </tr>
            </table>

                </td>
                </tr>
            </tbody>
            </table>

            <table style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 10px 20px;font-family:\'Montserrat\',sans-serif;" align="left">
                    
            <h1 class="v-text-align" style="margin: 0px; color: #ffffff; line-height: 140%; text-align: center; word-wrap: break-word; font-family: \'Cabin\',sans-serif; font-size: 34px; font-weight: 400;">Recordatorio</h1>

                </td>
                </tr>
            </tbody>
            </table>

            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
            </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
            </div>
            </div>



            <div class="u-row-container" style="padding: 0px;background-color: transparent">
            <div class="u-row" style="Margin: 0 auto;min-width: 320px;max-width: 500px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:500px;"><tr style="background-color: transparent;"><![endif]-->
                
            <!--[if (mso)|(IE)]><td align="center" width="500" style="width: 500px;padding: 40px 0px 50px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;" valign="top"><![endif]-->
            <div class="u-col u-col-100" style="max-width: 320px;min-width: 500px;display: table-cell;vertical-align: top;">
            <div style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
            <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 40px 0px 50px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;"><!--<![endif]-->
            
            <table id="u_content_heading_2" style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 10px 20px;font-family:\'Montserrat\',sans-serif;" align="left">
                    
            <h1 class="v-text-align" style="margin: 0px; color: #000000; line-height: 140%; text-align: left; word-wrap: break-word; font-family: \'Cabin\',sans-serif; font-size: 31px; font-weight: 400;">Tienes un dominio por vencerse</h1>

                </td>
                </tr>
            </tbody>
            </table>

            <table id="u_content_text_11" style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:30px 20px 20px;font-family:\'Montserrat\',sans-serif;" align="left">
                    
            <div class="v-text-align" style="font-size: 16px; color: #fd3e42; line-height: 160%; text-align: center; word-wrap: break-word;">
                <p style="line-height: 160%;">Recuerda renovarlo o darle de baja.</p>
            </div>

                </td>
                </tr>
            </tbody>
            </table>

            <table id="u_content_button_2" style="font-family:\'Montserrat\',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 10px 20px;font-family:\'Montserrat\',sans-serif;" align="left">
                    
            <!--[if mso]><style>.v-button {background: transparent !important;}</style><![endif]-->
            <div class="v-text-align" align="center">
            <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://www.unlayer.com" style="height:37px; v-text-anchor:middle; width:176px;" arcsize="54%"  stroke="f" fillcolor="#3598db"><w:anchorlock/><center style="color:#ffffff;font-family:\'Montserrat\',sans-serif;"><![endif]-->  
                <a href="'.$servidor.'" target="_blank" class="v-button" style="box-sizing: border-box;display: inline-block;font-family:\'Montserrat\',sans-serif;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #ffffff; background-color: #3598db; border-radius: 20px;-webkit-border-radius: 20px; -moz-border-radius: 20px; width:auto; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;">
                <span style="display:block;padding:10px 50px;line-height:120%;">Visitar sitio</span>
                </a>
            <!--[if mso]></center></v:roundrect><![endif]-->
            </div>

                </td>
                </tr>
            </tbody>
            </table>

            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
            </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
            </div>
            </div>


                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                </td>
            </tr>
            </tbody>
            </table>
            <!--[if mso]></div><![endif]-->
            <!--[if IE]></div><![endif]-->
            </body>

            </html>
            ';

            //credenciales del servicio de correo        
            $query = mysqli_query($link, "SELECT * FROM credentials_email");
            $row = mysqli_fetch_array($query);

            $dominio = $row['dominio_mail'];
            $host = $row['host_mail'];
            $email_host = $row['user_email'];
            $pass = $row['password_mail'];
            $smtp = $row['smtp_secure'];
            $port = $row['port'];

            //enviar correo
            try 
            {
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->SMTPDebug   = 2;
                //aqui esta la ip del dominio
                // $mail->DKIM_domain = $dominio;
                $mail->CharSet = 'UTF-8';
                $mail->Debugoutput = 'html';

                //El host del servidor de correo
                $mail->Host        = $host;
                //El puerto SMTP 465 or 587
                $mail->Port        = $port;
                $mail->AuthType    = 'LOGIN';

                //
                $mail->SMTPAuth    = true;
                //El email con el que se envan los correos
                $mail->Username    = $email_host;
                //Password del correo
                $mail->Password    = $pass;
                //el tipo de seguridad
                $mail->SMTPSecure  = $smtp;
                //el email con el que se envian los correos
                $mail->setFrom($email_host, 'WAF (Notificacion)');
                //
                $mail->addAddress($email, $nombre);
                $mail->isHTML(true);
                $mail->Body = $body;
                $mail->AltBody = '';
                $mail->Subject = 'Tienes un dominio por vencerse';
             
                //send the message, check for errors
                if (!$mail->send()) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                } 
            } catch(phpmailerException $ex) {
                $msg = "<div class='alert alert-warning'>".$ex->errorMessage()."</div>";
            }
        }
    }
?>