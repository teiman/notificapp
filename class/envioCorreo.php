<?php


include("class/class.phpmailer.php");

class Contacto extends PHPMailer {
    
    
    function enviar($to,$asunto="Email de la plataforma",$body=""){                
        $this->From       = "noreply@tiendas.com";
        $this->From          = "noreply@tiendas.com";
        $this->FromName   = "";
        $this->Subject    = $asunto;
        $this->AltBody    = "To view the message, please use an HTML compatible email viewer!";

        $this->MsgHTML($body);
        $this->AddAddress($to, "");

        $this->IsHTML(true);

        return $this->Send();                        
    }
    
    
}