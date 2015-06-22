<?php

/**
 * Ayuda para creacion de listas desplegables sobre elementos del sistema
 *
 * @package ecomm-aux
 */


//para su uso con combos de etiquetas
define("ETIQUETAS_BASICAS",1);
define("ETIQUETAS_USUARIO",2);
define("ETIQUETAS_ESTADOS",3);



function genComboLocationsVisible($idquien=-1){

    $id_user = getSesionDato("id_user");
    $sql = "SELECT count(*) as c FROM user_groups JOIN groups  ON user_groups.id_group = groups.id_group  WHERE  groups.id_location>0  and id_user='$id_user' ";//TODO: locations borrados?
    $data = queryrow($sql);

    $hayFiltrosDelegacion = $data["c"]>0;

	$sql = "SELECT * FROM `locations` WHERE eliminado=0 ORDER BY peso ASC, `name` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["name"];

		$key = $row["id_location"];

        $id_location = $key;
        $sql = "SELECT * FROM user_groups JOIN groups  ON user_groups.id_group = groups.id_group "
        .   " WHERE  groups.id_location>0 and id_user=$id_user and id_location=$id_location GROUP BY user_groups.id_user, groups.id_location ";

        $esVisible = queryrow($sql);


		if ($key == $idquien){
			$selected = "selected='selected' style='font-weight:bold;padding-left: 8px' ";
		} else {
			$selected = "";
		}

        if($esVisible or !$hayFiltrosDelegacion){
            $out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
        }
	}

	return $out;
}




function genComboLocations($idquien=-1){
        
        if(getParametro("argentina.diferenciar_otros")==1){
            $soloArgentina = estaHabilitado("restriccion_clientes_argentina",false);        

            $id = getParametro("argentina.id_delegacion_argentina");
            if ( $soloArgentina ){                                
                $extraSQL = " AND id_location='$id' ";
            } else {
                $extraSQL = " AND id_location!='$id' ";
            }   
        }
        
    
	$sql = "SELECT * FROM `locations` WHERE eliminado=0 $extraSQL ORDER BY `name` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["name"];

		$key = $row["id_location"];

		if ($key == $idquien){
			$selected = "selected='selected' style='font-weight:bold;padding-left: 8px' ";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}


function genComboEtiquetas($idquien=-1){

	$sql = "SELECT * FROM `labels` ORDER BY `id_label_type` ASC, label ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["label"];

		$key = $row["id_label"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}


function genComboTipoEtiqueta($idquien=-1){

	$sql = "SELECT * FROM `label_types` ORDER BY `label_type` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["label_type"];

		$key = $row["id_label_type"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}




function genComboProfiles($idquien=-1, $especifica=false){

	if ($especifica) {
		$extra = " isgroupprofile='".$especifica["id"]."' AND ";
	}

	$sql = "SELECT * FROM `profiles` WHERE $extra deleted=0 ORDER BY `name` ASC ";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["name"];

		$key = $row["id_profile"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}



function genCombosStatusCanal($canal,$idquien=-1,$exclusion=false){

	$sql = "SELECT * FROM `status` WHERE id_task='$canal' ORDER BY peso ASC,`status` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name	= $row["status"];
		$key	= $row["id_status"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}
        
        $agnadir = true;
        
        if($exclusion){
            if($exclusion[$key])
                $agnadir = false;            
        }

        if ($agnadir){
            $out .= "<option value='$key' $selected> " . html($name) . "</option>\n";
        }
	}

	return $out;
}


function genCombosStatus($idquien=-1){

	$sql = "SELECT *
FROM `status`
JOIN tasks
ON status.id_task = tasks.id_task

ORDER BY
status.id_task ASC,
`status` ASC
";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["status"];

		$key = $row["id_status"];

        $task = $row["task"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>[$task] " . html($name) . "</option>\n";
	}

	return $out;
}



function genComboTarea($idquien=-1){

	$sql = "SELECT * FROM `tasks` ORDER BY `task` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["task"];

		$key = $row["id_task"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}



function genComboMedios($idquien){

	$sql = "SELECT * FROM `medias` ORDER BY `media` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["media"];

		$key = $row["id_media"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}



function genComboGrupos($idquien,$ocultalocales=true){


    if($ocultalocales){
        $extra = "WHERE groups.id_location=0 ";
    }

	$sql = "SELECT * FROM `groups` $extra ORDER BY `group` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["group"];

		$key = $row["id_group"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}


function genComboGruposDelegaciones($idquien){



    $extra = "WHERE groups.id_location>0 ";


	$sql = "SELECT * FROM `groups` $extra ORDER BY `group` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["group"];

		$key = $row["id_group"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}




function getComboStatus($id_label_type=3,$idquien=-1){

	$sql = "SELECT * FROM `labels` WHERE id_label_type='$id_label_type' ORDER BY `label` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["label"];

		$key = $row["id_label"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$icon = $row["icon"];
		$css = $icon?"background-image: url(icons/$icon);background-repeat: no-repeat":"";

		$out .= "<option class='relativo_tipo_".$id_label_type."' style='$css;padding-left: 18px' value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}





function genComboCanales($idquien){

	$sql = "SELECT * FROM `channels` ORDER BY `channel` ASC";

	$res = query($sql);

	$out = "";
	while ($row= Row($res)){
		$name = $row["channel"];

		$key = $row["id_channel"];

		if ($key == $idquien){
			$selected = "selected='selected'";
		} else {
			$selected = "";
		}

		$out .= "<option value='$key' $selected>" . html($name) . "</option>\n";
	}

	return $out;
}


function genComboCOMDIR($com_dir ){

	$dirs = array("0"=>"Enviados y recibidos","1"=>"Recibidos","2"=>"Enviados");

	$out .= "";

	foreach($dirs as $key=>$value){
		$extra = ($com_dir==$key)?"selected='selected'":"";

		$out .= "<option $extra value='" . $key . "'>" . html($value) . "</option>";
	}

	return $out;
}


function genSelectorNubeEtiquetas($id_label_actual,$id_usuario_actual, $namelabel ){

	$sql = "SELECT label as dato FROM labels WHERE id_label='$id_label_actual'";
	$row = queryrow($sql);
	$labelactual = $row["dato"];

	$sql = "SELECT * FROM labels WHERE id_user='$id_usuario_actual' ORDER BY id_label_type ASC, id_channel ASC, label ASC ";
	$res = query($sql);
	$row = Row($res);

	$forceStart = true;
	$propiosAgotados = false;

	$out .= "<div style='color: #ddd'>";

	$out .= "<input type='text' id='label_seleccionada_texto' value='".html($labelactual)."'>";
	$out .= "<input type='hidden' name='$namelabel' value='".$id_label_actual."' id='id_label_seleccionada'><br/>";

	$out .= "<a href='#'  onclick='select(0,\"\")'>" . html("Borrar etiqueta") . "</a> | ";

	while($row or $forceStart){
		$forceStart = false;

		$decora = "";

		if ($row){

			$decora .= ($row["id_user"]==$id_usuario_actual)?"font-weight: bold;":"";

			$out .= "<a href='#' style='".$decora."' onclick='select(".$row['id_label'].",\"".addslashes($row["label"]) ."\")'>" . html($row["label"]) . "</a> | ";
		}
		
		//siguiente label
		$row = Row($res);
		if ( !$row and !$propiosAgotados){
			//se han agotado los labels propios, intentar los externos
			$sql =  "SELECT * FROM labels WHERE id_user!='$id_usuario_actual' ORDER BY id_label_type ASC, id_channel ASC, label ASC ";
			$res = query($sql);
			$row = Row($res);
			$propiosAgotados = true;
		}


	}

	$out .= "</div>";


	$out .= "<script>";

	$out .=  "  function select(id_seleccion,labelname){
	document.getElementById('id_label_seleccionada').setAttribute('value',id_seleccion);
	document.getElementById('id_label_seleccionada').value = id_seleccion;
	document.getElementById('label_seleccionada_texto').setAttribute('value',labelname);
 }; ";

	$out .= "</script>";


	return $out;

}




function genArrayEtiquetas($id_comm, $modo="sinfiltro"){

        $id_type_user = 1;

	$out = "";


        $extra = "";

        switch($modo){
            case "system":
                $extra = "AND labels.id_label_type !=$id_type_user ";
                break;
            case "user":
                $extra = "AND labels.id_label_type =$id_type_user ";
                break;
        }


	$sql = "SELECT * FROM labels
		INNER JOIN label_types ON labels.id_label_type = label_types.id_label_type
                INNER JOIN label_coms ON labels.id_label = label_coms.id_label
		WHERE labels.id_label>0 AND label_coms.id_comm = $id_comm $extra 
		ORDER BY labels.id_label_type ASC, `label`  ASC";



	$res = query($sql);

	$forceStart = true;
	$propiosAgotados = false;

	$etiquetas = array();

	$coma = "";

	while($row  =Row($res) ){

		if ($filter){
			if ($row["id_label_type"]!=$filter) continue;
		}

		$t = $row["id_label_type"];

		$label = $row['label'];//  . ",t:$t". ",f:".$filter;

		$etiquetas[]  = array('etiqueta'=>$label , 'idlabelcom'=>$row['id_label_com'],
                    'idlabel'=>$row["id_label"],"id_comm"=>$row["id_comm"],"type"=>$row["id_label_type"]);
	}

	return $etiquetas;
}



function genListEtiquetasCommArray($id_comm, $filter=false){

	$out = "";

	$sql = "SELECT * FROM labels
		INNER JOIN label_types ON labels.id_label_type = label_types.id_label_type
        INNER JOIN label_coms ON labels.id_label = label_coms.id_label
		WHERE labels.id_label>0 AND label_coms.id_comm = $id_comm
		ORDER BY labels.id_label_type ASC, `label`  ASC";

	$res = query($sql);

	$forceStart = true;
	$propiosAgotados = false;

	$etiquetas = array();

	$coma = "";

	while($row  =Row($res) ){

		if ($filter){
			if ($row["id_label_type"]!=$filter) continue;
		}

		$t = $row["id_label_type"];
	
		$label = $row['label'];//  . ",t:$t". ",f:".$filter;

		$etiquetas[]  = array('etiqueta'=>$label , 'idlabelcom'=>$row['id_label_com'],
                    'idlabel'=>$row["id_label"],"id_comm"=>$row["id_comm"],"type"=>$row["id_label_type"]);
	}

	return $etiquetas;
}


function genListEtiquetasComm($id_comm){

	$out = "";

	$sql = "SELECT * FROM labels
		INNER JOIN label_types ON labels.id_label_type = label_types.id_label_type
        INNER JOIN label_coms ON labels.id_label = label_coms.id_label
		WHERE labels.id_label>0 AND label_coms.id_comm = $id_comm
		ORDER BY labels.id_label_type ASC, `label`  ASC";



	$res = query($sql);

	$forceStart = true;
	$propiosAgotados = false;

	$coma = "";

	while($row  =Row($res) ){
		$out .= "$coma " . $row["label"];
		$coma = ",";
	}

	return $out;
}


