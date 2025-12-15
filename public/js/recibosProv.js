import DB from './fecth.js';

document.addEventListener("DOMContentLoaded", () => {    
  
  if (window.location.pathname.includes("/RecibosProveedores")) {           
    
    var urlCompleta = $('#ruta').val();           

    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']      
      if (opcionesControladas.includes(optionCRUD)) {      

        if(optionCRUD == 'u'){

           let idRecibo = event.target.getAttribute('data-idupd');
           if(idRecibo > 0){                    

            let ruta = urlCompleta+'/RecibosProveedores/obtenerRecibo'; 
            let params = {'id' : idRecibo};
              
            let fetch=new DB(ruta, 'POST').get(params);
            fetch.then((data => {            

              let form = document.getElementById('formulario_editar_recibo');
             
              form.idRecibo.value = idRecibo;
              form.numero_recibo.value = data.datos.numero;
              form.fecha_recibo.value = data.datos.fecha;
              form.lugar_recibo.value = data.datos.lugarexpedicion;
              form.importe_recibo.value = data.datos.importe;
              form.concepto_recibo.value = data.datos.concepto;
              form.nombre_librado.value = data.datos.librado;
              form.nombre_librador.value = data.datos.librador;

              form.vencimiento.value = data.datos.vencimiento;              
              form.estado.value = data.datos.estadoactual;
              document.getElementById('estado').classList.remove('impagado', 'pendiente', 'pagado');
              document.getElementById('estado').classList.add(data.datos.estadoactual);                 

              mostrarOcultarBotonesPagadoNoPagado(data.datos.estadoactual);

              actualizarColumnaEstadoRecibo(event.target);

              $('#modalFormEditarRecibo').modal('show'); 
            }))

                  
           }

        }else if(optionCRUD == 'b'){

          let idRecibo = event.target.getAttribute('data-iddel');
          if(idRecibo > 0){                          
            
               
            let bool = confirm("Esteu segur(a) d'eliminar el rebut?");
            
            if(bool){        
            
              let ruta = urlCompleta+'/RecibosProveedores/eliminarFilaRecibo'; 
              let params = {'idRecibo' : idRecibo};
    
              let fetch=new DB(ruta, 'POST').get(params);
              fetch.then((data => {            
                
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

        }
      
      } else {
         return
      }
    }

    var clickEdit;
    function actualizarColumnaEstadoRecibo(event) {     
      clickEdit = '' ;
      clickEdit = $(event).closest('tr').find('td:eq(6)');
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
        let ruta = urlCompleta+'/RecibosProveedores/cambiarEstadoPagadoRecibo'; 
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
        let ruta = urlCompleta+'/RecibosProveedores/cambiarEstadoNoPagadoRecibo'; 
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

    const tablaRecibosProveedores = document.getElementById('tablaRecibosProveedores');
    if(tablaRecibosProveedores){
      document.getElementById('tablaRecibosProveedores').addEventListener('click', eventHandler);
    }           

    let formulario_editar_recibo = document.getElementById('formulario_editar_recibo');
    if(formulario_editar_recibo){
      formulario_editar_recibo.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/RecibosProveedores/actualizarRecibo';
          let datosForm = new FormData(formulario_editar_recibo);
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {
            
            let url = urlCompleta+'/RecibosProveedores';
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
        



  }//fin del if

});
