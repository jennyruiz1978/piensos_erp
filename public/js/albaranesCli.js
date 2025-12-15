import DB from './fecth.js';
import RowsGrid from './rows_grilla.js';

document.addEventListener("DOMContentLoaded", () => {    
  


  if (window.location.pathname.includes("/AlbaranesClientes")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']      
      if (opcionesControladas.includes(optionCRUD)) {      

         if (optionCRUD == 'c') {

           window.location.href = urlCompleta+"/AlbaranesClientes/altaAlbaranes";

         }else if(optionCRUD == 'u'){

           let idAlbaran = event.target.getAttribute('data-idupd');
           if(idAlbaran > 0){   
            window.location = urlCompleta+'/AlbaranesClientes/verAlbaran/'+idAlbaran;            
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

    const tablaAlbaranesClientes = document.getElementById('tablaAlbaranesClientes');
    if(tablaAlbaranesClientes){
      document.getElementById('tablaAlbaranesClientes').addEventListener('click', eventHandler);
    }    

    const cliente_select = document.getElementById('idcliente');    
    if(cliente_select){

      cliente_select.addEventListener('change', function () {
        
        let cliente_selected = cliente_select.value;            
        let fechaAlbaran = document.getElementById('fecha').value;
        
        if(cliente_selected != ''){          

          let ruta = urlCompleta+'/AlbaranesClientes/obtenerClienteYPrecioClienteZona'; 
          let arrPro = document.querySelectorAll("[name='idArticulo[]']");
          let array = [];
          if(arrPro.length > 0){
            for (let index = 0; index < arrPro.length; index++) {
              const element = arrPro[index].value;    
              array.push(element);          
            }
          }
          let params = {'id' : cliente_selected, 'fecha' : fechaAlbaran, 'productos': array};

          let clientes=new DB(ruta, 'POST').get(params);
          clientes.then((data => {            
            document.getElementById('nif_albaran').value = data.datos.nif;                      
            /*
            var precios = document.querySelectorAll("[name='precioArticulo[]']");
            for (var pro of precios) {                
                pro.value = data.precioVenta;                      
            }
            */
            document.getElementById('zona').value = data.datos.zona;  
            document.getElementById('margen').value = data.margen;              
           
          }))

        }

      });      
    }

    let formCrearAlbaranCliente = document.getElementById('formulario_crear_albaran_cli');
    if(formCrearAlbaranCliente){
      formCrearAlbaranCliente.addEventListener('submit', function(e) {        
          e.preventDefault();

          let loader_albaran = document.getElementById('loader_albaran');
          if(loader_albaran) loader_albaran.style.display = 'block';

          // Deshabilitar botones
          let buttons = formCrearAlbaranCliente.querySelectorAll('button');
          buttons.forEach(btn => btn.disabled = true);
          let anclas = formCrearAlbaranCliente.querySelectorAll('a');
          anclas.forEach(an => an.disabled = true);

          
          let ruta = urlCompleta+'/AlbaranesClientes/actualizarAlbaranCompleto';
          let datosForm = new FormData(formCrearAlbaranCliente);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                       

            if(loader_albaran) loader_albaran.style.display = 'none';
            buttons.forEach(btn => btn.disabled = false);
            anclas.forEach(an => an.disabled = false);

            limpiarMensajesCamposError();
            mostrarResultadoFetch(respuesta);

            if(respuesta.error==false){

              document.getElementById('numero').value = respuesta.numero;

              let tableId = document.getElementById('tablaGrilla');                        
              let tBody = tableId.getElementsByTagName('tbody')[0];            
              tBody.innerHTML = respuesta.html;
  
              document.getElementById('baseimponible_importe').innerHTML = respuesta.baseimponible;
              document.getElementById('ivatotal_importe').innerHTML = respuesta.ivatotal;
              document.getElementById('total_importe').innerHTML = respuesta.total;  

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

          //loader_albaran_eliminar
          let loader_albaran_eliminar = document.getElementById('loader_albaran_eliminar');
          if(loader_albaran_eliminar) loader_albaran_eliminar.style.display = 'block';

          // Deshabilitar botones
          let buttons = formulario_eliminar_albaran.querySelectorAll('button');
          buttons.forEach(btn => btn.disabled = true);
          let anclas = formulario_eliminar_albaran.querySelectorAll('a');
          anclas.forEach(an => an.disabled = true);


                            
          let ruta = urlCompleta+'/AlbaranesClientes/eliminarAlbaran';
          let datosForm = new FormData(formulario_eliminar_albaran);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);
          
          fetch.then((respuesta => {   

            let rutaIni = urlCompleta+'/AlbaranesClientes'; 

            if(loader_albaran_eliminar) loader_albaran_eliminar.style.display = 'none';
            buttons.forEach(btn => btn.disabled = false);
            anclas.forEach(an => an.disabled = false);

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

    
    let btn_generar_factura_cliente = document.getElementById('generar_factura_cliente');
    if(btn_generar_factura_cliente){
      btn_generar_factura_cliente.addEventListener('click', function () {
        let idAlbaran = document.getElementById('id').value;        
        document.getElementById('idAlbaranCliente').value = idAlbaran;
        let idcliente = document.getElementById('idcliente').value;

        let ruta = urlCompleta+'/AlbaranesClientes/obtenerDatosAlbaran'; 
        let params = {'id' : idAlbaran, 'idcliente': idcliente};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          if(data.error == false){
            document.getElementById('nombre_cliente').value = data.cabecera.cliente;
            document.getElementById('nif_cliente').value = data.cabecera.nif;                       
            document.getElementById('fecha_factura_cliente').value = data.cabecera.fecha;
            document.getElementById('numero_albaran_cliente').value = data.cabecera.numero;
            document.getElementById('observaciones_factura_cliente').value = data.cabecera.observaciones;
            
            let tableFact = document.getElementById('tablaGrillaFactura');                        
            let tBody = tableFact.getElementsByTagName('tbody')[0];            
            tBody.innerHTML = data.detalle;

            construirSelectCuentasBancarias(data.cuentas_bancarias);
            construirSelectFormasDeCobro(data.formas_pago, data.forma_pago_cliente);               
            
            $('#modalFormCrearFacturaCliente').modal('show');
          }       
        }))        
      });       
    }    
    
    function construirSelectCuentasBancarias(cuentas_bancarias){

      let selectCuentas = '<option selected disabled value="">Seleccionar</option>';                        
    
      if(cuentas_bancarias && cuentas_bancarias.length > 0){                
        for (let index = 0; index < cuentas_bancarias.length; index++) {                
          selectCuentas += '<option value="'+cuentas_bancarias[index].id+'">'+cuentas_bancarias[index].numerocuenta+'</option>';
        }
      }
      document.getElementById('cuentabancaria').innerHTML = selectCuentas;  
    }

    function construirSelectFormasDeCobro(formas_pago, forma_pago_cliente){

      let selectHtml = '<option selected disabled value="">Seleccionar</option>';                        
    
      if(formas_pago && formas_pago.length > 0){
                      
        for (let index = 0; index < formas_pago.length; index++) {                
          selectHtml += '<option value="'+formas_pago[index].id+'" '+ ((formas_pago[index].id == forma_pago_cliente)? "selected":"" ) +' >'+formas_pago[index].formadepago+'</option>';
          /* console.log('Comparando:', formas_pago[index].id, 'con', forma_pago_cliente);
          selectHtml += '<option value="' + formas_pago[index].id + '" ' + ((formas_pago[index].id == forma_pago_cliente) ? 'selected' : '') + '>' + formas_pago[index].formadepago + '</option>';
           */
          
        }
      }
      document.getElementById('formacobro').innerHTML = selectHtml;  
    }

    $('#modalFormCrearFacturaCliente').on('hidden.bs.modal', function () {
			$('#formulario_crear_factura_cliente').trigger("reset");			
		});     

    let formCrearFacturaCliente = document.getElementById('formulario_crear_factura_cliente');
    if(formCrearFacturaCliente){
      formCrearFacturaCliente.addEventListener('submit', function(e) {        
          e.preventDefault();

          let loader = document.getElementById('loader');
          if(loader) loader.style.display = 'block';

          // Deshabilitar botones
          let buttons = formCrearFacturaCliente.querySelectorAll('button');
          buttons.forEach(btn => btn.disabled = true);
          
          let ruta = urlCompleta+'/FacturasClientes/crearFacturaClienteDesdeAlbaran';
          let datosForm = new FormData(formCrearFacturaCliente);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {

            if(loader) loader.style.display = 'none';
            buttons.forEach(btn => btn.disabled = false);

            limpiarMensajesCamposError();            
            
            let numerofactura = 0;
            let url = '';            

            if(respuesta.error==false){
              numerofactura = respuesta.idfactura;
              url = urlCompleta+'/FacturasClientes/verFactura/'+numerofactura;
              
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
            mostrarResultadoFetchEliminar(respuesta, 0, url);

          })).catch(error => {
            if(loader) loader.style.display = 'none';
            buttons.forEach(btn => btn.disabled = false);
            console.error('Error en la petición:', error);
          });

          
      });
    } 
    

    const dias_albaran_cliente = document.getElementById('dias_albaran_cliente');    
    if(dias_albaran_cliente){

      dias_albaran_cliente.addEventListener('keyup', function () {                        
        
          let dias = 0;
          if(this.value.trim()){
            dias = this.value.trim();
          }                        
          let fechaAlbaran = document.getElementById('fecha_factura_cliente').value;

          let ruta = urlCompleta+'/FacturasClientes/calcularFechaVencimientoFacturaCliente';         
          let params = {'dias' : dias, 'fecha' : fechaAlbaran};

          let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {                       
            document.getElementById('fecha_vencimiento_cliente').value = data.fechaVecimiento;       
          }))

        
      });      
    }

    const fecha_vencimiento_cliente = document.getElementById('fecha_vencimiento_cliente');    
    if(fecha_vencimiento_cliente){
           
      fecha_vencimiento_cliente.addEventListener('change', function () {                        
        
        let fecha_vencimiento_cliente = '';
        if(this.value.trim()){
          fecha_vencimiento_cliente = this.value.trim();
        }                                  
        let fecha_factura_cliente = document.getElementById('fecha_factura_cliente').value;
        

        let ruta = urlCompleta+'/FacturasClientes/calcularDiasCobroFacturaCliente';         
        let params = {'vencimiento' : fecha_vencimiento_cliente, 'fecha_factura_cliente':fecha_factura_cliente};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {                       
          document.getElementById('dias_albaran_cliente').value = data.dias_albaran_cliente;       
        }))        
      }); 

    }

    let generar_pdf = document.getElementById('generar_pdf');
    if(generar_pdf){
      generar_pdf.addEventListener('click', function () {
                
        let idAlbaran = document.getElementById('id').value;                                
        window.open(urlCompleta + "/AlbaranesClientes/exportarPdfFactura/" + idAlbaran);        
        
      });       
    }

    
    let agregar_linea = document.getElementById('agregar_linea');
    if(agregar_linea){
      agregar_linea.addEventListener('click', function () {
                
        let ruta = urlCompleta+'/AlbaranesClientes/obtenerDatosParaFilaNueva';   
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
            
              let ruta = urlCompleta+'/AlbaranesClientes/eliminarFilaDetalle'; 
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

    //enviar emails
    let enviar_factura_proveedor = document.getElementById('enviar_factura_proveedor');
    if(enviar_factura_proveedor){
      enviar_factura_proveedor.addEventListener('click', function () {

        let idAlbaran = document.getElementById('id').value;        
        document.getElementById('idAlbaranClienteEmail').value = idAlbaran;
        document.getElementById('tipoDocumento').value = 'albaran';
        document.getElementById('asunto').value = "Enviament de albará";
        document.getElementById('mensaje').value =  "Benvolgut client, adjuntem l'albarà corresponent. Una cordial salutació";      

        let ruta = urlCompleta+'/AlbaranesClientes/obtenerDatosEnvioEmailAlbaran'; 
        let params = {'id' : idAlbaran};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {    
                    
          if(data.error == false){
            document.getElementById('title_form_enviar').innerHTML = 'Enviar albarà';   
            
            // Cargar los contactos en el select
            let selectContactos = document.getElementById('contactosCliente');
            selectContactos.innerHTML = '<option selected disabled value="">Seleccionar contacto</option>';
            if(data.contactos && data.contactos.length > 0) {
              data.contactos.forEach(contacto => {
                selectContactos.innerHTML += `<option value="${contacto.email}">${contacto.nombre} - ${contacto.email}</option>`;
              });
            }
            
            $('#modalFormEnviarFacturaCliente').modal('show');
          } else{
            alert("Heu de desar l'albarà");
          }      
        }))        
      });       
    }

    /////
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
            let datosForm = new FormData(this);                  
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

    let tablaGrillaBody = document.getElementById('tablaGrillaBody');   
    if(tablaGrillaBody){
      tablaGrillaBody.addEventListener("change", (event) => {
      
        const clickedElement = event.target;
        
        if (clickedElement.matches('.articulo')) { 
          
          let idorden = clickedElement.dataset.idorden;
          let idcliente = document.getElementById('idcliente').value;

          let ruta = urlCompleta+'/AlbaranesClientes/obtenerProductoPrecioPorCliente';              
          let params = {'id' : clickedElement.value, 'idcliente' : idcliente};

          let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {            
            
            if(data.error == false){               
           
              document.getElementById('unidadArticulo'+idorden).value = data.datos.abrev_unidad;
              document.getElementById('iva'+idorden).value = data.datos.iva;
              document.getElementById('precioArticulo'+idorden).value = data.precio;

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
        
        let ruta = urlCompleta+'/AlbaranesClientes/verificarSiAlbaranSePuedeEliminar'; 
        let params = {'idAlbaran' : idAlbaran};
  
        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          
          if(data.error==false){
            window.location.href = urlCompleta+"/AlbaranesClientes";
          }

        }))    
        
      });
    }


  }    

});
