<?php



function md5_usuario($id_usuario,$pass){
    return md5("__MANDIOCA_". $id_usuario . $pass);
}


function registrar_usuario($row){

    setSesionDato("datos_usuario",$row);
    setSesionDato("id_usuario",$row["id_usuario"]);
    setSesionDato("login",$row["login"]);
}