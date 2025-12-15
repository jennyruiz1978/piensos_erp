import DB from './fecth.js';
import RowsGrid from './rows_grilla.js';

document.addEventListener("DOMContentLoaded", () => {    
  
  if (window.location.pathname.includes("/FacturasClientes")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']      
      if (opcionesControladas.includes(optionCRUD)) {      

        if(optionCRUD == 'u'){

           let idFactura = event.target.getAttribute('data-idupd');
           if(idFactura > 0){   
            window.location = urlCompleta+'/FacturasClientes/verFactura/'+idFactura;            
           }

        }else if(optionCRUD == 'b'){

          let idFactura = event.target.getAttribute('data-iddel');
          if(idFactura > 0){              

            let ruta = urlCompleta+'/FacturasClientes/consultarFacturaParaEliminar'; 
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
                  texto_mensaje += "Si elimina la factura, es desvinculen els albarans: "+albs+"<p>Esteu segur d'eliminar la factura?</p>";
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

    const tablaFacturasClientes = document.getElementById('tablaFacturasClientes');
    if(tablaFacturasClientes){
      document.getElementById('tablaFacturasClientes').addEventListener('click', eventHandler);
    }   

    const cliente_select = document.getElementById('idcliente');    

    if(cliente_select){

      cliente_select.addEventListener('change', function () {
        
        let cliente_selected = cliente_select.value;       
        
        if(cliente_selected != ''){          

          let ruta = urlCompleta+'/Clientes/obtenerCliente'; 
          let params = {'id' : cliente_selected};

          let clientes=new DB(ruta, 'POST').get(params);
          clientes.then((data => {            
            document.getElementById('nif_factura').value = data.datos.nif;
            
            if (data.datos.formacobro !== null && data.datos.formacobro !== "") {
              document.getElementById('formacobro').value = data.datos.formacobro;
            } else {
                document.getElementById('formacobro').value = ""; 
            }
          }))

        }

      });      
    }

    let formulario_eliminar_factura = document.getElementById('formulario_eliminar_factura');
    if(formulario_eliminar_factura){
      formulario_eliminar_factura.addEventListener('submit', function(e) {                
          e.preventDefault();               
          $('#modalFormEliminarFactura').modal('hide');

          let loader = document.getElementById('loader_factura');
          if(loader) loader.style.display = 'block';

          // Deshabilitar botones
          let buttons = formulario_eliminar_factura.querySelectorAll('button');
          buttons.forEach(btn => btn.disabled = true);

                            
          let ruta = urlCompleta+'/FacturasClientes/eliminarFactura';
          let datosForm = new FormData(formulario_eliminar_factura);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);
          
          fetch.then((respuesta => {   

            let rutaIni = urlCompleta+'/FacturasClientes'; 

            
            if(loader) loader.style.display = 'none';
            buttons.forEach(btn => btn.disabled = false);

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
                    
        let ruta = urlCompleta+'/FacturasClientes/obtenerAlbaranesFactura'; 
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
            
    let formulario_ver_factura_cli = document.getElementById('formulario_ver_factura_cli');
    if(formulario_ver_factura_cli){
      formulario_ver_factura_cli.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          
          let loader = document.getElementById('loader_factura');
          if(loader) loader.style.display = 'block';

          // Deshabilitar botones
          let buttons = formulario_ver_factura_cli.querySelectorAll('button');
          buttons.forEach(btn => btn.disabled = true);

          let ruta = urlCompleta+'/FacturasClientes/actualizarFactura';
          let datosForm = new FormData(formulario_ver_factura_cli);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                                                             

            if(loader) loader.style.display = 'none';
            buttons.forEach(btn => btn.disabled = false);

            limpiarMensajesCamposError();            
            mostrarResultadoFetch(respuesta);

            if(respuesta.error==false){
              
              let tableId = document.getElementById('tablaGrilla');                        
              let tBody = tableId.getElementsByTagName('tbody')[0];            
              tBody.innerHTML = respuesta.html;

              document.getElementById('baseimponible_importe').innerHTML = respuesta.baseimponible;
              document.getElementById('ivatotal_importe').innerHTML = respuesta.ivatotal;
              document.getElementById('total_importe').innerHTML = respuesta.total;       
              document.getElementById('estado').value = respuesta.estado;              
              document.getElementById('descuentotipo').value = respuesta.descuentotipo;
              
              document.getElementById('descuento_importe').innerHTML = respuesta.descuentoimporte;

              
              let alavista = document.getElementById("etiqueta_alavista");
              if(respuesta.formacobro == 2){
                if(alavista){
                  alavista.style.display = "block";
                }else{
                  let label_alavista = '<label id="etiqueta_alavista">A la vista</label>';
                  document.getElementById("container_formacobro").insertAdjacentHTML('beforeend',label_alavista);
                }
              }else{
                if(alavista){
                  alavista.style.display = "none";
                }
              }

            }else{

              if(respuesta.fieldsValidate && respuesta.fieldsValidate.length > 0){                
                for (let index = 0; index < respuesta.fieldsValidate.length; index++) {        
                  let textAdd = '';
                  if(respuesta.fieldsValidate[index] == 'diascobro'){
                    textAdd = '. Mínim zero';
                  }            
                  document.getElementById('error_'+respuesta.fieldsValidate[index]).innerHTML = 'Aquest camp és obligatori'+textAdd;
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

    let formulario_filtros_buscar_albaranes = document.getElementById('formulario_filtros_buscar_albaranes');
    if(formulario_filtros_buscar_albaranes){

      let buscarAlbaranesCli = document.getElementById('buscarAlbaranesCli');
      if(buscarAlbaranesCli){
        buscarAlbaranesCli.addEventListener('click', function () {

          let ruta = urlCompleta+'/FacturasClientes/obtenerAlbaranesConFiltros'; 
          let datosForm = new FormData(formulario_filtros_buscar_albaranes);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);                                          
          fetch.then((data => {                
            if(data.error == true){
              Swal.fire({
                title: 'Error',
                text: data.mensaje,
                icon: 'error',
                confirmButtonText: 'OK'
              });  
            }else{
              let tableId = document.getElementById('tablaGrillaAlbaranesFactura');                        
              let tBody = tableId.getElementsByTagName('tbody')[0];            
              tBody.innerHTML = data.html_albaranes;            
            }

            
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
  
          let ruta = urlCompleta+'/FacturasClientes/obtenerAlbaranFila';         
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


                
            let loader = document.getElementById('loader_factura');
            if(loader) loader.style.display = 'block';

            // Deshabilitar botones
            let buttons = formulario_crear_factura_masiva.querySelectorAll('button');
            buttons.forEach(btn => btn.disabled = true);



            let ruta = urlCompleta+'/FacturasClientes/crearFacturaMasivaCliente';
            let datosForm = new FormData(formulario_crear_factura_masiva);                  
            let fetch=new DB(ruta, 'POST').post(datosForm);
  
            fetch.then((respuesta => {        
              
              if(loader) loader.style.display = 'none';
              buttons.forEach(btn => btn.disabled = false);
              
              limpiarMensajesCamposError();                         
              if(respuesta.error==false){
                let numerofactura = respuesta.idfactura;
                let url = urlCompleta+'/FacturasClientes/verFactura/'+numerofactura;
                mostrarResultadoFetchEliminar(respuesta, 0, url);
              }else{
                
                if(respuesta.fieldsValidate && respuesta.fieldsValidate.length > 0){                
                  for (let index = 0; index < respuesta.fieldsValidate.length; index++) {        
                    let textAdd = '';
                    if(respuesta.fieldsValidate[index] == 'diascobro'){
                      textAdd = '. Mínim zero';
                    }            
                    document.getElementById('error_'+respuesta.fieldsValidate[index]).innerHTML = 'Aquest camp és obligatori'+textAdd;
                  }
                }
                Swal.fire({
                  title: 'Error',
                  text: respuesta.mensaje,
                  icon: 'error',
                  confirmButtonText: 'OK'
                });
              }              
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
          
          let bool = confirm(" Esteu segur de treure l'albarà? ");
          if(bool){

            let loader = document.getElementById('loader_factura');
            if(loader) loader.style.display = 'block';

            // Deshabilitar botones
            let buttons = formulario_ver_factura_cli.querySelectorAll('button');
            buttons.forEach(btn => btn.disabled = true);
            let anclas = formulario_ver_factura_cli.querySelectorAll('a');
            anclas.forEach(btn => btn.disabled = true);


            let idAlbaran= clickedElement.dataset.idalbaran;
            let idFactura= document.getElementById('id').value;
  
            let ruta = urlCompleta+'/FacturasClientes/eliminarAlbaranFactura';
            let params = {'id' : idAlbaran, 'idFactura' : idFactura};
  
            let fetch=new DB(ruta, 'POST').get(params);                
            fetch.then((data => {

              if(loader) loader.style.display = 'none';
              buttons.forEach(btn => btn.disabled = false);
              anclas.forEach(an => an.disabled = false);

              if(data.error == false){
                const filaSelected = document.getElementById('fila_alb_fact_ver_'+idAlbaran);
                filaSelected.remove();         
                                
                document.getElementById('diferencias').innerHTML = data.diferencias;

                if(data.html && data.html != ''){
                  $("#tablaGrillaBody").html(data.html);                  
                }
                
                document.getElementById('baseimponible_importe').innerHTML = data.baseimponible;
                document.getElementById('ivatotal_importe').innerHTML = data.ivatotal;
                document.getElementById('total_importe').innerHTML = data.total;
                
                if(data.estado){
                  document.getElementById('estado').value = data.estado;  
                }

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

        if(iFactura > 0){          

          let ruta = urlCompleta+'/FacturasClientes/datosFacturaClienteParaRecibo';
          let params = {'id' : iFactura};

          let fetch=new DB(ruta, 'POST').get(params); 
          fetch.then((data => {

            if(data.datos.estado == 'cobrada'){
              alert('La factura està cobrada. No admet més rebuts.');
            }else{
              document.getElementById('idFactura').value = iFactura;
              document.getElementById('vencimiento_recibo').value = data.datos.vencimiento;
              document.getElementById('fecha_recibo').value = data.fecha_actual;
              document.getElementById('importe_recibo').value = data.datos.total;
              document.getElementById('concepto_recibo').value = 'Cobrament de la factura Nº ' + data.datos.numero;
              document.getElementById('nombre_librado').value = data.datos.cliente;
              document.getElementById('nombre_librador').value = data.librador;
              
              //$('#modalFormAgregarRecibo').modal('show');
              $('#modalFormAgregarRecibo').modal({
                backdrop: 'static',
                keyboard: false
              });
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

        let loader = document.getElementById('loader_factura');
        if(loader) loader.style.display = 'block';

        // Deshabilitar botones
        let buttons = formulario_agregar_recibo.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);
        let anclas = formulario_agregar_recibo.querySelectorAll('button');
        anclas.forEach(an => an.disabled = true);



        let ruta = urlCompleta+'/RecibosClientes/crearRecibo';
        let datosForm = new FormData(formulario_agregar_recibo);              
        let fetch=new DB(ruta, 'POST').post(datosForm);
  
        fetch.then((respuesta => {          
        
              
          if(loader) loader.style.display = 'none';
          buttons.forEach(btn => btn.disabled = false);
          anclas.forEach(an => an.disabled = false);


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
        
        cargarTablaRecibosEnFactura();

      });       
    }    

    function cargarTablaRecibosEnFactura(){
      let idFactura = document.getElementById('id').value;
      let ruta = urlCompleta+'/RecibosClientes/obtenerRecibosFactura'; 
      let params = {'id' : idFactura};
        
      let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          
          let tableRecibos = document.getElementById('tablaVerRecibosFactura');                        
          let tBody = tableRecibos.getElementsByTagName('tbody')[0];            
          tBody.innerHTML = data.html_recibos;                   
          
        }))

    }
        
    var tablaVerRecibosFactura = document.getElementById('tablaVerRecibosFactura');   
    if(tablaVerRecibosFactura){
      tablaVerRecibosFactura.addEventListener("click", (event) => {
      
        const clickedTr = event.target;
              
        if (clickedTr.matches('.eliminar_recibo_fact')) {        
          let idRecibo= clickedTr.dataset.idrecibo;          

          let bool = confirm("Esteu segur(a) d'eliminar el rebut?");
          if(bool){


            let loader = document.getElementById('loader_factura');
            if(loader) loader.style.display = 'block';

            let ruta = urlCompleta+'/RecibosClientes/eliminarReciboFactura'; 
            let params = {'id' : idRecibo};

            let fetch=new DB(ruta, 'POST').get(params);
            fetch.then((data => {          
              
              if(loader) loader.style.display = 'none';
              
              if(data.error == false){
                const filaDelete = document.getElementById('fila_recibo_fact_ver_'+idRecibo); 
                filaDelete.remove();  
                document.getElementById('estado_recibo').value = data.estado;

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
    const dias = document.getElementById('dias');    
    if(dias){

      dias.addEventListener('keyup', function () {                        
        
          let diasCobro = 0;
          if(this.value.trim() != ''){
            diasCobro = this.value.trim();
          }                        
          let fechaFactura = document.getElementById('fecha').value;

          let ruta = urlCompleta+'/FacturasClientes/calcularFechaVencimientoFacturaCliente';         
          let params = {'dias' : diasCobro, 'fecha' : fechaFactura};

          let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {                       
            document.getElementById('vencimiento').value = data.fechaVecimiento;       
          }))

        
      });      
    }

    const fecha_vencimiento_cliente = document.getElementById('vencimiento');    
    if(fecha_vencimiento_cliente){
           
      fecha_vencimiento_cliente.addEventListener('change', function () {                        
        
        let fecha_vencimiento_cliente = '';
        if(this.value.trim() != ''){
          fecha_vencimiento_cliente = this.value.trim();
        }                                  
        let fecha_factura_cliente = document.getElementById('fecha').value;
        

        let ruta = urlCompleta+'/FacturasClientes/calcularDiasCobroFacturaCliente';         
        let params = {'vencimiento' : fecha_vencimiento_cliente, 'fecha_factura_cliente':fecha_factura_cliente};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {                       
          document.getElementById('dias').value = data.dias_albaran_cliente;       
        }))        
    }); 

    }

    const dias_albaran_cliente = document.getElementById('dias_albaran_cliente');    
    if(dias_albaran_cliente){     
     
      dias_albaran_cliente.addEventListener('keyup', function () {                        
        
        let diasCobro = 0;
        if(this.value.trim() != ''){
          diasCobro = this.value.trim();
        }                        
        let fechaFactura = document.getElementById('fecha_factura_cliente').value;

        let ruta = urlCompleta+'/FacturasClientes/calcularFechaVencimientoFacturaCliente';         
        let params = {'dias' : diasCobro, 'fecha' : fechaFactura};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {                       
          document.getElementById('vencimiento_cliente').value = data.fechaVecimiento;       
        }))
      })
           
    }

    
    const vencimiento_cliente = document.getElementById('vencimiento_cliente');    
    if(vencimiento_cliente){
           
      vencimiento_cliente.addEventListener('change', function () {                        
        
        let vencimiento_cliente = '';
        if(this.value.trim() != ''){
          vencimiento_cliente = this.value.trim();
        }                                  
        let fecha_factura_cliente = document.getElementById('fecha_factura_cliente').value;
        

        let ruta = urlCompleta+'/FacturasClientes/calcularDiasCobroFacturaCliente';         
        let params = {'vencimiento' : vencimiento_cliente, 'fecha_factura_cliente':fecha_factura_cliente};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {                       
          document.getElementById('dias_albaran_cliente').value = data.dias_albaran_cliente;       
        }))        
    }); 

    }

    let generar_factura_pdf = document.getElementById('generar_pdf');
    if(generar_factura_pdf){
      generar_factura_pdf.addEventListener('click', function () {
                
        let idFactura = document.getElementById('id').value;                                
        window.open(urlCompleta + "/FacturasClientes/exportarPdfFactura/" + idFactura);        
        
      });       
    }

    let submit_rectificativa = document.getElementById("submit_rectificativa");      
    if(submit_rectificativa){
        submit_rectificativa.addEventListener('click', (e) => {                                             
          var form_rectificativa = document.getElementById('formulario_crear_factura_rectificativa');            
          form_rectificativa.submit();     
      });    
    }

    let formulario_crear_factura_rectificativa = document.getElementById('formulario_crear_factura_rectificativa');
    if(formulario_crear_factura_rectificativa){
      formulario_crear_factura_rectificativa.addEventListener('submit', function(e) {        
          e.preventDefault();
          let bool = confirm('Esteu segur(a) de generar la factura rectificativa ?');
          if(bool){

            let ruta = urlCompleta+'/FacturasClientes/guardarFacturaRectificativa';
            let datosForm = new FormData(formulario_crear_factura_rectificativa);                  
            let fetch=new DB(ruta, 'POST').post(datosForm);
  
            fetch.then((respuesta => {                        
              
              limpiarMensajesCamposError();                         
              if(respuesta.error==false){
                let numerofactura = respuesta.idfactura;
                let url = urlCompleta+'/FacturasClientes/verFactura/'+numerofactura;
                mostrarResultadoFetchEliminar(respuesta, 0, url);
              }else{
                
                if(respuesta.fieldsValidate && respuesta.fieldsValidate.length > 0){                
                  for (let index = 0; index < respuesta.fieldsValidate.length; index++) {                                                          
                    document.getElementById('error_'+respuesta.fieldsValidate[index]).innerHTML = 'Aquest camp és obligatori';
                  }
                }
                Swal.fire({
                  title: 'Error',
                  text: respuesta.mensaje,
                  icon: 'error',
                  confirmButtonText: 'OK'
                });
              }              
            }))
  
          }                    
      });
    } 


    //enviar emails
    let enviar_factura_proveedor = document.getElementById('enviar_factura_proveedor');
    if(enviar_factura_proveedor){
      enviar_factura_proveedor.addEventListener('click', function () {
        
        let idFactura = document.getElementById('id').value;   
        document.getElementById('tipoDocumento').value = 'factura';     
        document.getElementById('idAlbaranClienteEmail').value = idFactura;
        document.getElementById('asunto').value = "Enviament de factura";
        document.getElementById('mensaje').value =  "Benvolgut client, adjuntem la factura corresponent. Una cordial salutació";

        let ruta = urlCompleta+'/FacturasClientes/obtenerDatosEnvioEmailFactura'; 
        let params = {'id' : idFactura};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          if(data.error == false){
            document.getElementById('title_form_enviar').innerHTML = 'Enviar factura';      
            
            // Cargar los contactos en el select
            let selectContactos = document.getElementById('contactosCliente');
            selectContactos.innerHTML = '<option selected disabled value="">Seleccionar contacto</option>';
            if(data.contactos && data.contactos.length > 0) {
              data.contactos.forEach(contacto => {
                selectContactos.innerHTML += `<option value="${contacto.email}">${contacto.nombre} - ${contacto.email}</option>`;
              });
            }            

            $('#modalFormEnviarFacturaCliente').modal('show');
          }else{
            alert("Heu de desar la factura");
          }
        }))        
      });       
    }


    // Agregar el evento para el select de contactos
    let selectContactos = document.getElementById('contactosCliente');
    if(selectContactos) {
      selectContactos.addEventListener('change', function() {
        if(this.value) {
          agregarEmail(this.value);
          this.value = ''; // Reset select
        }
      });
    }    
    
    $('#modalFormEnviarFacturaCliente').on('hidden.bs.modal', function () {
      resetearFormularioEnvioEmail();			
		});  

    function resetearFormularioEnvioEmail(){
      $('#formulario_enviar_factura_cliente').trigger("reset");			
      let correos = document.getElementsByClassName('contieneInput');
      $(correos).remove();
    }
               
    let formulario_enviar_factura_cliente = document.getElementById('formulario_enviar_factura_cliente');
    if(formulario_enviar_factura_cliente){
      formulario_enviar_factura_cliente.addEventListener('submit', function(e) {                
          e.preventDefault();               
         
          const spinner = document.getElementById("spinner");
          spinner.innerHTML = `<div class="spinner-border text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>`;
          spinner.classList.add('spinnerShow');          
                            
          let ruta = urlCompleta+'/EmailsDocumentos/enviarEmailDocumento';
          let datosForm = new FormData(formulario_enviar_factura_cliente);                  
          let fetch=new DB(ruta, 'POST').postSend(datosForm);
          
          fetch
            .then((respuesta => {   

              if(respuesta.error==false){
                resetearFormularioEnvioEmail();
                $('#modalFormEnviarFacturaCliente').modal('hide');
                Swal.fire({
                  title: 'Procés correcte',
                  text: respuesta.mensaje,
                  icon: 'success',
                  confirmButtonText: 'Ok'          
                });  
              }else{
                Swal.fire({
                  title: 'Error',
                  text: respuesta.mensaje,
                  icon: 'error',
                  confirmButtonText: 'Ok'          
                });  
              }
            
            }))
          
      });
    }
   
    let btnAddNuevoDestinatario = document.getElementById('btnAddNuevoDestinatario');
    if(btnAddNuevoDestinatario){
      btnAddNuevoDestinatario.addEventListener('click', function () {
        var email = document.getElementById('emailNuevoAgregar').value;

        if(email.trim() != ''){
          let procesado = email.replace(/\s+/g, '');
          let validar = validarEmail(procesado);
          
          if(validar){
            agregarEmail(procesado);
            $('#emailNuevoAgregar').val('');
          }else{
            alert("Heu d'escriure un correu vàlid");
          }   
        }
      })
    }

    function agregarEmail(email) {

        var divs = document.getElementsByClassName("contieneInput").length;

        var contador = 0;
        if (divs == 0) {
            contador = 1;
        }else if(divs >0){
            var ultimo = $('#tablaEmailsEnvioPresupuesto .contieneInput:last').data('index');
            contador = parseInt(ultimo)  + 1;
        }

        if (email !='') {            
            var fila = `<div class="flex mr-3 contieneInput" data-index="${contador}" id="cont_${contador}"><div class="mr-1"><input type="hidden" value="${email}" name="emailEnviar[]">${email}</div>                             
                <a class='eliminarEmailPres' data-email="${contador}"><i class='far fa-times-circle texto-rojo-del text-xs'></i></a>
            </div>`;
            $('#tablaEmailsEnvioPresupuesto').append(fila);
        }
    }

    function validarEmail(email) {
      
      var validEmail =  /^\w+([.-_+]?\w+)*@\w+([.-]?\w+)*(\.\w{2,10})+$/;

      if( validEmail.test(email) ){
        return true;
      }else{
        return false;
      }            
    }
    
    $(document).on('click', '.eliminarEmailPres', function (e) {
      e.preventDefault();
      var row = $(this).data('email');
      
      $('#cont_'+row).remove();
    });

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
            
              let ruta = urlCompleta+'/FacturasClientes/eliminarFilaDetalle';
              let params = {'idFila' : idDetalle, 'idFactura' : document.getElementById('id').value};
    
              let fetch=new DB(ruta, 'POST').get(params);
              fetch.then((data => {            
                
                if(data.error == false){
                  const filaSelected = document.getElementById('fila_grilla_id_'+idFila);
                  filaSelected.remove();    
                  document.getElementById('baseimponible_importe').innerHTML = data.datos.baseimponible;
                  document.getElementById('ivatotal_importe').innerHTML = data.datos.ivatotal;
                  document.getElementById('total_importe').innerHTML = data.datos.total;    
                  if(data.estado){
                    document.getElementById('estado').value = data.estado;    
                  }

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
    
    let agregar_linea = document.getElementById('agregar_linea');
    if(agregar_linea){
      agregar_linea.addEventListener('click', function () {
                
        let ruta = urlCompleta+'/FacturasClientes/obtenerDatosParaFilaNueva';   
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

    ///////modal recibo en factura
    var tablaVerRecibosFactura = document.getElementById('tablaVerRecibosFactura');   

    if(tablaVerRecibosFactura){
      tablaVerRecibosFactura.addEventListener("click", (event) => {
      
        const clickedTr = event.target;
              
        if (clickedTr.matches('.ver_recibo_cliente')) {        
          let idRecibo= clickedTr.dataset.idrecibo;          
                    
          if(idRecibo > 0){                    

           let ruta = urlCompleta+'/RecibosClientes/obtenerRecibo'; 
           let params = {'id' : idRecibo};
             
           let fetch=new DB(ruta, 'POST').get(params);
           fetch.then((data => {            

             let info = data.datos;
             resetearFormularioEditarRecibo();

           
             let form = document.getElementById('formulario_editar_recibo');              
             form.idRecibo.value = idRecibo;
             form.numero_recibo.value = info.numero;
             form.fecha_recibo.value = info.fecha;
             form.vencimiento.value = info.vencimiento;
             form.lugar_recibo.value = info.lugarexpedicion;
             form.importe_recibo.value = info.importe;
             form.concepto_recibo.value = info.concepto;
             form.nombre_librado.value = info.librado;
             form.nombre_librador.value = info.librador;
             form.estado_recibo_ver.value = info.estadoactual;
             document.getElementById('estado_recibo_ver').classList.remove('impagado', 'pendiente', 'pagado');
             document.getElementById('estado_recibo_ver').classList.add(info.estadoactual);

             let texto_alavista = document.getElementById("texto_alavista");
             if(data.formacobro && data.formacobro == 2){
              texto_alavista.style.display = "block";
             }else{
              texto_alavista.style.display = "none";
             }

             mostrarOcultarBotonesPagadoNoPagado(data.datos.estadoactual);             
             
             $('#modalFormEditarRecibo').modal('show'); 
           }))
          
          }
        }
              
      });
    } 

    function mostrarOcultarBotonesPagadoNoPagado(estadoactual) {
      
      let cont_button_product = document.getElementById('container_buttons_recibo');

    
      if(estadoactual=='pagado'){ //pagado,impagado,pendiente
        let btn_pagado = document.getElementById('recibo_pagado');
        if(btn_pagado){
          btn_pagado.remove();
        }             
        let btn_no_pagado = document.getElementById('recibo_no_pagado');   
        if(!btn_no_pagado){
          btn_no_pagado = `<a class="button_small_bar" id="recibo_no_pagado">No pagat</span></a> `;
          cont_button_product.insertAdjacentHTML('beforeend',btn_no_pagado);
        } 
        cargarEventosBotonesNoPagado();
      }else if(estadoactual=='impagado'){ 
        let btn_pagado = document.getElementById('recibo_pagado');               
        if(!btn_pagado){
          btn_pagado = `<a class="button_small_bar" id="recibo_pagado">Pagat</span></a> `;
          cont_button_product.insertAdjacentHTML('beforeend',btn_pagado);
        }
        let btn_no_pagado = document.getElementById('recibo_no_pagado'); 
        if(btn_no_pagado){
          btn_no_pagado.remove();
        }
        cargarEventosBotonesPagado();
      }else{
        let btn_pagado = document.getElementById('recibo_pagado');
        if(btn_pagado){
          btn_pagado.remove();                  
        }
        let btn_no_pagado = document.getElementById('recibo_no_pagado');       
        if(btn_no_pagado){
          btn_no_pagado.remove();
        }            
        btn_pagado = `<a class="button_small_bar" id="recibo_pagado">Pagat</span></a> `;
          cont_button_product.insertAdjacentHTML('beforeend',btn_pagado);    
          cargarEventosBotonesPagado();                
      }             
    }

    function cargarEventosBotonesPagado() {       

      let recibo_pagado = document.getElementById('recibo_pagado');
      if(recibo_pagado){
        recibo_pagado.addEventListener('click', function () {
                  
          let idRecibo = document.getElementById('idRecibo').value;                   
          let ruta = urlCompleta+'/RecibosClientes/cambiarEstadoPagadoRecibo'; 
          let params = {'id' : idRecibo};
  
          let fetch=new DB(ruta, 'POST').get(params);
          fetch.then(
            (data => {       
  
              if(data.error == false){
                document.getElementById('estado_recibo_ver').value = data.estado;
                document.getElementById('estado_recibo_ver').classList.remove('impagado', 'pendiente', 'pagado');
                document.getElementById('estado_recibo_ver').classList.add(data.estado);   

                let btn_pagado = document.getElementById('recibo_pagado');
                if(btn_pagado){
                  btn_pagado.remove();
                }             
                let btn_no_pagado = document.getElementById('recibo_no_pagado');   
                if(!btn_no_pagado){
                  btn_no_pagado = `<a class="button_small_bar" id="recibo_no_pagado">No pagat</span></a> `;
                  let cont_button_product = document.getElementById('container_buttons_recibo');
                  cont_button_product.insertAdjacentHTML('beforeend',btn_no_pagado);
                  cargarEventosBotonesNoPagado();
                }                                 
                cargarTablaRecibosEnFactura();
                document.getElementById('estado').value= data.estado_factura;
              }else{
                Swal.fire({
                  title: 'Error',
                  text: data.mensaje,
                  icon: 'error',
                  confirmButtonText: 'Tancar'
                });
              }            
            })
          );
          
        });       
      }            

    }

    function cargarEventosBotonesNoPagado() {
      
      let recibo_no_pagado = document.getElementById('recibo_no_pagado');
      if(recibo_no_pagado){
        recibo_no_pagado.addEventListener('click', function () {
                  
          let idRecibo = document.getElementById('idRecibo').value;                   
          let ruta = urlCompleta+'/RecibosClientes/cambiarEstadoNoPagadoRecibo'; 
          let params = {'id' : idRecibo};
  
          let fetch=new DB(ruta, 'POST').get(params);
          fetch.then(
            (data => {       
  
              if(data.error == false){
                document.getElementById('estado_recibo_ver').value = data.estado;
                document.getElementById('estado_recibo_ver').classList.remove('impagado', 'pendiente', 'pagado');
                document.getElementById('estado_recibo_ver').classList.add(data.estado);   

                this.remove();           
                let btn_pagado = document.getElementById('recibo_pagado');   
                if(!btn_pagado){
                  btn_pagado = `<a class="button_small_bar" id="recibo_pagado">Pagat</span></a> `;
                  let cont_button_product = document.getElementById('container_buttons_recibo');
                  cont_button_product.insertAdjacentHTML('beforeend',btn_pagado);
                  cargarEventosBotonesPagado();
                } 
                
                cargarTablaRecibosEnFactura();
                document.getElementById('estado').value= data.estado_factura;

              }else{
                Swal.fire({
                  title: 'Error',
                  text: data.mensaje,
                  icon: 'error',
                  confirmButtonText: 'Tancar'
                });
              }            
            })
          );
          
        });       
      }         

    }    

    function resetearFormularioEditarRecibo(){
      $('#formulario_editar_recibo').trigger("reset");      
    }

    let formulario_editar_recibo = document.getElementById('formulario_editar_recibo');
    if(formulario_editar_recibo){
      formulario_editar_recibo.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let loader = document.getElementById('loader_factura');
          if(loader) loader.style.display = 'block';

          // Deshabilitar botones
          let buttons = formulario_editar_recibo.querySelectorAll('button');
          buttons.forEach(btn => btn.disabled = true);
          let anclas = formulario_editar_recibo.querySelectorAll('button');
          anclas.forEach(an => an.disabled = true);

          
          let ruta = urlCompleta+'/RecibosClientes/actualizarRecibo';
          let datosForm = new FormData(formulario_editar_recibo);
          let fetch=new DB(ruta, 'POST').post(datosForm);
          $('#modalFormEditarRecibo').modal('hide');
          fetch.then((respuesta => {    

                     
            if(loader) loader.style.display = 'none';
            buttons.forEach(btn => btn.disabled = false);
            anclas.forEach(an => an.disabled = false);


            if(respuesta.error==false){
              cargarTablaRecibosEnFactura(idFactura);
              document.getElementById('estado').value= respuesta.estado_factura;
            }        
            alert(respuesta.mensaje);            
          }))          
      });
    } 

    let generar_pdf_recibo = document.getElementById('generar_pdf_recibo');
    if(generar_pdf_recibo){
      generar_pdf_recibo.addEventListener('click', function () {
                
        let idRecibo = document.getElementById('idRecibo').value;                                
        window.open(urlCompleta + "/RecibosClientes/exportarPdfRecibo/" + idRecibo);        
        
      });       
    }      
    

    ////Nuevos

    let agregar_albaran_factura = document.getElementById('agregar_albaran_factura');
    if(agregar_albaran_factura){

      agregar_albaran_factura.addEventListener('click', function () {
        let idFactura = document.getElementById('id').value;

        if(idFactura > 0){
          document.getElementById('idFacturaAgregarAlbaran').value = idFactura;
          document.getElementById('idFacturaEnviarAlbaran').value = idFactura;
          $('#modalFormAgregarAlbaranFactura').modal('show');
        } 
      
      });       
    }

    let formulario_buscar_albaranes_factura = document.getElementById('formulario_buscar_albaranes_factura');
    if(formulario_buscar_albaranes_factura){

      let buscarAlbaranesParaFactura = document.getElementById('buscarAlbaranesParaFactura');
      if(buscarAlbaranesParaFactura){
        buscarAlbaranesParaFactura.addEventListener('click', function () {

          let ruta = urlCompleta+'/FacturasClientes/obtenerAlbaranesConFiltrosParaAlbaranCliente';
          let datosForm = new FormData(formulario_buscar_albaranes_factura);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);                                          
          fetch.then((data => {                
            if(data.error == true){
              Swal.fire({
                title: 'Error',
                text: data.mensaje,
                icon: 'error',
                confirmButtonText: 'OK'
              });  
            }else{
              let tableId = document.getElementById('tablaGrillaAlbaranesFacturaBuscar');                        
              let tBody = tableId.getElementsByTagName('tbody')[0];            
              tBody.innerHTML = data.html_albaranes;            
            }

            
          }))
        });       
      }

    }     

    let tablaGrillaAlbaranesFacturaBuscar = document.getElementById('tablaGrillaAlbaranesFacturaBuscar');   
    if(tablaGrillaAlbaranesFacturaBuscar){
      tablaGrillaAlbaranesFacturaBuscar.addEventListener("click", (event) => {
      
        const clickedElement = event.target;
              
        if (clickedElement.matches('.agregar_alb_fact')) {        
          
          let idAlbaran= clickedElement.dataset.idalbaran;
          
          let arrAlbFact = arraysAlbaranesSeleccionados();        
  
          let ruta = urlCompleta+'/FacturasClientes/obtenerAlbaranFila';         
          let params = {'id' : idAlbaran};
          
            if(arrAlbFact.length > 0 ){
                          
              if(arrAlbFact.indexOf(idAlbaran) == -1){
                let fetch=new DB(ruta, 'POST').get(params);                       
                fetch.then((data => {
                  const filaSelected = document.getElementById('fila_alb_'+idAlbaran);
                  filaSelected.remove();
                  $("#tablaGrillaFacturaSeleccionados tbody").append(data.html_albaran);
                  
                }))              
              }else{
                alert('Ja ha afegit aquest albarà');
              }
              
            }else{
  
              let fetch=new DB(ruta, 'POST').get(params);                       
              fetch.then((data => {
                const filaSelected = document.getElementById('fila_alb_'+idAlbaran);
                filaSelected.remove();
                $("#tablaGrillaFacturaSeleccionados tbody").append(data.html_albaran);
                
              }))
  
            }
          
        }
              
      });
    }

    
    var tablaGrillaFacturaSeleccionados = document.getElementById('tablaGrillaFacturaSeleccionados');   
    if(tablaGrillaFacturaSeleccionados){
      tablaGrillaFacturaSeleccionados.addEventListener("click", (event) => {
      
        const clickedTr = event.target;
              
        if (clickedTr.matches('.eliminar_alb_fact')) {        
          let idAlbaran= clickedTr.dataset.idalbaran;          
          const filaDelete = document.getElementById('fila_alb_inv_'+idAlbaran); 
          filaDelete.remove();
          
        }
              
      });
    }        

    let formulario_agregar_albaranes = document.getElementById('formulario_agregar_albaranes');
    if(formulario_agregar_albaranes){
      formulario_agregar_albaranes.addEventListener('submit', function(e) {        
          e.preventDefault();
          let bool = confirm("Esteu segur(a) d'afegir l'albarà a la factura?");
          if(bool){

            let ruta = urlCompleta+'/FacturasClientes/agregarAlbaranesPendientesAFactura';
            let datosForm = new FormData(formulario_agregar_albaranes);                  
            let fetch=new DB(ruta, 'POST').post(datosForm);
  
            fetch.then((respuesta => {              
                
              
              let titulo='';
              let icono ='';
              if(respuesta.error==false){

                $("#tablaGrillaFacturaSeleccionados tbody").html('');
                $("#tablaGrillaBody").html(respuesta.html);
                document.getElementById('baseimponible_importe').innerHTML = respuesta.baseimponible;
                document.getElementById('ivatotal_importe').innerHTML = respuesta.ivatotal;
                document.getElementById('total_importe').innerHTML = respuesta.total;  
                document.getElementById('descuento_importe').innerHTML = respuesta.descuentoimporte;                

                if(respuesta.estado){
                  document.getElementById('estado').value = respuesta.estado;  
                }


                titulo = 'Procés correcte',
                icono = 'success';
                
              }else{                               
                titulo = 'Error';
                icono = 'error';
              }     
              Swal.fire({
                title: titulo,
                text: respuesta.mensaje,
                icon: icono,
                confirmButtonText: 'OK'
              });         
            }))
  
          }                    
      });
    } 
  
    
    
    const formacobro = document.getElementById('formacobro');    

    if(formacobro){

      formacobro.addEventListener('change', function () {
        
        let selected = formacobro.value;               
               
        if(selected == 2){             

          if(document.getElementById("etiqueta_alavista")){             
            document.getElementById("etiqueta_alavista").style.display = "block";
          }else{            
            let label = '<label class="etiqueta_alavista" id="etiqueta_alavista">A la vista</label>';
            document.getElementById("container_formacobro").insertAdjacentHTML('beforeend',label); 
          }

        }else{                        
          if(document.getElementById("etiqueta_alavista")){                      
            document.getElementById("etiqueta_alavista").style.display = "none";
          }
          
        }

      });      
    }

    //ver facturas enviadas por email        
    let ver_emails_enviados = document.getElementById('ver_emails_enviados');
    if(ver_emails_enviados){
      ver_emails_enviados.addEventListener('click', function () {
        
        let idFactura = document.getElementById('id').value;          

        let ruta = urlCompleta+'/FacturasClientes/obtenerEmailsFacturasEnviadas'; 
        let params = {'id' : idFactura};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          if(data.error == false){             
            $('#container_emails').html(data.html);
            $('#modalFormEmailsEnviados').modal('show');
          }else{
            alert("No hi ha correus electrònics enviats per a aquesta factura");
          }
        }))        
      });       
    }        


  }//fin del if

});
