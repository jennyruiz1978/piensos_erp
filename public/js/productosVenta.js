import DB from './fecth.js';

document.addEventListener("DOMContentLoaded", () => {    
  


  if (window.location.pathname.includes("/ProductosVenta")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']
      if (opcionesControladas.includes(optionCRUD)) {

          let tipo = asignarTipoProducto(); 

         if (optionCRUD == 'c') {
          document.getElementById('modalTitle').innerHTML = 'Crear producte de venda';
          document.getElementById('id').value = '';
          $('#modalFormEditarProducto').modal('show');            
          if(tipo == 'venta'){
            //document.getElementById('cont_prod_compra').style.display = "block";
            document.getElementById('cont_prod_compra').style.display = "none";
          }else{
            document.getElementById('cont_prod_compra').style.display = "none";
          }                  

         }else if(optionCRUD == 'u'){

          let idProducto = event.target.getAttribute('data-idupd');
          if(idProducto > 0){                    

            let ruta = urlCompleta+'/ProductosVenta/obtenerProducto';      
            let params = {'id' : idProducto};
  
            let fetch=new DB(ruta, 'POST').get(params);
            fetch.then((data => {            
  
              let form = document.getElementById('formulario_editar_producto');  
              form.id.value = idProducto;
              form.descripcion.value = data.datos.descripcion;
              form.iva.value = data.datos.iva;
              form.idunidad.value = data.datos.idunidad;  
              form.idproductocompra.value = data.datos.idproductocompra; 
              
              document.getElementById('modalTitle').innerHTML = 'Ver producte de venda';
              if(tipo == 'venta'){
                //document.getElementById('cont_prod_compra').style.display = "block";
                document.getElementById('cont_prod_compra').style.display = "none";
              }else{
                document.getElementById('cont_prod_compra').style.display = "none";
              }
              $('#modalFormEditarProducto').modal('show');
            }))                 

          }   

         }
      
      } else {
         return
      }
    }

    const tablaProductosVenta = document.getElementById('tablaProductosVenta');
    if(tablaProductosVenta){
      document.getElementById('tablaProductosVenta').addEventListener('click', eventHandler);
    }  

    function asignarTipoProducto(){
      let tipo = '';
      if(document.getElementById('tablaProductosVenta')){
        tipo = 'venta';
      }else if(document.getElementById('tablaProductosCompra')){
        tipo = 'compra';
      }
      return tipo;
    }

    $('#modalFormEditarProducto').on('hidden.bs.modal', function () {
			$('#formulario_editar_producto').trigger("reset");			
      document.getElementById('cont_prod_compra').style.display = "none";
		}); 

    
    let formulario_editar_producto = document.getElementById('formulario_editar_producto');
    if(formulario_editar_producto){
      formulario_editar_producto.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/ProductosVenta/crearActualizarProducto';
          let datosForm = new FormData(formulario_editar_producto);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                       

            limpiarMensajesCamposError();            

            let url = '';            
            if(respuesta.error==false){
              url = urlCompleta+'/ProductosVenta';
              document.getElementById('id').value = respuesta.id;
              document.getElementById('modalTitle').innerHTML = 'Ver producto venda '+respuesta.id;             

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
