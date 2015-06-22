<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 31/10/13
 * Time: 17:27
 */


function importar_fichero($file,$importador,$codigo,$nombre_root){

    switch($importador){
        case "infortisa":
            $imp = new Importador_infortisa();
            break;
        case "intronics":
            $imp = new Importador_intronics();
            break;
        case "generico":
            $imp = new Importador_generico();
            break;
        default:
        case "defecto":
            lesay("Herrrr... no hay importador implementeado para '$importador' '","problema");
            return false;
    }

    if(!$imp) {
        return false;
    }

    $imp->debug = true;
    $imp->canal = $codigo;//canal_root en la base de datos
    $imp->nombre_root = $nombre_root;
    $imp->cod_root = md5($nombre_root);
 //   $imp->maximo_productos = 15;

    $exito = $imp->cargar_csv($file);

    if ($exito) {
        lesay("Carga con exito. Vamos a actualizar productos.");

        $imp->actualizar_productos();
    } else {
        lesay("Errores en la carga","problema");
    }

    $imp->actualizar_marcamodificados();


    lesay("resumen:");
    $imp->resumen();

    lesay("fin");

    return true;
}


function name2importador($name){
    list( $v0 ) = explode( '_', $name );

    $code_s = sql(strtolower(trim($v0)));

    $sql = "SELECT id_proveedor,importador,activo,canal_root,nombre_root FROM proveedores  WHERE codigo='$code_s' ";
    $row = queryrow($sql);

    return array(
        "importador"=>$row["importador"],
        "activo"=>$row["activo"],
        "canal_root"=>$row["canal_root"],
        "nombre_root"=>$row["nombre_root"]
    );
}

function getCSV_Existe($fichero){
    $path = __ROOT__ . "/entrada_datos/". $fichero;

    return file_exists($path);
}

function lesay($text,$class="info"){
    global $r;
    echo "<div class='$class'>".$r.": ". html($text) . "</div>";

    error_log( date("d-m-Y H:i:s").": ". $text . "\n", 3, "/var/www/logs/importador_".date("Ymd").".log");
}

