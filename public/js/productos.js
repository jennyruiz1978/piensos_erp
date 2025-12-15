

document.addEventListener("DOMContentLoaded", () => {    
  
  if (window.location.pathname.includes("/Productos")) {        

    var urlCompleta = $('#ruta').val();

    let formCompra = document.getElementById('formulario_producto_compra');
    if(formCompra){
      formCompra.addEventListener('submit', function(e) {        
        e.preventDefault();
          let ruta = urlCompleta+'/Productos/actualizarProductoCompra';
          let data = `nombre_prod_compra=${formCompra.nombre_prod_compra.value}&iva_compras=${formCompra.iva_compras.value}&id_prod_compra=${formCompra.id_prod_compra.value}`;
          callMainBackEnd(data, ruta);
      }); 
    }

    
    let formVenta = document.getElementById('formulario_producto_venta');
    if(formVenta){

      formVenta.addEventListener('submit', function(e) {
        
        e.preventDefault();
          let ruta = urlCompleta+'/Productos/actualizarProductoVenta';
          let data = `nombre_prod_venta=${formVenta.nombre_prod_venta.value}&iva_ventas=${formVenta.iva_ventas.value}&id_prod_venta=${formVenta.id_prod_venta.value}`;
          callMainBackEnd(data, ruta);   
      });
  
    }

    
    function callMainBackEnd(data, ruta){

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
              let title = 'Error';                  
              let icono = 'error';     
              let confirmButtonTexto = 'Tancar';
              if(respuesta.error == false){
                title = 'Proceso correcto';                  
                icono = 'success';
                confirmButtonTexto = 'Ok';    
              }
              Swal.fire({
                title: title,
                text: texto,
                icon: icono,
                confirmButtonText: confirmButtonTexto
              });                

          }
      };      

    }
    



  }    

});
