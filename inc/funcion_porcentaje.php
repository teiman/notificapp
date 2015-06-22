<?php

/* function actualizaPrecio($id_nodo, $mod, $usuario) {

  creaOAjusta($id_nodo, $mod, $usuario);

  $sql = "SELECT id_nodo FROM arbol WHERE id_padre = '$id_nodo'";
  $res = query($sql);
  $hijos = array();
  while ($row = Row($res)) {
  $hijos[] = $row['id_nodo'];
  }
  foreach ($hijos as $id_nodo_hijo) {
  creaOAjusta($id_nodo_hijo, $mod, $usuario);

  actualizaprecio($id_nodo_hijo, $mod, $usuario);
  }
  }

  function creaOajusta($id_nodo, $mod, $usuario) {

  $sql = "INSERT INTO modificadores(id_nodo, porcentaje, id_usuario) VALUES('$id_nodo', '$mod', '$usuario')";
  query($sql);
  } */

function muestraArbol($id_padre, $id_usuario) {

    //vamos a buscar el valor por defecto y se lo endosamos a todos 
    // los que no tengan valor
    $sql = "SELECT porcentaje_defecto FROM parametros WHERE id_tienda = '$id_usuario' LIMIT 1";
    $row = queryrow($sql);
    if ($row){ 
      $porc = $row['porcentaje_defecto'];
    }  
    
    $sql = "SELECT * FROM arbol WHERE id_padre = '$id_padre'";
    $res = query($sql);

    while ($row = Row($res)) {

        $padre[] = $row;
    }
    if ($id_padre != 0) {
        $oculto = "ocu";
    }
    if ($padre) {
        $out .= "<ul class='$oculto' id='cat_" . $id_padre . "'>";
        foreach ($padre as $p) {

            $nodo = $p['id_nodo'];

            $sql = "SELECT count(*) as tienehijo FROM arbol WHERE id_padre = '$nodo'";
            $row = queryrow($sql);

            if ($row['tienehijo'] > 0) {
                $porcentaje = "";
                $input = "<input class='input padre' type='text' id='no_coger_" . $nodo . "' maxlength='3' size='3' />";
                $submit = "<input class='submit' title='El valor introducido se actualizará en todos los productos de la familia' value='Actualizar a Subfamilias' id='no_coger_" . $nodo . "' type='submit' onclick='return false;' />";
            } else {
                $submit = "";
                $sql = "SELECT porcentaje FROM modificadores WHERE id_nodo = '$nodo' AND id_usuario = '$id_usuario' LIMIT 1";
                $row = queryrow($sql);
                if ($row){ 
                  $porcentaje = $row['porcentaje'];
                }else{
                  $porcentaje = $porc; 
                }
                $input = "<input type='text' class='hijo' value='" . $porcentaje . "' maxlength='3' size='3' name='porcentaje_" . $nodo . "' />";
            }

            $out .= "<li>" . $input . "%<input type='hidden' name='id_nodo_" . $nodo . "' value='" . $nodo . "' />"
                    . "<span class='activable' data-id=#cat_" . $nodo . ">" . $p['descripcion'] . "</span>" . $submit;

            $out .= muestraArbol($nodo, $id_usuario);
            $out .= "</li>";
        }
        $out .= "</ul>";
    }
    return $out;
}