<?php


class envios {


    /*
     *  Crea un envio
     */
    //$datos = array("titulo"=>$titulo,"notificacion"=>$mensaje);
    //$id_envio = envios::crear_envio($datos);
    function crear_envio($datos){
        global $UltimaInsercion;

        $sql = parametros("INSERT INTO npp_envios (titulo,notificacion, fecha_creacion) "
            ." VALUES ('%s','%s',NOW())",$datos["titulo"],$datos["notificacion"]);
        query($sql);

        return $UltimaInsercion;
    }


    //$res = envios::get_dispositivos_usuario($dato_usuario);
    function get_dispositivos_usuario($dato_usuario){
        $sql = parametros("SELECT id_dispositivo FROM npp_dispositivos WHERE dato_usuario='%s' ",$dato_usuario);

        return query($sql);
    }



    //$datos = array("id_dispositivo"=>$row["id_dispositivo"],"id_envio"=>$id_envio);
    //envios::crear_notificacion($datos);
    function crear_notificacion($datos){
        global $UltimaInsercion;

        $sql = parametros("INSERT INTO npp_notificaciones (id_envio,id_dispositivo, fecha_creacion,enviada,estado) "
            ." VALUES (%d,%d,NOW(),0,'pendiente')",$datos["id_envio"],$datos["id_dispositivo"]);
        query($sql);

        return $UltimaInsercion;
    }



}