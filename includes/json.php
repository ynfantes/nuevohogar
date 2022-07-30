<?php
include './constants.php';

$ini = parse_ini_file('./emails.ini');

if (isset($_GET['accion'])) {

    switch ($_GET['accion']) {
    
        // <editor-fold defaultstate="collapsed" desc="mensaje pagina contacto">
        case "email_contacto":
            $mail = new mailto();
            $mensaje = sprintf($ini['CUERPO_CONTACTO_NH'], $_POST['nombre'],$_POST['email'], $_POST['asunto'], $_POST['mensaje']);
            $r = $mail->enviar_email($ini['ASUNTO_CONTACTO'], $mensaje, "", USER_MAIL);

            if ($r == "") {
                $mail = new mailto();
                $mensaje = $ini['CUERPO_CONTACTO'];
                $r = $mail->enviar_email($ini['ASUNTO_CONTACTO'], $mensaje, "", $_POST['email']);
                echo '<h4>Menseje enviado con éxito!</h4>En breve estaremos 
                    contactando con usted.<br>Gracias por su interés.';
            } else {
                echo '<h4>Error al enviar el mensaje.</h4>
                    No se pudo enviar el mensaje. Por favor intente nuevamente.';
            }
            break; // </editor-fold>
    
        // <editor-fold defaultstate="collapsed" desc="contacto_propiedad">
        case "contacto_propiedad":
            $mail = new mailto();
            $mensaje = sprintf($ini['CUERPO_CONTACTO_PROPIEDAD'],
                    $_POST['nombre'],
                    $_POST['titulo'], 
                    $_POST['mensaje'], 
                    $_POST['email'], 
                    $_POST['telefono'],                     $_POST['url']);
            
            $r = $mail->enviar_email('Información ' . $_POST['titulo'], $mensaje, "", "info@administradoranuevohogar.com.ve");

            if ($r == "") {
                $mail = new mailto();
                $mensaje = $ini['CUERPO_CONTACTO'];

                $r = $mail->enviar_email($ini['ASUNTO_CONTACTO'], $mensaje, "", $_POST['email']);

                echo '<h4>Menseje enviado con éxito!</h4>En breve estaremos 
                    contactando con usted.<br>Gracias por su interés.';
            } else {
                echo '<h4>Error al enviar el mensaje.</h4>
                    No se pudo enviar el mensaje. Por favor intente nuevamente.';
            }
            break; // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc="compartir propiedad">
        case "compartir_propiedad":
            $mail = new mailto();
            if (isset($_POST['de']) && isset($_POST['email']) && isset($_POST['de']) && isset($_POST['url']) && isset($_POST['titulo'])) {
                $mensaje = $_POST['mensaje'] . "<br>" . $_POST['titulo'] . "<br>" . $_POST['url'];
                $r = $mail->enviar_email($_POST['de'] . " ha compartido contigo una propiedad de www.administradoranuevohogar.com.ve", $mensaje, "", $_POST['email']);
                if ($r == "") {
                    echo '<h4>Menseje enviado con éxito!</h4>Gracias por compartir la información de administradoranuevohogar.com.ve.';
                } else {
                    echo '<h4>Error al enviar el mensaje.</h4>No se pudo enviar el mensaje. Por favor intente nuevamente.';
                }
            }
            break; // </editor-fold>
            }
}