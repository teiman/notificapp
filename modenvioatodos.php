<?php

include("tool.php");
include("class/group.class.php");
include("inc/paginabasica.inc.php");


if(!puedeHacer("modenvioatodos/")){
    header("Location: modnoacceso.php");
    exit;
}



$page->addVar('headers', 'titulopagina', 'Envía a todos los usuarios');
$page->setAttribute('listado', 'src', 'modenvioatodos.html');

/*
 * Guardo la id de la fila que contiene el estado de la tabla system_param
 */
$consultaId = "SELECT id_system_param FROM npp_system_param WHERE `system_param_title`='estado'";
$resConsultaId = query($consultaId);
$id = mysql_result($resConsultaId, 0);

/*
 * Consulto si nada más empezar el servidor esta on o off
 */
$consultaEstado = "SELECT system_param_value FROM npp_system_param WHERE `id_system_param`=$id";
$resConsultaEstado = query($consultaEstado);
$estado = mysql_result($resConsultaEstado, 0);

if ($estado == off) {
    $_SESSION['e'] = "Activar envios";
    $page->addVar('estadoservidor', 'mensajeinactivo', "Está inactivo");
    $page->addVar('estadoservidor', 'mensajeactivo', " ");

} else {
    $_SESSION['e'] = "Parar envios";
    $page->addVar('estadoservidor', 'mensajeinactivo', " ");
    $page->addVar('estadoservidor', 'mensajeactivo', "Está activo");
}

/*
 * Cuando clicas el botón si estado es 'Parar envios'...
 */
if ($_POST['estado'] == "Parar envios") {
    //Modifico el estado y lo pongo a 'off'
    $sql = "UPDATE npp_system_param SET system_param_value = 'off' WHERE id_system_param=$id";
    query($sql);
    //Ahora el texto del botón será 'Activar envios'
    $_SESSION['e'] = "Activar envios";
    $e = $_SESSION['e'];
    //El mensaje será 'Está inactivo' por que lo hemos apagado.  
    $page->addVar('estadoservidor', 'mensajeinactivo', "Está inactivo");
    $page->addVar('estadoservidor', 'mensajeactivo', " ");
    /*
     * Sino el botón que clicamos es 'Activar envios'
     */
} elseif ($_POST['estado'] == 'Activar envios') {
    //Modifico el estado y lo pongo a 'on'
    $sql = "UPDATE npp_system_param SET system_param_value = 'on' WHERE id_system_param=$id";
    query($sql);
    //Ahora el texto del botón será 'Parar envios'
    $_SESSION['e'] = "Parar envios";
    $e = $_SESSION['e'];
    //El mensaje será 'Está activo' por que lo hemos encendido.  
    $page->addVar('estadoservidor', 'mensajeinactivo', " ");
    $page->addVar('estadoservidor', 'mensajeactivo', "Está activo");
}
/*
 * Si pulsamos el botón enviar
 */
if ($_POST['accion'] == "enviar") {
    error_log("PLATAFORMA**" . $_POST['plataforma']);
    $mensaje = "";
    $plataforma = "";
    $titulo = "";
    /*
     * Control de errores:
     *  - El mensaje no puede ser menor que 5 carácteres
     *  - La plataforma tiene que estar seleccionada.
     */
    if (strlen($_POST['mensaje']) < 5 && $_POST['titulo'] == "" && $_POST['plataforma'] != "IOS" && $_POST['plataforma'] != "Android" && $_POST['plataforma'] != "Todas") {

        $page->addVar('error', 'error', 'Configuración del envío incorrecto');

    } elseif (strlen($_POST['mensaje']) < 5) {

        $page->addVar('error', 'error', 'Configuración del envío incorrecto');

    } elseif ($_POST['plataforma'] != "IOS" && $_POST['plataforma'] != "Android" && $_POST['plataforma'] != "Todas") {

        $page->addVar('error', 'error', 'Configuración del envío incorrecto');

    } elseif ($_SESSION['e'] == 'Activar envios') {

        $page->addVar('error', 'error', 'Error al enviar. Servicio inactivo.');
        //Si todo esta correcto guardo los datos en variables
    } elseif ($_POST['titulo'] == "") {
        $page->addVar('error', 'error', 'El titulo está vacío.');
    } else {
        //guardo el mensaje
        $mensaje = $_POST['mensaje'];
        //guardo la plataforma
        $plataforma = $_POST['plataforma'];
        $titulo = $_POST['titulo'];
        $page->addVar('plataformaseleccionada', 'plataforma_seleccionada', $plataforma);
        enviarAcola($titulo, $mensaje, $plataforma);

    }

}
/*
 * Función que recibe los parametros $titulo,$mensaje y $plataforma para luego
 * ser enviarlos a la cola de notificaciones / envio
 */
function enviarAcola($titulo, $mensaje, $plataforma)
{

    $sql_insertar_envio = "INSERT INTO npp_envios(`titulo`, `notificacion`,`fecha_creacion`) VALUES ('$titulo','$mensaje',NOW())";
    query($sql_insertar_envio);

    global $UltimaInsercion;
    $id_envio = $UltimaInsercion;

    if ($_POST['plataforma'] == 'Todas') {
        $sql_consulta = "SELECT d.id_dispositivo FROM npp_dispositivos d INNER JOIN npp_plataformas p ON p.id_plataforma=d.id_plataforma";
    } else {
        $sql_consulta = "SELECT d.id_dispositivo FROM npp_dispositivos d INNER JOIN npp_plataformas p ON p.id_plataforma=d.id_plataforma where p.plataforma='$plataforma'";
    }
    $res_consulta = query($sql_consulta);

    while ($row = Row($res_consulta)) {
        $id_dispositivo = $row['id_dispositivo'];

        $sql_insertar_notificacion = "INSERT INTO npp_notificaciones(`id_envio`, `id_dispositivo`,`fecha_creacion`,`fecha_envio`,`enviada`,`estado`) VALUES ($id_envio,$id_dispositivo,NOW(),NOW(),0,'pendiente')";
        query($sql_insertar_notificacion);

    }

}

//Rellenar Combo plataformas
$plataforma = array();

$sqlPlataforma = "SELECT plataforma FROM npp_plataformas";

$res = query($sqlPlataforma);

$plataforma[] = array("plataforma" => "Todas");

while ($row = Row($res)) {
    $plataforma[] = array("plataforma" => $row['plataforma']);
}

$page->addRows('lista_plataforma', $plataforma);
$page->addVar('estadoservidor', 'estado', $_SESSION['e']);
$page->addVar('selectplat', $_SESSION['opcion'], 'selected');

$page->Volcar();

