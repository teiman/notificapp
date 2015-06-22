<?php

include("tool.php");
include("inc/paginabasica.inc.php");

if(!puedeHacer("modconfiguracion/")){
    header("Location: modnoacceso.php");
    exit;
}


$mode = $_REQUEST['mode'];

$etiqueta = $_POST['etiqueta'];
$campo = $_POST['campo'];

$campoenlace = $_POST['campoenlace'];
$camposql = $_POST['sql'];

$num = 0;

//Botones ---------------------------------
switch ($mode) {
    case "add":

        if (strlen($etiqueta) > 2 && strlen($campo) > 2) {
            $sql = parametros("SELECT * FROM `npp_campos_filtro` WHERE etiqueta='%s'",$etiqueta);
            $res = query($sql);
            $num = mysql_numrows($res);

            if (!$num) {
                $sql_add = parametros("INSERT INTO `npp_campos_filtro` (`etiqueta`, `campo`) VALUES ('%s', '%s')", $etiqueta, $campo);

                query($sql_add);
                $texto = "Se han introducido correctamente";
                $clase = "normal";
            }
        } else {
            $texto = "Los datos del filtro son incorrectos";
            $clase = "error";
        }

        break;

    case "save":
            if (!$campoenlace  || !$camposql) {
                $error = "Debe introducir campo enlace y sql maestro";
            } else {
                setParametro("campo_usuario",trim($campoenlace));
                setParametro("sql_maestro",trim($camposql));

                //NOTA: Oscar: He quitado un montonde codigo que no sabia lo que hacia. Derp. Creo que no tendria que existir, si hace algo util lo podemos
                // recuperar del historico de git
            }
        break;

    case "eliminar":
        $id = $_POST['id_campo_filtro'];
        $sql = parametros("DELETE FROM `npp_campos_filtro` WHERE `id_campo_filtro` = '%s'", $id);

        query($sql);
        break;

    case "siguiente":

        $tab = $_SESSION['max'];

        if ($_SESSION['move'] + 1 < ($tab / 50)) {
            $_SESSION['move'] += 1;
        }
        break;
        
    case "irInicio":
        
        $_SESSION['move'] = 0;
        
        break;
    
    case "irFinal":
        
        $_SESSION['move'] = (int)($tab / 50);
        
        break;
    
    case "atras":

        if ($_SESSION['move'] - 1 > -1) {
            $_SESSION['move'] -= 1;
        }
        break;

    default:
        break;
}

//Control del Record set ------------------------------------
$sql = "SELECT * FROM npp_campos_filtro";   //TODO: order?

$res = query($sql);
$num = mysql_numrows($res);

$_SESSION['max'] = $num;
$tab = $_SESSION['max'];

$tam_pagina = 50;
$min = $tam_pagina * $_SESSION['move'];
$max = $tam_pagina * ($_SESSION['move'] + 1);

if ($max > $tam_pagina && $_SESSION['max'] == 0) {
    $max = $tab;
}
if ($max > $tab) {
    $max = $tab;
}

//Creacion del Record set----------------------------------------
$list = array();

$sql = parametros("SELECT * FROM npp_campos_filtro ORDER BY etiqueta ASC LIMIT %d,%d", $min, $tam_pagina);
$res = query($sql);

while ($row = Row($res)) {
    ++$i;

    $linea = ($i % 2) ? "fila_02" : "fila_01";

    $list[] = array(
        "id" => $row['id_campo_filtro'],
        "etiqueta" => $row['etiqueta'],
        "campo" => $row['campo'],
        "linea" => $linea,
        "nombre" => 'nombre' . $i);
}

//Control de botones de paginacion -------------------------------------

if ($_SESSION['move'] > 0 && $_SESSION['page'] < (int)($tab/$tam_pagina)){
    $page->addVar('paginacion', 'inicio', "mostrar");
    $page->addVar('paginacion', 'anterior', "mostrar");
    $page->addVar('paginacion', 'siguiente', "mostrar");
    $page->addVar('paginacion', 'final', "mostrar");
    
}elseif ($_SESSION['move'] == (int)($tab/$tam_pagina)){
    $page->addVar('paginacion', 'inicio', "mostrar");
    $page->addVar('paginacion', 'anterior', "mostrar");
    $page->addVar('paginacion', 'final', "ocultar");
    $page->addVar('paginacion', 'siguiente', "ocultar");
    
}elseif ($_SESSION['move'] == 0){
    $page->addVar('paginacion', 'inicio', "ocultar");
    $page->addVar('paginacion', 'anterior', "ocultar");
    $page->addVar('paginacion', 'siguiente', "mostrar");
    $page->addVar('paginacion', 'final', "mostrar");
    
}elseif ($_SESSION['move'] == 0 && $_SESSION['move'] == (int)($tab/$tam_pagina)){
    $page->addVar('paginacion', 'inicio', "ocultar");
    $page->addVar('paginacion', 'anterior', "ocultar");
    $page->addVar('paginacion', 'siguiente', "ocultar");
    $page->addVar('paginacion', 'final', "ocultar");
    
}


//Mostrar sql maestro---------------------------------------
$sql_maestro = getParametro("sql_maestro");


//Load page -------------------------------------
$page->setAttribute('listado', 'src', 'modconfiguracion.html');

$page->addVar('error', 'error', $error);
$page->addVar('sql', 'sqlmaestro', getParametro("sql_maestro"));
$page->addVar('sql', 'campo_usuario', getParametro("campo_usuario"));


$page->addVar('count', 'min', "$min - $max de $tab");
$page->addRows('config', $list);

$page->Volcar();

function check($consulta) {
    return true;
}
