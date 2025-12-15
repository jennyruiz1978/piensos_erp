document.addEventListener("DOMContentLoaded", () => {    

  if (window.location.pathname.includes("/Zonas")) {
        
    var urlCompleta = $('#ruta').val();
    
    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']
      if (opcionesControladas.includes(optionCRUD)) {

         if (optionCRUD == 'c') {                                 
           
           window.location.href = urlCompleta+"/Zonas/altaZonas";

         }else if(optionCRUD == 'u'){

           let idZona = event.target.getAttribute('data-idupd');
           if(idZona > 0){   
            document.getElementById('idZonaEditar').value = idZona;           
            obtenerDatosZona(idZona);
           }

         }else if(optionCRUD == 'b'){

           let idZona = event.target.getAttribute('data-iddel');
           if(idZona > 0){              
             $('#modalFormEliminarZona').modal('show');
             document.getElementById('idZonaEliminar').value = idZona;
           }

         }
      
      } else {
         return
      }
    }

    const tablaZonas = document.getElementById('tablaZonas');
    if(tablaZonas){
      document.getElementById('tablaZonas').addEventListener('click', eventHandler);
    }    
   


    let formCrearZona = document.getElementById('formulario_crear_zona');
    if(formCrearZona){
      formCrearZona.addEventListener('submit', function(e) {        
        e.preventDefault();
          let ruta = urlCompleta+'/Zonas/crearZona';
          
          let data = `nombre_zona=${formCrearZona.nombre_zona.value}&precio_zona=${formCrearZona.precio_zona.value}`;  
          crearZona(data, ruta);
      });
    }


    function crearZona(data, ruta){

      let xhr = new XMLHttpRequest();
      xhr.open("POST", ruta, true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(          
        data
      );
      xhr.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              let respuesta = this.responseText;
              respuesta = JSON.parse(respuesta);     
                                          
              let texto = respuesta.mensaje;    
              if(respuesta.error == false){                                
                Swal.fire({
                  title: 'Procés correcte',
                  text: texto,
                  icon: 'success',                  
                  timer: 3000,
                });
                setTimeout(function () {
                  window.location = urlCompleta+'/Zonas';
                },2000);
                

              }else{                                
                Swal.fire({
                  title: 'Error',
                  text: texto,
                  icon: 'error',
                  confirmButtonText: 'Tancar',
                });                  
              }
              
          }
      };      

    }

    function obtenerDatosZona(idZona) {
      
      let ruta = urlCompleta+'/Zonas/obtenerZona';
      let data = `id=${idZona}`;

      obtenerZonaPorId(data, ruta);                                     

    }
       
    function obtenerZonaPorId(data, ruta){

      let xhr = new XMLHttpRequest();
      xhr.open("POST", ruta, true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(          
        data
      );
      xhr.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              let respuesta = this.responseText;
              respuesta = JSON.parse(respuesta);     
                                          
              let texto = respuesta.mensaje;    
              if(respuesta.error == false){                                                
                datos = respuesta.datos;
                let formulario_editar_zona = document.getElementById('formulario_editar_zona');                
                formulario_editar_zona.nombre_zona.value = datos.zona;
                formulario_editar_zona.precio_zona.value = datos.margen;
                formulario_editar_zona.estado_zona.value = datos.status;                

                $('#modalFormEditZona').modal('show');     

              }else{
                Swal.fire({
                  title: 'Error',
                  text: texto,
                  icon: 'error',
                  confirmButtonText: 'Tancar',
                });                  
              }
              
          }
      };      

    }

    $('#modalFormEditZona').on('hidden.bs.modal', function () {
			$('#formulario_editar_zona').trigger("reset");			
		});   
    
    
    let formulario_editar_zona = document.getElementById('formulario_editar_zona');
    if(formulario_editar_zona){
      formulario_editar_zona.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_editar_zona.idZonaEditar.value != ''){
                        
            let ruta = urlCompleta+'/Zonas/actualizarZona';
            
            let data = `id=${formulario_editar_zona.idZonaEditar.value}&nombre_zona=${formulario_editar_zona.nombre_zona.value}&precio_zona=${formulario_editar_zona.precio_zona.value}&estado_zona=${formulario_editar_zona.estado_zona.value}`;

            actualizarDatosZona(data, ruta);
          }          
      });
    }

       
    function actualizarDatosZona(data, ruta){

      let xhr = new XMLHttpRequest();
      xhr.open("POST", ruta, true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(          
        data
      );
      xhr.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              let respuesta = this.responseText;
              respuesta = JSON.parse(respuesta);     
                                          
              let texto = respuesta.mensaje;    
              if(respuesta.error == false){                                
                Swal.fire({
                  title: 'Procés correcte',
                  text: texto,
                  icon: 'success',                  
                  timer: 3000,
                });
                setTimeout(function () {
                  window.location = urlCompleta+'/Zonas';
                },2000);
                

              }else{                                
                Swal.fire({
                  title: 'Error',
                  text: texto,
                  icon: 'error',
                  confirmButtonText: 'Tancar',
                });                  
              }
              
          }
      };      

    }
    

    let formulario_eliminar_zona = document.getElementById('formulario_eliminar_zona');
    if(formulario_eliminar_zona){
      formulario_eliminar_zona.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_eliminar_zona.idZonaEliminar.value != ''){
            
            $('#modalFormEliminarZona').modal('hide');
            let ruta = urlCompleta+'/Zonas/eliminarZona';
            let data = `id=${formulario_eliminar_zona.idZonaEliminar.value}`;
            borrarZonaPorId(data, ruta);
          }          
      });
    }

    function borrarZonaPorId(data, ruta){
      
      let xhr = new XMLHttpRequest();
      xhr.open("POST", ruta, true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(          
        data
      );
      xhr.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              let respuesta = this.responseText;
              
              respuesta = JSON.parse(respuesta); 

              let texto = respuesta.mensaje;    
              if(respuesta.error == false){                                
                Swal.fire({
                  title: 'Procés correcte',
                  text: texto,
                  icon: 'success',                  
                  timer: 3000,
                });
                setTimeout(function () {
                  window.location = urlCompleta+'/Zonas';
                },2000);
                

              }else{                                
                Swal.fire({
                  title: 'Error',
                  text: texto,
                  icon: 'error',
                  confirmButtonText: 'Tancar',
                });                  
              }
         
          }
      };      

    }

  }    

});
