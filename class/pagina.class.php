<?php



/*
 * Pagina
 *
 * Template de una pagina de ecomm
 * 
 */
class Pagina extends patTemplate {

	function IniciaTranslate(){
		global $lang;
		global $templatesDir;
		$pagina = 'cadenasdesistema.txt';

		$this->setOption( 'translationFolder', "translations" );
		$this->setOption( 'translationAutoCreate', true );
		$this->setOption( 'lang',  $lang );
		$this->addGlobalVar( 'page_encoding', "utf-8" );

		$this->setRoot( $templatesDir );

		//NOTA: activa el cache
		if (0){
		$this->useTemplateCache( 'File', array(
                                            'cacheFolder' => './templates/cache',
                                            'lifetime'    => 60*60,
                                            'filemode'    => 0644
                                        )
                        );
		}

  
		$this->readTemplatesFromInput($pagina);
	}


	function Inicia($modname,$pagina=false){
		global $page,$lang;//?? porque esto en lugar de $this
                global $templatesDir;
                global $usar_apc_cache;
                                                   
		if(!$pagina)
                    $pagina = 'basica.html';

		$page->setOption( 'translationFolder', "translations" );
		$page->setOption( 'translationAutoCreate',false );
		$page->setOption( 'lang',  $lang );
		$page->addGlobalVar( 'page_encoding', "utf-8" );
		$page->setRoot( $templatesDir );

                if($usar_apc_cache)
                    if(ini_get("apc.enabled")){
                        $this->useTemplateCache( 'apc',array());
                    }


		$loaded = $page->readTemplatesFromInput($pagina);

                if (!$loaded){
                    error_log("ERROR: template '$pagina' no se puede parsear");
                    //TODO: posiblemente abortar el resto de la carga aqui
                } 
		
		$page->addVar('page', 'modname', $modname );
		//$page->addVar('headers','versioncss',rand());
		$page->addVar('headers','modname',$modname);
	}


	function _($text){	
                if(1){ //desactivamos el sistema de traducciones
                    return $text; //
                }

                //--------------
		global $lang;
			
		//if ($lang=="es") $lang = "";		
		
		$folder = "translations";
		//$input = $this->_reader->getCurrentInput();		
		//$input = $this->_reader->_currentInput;						
		
		//$name = $folder . "/" . $input . "-".$lang.".ini";
		$name = "traducciones_" . $lang;
		
		$code = md5($text);
		
		$dato = $_SESSION[$name][$code];
		
		if ($dato)	{ //Nota, que no haya traduccion no es suficiente, puede que este ya en el fichero

			if (1){
				//TODO: desactivar esto en produccion
				$pagina = "templates" . "/" .'cadenasdesistema.txt';

				//Si no estaba, lo añadimos
				$template = file_get_contents($pagina);

				$existe = strstr($template,">".$text."<");

				if($template and !$existe){
					$template = str_replace("</patTemplate:tmpl>","Auto: <patTemplate:Translate>".$text."</patTemplate:Transl</patTemplate:tmpl>",$template);
					file_put_contents($pagina,$template);
				} else {
					if (!$template){
						$dir = getcwd();
						die("no puedo abrir ($pagina|$dir)");
					}
				}
			}
			
			return $dato;
		}
		
		return $text;
	}


	function Volcar(){

		$this->displayParsedTemplate();

        if (isset($_GET["vertemplate"]) and $_GET["vertemplate"] == 1) {
            $this->dump();
        }
	}

}


/*
 * Se comporta como si fuera una template, pero realmente esta vacia
 * se usa para simular el sistema de traducciones cuando esta desactivado.
 */

class FakePagina {
        function IniciaTranslate(){}
        
    	function _($text){
                    return $text; //
        }
}

