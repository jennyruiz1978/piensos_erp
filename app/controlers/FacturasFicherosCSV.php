<?php

class FacturasFicherosCSV extends Controlador {

    private $baseImponible;    
    private $ivaTotal;
    private $total;
    private $precio;
    private $cantidad;
    private $fetch;
    private $descuentoTipo;
    private $descuentoAcumulado;    


    public function __construct() {
        session_start();

        $this->modeloFacturasClientesCSV = $this->modelo('ModeloFacturasClientesCSV');
        $this->modeloCliente = $this->modelo('ModeloCliente');

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        } 

    }

    public function index() {
        $clientes = $this->modeloCliente->getEnabledClients();
        $datos = [            
            'clientes' => $clientes,
        ];
        $this->vista('facturasClienteCSV/facturaCSV', $datos);
    }


   public function obtenerFacturasConFiltros()
    {
      
        $respuesta = [
            'error' => true,
            'mensaje' => ERROR_DOESNT_EXIST
        ];

        if (
            !empty($_POST['fechainicio']) &&
            !empty($_POST['fechafin']) &&
            !empty($_POST['idclientesearch'])
        ) {


            $fechaInicio = $_POST['fechainicio'];
            $fechaFin = $_POST['fechafin'];
            $clientesArray = $_POST['idclientesearch']; // Esto es un array [1, 5, 12...]

            // 2. Limpiamos y preparamos los IDs para la consulta SQL
            $idsClientes = array_map('intval', $clientesArray);
            $stringIds = implode(',', $idsClientes);

            // 3. Construimos la query usando IN en lugar de =
            $query_search = " fac.idcliente IN ($stringIds) ";
            $query_search .= " AND fac.fecha BETWEEN '$fechaInicio' AND '$fechaFin' ";

            if ($_POST['estado_factura'] !== 'todos') {
                $estado = $_POST['estado_factura'];
                $query_search .= " AND fac.estado_exportar = '$estado' ";
            }

            
            
            $resultado = $this->modeloFacturasClientesCSV
                ->getFacturasConFiltros($query_search);

             
           

            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['html_albaranes'] =
                TemplateHelper::buildGridInvoicesSearchModified($resultado);
        } else {
            $respuesta['mensaje'] = 'Heu de seleccionar almenys un client';
        }

        echo json_encode($respuesta);
    }

    public function obtenerFacturasFilasMasivo() {
        // 1. Validamos que lleguen IDs
        if (!isset($this->fetch["ids"]) || !is_array($this->fetch["ids"]) || empty($this->fetch["ids"])) {
            echo json_encode(['error' => true, 'mensaje' => 'No s’han seleccionat factures']);
            return;
        }

        $idsEnviados = $this->fetch["ids"]; 
        $strIds = implode(",", $idsEnviados);
        
        // 2. Obtenemos las facturas (vienen desordenadas de la DB)
        $facturasDB = $this->modeloFacturasClientesCSV->getFacturasPorIdString($strIds);

        if ($facturasDB) {
            // 3. Re-ordenamos los objetos según el orden de $idsEnviados
            // Creamos un mapa indexado por ID para acceder rápido
            $mapaFacturas = [];
            foreach ($facturasDB as $f) {
                $mapaFacturas[$f->id] = $f;
            }

            $htmlTotal = '';
            // 4. Recorremos el array original de IDs para construir el HTML en ese orden
            foreach ($idsEnviados as $id) {
                if (isset($mapaFacturas[$id])) {
                    $htmlTotal .= TemplateHelper::getFilaFacturaSeleccionada($mapaFacturas[$id]);
                }
            }

            echo json_encode([
                'error' => false,
                'html_facturas' => $htmlTotal
            ]);
        } else {
            echo json_encode(['error' => true, 'mensaje' => 'No s’han trobat les factures']);
        }
    }


    public function obtenerFacturaFila() {
        if ( !isset($this->fetch["id"]) || empty($this->fetch["id"]) ) {
            echo json_encode(['error' => true, 'mensaje' => 'Falten dades']);
            return;
        }
            $id = $this->fetch["id"];
            $factura = $this->modeloFacturasClientesCSV->obtenerFacturaPorId($id);

            if ($factura) {
                $html = TemplateHelper::getFilaFacturaSeleccionada($factura);
                echo json_encode([
                    'error' => false, 
                    'html_factura' => $html 
                ]);
            } else {
                echo json_encode(['error' => true, 'mensaje' => 'Factura no trobada']);
            }
    }

    private function getDiccionarioCamposObligatorios() {
        return [
            'numero'               => 'Número de Factura',
            'fecha'                => 'Data de factura',
            'cifpiensos'           => 'CIF de lEmisor',
            'razonsocialpiensos'   => 'Raó Social Emissor',
            'direccionpiensos'     => 'Direcció Emissor',
            'codigopostalpiensos'  => 'CP Emissor',
            'localidadpiensos'     => 'Localitat Emissor',
            'provinciapiensos'     => 'Provincia Emissor',
            'cliente_nif'          => 'Identificador Receptor',
            'cliente_nombre'       => 'Nom Receptor',
            'cliente_direccion'    => 'Direcció Receptor',
            'cliente_cp'           => 'CP Receptor',
            'cliente_poblacion'    => 'Població Receptor',
            'cliente_provincia'    => 'Provincia Receptor',
            'total'                => 'Import Total',
            'vencimiento'          => 'Data Venciment'
        ];
    }

    private function prepararFilaFactura($f)
    {
        return [
            "CAB",                          // A: TIPO REGISTRO
            $f->numero,                     // B: NÚMERO DE FACTURA
            $f->fecha,                      // C: FECHA DE FACTURA
            $f->cifpiensos,                 // D: IDENTIFICADOR EMISOR
            $f->razonsocialpiensos,         // E: NOMBRE EMISOR
            $f->direccionpiensos,           // F: DIRECCIÓN EMISOR
            $f->codigopostalpiensos,        // G: CP EMISOR
            $f->localidadpiensos,           // H: CIUDAD EMISOR
            $f->provinciapiensos,           // I: PROVINCIA EMISOR
            "ES",                           // J: PAÍS EMISOR
            $f->cliente_nif,                // K: IDENTIFICADOR RECEPTOR
            $f->cliente_nombre,             // L: NOMBRE RECEPTOR
            $f->cliente_direccion,          // M: DIRECCIÓN RECEPTOR
            $f->cliente_cp,                 // N: CP RECEPTOR
            $f->cliente_poblacion,          // O: POBLACIÓN RECEPTOR
            $f->cliente_provincia,          // P: PROVINCIA RECEPTOR
            "EUR",                          // Q: MONEDA
            number_format($f->total, 2, ',', ''), // R: IMPORTE TOTAL
            $f->vencimiento,                // S: FECHA VENCIMIENTO
            $f->codigob2brouter,                // T: MÉTODO DE PAGO
            $f->numerocuenta,               // U: IBAN o BIC
            "",                             // V: ANTICIPOS
            $f->proveedor_nif,              // W: CÓDIGO UNIDAD PROVEEDORA
            "",                             // X: INICIO PERIODO
            "",                             // Y: FIN PERIODO
            "",                             // Z: E-MAIL COMPRADOR
            "",                             // AA: E-MAIL VENDEDOR
            "ES",                           // AB: PAÍS DEL CLIENTE
            "",                             // AC: CENTRO DE COSTE
            "",                             // AD: NOTA MÉTODO PAGO
            number_format($f->descuentotipo, 2, ',', ''),                             // AE: % DESCUENTO
            number_format($f->descuentoimporte, 2, ',', ''),                             // AF: DESCUENTO
            "",                             // AG: CARGO
            "",                             // AH: RAZÓN DEL CARGO
            ""                              // AI: REFERENCIA COMPRADOR
        ];
    }

    private function prepararFilaPet()
    {
        return [
            "PET", // A: TIPO REGISTRO (Vacío por ahora)
            "", // B: CÓDIGO OFICINA CONTABLE
            "", // C: CÓDIGO ÓRGANO GESTOR
            "", // D: CÓDIGO UNIDAD TRAMITADORA
            "", // E: EXPEDIENTE
            "", // F: PEDIDO
            "", // G: LÍNEA DEL PEDIDO
            "", // H: ALBARÁN
            "", // I: NOMBRE OFICINA CONTABLE
            "", // J: DIRECCIÓN OFICINA CONTABLE
            "", // K: CP OFICINA CONTABLE
            "", // L: CIUDAD OFICINA CONTABLE
            "", // M: PROVINCIA OFICINA CONTABLE
            "", // N: PAÍS OFICINA CONTABLE
            "", // O: NOMBRE CENTRO GESTOR
            "", // P: DIRECCIÓN CENTRO GESTOR
            "", // Q: CP ÓRGANO GESTOR
            "", // R: CIUDAD ÓRGANO GESTOR
            "", // S: PROVINCIA ÓRGANO GESTOR
            "", // T: PAÍS ÓRGANO GESTOR
            "", // U: NOMBRE UNIDAD TRAMITADORA
            "", // V: DIRECCIÓN UNIDAD TRAMITADORA
            "", // W: CP UNIDAD TRAMITADORA
            "", // X: CIUDAD UNIDAD TRAMITADORA
            "", // Y: PROVINCIA UNIDAD TRAMITADORA
            "", // Z: PAÍS UNIDAD TRAMITADORA
            "", // AA: INFO. ADICIONAL CABACERA
            "", // AB: FECHA PEDIDO
            "", // AC: CÓDIGO ÓRGANO PROPONENTE
            "", // AD: NOMBRE ÓRGANO PROPONENTE
            "", // AE: DIRECCIÓN ÓRGANO PROPONENTE
            "", // AF: CP ÓRGANO PROPONENTE
            "", // AG: CIUDAD ÓRGANO PROPONENTE
            "", // AH: PROVINCIA ÓRGANO PROPONENTE
            "", // AI: PAÍS ORGANO PROPONENTE
            ""  // AJ: CÓDIGO DIRE.
        ];
    }

    public function exportarCSV()
    {
        $respuesta = ['error' => true, 'mensaje' => 'Error al generar el fitxer'];

        if (!isset($_POST['idfacturaSelected']) || empty($_POST['idfacturaSelected'])) {
            $respuesta['mensaje'] = 'No hi ha factures seleccionades';
            echo json_encode($respuesta);
            return;
        }

        $idsEnviados = $_POST['idfacturaSelected'];
        $facturasDB = $this->modeloFacturasClientesCSV->getFacturasPorIdString(implode(",", $idsEnviados));

        if ($facturasDB) {
            
            $erroresValidacion = [];
            $camposCAB = $this->getDiccionarioCamposObligatorios();
            $camposDET = $this->getDiccionarioCamposObligatoriosDetalle();

            foreach ($facturasDB as $f) {
                $faltantesCab = [];
                $errorDetalle = ""; // Para guardar si no hay líneas o faltan campos en ellas

                // --- VALIDAR CABECERA ---
                foreach ($camposCAB as $key => $nombreAmigable) {
                    $valorCab = isset($f->$key) ? trim((string)$f->$key) : '';
                    
                    // Condición: Está vacío
                    $esVacio = ($valorCab === '');
                    
                    // Condición especial: Si es el campo 'total', no puede ser '0' ni '0.00'
                    $esCeroInvalido = ($key === 'total' && ($valorCab === '0' || $valorCab === '0.00' || $valorCab === '0,00'));

                    if ($esVacio || $esCeroInvalido) {
                        $faltantesCab[] = $nombreAmigable;
                    }
                }

                // --- VALIDAR DETALLE (Líneas) ---
                $lineas = $this->modeloFacturasClientesCSV->getLineasFactura($f->id);
                
                if (empty($lineas)) {
                    $errorDetalle = "No hi ha detall per a aquesta factura";
                } else {
                    $camposFaltantesEnLineas = [];
                    $camposNoCero = ['idproducto', 'cantidad', 'precio', 'subtotal'];
                    foreach ($lineas as $linea) {
                        foreach ($camposDET as $key => $nombreAmigable) {
                            $valor = isset($linea->$key) ? trim((string)$linea->$key) : '';

                            // Condición: Está vacío O (está en la lista de no-ceros y su valor es 0)
                            $esVacio = ($valor === '');
                            $esCeroInvalido = (in_array($key, $camposNoCero) && ($valor === '0' || $valor === '0.00'));

                            if ($esVacio || $esCeroInvalido) {
                                $camposFaltantesEnLineas[] = $nombreAmigable;
                            }
                        }
                    }
                    
                    if (!empty($camposFaltantesEnLineas)) {
                        $unicos = array_unique($camposFaltantesEnLineas);
                        $errorDetalle = "Al detall de la factura li falta o és zero: " . implode(", ", $unicos);
                    }
                }

                // Si hay algún error en esta factura, lo registramos
                if (!empty($faltantesCab) || !empty($errorDetalle)) {
                    $erroresValidacion[] = [
                        'numero' => $f->numero,
                        'campos' => $faltantesCab,
                        'errorDetalle' => $errorDetalle
                    ];
                }
            }

            if (!empty($erroresValidacion)) {
                echo json_encode([
                    'error' => true,
                    'tipo' => 'VALIDACION_DATOS',
                    'detalles' => $erroresValidacion
                ]);
                return;
            }

            // --- SI TODO ESTÁ BIEN, PROCEDER A GENERAR EL CSV ---
            $mapaFacturas = [];
            foreach ($facturasDB as $f) { $mapaFacturas[$f->id] = $f; }

            $f_temp = fopen('php://temp', 'r+');
            $contadorExportadas = 0;

            foreach ($idsEnviados as $id) {
                if (isset($mapaFacturas[$id])) {
                    if ($this->procesarFacturaIndividual($mapaFacturas[$id], $f_temp)) {
                        $contadorExportadas++;
                    }
                }
            }

            if ($contadorExportadas > 0) {
                rewind($f_temp);
                $respuesta = [
                    'error' => false,
                    'mensaje' => "S'han exportat $contadorExportadas factures correctament",
                    'csvData' => stream_get_contents($f_temp),
                    'filename' => "exportacion_facturas_" . date('Ymd_His') . ".csv"
                ];
            }
            fclose($f_temp);
        }

        echo json_encode($respuesta);
    }

    //Función Auxiliar: Gestiona el flujo de una sola factura (BD, CSV y Log)
    private function procesarFacturaIndividual($factura, $f)
    {
        // 1. Intentar actualizar el estado en la base de datos
        $actualizado = $this->modeloFacturasClientesCSV->updateFieldTablaByStringIn(
            'clientes_facturas', 'estado_exportar', 'exportada', $factura->id
        );

        if (!$actualizado) return false;

        // 2. Generar filas y escribir en el CSV físico
        $filaCab = $this->prepararFilaFactura($factura);
        fputcsv($f, $filaCab, ";");
        fputcsv($f, $this->prepararFilaPet(), ";");

        $lineas = $this->modeloFacturasClientesCSV->getLineasFactura($factura->id);
        $infoLog = implode(";", $filaCab) . PHP_EOL; // Iniciar cadena para el log con CAB

        foreach ($lineas as $linea) {
            $filaDet = $this->prepararFilaDetalle($linea);
            fputcsv($f, $filaDet, ";");
            $infoLog .= implode(";", $filaDet) . PHP_EOL; // Añadir DET a la cadena del log
        }

        // Escribir el resto de filas técnicas en el CSV
        fputcsv($f, $this->prepararFilaIVA(), ";");
        fputcsv($f, $this->prepararFilaFacturaRectificada(), ";");
        fputcsv($f, $this->prepararFilaFactoring(), ";");
        fputcsv($f, $this->prepararFilaVariosVencimientos(), ";");
        fputcsv($f, $this->prepararFilaEdi(), ";");

        // 3. Registrar el Log de forma limpia
        $this->registrarLog($factura, $infoLog);

        return true;
    }
      //Función Auxiliar: Encapsula la inserción del log
    private function registrarLog($factura, $contenido)
    {
        $datosLog = [
            'idfacturaexportada'     => $factura->id,
            'numerofacturaexportada' => $factura->numero,
            'usuarioexportador'      => $_SESSION['usuario'] ?? 'Desconocido',
            'infoexportada'          => $contenido
        ];
        return $this->modeloFacturasClientesCSV->insertarLogExportacion($datosLog);
    }

        private function getDiccionarioCamposObligatoriosDetalle() {
        return [
            'idproducto'      => 'Codi de Producte',
            'descripcion'     => 'Nom del Producte',
            'cantidad'        => 'Quantitat',
            'precio'          => 'Preu',
            'subtotal'        => 'Base Imponible',
            'ivatipo'         => '% Impost',
            'descuentolinea'  => 'Descompte de Línia'
        ];
    }

    private function prepararFilaDetalle($linea)
    {
        return [
            "DET",                          // A: TIPO REGISTRO =M
            $linea->idproducto,             // B: CÓDIGO PRODUCTO =M
            $linea->descripcion,            // C: NOMBRE PRODUCTO =M
            number_format($linea->cantidad, 2, ',', ''),// D: CANTIDAD =M
            $linea->unidad,                 // E: UNIDAD
            number_format($linea->precio, 2, ',', ''),// F: PRECIO =M
            number_format($linea->descuentotipo, 2, ',', ''), // G: % DTO 
            number_format($linea->descuentolinea, 2, ',', ''),// H: DESCUENTO =M
            number_format($linea->ivatipo, 2, ',', ''),// I: % IMPUESTO =M
            number_format($linea->subtotal, 2, ',', ''),          // J: BASE IMPONIBLE =M
            number_format($linea->importetotal, 2, ',', ''),                  // K: IMPORTE TOTAL 
            "",                             // L: INFO ADICIONAL LINEA
            "",                             // M: PEDIDO
            "",                             // N: LINEA EN EL PEDIDO
            "",                             // O: CÓDIGO ASIGNACIÓN
            "",                             // P: ALBARÁN
            "",                             // Q: MOTIVO EXENCIÓN IMPUESTO
            "",                             // R: CARGO
            "",                             // S: CERTIFICACIÓN RFE
            $linea->fecha,                  // T: INICIO PERIODO DE FACTURACIÓN
            $linea->vencimiento,            // U: FIN DEL PERIODO FACTURACIÓN
            "",                             // V: NÚMERO DE CONTRATO
            "",                             // W: INFORMACIÓN ADICIONAL
            "",                             // X: % IRPF
            "",                             // Y: FECHA DE CONTRATO
            ""                              // Z: CÓDIGO PRODUCTO COMPRADOR
        ];
    }

    private function prepararFilaIVA()
    {
        return ["IVA"];
    }

    private function prepararFilaFacturaRectificada()
    {
        return [
            "REC", // A: TIPO REGISTRO
            "",    // B: NÚMERO DE FACTURA RECTIFICADA
            "",    // C: CÓDIGO DEL MOTIVO
            "",    // D: FECHA FISCAL - INICIO
            "",    // E: FECHA FISCAL - FIN
            "",    // F: METODO DE CORRECCIÓN
            ""     // G: AMPLIACIÓN MOTIVO
        ];
    }

        private function prepararFilaFactoring()
        {
            return [
                "FAC", // A: TIPO REGISTRO
                "",//TIPO CESIONARIO
                "",// TIPO RESIDENCIA CESIONARIO
                "", //IDENTIFICADOR CESIONARIO
                "", //NOMBRE CESIONARIO
                "", //DIRECCIÓN CESIONARIO
                "", //CP CESIONARIO
                "", //CIUDAD CESIONARIO
                "", //PROVÍNCIA CESIONARIO
                "", //PAÍS CESIONARIO
                "", //INFORMACIÓN ADICIONAL CESIONARIO
                "", //VENCIMIENTO
                "", //IMPORTE
                "", //MEDIO DE PAGO
                "", //IBAN
                "", //CÓDIGO BANCARIO
                ""  //TEXTO CLAUSULA CESIÓN
            ];
        }

            private function prepararFilaEdi()
            {
                return [
                "DUE", //TIPO REGISTRO
                "", //TIPO DE FACTURA
                "", //PO LEGAL- NADBCO
                "", //PO COMPRADOR- NADBY
                "", //PO RECEPTOR DE LA FACTURA- NADIV
                "", //PO RECEPTOR DE LA MERCANCÍA- NADDP
                "", //PO EMISOR DEL PAGO- NADPR
                ];
            }

            private function prepararFilaVariosVencimientos()
            {
                return [
                "CABEDI", //TIPO REGISTRO
                "", //FECHA VENCIMIENTO
                "", //IMPORTE VENCIMIENTO
                ];
            }


    }