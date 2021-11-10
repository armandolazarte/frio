<?php
require_once "common/libs/PHPMailer/class.phpmailer.php";


class EmailHelper extends View {
        public function envia_email($array_datos, $destinos_array) { 
                $gui = file_get_contents("static/mail.html");
                $fecha_desglosada = $this->descomponer_fecha();
                $origen = "qManagement@tonka.com.ar";
                $nombre = "TonKa Informes: qManagement";

                $render = $this->render($fecha_desglosada, $gui);
                $render = $this->render($array_datos, $render);
                
                $mail = new PHPMailer();
                $mail->From = $origen;
                $mail->FromName = $nombre;
                foreach ($destinos_array as $clave=>$valor) $mail->AddAddress($valor);
                $mail->IsHTML(true);
                $mail->Subject = utf8_decode("Asignación de riesgo en qManagement");
                $mail->Body = utf8_decode($render);
                $mail->IsSMTP();
                $mail->Host = '172.18.1.29';
                $mail->Send();
        }
}
?>