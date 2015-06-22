<?PHP
/**
 * patTemplate modfifier Truncate
 *
 * $Id: modifiers.xml 34 2004-05-11 19:46:09Z schst $
 *
 * @package        patTemplate
 * @subpackage     Modifiers
 * @author
 */


//<patTemplate:var name="id_definicion" modifier="avatar" />


class patTemplate_Modifier_Avatar extends patTemplate_Modifier
{
    /**
    * truncate the string
    *
    * @access    public
    * @param    string        value
    * @return    string       modified value
    */
    function modify( $value, $params = array() )  {

	
	//$id_user = getSessionDato(""):
	    
	if(!$value) return "<h1>hola</h1>";
       
       $data = "(<img src='images/$value' class='contingente'>)";

       return $data;
    }

    
}

