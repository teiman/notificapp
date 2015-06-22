<?php


function tidyHTML5($buffer)
{
    $buffer = str_replace('<menu', '<mytag', $buffer);
    $buffer = str_replace('menu>', 'mytag>', $buffer);
    $tidy = new tidy();
    $options = array(
            'hide-comments'         => true,
            'tidy-mark'             => false,
            'indent'                => true,
            'indent-spaces'         => 4,
            'new-blocklevel-tags'   => 'menu,mytag,article,header,footer,section,nav',
            'new-inline-tags'       => 'video,audio,canvas,ruby,rt,rp',
            'doctype'               => '<!DOCTYPE HTML>',
            //'sort-attributes'     => 'alpha',
            'vertical-space'        => false,
            'output-xhtml'          => false,
            'wrap'                  => 180,
            'wrap-attributes'       => false,
            'break-before-br'       => false,
            'char-encoding'         => 'utf8',
            'input-encoding'        => 'utf8',
            'output-encoding'       => 'utf8',
    'tidy-mark' => false,
    'vertical-space' => false,
     'doctype' => false,
    'hide-comments' => true,
    'indent-spaces' => 0,
    'tab-size' => 1,
    'wrap-attributes' => false,
    'numeric-entities' => true,
    'ascii-chars' => true,
    'hide-endtags' => true,
    'indent' => false	    
    );

    $tidy->parseString($buffer, $options, 'utf8');
    $tidy->cleanRepair();

    $html = $tidy->html();
    $html = str_replace('<html lang="en" xmlns="http://www.w3.org/1999/xhtml">', '', $html);
    $html = str_replace('<html xmlns="http://www.w3.org/1999/xhtml">', '', $html);
    $html = str_replace('<!DOCTYPE HTML>', '', $html);
    $html = str_replace('<title></title>', '', $html);

    return $html;
}





function generar_token() {
    $diathing = date("Ymd");

    $md5cosa = md5($diathing . "_SALTINBANQUI_");

    return $md5cosa;
}

function token_es_valido($token) {
    $token_hoy = generar_token();

    return $token == $token_hoy;
}

function usuario_ok($usuario, $contrasegna) {
    /*
      $sql = "SELECT id_user FROM tienda WHERE"
      . " (user_email ='$usuario_s') and (user_pass='$md5_pass') ";

      $row= queryrow($sql);

      return $row["id_user"]>0; */

    if ($usuario == "remoto" and
            $contrasegna == "mark44") {
        return true;
    }

    return false;
}

function get_lista_tiendas_modificadas() {

    //Actualiza la bandera basandose en modificaciones de modificadores
    query("UPDATE tienda SET modificada=1 WHERE id_tienda IN (SELECT id_usuario FROM modificadores  WHERE modificado=1 GROUP BY id_usuario)");
    
    
    $sql = "SELECT cod_magento FROM tienda WHERE modificada=1 AND borrada=0";

    $res = query($sql);

    $tiendas = array();

    while ($row = Row($res)) {
        array_push($tiendas, $row["cod_magento"]);
    }

    return $tiendas;
}

function get_id_nodo_cod($cod) {
    $cod_s = sql($cod);

    if (!$cod or $cod == "##PADRE##")
        return 0;       

    $sql = "SELECT id_nodo FROM arbol WHERE cod='$cod_s' ";
    $row = queryrow($sql);

    return $row["id_nodo"];
}

function get_id_usuario_tienda($tienda) {
    $tienda_s = sql($tienda);
    $sql = "SELECT id_tienda FROM tienda WHERE cod_magento='$tienda_s' AND borrada=0 ";

    $row = queryrow($sql);

    return $row["id_tienda"];
}

function get_listado_deltacategorias($id_usuario) {
    $num = 0;

    $sql = "SELECT  
            base_arbol.cod as cod, 
            base_arbol.descripcion, 
            padre_arbol.descripcion as desc_padre,
            abuelo_arbol.descripcion as desc_abuelo,
            tataraabuelo_arbol.descripcion as desc_tataraabuelo,
            tatataraabuelo_arbol.descripcion as desc_tatataraabuelo,
            tatatataraabuelo_arbol.descripcion as desc_tatatataraabuelo,
            padre_arbol.cod as padrecod, 
            abuelo_arbol.cod as abuelocod,
            tataraabuelo_arbol.cod as tataraabuelocod,
            tatataraabuelo_arbol.cod as tatataraabuelocod,
            tatatataraabuelo_arbol.cod as tatatataraabuelocod
            FROM arbol as base_arbol 
            LEFT JOIN arbol as padre_arbol ON base_arbol.id_padre = padre_arbol.id_nodo   
            LEFT JOIN arbol as abuelo_arbol ON padre_arbol.id_padre = abuelo_arbol.id_nodo  
            LEFT JOIN arbol as tataraabuelo_arbol ON abuelo_arbol.id_padre = tataraabuelo_arbol.id_nodo
            LEFT JOIN arbol as tatataraabuelo_arbol ON  tataraabuelo_arbol.id_padre = tatataraabuelo_arbol.id_nodo
            LEFT JOIN arbol as tatatataraabuelo_arbol ON  tatataraabuelo_arbol.id_padre = tatatataraabuelo_arbol.id_nodo
        WHERE 
        base_arbol.modificado=1 
        ORDER BY base_arbol.id_padre ASC";

    $salida = array();

    $res = query($sql);
    while ($row = Row($res)) {
        $new = array();

        $path = str_replace("/","\/",$row["descripcion"]);

        if ($row["desc_padre"])
            $path = str_replace("/","\/",$row["desc_padre"]) . "/" . $path;

        if ($row["desc_abuelo"])
            $path = str_replace("/","\/",$row["desc_abuelo"]) . "/" . $path;

        if ($row["desc_tataraabuelo"])
            $path = str_replace("/","\/",$row["desc_tataraabuelo"]) . "/" . $path;

        if ($row["desc_tatataraabuelo"])
            $path = str_replace("/","\/",$row["desc_tatataraabuelo"]) . "/" . $path;

        if ($row["desc_tatatataraabuelo"])
            $path = str_replace("/","\/",$row["desc_tatatataraabuelo"]) . "/" . $path;


        $new["cod"] = $row["cod"];                
        $new["codpadre"] = $row["padrecod"];                
        $new["path"] = $path;
        $new["name"] = $row["descripcion"];
        
        if(!$new["codpadre"])
            $new["codpadre"] = "##PADRE##";        

        $salida[$num] = $new;
        $num++;
    }


    return $salida;
}

function comenta_barra($str){
    //$str = str_replace("/","\/",$str);
    
    //$str = '"'.$str.'"';
    
    return $str;    
}



function get_path($cod) {
    static $code2path = array();

    if (isset($code2path[$cod])) {
        return $code2path[$cod];
    }

    $id_nodo = get_id_nodo_cod($cod);
    $path = get_pathid($id_nodo);

    $code2path[$cod] = $path;

    return $path;
}


function get_pathid($id) {
    static $code2path = array();

    if (isset($code2path[$id])) {
        return $code2path[$id];
    }

    $id = sql($id);

    $sql = " SELECT base_arbol.cod AS cod, base_arbol.descripcion, padre_arbol.descripcion AS desc_padre, abuelo_arbol.descripcion AS desc_abuelo,
  tataraabuelo_arbol.descripcion AS desc_tataraabuelo, tatataraabuelo_arbol.descripcion AS desc_tatataraabuelo, tatataraabuelo_arbol.descripcion AS desc_tatatatataraabuelo,
   padre_arbol.cod AS padrecod, abuelo_arbol.cod AS abuelocod, tataraabuelo_arbol.cod AS tataraabuelocod, tatataraabuelo_arbol.cod AS tatataraabuelocod,
   tatatataraabuelo_arbol.cod AS tatatataraabuelocod
            FROM arbol AS base_arbol
            LEFT JOIN arbol AS padre_arbol ON base_arbol.id_padre = padre_arbol.id_nodo
            LEFT JOIN arbol AS abuelo_arbol ON padre_arbol.id_padre = abuelo_arbol.id_nodo
            LEFT JOIN arbol AS tataraabuelo_arbol ON abuelo_arbol.id_padre = tataraabuelo_arbol.id_nodo
            LEFT JOIN arbol AS tatataraabuelo_arbol ON tataraabuelo_arbol.id_padre = tatataraabuelo_arbol.id_nodo
            LEFT JOIN arbol AS tatatataraabuelo_arbol ON tatataraabuelo_arbol.id_padre = tatatataraabuelo_arbol.id_nodo
        WHERE
        base_arbol.id_nodo = '$id'
        LIMIT 1";

    $row = queryrow($sql);

    $sep = "##CAT##";

    $path = comenta_barra($row["descripcion"]);

    if ($row["desc_padre"])
        $path = comenta_barra($row["desc_padre"]) . $sep . ($path);

    if ($row["desc_abuelo"])
        $path = comenta_barra($row["desc_abuelo"]) . $sep . ($path);

    if ($row["desc_tataraabuelo"])
        $path = comenta_barra($row["desc_tataraabuelo"]) . $sep . ($path);

    if ($row["desc_tatataraabuelo"])
        $path = comenta_barra($row["desc_tatataraabuelo"]) . $sep . ($path);

    if ($row["desc_tatatataraabuelo"])
        $path = comenta_barra($row["desc_tatatataraabuelo"]) . $sep . ($path);

    $code2path[$id] = $path;

    return $path;
}


function get_listado_deltaproductos($id_usuario) {


    $sql = "SELECT
    DISTINCT arbol.id_nodo as id_nodo
    FROM productos
    JOIN arbol ON productos.id_categoria = arbol.id_nodo
    LEFT JOIN modificadores
    ON (productos.id_categoria = modificadores.id_nodo and modificadores.id_usuario='$id_usuario')
    WHERE   modificadores.modificado is NULL";

    $row = queryrow("SELECT * FROM parametros WHERE id_tienda='$id_usuario' ");
    $base_porcentaje = $row["porcentaje_defecto"];

    $res = query($sql);
    while($row = Row($res)){
        $id_nodo = $row["id_nodo"];
        query("INSERT modificadores(porcentaje,id_usuario,id_nodo,modificado ) VALUES ('$base_porcentaje','$id_usuario','$id_nodo',1)");
    }


    $sql = "SELECT precio, nombre,ficha_html ,modificadores.modificado,
    productos.stock_modificado,
    productos.precio_modificado,
    modificadores.id_usuario,
    codigo,stock,
    modificadores.id_modificador as idm, 
    modificadores.porcentaje,
    productos.thumbnail,
    productos.image,
    productos.requiere_borrado as requiere_borrado,
    arbol.id_nodo as id_nodo,
    arbol.cod as codcat,
    productos.marca as marca
    FROM productos 
    JOIN arbol ON productos.id_categoria = arbol.id_nodo
    LEFT JOIN modificadores ON (productos.id_categoria = modificadores.id_nodo and modificadores.id_usuario=$id_usuario)
    WHERE   modificadores.modificado is NULL or modificadores.modificado=1 or productos.stock_modificado=1 or
    productos.precio_modificado=1 or arbol.modificado=1 ";

    $num = 0;
    $salida = array();

    $res = query($sql);
    while ($row = Row($res)) {
        $new = array();

        $new["cod"] = $row["codigo"];

      //  $new["codcat"] = $row["codcat"]; //NOTA: nunca va a ser ##PADRE##
        $new["stock"] = $row["stock"];
        $new["stock_modificado"] = $row["stock_modificado"];

        $new["marca"] = $row["marca"];

        $new["nombre"] = $row["nombre"];
        $new["thumbnail"] = $row["thumbnail"];
        $new["image"] = $row["image"];

        $new["path"] = get_pathid($row["id_nodo"]);

        $new["precio_original"] = $row["precio"];
        $new["precio_modificado"]  = $row["precio_modificado"];
        
        $precio = $row["precio"];

        if ($row["idm"]) {
            $porcentaje = $row["porcentaje"];
        } else {
            $porcentaje = $base_porcentaje;
        }

        $precio = $precio + ($precio * $porcentaje) / 100 ;
        $new["precio"] = $precio;
        $new["ficha_html"] = $row["ficha_html"];

        if($row["requiere_borrado"]){
            $new["eliminar"] = true;
        }

        $salida[$num] = $new;
        $num++;
    }


    return $salida;
}



function get_listado_deltaproductos_simple($id_usuario) {

    $sql = "SELECT precio, nombre,ficha_html ,modificadores.modificado,
    productos.stock_modificado,
    productos.precio_modificado,
    modificadores.id_usuario,
    codigo,stock,
    modificadores.id_modificador as idm,
    modificadores.porcentaje,
    productos.requiere_borrado as requiere_borrado,
    arbol.id_nodo as id_nodo,
    arbol.cod as codcat
    FROM productos
    JOIN arbol ON productos.id_categoria = arbol.id_nodo
    LEFT JOIN modificadores ON (productos.id_categoria = modificadores.id_nodo and modificadores.id_usuario=$id_usuario)
    WHERE   modificadores.modificado is NULL or modificadores.modificado=1";

    $num = 0;

    $row = queryrow("SELECT * FROM parametros WHERE id_tienda='$id_usuario' ");
    $base_porcentaje = $row["porcentaje_defecto"];

    $salida = array();

    $res = query($sql);
    while ($row = Row($res)) {
        $new = array();

        $new["cod"] = $row["codigo"];

        //  $new["codcat"] = $row["codcat"]; //NOTA: nunca va a ser ##PADRE##
        $new["stock"] = $row["stock"];
        $new["precio_original"] = $row["precio"];
        $precio = $row["precio"];

        if ($row["idm"]) {
            $porcentaje = $new["porcentaje"];
        } else {
            $porcentaje = $base_porcentaje;
        }

        $precio = $precio + ($precio * $porcentaje) / 100 ;
        $new["precio"] = $precio;

        if($row["requiere_borrado"]){
            $new["eliminar"] = true;
        }

        $salida[$num] = $new;
        $num++;
    }


    return $salida;
}



function get_listado_categorias_con_porcentaje($id_usuario) {

    $row = queryrow("SELECT * FROM parametros WHERE id_tienda='$id_usuario' ");
    $base_porcentaje = $row["porcentaje_defecto"];
    $salida = array();

    $sql = "SELECT modificadores.id_modificador as idm, modificadores.porcentaje as porcentaje, modificadores.modificado as modificado, base_arbol.cod as cod, padre_arbol.cod as padrecod FROM arbol as base_arbol LEFT JOIN modificadores "
            . " ON base_arbol.id_nodo = modificadores.id_nodo  "
            . " LEFT JOIN arbol as padre_arbol ON base_arbol.id_padre = padre_arbol.id_nodo "
            . " where modificadores.id_usuario='$id_usuario' "
            . "ORDER BY base_arbol.id_padre ASC";

    $res = query($sql);

    $num = 0;
    while ($row = Row($res)) {
        $new = array();

        $new["cod"] = $row["cod"];
        $new["padre"] = $row["padrecod"];
        $new["modificado"] = $row["modificado"];

        if ($row["idm"]) {
            $new["porcentaje"] = $row["porcentaje"];
        } else {
            $new["porcentaje"] = $base_porcentaje;
        }

        $salida[$num] = $new;
        $num++;
    }

    return $salida;
}

function get_listado_categorias_simple() {

    $salida = array();

    $sql = "SELECT  base_arbol.cod as cod, padre_arbol.cod as padrecod FROM arbol as base_arbol LEFT JOIN modificadores "
            . " ON base_arbol.id_nodo = modificadores.id_nodo  "
            . " LEFT JOIN arbol as padre_arbol ON base_arbol.id_padre = padre_arbol.id_nodo "
            . "ORDER BY base_arbol.id_padre ASC";

    $res = query($sql);

    $num = 0;
    while ($row = Row($res)) {
        $new = array();

        $new["cod"] = $row["cod"];
        $new["padre"] = $row["padrecod"];

        $salida[$num] = $new;
        $num++;
    }

    return $salida;
}

