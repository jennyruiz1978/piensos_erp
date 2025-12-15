document.addEventListener("DOMContentLoaded", () => {    

  if (window.location.pathname.includes("/Proveedores")) {
        
    var urlCompleta = $('#ruta').val();
    
    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']
      if (opcionesControladas.includes(optionCRUD)) {

         if (optionCRUD == 'c') {                                 
           
           window.location.href = urlCompleta+"/Proveedores/altaProveedores";

         }else if(optionCRUD == 'u'){

           let idProveedor = event.target.getAttribute('data-idupd');
           if(idProveedor > 0){   
            document.getElementById('idProveedorEditar').value = idProveedor;           
            obtenerDatosProveedor(idProveedor);
           }

         }else if(optionCRUD == 'b'){

           let idProveedor = event.target.getAttribute('data-iddel');
           if(idProveedor > 0){              
             $('#modalFormEliminarProveedor').modal('show');
             document.getElementById('idProveedorEliminar').value = idProveedor;
           }

         }
      
      } else {
         return
      }
    }


    let formCrearProveedor = document.getElementById('formulario_crear_proveedor');
    if(formCrearProveedor){
      formCrearProveedor.addEventListener('submit', function(e) {        
        e.preventDefault();
          let ruta = urlCompleta+'/Proveedores/crearProveedor';
          
          let data = `nombre_proveedor=${formCrearProveedor.nombre_proveedor.value}&nif_proveedor=${formCrearProveedor.nif_proveedor.value}&direccion_proveedor=${formCrearProveedor.direccion_proveedor.value}&poblacion_proveedor=${formCrearProveedor.poblacion_proveedor.value}&codigo_postal_proveedor=${formCrearProveedor.codigo_postal_proveedor.value}&provincia_proveedor=${formCrearProveedor.provincia_proveedor.value}&telefono_proveedor=${formCrearProveedor.telefono_proveedor.value}&email_proveedor=${formCrearProveedor.email_proveedor.value}&observaciones_proveedor=${formCrearProveedor.observaciones_proveedor.value}&estado_proveedor=${formCrearProveedor.estado_proveedor.value}`;          
  
          crearProveedor(data, ruta);
      });
    }

    const tablaProveedor = document.getElementById('tablaProveedores');
    if(tablaProveedor){
      document.getElementById('tablaProveedores').addEventListener('click', eventHandler);
    }    
   
    function crearProveedor(data, ruta){

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
                  window.location = urlCompleta+'/Proveedores';
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

    function obtenerDatosProveedor(idProveedor) {
      
      let ruta = urlCompleta+'/Proveedores/obtenerProveedor';
      let data = `id=${idProveedor}`;

      obtenerProveedorPorId(data, ruta);                                     

    }
       
    function obtenerProveedorPorId(data, ruta){

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
                let formulario_editar_proveedor = document.getElementById('formulario_editar_proveedor');                
                formulario_editar_proveedor.nombre_proveedor.value = datos.nombrefiscal;
                formulario_editar_proveedor.nif_proveedor.value = datos.nif;
                formulario_editar_proveedor.direccion_proveedor.value = datos.direccion;
                formulario_editar_proveedor.poblacion_proveedor.value = datos.poblacion;
                formulario_editar_proveedor.codigo_postal_proveedor.value = datos.codigopostal;
                formulario_editar_proveedor.provincia_proveedor.value = datos.provincia;
                formulario_editar_proveedor.telefono_proveedor.value = datos.telefono;
                formulario_editar_proveedor.email_proveedor.value = datos.email;
                formulario_editar_proveedor.observaciones_proveedor.value = datos.observaciones;
                formulario_editar_proveedor.estado_proveedor.value = datos.status;

                $('#modalFormEditProveedor').modal('show');     

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

    $('#modalFormEditProveedor').on('hidden.bs.modal', function () {
			$('#formulario_editar_proveedor').trigger("reset");			
		});   
    
    
    let formulario_editar_proveedor = document.getElementById('formulario_editar_proveedor');
    if(formulario_editar_proveedor){
      formulario_editar_proveedor.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_editar_proveedor.idProveedorEditar.value != ''){
                        
            let ruta = urlCompleta+'/Proveedores/actualizarProveedor';
            
            let data = `id=${formulario_editar_proveedor.idProveedorEditar.value}&nombre_proveedor=${formulario_editar_proveedor.nombre_proveedor.value}&nif_proveedor=${formulario_editar_proveedor.nif_proveedor.value}&direccion_proveedor=${formulario_editar_proveedor.direccion_proveedor.value}&poblacion_proveedor=${formulario_editar_proveedor.poblacion_proveedor.value}&codigo_postal_proveedor=${formulario_editar_proveedor.codigo_postal_proveedor.value}&provincia_proveedor=${formulario_editar_proveedor.provincia_proveedor.value}&telefono_proveedor=${formulario_editar_proveedor.telefono_proveedor.value}&email_proveedor=${formulario_editar_proveedor.email_proveedor.value}&observaciones_proveedor=${formulario_editar_proveedor.observaciones_proveedor.value}&estado_proveedor=${formulario_editar_proveedor.estado_proveedor.value}`;

            actualizarDatosProveedor(data, ruta);
          }          
      });
    }

       
    function actualizarDatosProveedor(data, ruta){

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
                  window.location = urlCompleta+'/Proveedores';
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
    

    let formulario_eliminar_proveedor = document.getElementById('formulario_eliminar_proveedor');
    if(formulario_eliminar_proveedor){
      formulario_eliminar_proveedor.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_eliminar_proveedor.idProveedorEliminar.value != ''){
            
            $('#modalFormEliminarProveedor').modal('hide');
            let ruta = urlCompleta+'/Proveedores/eliminarProveedor';
            let data = `id=${formulario_eliminar_proveedor.idProveedorEliminar.value}`;
            borrarProveedorPorId(data, ruta);
          }          
      });
    }

    function borrarProveedorPorId(data, ruta){

      console.log('data borrar=> ',data);
      console.log('ruta borrar=> ',ruta);
      let xhr = new XMLHttpRequest();
      xhr.open("POST", ruta, true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(          
        data
      );
      xhr.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              let respuesta = this.responseText;

              console.log('respuesta1=>', respuesta)
              respuesta = JSON.parse(respuesta);     
              console.log('respuesta2=>', respuesta)

              let texto = respuesta.mensaje;    
              if(respuesta.error == false){                                
                Swal.fire({
                  title: 'Procés correcte',
                  text: texto,
                  icon: 'success',                  
                  timer: 3000,
                });
                setTimeout(function () {
                  window.location = urlCompleta+'/Proveedores';
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
