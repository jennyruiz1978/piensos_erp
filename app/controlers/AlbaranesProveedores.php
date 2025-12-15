<?php

class AlbaranesProveedores extends Controlador {

    private $arrFields;
    private $tabla;
    private $fetch;

    public function __construct() {

        
        session_start();      
        $this->tabla = 'proveedores_albaranes';
        $this->arrFieldsCreate = ['numero'];
        $this->arrFieldsUpdateFirst = ['numerointerno','numero','idproveedor','proveedor','fecha','observaciones','total','ivatotal','baseimponible'];
        $this->arrFieldsUpdate = [/*'numero',*/'idproveedor','proveedor','fecha','observaciones','total','ivatotal','baseimponible'];
        $this->arrFieldsValidate = ['numero','idproveedor','fecha'];
        $this->arrFieldsUpdateCustomOne = ['fecha','total','ivatotal','baseimponible'];
        $this->tablaRows = 'proveedores_albaranes_det';
        $this->arrFieldsRowsCreate = ['idalbaran','idproducto','descripcion','unidad','cantidad','precio','ivatipo','subtotal','idfactura'];
        $this->arrFieldsRowsUpdate = ['idproducto','descripcion','unidad','cantidad','precio', 'ivatipo','subtotal'];
        
        $this->tabla_proveedores_albaranes = 'proveedores_albaranes';
        $this->tabla_planificaciones = 'planificaciones';
        $this->tabla_planificaciones_fechas = 'planificaciones_fechas';

        $this->modeloAlbaranProveedor = $this->modelo('ModeloAlbaranProveedor');

        $this->modeloAlbaranDetalleProveedor = $this->modelo('ModeloAlbaranDetalleProveedor');

        $this->modeloProveedor = $this->modelo('ModeloProveedor');             


        $this->modeloProductoCompra = $this->modelo('ModeloProductoCompra');
        
        $this->modeloBase = $this->modelo('ModeloBase');
        $this->modeloIva = $this->modelo('ModeloIva');
        //$this->modeloPlanificacion = $this->modelo('ModeloPlanificacion');
        $this->modeloFacturaProveedor = $this->modelo('ModeloFacturaProveedor');   
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');

        $this->modeloPlanificacionFechas = $this->modelo('ModeloPlanificacionFechas');
        $this->modeloCliente = $this->modelo('ModeloCliente');

   
        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        }       
    
        
    }

    public function index() {
        $datos = [];
        $this->vista('albaranesProveedor/albaranes', $datos);
    }
    
    public function tablaAlbaranesProveedor()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY alb.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                                 

        $where = str_replace("id like", "alb.id like", $where);  
        $where = str_replace("numero like", "alb.numero like", $where);  
        $where = str_replace("proveedor like", "alb.proveedor like", $where);  
        $where = str_replace("fecha like", "DATE_FORMAT(alb.fecha, '%d/%m/%Y') like", $where);  
        $where = str_replace("total like", "alb.total like", $where);  
        $where = str_replace("estado like", "alb.estado like", $where);  
        $where = str_replace("numerofactura like", "fac.numero like", $where);                   
        $where = str_replace("nom_cli like", "planif_cliente.nom_cli like", $where);  
        
        $albaranes = $this->modeloAlbaranProveedor->obtenerAlbaranesProveedoresTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloAlbaranProveedor->obtenerTotalAlbaranesProveedor($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $albaranes
        ];

        print_r(json_encode($salida));
    }      

    public function altaAlbaranes()
    {        
        $idAlbaran = $this->crearAlbaran();    
        
        if($idAlbaran && $idAlbaran >0){
            redireccionar('/AlbaranesProveedores/verAlbaran/'.$idAlbaran);
        }else{
            redireccionar('/AlbaranesProveedores');
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
    
    private function construirCabeceraAlbaranProveedor($idAlbaran)
    {
        $datos = $this->modeloAlbaranProveedor->getAlbaranData($idAlbaran);        
        return $datos;
    }

    private function construirDetalleAlbaranProveedor($idAlbaran)
    {
        $rows = $this->modeloAlbaranDetalleProveedor->getRowsAlbaran($idAlbaran);       

        $productos = $this->modeloProductoCompra->getAllPurchaseProducts();
        
        $datos = [            
            'rows'=> $rows,
            'productos' => $productos,
            'tiposIva' => $this->modeloIva->getAllIvasActive()
        ];        
        return $datos;
    }    

    private function construirNuevaCabeceraAlbaranProveedor($idAlbaran)
    {                
        $numero = '';
        $fecha = date('Y-m-d');

        $idProveedor = false;
        $nifProveedor = "";
        $idProveedorDefault = $this->modeloConfiguracion->getIdTransportistaDefault();
        if ($idProveedorDefault > 0){
            $idProveedor = $idProveedorDefault;
            $nifProveedor = $this->modeloProveedor->getNifSupplier($idProveedorDefault);
        }          

        $observaciones = '';    

        $datos =[
            'numero' => $numero,
            'fecha' => $fecha,
            'idproveedor' => $idProveedor,            
            'nifProveedor' => $nifProveedor,
            'observaciones' => $observaciones
        ];
        return $datos;
    }

    private function construirNuevoDetalleAlbaranProveedor($idAlbaran)
    {        
        $productDefault = false;
        $precioDefault = 0;        

        if(PURCHASE_PRODUCT_DEFAULT > 0){
            $productDefault = $this->modeloProductoCompra->getPurchaseProduct(PURCHASE_PRODUCT_DEFAULT);
                   
        }

        $productos = $this->modeloProductoCompra->getAllPurchaseProducts();
        
        $datos = [            
            'productos' => $productos,
            'productDefault' => $productDefault,                
            'precioDefault' => $precioDefault,
            'tiposIva' => $this->modeloIva->getAllIvasActive()
        ];        
        return $datos;
    }

    public function verAlbaran($idAlbaran)
    {            
   
        if(isset($idAlbaran) && $idAlbaran > 0){

            if($this->modeloBase->existIdInvoice($this->tabla, $idAlbaran) > 0){
            
                $albaran = $this->modeloAlbaranProveedor->getAlbaranData($idAlbaran);
                if(isset($albaran->numero) && $albaran->numero != "" && $albaran->numero != "0"){
                    $existe = 1;
                    $cab = $this->construirCabeceraAlbaranProveedor($idAlbaran);
                    $cabecera = (array) $cab;
                    $nifProveedor = $this->modeloProveedor->getNifSupplier($cab->idproveedor);
                    $cabecera['nifProveedor'] = $nifProveedor;                
                    $detalle['html'] = $this->construirBodyTablaGrilla($idAlbaran, 'albaran');  
                }else{
                    $existe = 0;
                    $cabecera = $this->construirNuevaCabeceraAlbaranProveedor($idAlbaran);
                    $detalle = $this->construirNuevoDetalleAlbaranProveedor($idAlbaran);
                }
    
                $proveedores = $this->modeloProveedor->getEnabledSuppliers();

                $idFactura = $albaran->idfactura;
                $numFactura = (isset($idFactura) && $idFactura > 0)? ($this->modeloFacturaProveedor->getInvoiceNumber($idFactura)): '';

                $clienteDescarga = $this->obtenerClienteDescarga($idAlbaran);                          
    
                $tmp = [
                    'idAlbaran' => $idAlbaran,
                    'proveedores' => $proveedores,
                    'existe' => $existe,
                    'numFactura' => $numFactura,
                    'cliente_descarga' => $clienteDescarga
                ];
                
                $datos = array_merge($tmp, $cabecera, $detalle);     
              
               
                $this->vista('albaranesProveedor/altaAlbaran', $datos);    

            }else{        
                redireccionar('/AlbaranesProveedores');
            }
            
        }else{        
            redireccionar('/AlbaranesProveedores');
        }

    }  

    private function obtenerClienteDescarga($idAlbaran)
    {
        $clienteDescarga = '';
        $rows = $this->modeloAlbaranDetalleProveedor->getRowsAlbaran($idAlbaran);
        if(isset($rows) && count($rows) > 0){

            $idplanfecha = $rows[0]->idplanfecha;

            if(isset($idplanfecha) && $idplanfecha > 0){
                $idCliente = $this->modeloPlanificacionFechas->getDatesPlanningById($idplanfecha)->idcliente;
                $clienteDescarga =  $this->modeloCliente->getClientById($idCliente)->nombrefiscal;
            }

        }
        return $clienteDescarga;
          
    }

    public function actualizarAlbaranCompleto()
    {                      
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;    

        if(isset($_POST['id']) && $_POST['id'] != '' && $_POST['id'] > 0 && isset($_POST['numeroOrden']) && count($_POST['numeroOrden']) > 0 && isset($_POST['idproveedor']) && $_POST['idproveedor'] > 0 && $_POST['fecha'] != '' ){
                            
            $albaran = $this->modeloAlbaranProveedor->getAlbaranData($_POST['id']);            
            
            $arrWhere['id'] = $_POST['id'];  

            if(isset($albaran->numero) && $albaran->numero != "" && $albaran->numero != "0"){
                $existe = 1;
                $arrFieldsUpdate = $this->arrFieldsUpdate;
                
            }else{
                $existe = 0;
                            
                $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla_proveedores_albaranes, date("Y",strtotime($_POST['fecha'])));
                $_POST['numerointerno'] = $nextCodeInterno;    
                                                                 
                $ceros = '';
                if($nextCodeInterno==1 || ($nextCodeInterno > 1 && $nextCodeInterno <= 9)){
                    $ceros = '00';
                }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
                    $ceros = '0';
                }
                $_POST['numero'] = "ALB".date("y",strtotime($_POST['fecha'])).".".$ceros.$nextCodeInterno;
                
                $arrFieldsUpdate = $this->arrFieldsUpdateFirst;
            }
                        
            $_POST['proveedor'] = $this->modeloProveedor->getNameSupplier($_POST['idproveedor']);
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
                        $new = $this->modeloAlbaranProveedor->getAlbaranData($_POST['id']);                                                
                        
                        $this->actualizarAlbaranFabricaOAlbaranTransportista($_POST['id'], $new);
                        
                        $respuesta['total'] = $new->total;
                        $respuesta['ivatotal'] = $new->ivatotal;
                        $respuesta['baseimponible'] = $new->baseimponible;
                        $respuesta['numero'] = $new->numero;
                     
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


    private function actualizarCabeceraYDetallesAlbaran($s, $key, $dataAlbaranOrigen)
    {
        if($s){                            
        
            $idAlbaranDet = $s->id;
            $idAlbaranDestino = $s->idalbaran;     

            if($dataAlbaranOrigen->albaranfabrica==0){
                $precio = $this->modeloConfiguracion->getPriceFactorySupplierDefault();
            }else{                             
                $precio = $this->modeloAlbaranDetalleProveedor->getPriceRowByIdAlbaranDet($s->id);        
            }                              
                                                                
            $updRow = $this->actualizarAlbaranFabricaDesdeAlbaranTransportista($idAlbaranDet, $key, $precio);
            if($updRow){
                $updHeader = $this->actualizarCabeceraAlbaranFabricaDesdeAlbaranTransportista($idAlbaranDestino, $dataAlbaranOrigen);
            }
        }
    }

    private function actualizarAlbaranFabricaOAlbaranTransportista($idAlbaranOrigen, $dataAlbaranOrigen)
    {

        $r = $this->modeloAlbaranDetalleProveedor->getRowsAlbaran($idAlbaranOrigen);
        
        if($dataAlbaranOrigen->albaranfabrica==0){                                

            if($r){

                foreach ($r as $key) {
                    if(isset($key->idplanfecha) && $key->idplanfecha > 0){

                        $idPlanFecha = $key->idplanfecha;
                        $s = $this->modeloAlbaranDetalleProveedor->getDataAlbaranDetailFactoryDeliveryNoticeByIdPlanDate($idPlanFecha);
                                                
                        $this->actualizarCabeceraYDetallesAlbaran($s, $key, $dataAlbaranOrigen);       

                        $this->actualizarPlanificacion($key->idplanfecha, $key->cantidad);               
                        
                    }    
                }
                
            }

           
        }else if($dataAlbaranOrigen->albaranfabrica==1){                        

            if($r){

                foreach ($r as $key) {
                         
                    if(isset($key->idplanfechafab) && $key->idplanfechafab > 0){

                        $idPlanFechafab = $key->idplanfechafab;
                        $s = $this->modeloAlbaranDetalleProveedor->getDataAlbaranDetailNoticeByIdPlanDate($idPlanFechafab);

                        
                        $this->actualizarCabeceraYDetallesAlbaran($s, $key, $dataAlbaranOrigen);     
                        
                        $this->actualizarPlanificacion($key->idplanfechafab, $key->cantidad);         
                    }
                }            
            }
   
        }
      
    }

    private function actualizarPlanificacion($idplanfecha, $cantidadAlbaran)
    {              
        $this->modeloBase->updateFieldTabla($this->tabla_planificaciones_fechas, 'carga', $cantidadAlbaran, $idplanfecha);
        
       


        $idPlanificacion = $this->modeloPlanificacionFechas->getDatesPlanningById($idplanfecha)->idplanificacion;
        $totalPlanificacion = $this->modeloPlanificacionFechas->sumTotalPlanning($idPlanificacion);

        $this->modeloBase->updateFieldTabla($this->tabla_planificaciones, 'total', $totalPlanificacion, $idPlanificacion);
    }

    private function actualizarAlbaranFabricaDesdeAlbaranTransportista($idAlbaranDet, $detalleAlbaranOrigen, $precio)
    {
        $arraFields = $this->arrFieldsRowsUpdate;
              
        $tmp['idproducto'] = $detalleAlbaranOrigen->idproducto;
        $tmp['descripcion'] = $this->modeloProductoCompra->getNameProduct($detalleAlbaranOrigen->idproducto);
        $tmp['unidad'] = $detalleAlbaranOrigen->unidad;                   
        $tmp['cantidad'] = $detalleAlbaranOrigen->cantidad;
        $tmp['ivatipo'] = $detalleAlbaranOrigen->ivatipo;       
        
        $tmp['precio'] = $precio;
        $subTotal = $detalleAlbaranOrigen->cantidad * $precio;
        $tmp['subtotal'] = $subTotal;        

        $arrWhere['id'] = $idAlbaranDet;   

        $stringQueries = UtilsHelper::buildStringsUpdateQuery($tmp, $arraFields);
        $ok = $stringQueries['ok'];                        
            
        $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
        $okw = $stringWhere['ok'];                                           
            
        $upd=false;
        if($ok && $okw){
            $strFieldsValues = $stringQueries['strFieldsValues'];
            $strWhere = $stringWhere['strWhere'];                  
            $upd = $this->modeloBase->updateRow($this->tablaRows, $strFieldsValues, $strWhere);
        }
        return $upd;
    }

    private function actualizarCabeceraAlbaranFabricaDesdeAlbaranTransportista($idAlbaran, $cabeceraAlbaranOrigen){

        $arrFieldsUpdate = $this->arrFieldsUpdateCustomOne;        
        
        $totales = $this->modeloAlbaranDetalleProveedor->getTotalsRowNoticeDelivery($idAlbaran);
          
        $datos['fecha'] = $cabeceraAlbaranOrigen->fecha;
        $datos['total'] = (isset($totales->suma_total) && $totales->suma_total > 0)? $totales->suma_total: 0;
        $datos['ivatotal'] = (isset($totales->importe_iva) && $totales->importe_iva > 0)? $totales->importe_iva: 0;
        $datos['baseimponible'] = (isset($totales->base_imponible) && $totales->base_imponible > 0)? $totales->base_imponible: 0;
        
        $stringQueries = UtilsHelper::buildStringsUpdateQuery($datos, $arrFieldsUpdate);
        $ok = $stringQueries['ok'];                        
            
        $arrWhere['id'] = $idAlbaran;   

        $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
        $okw = $stringWhere['ok'];    
                    
        if($ok && $okw){
            $strFieldsValues = $stringQueries['strFieldsValues'];
            $strWhere = $stringWhere['strWhere'];

            $upd = $this->modeloBase->updateRow($this->tabla, $strFieldsValues, $strWhere);
        }

    }

    private function construirBodyTablaGrilla($idAlbaran, $tipoDoc){

        $rows = $this->modeloAlbaranDetalleProveedor->getRowsAlbaran($idAlbaran);
        $datos = [            
            'productos' => $this->modeloProductoCompra->getAllPurchaseProducts(),
            'tiposIva' => $this->modeloIva->getAllIvasActive()
        ]; 

        $html = TemplateHelper::buildGridRowsDeliveryNotesSuppliers($rows, $datos, $tipoDoc);
        return $html;
    }

    private function construirBodyTablaGrillaParaCrearFactura($idAlbaran, $tipoDoc)
    {
        $rows = $this->modeloAlbaranDetalleProveedor->getRowsAlbaran($idAlbaran);        
        $html = TemplateHelper::buildGridRowsInvoiceFromDeliveryNotes($rows, $tipoDoc);
        return $html;
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
                $tmp['descripcion'] = $this->modeloProductoCompra->getNameProduct($post['idArticulo'][$key]);
                $tmp['unidad'] = $post['unidadArticulo'][$key];           
                $cantidad = ($post['cantidadArticulo'][$key] != '')? str_replace(",", ".", $post['cantidadArticulo'][$key]): 0;
                $tmp['cantidad'] = $cantidad;
                $precio = ($post['precioArticulo'][$key] != '')? str_replace(",", ".", $post['precioArticulo'][$key]): 0;
                $tmp['precio'] = $precio;
                $tmp['ivatipo'] = ($post['iva'][$key] != '')? $post['iva'][$key]: 0;
                $subTotal = $cantidad * $precio;
                $tmp['subtotal'] = $subTotal;       
                $tmp['idfactura'] = $this->modeloAlbaranProveedor->getInvoiceNumberByDeliveryNoteById($post['id']);
          
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

    private function guardarFilasProductosAlbaranAntes($existe, $post)
    {

        $retorno = false;
        $cont = 0;
        
        foreach ($post['numeroOrden'] as $key => $value) {
            $tmp = [];            

            $tmp['idalbaran'] = $post['id'];
            $tmp['idproducto'] = $post['idArticulo'][$key];
            $tmp['descripcion'] = $this->modeloProductoCompra->getNameProduct($post['idArticulo'][$key]);
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

    public function getNextNumberCodeDocument($field){
        
        $query = "SELECT MAX($field) AS maximo FROM proveedores_albaranes";
        $x = $this->modeloBase->max($query);
        return $x;
        
    }

    public function eliminarAlbaran()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['idAlbaranEliminar']) && $_POST['idAlbaranEliminar'] > 0) {

            $idAlbaran = $_POST['idAlbaranEliminar'];
            $where1 = " id = $idAlbaran ";  
            $where2 = " idalbaran = $idAlbaran ";  
            
            $updCabecera = $this->modeloBase->deleteRow($this->tabla, $where1);
            $updFilas = $this->modeloBase->deleteRow($this->tablaRows, $where2);

            if ($updCabecera && $updFilas) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }

    public function obtenerDatosAlbaran()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idAlbaran = $this->fetch['id'];               
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['cabecera'] = $this->construirCabeceraAlbaranProveedor($idAlbaran);            
            $respuesta['detalle'] = $this->construirBodyTablaGrillaParaCrearFactura($idAlbaran, 'factura_proveedor');
        }                
        echo json_encode($respuesta);        
    }

    public function obtenerDatosParaFilaNueva()
    {                               
        $productoDefault = false;
        if(PURCHASE_PRODUCT_DEFAULT > 0){
            $productoDefault = $this->modeloProductoCompra->getPurchaseProduct(PURCHASE_PRODUCT_DEFAULT);
        }
        $retorno = [        
            'productos' => $this->modeloProductoCompra->getAllPurchaseProducts(),
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
            $where = " id = $idFilaAlbaran ";    

            $idplanfecha = $this->fetch['idFila'];

            $updFilas = $this->modeloBase->deleteRow($this->tablaRows, $where);

            if ($updFilas) {
                $this->actualizarTotalesAlbaran($this->fetch['idAlbaran']);
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;       
                $respuesta['datos'] = $this->modeloAlbaranDetalleProveedor->getTotalsAlbaranFormat($this->fetch['idAlbaran']);

            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }

    public function actualizarTotalesAlbaran($idAlbaran)
    {
        $totales = $this->modeloAlbaranDetalleProveedor->getTotalsAlbaran($idAlbaran);

        $arrFieldsValues['baseimponible'] = $totales->suma_base_imponible;
        $arrFieldsValues['ivatotal'] = $totales->suma_iva;
        $arrFieldsValues['total'] = $totales->total_final;
        $fieldsValuesString = UtilsHelper::buildStringsFieldsUpdateQuery($arrFieldsValues);
       
        $arrWhere['id'] = $idAlbaran;
        $whereString = UtilsHelper::buildStringsWhereQueryOnly($arrWhere);   
        $upd = $this->modeloBase->updateRow($this->tabla, $fieldsValuesString, $whereString);
    }
   
    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);          
            
            $where = str_replace("id like", "alb.id like", $where);  
            $where = str_replace("numero like", "alb.numero like", $where);  
            $where = str_replace("proveedor like", "alb.proveedor like", $where);  
            $where = str_replace("fecha like", "DATE_FORMAT(alb.fecha, '%d/%m/%Y') like", $where);  
            $where = str_replace("total like", "alb.total like", $where);  
            $where = str_replace("estado like", "alb.estado like", $where);  
            $where = str_replace("numerofactura like", "fac.numero like", $where);    
            $where = str_replace("nom_cli like", "planif_cliente.nom_cli like", $where);  

            $order = " ORDER BY alb.fecha ASC ";          
         
            $datos = $this->modeloAlbaranProveedor->obtenerAlbaranesProveedores($order,$where);
            $nombreReporte = '_AlbaranesProveedores';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }

    
    public function verificarSiAlbaranSePuedeEliminar()
    {
        $respuesta['error'] = false;
        
        if(isset($this->fetch) && $this->fetch['idAlbaran'] > 0) {                        

            $idAlbaran = $this->fetch['idAlbaran'];
            $albaran = $this->modeloAlbaranProveedor->getAlbaranData($idAlbaran);
        
                        
            if(!isset($albaran->numerointerno) || $albaran->numerointerno==0 || $albaran->numerointerno=='' ){
                $this->modeloAlbaranProveedor->deleteDeliveryNotice($idAlbaran);            
            }
        }
        echo json_encode($respuesta);
    }
    
}
