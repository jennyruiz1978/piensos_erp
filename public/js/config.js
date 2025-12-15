import DB from './fecth.js';

document.addEventListener("DOMContentLoaded", () => {    
  
  if (window.location.pathname.includes("/Configuracion")) {           
    
    var urlCompleta = $('#ruta').val();       
    
    
    let formulario_config_piensos = document.getElementById('formulario_config_piensos');
    if(formulario_config_piensos){
      formulario_config_piensos.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/Configuracion/actualizarDatosConfiguracionEmpresa';
          let datosForm = new FormData(formulario_config_piensos);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                       

            limpiarMensajesCamposError();

            if(respuesta.error==false){                                      
              let datos = respuesta.datos;
              document.getElementById('razonsocialpiensos').value = datos.razonsocialpiensos;
              document.getElementById('cifpiensos').value = datos.cifpiensos;
              document.getElementById('direccionpiensos').value = datos.direccionpiensos;
              document.getElementById('codigopostalpiensos').value = datos.codigopostalpiensos;
              document.getElementById('localidadpiensos').value = datos.localidadpiensos;
              document.getElementById('provinciapiensos').value = datos.provinciapiensos;
              Swal.fire({
                title: 'Procés correcte',
                text: respuesta.mensaje,
                icon: 'success',
                confirmButtonText: 'OK'
              });

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
                confirmButtonText: 'Tancar'
              });
            }                                           
          }))          
      });
    } 

     
    let formulario_config_correo = document.getElementById('formulario_config_correo');
    if(formulario_config_correo){
      formulario_config_correo.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/Configuracion/actualizarDatosConfiguracionCorreo';
          let datosForm = new FormData(formulario_config_correo);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                       

            limpiarMensajesCamposError();

            if(respuesta.error==false){           
              let datos = respuesta.datos;                                         
              document.getElementById('remitente').value = datos.remitente;
              document.getElementById('correo').value = datos.correo;
              document.getElementById('passwordcorreo').value = datos.passwordcorreo;
              document.getElementById('host').value = datos.host;
              document.getElementById('puerto').value = datos.puerto;
              document.getElementById('protocolo').value = datos.protocolo;
              Swal.fire({
                title: 'Procés correcte',
                text: respuesta.mensaje,
                icon: 'success',
                confirmButtonText: 'OK'
              });

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
                confirmButtonText: 'Tancar'
              });
            }                                           
          }))          
      });
    } 

    let formulario_config_transportista = document.getElementById('formulario_config_transportista');
    if(formulario_config_transportista){
      formulario_config_transportista.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          let ruta = urlCompleta+'/Configuracion/actualizarDatosConfiguracionTransportista';
          let datosForm = new FormData(formulario_config_transportista);
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                       

            limpiarMensajesCamposError();

            if(respuesta.error==false){           
              let datos = respuesta.datos;                                         
              document.getElementById('idtransportista').value = datos.idtransportista;              
              document.getElementById('idprovfabrica').value = datos.idprovfabrica;    
              document.getElementById('precioprovfab').value = datos.precioprovfab;    
              document.getElementById('idproductotransp').value = datos.idproductotransp;    
              document.getElementById('idproductofab').value = datos.idproductofab;    

              Swal.fire({
                title: 'Procés correcte',
                text: respuesta.mensaje,
                icon: 'success',
                confirmButtonText: 'OK'
              });

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
                confirmButtonText: 'Tancar'
              });
           
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

    
    let formulario_copia_seguridad = document.getElementById('formulario_copia_seguridad');
    if(formulario_copia_seguridad){
      formulario_copia_seguridad.addEventListener('submit', function(e) {        
          e.preventDefault();
          
          document.getElementById('guardarcopia').value = 1;
          let ruta = urlCompleta+'/Configuracion/guardarCopiaDeSeguridad';
          let datosForm = new FormData(formulario_copia_seguridad);                  
          let fetch=new DB(ruta, 'POST').post(datosForm);

          fetch.then((respuesta => {                                   

            let texto = (respuesta.error)? 'error': 'success';
            Swal.fire({
              title: 'Procés correcte',
              text: respuesta.mensaje,
              icon: texto,
              confirmButtonText: 'OK'
            });
            
          }))          
      });
    } 

  }//fin del if

});
