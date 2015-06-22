<?php

/**
 * Ayudas para buscar errores
 *
 * @package ecomm-aux
 */




function error_log2($texto){
    global $serial_registra;
    $txt = date("Y-m-d H:i:s") . " ". $serial_registra ." " . $texto ;
    $path = "/var/www/registros/traza.log";


    if (!($fp = fopen($path, 'a'))) {
      // die('Cannot open log file');
    }



    fwrite($fp, $txt. "\n");
    fclose($fp);

    //die("pudo abrir2");
}



$erroresPagina = array();

function AddBugToSession($bug){
	$oldError = $_SESSION["Errores_Session"];
	
	if (!$oldError)
		$oldError = array();
	$oldError[] = $bug;
	$_SESSION["Errores_Session"] = $oldError; 	
}



function error($donde,$texto=false){
	global $erroresPagina,$debug_mode;
	
	$donde = str_replace("\n"," ",$donde);
	$texto = str_replace("\n"," ",$texto);
	
	$strbug = $donde . ": ". $texto;
		
	//if($debug_mode)
	//	AddBugToSession($strbug);
	
	error_log( $strbug, 0);

        if(function_exists("debug")){
            debug($texto,$donde);
        }
}




function debug_imprimeSesion(){
    echo "<table border=1>";
    foreach($_SESSION as $key=>$dato){

        $dato = var_export($dato,true);

        echo "<tr><td>".html($key)."</td><td>".html($dato)."</td></tr>\n";

    }
    echo "</table>";
}

function debug_imprimePost(){
    echo "<table border=1>";
    foreach($_POST as $key=>$dato){

        echo "<tr><td>".html($key)."</td><td>".html(var_export($dato,true)) ."</td></tr>\n";

    }
    echo "</table>";
}


if (0){
			
	function AddErrorHandler($errno,$errstr){

                if($errno==8) return;//Not spam
                if($errno==8192) return;//No deprecated

                $dataerror= error_get_last();

                $donde = $dataerror["file"]."(".$dataerror["line"].")($errno)";
                $donde = $errno;
                
		error($donde,$errstr);
	}

        
    error_reporting(E_ALL & ~E_NOTICE );
    //error_reporting(E_USER_ERROR | E_RECOVERABLE_ERROR | E_USER_WARNING| E_USER_NOTICE);
    //error_reporting(E_USER_ERROR);
    set_error_handler("AddErrorHandler");
} else {
    //error_reporting(E_ALL & ~E_NOTICE );
    error_reporting(E_ALL - E_NOTICE );
    //error_reporting(E_USER_ERROR | E_RECOVERABLE_ERROR | E_USER_WARNING| E_USER_NOTICE);

    ini_set("display_errors","Off");

}


