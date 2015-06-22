<?php


include("tool.php");


include_once(__ROOT__ . "/class/api.class.php");
include_once(__ROOT__ . "/class/envios.class.php");

$modo = $_REQUEST["modo"];

$out  = array("ok"=>true);

switch($modo){

    case "eco":
        $payload = $_REQUEST["payload"];

        $out["payload"] = $payload;

        error_log("se ha visitado eco");
        sleep(12);
        error_log("se hizo esperar al visitante 12 segundos");

        api::termina_json($out);
        break;

    case "realizar_envio":
        $dato_usuario = $_REQUEST["dato_usuario"];
        $mensaje = $_REQUEST["mensaje"];
        $titulo = $_REQUEST["titulo"];
        $hash = $_REQUEST["codigo"];

        if($hash != api::verificar_envio($dato_usuario,$mensaje,$titulo)){
            $out = array("ok" => false, "error" => "autentificacion");
            api::termina_json($out);
            break;
        }

        api::anotar_envio($dato_usuario,$titulo,$mensaje);

        api::termina_json($out);
        break;

    case "anotar_regid":
        $identificador = $_REQUEST["identificador"];
        $plataforma = $_REQUEST["plataforma"];//1=Android,2=iOS
        $plataforma_long = $_REQUEST["plataforma_long"];
        $dato_usuario = $_REQUEST["dato_usuario"];

        query( parametros("DELETE FROM npp_dispositivos WHERE id_plataforma=%d and dato_usuario='%s'",$plataforma,$dato_usuario) );

        $sql = "INSERT INTO npp_dispositivos (id_plataforma,plataforma_version,dispositivo_code,dato_usuario) "
            ." VALUES (%d,'%s','%s','%s')"
        ;

        $sql = parametros($sql,$plataforma,$plataforma_long,$identificador,$dato_usuario);
        query($sql);

        $out = array("ok" => true,"anotar_regid"=>true);

        api::termina_json($out);
        break;

}