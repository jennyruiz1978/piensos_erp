class tarjetaOlvidoContrasenia extends HTMLElement {

    constructor() {
        super();
        this.nombre = this.getAttribute("nombre");        
        this.rutabase = this.getAttribute("root");
        this.rutametodo = this.getAttribute("metodo");
        this.imagenlogo = this.getAttribute("imglogo");
        this.slogan = this.getAttribute("slogan");

    }

    accion(rutabase, metodo) {
        const recupera = document.getElementById("recuperarPassword");
        
        recupera.addEventListener("click", (e) => {
            e.preventDefault();
            let email = document.getElementById('email').value;

            if (email !='') {
                                
                let ruta = rutabase + metodo;                

                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                                                
                        let valida = this.responseText;

                        if (valida == 1) {
                            document.getElementById('email').value = '';
                            document.getElementById("contenedorReestablecer").innerHTML = '';                            
                            var node = document.createElement("span");                                                        
                            node.setAttribute("class", "font-bold font-bold text-reed-600");
                            var textnode = document.createTextNode("Te hemos enviado un email para reestablecer la contraseña.");
                            node.appendChild(textnode);
                            document.getElementById("contenedorReestablecer").appendChild(node);
                            
                        }else{
                            document.getElementById("mensajeValidacion").innerHTML = `La cuenta de correo ingresada no es válida!`;                                                  
                        }   

                    }
                };
                xhr.open("POST", ruta, true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send(
                    `email=${email}`
                );
                                     
            }else{                                
                document.getElementById("mensajeValidacion").innerHTML = "No has ingresado una  cuenta de correo.!";

            }

        })
    }

    connectedCallback() {
        this.innerHTML = `<div class="flex items-center min-h-screen p-4 bg-gray-100 lg:justify-center">
                            <div class="flex flex-col overflow-hidden bg-white rounded-md shadow-lg max md:flex-row md:flex-1 lg:max-w-screen-md">
                                <div
                                    class="p-4 py-6 text-white bg-gray-50 md:w-80 md:flex-shrink-0 md:flex md:flex-col md:items-center md:justify-evenly">
                                    <div class="my-3 text-4xl font-bold tracking-wider text-center">
                                        <img src="${this.imagenlogo}">
                                    </div>
                                    <p class="mt-6 font-normal text-center text-gray-600 md:mt-0">
                                        ${this.slogan}
                                    </p>
                                </div>
                                <div class="p-5 bg-white md:flex-1" id="contenedorReestablecer">
                                    <h3 class="my-4 text-2xl font-semibold text-gray-700">Reestablecer contraseña</h3>
                                    <form class="flex flex-col space-y-5">            
                                        <div class="flex flex-col space-y-1">
                                            <label for="email" class="text-sm font-semibold text-gray-500">Ingresa su correo electrónico para reestablecer contraseña</label>
                                            <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            autofocus
                                            class="inputLogin px-4 py-2 transition duration-300 border border-gray-300 rounded focus:border-transparent focus:outline-none focus:ring-2 focus:ring-red-300"/>

                                        </div>  
                                        <span id="mensajeValidacion" class="font-bold font-bold text-reed-600"></span>          
                                        <div>
                                            <button id="recuperarPassword" class="w-full px-4 py-2 text-lg font-semibold text-white transition-colors duration-300 bg-red-700 hover:bg-red-500 rounded-md shadow focus:outline-none focus:ring-blue-200 focus:ring-4">${this.nombre}</button>
                                        </div>
                                        <a class="text-sm font-semibold text-gray-500" href="${this.rutabase}/Login">Volver al Login</a>
                                    </form>
                                </div>
                            </div>
                        </div>`;

        this.accion(this.rutabase, this.rutametodo);        
        
    }    

}

window.customElements.define("dlm-olvidocontrasenia", tarjetaOlvidoContrasenia);