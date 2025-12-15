document.addEventListener("DOMContentLoaded", () => {  


    // dropdown logout
    profileBtn.addEventListener("click", function(){
      let profileDiv = document.querySelector('#profileDiv');
      showHide(profileDiv);
    });
          
    function showHide(element) {
        if (element.classList.contains('hidden')) {
          element.classList.remove('hidden');
          element.classList.add('profileDiv_flex');

        } else {
          element.classList.add('hidden');
          element.classList.remove('profileDiv_flex');
        }
    }      

  
    // Show Hide menu's options
    var divs = document.getElementsByClassName("menu-btn");
  
    for (var i = 0; i < divs.length; i++) {                    
            divs[i].addEventListener("click", function () {                
              
                if(this.nextElementSibling.classList.contains('hidden')){
                    this.childNodes[2].firstChild.classList.value = "fas fa-angle-down ml-3";
                    this.nextElementSibling.classList.remove('hidden');
                    this.nextElementSibling.classList.add('flex');
                } else {
                    this.childNodes[2].firstChild.classList.value = "fas fa-angle-right ml-3";
                    this.nextElementSibling.classList.remove('flex');
                    this.nextElementSibling.classList.add('hidden');
                }                
            });       
    };


    // Show Hide sidebar
    let sidebarBtn = document.querySelector('#sidebarBtn');   
    let sidebarview = document.querySelector('aside');   
    if (sidebarBtn && screen.width > 960) {      
      sidebarview.style.display = 'block';
    }   
    if (sidebarBtn && screen.width < 960) {      
      sidebarview.style.display = 'none';
    }
   
    sidebarBtn.addEventListener("click", function(){

      let sidebar = document.querySelector('aside');      

        //side_bar
        if(sidebar.style.display == 'block'){          
          sidebar.style.display = 'none';          
        }else{          
          sidebar.style.display = 'block';          
        }
      
        /*
        if (sidebar.classList.contains('block')) {
          sidebar.classList.replace('block', 'sidebar_oculto');
      
        } else {
          sidebar.classList.replace('sidebar_oculto', 'block');
        }
        */          
    });


    function validatorInputKeyPress() {		
      
      //Validaciones de los campos del fomulario de creación/edición				
      var UXAPP = UXAPP || {};

      // paquete de validaciones
      UXAPP.validador = {};

      // método que inicia el validador con restriccion de caracteres
      UXAPP.validador.init = function () {
          // busca los elementos que contengan el atributo regexp definido
          $("input[regexp]").each(function(){
              // por cada elemento encontrado setea un listener del keypress
              $(this).keypress(function(event){
                  // extrae la cadena que define la expresión regular y creo un objeto RegExp 
                  // mas info en https://goo.gl/JEQTcK
                  var regexp = new RegExp( "^" + $(this).attr("regexp") + "$" , "g");
                  // evalua si el contenido del campo se ajusta al patrón REGEXP
                  if ( ! regexp.test( $(this).val() + String.fromCharCode(event.which) ) )
                      event.preventDefault();		
              });
          });	
      }

      // Arranca el validador al término de la carga del DOM
      $(document).ready( UXAPP.validador.init );

    }


    validatorInputKeyPress();


    
});

