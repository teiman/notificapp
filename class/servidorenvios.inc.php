<?php


class servidorenvios {

    var $apiKey = "AIzaSyBcQ2WVkvPBIIxxJP5CNAxS9HOQO2R02D4";

    function _getRegIds($id_usuario){
        $sql = parametros("SELECT regId FROM registro_moviles WHERE id_usuario=%d GROUP BY regId",$id_usuario);
        $res = query($sql);

        return $res;
    }

    function enviar_raw($array_regId,$mensaje){
        $registrationIDs = $array_regId;

        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registrationIDs, //join(",",$registrationIDs),
            'delay_while_idle' => true,
            'data'              => array( "message" => $mensaje ),
        );

        $headers = array(
            'Authorization: key=' . $this->apiKey,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $url );

        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

    function anotar_enviado($id_usuario,$mensaje){
        $sql = parametros("INSERT notificaciones_moviles (id_usuario,nota,fecha) VALUES('%d','%s',NOW())",$id_usuario,$mensaje);
        query($sql);

    }

    function enviar($id_usuario,$mensaje){
        $array_regId = array();

        $res = $this->_getRegIds($id_usuario);

        while($row = Row($res)){
            $regId = $row["regId"];
            array_push($array_regId,$regId);
        }

        try {
            $this->enviar_raw( $array_regId, $mensaje);

            logear("envio regids(".var_export($array_regId,true).") con mensaje:(".$mensaje.")");
        } catch (Exception $e) {
            //se lo come. El envio de notificaciones es "no reliable".
        }

        $this->anotar_enviado($id_usuario,$mensaje);

        return true;
    }

}

class servidorenvios_notificapp extends servidorenvios  {

    function _getRegIds($id_usuario){
        $sql = parametros("SELECT dispositivo_code as regId FROM npp_dispositivos WHERE id_dispositivo=%d GROUP BY dispositivo_code",$id_usuario);
        $res = query($sql);

        return $res;
    }

    function anotar_enviado($id_usuario,$mensaje){
        //nada
    }


}


