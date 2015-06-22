<?php
include("tool.php");
include("inc/paginabasica.inc.php");


if(!puedeHacer("modfiltro_envio/")){
    header("Location: modnoacceso.php");
    exit;
}


$mostrar = $_SESSION['mostrar'];
$ocultar = $_SESSION['ocultar'];

if ($mostrar == "" && $ocultar == ""){
   $_SESSION['mostrar'] = 'display:block';
   $_SESSION['ocultar'] = 'display:none';
   
   $mostrar = $_SESSION['mostrar'];
   $ocultar = $_SESSION['ocultar'];
}
/*
if ($_SESSION['tam_tabla'] == 0 || $_SESSION['tam_tabla'] == ""){
   $_SESSION['tam_tabla'] = 100;
}
*/
//Botones ------------------------------------------
//unset($_SESSION["combos_datos"]);
//unset($_SESSION["sql_maestro"]);
//unset($_SESSION["sql_completo"]);
$mode = $_REQUEST['modo'];

$titulo_envio = $_POST['t_filtro'];
$notificacion = $_POST['notificacion'];
$plataform = $_POST['plataforma'];
$whereconcat = "";

//Botones ---------------------------------------
switch ($mode) {
   case "reset_data":
       unset($_SESSION['notify']);
       unset($_SESSION['title']);
       unset($_SESSION['plataforma']);
       unset($_SESSION['mostrar']);
       unset($_SESSION['ocultar']);
       unset($_SESSION['disabled']);
       /*
        * Para que la url vuelva a ser : modfiltro_envio.php despu��s de darle
        * al bot��n del men��
        */
       header("Location: modfiltro_envio.php");
       break;
   
   case "filtrar":
       if (strlen($titulo_envio) > 5 && strlen($notificacion) > 5 && $plataform != "0"){
           $sql = getParametro("sql_maestro");

           $_SESSION['sql_maestro'] = $sql;
           $_SESSION['plataforma'] = $plataform;
           $_SESSION['title'] = $titulo_envio;
           $_SESSION['notify'] = $notificacion;
           
           if (strpos($sql, "*") == false && strpos($sql, "email") == false){
               $pos = strpos($sql, "FROM");
               $sql = substr_replace($sql, " email ", $pos -1, 0);
           }
           
           $enlace = getParametro("campo_usuario");
 
           if (strpos($sql, "WHERE") != false){
               $pos = strpos($sql, "WHERE");
               $sql = substr_replace($sql, " INNER JOIN npp_dispositivos ON npp_dispositivos.dato_usuario = ". $enlace ." ", $pos -1, 0);
               
           }
           
           $combo_datos = $_SESSION["combos_datos"];
           
           $whereconcat .= make_sql($combo_datos['filtro']);
               
           $where .= $whereconcat;
           $sql .= $where;
           
           switch ($_SESSION['plataforma']) {
                case "IOS":
                    $sql .= " AND npp_dispositivos.id_plataforma = 1 ";
                    break;
                case "Android":
                    $sql .= " AND npp_dispositivos.id_plataforma = 2 ";
                    break;
                default:
                    break;
            }
            
           $_SESSION['sql_completo'] = $sql;
           $_SESSION['disabled'] = 'disabled';
           
           $_SESSION['mostrar'] = 'display:none';
           $_SESSION['ocultar'] = 'display:block';
       
           $mostrar = $_SESSION['mostrar'];
           $ocultar = $_SESSION['ocultar'];
           
           //ocultar filtro eliminar
           $page->addVar('enum_filtros', 'ocultar_eliminar', "oculto");
           
       }else{
           
           $_SESSION['plataforma'] = $plataform;
           $_SESSION['title'] = $titulo_envio;
           $_SESSION['notify'] = $notificacion;
           $page->addVar('error', 'error','Error,datos incorrectos.');
           
       }
       break;
   
   case "enviar":

       $sql_maestro= $_SESSION['sql_maestro'];
       $sql_completo = $_SESSION['sql_completo'];
       $title = $_SESSION['title'];
       $notify = $_SESSION['notify'];
       
       $sql_send = "INSERT INTO npp_envios(`titulo`, `notificacion`, `sql_maestro`, `sql_completo`, `fecha_creacion`) VALUES ('$title','$notify','$sql_maestro','$sql_completo',NOW())";
       query($sql_send);
       
       $id_envio = $UltimaInsercion;
       
       $res = query($_SESSION['sql_completo']);
       
       while ($row = Row($res)) {
           
           $id_dispositivo = $row['id_dispositivo'];
           $sql_notify = "INSERT INTO npp_notificaciones(`id_envio`, `id_dispositivo`, `fecha_creacion`, `fecha_envio`, `enviada`) VALUES ($id_envio,$id_dispositivo,NOW(),NOW(),0)";
           query($sql_notify);
           
       }
       header("Location: modfiltro_envio.php?modo=reset_data");
       break;
   
   case "editar":
       
       $_SESSION['disabled'] = '';
       $_SESSION['mostrar'] = 'display:block';
       $_SESSION['ocultar'] = 'display:none';
       
       $mostrar = $_SESSION['mostrar'];
       $ocultar = $_SESSION['ocultar'];
       
       //mostrar filtro eliminar
       $page->addVar('enum_filtros', 'ocultar_eliminar', "img_prov");
           
       break;
   
   case "atras":
     
        $_SESSION['page'] = 0;
       break;   
   
   case "catras":
 
       if ($_SESSION['page'] - 1 > -1) {
           $_SESSION['page'] -= 1;
       }
       
       
       break;
   
   case "cadelante":
   
       $tab = $_SESSION['maximo'];
       $tam_pagina=$_SESSION['tam_tabla'];
       if ($_SESSION['page'] + 1 < ($tab / $tam_pagina)) {
           $_SESSION['page'] += 1;
       }
       break;
   
   case "adelante":
     
       $tab = $_SESSION['maximo'] - 1;
       $tam_pagina=$_SESSION['tam_tabla'];
       $_SESSION['page'] = (int) ($tab /$tam_pagina);
       
       break;
   
   case "anadir":
       /*
        * Se recogen los valores de cada parametro
        * parametro2 (=,like,<,>) hay que ponerlo en letra para que el usuario
        * lo entienda mejor.
        */
       $parametro1 = $_POST['plataforma1'];
       $parametro2 = $_POST['plataforma2'];
       //Comparamos el campo y cambiamos el valor para poder utilizar el 'valor'
       if($parametro2=='Es igual a'){
           $parametro2='=';
       }elseif($parametro2=='Contiene'){
            $parametro2='like';
       }elseif($parametro2=='Mayor que'){
            $parametro2='>';
       }elseif($parametro2=='Menor que'){
            $parametro2='<';
       }
       
       $parametro3 = $_POST['plataforma3'];
       
       $combo_datos = $_SESSION["combos_datos"];
       
       if ($parametro1 != "" && $parametro2 != "" && $parametro3 != ""){
           if($parametro2=='like'){
               $parametro3='%'.$parametro3."%";
           }
           $filtro = $parametro1 . " " . $parametro2 . " " .$parametro3."";
           
           $combo_datos["filtro"][] = $filtro;    
       }
          
       $_SESSION["combos_datos"] = $combo_datos;
       
       break;
       
  case "cambiar_record":
      $_SESSION['page']=0;
       if ($_POST['resultado'] != ""){
           switch ($_POST['resultado']) {
               case 25:
                   $_SESSION['tam_tabla'] = 25;
                   $_SESSION['option'] = "one";
                   break;
               case 50:
                     $_SESSION['tam_tabla'] = 50;
                   $_SESSION['option'] = "two";
                   break;
               case 75:
                     $_SESSION['tam_tabla'] = 75;
                   $_SESSION['option'] = "three";
                   break;
               case 100:
                   $_SESSION['tam_tabla'] = 100;
                   $_SESSION['option'] = "four";
                   break;                
               default:
                   break;
           }
       }
       break;    
   
       
   case "eliminar":
       $combo_datos = $_SESSION["combos_datos"];

               for ($index1 = 0; $index1 < count($combo_datos["filtro"]); $index1++) {
                   if ($_POST['idfiltro'] == $combo_datos["filtro"][$index1]){
                       unset($combo_datos["filtro"][$index1]);
                       unset($combo_datos["linea"][$index1]);
                       break;
                   }
               }

               $combo_datos["filtro"] = array_values($combo_datos["filtro"]);
               $_SESSION["combos_datos"] = $combo_datos;
               
       break;
   default:
       break;
}

//fin switch

//Creacion del Select----------------------------------------
$list = array();

$sql = "SELECT * FROM npp_campos_filtro ORDER BY etiqueta ASC";
$res = query($sql);

while ($row = Row($res)) {

   $list[] = array(
       "etiqueta" => $row['etiqueta'],
       "campo" => $row['campo']);
}

//Control del Record set ------------------------------------

  if ($_SESSION['mostrar'] == 'display:none'){
      $sqlcompleto=$_SESSION ['sql_completo'];
       $resconsulta = query($sqlcompleto);
       $num = mysql_numrows($resconsulta);
       if ($_SESSION['tam_tabla'] == "") {
       $_SESSION['tam_tabla'] = 100;
       }
       $_SESSION['maximo'] = $num;
       $tab = $_SESSION['maximo'];
       $tam_pagina = $_SESSION['tam_tabla'];
               
       $min = $tam_pagina * $_SESSION['page'];
       $max = $tam_pagina * ($_SESSION['page'] + 1);
       if($max>$tab && $_SESSION['page']==0){
           $max=$tab;
       }
       if($max>$tab){
           $max=$tab;
       }

       $record_view = make_record($min, $tam_pagina);
   }
//Creacion filtro plataforma -------------------------------------
$plataforma = array();

$sql = "SELECT plataforma FROM npp_plataformas";
$res = query($sql);


   if ("Todas" == $_SESSION['plataforma']) {
       $selected = 'selected';
   } else {
       $selected = "";
   }
        $plataforma[] = array("plataforma" => "Todas",
           "selected" => $selected);

while ($row = Row($res)) {
   
   if ($row['plataforma'] == $_SESSION['plataforma']) {
       $selected = 'selected';
   } else {
       $selected = "";
   }
       $plataforma[] = array("plataforma" => $row['plataforma'],
           "selected" => $selected);
}

//Control de botones de paginacion ---------------------------------
$tab = $_SESSION['maximo'];
$tam_pagina=$_SESSION['tam_tabla'];

if ($_SESSION['page'] > 0 && $_SESSION['page'] < (int)($tab/$tam_pagina)){
    $page->addVar('paginacion', 'inicio', "mostrar");
    $page->addVar('paginacion', 'anterior', "mostrar");
    $page->addVar('paginacion', 'siguiente', "mostrar");
    $page->addVar('paginacion', 'final', "mostrar");
    
}elseif ($_SESSION['page'] == (int)($tab/$tam_pagina)){
    $page->addVar('paginacion', 'inicio', "mostrar");
    $page->addVar('paginacion', 'anterior', "mostrar");
    $page->addVar('paginacion', 'final', "ocultar");
    $page->addVar('paginacion', 'siguiente', "ocultar");
    
}elseif ($_SESSION['page'] == 0){
    $page->addVar('paginacion', 'inicio', "ocultar");
    $page->addVar('paginacion', 'anterior', "ocultar");
    $page->addVar('paginacion', 'siguiente', "mostrar");
    $page->addVar('paginacion', 'final', "mostrar");
    
}elseif ($_SESSION['page'] == 0 && $_SESSION['page'] == (int)($tab/$tam_pagina)){
    $page->addVar('paginacion', 'inicio', "ocultar");
    $page->addVar('paginacion', 'anterior', "ocultar");
    $page->addVar('paginacion', 'siguiente', "ocultar");
    $page->addVar('paginacion', 'final', "ocultar");
    
}


//Creacion comparadores -------------------------------------
$comparadores[] = array('comparadores' => "Es igual a");
$comparadores[] = array('comparadores' => "Contiene");
$comparadores[] = array('comparadores' => "Mayor que");
$comparadores[] = array('comparadores' => "Menor que");

//Load page -------------------------------------
$page->setAttribute('listado', 'src', 'modfiltro_envio.html');

$disabled = $_SESSION['disabled'];
$page->addVar('read', 'editar', $disabled);
$page->addVar('disabled', 'disabled', $disabled);

$page->addVar('select_option', $_SESSION['option'], 'selected');
$page->addVar('buttons', 'mostrar', $mostrar);
$page->addVar('buttons', 'ocultar', $ocultar);
$page->addVar('count', 'min', "$min - $max de $tab");
$page->addVar('read', 'titulo', $_SESSION['title']);
$page->addVar('read', 'notificacion', $_SESSION['notify']);
$page->addRows('plataformas', $plataforma);
$page->addRows('comparadores', $comparadores);
$page->addRows('filtro', $list);

if (count($record_view) > 0){
    $page->addVar('tabla', 'mostrar_tabla', 'display:block');
}else{
    $page->addVar('tabla', 'mostrar_tabla', 'display:none');
}

$page->addRows('record_set', $record_view);

$combo_datos = $_SESSION["combos_datos"];

$cuenta = count($combo_datos['filtro']);



if ($cuenta == 0){
   $page->addVar('enum_filtros', 'mostrar_filtro', 'display:none');
   
}else{
   for ($index = 0; $index < count($combo_datos['filtro']); $index++) {
       $linea = ($index%2)?"fila_02":"fila_01";
       $datos_filtro[] = array(
           "filtro" => $combo_datos['filtro'][$index],
           "nombre_form" => 'nombre'.$index,
           "linea" => $linea);
   }
    
   $page->addVar('enum_filtros', 'nombre', 'display:inline');
   $page->addRows('enum_filtros', $datos_filtro);
}
$page->Volcar();


//Funciones ---------------------------------------

function make_sql($array){
   $cuenta = count($array);
 
           if ($cuenta != 0){
               
               for ($index = 0; $index < count($array); $index++) {
                       $whereconcat .= " AND ". $array[$index] ." ";
               }
           }
    
    return $whereconcat;
}

function make_record($minimo, $tamaño){
    
        $sql_completo = $_SESSION['sql_completo'];
        $notificacion = $_SESSION['notify'];
        
        $res_count = query($sql_completo);
        $num = mysql_numrows($res_count);
        $_SESSION['maximo'] = $num;
        
        $sql_parametro = $sql_completo . " LIMIT $minimo,$tamaño";    
        $res = query($sql_parametro);

            if ($num != 0){
            
                $records[] = array();
                while ($row = Row($res)) {
                    ++$i;

                    $linea = ($i%2)?"fila_02":"fila_01";
                    
                    $dato_escapado = "";
                    
                    if ($i == 1){
                        foreach ($row as $key => $value) {
                            $dato_escapado .= "<td>". html($key) . "</td>";
                        }
                        $dato_escapado .= "<td> Notificación </td>";
                        
                        $records['html'] = $dato_escapado;
                        $records['linea'] = $linea;
                    
                        $record_view[] = $records;
                        
                        $dato_escapado = "";
                        
                        foreach ($row as $key => $value) {
                            $dato_escapado .= "<td>". html($value) . "</td>";
                        }
                        $dato_escapado .= "<td>" . html($notificacion) . "</td>";
                        
                    }else{
                        foreach ($row as $key => $value) {
                            $dato_escapado .= "<td>". html($value) . "</td>";
                        }
                        $dato_escapado .= "<td>" . html($notificacion) . "</td>";
                    }

                    $records['html'] = $dato_escapado;
                    $records['linea'] = $linea;
                    
                    $record_view[] = $records;
                   
                }
                
            }
        
        return $record_view;
}
