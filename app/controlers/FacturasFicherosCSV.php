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
            $idCliente = (int)$_POST['idclientesearch'];
            $fechaInicio = $_POST['fechainicio'];
            $fechaFin = $_POST['fechafin'];

            $query_search = " fac.idcliente = $idCliente ";
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
                    'html_factura' => $html // Este nombre debe coincidir con el JS
                ]);
            } else {
                echo json_encode(['error' => true, 'mensaje' => 'Factura no trobada']);
            }
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
            number_format($f->total, 2, ',', ''),                      // R: IMPORTE TOTAL
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
        $respuesta['error'] = true;
        $respuesta['mensaje'] = 'Error al generar el fitxer';

        if(isset($_POST['idfacturaSelected']) && count($_POST['idfacturaSelected']) > 0) {
            
            $strIdesFactura = implode(",", $_POST['idfacturaSelected']);
            $facturas = $this->modeloFacturasClientesCSV->getFacturasPorIdString($strIdesFactura);

            if($facturas){
                $filename = "exportacion_facturas_" . date('Ymd_His') . ".csv";
                $f = fopen('php://temp', 'r+'); 
                $contadorExportadas = 0;

                foreach ($facturas as $factura) {
                    
                    $actualizado = $this->modeloFacturasClientesCSV->updateFieldTablaByStringIn(
                        'clientes_facturas', 
                        'estado_exportar', 
                        'exportada', 
                        $factura->id 
                    );

                    if($actualizado) {
                        $contadorExportadas++;

                        // 1. CABECERA
                        fputcsv($f, $this->prepararFilaFactura($factura), ";");

                        // 2. FILA AUXILIAR
                        fputcsv($f, $this->prepararFilaPet(), ";");

                        // 3. DETALLES
                        $lineas = $this->modeloFacturasClientesCSV->getLineasFactura($factura->id);
                        foreach ($lineas as $linea) {
                            fputcsv($f, $this->prepararFilaDetalle($linea), ";");
                        }

                        // 4. IVA
                        fputcsv($f, $this->prepararFilaIVA(), ";");

                        // 5. RECTIFICADA (Nueva posición: después de IVA)
                        fputcsv($f, $this->prepararFilaFacturaRectificada(), ";");

                        // 6. FACTORING (Nueva posición: después de RECTIFICADA)
                        fputcsv($f, $this->prepararFilaFactoring(), ";");

                        // 7. VARIOS VENCIMIENTOS (Nueva posición: después de FACTORING)
                        fputcsv($f, $this->prepararFilaVariosVencimientos(), ";");

                        //8. EDI (Nueva posición: después de VARIOS VENCIMIENTOS)
                        fputcsv($f, $this->prepararFilaEdi(), ";");
                    }
                }

                if($contadorExportadas > 0) {
                    rewind($f);
                    $csvContent = stream_get_contents($f);
                    fclose($f);

                    $respuesta['error'] = false;
                    $respuesta['mensaje'] = "S'han exportat $contadorExportadas factures correctament";
                    $respuesta['csvData'] = $csvContent; 
                    $respuesta['filename'] = $filename;
                } else {
                    $respuesta['mensaje'] = "No s'ha pogut actualitzar l'estat de ninguna factura. No s'ha generat el fitxer.";
                }
            }
        } else {
            $respuesta['mensaje'] = 'No hi ha factures seleccionades';
        }

        echo json_encode($respuesta);
    }

    private function prepararFilaDetalle($linea)
    {
        return [
            "DET",                          // A: TIPO REGISTRO
            $linea->idproducto,             // B: CÓDIGO PRODUCTO
            $linea->descripcion,            // C: NOMBRE PRODUCTO
            number_format($linea->cantidad, 2, ',', ''),               // D: CANTIDAD
            $linea->unidad,                 // E: UNIDAD
            number_format($linea->precio, 2, ',', ''),                 // F: PRECIO
            number_format($linea->descuentotipo, 2, ',', ''), // G: % DTO (Vacío según instrucción)
            number_format($linea->descuentolinea, 2, ',', ''),       // H: DESCUENTO (De la factura)
            number_format($linea->ivatipo, 2, ',', ''),               // I: % IMPUESTO (De la factura)
            number_format($linea->subtotal, 2, ',', ''),          // J: BASE IMPONIBLE (De la factura)
            number_format($linea->importetotal, 2, ',', ''),                  // K: IMPORTE TOTAL (De la factura)
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