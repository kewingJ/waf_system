<?php
if(!empty($_POST['emailR'])) 
{
    require("../includes/config.php");
	require("../includes/security.php");
    include_once '../phpmailer/PHPMailerAutoload.php';
	/*guardo los datos del usuario y los limpio de cualquier caracter*/
	$email = mysqli_real_escape_string($link,$_POST['emailR']);
    /*verificamos en la base de datos si existe el usuario*/
    $consulta = mysqli_query($link, "SELECT * FROM usuario WHERE email_u='{$email}'");
    $row = mysqli_fetch_array($consulta);
    if(is_numeric($row['id_usuario']) AND $row['id_usuario']>0) 
    {
        $id_usuario = $row['id_usuario'];
        $nombre = $row['nombre_u'].' '.$row['apellido_u'];

        //Carácteres para la contraseña
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $new_password = $id_usuario;
        //Reconstruimos la contraseña segun la longitud que se quiera
        for($i = 0; $i < 10; $i++) {
            //obtenemos un caracter aleatorio escogido de la cadena de caracteres
            $new_password .= substr($str,rand(0,62),1);
        }
        //encriptar y almacenar la nueva password
        $opciones = [
            'cost' => 12
        ];
        $password_encrip = password_hash($new_password, PASSWORD_BCRYPT, $opciones);
        $queryUpdate = mysqli_query($link,"UPDATE usuario SET password_encrip = '$password_encrip' 
                                                            WHERE id_usuario = '$id_usuario'") or die(mysqli_error($link));
        //MAQUETAR CORREO
        $body = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

                        <html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
                        <head>
                        <!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
                        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
                        <meta content="width=device-width" name="viewport"/>
                        <!--[if !mso]><!-->
                        <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
                        <!--<![endif]-->
                        <title></title>
                        <!--[if !mso]><!-->
                        <!--<![endif]-->
                        <style type="text/css">
                                body {
                                    margin: 0;
                                    padding: 0;
                                }

                                table,
                                td,
                                tr {
                                    vertical-align: top;
                                    border-collapse: collapse;
                                }

                                * {
                                    line-height: inherit;
                                }

                                a[x-apple-data-detectors=true] {
                                    color: inherit !important;
                                    text-decoration: none !important;
                                }
                            </style>
                        <style id="media-query" type="text/css">
                                @media (max-width: 620px) {

                                    .block-grid,
                                    .col {
                                        min-width: 320px !important;
                                        max-width: 100% !important;
                                        display: block !important;
                                    }

                                    .block-grid {
                                        width: 100% !important;
                                    }

                                    .col {
                                        width: 100% !important;
                                    }

                                    .col>div {
                                        margin: 0 auto;
                                    }

                                    img.fullwidth,
                                    img.fullwidthOnMobile {
                                        max-width: 100% !important;
                                    }

                                    .no-stack .col {
                                        min-width: 0 !important;
                                        display: table-cell !important;
                                    }

                                    .no-stack.two-up .col {
                                        width: 50% !important;
                                    }

                                    .no-stack .col.num4 {
                                        width: 33% !important;
                                    }

                                    .no-stack .col.num8 {
                                        width: 66% !important;
                                    }

                                    .no-stack .col.num4 {
                                        width: 33% !important;
                                    }

                                    .no-stack .col.num3 {
                                        width: 25% !important;
                                    }

                                    .no-stack .col.num6 {
                                        width: 50% !important;
                                    }

                                    .no-stack .col.num9 {
                                        width: 75% !important;
                                    }

                                    .video-block {
                                        max-width: none !important;
                                    }

                                    .mobile_hide {
                                        min-height: 0px;
                                        max-height: 0px;
                                        max-width: 0px;
                                        display: none;
                                        overflow: hidden;
                                        font-size: 0px;
                                    }

                                    .desktop_hide {
                                        display: block !important;
                                        max-height: none !important;
                                    }
                                }
                            </style>
                        </head>
                        <body class="clean-body" style="margin: 0; padding: 0; -webkit-text-size-adjust: 100%; background-color: #283C4B;">
                        <!--[if IE]><div class="ie-browser"><![endif]-->
                        <table bgcolor="#283C4B" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="table-layout: fixed; vertical-align: top; min-width: 320px; Margin: 0 auto; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #283C4B; width: 100%;" valign="top" width="100%">
                        <tbody>
                        <tr style="vertical-align: top;" valign="top">
                        <td style="word-break: break-word; vertical-align: top;" valign="top">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color:#283C4B"><![endif]-->
                        <div style="background-color:#283C4B;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #3AAEE0;">
                        <div style="border-collapse: collapse;display: table;width: 100%;background-color:#3AAEE0;">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#283C4B;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:#3AAEE0"><![endif]-->
                        <!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:#3AAEE0;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:0px;"><![endif]-->
                        <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
                        <div style="width:100% !important;">
                        <!--[if (!mso)&(!IE)]><!-->
                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
                        <!--<![endif]-->
                        <div align="center" class="img-container center autowidth fullwidth" style="padding-right: 0px;padding-left: 0px;">
                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr style="line-height:0px"><td style="padding-right: 0px;padding-left: 0px;" align="center"><![endif]--><img align="center" alt="Image" border="0" class="center autowidth fullwidth" src="https://agamotto.viseeon.com/build/media/icons/password_icon_ok.jpg" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 600px; display: block;" title="Image" width="600"/>
                        <!--[if mso]></td></tr></table><![endif]-->
                        </div>
                        <!--[if (!mso)&(!IE)]><!-->
                        </div>
                        <!--<![endif]-->
                        </div>
                        </div>
                        <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                        <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                        </div>
                        </div>
                        </div>
                        <div style="background-color:#283C4B;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #FFFFFF;">
                        <div style="border-collapse: collapse;display: table;width: 100%;background-color:#FFFFFF;">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#283C4B;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:#FFFFFF"><![endif]-->
                        <!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:#FFFFFF;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:0px; padding-bottom:15px;"><![endif]-->
                        <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
                        <div style="width:100% !important;">
                        <!--[if (!mso)&(!IE)]><!-->
                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:15px; padding-right: 0px; padding-left: 0px;">
                        <!--<![endif]-->
                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 30px; padding-left: 30px; padding-top: 10px; padding-bottom: 10px; font-family: Arial, sans-serif"><![endif]-->
                        <div style="color:#283C4B;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;line-height:1.5;padding-top:10px;padding-right:30px;padding-bottom:10px;padding-left:30px;">
                        <div style="font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif; line-height: 1.5; font-size: 12px; color: #283C4B; mso-line-height-alt: 18px;">
                        <p style="font-size: 16px; line-height: 1.5; text-align: center; mso-line-height-alt: 24px; margin: 0;"><span style="font-size: 16px;"><strong>Nueva Contraseña</strong></span></p>
                        <p style="line-height: 1.5; text-align: center; font-size: 16px; mso-line-height-alt: 24px; margin: 0;"><span style="font-size: 16px;"><strong>Contraseña : '.$new_password.'</strong></span></p>
                        </div>
                        </div>
                        <!--[if mso]></td></tr></table><![endif]-->
                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 30px; padding-left: 30px; padding-top: 10px; padding-bottom: 0px; font-family: Arial, sans-serif"><![endif]-->
                        <div style="color:#283C4B;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;line-height:1.5;padding-top:10px;padding-right:30px;padding-bottom:0px;padding-left:30px;">
                        <div style="font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif; font-size: 12px; line-height: 1.5; color: #283C4B; mso-line-height-alt: 18px;">
                        <p style="font-size: 12px; line-height: 1.5; text-align: center; mso-line-height-alt: 18px; margin: 0;"> </p>
                        </div>
                        </div>
                        <!--[if mso]></td></tr></table><![endif]-->
                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 20px; padding-left: 20px; padding-top: 20px; padding-bottom: 30px; font-family: Arial, sans-serif"><![endif]-->
                        <div style="color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;line-height:1.2;padding-top:20px;padding-right:20px;padding-bottom:30px;padding-left:20px;">
                        <div style="font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif; font-size: 12px; line-height: 1.2; color: #555555; mso-line-height-alt: 14px;">
                        <p style="font-size: 14px; line-height: 1.2; text-align: center; mso-line-height-alt: 17px; margin: 0;"><span style="font-size: 14px;"><a href="#" rel="noopener" style="color: #3AAEE0;" title="example">WAF</a></span></p>
                        </div>
                        </div>
                        <!--[if mso]></td></tr></table><![endif]-->
                        <!--[if (!mso)&(!IE)]><!-->
                        </div>
                        <!--<![endif]-->
                        </div>
                        </div>
                        <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                        <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                        </div>
                        </div>
                        </div>
                        <div style="background-color:transparent;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;">
                        <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                        <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px"><tr class="layout-full-width" style="background-color:transparent"><![endif]-->
                        <!--[if (mso)|(IE)]><td align="center" width="600" style="background-color:transparent;width:600px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
                        <div class="col num12" style="min-width: 320px; max-width: 600px; display: table-cell; vertical-align: top; width: 600px;">
                        <div style="width:100% !important;">
                        <!--[if (!mso)&(!IE)]><!-->
                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                        <!--<![endif]-->
                        <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
                        <tbody>
                        <tr style="vertical-align: top;" valign="top">
                        <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 10px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px;" valign="top">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; width: 100%;" valign="top" width="100%">
                        <tbody>
                        <tr style="vertical-align: top;" valign="top">
                        <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
                        </tr>
                        </tbody>
                        </table>
                        </td>
                        </tr>
                        </tbody>
                        </table>
                        <!--[if (!mso)&(!IE)]><!-->
                        </div>
                        <!--<![endif]-->
                        </div>
                        </div>
                        <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                        <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                        </div>
                        </div>
                        </div>
                        <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                        </td>
                        </tr>
                        </tbody>
                        </table>
                        <!--[if (IE)]></div><![endif]-->
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

        //enviar nueva password
        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPDebug   = 2;
            //aqui esta la ip del dominio
            $mail->DKIM_domain = $dominio;
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
            $mail->Subject = 'Cambio de Contraseña';
             
            //send the message, check for errors
            if (!$mail->send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                //echo "Message sent!";
                header("Location: ../index.php");
            }

        } catch(phpmailerException $ex) {
            $msg = "<div class='alert alert-warning'>".$ex->errorMessage()."</div>";
        }


    } else {
        header("Location: ../index.php");
    }
}else {
    header("Location: ../index.php");
}
?>