<?php

include("tool.php");
include("inc/paginabasica.inc.php");


if (!puedeHacer("modparametros/")) {
    header("Location: modnoacceso.php");
    exit;
}

$claseinicio = "c12 omega boton_standar";
$clasefinal = "c12 omega boton_standar";

$page->setAttribute('listado', 'src', 'modparametros.html');
$page->addVar('parametros', 'controlalta', "mostrar");
$page->addVar('parametros', 'controleditar', "oculto");

if ($_REQUEST["modo"] == "reset") {
    $_SESSION['numpaginas'] = 0;
}


switch ($_POST['modo']) {
    case 'numerofilas':
        $_SESSION['numpaginas'] = 0;
        $numerofilas = $_POST['result'];
        switch ($numerofilas) {
            case 0:
                $_SESSION['nfila'] = 10;
                $_SESSION['option'] = "combouno";
                break;
            case 1:
                $_SESSION['nfila'] = 75;
                $_SESSION['option'] = "combodos";
                break;
            case 2:
                $_SESSION['nfila'] = 50;
                $_SESSION['option'] = "combotres";
                break;
            case 3;
                $_SESSION['nfila'] = 25;
                $_SESSION['option'] = "combocuatro";
                break;
            case 4;
                $_SESSION['nfila'] = 300;
                $_SESSION['option'] = "combocinco";
                break;
        }
        break;
    case "alta":
        if (strlen($_REQUEST['miparametro']) < 3 || $_REQUEST['miparametro'] == "" || $_REQUEST['mivalor'] == "") {
            $page->addVar('parametros', 'txto', "Los datos introducidos no son correctos");
        } else {
            $parametro = $_POST['miparametro'];
            $valor = $_POST['mivalor'];
            $sqlbuscar = parametros("SELECT * FROM npp_system_param WHERE `system_param_title` = '%s'", $parametro);
            $resbuscar = query($sqlbuscar);
            $numfilas = mysql_numrows($resbuscar);
            if ($numfilas == 0) {
                $sqlguardar = parametros("INSERT INTO `system_param` (`system_param_title`, `system_param_value`) VALUES ('%s', '%s')", $parametro, $valor);
                $resguardar = query($sqlguardar);
            } else {
                $page->addVar('parametros', 'txto', "Los datos introducidos no son correctos");
            }
        }

        break;
    case "eliminar":
        $miid = $_POST['id_system'];
        $sqlborrar = parametros("DELETE FROM npp_system_param where id_system_param='%d'", $miid);
        $resborrar = query($sqlborrar);
        $_SESSION['numpaginas'] = 0;
        break;
    case "editar":
        //Modificar botones
        $page->addVar('parametros', 'controlalta', "oculto");
        $page->addVar('parametros', 'controleditar', "mostrar");

        //Recogida de datos
        $idedit = $_POST['id_system'];
        $sqleditable = parametros("Select * FROM npp_system_param where id_system_param='%d'", $idedit);

        $reseditable = query($sqleditable);
        $roweditable = Row($reseditable);

        $page->addVar('parametros', 'parametroeditar', $roweditable['system_param_title']);
        $page->addVar('parametros', 'valoreditar', $roweditable['system_param_value']);

        $_SESSION['ideditable'] = $idedit;
        break;
    case "guardar":
        $parametroeditar = $_POST['parametroeditable'];
        $valoreditar = $_POST['valoreditable'];
        $idwhere = $_SESSION['ideditable'];

        if (strlen($parametroeditar) < 3 || $parametroeditar == "") {
            $page->addVar('parametros', 'txto', "Los datos introducidos no son correctos");
        } else {
            $sqleditar = parametros("UPDATE npp_system_param SET `system_param_title`='%s',`system_param_value`='%s' WHERE `id_system_param`='%d'", $parametroeditar, $valoreditar, $idwhere);
            $resguardar = query($sqleditar);
            $page->addVar('parametros', 'controlalta', "mostrar");
            $page->addVar('parametros', 'controleditar', "oculto");
        }

        break;
    case "cancelar":
        $page->addVar('parametros', 'controlalta', "mostrar");
        $page->addVar('parametros', 'controleditar', "oculto");
        break;
    case "haciadelante";
        $pag = $_SESSION['maximo'];
        $nfila = $_SESSION['nfila'];
        if ($_SESSION['numpaginas'] + 1 < ($pag / $nfila)) {
            $_SESSION['numpaginas'] += 1;
        }
        break;
    case "haciadetras";
        if ($_SESSION['numpaginas'] - 1 >= 0) {
            $_SESSION['numpaginas'] -= 1;
        }
        break;
    case "irinicio";
        $_SESSION['numpaginas'] = 0;
        break;
    case "irfinal":
        $nfila = $_SESSION['nfila'];
        $pag = $_SESSION['maximo'] - 1;
        $_SESSION['numpaginas'] = (int) ($pag / $nfila);
        break;
}

//La primera vez por defecto el numero de filas por pagina seran 100
if ($_SESSION['nfila'] == "") {
    $_SESSION['nfila'] = 100;
}

//Control del Record set ------------------------------------
$sqlcontador = "SELECT * FROM npp_system_param";
$rescontador = query($sqlcontador);
$numregistros = mysql_numrows($rescontador);

$_SESSION['maximo'] = $numregistros;
$pag = $_SESSION['maximo'];

$nfila = $_SESSION['nfila'];
$tam_pagina = $nfila;
$minimo = $tam_pagina * $_SESSION['numpaginas'];
$maximo = $tam_pagina * ($_SESSION['numpaginas'] + 1);

//Listado de parametros -------------------------------------
$lista = array();

$sql = parametros("SELECT * FROM npp_system_param LIMIT %d, %d", $minimo, $nfila);
$res = query($sql);

while ($row = Row($res)) {
    ++$i;
    $miclase = ($i % 2) ? "fila_02" : "fila_01";
    $lista[] = array(
        "id" => $row['id_system_param'], "miparametro" => $row['system_param_title'],
        "mivalor" => $row['system_param_value'], "clase" => $miclase
    );
}

//Control paginación --------------------------------------- 
if ($maximo > $numregistros && $_SESSION['numpaginas'] == 0) {
    $maximo = $numregistros;
} elseif ($maximo > $numregistros) {
    $maximo = $numregistros;
}
//Estilo
if ($_SESSION['numpaginas'] == 0) {
    $claseinicio = "clasepag";
} elseif ($_SESSION['numpaginas'] == (($_SESSION['maximo'] - 1) / $nfila)) {
    $clasefinal = "clasepag";
}

$page->addRows("list_entry", $lista);
$page->addVar('parametros', 'contador', "$minimo - $maximo de $numregistros");
$page->addVar('paginacion', 'claseinicio', $claseinicio);
$page->addVar('paginacion', 'clasefinal', $clasefinal);
$page->addVar('select_option', $_SESSION['option'], 'selected');

$page->Volcar();
