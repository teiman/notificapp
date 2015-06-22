<?php



function puedeHacer($command){
    $id_usuario = getSesionDato("id_usuario");

    $sql = parametros("SELECT id_permiso FROM npp_permisos WHERE path='%s'  ",$command);
    $row = queryrow($sql);

    $id_permiso = $row["id_permiso"];

    $sql = parametros("SELECT id_permiso_usuario FROM npp_usuarios_permisos WHERE id_permiso=%d and id_usuario=%d",$id_permiso,$id_usuario);
    $row = queryrow($sql);

    if($row){
        return true;
    }

    return false;
}

