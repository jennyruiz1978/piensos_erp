
class botonCambiarContrasenia extends HTMLElement {

    constructor() {
        super();
        this.nombre = this.getAttribute("nombre");
        this.rutabase = this.getAttribute("root");
        this.rutametodo = this.getAttribute("metodo");
        this.rutalogin = this.getAttribute("rutalogin");
        this.imagenlogo = this.getAttribute("imglogo");
        this.slogan = this.getAttribute("slogan");        
        this.idusuario = this.getAttribute("idusuario");
        this.validez = this.getAttribute("validez");
        

    }

    accion(rutametodo,rutalogin) {
       
        const cambiar = document.getElementById("cambiarPassword");
        
        cambiar.addEventListener("click", (e) => {
            e.preventDefault();

            let id = document.getElementById('user').value;

            let password = document.getElementById('password').value;
            password = window.btoa(String(password));

            let repite = document.getElementById('repite').value;
            repite = window.btoa(String(repite));

            if (id != "" && password != "" && repite != "") {
                if (password == repite) {

                    let ruta = rutametodo;           
                   
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        if (this.readyState == 4 && this.status == 200) {
                                                    
                            let update = this.responseText;                                  

                            if (update == 1) {   
                                 
                                document.getElementById('password').value = '';
                                document.getElementById('repite').value = '';
                                document.getElementById("mensajeValidacion").innerHTML = `Se ha actualizado la contraseña. Hacer click en Login.`;
                                
                                document.getElementById("contenedorLoginSinLogo").innerHTML = '';
                                var node = document.createElement("p");                                                        
                                node.setAttribute("class", "font-bold text-red-600");
                                var textnode = document.createTextNode("Se ha actualizado la contraseña.");
                                node.appendChild(textnode);
                                document.getElementById("contenedorLoginSinLogo").appendChild(node);

                                var enlace = document.createElement("a");
                                enlace.setAttribute("class", "font-bold text-gray-600");
                                var ruta = document.getElementById("ruta").value;
                                enlace.setAttribute("href", ruta+"/Login");

                                var textnode = document.createTextNode("Ir al login");
                                enlace.appendChild(textnode);
                                document.getElementById("contenedorLoginSinLogo").appendChild(enlace);

                            }else{
                                                                       
                                
                                document.getElementById("mensajeValidacion").innerHTML = `No se ha podido actualizar la contraseña. Verique los datos ingresados.`;
                            }   

                        }
                    };
                    xhr.open("POST", ruta, true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.send(
                        `id=${id}&password=${password}&repite=${repite}`                        
                    );

                }else{
                    
                    document.getElementById("mensajeValidacion").innerHTML = "Las contraseñas no son iguales.";
                }
            }else{                                
                document.getElementById("mensajeValidacion").innerHTML = "No has ingresado una contraseña.";

            }

        })
    }

    
    login(rutalogin) {
       
        const logarse = document.getElementById("login");                
        
        if (logarse) {

            logarse.addEventListener("click", function () {                
                console.log(rutalogin);
                window.open(rutalogin);
                
            });
          
        }

    }        

    verPassword(){

        const passwordToggle = document.querySelector('.js-password-toggle')

        passwordToggle.addEventListener('change', function() {
            
            const password = document.querySelector('.js-password'),
            passwordLabel = document.querySelector('.js-password-label')
    
            if (password.type === 'password') {
                password.type = 'text'
                passwordLabel.innerHTML = "<i class='far fa-eye-slash text-red-800 text-base xl:text-xl js-password-label'></i>";
            } else {
                password.type = 'password'
                passwordLabel.innerHTML = "<i class='far fa-eye text-red-800 text-base xl:text-xl js-password-label'></i>";
            }
    
            password.focus()
        })

        const repiteToggle = document.querySelector('.js-repite-toggle')

        repiteToggle.addEventListener('change', function() {
            
            const repite = document.querySelector('.js-repite'),
            repiteLabel = document.querySelector('.js-repite-label')
    
            if (repite.type === 'password') {
                repite.type = 'text'
                repiteLabel.innerHTML = "<i class='far fa-eye-slash text-red-800 text-base xl:text-xl js-repite-label'></i>";
            } else {
                repite.type = 'password'
                repiteLabel.innerHTML = "<i class='far fa-eye text-red-800 text-base xl:text-xl js-repite-label'></i>";
            }
    
            repite.focus()
        })
        

    }

    connectedCallback() {

        let titulo = "Cambiar contraseña";
        let enlaceLogin = '';
        if (this.validez > 24) {
            titulo = "Este enlace ha caducado";
            enlaceLogin = `<a class="text-sm font-semibold text-gray-500" href="${this.rutabase}/Login">Volver al Login</a>`;
        }
        
        

        let parte1 = `<div class="flex items-center min-h-screen p-4 bg-gray-100 lg:justify-center">
                            <div class="flex flex-col overflow-hidden bg-white rounded-md shadow-lg max md:flex-row md:flex-1 lg:max-w-screen-md">
                                <div class="p-4 py-6 text-white bg-gray-50 md:w-80 md:flex-shrink-0 md:flex md:flex-col md:items-center md:justify-evenly">
                                    <div class="my-3 text-4xl font-bold tracking-wider text-center">
                                        <img src="${ this.imagenlogo}">
                                    </div>
                                    <p class="mt-6 font-normal text-center text-gray-600 md:mt-0">
                                        ${this.slogan}
                                    </p>
                                </div>
                                <input type="hidden" id="ruta" value="${this.rutabase}">
                                <div class="p-5 bg-white md:flex-1" id="contenedorLoginSinLogo">
                                    <h3 class="my-2 text-base 2xl:text-2xl font-semibold text-gray-700">${titulo}</h3>${enlaceLogin}`;

        if (this.validez <= 24) {
           
            parte1 += `
                                    <form class="flex flex-col space-y-5" id="formCambioPassword" method="POST" action="<?php echo RUTA_URL; ?>/Login/ejecutarCambioContrasenia">            
                                                                
                                        <input name="user" id="user" type="hidden" value= "${this.idusuario}" />

                                        <div class="flex flex-col space-y-1">
                                            <div class="flex items-center justify-between">
                                                <label for="password" class="text-sm font-semibold text-gray-500">Ingresa una nueva Contrase&ntilde;a</label>          
                                            </div> 

                                            <div class="relative w-full">

                                                <div class="absolute inset-y-0 right-0 flex items-center px-2">
                                                <input class="hidden js-password-toggle" id="toggle" type="checkbox" />
                                                <label class="bg-gray-300 hover:bg-gray-400 rounded px-2 py-1 text-sm text-gray-600 font-mono cursor-pointer js-password-label" for="toggle"><i class='far fa-eye text-red-800 text-base xl:text-xl'></i></label>
                            
                                                </div>
                                            
                                                <input 
                                                        type="password"
                                                        id="password"
                                                        name="pass"
                                                        autofocus
                                                        class="appearance-none w-full js-password inputLogin px-4 py-2 transition duration-300 border border-gray-300 rounded focus:border-transparent focus:outline-none focus:ring-2 focus:ring-red-700" autocomplete="off" />
                                            
                                            </div>

                                        </div>

                                        <div class="flex flex-col space-y-1">
                                            <div class="flex items-center justify-between">
                                                <label for="repite" class="text-sm font-semibold text-gray-500">Repite la contrase&ntilde;a para confirmar</label>              
                                            </div>                                          
                                                <div class="relative w-full">

                                                    <div class="absolute inset-y-0 right-0 flex items-center px-2">
                                                        <input class="hidden js-repite-toggle" id="togglerepite" type="checkbox" />
                                                        <label class="bg-gray-300 hover:bg-gray-400 rounded px-2 py-1 text-sm text-gray-600 font-mono cursor-pointer js-repite-label" for="togglerepite"><i class='far fa-eye text-red-800 text-base xl:text-xl'></i></label>
                                    
                                                    </div>
                                                
                                                    <input 
                                                            type="password"
                                                            id="repite"
                                                            name="repite"   
                                                            class="appearance-none w-full js-repite inputLogin px-4 py-2 transition duration-300 border border-gray-300 rounded focus:border-transparent focus:outline-none focus:ring-2 focus:ring-red-700" autocomplete="off" />
                                                
                                                </div>                                                    
                                        </div>
                                    
                                        <span id="mensajeValidacion" class="font-bold font-bold text-red-600"></span> 
                                        <div id="contenedorBoton">                     
                                            <button id="cambiarPassword" class="w-full px-4 py-2 text-lg font-semibold text-white transition-colors duration-300 bg-red-700 hover:bg-red-500 rounded-md shadow focus:outline-none focus:ring-blue-200 focus:ring-4">${this.nombre}</button>                     
                                        </div>                    
                                    </form>`;
        }
            parte1 += `
                                </div>
                            </div>
                        </div>`;
        
        this.innerHTML = parte1;
                                
        this.accion(this.rutametodo,this.rutalogin);
        this.login(this.rutalogin);
        this.verPassword();
        
    }    

}

window.customElements.define("dlm-cambiocontrasenia", botonCambiarContrasenia);