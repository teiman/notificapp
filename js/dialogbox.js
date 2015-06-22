  //creación de objeto
  //creamos un  los siguientes metodos para cada tipo de  cuadro de diálogo
  //.si_no  .aceptar_cancelar  .ok  .si_no_cancelar

 var cuadro_dialogo ={
    version:1
   };
  
  cuadro_dialogo.si_no = function  (titulo, mensaje, si, no){
     $("#dialog-confirm").attr("title", titulo);
     $("#dialog-confirm .js-mensaje").html( mensaje );
     $("#dialog-confirm").hide().removeClass("oculto");
     $("#dialog-confirm").show();
     $("#dialog-confirm").dialog({
      resizable: false,
      height:240,
      modal: true,
      buttons: {
       "Sí": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
	si();
        },
        "No": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
           no();	
        }
      }
    });
  };
  
  cuadro_dialogo.ok =  function  (titulo, mensaje){
     $("#dialog-confirm").attr("title", titulo);
     $("#dialog-confirm .js-mensaje").html( mensaje );
     $("#dialog-confirm").hide().removeClass("oculto");
     $("#dialog-confirm").show();
     $( "#dialog-confirm" ).dialog({
      resizable: false,
      height:240,
      modal: true,
      buttons: {
       "Ok": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
	}
      }
    });
  };
  
  cuadro_dialogo.aceptar_cancelar =  function  (titulo, mensaje, aceptar, cancelar){
     $("#dialog-confirm").attr("title", titulo);
     $("#dialog-confirm .js-mensaje").html( mensaje );
     $("#dialog-confirm").hide().removeClass("oculto");
     $("#dialog-confirm").show();
     $( "#dialog-confirm" ).dialog({
      resizable: false,
      height:240,
      modal: true,
      buttons: {
       "Aceptar": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
	aceptar();
        },
        "Cancelar": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
           cancelar();	
        }
      }
    });
  };
   
  
   cuadro_dialogo.si_no_cancelar=  function  (titulo, mensaje,  si, no, cancelar){
      $("#dialog-confirm").attr("title", titulo);
      $("#dialog-confirm .js-mensaje").html( mensaje );
      $("#dialog-confirm").hide().removeClass("oculto");
      $("#dialog-confirm").show();
      $("#dialog-confirm" ).dialog({
      resizable: false,
      height:240,
      modal: true,
      buttons: {
       "Sí": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
	si();
        },
        "No": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
           no();	
        },
	 "Cancelar": function() {
           $( this ).dialog( "close" );
	 $("#dialog-confirm").hide().addClass("oculto");
           cancelar();
       }
     }
   });
 };

cuadro_dialogo.popup_formulacion = function(opciones){
    
     var valordef = {
        titulo:"Título ventana" ,
        id_formula:"0",
        puedo_borrar_formula:true
        
     };
     //sobreescribo los valores enviados desde el programa
     //a los valores por defecto
     $.extend(valordef,opciones);
     
     $("#popupformulacion").attr("title", valordef.titulo);
     if (valordef.puedo_borrar_formula==false){
         $("#borrar_formula").attr("disabled","disabled")
     }
     $("#popupformulacion").hide().removeClass("oculto");
     $("#popupformulacion").show();
     $("#popupformulacion" ).dialog({
      resizable: false,
      height:500,
      width:700,
      modal: true
    });
  };
  
  
  
  cuadro_dialogo.formulario_popup =  function  (titulo, mensaje,idorigen, ok){
      $("#formpopup").attr("title", titulo);
      $("#formpopup").hide().removeClass("oculto");
      $("#formpopup").show();
      $("#formpopup" ).dialog({
      resizable: false,
      height:500,
      width:700,
      modal: true
    });
    
  };
  
   cuadro_dialogo.popup_grupos = function(opciones){
    
     var valordef = {
        titulo:"Título ventana" ,
        id_formula:"0",
        puedo_borrar_formula:true
        
     };
     //sobreescribo los valores enviados desde el programa
     //a los valores por defecto
     $.extend(valordef,opciones);
     
     $("#popupgrupos").attr("title", valordef.titulo);
     //if (valordef.puedo_borrar_formula==false){
     //    $("#borrar_formula").attr("disabled","disabled")
     //}
     $("#popupgrupos").hide().removeClass("oculto");
     $("#popupgrupos").show();
     $("#popupgrupos" ).dialog({
      resizable: false,
      height:500,
      width:350,
      modal: true
    });
  };
  
  
  cuadro_dialogo.popup_grp_usr = function(opciones, ok){
    
     var valordef = {
        titulo:"Título ventana"
     };
     //sobreescribo los valores enviados desde el programa
     //a los valores por defecto
     $.extend(valordef,opciones);
     
     $("#popuppermisos").attr("title", valordef.titulo);
     
     $("#popuppermisos").hide().removeClass("oculto");
     $("#popuppermisos").show();
     $("#popuppermisos" ).dialog({
      resizable: false,
      height:500,
      width:350,
      modal: true,
      buttons: {
       "Ok": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
        ok()
	}
      }
    });
  };
  
  
  cuadro_dialogo.popup_categorias = function(opciones, aceptar, cancelar){
    
     var valordef = {
        titulo:"Título ventana"
     };
     //sobreescribo los valores enviados desde el programa
     //a los valores por defecto
     $.extend(valordef,opciones);
     
     $("#popupcategorias").attr("title", valordef.titulo);
     
     $("#popupcategorias").hide().removeClass("oculto");
     $("#popupcategorias").show();
     $("#popupcategorias").dialog({
      resizable: false,
      height:500,
      width:850,
      modal: true,
       buttons: {
       "Aceptar": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
	aceptar();
        },
        "Cancelar": function() {
          $( this ).dialog( "close" );
	$("#dialog-confirm").hide().addClass("oculto");
           cancelar();	
        }
      }
    });
  };