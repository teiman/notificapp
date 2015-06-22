<?php


    class paginaBasica extends Pagina {

        function setTitulo($nombre){
            $this->addVar('headers', 'titulopagina', $nombre);
        }
    };

    $lang = "es";

    $page = new paginaBasica();

    $page->Inicia($template["modname"],"basica.html");

    
