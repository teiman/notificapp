<?php

//NOTA IMPORTANTE:
//
// Por un tema de optimizacion, se utiliza la fecha de este fichero para decidir el cache
// tras modificar un css, hay que hacer touch de este fichero para que parezca mas moderno y realmente
// envie los cambios. Sino no tiene forma de saber si hay datos nuevos

include("../config/config.php");


date_default_timezone_set ("Europe/Berlin");

define('TIME_BROWSER_CACHE','36000');
$last_modified = filemtime(__FILE__);

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) AND
	strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified) {
  header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified',TRUE,304);
  header('Pragma: public');
  header('x-macrojs: yes');
  header('Last-Modified: '.gmdate('D, d M Y H:i:s',$last_modified).' GMT');
  header('Cache-Control: max-age='.TIME_BROWSER_CACHE.', must-revalidate,public');
  header('Expires: '.gmdate('D, d M Y H:i:s',time() + TIME_BROWSER_CACHE).'GMT');
  // That's all, now close the connection:
  header('Connection: close');
  die();
}


if(!ob_start("ob_gzhandler")) ob_start();

header('Content-type: text/css');

function agnadirFichero($fichero){
    global $output,$base_estaticos;

    
    if(file_exists($fichero)) {
    
        $sText = @file_get_contents($fichero);
    
        if (strlen($sText)>3){                                
            $tiempoUltimo = filemtime($fichero);           

            if ( $tiempoUltimo > $output["ultimotiempo"]){
                $output["ultimotiempo"] = $tiempoUltimo;
            }

            $sText = str_replace("background-image: url(../","background-image: url($base_estaticos",$sText);
            $sText = str_replace("background-image: url(\"../","background-image: url(\"$base_estaticos",$sText);
            $sText = str_replace("background: url(\"../","background: url(\"$base_estaticos",$sText);
            $sText = str_replace("background: url(../","background: url($base_estaticos",$sText);
            


            echo "/* " . $fichero . " */\n";
            $output["css"] = $output["css"] . "\n" .$sText;  
        }
    } else {

       echo "/* no encuentra:" . $fichero . " */\n";
    }

}


echo "/* modified:$last_modified  */\n";



$output = array("css"=>"","ultimotiempo"=>0);

$modulo = isset($_REQUEST["modo"])?$_REQUEST["modo"] . ".css":false;

agnadirFichero("main.css");

if($modulo) {
    agnadirFichero("pages/" . $modulo);
}   

/*
    Termina y sale
                    */

$output_css = $output["css"];

$regex = array(
"`^([\t\s]+)`ism"=>'',
"`^\/\*(.+?)\*\/`ism"=>"",
"`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
"`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
);

$output_css  = preg_replace(array_keys($regex),$regex,$output_css );

//header("Last-Modified: ".date("D, d M Y H:i:s T", $output["ultimotiempo"] ));

header('Cache-Control: max-age='.TIME_BROWSER_CACHE.', must-revalidate,public');
header('Expires: '.gmdate('D, d M Y H:i:s',time() + TIME_BROWSER_CACHE).'GMT');
header("Last-Modified: ".date("D, d M Y H:i:s T", $last_modified ));
header("ETag: '".md5($output["css"])."'");



echo $output_css;

ob_flush();

