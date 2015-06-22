<?php

include("tool.php");
include_once("class/servidorenvios.inc.php");

$servidormensajes = new servidorenvios_notificapp();

function enviar_notificacion($datos){
    global $servidormensajes;

    $id_dispositivo = $datos["id_dispositivo"];

    logear("Se envia para id_dispositivo($id_dispositivo)");

    $servidormensajes->enviar($id_dispositivo,$datos["notificacion"]);

    return true;
}

function anotar_enviada($row){

    $id_notificacion = $row["id_notificacion"];

    $sql = parametros("UPDATE npp_notificaciones SET enviada=1,estado='enviada', fecha_envio=NOW() WHERE id_notificacion=%d",$id_notificacion);
    query($sql);
}

function logear($texto){
    echo "<div>" .date("U").": ".html($texto). "</div>\n";
}


/*
malUPDATE dispositivos SET plataforma_version="Android"
UPDATE dispositivos SET dispositivo_code = MD5(RAND())
*/

//query("UPDATE npp_notificaciones SET enviada=0, estado='pendiente'");



$sql = "SELECT id_notificacion,id_dispositivo,notificacion FROM npp_notificaciones "
    ." JOIN npp_envios ON npp_notificaciones.id_envio = npp_envios.id_envio "
    ." WHERE enviada=0 and estado='pendiente' ORDER BY npp_notificaciones.fecha_creacion ASC LIMIT 10";


logear("Buscando notificaciones pendientes");

$res = query($sql);

$num = $FilasAfectadas;

logear("Preparando '$num' para enviar");



while($row = Row($res)){

    logear("Enviando...");
    $enviado = enviar_notificacion($row);

    if($enviado){
        logear("Enviada.");
        anotar_enviada($row);
    }
}


logear("Termino los envios");