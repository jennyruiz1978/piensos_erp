import DB from './fecth.js';

document.addEventListener("DOMContentLoaded", () => {    
  
  if (window.location.pathname.includes("/FacturasProveedores")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']      
      if (opcionesControladas.includes(optionCRUD)) {      

        if(optionCRUD == 'u'){

           let idFactura = event.target.getAttribute('data-idupd');
           if(idFactura > 0){   
            window.location = urlCompleta+'/FacturasProveedores/verFactura/'+idFactura;            
           }

          }else if(optionCRUD == 'b'){

            let idFactura = event.target.getAttribute('data-iddel');
            if(idFactura > 0){              
  
              let ruta = urlCompleta+'/FacturasProveedores/consultarFacturaParaEliminar'; 
              let params = {'id' : idFactura};
  
              let fetch=new DB(ruta, 'POST').get(params);
              fetch.then((data => {            
                if(data.eliminar){
                  
                  let texto_mensaje= "";
  
                  let albaranes = data.albaranes;        
                  
                  if(albaranes && albaranes.length > 0){
                    let albs = "";
                    for (let index = 0; index < albaranes.length; index++) {            
                      albs += albaranes[index].numero+"\n";
                    }
                    texto_mensaje += "<p>Si elimina la factura, es desvinculen els albarans: "+albs+"</p><p>Esteu segur d'eliminar la factura?</p>";
                  }
                  
                  document.getElementById('mensaje_eliminar_factura').innerHTML = texto_mensaje;                
                  document.getElementById('idFacturaEliminar').value = idFactura;
                  document.getElementById('eliminarFila').value = 1;              
                  $('#modalFormEliminarFactura').modal('show');
  
                }else{
  
                  let texto_recibos= "";
  
                  let recibos = data.recibos;        
                  
                  if(recibos && recibos.length > 0){
                    let nums = "";
                    for (let index = 0; index < recibos.length; index++) {            
                      nums += recibos[index].numero+"\n";
                    }
                    texto_recibos += data.mensaje + " \n" + nums + "\n Heu d'eliminar els rebuts";
                  }
  
                  alert(texto_recibos);                            
                }
  
              }))
  
            }
  
          }
        
      
      } else {
         return
      }
    }

    const tablaFacturasProveedores = document.getElementById('tablaFacturasProveedores');
    if(tablaFacturasProveedores){
      document.getElementById('tablaFacturasProveedores').addEventListener('click', eventHandler);
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
            document.getElementById('nif_factura').value = data.datos.nif;
          }))

        }

      });      
    }

    let formulario_eliminar_factura = document.getElementById('formulario_eliminar_factura');
    if(formulario_eliminar_factura){
      formulario_eliminar_factura.addEventListener('submit', function(e) {                
          e.preventDefault();               
          $('#modalFormEliminarFactura').modal('hide');
                            
          let ruta = urlCompleta+'/FacturasProveedores/eliminarFactura';
          let datosForm = new FormData(formulario_eliminar_factura);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);
          
          fetch.then((respuesta => {   

            let rutaIni = urlCompleta+'/FacturasProveedores'; 

            if(respuesta.mensaje){
              if(document.getElementById('eliminarFila').value == 1){
                eliminarFilaTabla(formulario_eliminar_factura.idFacturaEliminar.value);
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


    
    function eliminarFilaTabla(idFactura) {
      let idEliminar = 'fila_'+idFactura;
      const element = document.getElementById(idEliminar);
      element.remove();
    }  
    
    let ver_albaranes_factura = document.getElementById('ver_albaranes_factura');
    if(ver_albaranes_factura){
      ver_albaranes_factura.addEventListener('click', function () {
        let idFactura = document.getElementById('id').value;
                    
        let ruta = urlCompleta+'/FacturasProveedores/obtenerAlbaranesFactura'; 
        let params = {'id' : idFactura};
          
        let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {            
            
            let tableAlb = document.getElementById('tablaVerAlbaranesFactura');                        
            let tBody = tableAlb.getElementsByTagName('tbody')[0];            
            tBody.innerHTML = data.html_albaranes;       
            document.getElementById('diferencias').innerHTML = data.diferencias;
          }))
      });       
    }
            
    let formulario_ver_factura_prov = document.getElementById('formulario_ver_factura_prov');
    if(formulario_ver_factura_prov){
      formulario_ver_factura_prov.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/FacturasProveedores/actualizarFactura';
          let datosForm = new FormData(formulario_ver_factura_prov);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                                                    

            let tableId = document.getElementById('tablaGrilla');                        
            let tBody = tableId.getElementsByTagName('tbody')[0];            
            tBody.innerHTML = respuesta.html;

            document.getElementById('baseimponible_importe').innerHTML = respuesta.baseimponible;
            document.getElementById('ivatotal_importe').innerHTML = respuesta.ivatotal;
            document.getElementById('total_importe').innerHTML = respuesta.total;       
            document.getElementById('retencion_importe').innerHTML = respuesta.retencionimporte;       
            document.getElementById('retenciontipo').value = respuesta.retenciontipo;
            document.getElementById('estado').value = respuesta.estado;                      

            mostrarResultadoFetch(respuesta);  
              
          }))
          
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

    let formulario_filtros_buscar_albaranes = document.getElementById('formulario_filtros_buscar_albaranes');
    if(formulario_filtros_buscar_albaranes){

      let buscarAlbaranesProv = document.getElementById('buscarAlbaranesProv');
      if(buscarAlbaranesProv){
        buscarAlbaranesProv.addEventListener('click', function () {

          let ruta = urlCompleta+'/FacturasProveedores/obtenerAlbaranesConFiltros'; 
          let datosForm = new FormData(formulario_filtros_buscar_albaranes);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);                                          
          fetch.then((data => {                                   
            let tableId = document.getElementById('tablaGrillaAlbaranesFactura');                        
            let tBody = tableId.getElementsByTagName('tbody')[0];            
            tBody.innerHTML = data.html_albaranes;            
            
          }))
        });       
      }

    }                    
        
    
    let tableX = document.getElementById('tablaGrillaAlbaranesFactura');   
    if(tableX){
      tableX.addEventListener("click", (event) => {
      
        const clickedElement = event.target;
              
        if (clickedElement.matches('.agregar_alb_fact')) {        
          
          let idAlbaran= clickedElement.dataset.idalbaran;
          
          let arrAlbFact = arraysAlbaranesSeleccionados();        
  
          let ruta = urlCompleta+'/FacturasProveedores/obtenerAlbaranFila';         
          let params = {'id' : idAlbaran};
          
            if(arrAlbFact.length > 0 ){
                          
              if(arrAlbFact.indexOf(idAlbaran) == -1){
                let fetch=new DB(ruta, 'POST').get(params);                       
                fetch.then((data => {
                  const filaSelected = document.getElementById('fila_alb_'+idAlbaran);
                  filaSelected.remove();
                  $("#tablaGrillaFactura tbody").append(data.html_albaran);
                  
                }))              
              }else{
                alert('Ja ha afegit aquest albarà');
              }
              
            }else{
  
              let fetch=new DB(ruta, 'POST').get(params);                       
              fetch.then((data => {
                const filaSelected = document.getElementById('fila_alb_'+idAlbaran);
                filaSelected.remove();
                $("#tablaGrillaFactura tbody").append(data.html_albaran);
                
              }))
  
            }
          
        }
              
      });
    }

    function arraysAlbaranesSeleccionados(){
       
      var selected = document.querySelectorAll(".fila_alb_inv");
      var arr= [];
      selected.forEach((item) => {          
          arr.push(item.dataset.idalbaran);
      });
      return arr;
    }

    
    var tablaGrillaFactura = document.getElementById('tablaGrillaFactura');   
    if(tablaGrillaFactura){
      tablaGrillaFactura.addEventListener("click", (event) => {
      
        const clickedTr = event.target;
              
        if (clickedTr.matches('.eliminar_alb_fact')) {        
          let idAlbaran= clickedTr.dataset.idalbaran;          
          const filaDelete = document.getElementById('fila_alb_inv_'+idAlbaran); 
          filaDelete.remove();
        }
              
      });
    }
       

    let formulario_crear_factura_masiva = document.getElementById('formulario_crear_factura_masiva');
    if(formulario_crear_factura_masiva){
      formulario_crear_factura_masiva.addEventListener('submit', function(e) {        
          e.preventDefault();
          let bool = confirm('Esteu segur(a) de generar la factura?');
          if(bool){

            let ruta = urlCompleta+'/FacturasProveedores/crearFacturaMasivaProveedor';
            let datosForm = new FormData(formulario_crear_factura_masiva);                  
            let fetch=new DB(ruta, 'POST').post(datosForm);
  
            fetch.then((respuesta => {
                            
              let numerofactura = respuesta.idfactura;       
              let url = urlCompleta+'/FacturasProveedores/verFactura/'+numerofactura;
              mostrarResultadoFetchEliminar(respuesta, 0, url);
              
            }))
  
          }
          
          
      });
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
      

    let tablaVerAlbaranesFactura = document.getElementById('tablaVerAlbaranesFactura');   
    if(tablaVerAlbaranesFactura){
      tablaVerAlbaranesFactura.addEventListener("click", (event) => {
      
        const clickedElement = event.target;
              
        if (clickedElement.matches('.eliminar_alb_fact')) {        
          
          let bool = confirm("Esteu segur de treure l'albarà?");
          if(bool){

            let idAlbaran= clickedElement.dataset.idalbaran;     
            let idFactura= document.getElementById('id').value;               
  
            let ruta = urlCompleta+'/FacturasProveedores/eliminarAlbaranFactura';                     
            let params = {'id' : idAlbaran, 'idFactura' : idFactura};
  
            let fetch=new DB(ruta, 'POST').get(params);                
            fetch.then((data => {
              if(data.error == false){
                const filaSelected = document.getElementById('fila_alb_fact_ver_'+idAlbaran);
                filaSelected.remove();

                document.getElementById('diferencias').innerHTML = data.diferencias;

              }else{
                Swal.fire({
                  title: 'Error',
                  text: data.mensaje,
                  icon: 'error',
                  confirmButtonText: 'OK'
                });   
              }
            }))
          }  
        }
              
      });
    }
    
    let agregar_recibo_factura = document.getElementById('agregar_recibo_factura');
    if(agregar_recibo_factura){
      agregar_recibo_factura.addEventListener('click', function () {
        let iFactura = document.getElementById('id').value;
        document.getElementById('idFactura').value = iFactura;
        
        if(iFactura > 0){

          let ruta = urlCompleta+'/FacturasProveedores/datosFacturaProveedorParaRecibo';
          let params = {'id' : iFactura};

          let fetch=new DB(ruta, 'POST').get(params); 
          fetch.then((data => {

            if(data.datos.estado == 'cobrada'){
              alert('La factura està cobrada. No admet més rebuts.');
            }else{
              document.getElementById('idFactura').value = iFactura;
              document.getElementById('vencimiento_recibo').value = data.vencimiento;
              document.getElementById('fecha_recibo').value = data.fecha_actual;
              document.getElementById('importe_recibo').value = data.datos.total;
              document.getElementById('concepto_recibo').value = 'Pagament de la factura Nº ' + data.datos.numero;
              document.getElementById('nombre_librado').value = data.librado;
              document.getElementById('nombre_librador').value = data.datos.proveedor;
              
              $('#modalFormAgregarRecibo').modal('show');
            }

          }))
        }   
      });       
    }

    $('#modalFormAgregarRecibo').on('hidden.bs.modal', function () {
			$('#formulario_agregar_recibo').trigger("reset");			
		});    
    
    let formulario_agregar_recibo = document.getElementById('formulario_agregar_recibo');
    if(formulario_agregar_recibo){
      formulario_agregar_recibo.addEventListener('submit', function(e) {        
            
        e.preventDefault();          
        let ruta = urlCompleta+'/RecibosProveedores/crearRecibo';
        let datosForm = new FormData(formulario_agregar_recibo);
        let fetch=new DB(ruta, 'POST').post(datosForm);
  
        fetch.then((respuesta => {          
        
          if(respuesta.error == false){
            $('#modalFormAgregarRecibo').modal('hide');
            let tablaVerRecibosFactura = document.getElementById('tablaVerRecibosFactura');                        
            let tBody = tablaVerRecibosFactura.getElementsByTagName('tbody')[0];            
            tBody.innerHTML = respuesta.html_recibos;   
            document.getElementById('estado').value = respuesta.estado;

          }else{
            Swal.fire({
              title: 'Error',
              text: respuesta.mensaje,
              icon: 'error',
              confirmButtonText: 'OK'
            }); 
          }
          
        }))                      
          
      });
    } 

    let ver_recibos_factura = document.getElementById('ver_recibos_factura');
    if(ver_recibos_factura){
      ver_recibos_factura.addEventListener('click', function () {
        let idFactura = document.getElementById('id').value;
                    
        let ruta = urlCompleta+'/RecibosProveedores/obtenerRecibosFactura'; 
        let params = {'id' : idFactura};
          
        let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {            
            
            let tableRecibos = document.getElementById('tablaVerRecibosFactura');                        
            let tBody = tableRecibos.getElementsByTagName('tbody')[0];            
            tBody.innerHTML = data.html_recibos;                   
            
          }))
      });       
    }    
        
    var tablaVerRecibosFactura = document.getElementById('tablaVerRecibosFactura');   
    if(tablaVerRecibosFactura){
      tablaVerRecibosFactura.addEventListener("click", (event) => {
      
        const clickedTr = event.target;
              
        if (clickedTr.matches('.eliminar_recibo_fact')) {        
          let idRecibo= clickedTr.dataset.idrecibo;          

          let bool = confirm("Esteu segur(a) d'eliminar el rebut?");
          if(bool){

            let ruta = urlCompleta+'/RecibosProveedores/eliminarReciboFactura'; 
            let params = {'id' : idRecibo};

            let fetch=new DB(ruta, 'POST').get(params);
            fetch.then((data => {            
              
              if(data.error == false){
                const filaDelete = document.getElementById('fila_recibo_fact_ver_'+idRecibo); 
                filaDelete.remove();  
                document.getElementById('estado').value = data.estado;

              }else{
                Swal.fire({
                  title: 'Error',
                  text: data.mensaje,
                  icon: 'error',
                  confirmButtonText: 'OK'
                });
              }
                             
              
            }))
          
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




  }//fin del if

});
