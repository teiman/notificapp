<?php

include_once(__ROOT__ . "/class/envios.class.php");

class api {


    function verificar_envio($dato_usuario,$mensaje,$titulo){
        return md5(trim($dato_usuario). "_PATATA_". trim($mensaje) . "_ALAMOGORDO_". trim($titulo));
    }

    function anotar_envio($dato_usuario,$titulo,$mensaje){
        if(!$titulo) $titulo = "Por API";


        $datos = array("titulo"=>$titulo,"notificacion"=>$mensaje);

        $id_envio = envios::crear_envio($datos);

        $res = envios::get_dispositivos_usuario($dato_usuario);

        while($row = Row($res)){
            $datos = array("id_dispositivo"=>$row["id_dispositivo"],"id_envio"=>$id_envio);

            envios::crear_notificacion($datos);
        }
    }


    /**
    * Imprime json y termina el programa
    *
    */
    function termina_json($out){
        header("Content-type: application/json");
        $out = json_encode($out);

        echo $out;
        //error_log("jsonout: ". $out);
        exit();
    }
}