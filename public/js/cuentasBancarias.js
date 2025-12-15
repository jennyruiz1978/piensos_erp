import DB from './fecth.js';

document.addEventListener("DOMContentLoaded", () => {    
  


  if (window.location.pathname.includes("/CuentasBancarias")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']
      if (opcionesControladas.includes(optionCRUD)) {

         if (optionCRUD == 'c') {
          document.getElementById('modalTitle').innerHTML = 'Crear cuenta bancaria';
          document.getElementById('id').value = '';
          $('#modalFormEditarCuenta').modal('show');                   

         }else if(optionCRUD == 'u'){

          let idCuenta = event.target.getAttribute('data-idupd');
          if(idCuenta > 0){                    

            let ruta = urlCompleta+'/CuentasBancarias/obtenerCuenta';
            let params = {'id' : idCuenta};
  
            let fetch=new DB(ruta, 'POST').get(params);
            fetch.then((data => {            
  
              let form = document.getElementById('formulario_editar_cuenta');  
              form.id.value = idCuenta;
              form.numerocuenta.value = data.datos.numerocuenta;
              form.banco.value = data.datos.banco;              
              
              document.getElementById('modalTitle').innerHTML = 'Ver cuenta bancaria';
              $('#modalFormEditarCuenta').modal('show');
            }))                 

          }   

         }else if(optionCRUD == 'b'){

           let idCuenta = event.target.getAttribute('data-iddel');
           if(idCuenta > 0){                           
  
              let ruta = urlCompleta+'/CuentasBancarias/consultarCuentaBancariaParaEliminar'; 
              let params = {'id' : idCuenta};
  
              let fetch=new DB(ruta, 'POST').get(params);
              fetch.then((data => {   

                if(data.error==false){                                             
                  
                  document.getElementById('mensaje_eliminar_cuenta').innerHTML = data.mensaje;                  
                  document.getElementById('idCuentaEliminar').value = idCuenta;
                  $('#modalFormEliminarCuenta').modal('show');

                }else{
                  alert(data.mensaje);                            
                }
  
              }))
                
           }

         }
      
      } else {
         return
      }
    }

    const tablaCuentasBancarias = document.getElementById('tablaCuentasBancarias');
    if(tablaCuentasBancarias){
      document.getElementById('tablaCuentasBancarias').addEventListener('click', eventHandler);
    }  

    $('#modalFormEditarCuenta').on('hidden.bs.modal', function () {
			$('#formulario_editar_cuenta').trigger("reset");			
		}); 

    
    let formulario_editar_cuenta = document.getElementById('formulario_editar_cuenta');
    if(formulario_editar_cuenta){
      formulario_editar_cuenta.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/CuentasBancarias/crearActualizarCuentaBancaria';
          let datosForm = new FormData(formulario_editar_cuenta);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                       

            limpiarMensajesCamposError();            

            let url = '';            
            if(respuesta.error==false){
              url = urlCompleta+'/CuentasBancarias';
              document.getElementById('id').value = respuesta.id;
              document.getElementById('modalTitle').innerHTML = 'Ver cuenta bancaria '+respuesta.id;             

            }else{

              if(respuesta.fieldsValidate && respuesta.fieldsValidate.length > 0){                
                for (let index = 0; index < respuesta.fieldsValidate.length; index++) {                    
                  document.getElementById('error_'+respuesta.fieldsValidate[index]).innerHTML = 'Aquest camp és obligatori';
                }
              }
            }  
            
            mostrarResultadoFetchRecargar(respuesta, 0, url);

          }))          
      });
    } 

    function limpiarMensajesCamposError(){
      let spans = document.querySelectorAll('.mensaje_required');     
      spans.forEach(function(elemento, index, arreglo) {        
        elemento.innerHTML = '';
      });            
    }

    function mostrarResultadoFetchRecargar(respuesta, eliminarFila=false, url=false) {
      let texto = respuesta.mensaje;    
      let confirmButtonTexto = 'Tancar';
      if(respuesta.error == false){                                
        Swal.fire({
          title: 'Procés correcte',
          text: texto,
          icon: 'success',
          confirmButtonText: confirmButtonTexto          
        });
        if(eliminarFila==0){
          setTimeout(function () {
            window.location = url;
          },2000);
        }
      }else{                                
        Swal.fire({
          title: 'Error',
          text: texto,
          icon: 'error',
          confirmButtonText: confirmButtonTexto
        });                  
      }
    }

    let formulario_eliminar_cuenta = document.getElementById('formulario_eliminar_cuenta');
    if(formulario_eliminar_cuenta){
      formulario_eliminar_cuenta.addEventListener('submit', function(e) {                
          e.preventDefault();               

          let idCuentaDel = document.getElementById('idCuentaEliminar').value;
          $('#modalFormEliminarCuenta').modal('hide');
                            
          let ruta = urlCompleta+'/CuentasBancarias/eliminarCuenta';
          let datosForm = new FormData(formulario_eliminar_cuenta);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);
          
          fetch.then((respuesta => {               

            let icono = '';
            let titulo = '';
            if(respuesta.error==false){    
              titulo = 'Procés correcte';
              icono = 'success';      
              eliminarFilaTabla(idCuentaDel)
            }else{
              icono = 'error';    
              titulo = 'Error';
            }

            Swal.fire({
              title: titulo,
              text: respuesta.mensaje,
              icon: icono,
              confirmButtonText: 'OK'
            });  

            
          }))
          
      });
    }

    function eliminarFilaTabla(id) {
      let idEliminar = 'fila_'+id;
      const element = document.getElementById(idEliminar);
      element.remove();
    }  
    
  }    

});
