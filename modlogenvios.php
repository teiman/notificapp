<?php

include("tool.php");
include("inc/paginabasica.inc.php");

$claseinicio = "c12 omega boton_standar";
$clasefinal = "c12 omega boton_standar";

if (!puedeHacer("modlogenvios/")) {
    header("Location: modnoacceso.php");
    exit;
}

$page->setAttribute('listado', 'src', 'modlogenvios.html');

if ($_REQUEST["modo"] == "reset") {
    $_SESSION['numpag'] = 0;
    unset($_SESSION['where']);
    $_SESSION['comboestado'] = "";
    header("Location: modlogenvios.php");
    $datosintroducidos[] = array(
        "usuario" => "", "mensaje" => "", "titulo" => "", "fechaentrada" => "", "fechasalida" => ""
    );
}

//Botones -------------------------
switch ($_POST['modo']) {
    case 'numerofilas':
        $_SESSION['numpag'] = 0;
        $numerofilas = $_POST['result'];
        switch ($numerofilas) {
            case 0:
                $_SESSION['nfila'] = 100;
                $_SESSION['option'] = "combouno";
                break;
            case 1:
                $_SESSION['nfila'] = 150;
                $_SESSION['option'] = "combodos";
                break;
            case 2:
                $_SESSION['nfila'] = 200;
                $_SESSION['option'] = "combotres";
                break;
            case 3;
                $_SESSION['nfila'] = 250;
                $_SESSION['option'] = "combocuatro";
                break;
            case 4;
                $_SESSION['nfila'] = 300;
                $_SESSION['option'] = "combocinco";
                break;
        }
        break;
    case 'siguiente':
        $pag = $_SESSION['max'];
        $nfila = $_SESSION['nfila'];

        if ($_SESSION['numpag'] + 1 < ($pag / $nfila)) {
            $_SESSION['numpag'] += 1;
        }
        break;
    case 'atras':
        if ($_SESSION['numpag'] - 1 >= 0) {
            $_SESSION['numpag'] -= 1;
        }
        break;
    case 'inicio':
        $_SESSION['numpag'] = 0;
        break;
    case 'final':
        $nfila = $_SESSION['nfila'];
        $pag = $_SESSION['max'] - 1;
        $_SESSION['numpag'] = (int) ($pag / $nfila);
        break;
    case 'filtrar':
        //Recogida de datos ---------------
        $_SESSION['numpag'] = 0;
        $usuario = $_POST['usuario'];
        $fechacreaciondesde = $_POST['fechacreaciondesde'];
        $fechacreacionhasta = $_POST['fechacreacionhasta'];
        $miplataforma = $_POST['plataforma'];
        $titulo = $_POST['titulo'];
        $mensaje = $_POST['mensaje'];
        $enviado = $_POST['enviado'];

        //Control de plataforma --------------
        if ($miplataforma == "selecciona") {
            $miplataforma = "";
        }

        //Variable con los elementos a filtrar
        $_SESSION['where'] = "";

        if ($usuario !== "") {
            $_SESSION['where'] .= parametros(" and d.dato_usuario='%s'",$usuario);
        }

        if ($miplataforma !== "") {
            $_SESSION['where'] .= parametros(" and p.plataforma='%s'",$miplataforma);
        }

        if ($titulo !== "") {
            $_SESSION['where'] .= parametros(" and e.titulo like '%s'", "%".$titulo."%");
        }

        if ($mensaje !== "") {
            $_SESSION['where'] .= parametros(" and e.notificacion like '%s'","%".$mensaje."%");
        }

        if ($enviado == "si") {
            $_SESSION['where'] .= " and n.estado='enviada'";
            $_SESSION['comboestado'] = 'selectenviado';
        } elseif ($enviado == "no") {
            $_SESSION['where'] .= " and n.estado='pendiente'";
            $_SESSION['comboestado'] = 'selectpendiente';
        } else {
            $_SESSION['comboestado'] = 'selecciona';
        }

        if ($fechacreaciondesde !== "" and $fechacreacionhasta !== "") {
            list($diadesde, $mesdesde, $añodesde) = split('[/-]', $fechacreaciondesde);
            list($diahasta, $meshasta, $añohasta) = split('[/-]', $fechacreacionhasta);

            if (checkdate($mesdesde, $diadesde, $añodesde) and checkdate($meshasta, $diahasta, $añohasta)) {
                $_SESSION['where'] .= " and n.fecha_envio BETWEEN '$añodesde/$mesdesde/$diadesde' AND '$añohasta/$meshasta/$diahasta'";
            } else {
                $errordato = "Debe introducir un dato fecha";
            }

        } else {
            if ($fechacreaciondesde !== "" and $fechacreacionhasta == "") {
                list($diadesde, $mesdesde, $añodesde) = split('[/-]', $fechacreaciondesde);
                if (checkdate($mesdesde, $diadesde, $añodesde)) {
                    $tiempo = getdate();
                    $dia = $tiempo['mday'];
                    $mes = $tiempo['mon'];
                    $año = $tiempo['year'];
                    $_SESSION['where'] .= " and n.fecha_envio BETWEEN '$añodesde/$mesdesde/$diadesde' AND '$año/$mes/$dia'";
                } else {
                    $errordato = "Debe introducir un dato fecha";
                }
            } elseif ($fechacreaciondesde == "" and $fechacreacionhasta !== "") {
                list($diahasta, $meshasta, $añohasta) = split('[/-]', $fechacreacionhasta);
                if (checkdate($meshasta, $diahasta, $añohasta)) {
                    $_SESSION['where'] .= " and n.fecha_envio BETWEEN '1/0/0' AND '$añohasta/$meshasta/$diahasta'";
                } else {
                    $errordato = "Debe introducir un dato fecha";
                }
            }
        }

        break;
}

$where = $_SESSION['where'];

//Indicador del número de resultados

$sqlbuscar = "SELECT n.id_notificacion,d.dato_usuario, e.titulo, n.fecha_envio, p.plataforma,e.notificacion, n.estado
FROM npp_notificaciones n INNER JOIN npp_envios e ON e.id_envio=n.id_envio INNER JOIN npp_dispositivos d ON d.id_dispositivo=n.id_dispositivo
INNER JOIN npp_plataformas p ON p.id_plataforma=d.id_plataforma
WHERE n.estado!='eliminada' $where ORDER BY n.id_notificacion";

$resconsulta = query($sqlbuscar);
$num = mysql_numrows($resconsulta);

//La primera vez por defecto el numero de filas por pagina seran 100
if ($_SESSION['nfila'] == "") {
    $_SESSION['nfila'] = 100;
}

$_SESSION['max'] = $num;
$pag = $_SESSION['max'];

$nfila = $_SESSION['nfila'];
$tam_pagina = $nfila;

$min = $tam_pagina * $_SESSION['numpag'];
$max = $tam_pagina * ($_SESSION['numpag'] + 1);


//Indicador del número de resultados
$sqlbuscar = "SELECT n.id_notificacion,d.dato_usuario, e.titulo, n.fecha_envio, p.plataforma,e.notificacion, n.estado
FROM npp_notificaciones n,npp_plataformas p, npp_envios e, npp_dispositivos d
WHERE e.id_envio=n.id_envio and d.id_dispositivo=n.id_dispositivo and p.id_plataforma=d.id_plataforma
and n.estado!='eliminada'
ORDER BY n.id_notificacion";


if ($max > $pag && $_SESSION['numpag'] == 0) {
    $max = $pag;
} elseif ($max > $pag) {
    $max = $pag;
}
if ($_SESSION['numpag'] == 0) {
    $claseinicio = "clasepag";
} elseif ($_SESSION['numpag'] == (int) (($_SESSION['max'] - 1) / $nfila)) {
    $clasefinal = "clasepag";
}

//Listado ---------------------------
$sql = "SELECT n.id_notificacion,d.dato_usuario, e.titulo, n.fecha_envio, p.plataforma,e.notificacion, n.estado
FROM npp_notificaciones n INNER JOIN npp_envios e ON e.id_envio=n.id_envio INNER JOIN npp_dispositivos d ON d.id_dispositivo=n.id_dispositivo
INNER JOIN npp_plataformas p ON p.id_plataforma=d.id_plataforma
WHERE n.estado!='eliminada' $where ORDER BY n.id_notificacion LIMIT $min, $nfila";

$res = query($sql);
$datosintroducidos[] = array(
    "usuario" => "$usuario", "mensaje" => "$mensaje", "titulo" => "$titulo", "fechaentrada" => "$fechacreaciondesde",
    "fechasalida" => "$fechacreacionhasta"
);

while ($row = Row($res)) {
    ++$i;
    $clase = ($i % 2) ? "fila_02" : "fila_01";
    if ($row['estado'] == "pendiente") {
        $imagen = "img/no.png";
    } elseif ($row['estado'] == "enviada") {
        $imagen = "img/yes.png";
    };

    $lista[] = array(
        "id" => $row['id_notificacion'], "datousuario" => $row['dato_usuario'], "titulo" => $row['titulo'],
        "fechaEnvio" => $row['fecha_envio'], "plataforma" => $row['plataforma'], "mensaje" => $row['notificacion'],
        "estado" => $imagen, "miclase" => $clase
    );
}

//Combo tipo plataformas ------------
$plataforma = array();

$sqlplataforma = "SELECT plataforma FROM npp_plataformas";
$resplataforma = query($sqlplataforma);

while ($row = Row($resplataforma)) {
    if ($row['plataforma'] == $miplataforma) {
        $selected = 'selected';
    } else {
        $selected = "";
    }

    $plataforma[] = array("plataforma" => $row['plataforma'], "selected" => $selected);
}

//Nombre comtenedor / Nombre template / Dato
$page->addVar('select_option', $_SESSION['option'], 'selected');
$page->addVar('select_estado', $_SESSION['comboestado'], 'selected');
//Template general
$page->addRows('list_plataforma', $plataforma);
$page->addRows('list_entry', $lista);
$page->addVar('logenvios', 'contador', "$min - $max de $pag");
$page->addVar('logenvios', 'errorDato', $errordato);
$page->addVar('paginacion', 'claseinicio', $claseinicio);
$page->addVar('paginacion', 'clasefinal', $clasefinal);
$page->addRows('datosintroducidos', $datosintroducidos);

$page->Volcar();
