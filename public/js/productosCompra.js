import DB from './fecth.js';

document.addEventListener("DOMContentLoaded", () => {    
  


  if (window.location.pathname.includes("/ProductosCompra")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']
      if (opcionesControladas.includes(optionCRUD)) {

         if (optionCRUD == 'c') {
          document.getElementById('modalTitle').innerHTML = 'Crear producte de compra';
          document.getElementById('id').value = '';
          $('#modalFormEditarProducto').modal('show');                   

         }else if(optionCRUD == 'u'){

          let idProducto = event.target.getAttribute('data-idupd');
          if(idProducto > 0){                    

            let ruta = urlCompleta+'/ProductosCompra/obtenerProducto';      
            let params = {'id' : idProducto};
  
            let fetch=new DB(ruta, 'POST').get(params);
            fetch.then((data => {            
  
              let form = document.getElementById('formulario_editar_producto');  
              form.id.value = idProducto;
              form.descripcion.value = data.datos.descripcion;
              form.iva.value = data.datos.iva;
              form.idunidad.value = data.datos.idunidad;  
              
              document.getElementById('modalTitle').innerHTML = 'Ver producte de compra';
              $('#modalFormEditarProducto').modal('show');
            }))                 

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

    const tablaProductosCompra = document.getElementById('tablaProductosCompra');
    if(tablaProductosCompra){
      document.getElementById('tablaProductosCompra').addEventListener('click', eventHandler);
    }  

    $('#modalFormEditarProducto').on('hidden.bs.modal', function () {
			$('#formulario_editar_producto').trigger("reset");			
		}); 

    
    let formulario_editar_producto = document.getElementById('formulario_editar_producto');
    if(formulario_editar_producto){
      formulario_editar_producto.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/ProductosCompra/crearActualizarProducto';
          let datosForm = new FormData(formulario_editar_producto);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                       

            limpiarMensajesCamposError();            

            let url = '';            
            if(respuesta.error==false){
              url = urlCompleta+'/ProductosCompra';
              document.getElementById('id').value = respuesta.id;
              document.getElementById('modalTitle').innerHTML = 'Ver producto compra '+respuesta.id;             

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
    
  }    

});
