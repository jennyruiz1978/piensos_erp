document.addEventListener("DOMContentLoaded", () => {    

  if (window.location.pathname.includes("/Usuarios")) {
        
    var urlCompleta = $('#ruta').val();

    
    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']
      if (opcionesControladas.includes(optionCRUD)) {

         if (optionCRUD == 'c') {                                 
           
           window.location.href = urlCompleta+"/Usuarios/altaUsuarios";

         }else if(optionCRUD == 'u'){

           let idUsuario = event.target.getAttribute('data-idupd');
           if(idUsuario > 0){   
            document.getElementById('idUsuarioEditar').value = idUsuario;           
            obtenerDatosUsuario(idUsuario);
           }

         }else if(optionCRUD == 'b'){

           let idUsuario = event.target.getAttribute('data-iddel');
           if(idUsuario > 0){              
             $('#modalFormEliminarUsuario').modal('show');
             document.getElementById('idUsuarioEliminar').value = idUsuario;
           }

         }
      
      } else {
         return
      }
    }


    let formCrearUsuario = document.getElementById('formulario_crear_usuario');
    if(formCrearUsuario){
      formCrearUsuario.addEventListener('submit', function(e) {        
        e.preventDefault();
          let ruta = urlCompleta+'/Usuarios/crearUsuario';
          
          let data = `nombre_usuario=${formCrearUsuario.nombre_usuario.value}&apellidos_usuario=${formCrearUsuario.apellidos_usuario.value}&email_usuario=${formCrearUsuario.email_usuario.value}&password_usuario=${formCrearUsuario.password_usuario.value}&rol_usuario=${formCrearUsuario.rol_usuario.value}&estado_usuario=${formCrearUsuario.estado_usuario.value}`;
  
          crearUsuario(data, ruta);
      });
    }

    const tablaUsuario = document.getElementById('tablaUsuarios');
    if(tablaUsuario){
      document.getElementById('tablaUsuarios').addEventListener('click', eventHandler);
    }    
   
    function crearUsuario(data, ruta){

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
                  window.location = urlCompleta+'/Usuarios';
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

    function obtenerDatosUsuario(idUsuario) {
      
      let ruta = urlCompleta+'/Usuarios/obtenerUsuario';
      let data = `id=${idUsuario}`;

      obtenerUsuarioPorId(data, ruta);                                     

    }
       
    function obtenerUsuarioPorId(data, ruta){

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
                let formulario_editar_usuario = document.getElementById('formulario_editar_usuario');

                formulario_editar_usuario.nombre_usuario.value = datos.user_name;                
                formulario_editar_usuario.apellidos_usuario.value = datos.apellidos;
                formulario_editar_usuario.email_usuario.value = datos.email;
                formulario_editar_usuario.password_usuario.value = datos.pass;
                formulario_editar_usuario.rol_usuario.value = datos.idRol;
                formulario_editar_usuario.estado_usuario.value = datos.status;

                $('#modalFormEditUsuario').modal('show');     

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

    $('#modalFormEditUsuario').on('hidden.bs.modal', function () {
			$('#formulario_editar_usuario').trigger("reset");			
		});   
    
    
    let formulario_editar_usuario = document.getElementById('formulario_editar_usuario');
    if(formulario_editar_usuario){
      formulario_editar_usuario.addEventListener('submit', function(e) {        
        console.log('id ditar', formulario_editar_usuario.idUsuarioEditar.value);
          e.preventDefault();
          if(formulario_editar_usuario.idUsuarioEditar.value != ''){
                        
            let ruta = urlCompleta+'/Usuarios/actualizarUsuario';

            let data = `id=${formulario_editar_usuario.idUsuarioEditar.value}&nombre_usuario=${formulario_editar_usuario.nombre_usuario.value}&apellidos_usuario=${formulario_editar_usuario.apellidos_usuario.value}&email_usuario=${formulario_editar_usuario.email_usuario.value}&password_usuario=${formulario_editar_usuario.password_usuario.value}&rol_usuario=${formulario_editar_usuario.rol_usuario.value}&estado_usuario=${formulario_editar_usuario.estado_usuario.value}`;
            
            actualizarDatosUsuario(data, ruta);
          }          
      });
    }

       
    function actualizarDatosUsuario(data, ruta){

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
                  window.location = urlCompleta+'/Usuarios';
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
    

    let formulario_eliminar_usuario = document.getElementById('formulario_eliminar_usuario');
    if(formulario_eliminar_usuario){
      formulario_eliminar_usuario.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_eliminar_usuario.idUsuarioEliminar.value != ''){
            
            $('#modalFormEliminarUsuario').modal('hide');
            let ruta = urlCompleta+'/Usuarios/eliminarUsuario';
            let data = `id=${formulario_eliminar_usuario.idUsuarioEliminar.value}`;
            borrarUsuarioPorId(data, ruta);
          }          
      });
    }

    function borrarUsuarioPorId(data, ruta){

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
                  window.location = urlCompleta+'/Usuarios';
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
