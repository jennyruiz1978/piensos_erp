<?php

class AlbaranesClientes extends Controlador {

   
    private $arrFields;
    private $tabla;
    private $fetch;

    public function __construct() {
        session_start();
    

        $this->tabla = 'clientes_albaranes';

        $this->arrFieldsCreate = ['numero'];
        $this->arrFieldsUpdateFirst = ['numerointerno','numero','idcliente','cliente','fecha','observaciones','total','ivatotal','baseimponible'];
        $this->arrFieldsUpdate = ['idcliente','cliente','fecha','observaciones','total','ivatotal','baseimponible'];
        $this->arrFieldsValidate = ['idcliente','fecha'];
        $this->tablaRows = 'clientes_albaranes_det';
        $this->tabla_clientes_facturas_det = 'clientes_facturas_det';
        $this->tabla_clientes_facturas = 'clientes_facturas';
        $this->arrFieldsRowsCreate = ['idalbaran','idproducto','descripcion','unidad','cantidad','precio','ivatipo','subtotal','idfactura'];
        $this->arrFieldsRowsUpdate = ['idproducto','descripcion','unidad','cantidad','precio', 'ivatipo','subtotal'];        
        $this->arrFieldsRowsCreateInvoice = ['idproducto','idfactura','descripcion','unidad','cantidad','precio','ivatipo','subtotal','idfilaalbaran','idalbaran'];
        $this->modeloAlbaranCliente = $this->modelo('ModeloAlbaranCliente');
        $this->modeloReciboCliente = $this->modelo('ModeloReciboCliente');
        $this->modeloAlbaranDetalleCliente = $this->modelo('ModeloAlbaranDetalleCliente');        
        $this->modeloCliente = $this->modelo('ModeloCliente');
        $this->modeloProductoVenta = $this->modelo('ModeloProductoVenta');
        $this->modeloBase = $this->modelo('ModeloBase');
        $this->modeloIva = $this->modelo('ModeloIva');
        $this->modeloPlanificacion = $this->modelo('ModeloPlanificacion');
        $this->modeloCondiciones = $this->modelo('ModeloCondiciones');
        $this->modeloCuentasBancarias = $this->modelo('ModeloCuentasBancarias');
        $this->modeloFormasPago = $this->modelo('ModeloFormasPago');
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');
        $this->modeloFacturaCliente = $this->modelo('ModeloFacturaCliente');
        $this->modeloFacturaDetalleCliente = $this->modelo('ModeloFacturaDetalleCliente');
        $this->arrFieldsRowsUpdateFactura = ['idproducto','descripcion','unidad','cantidad','precio', 'ivatipo','subtotal'];
     

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        } 

    /*         
        echo"<br>imprimo this2<br>";
        print_r($this);
        die;
    */
        
    }

    public function index() {
        $datos = [];
        $this->vista('albaranesCliente/albaranes', $datos);
    }

       
    public function tablaAlbaranesCliente()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];      
        
       /*  echo"<br><br>where1<br>";
        print_r($where); */

        $valHaving= $this->extractSumacantidadValue($where);
        /* echo"<br><br>valHaving<br>";
        print_r($valHaving); */
        
        $havingSumCant = '';
        if (!empty($valHaving)) {
            $havingSumCant = " HAVING COALESCE(SUM(det.cantidad), 0) like '%".$valHaving."%' ";
             // Reemplazar el patrón con "1 like '%%'"
            $where = str_replace("sumacantidad like '%$valHaving%'", "1 like '%%'", $where);
        }

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);       
        
        $where = str_replace("fecha like", "DATE_FORMAT(fecha, '%d/%m/%Y') like", $where); 
        $where = str_replace("sumacantidad like", " 1 like", $where);    

        $albaranes = $this->modeloAlbaranCliente->obtenerAlbaranesClientesTabla($page,$order,$where,$limit, $havingSumCant);
        $totalRegistrosArray = $this->modeloAlbaranCliente->obtenerTotalAlbaranesCliente($where, $havingSumCant);
        
        $totalRegistros = count($totalRegistrosArray);     

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $albaranes
        ];

        print_r(json_encode($salida));
    }      

    public function extractSumacantidadValue($where) {        
        $pattern = "/sumacantidad like '%(\d+)%'/";
            
        $value = null;
            
        if (preg_match($pattern, $where, $matches)) {            
            $value = $matches[1];
        }                          
        return $value;
    }

    public function altaAlbaranes()
    {        
        $idAlbaran = $this->crearAlbaran();    
        
        if($idAlbaran && $idAlbaran >0){
            redireccionar('/AlbaranesClientes/verAlbaran/'.$idAlbaran);
        }else{
            redireccionar('/AlbaranesClientes');
        }
    }

    private function crearAlbaran()
    {
            $ins = false;                        
            
            $arrValues['numero'] = 0;            

            $stringQueries = UtilsHelper::buildStringsInsertQuery($arrValues, $this->arrFieldsCreate);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];
                       
            if($ok){
                $ins = $this->modeloBase->insertRow($this->tabla, $strFields, $strValues);                
            }                                
            return $ins;        
    }

    public function verAlbaran($idAlbaran)
    {            
   
        if(isset($idAlbaran) && $idAlbaran > 0){

            if($this->modeloBase->existIdInvoice($this->tabla, $idAlbaran) > 0){
            
                $albaran = $this->modeloAlbaranCliente->getAlbaranData($idAlbaran);
                if(isset($albaran->numero) && $albaran->numero != "" && $albaran->numero != "0"){
                    $existe = 1;
                    $cab = $this->construirCabeceraAlbaranCliente($idAlbaran);
                    $cabecera = (array) $cab;
                    $nifCliente = $this->modeloCliente->getNifClient($cab->idcliente);
                    $cabecera['nifCliente'] = $nifCliente;                
                    $detalle['html'] = $this->construirBodyTablaGrilla($idAlbaran, 'albaran');
                    if($cab->idcliente > 0){
                        $cli = $this->modeloCliente->getClientZoneProfitMargin($cab->idcliente);
                        $cabecera['zona'] = $cli->zona;
                        $cabecera['margen'] = $cli->margen;
                    }  
                }else{
                    $existe = 0;
                    $cabecera = $this->construirNuevaCabeceraAlbaranCliente($idAlbaran);
                    $detalle = $this->construirNuevoDetalleAlbaranCliente($idAlbaran);
                }
    
                $clientes = $this->modeloCliente->getEnabledClients();
    
                $tmp = [
                    'idAlbaran' => $idAlbaran,
                    'clientes' => $clientes,
                    'existe' => $existe
                ];
                
                $datos = array_merge($tmp, $cabecera, $detalle);          
               
                $this->vista('albaranesCliente/altaAlbaran', $datos);

            }else{        
                redireccionar('/AlbaranesClientes');
            }
            
        }else{        
            redireccionar('/AlbaranesClientes');
        }

    }  

    private function construirCabeceraAlbaranCliente($idAlbaran)
    {
        $datos = $this->modeloAlbaranCliente->getAlbaranData($idAlbaran);        
        return $datos;
    }

    private function construirNuevaCabeceraAlbaranCliente($idAlbaran)
    {                
        $numero = '';
        $fecha = date('Y-m-d');

        $idCliente = false;
        $nifCliente = "";         

        $observaciones = '';    

        $datos =[
            'numero' => $numero,
            'fecha' => $fecha,
            'idcliente' => $idCliente,            
            'nifCliente' => $nifCliente,
            'observaciones' => $observaciones
        ];
        return $datos;
    }
    

    private function construirNuevoDetalleAlbaranCliente($idAlbaran)
    {        
        $productDefault = false;
        $precioDefault = 0;        

        if(SALE_PRODUCT_DEFAULT > 0){
            $productDefault = $this->modeloProductoVenta->getSaleProduct(SALE_PRODUCT_DEFAULT);         
        }

        $productos = $this->modeloProductoVenta->getAllSaleProducts();
        
        $datos = [            
            'productos' => $productos,
            'productDefault' => $productDefault,                
            'precioDefault' => $precioDefault,
            'tiposIva' => $this->modeloIva->getAllIvasActive()
        ];        
        return $datos;
    }

    public function actualizarAlbaranCompleto()
    {                      
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;   

        if(isset($_POST['id']) && $_POST['id'] != '' && $_POST['id'] > 0 && isset($_POST['numeroOrden']) && count($_POST['numeroOrden']) > 0 && isset($_POST['idcliente']) && $_POST['idcliente'] > 0 && $_POST['fecha'] != '' ){
                            
            $albaran = $this->modeloAlbaranCliente->getAlbaranData($_POST['id']);            
            
            $albAntes = new ModeloAlbaranCliente();
            $dataAlbAntes = $albAntes->getAlbaranData($_POST['id']);
            //si tiene factura debe actualizarla
            $idFactura = $this->modeloAlbaranCliente->getInvoiceNumberByDeliveryNoteById($_POST['id']);  
            $dataFactAntes = false;   
            if(!empty($idFactura)){
                $factAmtes = new ModeloFacturaCliente();
                $dataFactAntes = $factAmtes->getInvoiceData($idFactura);
            }

            $arrWhere['id'] = $_POST['id'];  

            if(isset($albaran->numero) && $albaran->numero != "" && $albaran->numero != "0"){
                $existe = 1;
                $arrFieldsUpdate = $this->arrFieldsUpdate;
                
            }else{
                $existe = 0;
                                
                $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla, date("Y",strtotime($_POST['fecha'])));
                $ceros = '';
                if($nextCodeInterno==1 || ($nextCodeInterno > 1 && $nextCodeInterno <= 9)){
                    $ceros = '00';
                }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
                    $ceros = '0';
                }
                $_POST['numerointerno'] = $nextCodeInterno;                
                $_POST['numero'] = "ALB".date("y",strtotime($_POST['fecha'])).".".$ceros.$nextCodeInterno;
                $arrFieldsUpdate = $this->arrFieldsUpdateFirst;
            }
            
            $_POST['cliente'] = $this->modeloCliente->getNameClient($_POST['idcliente']);
            $_POST['total'] = $this->calcularTotalesAlbaran($_POST)['total'];
            $_POST['ivatotal'] = $this->calcularTotalesAlbaran($_POST)['ivatotal'];
            $_POST['baseimponible'] = $this->calcularTotalesAlbaran($_POST)['baseimponible'];
            
            $stringQueries = UtilsHelper::buildStringsUpdateQuery($_POST, $arrFieldsUpdate);
            $ok = $stringQueries['ok'];                        
                 
            $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
            $okw = $stringWhere['ok'];    
                          
            if($ok && $okw){
                $strFieldsValues = $stringQueries['strFieldsValues'];
                $strWhere = $stringWhere['strWhere'];

                $upd = $this->modeloBase->updateRow($this->tabla, $strFieldsValues, $strWhere);

                if($upd){
                    $updRows = $this->guardarFilasProductosAlbaran($existe, $_POST);
                  
                    if($updRows){
                        
                        $respuesta['html'] = $this->construirBodyTablaGrilla($_POST['id'], 'albaran');
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_CREACION;
                        $new = $this->modeloAlbaranCliente->getAlbaranData($_POST['id']);   
                        $respuesta['total'] = $new->total;
                        $respuesta['ivatotal'] = $new->ivatotal;
                        $respuesta['baseimponible'] = $new->baseimponible;
                        $respuesta['numero'] = $new->numero;
                        
                        $this->actualizarTotalesFacturaDesdeFilaAlbaran($_POST['id']);
                        $this->actualizarEstadoFactura($_POST['id']);                     

                        if(API_GOOGLE_DRIVE==1){
                            if(!empty($new->numero) && $new->numero > 0){                            
                                $numAlb = $new->numero;
                                $nombreArchivo = "Albaran_{$numAlb}.pdf";                                                           
                                GoogleDriveUploader::reemplazarArchivo($_POST['id'], $new->fecha, 'Albaranes Cliente', $nombreArchivo, $dataAlbAntes->fecha);        

                                                        
                                if(!empty($idFactura) && $dataFactAntes){                                         
                                    $fact = new ModeloFacturaCliente();
                                    $dataFact = $fact->getInvoiceData($idFactura);
                                    $numFact = $dataFact->numero;
                                    $nombreArchivoFact = "Factura_{$numFact}.pdf";    
                                    GoogleDriveUploader::reemplazarArchivo($idFactura, $dataFact->fecha, 'Facturas Cliente', $nombreArchivoFact, $dataFactAntes->fecha);
                                }
                            }
                        }

                    }                    
                }
            }            

        }else{
            $fieldsValidate = UtilsHelper::validateRequiredFields($_POST, $this->arrFieldsValidate);
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
            $respuesta['fieldsValidate'] = $fieldsValidate;
        } 
        echo json_encode($respuesta);
    } 
    
    private function actualizarEstadoFactura($idAlbaran)
    {
        $idFactura = $this->modeloAlbaranCliente->getInvoiceNumberByDeliveryNoteById($idAlbaran);  
        $totalFactura = $this->modeloFacturaCliente->getTotalAmountInvoice($idFactura);
        $totalRecibos = $this->modeloReciboCliente->getTotalAmountPaidReceiptsByInvoice($idFactura);                      

        $estado = 'pendiente';

        if($totalRecibos == 0){
            $estado = 'pendiente';
        }else if($totalFactura > 0 && $totalRecibos < $totalFactura ){
            $estado = 'cobrada parcial';
        }else if($totalFactura > 0 && $totalRecibos == $totalFactura ){
            $estado = 'cobrada';
        }

      
        
        $this->modeloBase->updateFieldTabla($this->tabla_clientes_facturas, 'estado', $estado, $idFactura);
        
        return $estado;
    }


    public function calcularTotalesAlbaran($post)
    {
        $baseImponible = 0;        
        $ivaTotal = 0;
        foreach ($post['numeroOrden'] as $key => $value) {                                
                        
            $cantidad = ($post['cantidadArticulo'][$key] != '')? str_replace(",", ".", $post['cantidadArticulo'][$key]): 0;            
            $precio = ($post['precioArticulo'][$key] != '')? str_replace(",", ".", $post['precioArticulo'][$key]): 0;
            $ivatipo = ($post['iva'][$key] != '')? $post['iva'][$key]: 0;   
            
            $subTotal = $cantidad * $precio;        

            $baseImponible = $baseImponible + $subTotal;
            $ivaTotal = $ivaTotal + ($ivatipo * $subTotal / 100);
        }
        $retorno = [
            "baseimponible" => $baseImponible,
            "ivatotal" => $ivaTotal,
            "total" => $baseImponible + $ivaTotal
        ];
        return $retorno;
    }
   
    private function guardarFilasProductosAlbaran($existe, $post)
    {

        $retorno = false;
        $cont = 0;   
        $contValid = 0;

        foreach ($post['numeroOrden'] as $key => $value) {

            if(isset($post['idArticulo'][$key])){
                
                $contValid++;

                $tmp = [];            

                $tmp['idalbaran'] = $post['id'];
                $tmp['idproducto'] = $post['idArticulo'][$key];
                $tmp['descripcion'] = $this->modeloProductoVenta->getNameProduct($post['idArticulo'][$key]);
                $tmp['unidad'] = $post['unidadArticulo'][$key];           
                $cantidad = ($post['cantidadArticulo'][$key] != '')? str_replace(",", ".", $post['cantidadArticulo'][$key]): 0;
                $tmp['cantidad'] = $cantidad;
                $precio = ($post['precioArticulo'][$key] != '')? str_replace(",", ".", $post['precioArticulo'][$key]): 0;
                $tmp['precio'] = $precio;
                $tmp['ivatipo'] = ($post['iva'][$key] != '')? $post['iva'][$key]: 0;
                $subTotal = $cantidad * $precio;
                $tmp['subtotal'] = $subTotal;     
                $idFactura = $this->modeloAlbaranCliente->getInvoiceNumberByDeliveryNoteById($post['id']);
                $tmp['idfactura'] = $idFactura;
    
                $arrWhere = [];
                if(isset($post['idFila'][$key])){
                    $arraFields = $this->arrFieldsRowsUpdate;
                    $arrWhere['id'] = $post['idFila'][$key]; 

                    $stringQueries = UtilsHelper::buildStringsUpdateQuery($tmp, $arraFields);
                    $ok = $stringQueries['ok'];                        
                        
                    $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
                    $okw = $stringWhere['ok'];                                           
                                
                    if($ok && $okw){
                        $strFieldsValues = $stringQueries['strFieldsValues'];
                        $strWhere = $stringWhere['strWhere'];                  
    
                        $ins = $this->modeloBase->updateRow($this->tablaRows, $strFieldsValues, $strWhere);
    
                        if($ins){             
                            $cont++;
                            if($idFactura > 0){
                                $this->actualizarFilaFacturaDesdeFilaAlbaran($post['idFila'][$key], $tmp);                                
                            }                                                              
                        }
                    }                    
                }else{

                    $arraFields = $this->arrFieldsRowsCreate; 

                    $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $arraFields);
                                       
                    $ok = $stringQueries['ok'];
                    $strFields = $stringQueries['strFields'];
                    $strValues = $stringQueries['strValues'];
                    if($ok){
                        $ins = $this->modeloBase->insertRow($this->tablaRows, $strFields, $strValues);
                        if($ins){
                            $cont++;
                            if($idFactura > 0){
                                $this->agregarFilaFacturaDesdeFilaAlbaran($ins,$post['id'], $tmp, $idFactura);
                                $this->actualizarTotalesFactura($idFactura);
                            }                    
                        }
                    }                      
                }                                                  
            }
            
        }

        if($cont == $contValid){            
            $retorno = true;
        }
        return $retorno;
       
    }

    private function agregarFilaFacturaDesdeFilaAlbaran($idFilaAlbaran, $idAlbaran, $tmp, $idFactura)
    {
        $retorno = false;
        $cont = 0;            
        $baseImponible = 0;        
        $ivaTotal = 0;
        $total = 0;               
    
        $tmp['idfilaalbaran'] = $idFilaAlbaran; 
        $tmp['idalbaran'] = $idAlbaran;                        
        
        $arraFields = $this->arrFieldsRowsCreateInvoice; 

        $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $arraFields);
        
        $ok = $stringQueries['ok'];
        $strFields = $stringQueries['strFields'];
        $strValues = $stringQueries['strValues'];
        if($ok){
            $insRows = $this->modeloBase->insertRow($this->tabla_clientes_facturas_det, $strFields, $strValues);
        }             
               
    }

    private function actualizarTotalesFacturaDesdeFilaAlbaran($idAlbaran)
    {
        $idFactura = $this->modeloAlbaranCliente->getInvoiceNumberByDeliveryNoteById($idAlbaran);           
        $totales = $this->modeloFacturaDetalleCliente->getTotalsInvoice($idFactura);

        $datos['baseimponible'] = (isset($totales->suma_base_imponible) && $totales->suma_base_imponible > 0)? $totales->suma_base_imponible: 0;
        $datos['ivatotal'] = (isset($totales->suma_iva) && $totales->suma_iva > 0)? $totales->suma_iva: 0;
        $datos['total'] = (isset($totales->total_final) && $totales->total_final > 0)? $totales->total_final: 0;
        $datos['id'] = $idFactura;

        $this->modeloFacturaCliente->updateInvoiceHead($datos);

    }


    private function actualizarFilaFacturaDesdeFilaAlbaran($idFilaAlbaran, $fila)
    {
        $idFilaFactura = $this->modeloFacturaDetalleCliente->getIdRowInvoiceByIdRowDeliveryNotice($idFilaAlbaran);                
        $arraFields = $this->arrFieldsRowsUpdateFactura;
        $arrWhere['id'] = $idFilaFactura; 

        $stringQueries = UtilsHelper::buildStringsUpdateQuery($fila, $arraFields);
        $ok = $stringQueries['ok'];                        
            
        $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
        $okw = $stringWhere['ok'];                                           
                    
        if($ok && $okw){
            $strFieldsValues = $stringQueries['strFieldsValues'];
            $strWhere = $stringWhere['strWhere'];                  

            $upd = $this->modeloBase->updateRow($this->tabla_clientes_facturas_det, $strFieldsValues, $strWhere);    
        }            
        
    }

    private function guardarFilasProductosAlbaranAntes($existe, $post)
    {

        $retorno = false;
        $cont = 0;   
            
        foreach ($post['numeroOrden'] as $key => $value) {
            $tmp = [];            

            $tmp['idalbaran'] = $post['id'];
            $tmp['idproducto'] = $post['idArticulo'][$key];
            $tmp['descripcion'] = $this->modeloProductoVenta->getNameProduct($post['idArticulo'][$key]);
            $tmp['unidad'] = $post['unidadArticulo'][$key];           
            $cantidad = ($post['cantidadArticulo'][$key] != '')? str_replace(",", ".", $post['cantidadArticulo'][$key]): 0;
            $tmp['cantidad'] = $cantidad;
            $precio = ($post['precioArticulo'][$key] != '')? str_replace(",", ".", $post['precioArticulo'][$key]): 0;
            $tmp['precio'] = $precio;
            $tmp['ivatipo'] = ($post['iva'][$key] != '')? $post['iva'][$key]: 0;
            $subTotal = $cantidad * $precio;
            $tmp['subtotal'] = $subTotal;           

            $arraFields = $this->arrFieldsRowsCreate;   
            if($existe == 1){
                $arrWhere['id'] = $post['idFila'][$key];                
                $arraFields = $this->arrFieldsRowsUpdate;
            }                       
            
            if($existe == 1){                               
                
                $stringQueries = UtilsHelper::buildStringsUpdateQuery($tmp, $arraFields);
                $ok = $stringQueries['ok'];                        
                    
                $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
                $okw = $stringWhere['ok'];                                           
                            
                if($ok && $okw){
                    $strFieldsValues = $stringQueries['strFieldsValues'];
                    $strWhere = $stringWhere['strWhere'];                  

                    $ins = $this->modeloBase->updateRow($this->tablaRows, $strFieldsValues, $strWhere);

                    if($ins){
                        $cont++;
                    }
                }

            }else{
                $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $arraFields);
                $ok = $stringQueries['ok'];
                $strFields = $stringQueries['strFields'];
                $strValues = $stringQueries['strValues'];
                if($ok){
                    $ins = $this->modeloBase->insertRow($this->tablaRows, $strFields, $strValues);
                    if($ins){
                        $cont++;
                    }
                }                
            }
                        
        }

        if($cont == count($post['numeroOrden'])){            
            $retorno = true;
        }
        return $retorno;
       
    }
   
    private function construirBodyTablaGrilla($idAlbaran, $tipoDoc){

        $rows = $this->modeloAlbaranDetalleCliente->getRowsAlbaran($idAlbaran);
        $datos = [            
            'productos' => $this->modeloProductoVenta->getAllSaleProducts(),
            'tiposIva' => $this->modeloIva->getAllIvasActive()
        ]; 

        $html = TemplateHelper::buildGridRowsDeliveryNotesSuppliers($rows, $datos, $tipoDoc);
        return $html;
    }

    private function construirBodyTablaGrillaParaCrearFactura($idAlbaran, $tipoDoc)
    {
        $rows = $this->modeloAlbaranDetalleCliente->getRowsAlbaran($idAlbaran);        

        $html = TemplateHelper::buildGridRowsInvoiceFromDeliveryNotes($rows, $tipoDoc);
        return $html;
    }

    public function eliminarAlbaran()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['idAlbaranEliminar']) && $_POST['idAlbaranEliminar'] > 0) {

            $idAlbaran = $_POST['idAlbaranEliminar'];
            $where1 = " id = $idAlbaran ";  
            $where2 = " idalbaran = $idAlbaran ";  

            $modeloAlbaran = $this->modeloAlbaranCliente->getAlbaranData($idAlbaran);

            $filasFactura = $this->modeloFacturaDetalleCliente->getIdFilaAlbaranByIdAlbaran($idAlbaran);
            $idFactura = $this->modeloAlbaranCliente->getInvoiceNumberByDeliveryNoteById($idAlbaran);

            
            $delCabecera = $this->modeloBase->deleteRow($this->tabla, $where1);
            $delFilas = $this->modeloBase->deleteRow($this->tablaRows, $where2);

            if ($delCabecera && $delFilas) {

                if(isset($idFactura) && $idFactura > 0){
                    $this->eliminarFilasFacturaDesdeIdAlbaranEliminado($filasFactura);
                    
                    $this->actualizarTotalesFactura($idFactura);
                    $this->actualizarEstadoFactura($idAlbaran);
                }                

                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;     
                
                //eliminar albaran de Drive
                if(API_GOOGLE_DRIVE==1){
                    $numAlbaran = $modeloAlbaran->numero;
                    $nombreArchivo = "Albaran_{$numAlbaran}.pdf";                
                    GoogleDriveUploader::eliminarArchivo($modeloAlbaran->fecha, 'Albaranes Cliente', $nombreArchivo);
                }
            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }

    private function eliminarFilasFacturaDesdeIdAlbaranEliminado($filasFactura)
    {
        if($filasFactura && count($filasFactura) > 0){
            $ides = [];
            foreach ($filasFactura as $key) {
                $ides[] = $key->idfilaalbaran;
            }
            $str = implode(",", $ides);
            $this->modeloBase->deleteRow($this->tabla_clientes_facturas_det, " idfilaalbaran IN ($str)  ");
        }
    }
    
    public function obtenerDatosAlbaran()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idAlbaran = $this->fetch['id'];               
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['cabecera'] = $this->construirCabeceraAlbaranCliente($idAlbaran);
            $respuesta['detalle'] = $this->construirBodyTablaGrillaParaCrearFactura($idAlbaran, 'factura_cliente');
            $respuesta['dias_cobro'] = $this->modeloCondiciones->getPaymentConditions();
            $respuesta['cuentas_bancarias'] = $this->modeloCuentasBancarias->getBankAccounts();
            $respuesta['formas_pago'] = $this->modeloFormasPago->getPaymentForms();            
            $respuesta['forma_pago_cliente'] = $this->modeloCliente->getFormaCobroClientById($this->fetch['idcliente']);
            
            
        }                
        echo json_encode($respuesta);
    }

    public function obtenerClienteYPrecioClienteZona()
    {  
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   
            
        $precioVentaFinal = 0;
        $respuesta['margen'] = 0;
        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idCliente = $this->fetch['id'];
            $getDatosCliente = $this->modeloCliente->getClientById($idCliente);
            
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['datos'] = $getDatosCliente;
           
            $fecha = ($this->fetch['fecha'] != '' && $this->fetch['fecha'] > 0)? $this->fetch['fecha']: date("Y-m-d");
            
            $precioVentaFinal = 0;
            
            if(isset($this->fetch['productos']) && count($this->fetch['productos']) > 0){                
                if(SALE_PRODUCT_DEFAULT > 0 && in_array(SALE_PRODUCT_DEFAULT, $this->fetch['productos'])){
                    $idProductoVenta = SALE_PRODUCT_DEFAULT;
                    //$precioVentaFinal = $this->calcularPrecioVentaCliente($idProductoVenta, $idCliente, $fecha);
                }                
            }      
            $respuesta['margen'] = $this->modeloCliente->getClientZoneProfitMargin($idCliente)->margen;
        }                 
        $respuesta['precioVenta'] = $precioVentaFinal;  
        print_r(json_encode($respuesta));
    }

    public function calcularPrecioVentaCliente($idProductoVenta, $idCliente, $fecha)
    {                        
        $productoVenta = $this->modeloProductoVenta->getSaleProductData($idProductoVenta);
        
        $productoCompra = $productoVenta->idproductocompra;

        $precioCompraUnidadCompra = $this->modeloPlanificacion->getProductPurchasePriceByPlanningDateNotFormat($productoCompra, $fecha);                             
        
        $precioVentaUnidadVenta = $precioCompraUnidadCompra * $productoVenta->equivalencia;

        $clienteData = $this->modeloCliente->getClientZoneProfitMargin($idCliente);
        if(isset($clienteData->margen) && $clienteData->margen > 0){           
            $precioVentaUnidadVenta = $precioVentaUnidadVenta * (1 + $clienteData->margen/100);
        }                
        return $precioVentaUnidadVenta;
    }

    public function exportarPdfFactura($idAlbaran)
    {                       
        $datos['cabecera'] = $this->modeloAlbaranCliente->getAlbaranDataDocumento($idAlbaran);
        $datos['detalle'] = $this->modeloAlbaranDetalleCliente->getRowsAlbaran($idAlbaran);        
        $datos['tipo'] = 'albarà';
        $datos['razonsocialpiensos'] = $this->modeloConfiguracion->getBusinessName();

        generarPdf::documentoPDFExportar('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0), true, 'documentos', 'albaran.php', $datos);
    }



    public function obtenerDatosParaFilaNueva()
    {                               
        $productoDefault = false;
        if(SALE_PRODUCT_DEFAULT > 0){
            $productoDefault = $this->modeloProductoVenta->getSaleProduct(SALE_PRODUCT_DEFAULT);
        }
        $retorno = [        
            'productos' => $this->modeloProductoVenta->getAllSaleProducts(),
            'tiposIva' => $this->modeloIva->getAllIvasActive(),
            'default' => $productoDefault
        ]; 
        
        echo json_encode($retorno);  
    }

    public function eliminarFilaDetalle()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($this->fetch['idFila']) && $this->fetch['idFila'] > 0 && $this->fetch['idAlbaran'] > 0) {

            $idFilaAlbaran = $this->fetch['idFila'];            
            $idFilaFactElimina = $this->modeloFacturaDetalleCliente->getIdRowInvoiceByIdRowDeliveryNotice($idFilaAlbaran);
            $idFactura = $this->modeloFacturaDetalleCliente->getIdInvoiceByIdRowDeliveryNotice($idFilaAlbaran);

            $where = " id = $idFilaAlbaran ";                        
            $updFilas = $this->modeloBase->deleteRow($this->tablaRows, $where);

            if ($updFilas) {
                $this->actualizarTotalesAlbaran($this->fetch['idAlbaran']);                               
                
                if($idFilaFactElimina > 0){
                    
                    $this->modeloBase->deleteRow($this->tabla_clientes_facturas_det, "id = ".$idFilaFactElimina);                    
                    $this->actualizarTotalesFactura($idFactura);
                    $this->actualizarEstadoFactura($this->fetch['idAlbaran']);
                }                

                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;       
                $respuesta['datos'] = $this->modeloAlbaranDetalleCliente->getTotalsAlbaranFormat($this->fetch['idAlbaran']);

            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }

    

    public function actualizarTotalesAlbaran($idAlbaran)
    {
        $totales = $this->modeloAlbaranDetalleCliente->getTotalsAlbaran($idAlbaran);

        $arrFieldsValues['baseimponible'] = $totales->suma_base_imponible;
        $arrFieldsValues['ivatotal'] = $totales->suma_iva;
        $arrFieldsValues['total'] = $totales->total_final;
        $fieldsValuesString = UtilsHelper::buildStringsFieldsUpdateQuery($arrFieldsValues);
       
        $arrWhere['id'] = $idAlbaran;
        $whereString = UtilsHelper::buildStringsWhereQueryOnly($arrWhere);   
        $upd = $this->modeloBase->updateRow($this->tabla, $fieldsValuesString, $whereString);
    }

    public function actualizarTotalesFactura($idFactura)
    {
     

        $totales = $this->modeloFacturaDetalleCliente->getTotalsInvoice($idFactura);

        

        $arrFieldsValues['baseimponible'] = (isset($totales->suma_base_imponible) && $totales->suma_base_imponible > 0)? $totales->suma_base_imponible:0;
        $arrFieldsValues['ivatotal'] = (isset($totales->suma_iva) && $totales->suma_iva > 0)? $totales->suma_iva: 0;
        $arrFieldsValues['total'] = (isset($totales->total_final) && $totales->total_final > 0)? $totales->total_final : 0;

        
        $fieldsValuesString = UtilsHelper::buildStringsFieldsUpdateQuery($arrFieldsValues);
       
        $arrWhere['id'] = $idFactura;
        $whereString = UtilsHelper::buildStringsWhereQueryOnly($arrWhere);   
        $upd = $this->modeloBase->updateRow($this->tabla_clientes_facturas, $fieldsValuesString, $whereString);
    }

    public function obtenerDatosEnvioEmailAlbaran()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idAlbaran = $this->fetch['id'];
            
            $cabecera = $this->construirCabeceraAlbaranCliente($idAlbaran);
            $respuesta['cabecera'] = $cabecera;            
            $respuesta['detalle'] = $this->construirBodyTablaGrilla($idAlbaran, 'factura');
            $respuesta['dias_cobro'] = $this->modeloCondiciones->getPaymentConditions();
            $respuesta['cuentas_bancarias'] = $this->modeloCuentasBancarias->getBankAccounts();
            $respuesta['formas_pago'] = $this->modeloFormasPago->getPaymentForms();

            // Agregar los contactos del cliente
            $idCliente = $cabecera->idcliente;
            $respuesta['contactos'] = json_decode($this->modeloCliente->getClientById($idCliente)->contactos);
            

            if(isset($cabecera->id) && $cabecera->id > 0 && $cabecera->numero != "" && $cabecera->idcliente > 0 ){
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';
            }
        }                
        echo json_encode($respuesta);
    }
    
    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);          
            
            $where = str_replace("fecha like", "DATE_FORMAT(fecha, '%d/%m/%Y') like", $where);        

            $order = " ORDER BY fecha ASC ";          
         
            $datos = $this->modeloAlbaranCliente->obtenerAlbaranesClientesExportar($order,$where);
            $nombreReporte = '_AlbaranesClientes';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }

    public function obtenerProductoPrecioPorCliente()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   
               
        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            
            $idProducto = $this->fetch['id'];
            $datosProducto = $this->modeloProductoVenta->getSaleProduct($idProducto);
            $precio = 0;
            if(isset($this->fetch['idcliente']) && $this->fetch['idcliente'] > 0 && $idProducto==SALE_PRODUCT_DEFAULT){
                $precio = $this->modeloCliente->getPriceByClientId($this->fetch['idcliente']);
            }
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['datos'] = $datosProducto;
            $respuesta['precio'] = $precio;
        }

        print_r(json_encode($respuesta));
    }

    public function verificarSiAlbaranSePuedeEliminar()
    {
        $respuesta['error'] = false;
        
        if(isset($this->fetch) && $this->fetch['idAlbaran'] > 0) {                        

            $idAlbaran = $this->fetch['idAlbaran'];
            $albaran = $this->modeloAlbaranCliente->getAlbaranData($idAlbaran);
        
                        
            if(!isset($albaran->numerointerno) || $albaran->numerointerno==0 || $albaran->numerointerno=='' ){
                $this->modeloAlbaranCliente->deleteDeliveryNotice($idAlbaran);            
            }
        }
        echo json_encode($respuesta);
    }



}
