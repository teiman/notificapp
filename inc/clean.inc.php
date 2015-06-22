<?php

/**
 * Ayudas para sanitificar entradas, escapar variables
 *
 * @package ecomm-aux
 */


function CleanCOD($texto){
	$texto = str_replace(" ","",$texto);
	$texto = str_replace("COD","",$texto);
	return intval($texto)-1000;	
}


function CleanParaWeb($valor){
	return htmlentities($valor,ENT_QUOTES,'UTF-8');
}

function CleanXSS($valor){
	return strip_tags($valor);
}


function CleanFechaFromDB($fecha){
	if ($fecha == "0000-00-00")
		return "";

        if(!$fecha)
                return "";
        if($fecha=="null" or $fecha=="NULL")
                return "";


	list($agno,$mes,$dia) = explode("-",$fecha);
	return($dia . "-" . $mes . "-" . $agno);
}


function CleanDatetimeToFechaES($fecha){
	$datos = explode(" ",$fecha);
	
	return CleanFechaFromDB($datos[0]);	
}


function CleanDatetimeDBToDatetimeES($fecha){
	$datos = explode(" ",$fecha);

	return CleanFechaFromDB($datos[0]). " " . $datos[1];	
}



function CleanFechaES($fecha){
	if (!$fecha)
		return "";

	$fecha	= str_replace("/","-",$fecha);

	if ($fecha == "DD-MM-AAAA")
		return "";
        
	list($dia,$mes,$agno) = explode("-",$fecha);
	return($agno . "-".$mes."-".$dia);
}

function CleanCP($local){
	$local = CleanTo($local);
	$local = str_replace('"',"",$local);
	$local = trim($local);
	return strtoupper(trim(CleanTo($local))); 	
}




function Corta($str,$len,$padstr=".."){
	$reallen = strlen($str);
	$lenpad = strlen($padstr);

	if ($reallen+$lenpad <= $len)
		return $str;

	$newstr = substr($str, 0, $len-$lenpad) .$padstr;

	return $newstr;
}



function CleanInt($int){
	return intval($int,10);	
}

function CleanPass($pass){
	return CleanText($pass);	
}

function CleanLogin($login){
	return CleanText($login);	
}

function CleanXulLabel($label){
	return $label;	
}

function CleanCB($cb){
	$ref = str_replace("\t","",$cb);
	$ref = trim($cb);
	$ref = str_replace(" ","",$ref);
	return $ref;	
}

function CleanRef($ref){
	return CleanReferencia($ref);
}

function CleanReferencia($ref){
	$ref = trim($ref);
	$ref = str_replace(" ","",$ref);	
	$ref = strtoupper($ref);
	
	return $ref;	
}

function CleanDinero($val){
	return CleanFloat($val);	
}

//Heavy, quita metacaracteres y espacios. Util para palabras
function CleanTo($text,$to="")  {
	$text = str_replace("'",$to,$text);
	$text = str_replace("\\",$to,$text);
	$text = str_replace("@",$to,$text);
	$text = str_replace("#",$to,$text);
	$text = str_replace(" ",$to,$text);
	$text = str_replace("\t",$to,$text);
	
	return $text;	
}


function CleanText($text){
	return CleanTo($text," ");	
}

function Clean($text){
	return CleanTo($text," ");
}



//Para limpiar nombres
function CleanPersonales($text,$to=" ")  {
	$text = str_replace("'",$to,$text);
	$text = str_replace("\\",$to,$text);
	$text = str_replace("#",$to,$text);
	$text = str_replace(" ",$to,$text);
	$text = str_replace("\t",$to,$text);	
	return $text;	
}


//Para identificadores 
function CleanID($IdentificadorNumerico) {
	return 	intval($IdentificadorNumerico);
}

//Para numeros positivos
function CleanIndexPositivo($num){
	$num = intval($num);
	if ($num<0)
		return - $num;
	return $num;	
}

//Convierte texto en html
function CleanToHtml($str) {	
	$str = htmlentities($str,ENT_QUOTES,'UTF-8'); 
	return str_replace("\n","<br>",$str);	
	//return nl2br($str);
}


function dumb_html($str){

    $str = str_replace("<","&lt;",$str);
    $str = str_replace(">","&gt;",$str);
    $str = str_replace("\n","<br />",$str);

    return $str;
}

function html($str){
	return 	htmlentities($str,ENT_QUOTES,'UTF-8'); ;
}



function entichar($chr){
	return "&#" . ord($chr) . ";";
}

function fichero($file){	//fichero sin paths, ni cosas raras.
	$file = str_replace("/","",$file);
	$file = str_replace("..","",$file);

	return $file;
}



function CleanHTMLtoBD($text) {
	$text = str_replace("#",entichar("#"),$text);
	$text = str_replace("'",entichar("'"),$text);	
	$text = str_replace("\\",entichar("\\"),$text);	
	$text = strip_tags($text,"<br>");
	//$text = str_replace("<br>","\n",$text);	
	return $text;		
}

function CleanBDtoTexto($text) {
	$text = str_replace("\\'","'",$text);
	return $text;	
}

function CleanDoMagicQuotes($text) {
	if( get_magic_quotes_gpc())
		return $text;
	return addslashes($text);
}

function CleanNL2BR($text){
	return nl2br($text);	
}


function Convertir2Textoplano($html) {
	$out = str_replace("<br>","\n",$html);
	$out = strip_tags($out);		
	return $out;
}

//Elimina los atributos del html
function SimplificaHTML($html){
	$out = "";
	//Interprete de HTML
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)	{
		if($i%2==0)		{
			//Text
			$out .= $e;			
		}	else		{
			//Etiqueta			
			//$out .= "<$e>";
			
			//Extraer atributos			
			$tag=array_shift(explode(' ',$e));
			$out .= "<$tag>";
		}
	}
	
	return $out;
}

function CleanFloat($val) {	
	$val = str_replace(",", ".", $val );
	return (float)$val;	
}


//Para localizadores 
function CleanLocalizador($local) {
	return trim($local); 	
}

//Para DNI
function CleanDNI($local) {
	$local = trim($local);
	return strtoupper(trim(CleanTo($local))); 	
}


function CleanUrl($url){
	$url = str_replace("'","",$url);
	$url = trim($url);
	return $url;	
}

/*
function CleanCP($cp){
	$cp = trim($cp);
	return $cp;
}*/


function sql($dato){
	return CleanRealMysql($dato);
}

function CleanRealMysql($dato,$quitacomilla=true){
	global  $link;
	
	if (!$link){
		//NOTA:
		//  mysql real escape necesita exista una conexion,
		// ..por eso si no hay ninguna establecida, la abrimos. 
		forceconnect();
	}
		
	if ($quitacomilla)
		$dato = str_replace("'"," ",$dato);
	$dato_s = mysqlescape($dato);
	return $dato_s;
}

function CleanNif($nif){
	return CleanDNI($nif);	
}

function CleanEmail($correo){
	return CleanCorreo($correo);	
}

function CleanCC($cc){
	$cc = trim($cc);
	$cc = str_replace(" ","",$cc);
	return $cc;	
}


function CleanCorreo($correo){
	$correo = trim($correo);
	$correo = str_replace(" ","",$correo);
	return $correo;
}

function esCorreoValido($correo){
	$correo = CleanCorreo($correo);
	list($usuario,$host) = _split("\@",$correo);

	$len = strlen($usuario);
	if ($len<1)	return false;
	$len = strlen($host);
	if ($len<1)	return false;
	return true;
}

function CleanTelefono($tel){
	$tel = trim($tel);
	$tel = str_replace(" ","",$tel);
	return $tel;	
}


function esTelefonoValido($tel){

	if (!$tel or $tel=="")
		return false;		
			
	$len = strlen($tel);
	if ($len<6)	return false;
	
	return true;
}


function FormatMoney($val,$symbol=" &euro;") {
	$val = CleanDinero($val);
	//return htmlentities(money_format('%.2n $euro;', $val),ENT_QUOTES,'ISO-8859-15');
	//return money_format('%.2n &euro;', $val);
	return number_format($val, 2, ',', "."). $symbol;
}


function FormatUnidades($val) {
	return number_format($val, 0, ',', ".");
}


function FormatUnits($val) {
	return $val . " u.";	
}


if(function_exists("iconv")) {
	function iso2utf($text) {	
		return iconv("ISO-8859-1","UTF8",$text);
	}
	function utf8iso($text){
		return iconv("UTF8","ISO-8859-1//TRANSLIT",$text);		
	}	
	
} else {
	//TODO: buscar alternativa que no sea lenta
	function iso2utf($text) {	
		return $text;
	}
	function utf8iso($text){
		return $text;		
	}			
}


function CleanNombre($nombre){	
	return $nombre;	
}



function genCssName($name){
	$name = strtolower($name);
	$name = str_replace(" ","",$name);
	$name = str_replace("-","",$name);
	$name = str_replace("#","",$name);
	$name = trim($name);
	return $name;
}


function descodifica_mime($var){

        $entra = $var;
	$procesa = $var;
	if(strlen($var)==100 and  stristr($var,"=?") and stristr($var,"?=")===FALSE ){
		$procesa = $var . "?=";
	}

        $mime = false;

	if ( stristr($var,"iso-8859-1?")){
                $mime = "iso-8859-1";
		$newvar = iconv_mime_decode($procesa,2,$mime . "//TRANSLIT");
	} else if ( stristr($var,"iso-8859-15?")){
                $mime = "iso-8859-15";
		$newvar = iconv_mime_decode($procesa,2,$mime . "//TRANSLIT");
	} else if ( stristr($var,"Windows-1252?")){
                $mime = "Windows-1252";
		$newvar = iconv_mime_decode($procesa,2,$mime . "//TRANSLIT");
	} else if (stristr($var,"UTF-8?")){
                $mime = "UTF-8";
		$newvar = iconv_mime_decode($procesa,2,$mime . "//TRANSLIT");
	} else {
                $mime = "UTF-7";
		$txt = preg_replace( '/=\?([^?]+)\?/', '=?iso-8859-1?', $procesa);
		$newvar = iconv_mime_decode( $txt, 0, "UTF-8//TRANSLIT" );
	}
        
	if ($newvar){
            if($mime and $mime!="UTF-8"){
                $newvar = iconv($mime, "UTF-8//IGNORE", $newvar);
            }
        }

        if($newvar)
           $var = $newvar;

	return $var;
}




function Simboliza($texto){
        $texto = strtolower($texto);
	$len = strlen($texto);
	$final = "";

	for($t=0;$t<$len;$t++){
		$c = $texto[$t];
		if (ord($c)>127){
			$c = "";
	        }
		if($c==" ") $c = "_";
		if($c=="(") $c = "D";
		if($c==")") $c = "c";
		if($c=="!") $c = "i";
		$final .= $c;
	}

        $final = "S". md5($str);

	return $final;
}



/**
 * Convert number of seconds into hours, minutes and seconds
 * and return an array containing those values
 *
 * @param integer $seconds Number of seconds to parse
 * @return array
 */
function secondsToTime($seconds)
{
	// extract hours
	$hours = floor($seconds / (60 * 60));

	// extract minutes
	$divisor_for_minutes = $seconds % (60 * 60);
	$minutes = floor($divisor_for_minutes / 60);

	// extract the remaining seconds
	$divisor_for_seconds = $divisor_for_minutes % 60;
	$seconds = ceil($divisor_for_seconds);

	// return the final array
	$ret = array(
		"h" => (int) $hours,
		"m" => (int) $minutes,
		"s" => (int) $seconds,
	);

        if ($hours>0){
            $patron = sprintf("%d horas, %d min %d s",$hours,$minutes,$seconds);
        } else  if($hours<=0 and $minutes>0){
            $patron = sprintf("%d minutos, %d s",$minutes,$seconds);
        } else {
            $patron = sprintf("%d segundos",$seconds);
        }

        $ret["txt"] = $patron;

        return $ret;
}

function CleanSecondsAHumano($seconds){
    $ret = secondsToTime($seconds);

    return $ret["txt"];
}