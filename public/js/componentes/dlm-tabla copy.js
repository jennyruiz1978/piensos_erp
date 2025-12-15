class tabla extends HTMLElement {

    constructor() {
        super();        
        this.id = this.getAttribute("id");        
        this.titulos = this.getAttribute("titulos").split(",");        
        this.crud = this.getAttribute("crud");        
        this.hiddencol = this.getAttribute("hiddencol");
        // con hidden col indicamos a nuestra petición ajax donde hacer la petición de nuestro json de datos
        this.url = this.getAttribute("url");
        // contenido es el elemento que contiene nuestra tabla y sus componentes
        this.contenido = document.createElement("div");
        // iconosCrud contiene los distintos iconos que va a mostrar en los botones de nuestro crud cuando estén activos, es importante
        // recordar que hay que añadirles style="pointer-events: none;" para que no haya problemas al recoger el evento click del boton       
        this.iconosCrud = { c: '<i style="pointer-events: none;" class="fas fa-plus-circle"></i>', r: '<i style="pointer-events: none;" class="far fa-question-circle"></i>', u: '<i style="pointer-events: none;" class="far fa-edit"></i>', d: '<i style="pointer-events: none;" class="far fa-times-circle"></i>', a: '<i style="pointer-events: none;" class="far fa-bell-slash"></i>', o: '<i style="pointer-events: none;" class="far fa-eye"></i>', f: '<i class="far fa-file"></i>' };
        // iconosOrder contiene los iconos que aparecen junto al titulo de nuestras columnas, indicando el modo de orden que se va a aplicar en nuestra busqueda
        this.iconosOrder = { NONE: '<i style="pointer-events: none;" class="fas fa-sort"></i>', ASC: '<i style="pointer-events: none;" class="fas fa-sort-amount-up-alt"></i>', DESC: '<i style="pointer-events: none;" class="fas fa-sort-amount-down-alt"></i>' }; 
        // son los 3 estados de ordenación de nuestras columnas
        this.estadosOrder = ['NONE', 'ASC', 'DESC'];
        // en estadosTitulo almacenamos el estado de cada una de nuestras columnas
        this.estadosTitulo = [];
        // en orden almacenamos nuestra clausula ORDER BY para nuestra select
        this.orden = "";
        // en paginaActiva almacenamos la última pagina activa
        this.paginaActiva = 1;
        // en totalPaginas almacenamos el total de paginas que calculamos al dividir el total de registros y los registros que queremos ver por página
        this.totalPaginas = "";
        // total de registros que queremos mostrar por pagina de nuestra tabla
        this.registrosPagina = 25;
        // almacenamos nuestro elemento de control de registros por página
        this.registrosPag = '';

        this.totalRegistros = 0;

        this.ultimoElementoConFoco = '';

        
        this.tablaGenerada = '';
        this.botonesOrden = [];
        this.searchTitulos = [];
        this.searchGeneral = '';
        
        this.searchTitulosValues = [];
        this.searchGeneralValue = '';        
        this.where = '';
        this.keysDatos = [];

        // esta ruta sirva para enviar el id con el submit en un botón en cada fila (los permisos no permiten enviar datos por get)
        this.urlEditar = this.getAttribute("urlEditar");      
        
        this.fechas = this.getAttribute("fechas");
        this.fechaIni = document.getElementById("fechaIni");
        this.fechaFin = document.getElementById("fechaFin");
        this.fechaIniValue = '';        
        this.fechaFinValue = '';
        this.cliente = this.getAttribute("cliente");

        this.fechaIniSession = document.getElementById("fechaIniDashboardSession");
        this.fechaFinSession = document.getElementById("fechaFinDashboardSession");

        this.tipoListado = this.getAttribute("tipolistado");
        this.botonExcel = this.getAttribute("botonExcel");

        localStorage.clear();
    }

    connectedCallback() {
        this.obtenerDatos();
    }

    renderTabla() {
        console.log('1-render');
        this.innerHTML = '';
        //this.innerHTML += this.buscador();
        this.innerHTML += this.tablaGenerada;
        this.innerHTML += this.paginador();
        this.listenerBotonesOrden();
        this.listenerSearchTitulos();
        this.listenerSearchGeneral();
        if (this.fechas != '') {
            this.listenerSearchButonFechas();
            this.listenerLimpiarFiltros();
        }
        if (this.botonExcel != '') {
            this.listenerExportExcel();
        }
        
        //ocultado para este proyecto
        //this.listenerNumRegistros();
        //ocultado para este proyecto

        //this.listenerCrud();
        this.listenerPaginador();
        this.asignarFoco();                
    }

    listenerBotonesOrden() {
        this.botonesOrden = document.getElementsByClassName(this.id + "_cambioOrden");
        var claseTabla = this;
        Array.from(this.botonesOrden).forEach(function(boton) {
            boton.addEventListener('click', (e) => { claseTabla.cambioOrden(boton) });
        });
    }

    listenerSearchTitulos() {
        
        this.searchTitulos = document.getElementsByClassName(this.id + "_searchTitulos");
        var claseTabla = this;
        Array.from(this.searchTitulos).forEach(function(input) {
            
            /*input.addEventListener('keyup', (e) => {                
                claseTabla.ultimoElementoConFoco = input;           
                claseTabla.cambioValorSearchTitulos(input);
            });*/

            let timeout = null;
            input.addEventListener('keyup', function (e) {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    claseTabla.ultimoElementoConFoco = input;           
                    claseTabla.cambioValorSearchTitulos(input);
                }, 1000);
            });

        });
        
    }

    guardarEnLocalStorage(){
        
        let obj = {};      
        let array = document.getElementsByClassName(this.id + '_searchTitulos');
        for (let index = 0; index < array.length; index++) {            
            let valor = array[index].value;            
            let key = array[index].getAttribute('data-titulo');            
            obj[this.id + '_search'+key] = valor;            
        }

        //ocultado para este proyecto
        //let searchGeneralValor = document.getElementById(this.id + '_searchGeneral').value;        
        //obj[this.id + '_searchGeneral'] = searchGeneralValor;
        //ocultado para este proyecto


        if (this.fechas != '') {
            let fechaIniFiltro = document.getElementById('fechaIni').value;        
            obj['fechaIni'] = fechaIniFiltro;

            let fechaFinFiltro = document.getElementById('fechaFin').value;        
            obj['fechaFin'] = fechaFinFiltro;
        }
        
        localStorage.setItem(this.id, JSON.stringify(obj));
        
    }

    listenerSearchGeneral() {
        console.log('entra listener searchgeneral');
        this.searchGeneral = document.getElementById(this.id + "_searchGeneral");
        console.log('this.searchGeneral', this.searchGeneral);
        var claseTabla = this;
        
        //ocultado para este proyecto
        /*this.searchGeneral.addEventListener('keyup', (e) => {                                           
            claseTabla.ultimoElementoConFoco = claseTabla.searchGeneral;
            claseTabla.cambioValorSearchGeneral(this.searchGeneral)
        });*/
        //ocultado para este proyecto

        //modificación para que busque despues de 1000 ms de dejar de escribir
        let timeout = null;
        

        //ocultado para este proyecto
        /*
        this.searchGeneral.addEventListener('keyup', function (e) {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                claseTabla.ultimoElementoConFoco = claseTabla.searchGeneral;
                claseTabla.cambioValorSearchGeneral(claseTabla.searchGeneral)
            }, 1000);
        });
        */
       //ocultado para este proyecto
    }
    
    listenerSearchButonFechas(){
        let clickBotonFechas = document.getElementById("filtrarPorFecha");
        var claseTabla = this; 
        clickBotonFechas.addEventListener('click', (e) => {
            this.guardarEnLocalStorage();
            claseTabla.mantenerValorInputsFecha(fechaIni,fechaFin);                        
        });
    }      
    
    listenerExportExcel(){
        let clickBotonExcel = document.getElementById("exportarExcel_test");
        let where = this.montarClausulaSearchTitulos();          
        clickBotonExcel.addEventListener('click', (e) => {                                             
            var form = `
                <form id="form_send_excel" action="${this.urlEditar}" method="post">
                    <input type="hidden" name="creq" value="xxx" />
                    <input type="hidden" name="cadenaCriterios" value="${window.btoa(String(where))}" />
                </form>`;
            var form_cont = document.getElementById('container_form_acciones');
            form_cont.innerHTML = form;            
            let formX = document.getElementById('form_send_excel');            
            formX.submit();     
        });
    }  

    
    listenerLimpiarFiltros(){
        let clickBotonLimpiar = document.getElementById("limpiarFiltros");
        clickBotonLimpiar.addEventListener('click', (e) => {
            localStorage.clear();
            document.getElementById('fechaIni').value = '';            
            this.fechaIniValue = '';

            document.getElementById('fechaFin').value = '';            
            this.fechaFinValue = '';

            document.getElementById(this.id + '_searchGeneral').value = '';

            //ocultado para este proyecto
            //document.getElementById(this.id + '_searchGeneral').removeAttribute( "value" );
            //ocultado para este proyecto


            this.searchGeneralValue = '';

            let todosLosInputs = document.getElementsByClassName(this.id + '_searchTitulos');
            
            for (let index = 0; index < todosLosInputs.length; index++) {
                todosLosInputs[index].value = "";
                this.searchTitulosValues = []; //=======> da error en la busqueda de los inputs, porque vacía el array, solo debe limpiar los values
                //todosLosInputs[index].removeAttribute( "value" );
            }            
        });
        
    }
    

    mantenerValorInputsFecha(fechaIni,fechaFin){
        var claseTabla = this;
        claseTabla.recogerAsignarFechas(fechaIni,fechaFin);
        this.obtenerDatos();      
    }       
    

    recogerAsignarFechas(fechaIni,fechaFin){
        this.fechaIniValue = fechaIni.value;
        if (this.fechaIniSession && this.fechaIniSession.value != '') {
            this.fechaIniValue = this.fechaIniSession.value;
        }        
        this.fechaFinValue = fechaFin.value;
        if (this.fechaFinSession && this.fechaFinSession.value != '') {
            this.fechaFinValue = this.fechaFinSession.value;
        }
    }
    

    listenerNumRegistros() {
        this.registrosPag = document.getElementById(this.id + "_registrosPag");
        var claseTabla = this;
        this.registrosPag.addEventListener('change', (e) => {
            claseTabla.ultimoElementoConFoco = claseTabla.registrosPag;
            claseTabla.cambioValorRegistrosPagina(this.registrosPag)
        });
    }

    listenerCrud() {
        let createbtn = document.getElementById(this.id + "_createbtn");
        var claseTabla = this;
        createbtn.addEventListener('click', (e) => { /*alert('va a crear un nuevo elemento')*/ });

        let updatebtn = document.getElementsByClassName(this.id + "_updatebtn");
        Array.from(updatebtn).forEach(function(input) {
            input.addEventListener('click', (e) => {                
                return window.atob(input.getAttribute('data-datos'));
            });
        });

        let readbtn = document.getElementsByClassName(this.id + "_readbtn");
        Array.from(readbtn).forEach(function(input) {
            input.addEventListener('click', (e) => { /*alert('va a leer un elemento')*/ });
        });

        let deletebtn = document.getElementsByClassName(this.id + "_deletebtn");
        Array.from(deletebtn).forEach(function(input) {
            input.addEventListener('click', (e) => { /*alert('va a eliminar un elemento')*/ });
        });
    }

    listenerPaginador() {
        var claseTabla = this;
        let paginadorbtn = document.getElementsByClassName(this.id + "_paginadorbtn");
        
        if (this.fechaFinSession && this.fechaFinSession.value != '') {
            claseTabla.recogerAsignarFechas(fechaIni,fechaFin);
        }

        Array.from(paginadorbtn).forEach(function(input) {
            input.addEventListener('click', (e) => { claseTabla.paginaActual(input.dataset.action) });
        });
    }

    asignarFoco() {
        if (this.ultimoElementoConFoco != '') {
            //cojo la longitud total del string del input
            let longitudValorElementoFocus = document.getElementById(this.ultimoElementoConFoco.id).value.length;
            //asigno el foco al input
            document.getElementById(this.ultimoElementoConFoco.id).focus();
            //pongo el cursos al final del valor del input
            document.getElementById(this.ultimoElementoConFoco.id).setSelectionRange(longitudValorElementoFocus, longitudValorElementoFocus);
        }
    }

    cabecera() {
        let salida = "";
        let columnasOcultas = this.hiddencol;
        let acciones = this.crud;
        for (let i = 0; i < this.titulos.length; i++) {
            if ((this.titulos[i] in this.estadosTitulo) === false) {
                this.estadosTitulo[this.titulos[i]] = 0;
            }
            let ordenActual = this.estadosTitulo[this.titulos[i]];
            let estadoOrden = this.estadosOrder[ordenActual];
            let iconoOrden = this.iconosOrder[estadoOrden];
            let visible = (columnasOcultas.includes(i)) ? ' style="display:none"' : '';
            
            salida += `<th class="p-2 border-r cursor-pointer text-sm font-thin text-gray-500" ${visible}>${this.titulos[i]}
            <button type="button"  class="${this.id}_cambioOrden order_button rounded-full bg-gray-0 text-gray-600 hover:text-gray-700 hover:bg-gray-200 h-full w-10 cursor-pointer outline-none" data-orden="${ordenActual}" data-titulo="${this.titulos[i]}">${iconoOrden}</button></th>`;
        }
        if (acciones != '' && acciones != 'c') {
            if (acciones.includes('c')) {
                salida += `<th class="p-2 border-r cursor-pointer text-sm font-thin text-gray-500">Accions</th>`;
            } else {
                salida += `<th rowspan="2" class="p-2 border-r cursor-pointer text-sm font-thin text-gray-500">Accions</th>`;
            }
        }
        return salida;
    }

    inputs() {        
        let salida = "";
        let columnasOcultas = this.hiddencol;
        let acciones = this.crud;
        let datos = JSON.parse(localStorage.getItem(this.id));        

        for (let i = 0; i < this.titulos.length; i++) {
            
            let visible = (columnasOcultas.includes(i)) ? ' style="display:none"' : '';                        
            
            
            this.searchTitulosValues[this.titulos[i]] = '';
            if ( (this.titulos[i] in this.searchTitulosValues) === false) {

                this.searchTitulosValues[this.titulos[i]] = '';
                let al = `${this.id}_search${this.titulos[i]}`;  
                if(datos != null) {                    
                    
                    if (datos[al] != '' && datos[al] != null && datos[al] != undefined) {
                        this.searchTitulosValues[this.titulos[i]] = datos[al];
                    }
                }                                         
            }else{                
                let al = `${this.id}_search${this.titulos[i]}`;  
                if(datos != null) {                    
                    if (datos[al] != '' && datos[al] != null && datos[al] != undefined) {                        
                        this.searchTitulosValues[this.titulos[i]] = datos[al];    
                    }
                }/*else{
                    this.searchTitulosValues[this.titulos[i]] = this.searchTitulosValues[this.titulos[i]];
                }*/
            }            
            salida += `<th class="p-2 border-r" ${visible}>
                <input type="text" id="${this.id}_search${this.titulos[i]}" class="input_search_title p-1 ${this.id}_searchTitulos" data-titulo="${this.titulos[i]}" value="${this.searchTitulosValues[this.titulos[i]]}">
            </th>`;
        }

        if (acciones != '' && acciones != 'c') {
            if (acciones.includes('c')) {
                salida += `<th class="p-2 border-r">
                    <a data-crud="c" id="${this.id}_createbtn" href="#" class="createbtn text-green-500 p-2 text-lg hover:text-green-400">${this.iconosCrud['c']}</a>
                </th>`;
            }
        }

        return salida;
    }

    buscador() {        
        let botonNuevo = '';

        //ocultado para este proyecto
        let buscadorGeneral = this.buscadorGeneral();
        //ocultado para este proyecto

        //let buscadorGeneral = '';
        if (this.crud == 'c') {
            botonNuevo += `
            <div class="grid grid-cols-1 md:grid-cols-2">
                ${buscadorGeneral}
                <div class="p-2 pt-4 text-right">
                    <a data-crud="c" id="${this.id}_createbtn" href="#" class="createbtn rounded bg-green-500 text-white p-2 text-xs hover:bg-green-400 font-semibold">${this.iconosCrud['c']}<span class="pl-2">Nuevo Registro<span></a>
                </div>                
                </div>`;
        } else {
            //botonNuevo += buscadorGeneral;
        }
        return `        
        <thead id="${this.id}_principal">
            <tr class="bg-gray-50 border-b text-center">
            ${this.cabecera()}
            </tr>
            <tr class="bg-gray-50 border-b text-center">
            ${this.inputs()}
            </tr>
        </thead>`;
    }

    buscadorGeneral() {
        let buscadorG = '';
        let buscadorFechas = '';
        
        let datos = JSON.parse(localStorage.getItem(this.id));
        let valorGuardado = '';
        if (datos != null) {
            let al = `${this.id}_searchGeneral`;
            valorGuardado = datos[al];
        }
        
        if (valorGuardado != '' || valorGuardado != null || valorGuardado != undefined) {
            this.searchGeneralValue = valorGuardado;
        }

        let valorFechaIniValue = this.fechaIniMes();
        if (datos != null && datos.fechaIni != '' && datos.fechaIni != null && datos.fechaIni != undefined) { //viene del localstorage
            console.log('entra al if c');
            valorFechaIniValue = datos.fechaIni;
        }
        /*
        let valorFechaIniValue = this.fechaIniValue;
        if (this.fechaIniSession && this.fechaIniSession.value != '') { //viene del dashboard
            console.log('entra al if a');
            valorFechaIniValue = this.fechaIniSession.value;
        }else if (datos != null && datos['fechaIni'] != '' && datos['fechaIni'] != null && datos['fechaIni'] != undefined) { //viene del localstorage
            console.log('entra al else b');
            valorFechaIniValue = datos['fechaIni'];
        }
        */
        let valorFechaFinValue = this.fechaFinMes();
        if (datos != null && datos.fechaFin != '' && datos.fechaFin != null && datos.fechaFin != undefined) { //viene del localstorage
            valorFechaFinValue = datos.fechaFin;
        }
        /*
        let valorFechaFinValue = this.fechaFinValue;
        if (this.fechaFinSession && this.fechaFinSession.value != '') {
            valorFechaFinValue = this.fechaFinSession.value;
        }else if (datos != null && datos['fechaFin'] != '' && datos['fechaFin'] != null && datos['fechaFin'] != undefined) { //viene del localstorage
            valorFechaFinValue = datos['fechaFin'];
        }
        */

        let excelButton = (this.botonExcel != '')? '<div class="grids_search_fechas mt-2 md:mt-6"><a id="exportarExcel_test" class="exportarExcel_test text-white px-4 py-1" target="_blank">Exportar <i class="fas fa-file-excel ml-2"></i></a></div>': '';
        
        if (this.fechas != '' && this.botonExcel != '') {
            buscadorFechas += `<div class="container_search_fechas p-2">
                            
            <div class="grids_search_fechas">
                <label class="uppercase md:text-sm text-xs text-gray-500 text-light font-semibold">Desde</label>
                <input type="date" id="fechaIni" class="p-1 rounded-button border-2 border-gray-200 mt-1 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent" type="text" value="${valorFechaIniValue}">
            </div>

            <div class="grids_search_fechas">
            <label class="uppercase md:text-sm text-xs text-gray-500 text-light font-semibold">Hasta</label>
                <input type="date" id="fechaFin" class="p-1 rounded-button border-2 border-gray-200 mt-1 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent" type="text" value="${valorFechaFinValue}">
            </div>

            <div class="grids_search_fechas mt-2 md:mt-6">
                <button class='w-auto bg-green-700 hover:bg-green-900 rounded-button shadow-xl text-xs xl:text-sm 2xl:text-base text-white px-4 py-1' id="filtrarPorFecha">Buscar </button> 
            </div>
            <div class="grids_search_fechas mt-2 md:mt-6">
                <button class='w-auto bg-gray-500 hover:bg-gray-700 rounded-button shadow-xl text-xs xl:text-sm 2xl:text-base text-white px-4 py-1' id="limpiarFiltros">Limpiar </button> 
            </div>
            ${excelButton}
            <div style="display:none;" id="container_form_acciones">            
            </div>

            </div>`;  

      
        }else{
            buscadorFechas += `<div class="container_search_fechas p-2">${excelButton}
            <div style="display:none;" id="container_form_acciones">            
            </div></div>`;
        }
        buscadorG += buscadorFechas;

        /*
        buscadorG += `        
        <div class="box p-2">
            <div class="box-wrapper">
                <div class="buscador_general p-2">
                  <span class="outline-none focus:outline-none"><svg class="search_icon w-5 text-gray-600 h-5 cursor-pointer" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></span>
                  <input type="search" name="searchGeneral" id="${this.id}_searchGeneral" placeholder="cercador general..." x-model="q" class="${this.id}_searchGeneral input_search_main w-full pl-4 text-sm outline-none focus:outline-none bg-transparent" value="${this.searchGeneralValue}">
                  <div class="select">
                    <!--<select name="registrosPag" id="${this.id}_registrosPag" x-model="image_type" class="select_registros">
                      <option value="10" ${(this.registrosPagina==10)?'selected':''}>10 Reg</option>
                      <option value="20" ${(this.registrosPagina==20)?'selected':''}>20 Reg</option>
                      <option value="30" ${(this.registrosPagina==30)?'selected':''}>30 Reg</option>
                      <option value="40" ${(this.registrosPagina==40)?'selected':''}>40 Reg</option>
                      <option value="50" ${(this.registrosPagina==50)?'selected':''}>50 Reg</option>
                     </select>-->
                  </div>
                </div>
              
            </div>
        </div>`;
        */
        
        /*
        if (document.getElementById('fechaIniDashboardSession') && document.getElementById('fechaIniDashboardSession')) {
            this.vaciarVariablesSessionFechas();    
        }
        */
        
        return buscadorG;
    }

    fechaIniMes(){
       
        // Obtén la fecha actual
        const fechaActual = new Date();

        // Establece la fecha en el primer día del mes actual
        fechaActual.setDate(1);

        // Obtiene el año, mes y día
        const año = fechaActual.getFullYear();
        const mes = fechaActual.getMonth() + 1; // Los meses comienzan desde 0 (enero) hasta 11 (diciembre), por lo que sumamos 1
        const dia = 1; // El día es siempre 1, ya que estamos obteniendo el primer día del mes

        // Formatea la fecha en Y-m-d
        const primerDiaDelMesEnFormatoYMD = `${año}-${mes.toString().padStart(2, '0')}-${dia.toString().padStart(2, '0')}`;        

        return primerDiaDelMesEnFormatoYMD;
        
    }

    fechaFinMes(){
        // Obtén la fecha actual
        const fechaActual = new Date();

        // Establece la fecha en el último día del mes actual
        fechaActual.setMonth(fechaActual.getMonth() + 1, 0);

        // Obtiene el año, mes y día
        const año = fechaActual.getFullYear();
        const mes = fechaActual.getMonth() + 1; // Los meses comienzan desde 0 (enero) hasta 11 (diciembre), por lo que sumamos 1
        const dia = fechaActual.getDate(); // Obtenemos el día del último día del mes

        // Formatea la fecha en Y-m-d
        const ultimoDiaDelMesEnFormatoYMD = `${año}-${mes.toString().padStart(2, '0')}-${dia.toString().padStart(2, '0')}`;

        console.log(`El último día del mes actual en formato Y-m-d es: ${ultimoDiaDelMesEnFormatoYMD}`);
        return ultimoDiaDelMesEnFormatoYMD;

    }

    paginador() {
        let paginador = '';
        this.totalPaginas = Math.ceil((this.totalRegistros / this.registrosPagina))

        paginador += `<div class="footer_paginator text-center my-4">
        <label for="custom-input-number" class="w-35 text-gray-700 text-sm font-semibold md:mt-3">Pàgina ${this.paginaActiva} de ${this.totalPaginas}</label>
        <div class="flex flex-row h-8 w-35 rounded-lg relative bg-transparent mt-1 justify-center">`;
        if (this.paginaActiva > 1) {
            paginador += `<button id="${this.id}_first" data-action="first" class="${this.id}_paginadorbtn bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 rounded-l cursor-pointer outline-none">
            <span class="m-auto text-lg font-thin"><i style="pointer-events: none;" class="fas fa-angle-double-left"></i></span>
          </button>
          <button data-action="decrement" class="${this.id}_paginadorbtn bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 cursor-pointer outline-none">
            <span class="m-auto text-lg font-thin"><i style="pointer-events: none;" class="fas fa-angle-left"></i></span>
          </button>`;
        }
        paginador += `<input type="number" class="input_current_page outline-none focus:outline-none text-center w-20  bg-gray-200 font-semibold text-md hover:text-black focus:text-black  md:text-basecursor-default flex items-center text-gray-700  outline-none" name="custom-input-number" value="${this.paginaActiva}" readonly></input>`;
        if (this.paginaActiva < this.totalPaginas) {
            paginador += `<button data-action="increment" class="${this.id}_paginadorbtn bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10  cursor-pointer">
          <span class="m-auto text-lg font-thin"><i style="pointer-events: none;" class="fas fa-angle-right"></i></span>
        </button>
        <button id="${this.id}_last" data-action="last" class="${this.id}_paginadorbtn bg-gray-200 text-gray-600 hover:text-gray-700 hover:bg-gray-400 h-full w-10 rounded-r cursor-pointer">
          <span class="m-auto text-lg font-thin"><i style="pointer-events: none;" class="fas fa-angle-double-right"></i></span>
        </button>`;
        }
        paginador += `</div>
      <label for="custom-input-number" class="w-35 text-gray-700 text-sm font-semibold md:mt-3">Total Registres ${this.totalRegistros}</label>
      </div>`;

        return paginador;
    }

    paginaActual(action) {
        if (action === 'increment' && this.paginaActiva < this.totalPaginas) {
            this.paginaActiva++;
        } else if (action === 'decrement' && this.paginaActiva > 1) {
            this.paginaActiva--;
        } else if (action === 'first') {
            this.paginaActiva = 1;
        } else if (action === 'last') {
            this.paginaActiva = this.totalPaginas;
        }
        this.obtenerDatos();
    }

    montarTabla(resultado) {        
        let tabla = '';

        //ocultado para este proyecto
        let buscadorGeneral = this.buscadorGeneral();
        //ocultado para este proyecto


        //let buscadorGeneral = '';
        tabla = `
                ${buscadorGeneral}
                <div class="mx-2 pt-1  overflow-x-auto">
                    <div class="cont_table_level2 inline-block min-w-full shadow rounded-lg overflow-hidden">
                        <div class="p-2">
                            <table class="table_component_content" id="${this.id}">
                            
                            ${this.buscador()}
                            <tbody>
                            ${this.montarFilasTabla(resultado)}
                            </tbody>
                            </table>
                        </div>                        
                    </div>
                </div>`;

        return tabla;
    }

    montarFilasTabla(resultado) {
        let botones = this.crud;
        let columnasOcultas = this.hiddencol;
        let iconosCrud = this.iconosCrud;
        let acciones = this.crud;
        let filas = "";
        for (let i = 0; i < resultado.length; i++) {
            let datos = resultado[i];
            let col = 0;
            let columnas = '';
            let colAcciones = '';
            var numColumnas = Object.keys(datos).length;
            if (acciones != '' && acciones != 'c') {
                numColumnas++;
                colAcciones += `<td class="p-2 border-r"><div class="block_buttons_actions">`;
                if (acciones.includes('r')) {
                    colAcciones += `<a href="#" data-crud="r" data-datos="${window.btoa(JSON.stringify(datos))}" id="${this.id}_readbtn${i}" class="${this.id}_readbtn text-blue-600 px-2 text-lg hover:text-blue-300 hover:text-xl">${iconosCrud['r']}</a>`;
                }
                if (acciones.includes('u')) {
                    colAcciones += `<a href="#" data-crud="u" data-idupd="${resultado[i].id}" id="${this.id}_updatebtn${i}" class="${this.id}_updatebtn text-yellow-600 px-2 text-lg hover:text-yellow-300 hover:text-xl">${iconosCrud['u']}</a>`;
                }
                if (acciones.includes('d')) {
                    colAcciones += `<a href="#" data-crud="d" data-datos="${window.btoa(JSON.stringify(datos))}" id="${this.id}_deletebtn${i}" class="${this.id}_deletebtn text-red-600 px-2 text-lg hover:text-red-300">${iconosCrud['d']}</a>`;
                }
                if (acciones.includes('e')) {
                    colAcciones += `<div>
                                        <form action="${this.urlEditar}" method="POST" title="editar">
                                            <input type="number" class="hidden" name="idEdit" value="${resultado[i].id}">
                                            <button type="submit" id="${this.id}_editarbtn${i}" class="${this.id}_editarbtn text-yellow-600 px-2 text-lg hover:text-yellow-300 hover:text-xl">${iconosCrud['u']}</button>
                                        </form>
                                    </div>`;
                }
                if (acciones.includes('v')) {
                    colAcciones += `<div>
                                        <form action="${this.urlEditar}" method="POST" title="editar" id="formularioBotonEditar_${resultado[i].id}">
                                            <input type="number" class="hidden" name="idEdit" value="${resultado[i].id}">
                                            <button type="submit" id="${this.id}_editarbtn${i}" class="${this.id}_editarbtn text-yellow-600 px-2 text-lg hover:text-yellow-300 hover:text-xl" data-edit="${resultado[i].id}">${iconosCrud['u']}</button>
                                        </form>
                                    </div>`;
                }
                if (acciones.includes('b')) {
                    colAcciones += `<a href="#" data-crud="b" data-iddel="${resultado[i].id}" id="${this.id}_borrarbtn${i}" class="${this.id}_borrarbtn button_delete_tabla text-red-600 px-2 text-lg hover:text-red-300">${iconosCrud['d']}</a>`;
                }
                if (acciones.includes('a')) {
                    colAcciones += `<a href="#" title="postergar alerta" data-crud="a" data-idppto="${resultado[i].id}" id="${this.id}_quitaralerta${i}" class="${this.id}_quitaralerta text-red-600 px-2 text-lg hover:text-red-300">${iconosCrud['a']}</a>`;
                }

                if (acciones.includes('o')) {
                    colAcciones += `<a data-crud="o" data-idppto="${resultado[i].id}" id="${this.id}_verobsinterna${i}" class="${this.id}_verobsinterna text-red-600 px-2 text-lg hover:text-red-300">${iconosCrud['o']}</a>`;
                }
                
                if (acciones.includes('f')) {
                    let mostrar = 'hidden'
                    if(resultado[i].documentos > 0){
                        mostrar = 'visible'
                    }                    
                    colAcciones += `<a data-crud="f" class="${this.id}_readbtn text-blue-600 px-2 text-lg hover:text-blue-300 hover:text-xl" style="visibility:${mostrar}">${iconosCrud['f']}</a>`;
                }                

                colAcciones += `</div></td>`;
            }
            for (const key in datos) {
                let visible = (columnasOcultas.includes(col)) ? ' style="display:none"' : '';
                columnas += `
                            <td class="p-2 border-r text-xs lg:text-sm 2xl:text-base" ${visible}>${datos[key]}</td>`;
                col++;
            }

            filas += `<tr class="rows bg-white border-b text-center" id="fila_${resultado[i].id}">
                            ${ columnas }
                        ${ colAcciones } </tr>`;
        }

        return filas;
    }

    
    obtenerDatos() {        
        let dataURL = this.url;
        this.orden = this.montarClausulaOrden();
        this.where = this.montarClausulaSearchTitulos();               
        //this.where = this.montarClausulaSearchGeneral(this.where);        
        if ( document.getElementById("fechaIni") && document.getElementById("fechaFin") ) {       
            
            this.where = this.montarClausulaSearchFecha(this.where);   
        }            
        let tablaClass = this;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", dataURL, true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(
            `numPagina=${tablaClass.paginaActiva}&numRegistrosPagina=${tablaClass.registrosPagina}&where=${window.btoa(String(tablaClass.where))}&orden=${tablaClass.orden}&idCliente=${this.cliente}`
        );
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let respuesta = this.responseText;
                let resultado = JSON.parse(respuesta);
                
                if (resultado.totalRegistros > 0) {
                    tablaClass.keysDatos = Object.keys(resultado.registros[0]);    
                }else{
                    //tablaClass.keysDatos = [];
                }
                
                tablaClass.totalRegistros = resultado.totalRegistros;
                tablaClass.tablaGenerada = tablaClass.montarTabla(resultado.registros);
                tablaClass.renderTabla();
            }
        };
    }
    

    
    vaciarVariablesSessionFechas() {
        let dataURL = document.getElementById('ruta').value;
        let dataMetodo = `${dataURL}/Presupuestos/limpiarVariablesDeSesionParaFechasDashboard`;     
        
        let xhr = new XMLHttpRequest();
        xhr.open("POST", dataMetodo, true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(
            `null`
        );
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
             document.getElementById('fechaIniDashboardSession').value = '';
             document.getElementById('fechaFinDashboardSession').value = '';
            }
        };
    }

    cambioOrden(boton) {
        let titulo = boton.dataset.titulo;
        let orden = boton.dataset.orden;
        boton.dataset.orden = orden + 1;
        let nuevoOrden = this.estadosOrder.length - (this.estadosOrder.length - (boton.dataset.orden % this.estadosOrder.length));
        let estado = this.estadosOrder[nuevoOrden];
        boton.innerHTML = this.iconosOrder[estado];
        this.estadosTitulo[titulo] = nuevoOrden;
        this.obtenerDatos();
    }

    cambioValorSearchTitulos(input) {
        let titulo = input.dataset.titulo;
        this.searchTitulosValues[titulo] = input.value;        
        this.guardarEnLocalStorage();
        this.obtenerDatos();
    }

    cambioValorSearchGeneral(input) {
        console.log('entra cambio');
        console.log('cambio input', input);
        this.searchGeneralValue = input.value;
        this.guardarEnLocalStorage();
        this.obtenerDatos();
    }

    cambioValorRegistrosPagina(select) {
        this.registrosPagina = select.value;
        this.obtenerDatos();
    }

    montarClausulaOrden() {
        let orden = '';
        let cnt = 0;
        for (const titulo in this.estadosTitulo) {
            let tituloDatos = this.keysDatos[cnt];
            if (this.estadosTitulo[titulo] != 0) {
                if (orden == '') {
                    orden = ' ORDER BY ';
                } else {
                    orden += ' , ';
                }
                let estado = this.estadosTitulo[titulo];
                orden += `${tituloDatos} ${this.estadosOrder[estado]}`;
            }
            cnt++;
        }
        return orden;
    }

    montarClausulaSearchTitulos() {
        let where = '';
        let cnt = 0;
        
        for (const titulo in this.searchTitulosValues) {
            let tituloDatos = this.keysDatos[cnt];
            
            if (this.searchTitulosValues[titulo] !== '') {
            
                if (where == '') {
                    where = ' WHERE ';
                } else {
                    where += ' AND ';
                }
                let value = this.searchTitulosValues[titulo];
                
                where += ` ${tituloDatos} like '%${value}%' `;
                
            }
            cnt++;
        }
        //console.log('hi',window.btoa(String(where)));
        //return window.btoa(String(where));
        return where;
    }
    

    montarClausulaSearchGeneral(where) {        
        let cadenatitulos = '';
        for (let index = 0; index < this.titulos.length; index++) {
            let tituloDatos = this.keysDatos[index];
            if (cadenatitulos === '') {
                cadenatitulos = ' lower(concat( ';
            } else {
                cadenatitulos += `,`;
            }
            const titulo = tituloDatos;
            cadenatitulos += `' ',${titulo}`;
        }        
        if (cadenatitulos !== '') {
            cadenatitulos += ' )) ';            

            if (this.searchGeneralValue !== '') {
                if (where == '') {
                    where = ' WHERE ';
                } else {
                    where += ' AND ';
                }
                let value = this.searchGeneralValue;
                where += ` ${cadenatitulos} like '%${value.toLowerCase()}%' `;
            }
        }        
        return where;
    }


    
    montarClausulaSearchFecha(where) {        
        
        let fechaIni = document.getElementById("fechaIni").value;
        let fechaFin = document.getElementById("fechaFin").value;

        if (fechaIni !== '' && fechaFin !=='') {        
            if (where == '') {                
                where = ' WHERE ';
            } else {                
                where += ' AND ';
            }               
            
            
            if (typeof this.tipoListado !== 'undefined' && this.tipoListado == 'clientes') { 
                where += ` ${this.fechas} <= '${fechaFin}' `;                 
            } else{
                where += ` ${this.fechas} BETWEEN '${fechaIni}' AND  '${fechaFin}' `;       
            }
                   
        }        
        return where;
    }


}

window.customElements.define("dlm-tabla", tabla);