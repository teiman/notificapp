<?php

include("tool.php");
include("class/group.class.php");
include("inc/paginabasica.inc.php");

if (!puedeHacer("modcolanotificaciones/")) {
    header("Location: modnoacceso.php");
    exit;
}
/*
  Poner el estilo que quieres que se vean en los botones 'inicio'
  y final cuando NO estas en el principio ni en el final.
 * 
 * Nota: Buscar mas abajo las mismas variables para cambiar el estilo de cuando
 * SI estes en inicio o final.
 */
$styleinicio = 'boton_standar c12 omega ';
$stylefinal = 'c12 omega boton_standar ';

$page->addVar('headers', 'titulopagina', 'Cola de Notificaciones');
if ($_REQUEST["modo"] == "conexion") {
    $_SESSION['numpagina'] = 0;
    header("Location: modcolanotificaciones.php");
}
switch ($_POST["modo"]) {
    /*
     * Controla la elección de la paginación.
     */
    case "numfilas":
        $_SESSION['numpagina'] = 0;
        switch ($_POST['npag']) {
            case 100:
                $_SESSION['numfila'] = 100;
                $_SESSION['option'] = "combo1";
                break;
            case 75:
                $_SESSION['numfila'] = 75;
                $_SESSION['option'] = "combo2";
                break;
            case 50:
                $_SESSION['numfila'] = 50;
                $_SESSION['option'] = "combo3";
                break;
            case 25:
                $_SESSION['numfila'] = 25;
                $_SESSION['option'] = "combo4";
                break;
            default:
                break;
        }

    case "refrescar":
        $nfila = $_SESSION['numfila'];
        $min = $nfila * $_SESSION['numpagina'];

        $sqlRefrescar = parametros("SELECT d.dato_usuario, e.titulo, p.plataforma,e.notificacion
                    FROM npp_notificaciones n INNER JOIN npp_envios e ON e.id_envio=n.id_envio
                    INNER JOIN npp_dispositivos d ON d.id_dispositivo=n.id_dispositivo
                    INNER JOIN npp_plataformas p ON p.id_plataforma=d.id_plataforma
                    WHERE n.estado='pendiente' ORDER BY n.id_notificacion LIMIT %d, %d", $min, $nfila);

        query($sqlRefrescar);


        break;

    case "cancelar":

        //Se necesita el id del usuario de esa fila para solo eliminar ese.
        $id = $_POST['id_notificacion'];

        $sqlCancelarId = parametros("UPDATE npp_notificaciones SET estado='eliminada' where id_notificacion='%d'", $id);

        query($sqlCancelarId);
        $_SESSION['numpagina'] = 0;
        break;

    case "cancelartodos":

        $sqlCancelarTodo = "UPDATE npp_notificaciones SET estado='eliminada' where estado='pendiente'";
        query($sqlCancelarTodo);
        $min = 0;
        break;

    case "irInicio":

        $_SESSION['numpagina'] = 0;
        break;

    case "atras":

        if ($_SESSION['numpagina'] - 1 > -1) {
            $_SESSION['numpagina'] -= 1;
        }

        break;

    case "siguiente":

        $pag = $_SESSION['max'];
        $nfila = $_SESSION['numfila'];

        if ($_SESSION['numpagina'] + 1 < ($pag / $nfila)) {
            $_SESSION['numpagina'] += 1;
        }

        break;

    case "irFinal":

        $nfila = $_SESSION['numfila'];
        $pag = $_SESSION['max'] - 1;
        $_SESSION['numpagina'] = (int) ($pag / $nfila);

        break;

    default:
        break;
}

$page->setAttribute('listado', 'src', 'modcolanotificaciones.html');

$list = array();
//-----------------Paginacion ------------------ 
$consulta = "SELECT * FROM npp_notificaciones where estado='pendiente'";
$resconsulta = query($consulta);
$num = mysql_numrows($resconsulta);

//Si no hay filas tiene que cambiarse el class del botón cancelar
if ($num > 0) {
    $c = "oculto";
    $cancel = "cancelar";
    $page->addVar('botones', 'controldato', "mostrar");
} else {
    $c = " ";
    $cancel = "oculto";
    $page->addVar('botones', 'controldato', "oculto");
    $min = 0;
}
//La primera vez por defecto el numero de filas por pagina seran 100
if ($_SESSION['numfila'] == "") {
    $_SESSION['numfila'] = 100;
}

$_SESSION['max'] = $num;
$pag = $_SESSION['max'];

$nfila = $_SESSION['numfila'];
$tam_pagina = $nfila;

$min = $tam_pagina * $_SESSION['numpagina'];
$max = $tam_pagina * ($_SESSION['numpagina'] + 1);


if ($max > $pag && $_SESSION['numpagina'] == 0) {
    $max = $pag;
} elseif ($max > $pag) {
    $max = $pag;
}
/*
  Poner el estilo que quieres que se vean en los botones 'inicio'
  y final cuando "SI" estas en el principio y en el final.
 */
//PARA EL ESTILO DE LA PAGINACION
if ($_SESSION['numpagina'] == 0) {  //Si estas en 'inicio'
    $styleinicio = 'ponerestilo ';
} elseif ($_SESSION['numpagina'] == (int) ($pag / $nfila)) { //si eestas en 'final'
    $stylefinal = 'ponerestilo ';
}

//Lista la cola de notificaciones
$sql = parametros("SELECT n.id_notificacion,d.dato_usuario, e.titulo, p.plataforma,e.notificacion
                FROM npp_notificaciones n INNER JOIN npp_envios e ON e.id_envio=n.id_envio
                INNER JOIN npp_dispositivos d ON d.id_dispositivo=n.id_dispositivo
                INNER JOIN npp_plataformas p ON p.id_plataforma=d.id_plataforma
                WHERE n.estado='pendiente' 
                ORDER BY n.id_notificacion LIMIT %d,%d", $min, $nfila);

$res = query($sql);

//contador de filas
$numFilas = 0;

while ($row = Row($res)) {
    //Si las filas son pares se mostrara un estilo(css) sino otro.
    if ($numFilas % 2 == 0) {
        $estilo = "fila_02";
    } else {
        $estilo = "fila_01";
    }

    $numFilas++;
    $fila = array(
        "id" => $row['id_notificacion'],
        "estilo" => $estilo,
        "dato_usuario" => $row['dato_usuario'],
        "titulo" => $row['titulo'],
        "plataforma" => $row['plataforma'],
        "notificacion" => $row['notificacion']
    );
    $list[] = $fila;
}


$page->addRows('lista', $list);
$page->addVar('vacio', 'classvacio', $c);
$page->addVar('lista', 'classcancel', $cancel);
$page->addVar('paginas', 'min', "$min - $max de $pag");
$page->addVar('selectpag', $_SESSION['option'], 'selected');

$page->addVar('stylepaginacion', 'styleinicio', $styleinicio);
$page->addVar('stylepaginacion', 'stylefinal', $stylefinal);
$page->Volcar();


