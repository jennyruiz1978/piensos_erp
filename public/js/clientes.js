document.addEventListener("DOMContentLoaded", () => {    

  if (window.location.pathname.includes("/Clientes")) {
        
    var urlCompleta = $('#ruta').val();
    
    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']
      if (opcionesControladas.includes(optionCRUD)) {

         if (optionCRUD == 'c') {                                 
           
           window.location.href = urlCompleta+"/Clientes/altaClientes";

         }else if(optionCRUD == 'u'){

           let idCliente = event.target.getAttribute('data-idupd');
           if(idCliente > 0){   
            document.getElementById('idClienteEditar').value = idCliente;           
            obtenerDatosCliente(idCliente);
           }

         }else if(optionCRUD == 'b'){

           let idCliente = event.target.getAttribute('data-iddel');
           if(idCliente > 0){              
             $('#modalFormEliminarCliente').modal('show');
             document.getElementById('idClienteEliminar').value = idCliente;
           }

         }
      
      } else {
         return
      }
    }


    let formCrearCliente = document.getElementById('formulario_crear_cliente');
    if(formCrearCliente){
      formCrearCliente.addEventListener('submit', function(e) {        
        e.preventDefault();
          let ruta = urlCompleta+'/Clientes/crearCliente';

          
          // Obtener los contactos del componente
          let contactosData = document.querySelector('.contactos-emails').value;
          
          let data = `nombre_cliente=${formCrearCliente.nombre_cliente.value}&nif_cliente=${formCrearCliente.nif_cliente.value}&direccion_cliente=${formCrearCliente.direccion_cliente.value}&poblacion_cliente=${formCrearCliente.poblacion_cliente.value}&codigo_postal_cliente=${formCrearCliente.codigo_postal_cliente.value}&provincia_cliente=${formCrearCliente.provincia_cliente.value}&telefono_cliente=${formCrearCliente.telefono_cliente.value}&email_cliente=${formCrearCliente.email_cliente.value}&observaciones_cliente=${formCrearCliente.observaciones_cliente.value}&estado_cliente=${formCrearCliente.estado_cliente.value}&zona_cliente=${formCrearCliente.zona_cliente.value}&precio_cliente=${formCrearCliente.precio_cliente.value}&formacobro=${formCrearCliente.formacobro.value}&contactos=${encodeURIComponent(contactosData)}`;        
  
          crearCliente(data, ruta);
      });
    }

    const tablaCliente = document.getElementById('tablaClientes');
    if(tablaCliente){
      document.getElementById('tablaClientes').addEventListener('click', eventHandler);
    }    

 
   
    function crearCliente(data, ruta){

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
                  window.location = urlCompleta+'/Clientes';
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

    function obtenerDatosCliente(idCliente) {
      
      let ruta = urlCompleta+'/Clientes/obtenerCliente';
      let data = `id=${idCliente}`;

      obtenerClientePorId(data, ruta);                                     

    }
       
    function obtenerClientePorId(data, ruta){

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
                let formulario_editar_cliente = document.getElementById('formulario_editar_cliente');                
                formulario_editar_cliente.nombre_cliente.value = datos.nombrefiscal;
                formulario_editar_cliente.nif_cliente.value = datos.nif;
                formulario_editar_cliente.direccion_cliente.value = datos.direccion;
                formulario_editar_cliente.poblacion_cliente.value = datos.poblacion;
                formulario_editar_cliente.codigo_postal_cliente.value = datos.codigopostal;
                formulario_editar_cliente.provincia_cliente.value = datos.provincia;
                formulario_editar_cliente.telefono_cliente.value = datos.telefono;
                formulario_editar_cliente.email_cliente.value = datos.email;
                formulario_editar_cliente.observaciones_cliente.value = datos.observaciones;
                formulario_editar_cliente.estado_cliente.value = datos.status;
                formulario_editar_cliente.zona_cliente.value = datos.idzona;
                formulario_editar_cliente.precio_cliente.value = datos.precio;
                if (datos.formacobro !== null && datos.formacobro !== "") {
                  formulario_editar_cliente.formacobro.value = datos.formacobro;
                } else {
                    formulario_editar_cliente.formacobro.value = ""; 
                }

               // Inicializar contactos si existen
               const contactosContainer = document.querySelector('.contactos-component');
               const contactosManager = new ContactosManager(contactosContainer);
               
               if(datos.contactos) {
                   try {
                       const contactosData = JSON.parse(datos.contactos);
                       contactosManager.setContactos(contactosData);
                   } catch(e) {
                       console.error('Error al cargar contactos:', e);
                   }
               }

                $('#modalFormEditCliente').modal('show');     

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

    $('#modalFormEditCliente').on('hidden.bs.modal', function () {
			$('#formulario_editar_cliente').trigger("reset");			
      document.getElementById('lista-contactos').innerHTML = '';		
		});   
    
    
    let formulario_editar_cliente = document.getElementById('formulario_editar_cliente');
    if(formulario_editar_cliente){
      formulario_editar_cliente.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_editar_cliente.idClienteEditar.value != ''){
                        
            let ruta = urlCompleta+'/Clientes/actualizarCliente';

             /* // Obtener todos los contactos actualizados antes de crear el data string             
             let contactosData = document.querySelector('.contactos-emails').value; */


            // Recoger todos los contactos manualmente de los inputs
            let contactos = [];
            const contactosItems = document.querySelectorAll('.contacto-item');
            
            contactosItems.forEach(item => {
                const nombreInput = item.querySelector('.nombre-contacto');
                const emailInput = item.querySelector('.email-contacto');
                
                if(nombreInput && emailInput) {
                    contactos.push({
                        nombre: nombreInput.value.trim(),
                        email: emailInput.value.trim()
                    });
                }
            });             
            
            let data = `id=${formulario_editar_cliente.idClienteEditar.value}&nombre_cliente=${formulario_editar_cliente.nombre_cliente.value}&nif_cliente=${formulario_editar_cliente.nif_cliente.value}&direccion_cliente=${formulario_editar_cliente.direccion_cliente.value}&poblacion_cliente=${formulario_editar_cliente.poblacion_cliente.value}&codigo_postal_cliente=${formulario_editar_cliente.codigo_postal_cliente.value}&provincia_cliente=${formulario_editar_cliente.provincia_cliente.value}&telefono_cliente=${formulario_editar_cliente.telefono_cliente.value}&email_cliente=${formulario_editar_cliente.email_cliente.value}&observaciones_cliente=${formulario_editar_cliente.observaciones_cliente.value}&estado_cliente=${formulario_editar_cliente.estado_cliente.value}&zona_cliente=${formulario_editar_cliente.zona_cliente.value}&precio_cliente=${formulario_editar_cliente.precio_cliente.value}&formacobro=${formulario_editar_cliente.formacobro.value}&contactos=${encodeURIComponent(JSON.stringify(contactos))}`;            

            actualizarDatosCliente(data, ruta);
          }          
      });
    }

       
    function actualizarDatosCliente(data, ruta){     
      
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
                  window.location = urlCompleta+'/Clientes';
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
    

    let formulario_eliminar_cliente = document.getElementById('formulario_eliminar_cliente');
    if(formulario_eliminar_cliente){
      formulario_eliminar_cliente.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_eliminar_cliente.idClienteEliminar.value != ''){
            
            $('#modalFormEliminarCliente').modal('hide');
            let ruta = urlCompleta+'/Clientes/eliminarCliente';
            let data = `id=${formulario_eliminar_cliente.idClienteEliminar.value}`;
            borrarClientePorId(data, ruta);
          }          
      });
    }

    function borrarClientePorId(data, ruta){

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
                  window.location = urlCompleta+'/Clientes';
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
