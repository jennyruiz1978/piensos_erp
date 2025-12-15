<div class="contactos-component">
    <label class="form-label">Contactes i correus</label>
    <div class="input-group mb-2">
        <input type="text" class="form-control nombre-contacto-input" placeholder="Nom del contacte">
        <input type="email" class="form-control email-input" placeholder="Correu">
        <button type="button" class="btn btn-primary btn-agregar-contacto">+</button>
    </div>
    <div class="invalid-feedback email-error"></div>
    <div class="lista-contactos mt-2">
        <!-- Aquí se mostrarán los contactos con sus emails -->
    </div>
    <input type="hidden" class="contactos-emails" name="contactos_emails" value="">
</div>

<style>
    .contacto-item {
        background-color: #f8f9fa;
        padding: 0.5rem;
        border-radius: 4px;
        margin-bottom: 0.5rem;
    }

    .contacto-item button {
        padding: 0 0.5rem;
        margin-left: 0.5rem;
    }

    .input-group input:first-child {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group input:nth-child(2) {
        border-radius: 0;
    }

    .input-group button {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }


    .input-group input.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .contacto-item .input-group {
        margin-bottom: 0;
    }

    .contacto-item .form-control {
        height: calc(1.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .contacto-item .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .invalid-feedback.show {
        display: block;
    }
</style>

<script>
class ContactosManager {

    constructor(container, contactosIniciales = []) {
        // Ahora recibimos el elemento contenedor directamente
        this.container = container;
        this.nombreInput = this.container.querySelector('.nombre-contacto-input');
        this.emailInput = this.container.querySelector('.email-input');
        this.btnAgregar = this.container.querySelector('.btn-agregar-contacto');
        this.listaContactos = this.container.querySelector('.lista-contactos');
        this.contactosHidden = this.container.querySelector('.contactos-emails');
        this.emailError = this.container.querySelector('.email-error');
        this.contactos = contactosIniciales;

        this.inicializarEventos();
        this.actualizarListaContactos();
        this.actualizarInputHidden();

         // Modificar el método para generar HTML
         this.componentId = container.id;
    }


    inicializarEventos() {
        this.btnAgregar.addEventListener('click', () => this.agregarContacto());
        
        // Validar email mientras se escribe
        this.emailInput.addEventListener('input', () => {
            this.validarEmail();
        });

        // Permitir agregar con Enter en el campo de email
        this.emailInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.agregarContacto();
            }
        });
    }

    validarEmail() {
        const email = this.emailInput.value.trim();
        
        if (email === '') {
            this.mostrarError('');
            return false;
        }
        
        if (!this.isValidEmail(email)) {
            this.mostrarError('El format del correu no és vàlid');
            return false;
        }
        
        if (this.contactos.some(c => c.email === email)) {
            this.mostrarError('Aquest correu ja existeix a la llista');
            return false;
        }

        this.mostrarError('');
        return true;
    }

    mostrarError(mensaje) {
        if (mensaje) {
            this.emailInput.classList.add('is-invalid');
            this.emailError.textContent = mensaje;
            this.emailError.classList.add('show');
        } else {
            this.emailInput.classList.remove('is-invalid');
            this.emailError.textContent = '';
            this.emailError.classList.remove('show');
        }
    }

    agregarContacto() {
        const nombre = this.nombreInput.value.trim();
        const email = this.emailInput.value.trim();
        
        if (!this.validarEmail()) {
            return;
        }

        // Obtener los contactos existentes del input hidden
        let contactosExistentes = [];
        try {
            contactosExistentes = JSON.parse(this.contactosHidden.value || '[]');
        } catch(e) {
            console.error('Error parsing existing contacts:', e);
        }

        // Combinar los contactos existentes con el nuevo
        this.contactos = [
            ...contactosExistentes,
            {
                nombre: nombre || 'Sense nom',
                email: email
            }
        ];
        
        // Crear y agregar el nuevo elemento visual
        const nuevoContactoDiv = document.createElement('div');
        nuevoContactoDiv.className = 'contacto-item';

        /* nuevoContactoDiv.innerHTML = `
            <div class="input-group">
                <input type="text" 
                    class="form-control nombre-contacto" 
                    value="${nombre || 'Sense nom'}"
                    onchange="contactosManager.actualizarContacto(${this.contactos.length - 1}, this.value, 'nombre')">
                <input type="email" 
                    class="form-control email-contacto" 
                    value="${email}"
                    onchange="contactosManager.actualizarContacto(${this.contactos.length - 1}, this.value, 'email')">
                <button type="button" 
                    class="btn btn-danger" 
                    onclick="contactosManager.eliminarContacto(${this.contactos.length - 1})">×</button>
            </div>
        `; */

        nuevoContactoDiv.innerHTML = `
            <div class="input-group">
                <input type="text" 
                    class="form-control nombre-contacto" 
                    value="${nombre || 'Sense nom'}"
                    onchange="contactosManagers['${this.componentId}'].actualizarContacto(${this.contactos.length - 1}, this.value, 'nombre')">
                <input type="email" 
                    class="form-control email-contacto" 
                    value="${email}"
                    onchange="contactosManagers['${this.componentId}'].actualizarContacto(${this.contactos.length - 1}, this.value, 'email')">
                <button type="button" 
                    class="btn btn-danger" 
                    onclick="contactosManagers['${this.componentId}'].eliminarContacto(${this.contactos.length - 1})">×</button>
            </div>
        `;
        
        // Agregar el nuevo contacto a la lista visual
        this.listaContactos.appendChild(nuevoContactoDiv);
        
        // Limpiar los campos de entrada
        this.nombreInput.value = '';
        this.emailInput.value = '';
        
        // Actualizar el input hidden con todos los contactos
        this.actualizarInputHidden();
        this.mostrarError('');
    }    
  

    eliminarContacto(index) {
        // Eliminar el contacto del array
        this.contactos.splice(index, 1);
        
        /* // Actualizar toda la lista después de eliminar
        this.listaContactos.innerHTML = this.contactos.map((contacto, idx) => `
            <div class="contacto-item">
                <div class="input-group">
                    <input type="text" 
                        class="form-control nombre-contacto" 
                        value="${contacto.nombre}"
                        onchange="contactosManager.actualizarContacto(${idx}, this.value, 'nombre')">
                    <input type="email" 
                        class="form-control email-contacto" 
                        value="${contacto.email}"
                        onchange="contactosManager.actualizarContacto(${idx}, this.value, 'email')">
                    <button type="button" 
                        class="btn btn-danger" 
                        onclick="contactosManager.eliminarContacto(${idx})">×</button>
                </div>
            </div>
        `).join(''); */

        this.listaContactos.innerHTML = this.contactos.map((contacto, idx) => `
            <div class="contacto-item">
                <div class="input-group">
                    <input type="text" 
                        class="form-control nombre-contacto" 
                        value="${contacto.nombre}"
                        onchange="contactosManagers['${this.componentId}'].actualizarContacto(${idx}, this.value, 'nombre')">
                    <input type="email" 
                        class="form-control email-contacto" 
                        value="${contacto.email}"
                        onchange="contactosManagers['${this.componentId}'].actualizarContacto(${idx}, this.value, 'email')">
                    <button type="button" 
                        class="btn btn-danger" 
                        onclick="contactosManagers['${this.componentId}'].eliminarContacto(${idx})">×</button>
                </div>
            </div>
        `).join('');
        
        this.actualizarInputHidden();
    }
  

    actualizarListaContactos() {
        // Si es una actualización completa (setContactos o eliminar)
        if (this.contactos.length === 0) {
            this.listaContactos.innerHTML = '';
            return;
        }

        // Para agregar un nuevo contacto
        const ultimoContacto = this.contactos[this.contactos.length - 1];
        const contactosExistentes = this.listaContactos.querySelectorAll('.contacto-item');
        
        // Si el último contacto ya está en la vista, no hacer nada
        if (contactosExistentes.length >= this.contactos.length) {
            return;
        }

        // Crear elemento para el nuevo contacto
        const nuevoContactoDiv = document.createElement('div');
        nuevoContactoDiv.className = 'contacto-item';
        nuevoContactoDiv.innerHTML = `
            <div class="input-group">
                <input type="text" 
                    class="form-control nombre-contacto" 
                    value="${ultimoContacto.nombre}"
                    onchange="contactosManager.actualizarContacto(${this.contactos.length - 1}, this.value, 'nombre')">
                <input type="email" 
                    class="form-control email-contacto" 
                    value="${ultimoContacto.email}"
                    onchange="contactosManager.actualizarContacto(${this.contactos.length - 1}, this.value, 'email')">
                <button type="button" 
                    class="btn btn-danger" 
                    onclick="contactosManager.eliminarContacto(${this.contactos.length - 1})">×</button>
            </div>
        `;

        // Agregar el nuevo contacto a la lista
        this.listaContactos.appendChild(nuevoContactoDiv);
    }



    actualizarInputHidden() {
        // Asegurarse de que this.contactos contenga todos los contactos
        const contactosVisuales = Array.from(this.listaContactos.querySelectorAll('.contacto-item')).map((item, index) => {
            return {
                nombre: item.querySelector('.nombre-contacto').value,
                email: item.querySelector('.email-contacto').value
            };
        });

        // Actualizar this.contactos con todos los contactos visuales
        this.contactos = contactosVisuales;
        
        // Actualizar el input hidden
        this.contactosHidden.value = JSON.stringify(this.contactos);
    }    

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    getContactos() {
        return this.contactos;
    }

    /* setContactos(contactos) {
        this.contactos = contactos;
        // Actualizar toda la lista con los contactos iniciales
        this.listaContactos.innerHTML = this.contactos.map((contacto, index) => `
            <div class="contacto-item">
                <div class="input-group">
                    <input type="text" 
                        class="form-control nombre-contacto" 
                        value="${contacto.nombre}"
                        onchange="contactosManager.actualizarContacto(${index}, this.value, 'nombre')">
                    <input type="email" 
                        class="form-control email-contacto" 
                        value="${contacto.email}"
                        onchange="contactosManager.actualizarContacto(${index}, this.value, 'email')">
                    <button type="button" 
                        class="btn btn-danger" 
                        onclick="contactosManager.eliminarContacto(${index})">×</button>
                </div>
            </div>
        `).join('');
        
        this.actualizarInputHidden();
    } */

    setContactos(contactos) {
        this.contactos = contactos;
        // Actualizar toda la lista con los contactos iniciales
        this.listaContactos.innerHTML = this.contactos.map((contacto, index) => `
            <div class="contacto-item">
                <div class="input-group">
                    <input type="text" 
                        class="form-control nombre-contacto" 
                        value="${contacto.nombre}"
                        onchange="contactosManagers['${this.componentId}'].actualizarContacto(${index}, this.value, 'nombre')">
                    <input type="email" 
                        class="form-control email-contacto" 
                        value="${contacto.email}"
                        onchange="contactosManagers['${this.componentId}'].actualizarContacto(${index}, this.value, 'email')">
                    <button type="button" 
                        class="btn btn-danger" 
                        onclick="contactosManagers['${this.componentId}'].eliminarContacto(${index})">×</button>
                </div>
            </div>
        `).join('');
        
        this.actualizarInputHidden();
    }

    actualizarContacto(index, valor, campo) {
        
        if (campo === 'email') {
            if (!this.validarEmailEdicion(valor, index)) {
                // Si la validación falla, restaurar el valor anterior
                this.actualizarListaContactos();
                return;
            }
        }
        
        this.contactos[index][campo] = valor;
        this.actualizarInputHidden();
    }

    validarEmailEdicion(email, indiceActual) {
        if (email === '') {
            return false;
        }
        
        if (!this.isValidEmail(email)) {
            return false;
        }
        
        // Verificar duplicados, ignorando el índice actual
        const existeDuplicado = this.contactos.some((c, index) => 
            index !== indiceActual && c.email === email
        );
        
        return !existeDuplicado;
    }
}

// Inicializar todas las instancias del componente
document.addEventListener('DOMContentLoaded', function() {

    ///////////////////////////
    // Crear un objeto para almacenar todas las instancias
    window.contactosManagers = {};
    ////////

    document.querySelectorAll('.contactos-component').forEach((container, index) => {

        // Asignar un ID único a cada instancia
        const componentId = `contactos-component-${index}`;
        container.id = componentId;

         // Modificar los atributos onclick y onchange para usar el ID específico
         container.querySelectorAll('[onclick*="contactosManager."]').forEach(el => {
            el.setAttribute('onclick', el.getAttribute('onclick').replace(
                'contactosManager.',
                `contactosManagers['${componentId}'].`
            ));
        });
        
        container.querySelectorAll('[onchange*="contactosManager."]').forEach(el => {
            el.setAttribute('onchange', el.getAttribute('onchange').replace(
                'contactosManager.',
                `contactosManagers['${componentId}'].`
            ));
        });
        
        // Crear y almacenar la instancia
        window.contactosManagers[componentId] = new ContactosManager(container);

        /* // Asignar un ID único a cada instancia si es necesario
        if (!container.id) {
            container.id = 'contactos-component-' + index;
        }
        // Crear una nueva instancia del manager para cada componente
        new ContactosManager(container); */
    });
});
// No inicializamos aquí el ContactosManager
</script>