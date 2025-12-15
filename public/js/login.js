
$(document).ready(function() {    

    //para ocultar o mostrar contraseña
    const passToggle = document.querySelector('.js-pass-toggles')

    if (passToggle) {
            
        passToggle.addEventListener('change', function() {
            
            const pass = document.querySelector('.js-pass'),
            passLabel = document.querySelector('.js-pass-label')

            if (pass.type === 'password') {
                pass.type = 'text'
                passLabel.innerHTML = "<i class='far fa-eye-slash login_view'></i>";
            } else {
                pass.type = 'password'
                passLabel.innerHTML = "<i class='far fa-eye login_view'></i>";
            }
            //svg-inline--fa fa-eye
            pass.focus()
        })
    }

    /*const forgotPass = document.querySelector('.link_forgot_pass');
    if(forgotPass){
        forgotPass.addEventListener('click', function(){
            alert('Estem construint aquesta funcionalitat');
        });
    }*/

    let form_forgot_pass = document.getElementById('form_forgot_pass');
    if(form_forgot_pass){
      form_forgot_pass.addEventListener('submit', function(e) {        
          e.preventDefault();
          let url = document.getElementById('rutaIni').value;
          let ruta = url+'/Login/resetearContrasenia';
          console.log('url=>', url);
          console.log('ruta=>', ruta);

          let data = `email=${form_forgot_pass.mail.value}`;
          if(data != ''){
            solicitarCambioDeContrasenia(data, ruta);
          }            
      });
    }

    function solicitarCambioDeContrasenia(data, ruta){

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
                msgShow(texto);                               
            }
        };      
  
      }
          
      function msgShow(mensaje){      
        let mensajeLogin = document.getElementById("msgErrores");
        mensajeLogin.innerHTML = mensaje;
        msgClean(mensajeLogin);
      }
      function msgClean(mensajeLogin){
        setTimeout(function(){        
          mensajeLogin.innerHTML='';
        },5000)
      }

    
});
