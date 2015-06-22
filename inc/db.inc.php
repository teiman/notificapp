<?php

/**
 * Ayudas para acceso a datos
 *
 * @package ecomm-aux
 */


define ("FORCE",1);

$link = falsE;
$UltimaInsercion = false;
$FilasAfectadas = false;


function parametros(){
    $args = func_get_args();
    if (count($args) < 2){
        return false;
    }
    $query = array_shift($args);
    $args = array_map('mysql_real_escape_string', $args);
    array_unshift($args, $query);
    $query = call_user_func_array('sprintf', $args);

    return ($query);
}



function ContarFilas($from,$where=""){
	global $FilasAfectadas;
	$res = Seleccion($from,$where);
	if (!$res)
		return false;
	
	return $FilasAfectadas;	
} 


function Row($res) {

	if(!$res){
		error(__FILE__ . __LINE__ ,"ERROR requiriendo datos");
		return false;	
	}
	
	$data = mysql_fetch_array($res, MYSQL_ASSOC);
	if (!is_array($data)) {
		$data = mysql_fetch_row($res);
	}

	return $data;
}

function LogSQLErroneo ($sql) {
	global $logSQL;

	error_log("E: sql($sql)");
}



function forceconnect(){
	//Solamente abre una conexion	
	global $link;	
	global $ges_database;	
	global $global_host_db;
	global $global_user_db ;
	global $global_pass_db;
	
	$database = $ges_database;	
		
	if (!$link) {
		//Si no se conecto antes, conecta ahora.
		$link = mysql_connect($global_host_db, $global_user_db, $global_pass_db);
		if (!$link)
			error(__FILE__. __LINE__, "Fatal: No puedo conectar a la base de datos");
		else
			mysql_select_db($database,$link);							
	}		
}          


function conexion($uselink=false){
    global $link;
    $oldlink = $link;

    $link = $uselink;
    return $oldlink;
}


function queryCount($sql=false,$nick="") {
	global $link;
	global $UltimaInsercion,$FilasAfectadas, $debug_sesion;
	global $ges_database;
	global $sqlTimeSuma;
	global $global_host_db;
	global $global_user_db ;
	global $global_pass_db;
	global $querysRealizadas;
	global $logSQL;

	$lastime = microtime(true);

	if (!isset($sql)) {
		error(__FILE__ . __LINE__ , "Fatal: se paso un sql vacio!");
		return false;
	}

	$database = $ges_database;
	$result = false;

	if (!$link) { forceconnect(); }

	if ($link) 	$result = mysql_unbuffered_query($sql,$link) or LogSQLErroneo($sql);

	if (!$result) {
		$error = mysql_error($link);
		die("Fallo de conexión en $sql o $link\n<br>\nTipo: $error");
	}

        $num = mysql_num_rows($result);
        mysql_free_result($result);

	return $num;
}

function query($sql=false,$nick="") {
	global $link;
	global $UltimaInsercion,$FilasAfectadas, $debug_sesion;		
	global $ges_database;
	global $sqlTimeSuma;
	global $global_host_db;
	global $global_user_db ;
	global $global_pass_db;

	global $logSQL;
	
	$lastime = microtime(true);
	
	if (!isset($sql)) {
		error(__FILE__ . __LINE__ , "Fatal: se paso un sql vacio!");
		return false;
	}
		
	$database = $ges_database;	
	$result = false;

	if (!$link) { forceconnect(); }


    d2($sql,"query.".$nick);

	if ($link) 	$result = mysql_query($sql) or LogSQLErroneo($sql);



	if (!$result) {
		$error = mysql_error($link);
		die("Fallo de conexión en $sql o $link\n<br>\nTipo: $error");
	}
	
	$ahora = microtime(true);
		
	$sqlTimeSuma = $sqlTimeSuma + ($ahora - $lastime);

	$UltimaInsercion  = mysql_insert_id($link);
	$FilasAfectadas  = mysql_affected_rows($link);	
	return $result;
}

		

function CreaUpdate ($soloEstos, $data,$nombreTabla, $nombreID,$idvalue ) {
		$coma = false;
		$str = "";
	
		foreach ($data as $key => $value) {
			if ( in_array($key,$soloEstos) and $key != "0" ) {
				if ($coma)
					$str .= ",";

				$value = mysql_escape_string($value);

				$str .= " $key = '$value'";
				$coma = true;
			}
		}

		return "UPDATE $nombreTabla SET $str WHERE $nombreID = '$idvalue'";
}
	
function CreaUpdateSimple ($data,$nombreTabla, $nombreID,$idvalue ) {
		$coma = false;

                error_log("DEPRECATE(CreaUpdateSimple): usado por $nombreTabla");

                $str = "";
		foreach ($data as $key => $value) {
			if (  $key != "0" and intval($key)==0 ) {
				if ($coma)
					$str .= ",";

				$value = mysql_escape_string($value);

				$str .= " `$key` = '$value'";
				$coma = true;
			}
		}

		return "UPDATE $nombreTabla SET $str WHERE $nombreID = '$idvalue'";
}
 
 
function queryrow($sql,$nick=false) {
	$res = query($sql,$nick);
	if (!$res){
		return false;	
	}
	$row = Row($res);
	if (!is_array($row)){
		//echo "no es array...($sql)(" .var_export($row,true). ")\n";
		return false;	
	}
	return $row;
} 

