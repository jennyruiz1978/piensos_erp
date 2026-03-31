import DB from './fecth.js';
import RowsGrid from './rows_grilla.js';

document.addEventListener("DOMContentLoaded", () => {    
    
    var urlCompleta = $('#ruta').val(); 

    if (window.location.pathname.includes("/FacturasFicherosCSV")) {                
        
        // --- INICIALIZACIÓN DE TAIL.SELECT ---
        // 1. Configuración Múltiple para Clientes
        tail.select("#idclientesearch", {
            search: true,              // Habilita la búsqueda
            multiple: true,            // Habilita selección múltiple
            multiLimit: 10,            // Opcional: límite de selecciones
            multiShowCount: false,     // Muestra "X seleccionados" en lugar de todas las etiquetas
            multiContainer: true,      // Agrupa las selecciones
            placeholder: "Seleccionar clientes...",
            descriptions: true,
            animate: true
        });

        // Inicializamos el estado de factura (sin búsqueda si son pocos items)
        tail.select("#estado_factura", {
            search: false,
            multiple: false,
            placeholder: "Estado..."
        });

        // --- BUSQUEDA DE FACTURAS ---
        let formulario_filtros_buscar_facturas = document.getElementById('formulario_filtros_buscar_facturas');
        if(formulario_filtros_buscar_facturas){
            let buscarfacturaCli = document.getElementById('buscarfacturaCli');
            if(buscarfacturaCli){
                buscarfacturaCli.addEventListener('click', function () {
                    let ruta = urlCompleta + '/FacturasFicherosCSV/obtenerFacturasConFiltros'; 
                    let datosForm = new FormData(formulario_filtros_buscar_facturas);          
                    
                     // 🔴 IMPORTANTE: eliminamos el valor incorrecto
                    datosForm.delete('idclientesearch[]');

                    // ✅ Obtenemos los valores reales desde tail.select
                    let clientesSeleccionados = tail.select("#idclientesearch").value();

                    clientesSeleccionados.forEach(id => {
                        datosForm.append('idclientesearch[]', id);
                    });

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

    // --- EXPORTAR CSV Y XML (eventos separados) ---
        // --- Botones de exportación ---
    const btnCSV = document.getElementById('exportarCSVBtn');
    const btnXML = document.getElementById('exportarXMLBtn');
    const formularioExportar = document.getElementById('formulario_crear_factura_masiva');

    if (!formularioExportar) {
        console.error('No se encontró el formulario');
    } else {
        if (btnCSV) {
            btnCSV.addEventListener('click', function() {
                console.log('Click en CSV');
                exportar('csv');
            });
        }
        if (btnXML) {
            btnXML.addEventListener('click', function() {
                console.log('Click en XML');
                exportar('xml');
            });
        }
    }

    function exportar(formato) {
        let ruta = urlCompleta + '/FacturasFicherosCSV/exportar' + (formato === 'csv' ? 'CSV' : 'XML');
        console.log('Ruta:', ruta);

        // Obtener los IDs de las facturas seleccionadas en la tabla derecha
        const filas = document.querySelectorAll("#tablaGrillaFactura tbody .fila_alb_inv");
        const ids = Array.from(filas).map(f => f.getAttribute('data-idfactura'));
        
        if (ids.length === 0) {
            Swal.fire('Atenció', 'No hi ha factures seleccionades', 'warning');
            return;
        }

        // Construir FormData
        const datosForm = new FormData();
        ids.forEach(id => datosForm.append('idfacturaSelected[]', id));

        Swal.fire({
            title: 'Confirmació',
            text: 'Esteu segur(a) de marcar les factures seleccionades com exportades?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, exportar',
            cancelButtonText: 'Cancel·lar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            const loader = document.getElementById('loader_factura');
            if (loader) loader.style.display = 'block';

            const buttons = formularioExportar.querySelectorAll('button');
            buttons.forEach(btn => btn.disabled = true);
            const anclas = formularioExportar.querySelectorAll('a');
            anclas.forEach(an => an.disabled = true);

            fetch(ruta, {
                method: 'POST',
                body: datosForm
            })
            .then(response => response.json())
            .then(respuesta => {
                if (loader) loader.style.display = 'none';
                buttons.forEach(btn => btn.disabled = false);
                anclas.forEach(an => an.disabled = false);

                if (respuesta.error === false) {
                    // 📌 CSV (un solo archivo)
                    if (formato === 'csv') {
                        const blob = new Blob([respuesta.contenido], { type: respuesta.tipo_mime });
                        const link = document.createElement('a');
                        const url = URL.createObjectURL(blob);
                        link.href = url;
                        link.download = respuesta.filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                    } 
                    // 📌 XML (múltiples archivos)
                    else if (formato === 'xml' && respuesta.multifile) {
                        respuesta.archivos.forEach((archivo, index) => {
                            // Pequeño retraso para evitar que el navegador bloquee múltiples descargas
                            setTimeout(() => {
                                const blob = new Blob([archivo.contenido], { type: 'application/xml' });
                                const link = document.createElement('a');
                                const url = URL.createObjectURL(blob);
                                link.href = url;
                                link.download = archivo.filename;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                                URL.revokeObjectURL(url);
                            }, index * 200);
                        });
                    }

                    Swal.fire('Procés correcte', respuesta.mensaje, 'success').then(() => {
                        document.querySelector("#tablaGrillaFactura tbody").innerHTML = '';
                        const btnBuscar = document.getElementById('buscarfacturaCli');
                        if (btnBuscar) btnBuscar.click();
                    });
                } 
                else if (respuesta.tipo === 'VALIDACION_DATOS') {
                    let htmlErrores = '<div style="text-align: left;">';
                    respuesta.detalles.forEach(err => {
                        let txt = `<b>Factura ${err.numero}:</b>`;
                        if (err.campos && err.campos.length) txt += ` falten ${err.campos.join(', ')}.`;
                        if (err.errorDetalle) txt += `<br><i>${err.errorDetalle}</i>`;
                        htmlErrores += `<div style="margin-bottom: 8px;">${txt}</div>`;
                    });
                    htmlErrores += '</div>';
                    Swal.fire('Dades incompletes', htmlErrores, 'error');
                } 
                else {
                    Swal.fire('Error', respuesta.mensaje, 'error');
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                if (loader) loader.style.display = 'none';
                buttons.forEach(btn => btn.disabled = false);
                anclas.forEach(an => an.disabled = false);
                Swal.fire('Error', 'Error de comunicació amb el servidor', 'error');
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

    // --- EVENTO AFEGIR TOTES ---
let btnAccionFacturas = document.getElementById('btnAccionFacturas');
if (btnAccionFacturas) {
    btnAccionFacturas.addEventListener('click', function() {
        // 1. Obtener todos los botones de "Afegir" que hay en la tabla de búsqueda
        let botonesBusqueda = document.querySelectorAll('#tablaGrillaFacturaPrincipal .agregar_alb_fact');
        let arrExistentes = arraysFacturasSeleccionadas(); // IDs ya en la derecha
        let idsParaAgregar = [];
        let hayExportadas = false;

        botonesBusqueda.forEach(btn => {
            let id = btn.dataset.idfactura;
            // Solo añadir si no está ya en la tabla de la derecha
            if (!arrExistentes.includes(id)) {
                idsParaAgregar.push(id);
                // Detectar si alguna de las nuevas ya estaba exportada (botón verde)
                if (btn.classList.contains('button_small_add_row_green')) {
                    hayExportadas = true;
                }
            }
        });

        if (idsParaAgregar.length === 0) {
            Swal.fire({ title: 'Info', text: 'No hay facturas nuevas para añadir.', icon: 'info' });
            return;
        }

        const procesarAgregadoMasivo = () => {
            let ruta = urlCompleta + '/FacturasFicherosCSV/obtenerFacturasFilasMasivo';
            let params = { 'ids': idsParaAgregar };

            new DB(ruta, 'POST').get(params).then((data => {
                if (!data.error) {
                    // Añadir el bloque de HTML a la tabla derecha
                    $("#tablaGrillaFactura tbody").append(data.html_facturas);
                    
                    // Eliminar las filas de la tabla izquierda
                    idsParaAgregar.forEach(id => {
                        let filaBusqueda = document.getElementById('fila_alb_' + id);
                        if (filaBusqueda) filaBusqueda.remove();
                    });
                } else {
                    Swal.fire({ title: 'Error', text: data.mensaje, icon: 'error' });
                }
            }));
        };

        // 2. Si hay facturas ya exportadas, pedir confirmación una sola vez para todas
        if (hayExportadas) {
                    Swal.fire({
                        title: 'Confirmació masiva',
                        text: 'Algunes de les factures seleccionades ja han estat exportades. Voleu afegir-les totes igualment?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, afegir totes',
                        cancelButtonText: 'Cancel·lar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            procesarAgregadoMasivo();
                        }
                    });
                } else {
                    procesarAgregadoMasivo();
                }
            });
        }

        const btnLimpiar = document.getElementById('btnLimpiarfacturas');
        const tablaExportarBody = document.querySelector('#tablaGrillaFactura tbody');

        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function(e) {
                e.preventDefault(); // Evitamos cualquier acción por defecto

                // 1. Pedimos confirmación al usuario (Opcional pero recomendado)
                if (confirm('Estàs segur que vols buidar tota la llista de factures a exportar?')) {
                    
                    // 2. Limpiamos el contenido visual de la tabla derecha
                    tablaExportarBody.innerHTML = '';
                }
            });
        }



});