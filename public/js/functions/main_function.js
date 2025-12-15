/*
function callMainBackEnd(data, ruta){ //sin utilizar

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
  */

