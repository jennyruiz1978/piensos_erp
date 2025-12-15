import DB from './fecth.js';

document.addEventListener("DOMContentLoaded", () => {    
  
  if (window.location.pathname.includes("/RecibosClientes")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b', 'p']      
      if (opcionesControladas.includes(optionCRUD)) {      

        if(optionCRUD == 'u'){

           let idRecibo = event.target.getAttribute('data-idupd');
           if(idRecibo > 0){                    

            let ruta = urlCompleta+'/RecibosClientes/obtenerRecibo'; 
            let params = {'id' : idRecibo};
              
            let fetch=new DB(ruta, 'POST').get(params);
            fetch.then((data => {            

              let form = document.getElementById('formulario_editar_recibo');              
              form.idRecibo.value = idRecibo;
              form.numero_recibo.value = data.datos.numero;
              form.fecha_recibo.value = data.datos.fecha;
              form.vencimiento.value = data.datos.vencimiento;
              form.lugar_recibo.value = data.datos.lugarexpedicion;
              form.importe_recibo.value = data.datos.importe;
              form.concepto_recibo.value = data.datos.concepto;
              form.nombre_librado.value = data.datos.librado;
              form.nombre_librador.value = data.datos.librador;
              form.estado.value = data.datos.estadoactual;
              document.getElementById('estado').classList.remove('impagado', 'pendiente', 'pagado');
              document.getElementById('estado').classList.add(data.datos.estadoactual);           
              
              let texto_alavista = document.getElementById("texto_alavista");
              if(data.formacobro && data.formacobro == 2){
               texto_alavista.style.display = "block";
              }else{
               texto_alavista.style.display = "none";
              }

              mostrarOcultarBotonesPagadoNoPagado(data.datos.estadoactual);

              actualizarColumnaEstadoRecibo(event.target);
              
              //$('#modalFormEditarRecibo').modal('show'); 
              $('#modalFormEditarRecibo').modal({
                backdrop: 'static',
                keyboard: false
              });

            }))
                  
           }

        }else if(optionCRUD == 'b'){          

          let idRecibo = event.target.getAttribute('data-iddel');
          if(idRecibo > 0){                          
            
               
            let bool = confirm("Esteu segur(a) d'eliminar el rebut?");
            
            if(bool){        


              let loader_recibo = document.getElementById('loader_recibo_recibo');
              if(loader_recibo) loader.style.display = 'block';            


            
              let ruta = urlCompleta+'/RecibosClientes/eliminarFilaRecibo'; 
              let params = {'idRecibo' : idRecibo};
    
              let fetch=new DB(ruta, 'POST').get(params);
              fetch.then((data => {            

                 if(loader_recibo) loader_recibo.style.display = 'none';
                
                let titulo = '';
                let icono = '';
                if(data.error == false){
                  titulo = 'Procés correcte';
                  icono = 'success';
                  const filaSelected = document.getElementById('fila_'+idRecibo);
                  filaSelected.remove();    

                }else{
                  titulo = 'Error';
                  icono = 'error';
                }

                Swal.fire({
                  title: titulo,
                  text: data.mensaje,
                  icon: icono,
                  confirmButtonText: 'Tancar'
                });  

              }))            
              
            }            

          }

        }else if(optionCRUD == 'p'){          

          let idRecibo = event.target.getAttribute('data-idpay');

          if(idRecibo > 0){                                      
            
            let estadoRecibo = obtenerTextoEstado(idRecibo);
            
            if(estadoRecibo=='pagado'){
              alert('Aquest rebut ja està pagat.');
            }else{
              
              let bool = confirm("Esteu segur(a) d'pagar el rebut?");
              
              if(bool){        
              
                let ruta = urlCompleta+'/RecibosClientes/cambiarEstadoPagadoRecibo'; 
                let params = {'id' : idRecibo};
        
                let fetch=new DB(ruta, 'POST').get(params);
                fetch.then(
                  (data => {       
        
                    if(data.error == false){                     
                      let fila = document.getElementById('fila_'+idRecibo);
                      let tdEstado = fila.querySelectorAll('td')[7];                       
                      tdEstado.textContent = 'pagado';                      
                      
                    }else{
                      alert(data.mensaje);                   
                    }            
                  })
                );                  
                
              }  

            }
            

          }
        }

      
      } else {
         return
      }
    }

    var clickEdit;
    function actualizarColumnaEstadoRecibo(event) {     
      clickEdit = '' ;
      clickEdit = $(event).closest('tr').find('td:eq(7)');
    }
       

    function obtenerTextoEstado(idTr) {

      let fila = document.getElementById('fila_'+idTr);
            
      if (fila) {      
        let tdEstado = fila.querySelectorAll('td')[7];        
        if (tdEstado) {
          return tdEstado.textContent.trim();
        }
      }      
      return null;
    }
   

    const tablaRecibosClientes = document.getElementById('tablaRecibosClientes');
    if(tablaRecibosClientes){
      document.getElementById('tablaRecibosClientes').addEventListener('click', eventHandler);
    }           

    let formulario_editar_recibo = document.getElementById('formulario_editar_recibo');
    if(formulario_editar_recibo){
      formulario_editar_recibo.addEventListener('submit', function(e) {        
          e.preventDefault();

          let loader_recibo = document.getElementById('loader_recibo_recibo');
          if(loader_recibo) loader.style.display = 'block';

          // Deshabilitar botones
          let buttons = formulario_editar_recibo.querySelectorAll('button');
          buttons.forEach(btn => btn.disabled = true);
          let anclas = formulario_editar_recibo.querySelectorAll('button');
          anclas.forEach(an => an.disabled = true);



          
          let ruta = urlCompleta+'/RecibosClientes/actualizarRecibo';
          let datosForm = new FormData(formulario_editar_recibo);
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {

            if(loader_recibo) loader_recibo.style.display = 'none';
            buttons.forEach(btn => btn.disabled = false);
            anclas.forEach(an => an.disabled = false);
            
            let url = urlCompleta+'/RecibosClientes';
            mostrarResultadoFetchEliminar(respuesta, 0, url);           
            
          }))

          
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
   

    let generar_recibo_pdf = document.getElementById('generar_pdf');
    if(generar_recibo_pdf){
      generar_recibo_pdf.addEventListener('click', function () {
                
        let idRecibo = document.getElementById('idRecibo').value;                                
        window.open(urlCompleta + "/RecibosClientes/exportarPdfRecibo/" + idRecibo);        
        
      });       
    }  

    function mostrarOcultarBotonesPagadoNoPagado(estadoactual) {
      
              let cont_button_product = document.querySelector('.cont_button_product');

            
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
                document.getElementById('estado').value = data.estado;
                document.getElementById('estado').classList.remove('impagado', 'pendiente', 'pagado');
                document.getElementById('estado').classList.add(data.estado);   

                let btn_pagado = document.getElementById('recibo_pagado');
                if(btn_pagado){
                  btn_pagado.remove();
                }             
                let btn_no_pagado = document.getElementById('recibo_no_pagado');   
                if(!btn_no_pagado){
                  btn_no_pagado = `<a class="button_small_bar" id="recibo_no_pagado">No pagat</span></a> `;
                  let cont_button_product = document.querySelector('.cont_button_product');
                  cont_button_product.insertAdjacentHTML('beforeend',btn_no_pagado);
                  cargarEventosBotonesNoPagado();
                } 
                clickEdit.text(data.estado);

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
                document.getElementById('estado').value = data.estado;
                document.getElementById('estado').classList.remove('impagado', 'pendiente', 'pagado');
                document.getElementById('estado').classList.add(data.estado);   

                this.remove();           
                let btn_pagado = document.getElementById('recibo_pagado');   
                if(!btn_pagado){
                  btn_pagado = `<a class="button_small_bar" id="recibo_pagado">Pagat</span></a> `;
                  let cont_button_product = document.querySelector('.cont_button_product');
                  cont_button_product.insertAdjacentHTML('beforeend',btn_pagado);
                  cargarEventosBotonesPagado();
                } 
                clickEdit.text(data.estado);

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
    

    //enviar emails
    let enviar_factura_proveedor = document.getElementById('enviar_factura_proveedor');
    if(enviar_factura_proveedor){
      enviar_factura_proveedor.addEventListener('click', function () {
        
        let idRecibo = document.getElementById('idRecibo').value;   
        document.getElementById('tipoDocumento').value = 'recibo';     
        document.getElementById('idAlbaranClienteEmail').value = idRecibo;
        document.getElementById('asunto').value = "Enviament de rebut";
        document.getElementById('mensaje').value =  "Benvolgut client, adjuntem el rebut corresponent. Una cordial salutació";

        let ruta = urlCompleta+'/RecibosClientes/obtenerDatosEnvioEmailRecibo'; 
        let params = {'id' : idRecibo};

        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          if(data.error == false){
            document.getElementById('title_form_enviar').innerHTML = 'Enviar rebut';      
            
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
            alert("Heu de desar el rebut");
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


  }//fin del if

});
