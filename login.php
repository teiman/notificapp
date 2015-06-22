<?php

//define("CAPTURARTODOBUG",true);
include("tool.php");
include("inc/paginabasica.inc.php");
include_once("inc/usuarios.inc.php");

$modo = $_REQUEST["modo"];

switch($modo){

    //mensaje-error-logueo
    case "entrar":
        $login = $_REQUEST["login"];
        $pass = $_REQUEST["pass"];

        $mensaje = "Usuario o contraseña incorrecta";

        $mensaje = "HINT: Si ha olvidado su contraseña, intente con usuario admin y contraseña admin";

        if(!$login || !$pass) break;


        $sql = parametros("SELECT id_usuario FROM npp_usuarios WHERE login='%s' LIMIT 1",$login);
        $row = queryrow($sql);

        $id_usuario = $row["id_usuario"];

        if(!$id_usuario) break;

        $pass_md5 = md5_usuario($id_usuario,$pass);

        $sql = parametros("SELECT * FROM npp_usuarios WHERE login='%s' and pass='%s' LIMIT 1",$login,$pass_md5);
        $row = queryrow($sql);

        if(!$row) break;


        registrar_usuario($row);

        header("Location: modfiltro_envio.php");
        exit();
}


$page->setAttribute('listado', 'src', 'modlogin.html');


if($mensaje){
    $page->addVar("listado","mensajecss","visible");
    $page->addVar("listado","mensaje-error-logueo", $mensaje);
}



$page->Volcar();


//$page->addVar("listado","hola","Desde santurce a bilbao");
//$data = queryrow("SELECT 1+2+3");
//echo var_export($data,true);




