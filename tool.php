<?php


$lang = "es";

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

define('__ROOT__', (dirname(__FILE__)));




if(1){
    //modo debugging
    require_once("inc/PhpConsole/__autoload.php");

    $connector = PhpConsole\Connector::getInstance();
    $connector = PhpConsole\Helper::register();
    $connector->setSourcesBasePath(__ROOT__);

    function d2($var, $tags = null) {
        PhpConsole\Connector::getInstance()->getDebugDispatcher()->dispatchDebug($var, $tags, 1);
    }

    //$connector->setPassword('amoros', true);

}   else {
    //modo produccion
    function d2(){
        //nothing
    }
}

function d3($dato){
    error_log("d3:dato:".var_export($dato,true));

    echo "<pre>";
    echo var_export($dato);
    echo "</pre>";
    die();
}


d2($_REQUEST,"_REQUEST");



//define('CAPTURARTODOBUG', "Esta activo");
//die("version:". PHP_VERSION_ID);50204

if( 0 ){
	ini_set("session.gc_maxlifetime",    "86400");
	ini_alter("session.cookie_lifetime", "86400" );

	$expire = 60*60*23;
	ini_set("session.gc_maxlifetime", $expire);

	if (empty($_COOKIE['PHPSESSID'])) {
		session_set_cookie_params($expire);
		session_start();
	} else {
		session_start();
		setcookie("PHPSESSID", session_id(), time() + $expire);
	}
} else {
        //Si no hay sesion, la creamos.

        if (!defined("NO_SESSION")) {
                if (session_id() == "") {
                    //session_set_cookie_params(0, '/ev/');//no expira nunca, o hasta que cierran sesion
                    session_start();
                }
        }
}

$modo = (isset($_REQUEST["modo"])?$_REQUEST["modo"]:false);



function valido8($data) {
    $valid_utf8 = (@iconv('UTF-8', 'UTF-8', $data) === $data);

    if (!$valid_utf8){
        $data = utf8_encode($data);
    }

    return $data;
}

date_default_timezone_set("Europe/Berlin");


if(function_exists("get_magic_quotes_gpc")){
	if (get_magic_quotes_gpc()) {
		function stripslashes_profundo($valor)    {
			$valor = is_array($valor) ?
						array_map('stripslashes_profundo', $valor) :
						stripslashes($valor);
			return $valor;
		}

		$_POST = array_map('stripslashes_profundo', $_POST);
		$_GET = array_map('stripslashes_profundo', $_GET);
		$_COOKIE = array_map('stripslashes_profundo', $_COOKIE);
		$_REQUEST = array_map('stripslashes_profundo', $_REQUEST);
	}
}


if(!function_exists("_")){
	function _($text){
		return $text;
	}
}

if(!function_exists("_split")){
    if(function_exists("mb_split")){
        function _split($a,$b){
            return mb_split($a,$b);
        }
    } else {
        function _split($a,$b){
            return split($a,$b);
        }
    }
}




$SEPARADOR = DIRECTORY_SEPARATOR;


include_once("config/config.php");
include_once("inc/debug.inc.php");
include_once("inc/clean.inc.php");

if(1){
	include_once("inc/db.inc.php");

	function mysqlescape($str){
		forceconnect();
		return mysql_real_escape_string($str);
	}
} else {

        //dbi es db con algunas mejora, pero requiere un uso mucho mas detallado 
	include_once("inc/dbi.inc.php");

	//NOTE: you can't freely use  mysql_real escape, because it needs a link of his type
	// so we encapsulate  escape into a function, using the mysqli version
	function mysqlescape($str){
		global $link;
		forceconnect();
		return mysqli_real_escape_string($link, $str);
	}
	
}

include_once("inc/html.inc.php");
include_once("inc/supersesion.inc.php");
include_once("inc/combos.inc.php");
include_once("inc/auth.inc.php");
include_once("class/json.class.php");//comunicacion

include_once("class/cursor.class.php");
include_once("class/config.class.php");


include_once("class/patError.php");
include_once("class/patErrorManager.php");
include_once("class/patTemplate.php");
include_once("class/pagina.class.php");


if(!isset($_SESSION["base_estaticos"])){
    $_SESSION["base_estaticos"] = $base_estaticos;
}

$_SESSION["base_estaticos"] = $base_estaticos;

$script = basename($_SERVER['SCRIPT_NAME']);
$script = substr($script, 0, -4);

$template = array();
$template["modname"] = $script;

if(defined("CAPTURARTODOBUG")){
    function myErrorHandler_Tool($errno, $errstr, $errfile, $errline) {
        global $corriendoGeneral;

        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }

        switch ($errno) {
            case E_USER_ERROR:
                echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
                echo "  Fatal error on line $errline in file $errfile";
                echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
                echo "Aborting...(limpiando procs)<br />\n";

                exit(1);
                break;

            case E_USER_WARNING:
                echo "<b>WARNING</b> [$errno] $errstr<br />\n";
                break;

            case E_USER_NOTICE:
                echo "<b>NOTICE</b> [$errno] $errstr<br />\n";
                break;

            default:
                echo "Unknown error type: [$errno] $errstr<br />\n";
                break;
        }

        /* Don't execute PHP internal error handler */
        return true;
    }


    // set to the user defined error handler
    $old_error_handler = set_error_handler("myErrorHandler_Tool");

    function handleShutdown_Tool() {
        global $corriendoGeneral,$selected_moduleº;
        $error = error_get_last();
        if($error !== NULL) {
            $info = "[SHUTDOWN] file:".$error['file']." | ln:".$error['line']." | msg:".$error['message'];
            echo $info . "<br>\n";
            echo "Aborting...(limpiando procs)<br />\n";
            //abortarRunGateway();
        }
    }

    register_shutdown_function('handleShutdown_Tool');
}

query("SET Names utf8");