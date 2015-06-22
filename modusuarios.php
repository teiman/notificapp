<?php


//define("CAPTURARTODOBUG",true);

include("tool.php");
include("inc/paginabasica.inc.php");
include("inc/usuarios.inc.php");

if(!puedeHacer("modusuarios/")){
    header("Location: modnoacceso.php");
    exit;
}



$offset = 0;

$modo = $_REQUEST["modo"];

$mostrarListado = true;
$mostrarEdicion = false;
$mensaje = false;

switch ($modo) {
    case "alta":
        $login = $_REQUEST["usuario"];
        $pass = $_REQUEST["pass"];

        d2($modo,"debug");

        if(!$login || !$pass){
            $mostrarListado = true;
            $mensaje = "Usuario o contraseña vacios";

            d2($mensaje,"debug");
            d2($mensaje,"debug");
            break;
        }

        $sql = parametros("INSERT INTO npp_usuarios(login,pass) VALUES ('%s','%s')",$login,"_CREANDO_");
        query($sql);

        $id_usuario = $UltimaInsercion;
        $datos["id_usuario"] = $id_usuario;

        $md5_pass = md5_usuario($id_usuario,$pass);
        $sql = parametros("UPDATE npp_usuarios SET pass='%s' WHERE id_usuario=%d",$md5_pass,$id_usuario);
        query($sql);

        $sql = parametros("SELECT * FROM npp_usuarios WHERE id_usuario=%d",$id_usuario);
        $datos = queryrow($sql);

        $mostrarEdicion = true;
        break;

    case "guardarcambios":
        $id_usuario = $_REQUEST["id_usuario"];
        $permisos = $_REQUEST["permisos"];

        query( parametros("DELETE FROM npp_usuarios_permisos WHERE id_usuario=%d",$id_usuario));

        foreach($permisos as $n=>$id_permiso){
            query( parametros("INSERT npp_usuarios_permisos (id_permiso, id_usuario) VALUES (%d,%d)",$id_permiso,$id_usuario) );
        }

        if($_REQUEST["pass1"]==$_REQUEST["pass2"] && $_REQUEST["pass1"]){
            $md5_pass = md5_usuario($id_usuario,$_REQUEST["pass1"]);
            $sql = parametros("UPDATE npp_usuarios SET pass='%s' WHERE id_usuario=%d",$md5_pass,$id_usuario);
            query($sql);
        }


        d2($permisos,"dato");
        break;
    case "editar":
        $id_usuario = $_POST["id_usuario"];

        $sql = parametros("SELECT * FROM npp_usuarios WHERE id_usuario=%d",$id_usuario);
        $datos = queryrow($sql);

        $mostrarEdicion = true;
        break;
    default:
        break;
}



if($mostrarListado){
    $page->setAttribute('listado', 'src', 'modusuarios.html');

    $lista = array();


    $sql = parametros("SELECT * FROM npp_usuarios LIMIT %d, 50",$offset);
    $res = query($sql);

    while ($row = Row($res)) {
        $lista[] = $row;
    }
    $page->addRows("list_entry", $lista);


    if($mensaje)
        $page->setAttribute('listado', 'mensaje', $mensaje);
    else
        $page->setAttribute('listado', 'cssmensaje', "oculto");
}

if($mostrarEdicion){
    $page->setAttribute('listado', 'src', 'modusuarios_editar.html');


    foreach($datos as $key=>$value){
        $page->addVar('usuario_editar', $key, $value);
    }


    $id = intval($id_usuario);

    $sql = "SELECT npp_permisos.id_permiso, npp_permisos.path, id_permiso_usuario "
         ." FROM permisos LEFT JOIN npp_usuarios_permisos ON (npp_permisos.id_permiso = npp_usuarios_permisos.id_permiso and npp_usuarios_permisos.id_usuario=$id)"
         ." ORDER BY grupo ASC, path ASC";

    $res = query($sql);

    $lineas = array();

    while($row = Row($res)){

        $pid = $row["id_permiso_usuario"];
        if(!$pid || $pid=="NULL"){
            //no esta seleccionada
        } else {
            $row["checked"] = " checked='checked' ";
        }

        $lineas[] = $row;
    }

    $page->addRows('lista_permisos', $lineas);
}




$page->Volcar();
