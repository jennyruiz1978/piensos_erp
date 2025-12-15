import DB from './fecth.js';
import RowsGrid from './rows_grilla.js';

document.addEventListener("DOMContentLoaded", () => {    
  


  if (window.location.pathname.includes("/AlbaranesProveedores")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']      
      if (opcionesControladas.includes(optionCRUD)) {      

         if (optionCRUD == 'c') {

           window.location.href = urlCompleta+"/AlbaranesProveedores/altaAlbaranes";

         }else if(optionCRUD == 'u'){

           let idAlbaran = event.target.getAttribute('data-idupd');
           if(idAlbaran > 0){   
            window.location = urlCompleta+'/AlbaranesProveedores/verAlbaran/'+idAlbaran;            
           }

          }else if(optionCRUD == 'b'){

            let idAlbaran = event.target.getAttribute('data-iddel');
            if(idAlbaran > 0){              
              $('#modalFormEliminarAlbaran').modal('show');
              document.getElementById('idAlbaranEliminar').value = idAlbaran;
              document.getElementById('eliminarFila').value = 1;              
            }
 
          }
      
      } else {
         return
      }
    }

    const tablaAlbaranesProveedores = document.getElementById('tablaAlbaranesProveedores');
    if(tablaAlbaranesProveedores){
      document.getElementById('tablaAlbaranesProveedores').addEventListener('click', eventHandler);
    }    

    const proveedor_select = document.getElementById('idproveedor');    
    if(proveedor_select){

      proveedor_select.addEventListener('change', function () {
        
        let proveedor_selected = proveedor_select.value;            
       
        
        if(proveedor_selected != ''){          

          let ruta = urlCompleta+'/Proveedores/obtenerProveedor'; 
          let params = {'id' : proveedor_selected};

          let proveedores=new DB(ruta, 'POST').get(params);
          proveedores.then((data => {            
            document.getElementById('nif_albaran').value = data.datos.nif;
          }))

        }

      });      
    }

    let formCrearAlbaranProveedor = document.getElementById('formulario_crear_albaran_prov');
    if(formCrearAlbaranProveedor){
      formCrearAlbaranProveedor.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/AlbaranesProveedores/actualizarAlbaranCompleto';
          let datosForm = new FormData(formCrearAlbaranProveedor);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                                   
            limpiarMensajesCamposError();
            mostrarResultadoFetch(respuesta);
            
            if(respuesta.error==false){
              let tableId = document.getElementById('tablaGrilla');                        
              let tBody = tableId.getElementsByTagName('tbody')[0];            
              tBody.innerHTML = respuesta.html;  
              
              document.getElementById('baseimponible_importe').innerHTML = respuesta.baseimponible;
              document.getElementById('ivatotal_importe').innerHTML = respuesta.ivatotal;
              document.getElementById('total_importe').innerHTML = respuesta.total;     
              document.getElementById('numero').value = respuesta.numero;         

            }else{

              if(respuesta.fieldsValidate && respuesta.fieldsValidate.length > 0){                
                for (let index = 0; index < respuesta.fieldsValidate.length; index++) {                    
                  document.getElementById('error_'+respuesta.fieldsValidate[index]).innerHTML = 'Aquest camp és obligatori';
                }
              }

            }

            
          }))

          
      });
    } 

    function limpiarMensajesCamposError(){
      let spans = document.querySelectorAll('.mensaje_required');     
      spans.forEach(function(elemento, index, arreglo) {        
        elemento.innerHTML = '';
      });
            
    }

    function mostrarResultadoFetch(respuesta) {
      let texto = respuesta.mensaje;    
      let confirmButtonTexto = 'Tancar';
      if(respuesta.error == false){                                
        Swal.fire({
          title: 'Procés correcte',
          text: texto,
          icon: 'success',
          confirmButtonText: confirmButtonTexto          
        });        
      }else{                                
        Swal.fire({
          title: 'Error',
          text: texto,
          icon: 'error',
          confirmButtonText: confirmButtonTexto
        });                  
      }
    }

    function mostrarResultadoFetchEliminar(respuesta, eliminarFila=false, url=false) {
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
            
    let formulario_eliminar_albaran = document.getElementById('formulario_eliminar_albaran');
    if(formulario_eliminar_albaran){
      formulario_eliminar_albaran.addEventListener('submit', function(e) {                
          e.preventDefault();               
          $('#modalFormEliminarAlbaran').modal('hide');
                            
          let ruta = urlCompleta+'/AlbaranesProveedores/eliminarAlbaran';
          let datosForm = new FormData(formulario_eliminar_albaran);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);
          
          fetch.then((respuesta => {   

            let rutaIni = urlCompleta+'/AlbaranesProveedores'; 

            if(respuesta.mensaje){
              if(document.getElementById('eliminarFila').value == 1){
                eliminarFilaTabla(formulario_eliminar_albaran.idAlbaranEliminar.value);
                mostrarResultadoFetch(respuesta);
              }else{
                mostrarResultadoFetchEliminar(respuesta, document.getElementById('eliminarFila').value, rutaIni);
              }                               
            }else{
              mostrarResultadoFetch(respuesta);
            }
            
          }))
          
      });
    }

    function eliminarFilaTabla(idAlbaran) {
        let idEliminar = 'fila_'+idAlbaran;
        const element = document.getElementById(idEliminar);
        element.remove();
    }

    let btn_eliminar_albaran = document.getElementById('eliminar_albaran');
    if(btn_eliminar_albaran){
      btn_eliminar_albaran.addEventListener('click', function () {
        let id = document.getElementById('id').value;
        document.getElementById('idAlbaranEliminar').value = id;
        document.getElementById('eliminarFila').value = 0;  
        $('#modalFormEliminarAlbaran').modal('show');
      });       
    }

    
    let btn_generar_factura_proveedor = document.getElementById('generar_factura_proveedor');
    if(btn_generar_factura_proveedor){
      btn_generar_factura_proveedor.addEventListener('click', function () {
        let idAlbaran = document.getElementById('id').value;        
        document.getElementById('idAlbaranProveedor').value = idAlbaran;

        let ruta = urlCompleta+'/AlbaranesProveedores/obtenerDatosAlbaran'; 
        let params = {'id' : idAlbaran};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          if(data.error == false){
            document.getElementById('nombre_proveedor').value = data.cabecera.proveedor;
            document.getElementById('nif_proveedor').value = data.cabecera.nif;                       
            document.getElementById('fecha_factura_proveedor').value = data.cabecera.fecha;
            document.getElementById('numero_albaran_proveedor').value = data.cabecera.numero;
            document.getElementById('observaciones_factura_proveedor').value = data.cabecera.observaciones;            
            
            let tableFact = document.getElementById('tablaGrillaFactura');                        
            let tBody = tableFact.getElementsByTagName('tbody')[0];            
            tBody.innerHTML = data.detalle;

            $('#modalFormCrearFacturaProveedor').modal('show');
          }       
        }))        
      });       
    }

    $('#modalFormCrearFacturaProveedor').on('hidden.bs.modal', function () {
			$('#formulario_crear_factura_proveedor').trigger("reset");			
		});     

    let formCrearFacturaProveedor = document.getElementById('formulario_crear_factura_proveedor');
    if(formCrearFacturaProveedor){
      formCrearFacturaProveedor.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/FacturasProveedores/crearFacturaProveedor';
          let datosForm = new FormData(formCrearFacturaProveedor);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {

            limpiarMensajesCamposError();

            let numerofactura = 0;
            let url = '';        

            if(respuesta.error==false){              
              numerofactura = respuesta.idfactura;
              url = urlCompleta+'/FacturasProveedores/verFactura/'+numerofactura;
            }else{
              if(respuesta.fieldsValidate && respuesta.fieldsValidate.length > 0){                
                for (let index = 0; index < respuesta.fieldsValidate.length; index++) {        
                  let textAdd = '';
                  if(respuesta.fieldsValidate[index] == 'diaspago'){
                    textAdd = '. Mínim zero';
                  }            
                  document.getElementById('error_'+respuesta.fieldsValidate[index]).innerHTML = 'Aquest camp és obligatori'+textAdd;
                }
              }
            }
            
            mostrarResultadoFetchEliminar(respuesta, 0, url);
            
          }))

          
      });
    } 
    
    let agregar_linea = document.getElementById('agregar_linea');
    if(agregar_linea){
      agregar_linea.addEventListener('click', function () {
                
        let ruta = urlCompleta+'/AlbaranesProveedores/obtenerDatosParaFilaNueva';   
        let params = {};                       
        let fetch=new DB(ruta, 'POST').get(params);

        fetch.then((data => {             
          let params = {'tbody':'tablaGrillaBody', 'productos':data.productos, 'tiposivas':data.tiposIva, 'productdefault':data.default};
          let rows=new RowsGrid().get(params);
          rows.then((respuesta => {              
            document.getElementById('tablaGrillaBody').insertAdjacentHTML('beforeend',respuesta);            
          }))
          

        }))

      });       
    }

    let tablaGrilla = document.getElementById('tablaGrilla');   
    if(tablaGrilla){
      tablaGrilla.addEventListener("click", (event) => {
      
        const clickedElement = event.target;

        if (clickedElement.matches('.eliminar_fila')) { 
          
          let idFila= clickedElement.dataset.idfila;

          if(document.getElementById('idFila'+idFila)){

            let idDetalle = document.getElementById('idFila'+idFila).value;            
            let bool = confirm("Esteu segur(a) d'eliminar el producte?");
            
            if(bool){        
            
              let ruta = urlCompleta+'/AlbaranesProveedores/eliminarFilaDetalle'; 
              let params = {'idFila' : idDetalle, 'idAlbaran' : document.getElementById('id').value};
    
              let fetch=new DB(ruta, 'POST').get(params);
              fetch.then((data => {            
                
                if(data.error == false){
                  const filaSelected = document.getElementById('fila_grilla_id_'+idFila);
                  filaSelected.remove();    
                  document.getElementById('baseimponible_importe').innerHTML = data.datos.baseimponible;
                  document.getElementById('ivatotal_importe').innerHTML = data.datos.ivatotal;
                  document.getElementById('total_importe').innerHTML = data.datos.total;    

                }else{
                  Swal.fire({
                    title: 'Error',
                    text: data.mensaje,
                    icon: 'error',
                    confirmButtonText: 'Tancar'
                  });  
                }

              }))            
              
            }

          }else{
            const filaSelected = document.getElementById('fila_grilla_id_'+idFila);
            filaSelected.remove(); 
          }

        }
                            
      });
    }

    //eventos change y keyup para cálculo de días de vencimiento y fecha de vencimiento
    const dias_albaran_proveedor = document.getElementById('dias_albaran_proveedor');    
    if(dias_albaran_proveedor){

      dias_albaran_proveedor.addEventListener('keyup', function () {                        
                
          let diasCobro = 0;
          if(this.value.trim() != ''){
            diasCobro = this.value.trim();
          }                        
          let fechaFactura = document.getElementById('fecha_factura_proveedor').value;

          let ruta = urlCompleta+'/FacturasProveedores/calcularFechaVencimientoFacturaProveedor';         
          let params = {'dias' : diasCobro, 'fecha' : fechaFactura};

          let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {                       
            document.getElementById('vencimiento').value = data.fechaVecimiento;       
          }))

        
      });      
    }

    const fecha_vencimiento_prov = document.getElementById('vencimiento');    
    if(fecha_vencimiento_prov){
           
      fecha_vencimiento_prov.addEventListener('change', function () {                        
        
        let fecha_vencimiento_prov = '';
        if(this.value.trim() != ''){
          fecha_vencimiento_prov = this.value.trim();
        }                                  
        let fecha_factura_proveedor = document.getElementById('fecha_factura_proveedor').value;
        

        let ruta = urlCompleta+'/FacturasProveedores/calcularDiasCobroFacturaProveedor';         
        let params = {'vencimiento' : fecha_vencimiento_prov, 'fecha_factura_proveedor':fecha_factura_proveedor};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {                       
          document.getElementById('dias_albaran_proveedor').value = data.dias_albaran_proveedor;       
        }))        
    }); 

    }

    const fecha_factura = document.getElementById('fecha_factura_proveedor');    
    if(fecha_factura){
           
      fecha_factura.addEventListener('change', function () {                        
        
        let dias_albaran_proveedor = document.getElementById('dias_albaran_proveedor');    
        let diasCobro = 0;
        if(dias_albaran_proveedor.value.trim() != ''){
          diasCobro = dias_albaran_proveedor.value.trim();
        }

        let fecha_factura = '';
        if(this.value.trim() != ''){
          fecha_factura = this.value.trim();
        }                                  
        let vencimiento = document.getElementById('vencimiento').value;
        
        let ruta = urlCompleta+'/FacturasProveedores/calcularFechaVencimientoFacturaProveedorCambiarFecha';
        let params = {'dias' : diasCobro, 'fecha_factura_proveedor' : fecha_factura, 'vencimiento':vencimiento};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {                                       
          if(data.dias_albaran_proveedor){
            document.getElementById('dias_albaran_proveedor').value = data.dias_albaran_proveedor;
          }
          if(data.fechaVecimiento){
            document.getElementById('vencimiento').value = data.fechaVecimiento;
          }          
        }))        
    }); 

    }    

    let tablaGrillaBody = document.getElementById('tablaGrillaBody');   
    if(tablaGrillaBody){
      tablaGrillaBody.addEventListener("change", (event) => {
      
        const clickedElement = event.target;
        
        if (clickedElement.matches('.articulo')) { 
          
          let idorden= clickedElement.dataset.idorden;                    

          let ruta = urlCompleta+'/ProductosCompra/obtenerProducto';              
          let params = {'id' : clickedElement.value};

          let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {            
            
            if(data.error == false){                          
              document.getElementById('unidadArticulo'+idorden).value = data.datos.abrev_unidad;
              document.getElementById('iva'+idorden).value = data.datos.iva;

            }else{
              Swal.fire({
                title: 'Error',
                text: data.mensaje,
                icon: 'error',
                confirmButtonText: 'Tancar'
              });  
            }

          }))                
        }
                            
      });
    }    
    
    const cerrar_albaran = document.getElementById('cerrar_albaran');    
    if(cerrar_albaran){
      let idAlbaran = document.getElementById('id').value;

      cerrar_albaran.addEventListener('click', function () {            
        
        let ruta = urlCompleta+'/AlbaranesProveedores/verificarSiAlbaranSePuedeEliminar'; 
        let params = {'idAlbaran' : idAlbaran};
  
        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          
          if(data.error==false){
            window.location.href = urlCompleta+"/AlbaranesProveedores";
          }

        }))    
        
      });
    }


  }    //fin del if

});
