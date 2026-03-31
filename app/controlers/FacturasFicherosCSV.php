<?php
error_reporting(0);

class FacturasFicherosCSV extends Controlador {

    const MAX_FACTURAS_EXPORTAR = 10;

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

        if (!$facturasDB) {
            $respuesta['mensaje'] = 'No s\'han trobat les factures';
            echo json_encode($respuesta);
            return;
        }

        // Validar datos
        $erroresValidacion = $this->validarDatosObligatorios($facturasDB);
        if (!empty($erroresValidacion)) {
            echo json_encode([
                'error' => true,
                'tipo' => 'VALIDACION_DATOS',
                'detalles' => $erroresValidacion
            ]);
            return;
        }

        // Generar CSV
        $resultado = $this->generarCSV($facturasDB, $idsEnviados);
        echo json_encode($resultado);
    }

    private function generarCSV($facturasDB, $idsEnviados)
    {
        $mapaFacturas = [];
        foreach ($facturasDB as $f) { $mapaFacturas[$f->id] = $f; }

        $f_temp = fopen('php://temp', 'r+');
        $contadorExportadas = 0;
        $errores = [];

        foreach ($idsEnviados as $id) {
            if (isset($mapaFacturas[$id])) {
                $factura = $mapaFacturas[$id];
                if ($this->procesarFacturaIndividual($factura, $f_temp)) {
                    $contadorExportadas++;
                } else {
                    $errores[] = "No s'ha pogut exportar factura {$factura->numero}";
                }
            }
        }

        if ($contadorExportadas > 0) {
            rewind($f_temp);
            $contenido = stream_get_contents($f_temp);
            fclose($f_temp);
            return [
                'error' => false,
                'mensaje' => "S'han exportat $contadorExportadas factures correctament" . (empty($errores) ? '' : ' Amb alguns errors: ' . implode(', ', $errores)),
                'contenido' => $contenido,
                'tipo_mime' => 'text/csv;charset=utf-8;',
                'filename' => "exportacion_facturas_" . date('Ymd_His') . ".csv"
            ];
        } else {
            fclose($f_temp);
            return ['error' => true, 'mensaje' => 'No s\'ha pogut exportar cap factura'];
        }
    }

    public function exportarXML()
    {
        $respuesta = ['error' => true, 'mensaje' => 'Error al generar el fitxer XML'];

        if (!isset($_POST['idfacturaSelected']) || empty($_POST['idfacturaSelected'])) {
            $respuesta['mensaje'] = 'No hi ha factures seleccionades';
            echo json_encode($respuesta);
            return;
        }

        $idsEnviados = $_POST['idfacturaSelected'];
        
        // Validar límite máximo
        if (count($idsEnviados) > self::MAX_FACTURAS_EXPORTAR) {
            $respuesta['mensaje'] = 'Màxim ' . self::MAX_FACTURAS_EXPORTAR . ' factures per exportació. Has seleccionat ' . count($idsEnviados) . '.';
            echo json_encode($respuesta);
            return;
        }

        $facturasDB = $this->modeloFacturasClientesCSV->getFacturasPorIdString(implode(",", $idsEnviados));

        if (!$facturasDB) {
            $respuesta['mensaje'] = 'No s\'han trobat les factures';
            echo json_encode($respuesta);
            return;
        }

        // Validar datos obligatorios
        $erroresValidacion = $this->validarDatosObligatorios($facturasDB);
        if (!empty($erroresValidacion)) {
            echo json_encode([
                'error' => true,
                'tipo' => 'VALIDACION_DATOS',
                'detalles' => $erroresValidacion
            ]);
            return;
        }

        // Generar XMLs individuales
        $archivos = [];
        $contadorExitos = 0;
        $errores = [];

        foreach ($idsEnviados as $id) {
            // Buscar la factura en el array $facturasDB
            $factura = null;
            foreach ($facturasDB as $f) {
                if ($f->id == $id) {
                    $factura = $f;
                    break;
                }
            }
            if (!$factura) {
                $errores[] = "Factura ID $id no trobada";
                continue;
            }

            // Generar XML para esta factura
            $xmlData = $this->generarXMLIndividual($factura);
            if ($xmlData['error'] === false) {
                $archivos[] = [
                    'contenido' => $xmlData['contenido'],
                    'filename' => $xmlData['filename']
                ];
                $contadorExitos++;
            } else {
                $errores[] = "Error en factura {$factura->numero}: {$xmlData['mensaje']}";
            }
        }

        if ($contadorExitos > 0) {
            $respuesta = [
                'error' => false,
                'mensaje' => "S'han exportat $contadorExitos factures correctament" . (empty($errores) ? '' : ' Amb alguns errors: ' . implode(', ', $errores)),
                'multifile' => true,
                'archivos' => $archivos
            ];
        } else {
            $respuesta['mensaje'] = 'No s\'ha pogut exportar cap factura';
        }

        echo json_encode($respuesta);
    }

    private function generarXMLIndividual($factura)
    {
        try {
            // Crear documento XML
            $doc = new DOMDocument('1.0', 'UTF-8');
            $doc->formatOutput = true;
            
            // Elemento raíz Facturae
            $root = $doc->createElementNS('http://www.facturae.gob.es/formato/Versiones/Facturaev3_2_2.xml', 'Facturae');
            $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
            $doc->appendChild($root);
            
            // 1. FileHeader (individual)
            $fileHeader = $this->crearFileHeaderIndividual($doc, $factura);
            $root->appendChild($fileHeader);
            
            // 2. Parties
            $parties = $this->crearParties($doc, $factura);
            $root->appendChild($parties);
            
            // 3. Invoices (una sola factura)
            $invoicesNode = $doc->createElement('Invoices');
            $invoiceNode = $this->crearInvoice($doc, $factura);
            $invoicesNode->appendChild($invoiceNode);
            $root->appendChild($invoicesNode);
            
            // Generar string XML
            $xmlString = $doc->saveXML();
            
            return [
                'error' => false,
                'contenido' => $xmlString,
                'filename' => "factura_" . $factura->numero . "_" . date('Ymd_His') . ".xml"
            ];
            
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => 'Error generant XML: ' . $e->getMessage()];
        }
    }

    private function crearFileHeaderIndividual($doc, $factura)
    {
        $fileHeader = $doc->createElement('FileHeader');
        
        $schemaVersion = $doc->createElement('SchemaVersion', '3.2.2');
        $fileHeader->appendChild($schemaVersion);
        
        // Modality individual
        $modality = $doc->createElement('Modality', 'I');
        $fileHeader->appendChild($modality);
        
        $issuerType = $doc->createElement('InvoiceIssuerType', 'EM');
        $fileHeader->appendChild($issuerType);
        
        // Batch
        $batch = $doc->createElement('Batch');
        $batchIdentifier = $doc->createElement('BatchIdentifier', 'LOTE_' . date('YmdHis') . '_' . $factura->id);
        $batch->appendChild($batchIdentifier);
        
        $invoicesCount = $doc->createElement('InvoicesCount', '1');
        $batch->appendChild($invoicesCount);
        
        // Totales para una factura
        $totalInvoicesAmount = (float)$factura->total;
        $totalOutstandingAmount = (float)$factura->total;
        $totalExecutableAmount = (float)$factura->total;
        
        $totalInvoicesAmountNode = $doc->createElement('TotalInvoicesAmount');
        $totalInvoicesAmountNode->appendChild($this->createTotalAmountElement($doc, $totalInvoicesAmount));
        $batch->appendChild($totalInvoicesAmountNode);
        
        $totalOutstandingAmountNode = $doc->createElement('TotalOutstandingAmount');
        $totalOutstandingAmountNode->appendChild($this->createTotalAmountElement($doc, $totalOutstandingAmount));
        $batch->appendChild($totalOutstandingAmountNode);
        
        $totalExecutableAmountNode = $doc->createElement('TotalExecutableAmount');
        $totalExecutableAmountNode->appendChild($this->createTotalAmountElement($doc, $totalExecutableAmount));
        $batch->appendChild($totalExecutableAmountNode);
        
        $invoiceCurrencyCode = $doc->createElement('InvoiceCurrencyCode', 'EUR');
        $batch->appendChild($invoiceCurrencyCode);
        
        $fileHeader->appendChild($batch);
        
        return $fileHeader;
    }

    private function crearParties($doc, $factura)
    {
        $parties = $doc->createElement('Parties');
        
        // SellerParty
        $sellerParty = $doc->createElement('SellerParty');
        
        // TaxIdentification del vendedor
        $taxIdSeller = $doc->createElement('TaxIdentification');
        $taxIdSeller->appendChild($doc->createElement('PersonTypeCode', 'J'));
        $taxIdSeller->appendChild($doc->createElement('ResidenceTypeCode', 'R'));
        $taxIdSeller->appendChild($doc->createElement('TaxIdentificationNumber', $factura->cifpiensos));
        $sellerParty->appendChild($taxIdSeller);
        
        // LegalEntity
        $legalEntity = $doc->createElement('LegalEntity');
        $legalEntity->appendChild($doc->createElement('CorporateName', $factura->razonsocialpiensos));
        
        // AddressInSpain
        $address = $doc->createElement('AddressInSpain');
        $address->appendChild($doc->createElement('Address', $factura->direccionpiensos));
        $address->appendChild($doc->createElement('PostCode', $factura->codigopostalpiensos));
        $address->appendChild($doc->createElement('Town', $factura->localidadpiensos));
        $address->appendChild($doc->createElement('Province', $factura->provinciapiensos));
        $address->appendChild($doc->createElement('CountryCode', 'ESP'));
        $legalEntity->appendChild($address);
        
        $sellerParty->appendChild($legalEntity);
        $parties->appendChild($sellerParty);
        
        // BuyerParty
        $buyerParty = $doc->createElement('BuyerParty');
        
        $taxIdBuyer = $doc->createElement('TaxIdentification');
        $taxIdBuyer->appendChild($doc->createElement('PersonTypeCode', 'J')); // Puede ser física también, pero simplificamos
        $taxIdBuyer->appendChild($doc->createElement('ResidenceTypeCode', 'R'));
        $taxIdBuyer->appendChild($doc->createElement('TaxIdentificationNumber', $factura->cliente_nif));
        $buyerParty->appendChild($taxIdBuyer);
        
        $legalEntityBuyer = $doc->createElement('LegalEntity');
        $legalEntityBuyer->appendChild($doc->createElement('CorporateName', $factura->cliente_nombre));
        
        $addressBuyer = $doc->createElement('AddressInSpain');
        $addressBuyer->appendChild($doc->createElement('Address', $factura->cliente_direccion));
        $addressBuyer->appendChild($doc->createElement('PostCode', $factura->cliente_cp));
        $addressBuyer->appendChild($doc->createElement('Town', $factura->cliente_poblacion));
        $addressBuyer->appendChild($doc->createElement('Province', $factura->cliente_provincia));
        $addressBuyer->appendChild($doc->createElement('CountryCode', 'ESP'));
        $legalEntityBuyer->appendChild($addressBuyer);
        
        $buyerParty->appendChild($legalEntityBuyer);
        $parties->appendChild($buyerParty);
        
        return $parties;
    }

    private function crearInvoice($doc, $factura)
    {
        $invoice = $doc->createElement('Invoice');
        
        // InvoiceHeader
        $invoiceHeader = $doc->createElement('InvoiceHeader');
        $invoiceHeader->appendChild($doc->createElement('InvoiceNumber', $factura->numero));
        if (!empty($factura->serie)) {
            $invoiceHeader->appendChild($doc->createElement('InvoiceSeriesCode', $factura->serie));
        }
        $invoiceHeader->appendChild($doc->createElement('InvoiceDocumentType', 'FC'));
        $invoiceHeader->appendChild($doc->createElement('InvoiceClass', 'OO'));
        $invoice->appendChild($invoiceHeader);
        
        // InvoiceIssueData
        $issueData = $doc->createElement('InvoiceIssueData');
        $issueData->appendChild($doc->createElement('IssueDate', $factura->fecha));
        if (!empty($factura->fecha_operacion)) {
            $issueData->appendChild($doc->createElement('OperationDate', $factura->fecha_operacion));
        }
        $place = $doc->createElement('PlaceOfIssue');
        $place->appendChild($doc->createElement('PostCode', $factura->codigopostalpiensos));
        $place->appendChild($doc->createElement('PlaceOfIssueDescription', $factura->localidadpiensos));
        $issueData->appendChild($place);
        
        $issueData->appendChild($doc->createElement('InvoiceCurrencyCode', 'EUR'));
        $issueData->appendChild($doc->createElement('TaxCurrencyCode', 'EUR'));
        $issueData->appendChild($doc->createElement('LanguageName', 'es'));
        $invoice->appendChild($issueData);
        
        // Obtener líneas
        $lineas = $this->modeloFacturasClientesCSV->getLineasFactura($factura->id);
        
        // Agrupar impuestos
        $impuestos = $this->agruparImpuestos($lineas);
        
        // TaxesOutputs (cabecera)
        $taxesOutputs = $doc->createElement('TaxesOutputs');
        foreach ($impuestos['iva'] as $iva) {
            $tax = $doc->createElement('Tax');
            $tax->appendChild($doc->createElement('TaxTypeCode', '01'));
            $tax->appendChild($doc->createElement('TaxRate', number_format($iva['porcentaje'], 2, '.', '')));
            
            $taxableBase = $doc->createElement('TaxableBase');
            $taxableBase->appendChild($this->createTotalAmountElement($doc, $iva['base']));
            $tax->appendChild($taxableBase);
            
            $taxAmount = $doc->createElement('TaxAmount');
            $taxAmount->appendChild($this->createTotalAmountElement($doc, $iva['cuota']));
            $tax->appendChild($taxAmount);
            
            $taxesOutputs->appendChild($tax);
        }
        $invoice->appendChild($taxesOutputs);
        
        // TaxesWithheld (si hubiera IRPF)
        if (!empty($impuestos['irpf'])) {
            $taxesWithheld = $doc->createElement('TaxesWithheld');
            foreach ($impuestos['irpf'] as $irpf) {
                $tax = $doc->createElement('Tax');
                $tax->appendChild($doc->createElement('TaxTypeCode', '04'));
                $tax->appendChild($doc->createElement('TaxRate', number_format($irpf['porcentaje'], 2, '.', '')));
                
                $taxableBase = $doc->createElement('TaxableBase');
                $taxableBase->appendChild($this->createTotalAmountElement($doc, $irpf['base']));
                $tax->appendChild($taxableBase);
                
                $taxAmount = $doc->createElement('TaxAmount');
                $taxAmount->appendChild($this->createTotalAmountElement($doc, $irpf['cuota']));
                $tax->appendChild($taxAmount);
                
                $taxesWithheld->appendChild($tax);
            }
            $invoice->appendChild($taxesWithheld);
        }
        
        // InvoiceTotals
        $totals = $this->calcularTotales($factura, $lineas, $impuestos);
        $invoiceTotals = $doc->createElement('InvoiceTotals');
        $invoiceTotals->appendChild($doc->createElement('TotalGrossAmount', number_format($totals['totalGrossAmount'], 2, '.', '')));
        if ($totals['totalGeneralDiscounts'] > 0) {
            $invoiceTotals->appendChild($doc->createElement('TotalGeneralDiscounts', number_format($totals['totalGeneralDiscounts'], 2, '.', '')));
        }
        $invoiceTotals->appendChild($doc->createElement('TotalGrossAmountBeforeTaxes', number_format($totals['totalGrossAmountBeforeTaxes'], 2, '.', '')));
        $invoiceTotals->appendChild($doc->createElement('TotalTaxOutputs', number_format($totals['totalTaxOutputs'], 2, '.', '')));
        $invoiceTotals->appendChild($doc->createElement('TotalTaxesWithheld', number_format($totals['totalTaxesWithheld'], 2, '.', '')));
        $invoiceTotals->appendChild($doc->createElement('InvoiceTotal', number_format($totals['invoiceTotal'], 2, '.', '')));
        $invoiceTotals->appendChild($doc->createElement('TotalOutstandingAmount', number_format($totals['totalOutstandingAmount'], 2, '.', '')));
        $invoiceTotals->appendChild($doc->createElement('TotalExecutableAmount', number_format($totals['totalExecutableAmount'], 2, '.', '')));
        $invoice->appendChild($invoiceTotals);
        
        // Items
        $items = $doc->createElement('Items');
        foreach ($lineas as $linea) {
            $invoiceLine = $doc->createElement('InvoiceLine');
            $invoiceLine->appendChild($doc->createElement('ItemDescription', $linea->descripcion));
            $invoiceLine->appendChild($doc->createElement('Quantity', number_format($linea->cantidad, 2, '.', '')));
            
            // Unidad de medida
            if (!empty($linea->unidad)) {
                if ($linea->unidad == 'Tn') {
                    $invoiceLine->appendChild($doc->createElement('UnitOfMeasure', '05'));
                    $invoiceLine->appendChild($doc->createElement('AdditionalLineItemInformation', 'Unidad de medida: toneladas'));
                } else {
                    $invoiceLine->appendChild($doc->createElement('UnitOfMeasure', $linea->unidad));
                }
            }
            
            $invoiceLine->appendChild($doc->createElement('UnitPriceWithoutTax', number_format($linea->precio, 2, '.', '')));
            $invoiceLine->appendChild($doc->createElement('TotalCost', number_format($linea->cantidad * $linea->precio, 2, '.', '')));
            
            if ($linea->descuentolinea > 0) {
                $discounts = $doc->createElement('DiscountsAndRebates');
                $discount = $doc->createElement('Discount');
                $discount->appendChild($doc->createElement('DiscountReason', 'Descuento'));
                $discount->appendChild($doc->createElement('DiscountAmount', number_format($linea->descuentolinea, 2, '.', '')));
                $discounts->appendChild($discount);
                $invoiceLine->appendChild($discounts);
            }
            
            $grossAmount = $linea->subtotal - ($linea->descuentolinea ?? 0);
            $invoiceLine->appendChild($doc->createElement('GrossAmount', number_format($grossAmount, 2, '.', '')));
            
            // Impuestos por línea
            $lineTaxes = $doc->createElement('TaxesOutputs');
            $taxLine = $doc->createElement('Tax');
            $taxLine->appendChild($doc->createElement('TaxTypeCode', '01'));
            $taxLine->appendChild($doc->createElement('TaxRate', number_format($linea->ivatipo, 2, '.', '')));
            
            $taxableBase = $doc->createElement('TaxableBase');
            $taxableBase->appendChild($this->createTotalAmountElement($doc, $grossAmount));
            $taxLine->appendChild($taxableBase);
            
            $taxAmount = $doc->createElement('TaxAmount');
            $taxAmount->appendChild($this->createTotalAmountElement($doc, $linea->ivaimporte));
            $taxLine->appendChild($taxAmount);
            
            $lineTaxes->appendChild($taxLine);
            $invoiceLine->appendChild($lineTaxes);
            
            $items->appendChild($invoiceLine);
        }
        $invoice->appendChild($items);
        
        // PaymentDetails
        if (!empty($factura->vencimiento) && !empty($factura->codigob2brouter)) {
            $paymentDetails = $doc->createElement('PaymentDetails');
            $installment = $doc->createElement('Installment');
            $installment->appendChild($doc->createElement('InstallmentDueDate', $factura->vencimiento));
            $installment->appendChild($doc->createElement('InstallmentAmount', number_format($factura->total, 2, '.', '')));
            
            $paymentMeans = $factura->codigob2brouter;
            if ($paymentMeans == '02' || $paymentMeans == '04') {
                $installment->appendChild($doc->createElement('PaymentMeans', '04'));
                if (!empty($factura->numerocuenta)) {
                    $account = $doc->createElement('AccountToBeCredited');
                    $account->appendChild($doc->createElement('IBAN', $factura->numerocuenta));
                    $installment->appendChild($account);
                }
            } else {
                $installment->appendChild($doc->createElement('PaymentMeans', $paymentMeans));
            }
            
            $paymentDetails->appendChild($installment);
            $invoice->appendChild($paymentDetails);
        }
        
        return $invoice;
    }

    private function agruparImpuestos($lineas)
    {
        $iva = [];
        $irpf = [];
        foreach ($lineas as $linea) {
            $base = $linea->subtotal - ($linea->descuentolinea ?? 0);
            $cuota = $linea->ivaimporte;
            $porcentaje = $linea->ivatipo;
            // IVA
            $key = 'iva_' . $porcentaje;
            if (!isset($iva[$key])) {
                $iva[$key] = ['porcentaje' => $porcentaje, 'base' => 0, 'cuota' => 0];
            }
            $iva[$key]['base'] += $base;
            $iva[$key]['cuota'] += $cuota;
        }
        return ['iva' => array_values($iva), 'irpf' => []];
    }

    private function calcularTotales($factura, $lineas, $impuestos)
    {
        $totalGrossAmount = 0;
        $totalGeneralDiscounts = 0;
        foreach ($lineas as $linea) {
            $totalGrossAmount += $linea->subtotal;
            $totalGeneralDiscounts += $linea->descuentolinea ?? 0;
        }
        $totalGrossAmountBeforeTaxes = $totalGrossAmount - $totalGeneralDiscounts;
        $totalTaxOutputs = array_sum(array_column($impuestos['iva'], 'cuota'));
        $totalTaxesWithheld = array_sum(array_column($impuestos['irpf'], 'cuota'));
        $invoiceTotal = $totalGrossAmountBeforeTaxes + $totalTaxOutputs - $totalTaxesWithheld;
        $totalOutstandingAmount = $invoiceTotal; // Si no hay anticipos
        $totalExecutableAmount = $totalOutstandingAmount; // Simplificado
        
        return [
            'totalGrossAmount' => $totalGrossAmount,
            'totalGeneralDiscounts' => $totalGeneralDiscounts,
            'totalGrossAmountBeforeTaxes' => $totalGrossAmountBeforeTaxes,
            'totalTaxOutputs' => $totalTaxOutputs,
            'totalTaxesWithheld' => $totalTaxesWithheld,
            'invoiceTotal' => $invoiceTotal,
            'totalOutstandingAmount' => $totalOutstandingAmount,
            'totalExecutableAmount' => $totalExecutableAmount,
        ];
    }

    private function validarDatosObligatorios($facturasDB)
    {
        $erroresValidacion = [];
        $camposCAB = $this->getDiccionarioCamposObligatorios();
        $camposDET = $this->getDiccionarioCamposObligatoriosDetalle();

        foreach ($facturasDB as $f) {
            $faltantesCab = [];
            $errorDetalle = "";

            // Validar cabecera
            foreach ($camposCAB as $key => $nombreAmigable) {
                $valorCab = isset($f->$key) ? trim((string)$f->$key) : '';
                $esVacio = ($valorCab === '');
                $esCeroInvalido = ($key === 'total' && ($valorCab === '0' || $valorCab === '0.00' || $valorCab === '0,00'));

                if ($esVacio || $esCeroInvalido) {
                    $faltantesCab[] = $nombreAmigable;
                }
            }

            // Validar detalle
            $lineas = $this->modeloFacturasClientesCSV->getLineasFactura($f->id);
            if (empty($lineas)) {
                $errorDetalle = "No hi ha detall per a aquesta factura";
            } else {
                $camposFaltantesEnLineas = [];
                $camposNoCero = ['idproducto', 'cantidad', 'precio', 'subtotal'];
                foreach ($lineas as $linea) {
                    foreach ($camposDET as $key => $nombreAmigable) {
                        $valor = isset($linea->$key) ? trim((string)$linea->$key) : '';
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

            if (!empty($faltantesCab) || !empty($errorDetalle)) {
                $erroresValidacion[] = [
                    'numero' => $f->numero,
                    'campos' => $faltantesCab,
                    'errorDetalle' => $errorDetalle
                ];
            }
        }

        return $erroresValidacion;
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

            private function createTotalAmountElement($doc, $value)
            {
                $totalAmount = $doc->createElement('TotalAmount');
                $totalAmount->appendChild($doc->createTextNode(number_format($value, 2, '.', '')));
                return $totalAmount;
            }

    }