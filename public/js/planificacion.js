import DB from './fecth.js';
import RowsGrid from './rows_grilla.js';

document.addEventListener("DOMContentLoaded", () => {    

  if (window.location.pathname.includes("/Planificaciones")) {
       
    var urlCompleta = $('#ruta').val();
    
    function eventHandler(event) {       
      let optionCRUD = event.target.getAttribute('data-crud');
      let opcionesControladas = ['c', 'u', 'b']
      if (opcionesControladas.includes(optionCRUD)) {

      

         if (optionCRUD == 'c') {                                 
           
           window.location.href = urlCompleta+"/Planificaciones/altaPlanificaciones";

         }else if(optionCRUD == 'u'){

           let idPlanififcacion = event.target.getAttribute('data-idupd');
           if(idPlanififcacion > 0){   
            window.location = urlCompleta+'/Planificaciones/verPlanificacion/'+idPlanififcacion;
           }

         }
      
      } else {
         return
      }
    }

    const calcular_fechas = document.getElementById('calcular_fechas');
    if(calcular_fechas){
      calcular_fechas.addEventListener('click', function (){
        let fecha_inicio = document.getElementById('fecha_inicio').value;        
        let idPlanificacion = document.getElementById('idPlanificacion').value;

        if(idPlanificacion != '' && idPlanificacion > 0){

          if(fecha_inicio == ''){
            alert('Introduïu dates vàlides');
          }else{
              
           
            let ruta = urlCompleta+'/Planificaciones/crearRangoFechas';
                  
            let data = `id=${idPlanificacion}&fecha_inicio=${fecha_inicio}`;          
          
            crearRangoFechaPlanificacion(data, ruta);
              
          }
        }

      });
    }

    function crearRangoFechaPlanificacion(data, ruta){

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
              if(respuesta.error == false){                                
                
                document.getElementById('container_fechas_cantidades_recojos').innerHTML = respuesta.html;
                document.getElementById('container_agregar_fila').style.display = 'flex';
                document.getElementById('container_totales').style.display = 'flex';     
                document.getElementById('suma_total').innerHTML = 0;
                document.getElementById('semana').value = respuesta.semana;
                cargarDatosParaPlanificacionCeldas();
              }else{                                
                Swal.fire({
                  title: 'Error',
                  text: texto,
                  icon: 'error',
                  confirmButtonText: 'Tancar',
                });                  
              }

              
              
          }
      };      

    }

    const tablaPlanificacion = document.getElementById('tablaPlanificaciones');
    if(tablaPlanificacion){
      document.getElementById('tablaPlanificaciones').addEventListener('click', eventHandler);
    }    

    $(document).on('click', '.btn_guardar_carga', function (e) {
          let idcelda = e.target.dataset.idcelda;          
          let action = 'save-charge';
          let mensajeAlerta = "Aquesta acció desarà la planificació i crearà l'albarà de proveïdor. Esteu segur de continuar?";
          let existeAlbaran = document.getElementById('idalbarandet_'+idcelda).value;
          if(existeAlbaran > 0){
            mensajeAlerta = "Aquesta acció desarà la planificació i actualitzarà l'albarà de proveïdor existent. Esteu segur de continuar?";
          }

          let bool = confirm(mensajeAlerta);
          if (bool) {

            let input_value = document.getElementById('celda_'+idcelda).value;
            let idPlanificacion = document.getElementById('idPlanificacion').value;
            let transportista_value = document.getElementById('transportista_'+idcelda).value;          
            let id_producto_compra = document.getElementById('id_producto_compra').value;
            let idalbarandet = document.getElementById('idalbarandet_'+idcelda).value;
            let cliente_value = document.getElementById('cliente_'+idcelda).value;
            
            if(input_value != ''){
  
              let ruta = urlCompleta+'/Planificaciones/actualizarCeldaPlanificacion';                  
              let data = `id=${idcelda}&input_value=${input_value}&idplanificacion=${idPlanificacion}&idtransportista=${transportista_value}&id_producto_compra=${id_producto_compra}&idalbarandet=${idalbarandet}&idcliente=${cliente_value}`;  
              actualizarValorCarga(data, ruta, idcelda, action);
  
            }
          }
          
    });

    function actualizarValorCarga(data, ruta, idcelda, action=false){

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

              if(respuesta.error == false){    
                             
                let rutaRedirect = '';
                mostrarResultadoFetchAcciones(respuesta, 0, rutaRedirect);

                /* document.getElementById('suma_total').innerHTML = respuesta.total;    
                
                if(action=='del-plan-date'){

                  let rutaRedirect = '';
                  mostrarResultadoFetchAcciones(respuesta, 0, rutaRedirect);

                }else{
                  document.getElementById('idalbarandet_'+idcelda).value = respuesta.idalbarandet;
                
                  if(!document.getElementById('btn_ver_albaran_cliente_'+idcelda)){                  
                    document.getElementById('btn_albaran_cliente_'+idcelda).style.display = 'block';
                  }                                
                  let btn_ver_albaran_prov = document.getElementById('btn_ver_albaran_prov_'+idcelda);
                  btn_ver_albaran_prov.href = urlCompleta+'/AlbaranesProveedores/verAlbaran/'+respuesta.idalbaranprov;
                  btn_ver_albaran_prov.style.display = 'block';
  
                  let  btn_ver_albaran_fab = document.getElementById('btn_ver_albaran_fab_'+idcelda);
                  btn_ver_albaran_fab.href = urlCompleta+'/AlbaranesProveedores/verAlbaran/'+respuesta.idalbaranfabrica;
                  btn_ver_albaran_fab.style.display = 'block';
                                                
      
                  Swal.fire({
                    title: 'Procés correcte',
                    text: texto,
                    icon: 'success',                  
                    timer: 4000,
                  });        
                }   */     

              }else{                                
                Swal.fire({
                  title: 'Error',
                  text: texto,
                  icon: 'error',
                  confirmButtonText: 'Tancar',
                });                  
              }
              
          }
      };      

    }
    
    const descartar_planificacion = document.getElementById('descartar_planificacion');    
    if(descartar_planificacion){
      let idPlanificacion = document.getElementById('idPlanificacion').value;
      descartar_planificacion.addEventListener('click', function () {    
        document.getElementById('idPlanEliminar').value = idPlanificacion;
        
        let ruta = urlCompleta+'/Planificaciones/verificarSiExistenAlbaranesPorIdPlanificacion'; 
        let params = {'idPlanificacion' : idPlanificacion};
  
        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          
          let texto_mensaje= "";

          let exitenalbaranes_prov = data.existe_proveedor;        
          
          if(exitenalbaranes_prov && exitenalbaranes_prov.length > 0){
            let alb_prov = "";
            for (let index = 0; index < exitenalbaranes_prov.length; index++) {            
              alb_prov += exitenalbaranes_prov[index].numalbaran+"\n";
            }
            texto_mensaje += "<p>Hi ha albarans de proveïdor generats per a aquesta recollida: "+alb_prov+"</p>";
          }
  
          let exitenalbaranes_cli = data.existe_cliente;        
          
          if(exitenalbaranes_cli && exitenalbaranes_cli.length > 0){
            let alb_cli = "";
            for (let index = 0; index < exitenalbaranes_cli.length; index++) {            
              alb_cli += exitenalbaranes_cli[index].numalbaran+"\n";
            }
            texto_mensaje += "<p>Hi ha albarans de client generats per a aquesta recollida: "+alb_cli+"</p>";
          }
            
          let texto_si_existe ="";
          if(texto_mensaje!= ""){
            texto_si_existe = " i els seus albarans respectius ";
          }
          let bool = texto_mensaje+"\n Esteu segur d'eliminar la recollida "+texto_si_existe+" ?";  
          
          document.getElementById('mensaje_eliminar_plan').innerHTML = bool;
          $('#modalFormEliminarPlanificacion').modal('show');          
        }))    
        
      });
    }

    let formulario_eliminar_planificacion = document.getElementById('formulario_eliminar_planificacion');
    if(formulario_eliminar_planificacion){
      formulario_eliminar_planificacion.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_eliminar_planificacion.idPlanEliminar.value != ''){
            
            $('#modalFormEliminarCliente').modal('hide');
            let ruta = urlCompleta+'/Planificaciones/eliminarPlanificacion';
            let data = `id=${formulario_eliminar_planificacion.idPlanEliminar.value}`;
            borrarPlanificacionPorId(data, ruta);
          }          
      });
    }

    let formulario_crear_planificacion = document.getElementById('formulario_crear_planificacion');
    if(formulario_crear_planificacion){
      formulario_crear_planificacion.addEventListener('submit', function(e) {                
          e.preventDefault();
          if(formulario_crear_planificacion.idPlanEliminar.value != ''){
            
            $('#modalFormEliminarCliente').modal('hide');
            let ruta = urlCompleta+'/Planificaciones/eliminarPlanificacion';
            let data = `id=${formulario_crear_planificacion.idPlanEliminar.value}`;
            borrarPlanificacionPorId(data, ruta);
          }          
      });
    }

    function borrarPlanificacionPorId(data, ruta){
      
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
              if(respuesta.error == false){                                
                Swal.fire({
                  title: 'Procés correcte',
                  text: texto,
                  icon: 'success',                  
                  timer: 3000,
                });
                setTimeout(function () {
                  window.location = urlCompleta+'/Planificaciones';
                },2000);
                

              }else{                                
                Swal.fire({
                  title: 'Error',
                  text: texto,
                  icon: 'error',
                  confirmButtonText: 'Tancar',
                });                  
              }
         
          }
      };      

    }

    if(document.getElementById('btn_actualizar_precio')){
      const btn_guardar_precio = document.getElementById('btn_actualizar_precio');
      btn_guardar_precio.addEventListener('click', function () {
        let bool = confirm("¿Aquesta acció actualitzarà el preu dels albarans aquesta setmana?");
          if (bool) {
            let precio = document.getElementById('precio_semana_plan').value;
            let idPlanificacion = document.getElementById('idPlanificacion').value;
            if(precio != ''){
              let ruta = urlCompleta+'/Planificaciones/actualizarPrecioPlanificacion';
              let data = `id=${idPlanificacion}&precio=${precio}`;
              actualizarPrecioPlanificacion(data, ruta);
            }else{
              alert('El precio debe ser mayor que cero');
            }
            
          }
      });
    }
    
    function actualizarPrecioPlanificacion(data, ruta){
      
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
              msgShow(texto,'precio'); 
         
          }
      };      

    }

    function msgShow(mensaje, param){      
      let mensajeLogin = document.getElementById("msgErrores_"+param);
      mensajeLogin.innerHTML = mensaje;
      msgClean(mensajeLogin);
    }
    function msgClean(mensajeLogin){
      setTimeout(function(){        
        mensajeLogin.innerHTML='';
      },1000)
    }

        
    let agregar_linea = document.getElementById('agregar_linea');
    if(agregar_linea){
      agregar_linea.addEventListener('click', function () {
        
     
            let idPlanificacion = document.getElementById('idPlanificacion').value;
            let ruta = urlCompleta+'/Planificaciones/crearFilaNuevaPlanificacion';   
            let params = {'idPlanificacion':idPlanificacion};
                      
            let fetch=new DB(ruta, 'POST').get(params);
    
            fetch.then((data => {                                                  
              
              if(data.error){
                
                Swal.fire({
                  title: 'Error',
                  text: data.mensaje,
                  icon: 'error',
                  confirmButtonText: 'Tancar',
                }); 
                
              }else{
                document.getElementById('body_tabla_planificacion').insertAdjacentHTML('beforeend',data.html); 
                //cargarDatosParaPlanificacionCeldas();
              }
              
            }))

     
      })
    }

    $(document).on('click', '.celdaEliminarPlan', function () {
      
      let idrecojo = this.dataset.idrecojo;

      let ruta = urlCompleta+'/Planificaciones/verificarSiExistenAlbaranesPorIdRecojo'; 
      let params = {'idrecojo' : idrecojo};

      let fetch=new DB(ruta, 'POST').get(params);
      fetch.then((data => {            
        
        let texto_mensaje= "";

        let exitenalbaranes_prov = data.existe_proveedor;        
        
        if(exitenalbaranes_prov && exitenalbaranes_prov.length > 0){
          let alb_prov = "";
          for (let index = 0; index < exitenalbaranes_prov.length; index++) {            
            alb_prov += exitenalbaranes_prov[index].numalbaran+"\n";
          }
          texto_mensaje += "Hi ha albarans de proveïdor generats per a aquesta recollida: "+alb_prov;
        }

        let exitenalbaranes_cli = data.existe_cliente;        
        
        if(exitenalbaranes_cli && exitenalbaranes_cli.length > 0){
          let alb_cli = "";
          for (let index = 0; index < exitenalbaranes_cli.length; index++) {            
            alb_cli += exitenalbaranes_cli[index].numalbaran+"\n";
          }
          texto_mensaje += "Hi ha albarans de client generats per a aquesta recollida: "+alb_cli;
        }
          
        let texto_si_existe ="";
        if(texto_mensaje!= ""){
          texto_si_existe = " i els seus albarans respectius ";
        }
        let bool = confirm(texto_mensaje+"\n Esteu segur d'eliminar la recollida "+texto_si_existe+" ?");    

        if(bool){        
          
          let ruta = urlCompleta+'/Planificaciones/eliminarRecojo'; 
          let params = {'idrecojo' : idrecojo};
  
          let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {            
            
            if(data.error == false){
              document.getElementById('suma_total').innerHTML = data.total;
              const filaSelected = document.getElementById('recojo_'+idrecojo);
              filaSelected.remove();    
              
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

      }))  

                   
      

    });
                
      
    cargarDatosParaPlanificacionCeldas();
    function cargarDatosParaPlanificacionCeldas() {
      
      
      let body_tabla_planificacion = document.getElementById('body_tabla_planificacion');   
      if(body_tabla_planificacion){
        body_tabla_planificacion.addEventListener("change", (event) => {
        
          const clickedElement = event.target;
  
          if (clickedElement.matches('.cliente_select_plan')) { 
            
            let idcelda = clickedElement.dataset.idcelda;
            let idcliente = document.getElementById('cliente_'+idcelda).value;
            let idtransportista = document.getElementById('transportista_'+idcelda).value;
  
            if(idtransportista > 0 && idcliente > 0 ){
  
              
              let ruta = urlCompleta+'/Planificaciones/buscarDatosParaPlanificacionCelda'; 
              let params = {'idcelda' : idcelda, 'idcliente' : idcliente, 'idtransportista' : idtransportista};
    
              let fetch=new DB(ruta, 'POST').get(params);
              fetch.then((data => {            
  
                document.getElementById("precio_cliente_"+idcelda).innerHTML = data.datos.msgPrecioCliente;
                document.getElementById("zona_precio_transportista_"+idcelda).innerHTML = data.datos.msgTransportista;
                
              }))            
                                
            }                                                        
          }
                            
        });
      } 
  

    }

    $(document).on('click', '.btn_albaran_cliente', function (e) {
      let idcelda = e.target.dataset.idcelda;
      
      let bool = confirm("¿Esteu segur(a) de generar l'albarà per al client??");
      if (bool) {
      
        let input_value = document.getElementById('celda_'+idcelda).value;
        let idPlanificacion = document.getElementById('idPlanificacion').value;
        let transportista_value = document.getElementById('transportista_'+idcelda).value;      
        let id_producto_compra = document.getElementById('id_producto_compra').value;
        let idalbarandet = document.getElementById('idalbarandet_'+idcelda).value;
        let idalbarandetcli = document.getElementById('idalbarandetcli_'+idcelda).value;
        
        let cliente_value = document.getElementById('cliente_'+idcelda).value;
        
        if(input_value != ''){
  
          let ruta = urlCompleta+'/Planificaciones/crearAlbaranClienteDesdeCeldaPlanificacion';                  
          let data = `id=${idcelda}&input_value=${input_value}&idplanificacion=${idPlanificacion}&idtransportista=${transportista_value}&id_producto_compra=${id_producto_compra}&idalbarandet=${idalbarandet}&idcliente=${cliente_value}&idalbarandetcli=${idalbarandetcli}`;  
          enviarDatosCrearAlbaranCliente(data, ruta);
  
        }
        
      }
        
    });

    function enviarDatosCrearAlbaranCliente(data, ruta){

      let loader = document.getElementById('loader_planificacion');
      if(loader) loader.style.display = 'block';

      // Deshabilitar botones
      let buttons = formulario_crear_planificacion.querySelectorAll('button');
      buttons.forEach(btn => btn.disabled = true);
      let anclas = formulario_crear_planificacion.querySelectorAll('a');
      anclas.forEach(an => an.disabled = true);



      let xhr = new XMLHttpRequest();
      xhr.open("POST", ruta, true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(          
        data
      );
      xhr.onreadystatechange = function() {
        
        if (xhr.readyState === 4) {

          // ✅ Restaurar botones y enlaces
          buttons.forEach(btn => btn.disabled = false);
          anclas.forEach(an => an.classList.remove('disabled'));
          if(loader) loader.style.display = 'none';

          if (/* this.readyState == 4 &&  */this.status == 200) {
              let respuesta = this.responseText;
              respuesta = JSON.parse(respuesta);     
              
              if(respuesta.total){
                document.getElementById('suma_total').innerHTML = respuesta.total;      
              }              
              
              let rutaRedirect = '';
              mostrarResultadoFetchAcciones(respuesta, 0, rutaRedirect);      
              
          }else {
                console.error('Error en la petición AJAX', xhr.status, xhr.statusText);
          }
        }


      };      

    }
    
    function mostrarResultadoFetchAcciones(respuesta, eliminarFila=false, url=false) {
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


    const cerrar_planificacion = document.getElementById('cerrar_planificacion');    
    if(cerrar_planificacion){
      let idPlanificacion = document.getElementById('idPlanificacion').value;

      cerrar_planificacion.addEventListener('click', function () {            
        
        let ruta = urlCompleta+'/Planificaciones/verificarSiPlanificacionSePuedeEliminar'; 
        let params = {'idPlanificacion' : idPlanificacion};
  
        let fetch=new DB(ruta, 'POST').get(params);
        fetch.then((data => {            
          
          if(data.error==false){
            window.location.href = urlCompleta+"/Planificaciones";
          }

        }))    
        
      });
    }


    $(document).on('click', '.btn_eliminar_carga', function (e) {

      let idcelda = e.target.dataset.idcelda;          
      let action = 'del-plan-date';
      let mensajeAlerta = "Atenció, la planificació que vol eliminar té albarans generats. Esteu segurs d'eliminar la planificació i els albarans corresponents?";    

      let bool = confirm(mensajeAlerta);

      if (bool) {        
        let idPlanificacion = document.getElementById('idPlanificacion').value;
        
          let ruta = urlCompleta+'/Planificaciones/eliminarCeldaPlanificacionFecha';                             
          let data = `id=${idcelda}&idplanificacion=${idPlanificacion}`;  
          actualizarValorCarga(data, ruta, idcelda, action);
      }      
    });

  }    

});
