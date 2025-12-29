import DB from './fecth.js';
import RowsGrid from './rows_grilla.js';

document.addEventListener("DOMContentLoaded", () => {    
    
    var urlCompleta = $('#ruta').val(); 

    if (window.location.pathname.includes("/FacturasFicherosCSV")) {                
        
        // --- BUSQUEDA DE FACTURAS ---
        let formulario_filtros_buscar_facturas = document.getElementById('formulario_filtros_buscar_facturas');
        if(formulario_filtros_buscar_facturas){
            let buscarfacturaCli = document.getElementById('buscarfacturaCli');
            if(buscarfacturaCli){
                buscarfacturaCli.addEventListener('click', function () {
                    let ruta = urlCompleta + '/FacturasFicherosCSV/obtenerFacturasConFiltros'; 
                    let datosForm = new FormData(formulario_filtros_buscar_facturas);                  
                    new DB(ruta, 'POST').post(datosForm).then((data => {                
                        if(data.error == true){
                            Swal.fire({ title: 'Error', text: data.mensaje, icon: 'error' });  
                        } else {
                            let tableId = document.getElementById('tablaGrillaFacturaPrincipal');                        
                            let tBody = tableId.getElementsByTagName('tbody')[0];            
                            tBody.innerHTML = data.html_albaranes;            
                        }
                    }));
                });       
            }
        }

        // --- DELEGACIÓN DE EVENTOS PARA CLICS EN TABLAS ---
        document.addEventListener('click', function(e) {
            const clickedElement = e.target;

            // 1. EVENTO AGREGAR (Botón de la izquierda)
            if (clickedElement.matches('.agregar_alb_fact')) {        
                let idFactura = clickedElement.dataset.idfactura; 
                let arrExistentes = arraysFacturasSeleccionadas();
                
                // Verificamos si ya está en la tabla de la derecha
                if (arrExistentes.includes(idFactura)) {
                    Swal.fire({ title: 'Atenció', text: 'Aquesta factura ja està seleccionada.', icon: 'warning' });
                    return;
                }

                // Función interna para ejecutar la lógica de agregar la fila
                const ejecutarAgregado = () => {
                    let ruta = urlCompleta + '/FacturasFicherosCSV/obtenerFacturaFila';          
                    let params = {'id': idFactura};
                    
                    new DB(ruta, 'POST').get(params).then((data => {
                        if(!data.error) {
                            const filaBusqueda = document.getElementById('fila_alb_' + idFactura);
                            if(filaBusqueda) filaBusqueda.remove();
                            $("#tablaGrillaFactura tbody").append(data.html_factura);
                        } else {
                            Swal.fire({ title: 'Error', text: data.mensaje, icon: 'error' });
                        }
                    }));
                };

                // VALIDACIÓN: Si el botón es el verde (Repetir), pedimos confirmación
                if (clickedElement.classList.contains('button_small_add_row_green')) {
                    Swal.fire({
                        title: 'Confirmació',
                        text: 'Confirma que voleu exportar novament una factura que ja va ser exportada?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            ejecutarAgregado();
                        }
                    });
                } else {
                    // Si es el botón normal (azul/pendiente), agregamos directamente
                    ejecutarAgregado();
                }
            }

            // 2. EVENTO ELIMINAR (Botón de la derecha)
            if (clickedElement.closest('.eliminar_alb_fact')) {        
                e.preventDefault(); 
                const eliminarBtn = clickedElement.closest('.eliminar_alb_fact');
                let idFactura = eliminarBtn.dataset.idfactura;          
                const filaDelete = document.getElementById('fila_alb_inv_' + idFactura); 
                if(filaDelete) {
                    filaDelete.remove();
                }
            }
        });
    }

    // --- EVENTO PARA EXPORTAR CSV ---
    let formulario_exportar_csv = document.getElementById('formulario_crear_factura_masiva');
    if(formulario_exportar_csv){
        formulario_exportar_csv.addEventListener('submit', function(e) {        
            e.preventDefault();
            Swal.fire({
                title: 'Confirmació',
                text: 'Esteu segur(a) de marcar les factures seleccionades com exportades?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, exportar',
                cancelButtonText: 'Cancel·lar'
            }).then((result) => {
                if(result.isConfirmed){
                    
                    let loader = document.getElementById('loader_factura');
                    if(loader) loader.style.display = 'block';

                    // Deshabilitar botones
                    let buttons = formulario_exportar_csv.querySelectorAll('button');
                    buttons.forEach(btn => btn.disabled = true);
                    let anclas = formulario_exportar_csv.querySelectorAll('a');
                    anclas.forEach(an => an.disabled = true);

                    let ruta = urlCompleta + '/FacturasFicherosCSV/exportarCSV';
                    let datosForm = new FormData(formulario_exportar_csv);                  
                    let fetch = new DB(ruta, 'POST').post(datosForm);
            
                    fetch.then((respuesta => {        
                        if(loader) loader.style.display = 'none';
                        buttons.forEach(btn => btn.disabled = false);
                        anclas.forEach(an => an.disabled = false);

                        if(respuesta.error == false){
                            // --- LÓGICA DE DESCARGA ---
                            if(respuesta.csvData) {
                                const blob = new Blob([respuesta.csvData], { type: 'text/csv;charset=utf-8;' });
                                const link = document.createElement("a");
                                const url = URL.createObjectURL(blob);
                                
                                link.setAttribute("href", url);
                                link.setAttribute("download", respuesta.filename || 'export.csv');
                                link.style.visibility = 'hidden';
                                
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            }

                            Swal.fire({
                                title: 'Procés correcte',
                                text: respuesta.mensaje,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                const tablaSeleccionados = document.querySelector("#tablaGrillaFactura tbody");
                                if (tablaSeleccionados) {
                                    tablaSeleccionados.innerHTML = "";
                                }

                                const btnBuscar = document.getElementById('buscarfacturaCli');
                                if (btnBuscar) {
                                    btnBuscar.click(); 
                                }
                            });
                        } else {
                            Swal.fire({ title: 'Error', text: respuesta.mensaje, icon: 'error' });
                        }              
                    }))
                }
            });
        });
    }

    function arraysFacturasSeleccionadas() {
        var selected = document.querySelectorAll(".fila_alb_inv");
        var arr = [];
        selected.forEach((item) => {          
            arr.push(item.dataset.idfactura);
        });
        return arr;
    }
});