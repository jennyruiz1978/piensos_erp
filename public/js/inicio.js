import DB from './fecth.js';

document.addEventListener("DOMContentLoaded", () => {    
  
  if (window.location.pathname.includes("/Inicio")) {           
    
    var urlCompleta = $('#ruta').val();       
    
    
    let buscarfechasDashboard = document.getElementById('buscarfechasDashboard');
    if(buscarfechasDashboard){
      buscarfechasDashboard.addEventListener('click', function () {

        let ini = document.getElementById('fechaIni').value;
        let fin = document.getElementById('fechaFin').value;
                    
        let ruta = urlCompleta+'/Inicio/buscarDatosDashboardSegunFechas'; 
        let params = {'ini' : ini, 'fin' :fin};
          
        let fetch=new DB(ruta, 'POST').get(params);
          fetch.then((data => {                      
               
            if(data.error == false){         
              let datos = data.datos;   
              document.getElementById('total_kilos_albaranados').innerHTML = datos.total_kilos_albaranados;
              document.getElementById('kilos_sin_facturar').innerHTML = datos.kilos_sin_facturar;
              document.getElementById('total_euros_albaranados').innerHTML = datos.total_euros_albaranados;
              document.getElementById('euros_sin_facturar').innerHTML = datos.euros_sin_facturar;
              document.getElementById('kilos_facturados').innerHTML = datos.kilos_facturados;
              document.getElementById('euros_facturados').innerHTML = datos.euros_facturados;
              document.getElementById('euros_ivafacturado').innerHTML = datos.euros_ivafacturado;
              document.getElementById('euros_pago_pendiente').innerHTML = datos.euros_pago_pendiente;
              document.getElementById('num_facturas_pago_pendiente').innerHTML = datos.num_facturas_pago_pendiente;
              document.getElementById('euros_pagados').innerHTML = datos.euros_pagados;
              document.getElementById('euros_pago_parcial').innerHTML = datos.euros_pago_parcial;
              document.getElementById('euros_vencidos').innerHTML = datos.euros_vencidos;
              //document.getElementById('num_facturas_vencidas').innerHTML = datos.num_facturas_vencidas;
                      
              document.getElementById('total_kilos_albaranados_cli').innerHTML = datos.total_kilos_albaranados_cli;
              document.getElementById('kilos_sin_facturar_cli').innerHTML = datos.kilos_sin_facturar_cli;
              document.getElementById('total_euros_albaranados_cli').innerHTML = datos.total_euros_albaranados_cli;
              document.getElementById('euros_sin_facturar_cli').innerHTML = datos.euros_sin_facturar_cli;
              document.getElementById('kilos_facturados_cli').innerHTML = datos.kilos_facturados_cli;
              document.getElementById('euros_facturados_cli').innerHTML = datos.euros_facturados_cli;
              document.getElementById('euros_ivafacturado_cli').innerHTML = datos.euros_ivafacturado_cli;
              document.getElementById('euros_pago_pendiente_cli').innerHTML = datos.euros_pago_pendiente_cli;
              document.getElementById('num_facturas_pago_pendiente_cli').innerHTML = datos.num_facturas_pago_pendiente_cli;
              document.getElementById('euros_pagados_cli').innerHTML = datos.euros_pagados_cli;
              document.getElementById('euros_pago_parcial_cli').innerHTML = datos.euros_pago_parcial_cli;
              document.getElementById('euros_vencidos_cli').innerHTML = datos.euros_vencidos_cli;
              //document.getElementById('num_facturas_vencidas_cli').innerHTML = datos.num_facturas_vencidas_cli;
            
            }else{
              alert(data.mensaje);
            }


            
          }))
      });       
    }    

    //
    let generar_factura_pdf = document.getElementById('generar_pdf_dashboard');
    if(generar_factura_pdf){
      generar_factura_pdf.addEventListener('click', function () {
              
        let fechaIni = document.getElementById('fechaIni').value;
        let fechaFin = document.getElementById('fechaFin').value;                                   
        window.open(urlCompleta + "/Inicio/exportarPdfDashboard/" + fechaIni + "/"+fechaFin);
        
      });       
    }



  }//fin del if

});
