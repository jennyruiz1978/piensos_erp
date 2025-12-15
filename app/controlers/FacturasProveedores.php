<?php

class FacturasProveedores extends Controlador {

    private $baseImponible;    
    private $ivaTotal;
    private $total;
    private $precio;
    private $cantidad;
    private $fetch;
    private $retencionTipo;
    private $retencionImporte;
    
    public function __construct() {
        session_start();        
        $this->tabla = 'proveedores_facturas';
        $this->tablaRows = 'proveedores_facturas_det';
        $this->tabla_proveedores_albaranes = 'proveedores_albaranes';
        $this->tabla_proveedores_albaranes_det = 'proveedores_albaranes_det';
        $this->arrFieldsCreate = ['numero','idproveedor','proveedor','fecha','baseimponible','ivatotal','total','observaciones','diaspago','vencimiento'];                
        $this->arrFieldsRowsCreate = ['idproducto','idfactura','descripcion','unidad','cantidad','precio','ivatipo','subtotal','idfilaalbaran','idalbaran'];
        $this->modeloFacturaProveedor = $this->modelo('ModeloFacturaProveedor');    
        $this->modeloAlbaranProveedor = $this->modelo('ModeloAlbaranProveedor');
        $this->modeloAlbaranDetalleProveedor = $this->modelo('ModeloAlbaranDetalleProveedor');            
        $this->modeloFacturaDetalleProveedor = $this->modelo('ModeloFacturaDetalleProveedor');            
        $this->modeloBase = $this->modelo('ModeloBase');         
        $this->modeloIva = $this->modelo('ModeloIva');
        $this->modeloProveedor = $this->modelo('ModeloProveedor');
        $this->modeloReciboProveedor = $this->modelo('ModeloReciboProveedor');                
        $this->arrFieldsUpdate = ['idproveedor','proveedor','fecha','numero','diaspago','observaciones','total','ivatotal','baseimponible','vencimiento','retenciontipo','retencionimporte'];
        $this->arrFieldsRowsUpdate = ['descripcion','unidad','cantidad','precio','ivatipo','subtotal'];        
        $this->arrFieldsValidate = ['numfactura','proveedor','nif','fecha','diaspago','vencimiento'];
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');

        $this->modeloPlanificacionFechas = $this->modelo('ModeloPlanificacionFechas');
        $this->modeloCliente = $this->modelo('ModeloCliente');
        $this->tablaProveedoresRecibos = 'proveedores_recibos';


        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        }   
    }

    public function index() {
        $datos = [];
        $this->vista('facturasProveedor/facturas', $datos);
    }

    public function tablaFacturasProveedor()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);       
        
        $where = str_replace("fecha like", "DATE_FORMAT(fac.fecha, '%d/%m/%Y') like", $where); 
        $where = str_replace("nom_cli like", "planif_cliente.nom_cli like", $where);    
        
        $facturas = $this->modeloFacturaProveedor->obtenerFacturasProveedoresTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloFacturaProveedor->obtenerTotalFacturasProveedor($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }      

    public function crearFacturaProveedor()
    {   
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;    

        $estado = $this->modeloAlbaranProveedor->getAlbaranStatus($_POST['idAlbaranProveedor']);
        
        if(isset($estado) && $estado == 'facturado'){

            $respuesta['error'] = true;
            $respuesta['mensaje'] = ALBARAN_FACTURADO; 

        }else{
        
            if(isset($_POST['idAlbaranProveedor']) && $_POST['idAlbaranProveedor'] != '' && $_POST['idAlbaranProveedor'] > 0 && $_POST['fecha_factura_proveedor'] != '' && trim($_POST['fecha_factura_proveedor']) != ''  && isset($_POST['numeroOrden']) && count($_POST['numeroOrden']) > 0 && trim($_POST['numero_factura_proveedor']) != '' && $_POST['vencimiento'] != '' && trim($_POST['dias_albaran_proveedor']) != ''){

                $filasAlbaran = $this->modeloAlbaranDetalleProveedor->getRowsAlbaran($_POST['idAlbaranProveedor']);

                if($filasAlbaran && count($filasAlbaran) > 0){

                    $ins = $this->crearCabeceraFacturaDesdeAlbaran($_POST);
                    if($ins){                        
                        $insRows = $this->guardarFilasProductosFacturaNueva($ins, $_POST['idAlbaranProveedor'], $_POST);
                        
                        if($insRows){
                        
                            $this->modeloBase->updateFieldTabla($this->tabla, 'baseimponible', $this->baseImponible, $ins);
                            $this->modeloBase->updateFieldTabla($this->tabla, 'ivatotal', $this->ivaTotal, $ins);
                            $this->modeloBase->updateFieldTabla($this->tabla, 'total', $this->total, $ins);
                            $this->modeloBase->updateFieldTabla($this->tabla_proveedores_albaranes, 'estado', 'facturado', $_POST['idAlbaranProveedor']);
                            $this->modeloBase->updateFieldTabla($this->tabla_proveedores_albaranes, 'idfactura', $ins, $_POST['idAlbaranProveedor']);                        
                            $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_proveedores_albaranes_det, 'idfactura', $ins, 'idalbaran', $_POST['idAlbaranProveedor']);
        
                            $this->crearReciboDesdeFactura($ins);

                            $respuesta['error'] = false;
                            $respuesta['mensaje'] = OK_CREACION; 
                            $respuesta['idfactura'] = $ins; 
                        }                
                    }

                }else{
                    $respuesta['error'] = true;
                    $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
                }
    
            }else{

                $arrValidar = $this->construirArrayCamposObligatorio($_POST);
                $fieldsValidate = UtilsHelper::validateRequiredFields($arrValidar, $this->arrFieldsValidate);
                $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;    
                $respuesta['fieldsValidate'] = $fieldsValidate; 

            }
            
        }

        print_r(json_encode($respuesta));
    }

    private function guardarFilasProductosFacturaNueva($idFactura, $idAlbaran, $post)
    {          
        $retorno = false;
        $cont = 0;            
        $baseImponible = 0;        
        $ivaTotal = 0;
        $total = 0;           
        
        $filasAlbaran = $this->modeloAlbaranDetalleProveedor->getRowsAlbaran($idAlbaran);
        
        foreach ($filasAlbaran as $key) {                  
            
                $tmp = [];            
            
                $tmp['idfactura'] = $idFactura;  
                $tmp['idproducto'] = $key->idproducto;          
                $tmp['descripcion'] = $key->descripcion;
                $tmp['unidad'] = $key->unidad;                                 
                $cantidad = $key->cantidad;
                $tmp['cantidad'] = $cantidad;            
                $tmp['precio'] = $key->precio;
                $ivatipo = ($key->ivatipo != '')? $key->ivatipo: 0;
                $tmp['ivatipo'] = $ivatipo;
                $subTotal = $cantidad * $key->precio;
                $tmp['subtotal'] = $subTotal;                
    
                $tmp['idfilaalbaran'] = $key->id; 
                $tmp['idalbaran'] = $key->idalbaran;
                                 
                $baseImponible = $baseImponible + $subTotal;
                $ivaTotal = $ivaTotal + ($ivatipo * $subTotal / 100);         
                
                $arraFields = $this->arrFieldsRowsCreate;                

                $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $arraFields);
          

                $ok = $stringQueries['ok'];
                $strFields = $stringQueries['strFields'];
                $strValues = $stringQueries['strValues'];
                if($ok){
                    $insRows = $this->modeloBase->insertRow($this->tablaRows, $strFields, $strValues);
                    if($insRows){
                        $cont++;
                    }
                }                                                           
        }       

        if($cont == count($filasAlbaran) ){            
            $retorno = true;
            $this->baseImponible = $baseImponible;
            $this->ivaTotal = $ivaTotal;
            $this->total = $baseImponible + $ivaTotal;
        }
        return $retorno;
       
    }


    private function crearReciboDesdeFactura($idFactura)
    {      
        $factura = $this->modeloFacturaProveedor->getInvoiceData($idFactura);
        
        if($factura->fecha != '' && $factura->total != '' && $factura->numero != ''){
            
            $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tablaProveedoresRecibos, date("Y",strtotime($factura->fecha)));

            $ceros = '';
            if($nextCodeInterno >= 1 && $nextCodeInterno <= 9){
                $ceros = '00';
            }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
                $ceros = '0';
            }
            $arrValues =[                               
                'numerointerno' => $nextCodeInterno,
                'numero' => date("y",strtotime($factura->fecha)).".".$ceros.$nextCodeInterno,
                'fecha' => date('Y-m-d'),
                'importe' => $factura->total,
                'idfactura' => $factura->id,
                'concepto' => 'Cobrament de la factura Nº '. $factura->numero,
                'lugarexpedicion' => '',
                'librado' => $this->modeloConfiguracion->getBusinessName(),
                'librador'   => $factura->proveedor, 
                'estado' => 'pendiente', //pagado,impagado,pendiente
                'vencimiento' => $factura->vencimiento
            ];                                     

            $arrFields = ['numero','numerointerno','fecha','importe','idfactura','concepto','lugarexpedicion','librado','librador','estado','vencimiento'];            

            $r = UtilsHelper::buildStringsInsertQuery($arrValues, $arrFields);
            if($r['ok']){
                $ins = $this->modeloReciboProveedor->createReceiptFromInvoice($r['strValues'], $r['strFields']);        
                
                if($ins){              
                    $this->actualizarEstadoFactura($factura->id);
                }                              
            }
        }
    }    


    private function construirArrayCamposObligatorio($post)
    {   
        $arrValidar['numfactura'] = trim($post['numero_factura_proveedor']);     
        $arrValidar['proveedor'] = trim($post['nombre_proveedor']);
        $arrValidar['nif'] = trim($post['nif_proveedor']);
        $arrValidar['fecha'] = $post['fecha_factura_proveedor']; 
        $arrValidar['diaspago'] = (trim($post['dias_albaran_proveedor']) != '')? $post['dias_albaran_proveedor']: '';       
        $arrValidar['vencimiento'] = $post['vencimiento'];        
        return $arrValidar;
    }    

    private function crearCabeceraFacturaDesdeAlbaran($post)
    {
            $ins = false;   
                                 
            $arrValues['numero'] = $post['numero_factura_proveedor'];            
            $arrValues['idproveedor'] = $this->modeloAlbaranProveedor->getNombreProveedorByIdAlbaran($post['idAlbaranProveedor']);
            $arrValues['proveedor'] = $post['nombre_proveedor'];
            $arrValues['fecha'] = $post['fecha_factura_proveedor'];
            $arrValues['baseimponible'] = 0;
            $arrValues['ivatotal'] = 0;
            $arrValues['total'] = 0;
            $arrValues['observaciones'] = (trim($post['observaciones_factura_proveedor'])!= '')? $post['observaciones_factura_proveedor']: '';
            $arrValues['diaspago'] = ($post['dias_albaran_proveedor'] != '')? $post['dias_albaran_proveedor']: 0;  
            
            $vencimiento = $post['vencimiento'];
            if($post['vencimiento'] == '' && $arrValues['diaspago'] == 0)    {
                $vencimiento = $post['fecha_factura_proveedor'];
            }
            $arrValues['vencimiento'] = $vencimiento;   

            $stringQueries = UtilsHelper::buildStringsInsertQuery($arrValues, $this->arrFieldsCreate);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];
                       
            if($ok){
                $ins = $this->modeloBase->insertRow($this->tabla, $strFields, $strValues);                
            }                                
            return $ins;        
    }  

    private function guardarFilasProductosFactura($idFactura, $post)
    {

        $retorno = false;
        $cont = 0;            
        $baseImponible = 0;        
        $ivaTotal = 0;
        $total = 0;   


        foreach ($post['numeroOrden'] as $key => $value) {
            $tmp = [];            
            
            $tmp['idfactura'] = $idFactura;            
            $tmp['descripcion'] = $post['descripcion'][$key];
            $tmp['unidad'] = $post['unidadArticulo'][$key];           
            $cantidad = ($post['cantidadArticulo'][$key] != '')? str_replace(",", ".", $post['cantidadArticulo'][$key]): 0;
            $tmp['cantidad'] = $cantidad;
            $precio = ($post['precioArticulo'][$key] != '')? str_replace(",", ".", $post['precioArticulo'][$key]): 0;
            $tmp['precio'] = $precio;
            $ivatipo = ($post['iva'][$key] != '')? $post['iva'][$key]: 0;
            $tmp['ivatipo'] = $ivatipo;
            $subTotal = $cantidad * $precio;
            $tmp['subtotal'] = $subTotal;                
                             
            $baseImponible = $baseImponible + $subTotal;
            $ivaTotal = $ivaTotal + ($ivatipo * $subTotal / 100);                  

            //$stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $this->arrFieldsRowsUpdate);
            //$stringQueries = UtilsHelper::buildStringsInsertQueryNuevo($tmp, $this->arrFieldsRowsUpdate);            
           
            if(isset($post['idFila'][$key])){   
                $arrWhere['id'] = $post['idFila'][$key];
                $stringQueries = UtilsHelper::buildStringsUpdateQuery($tmp, $this->arrFieldsRowsUpdate);
                $ok = $stringQueries['ok'];                        
                    
                $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
                $okw = $stringWhere['ok'];                                           
                            
                if($ok && $okw){
                    $strFieldsValues = $stringQueries['strFieldsValues'];
                    $strWhere = $stringWhere['strWhere'];                  

                    $insRows = $this->modeloBase->updateRow($this->tablaRows, $strFieldsValues, $strWhere);

                    if($insRows){
                        $cont++;
                        //TODO: actualizar albaranes origen de la factura
                    }
                }    
            }                   
        }

        if($cont == count($post['numeroOrden'])){            
            $retorno = true;
            $this->baseImponible = $baseImponible;
            $this->ivaTotal = $ivaTotal;            
            $retencionTipo = (isset($post['retenciontipo']) && $post['retenciontipo'] > 0)? str_replace(",", ".", $post['retenciontipo']): 0;
            $this->retencionTipo = $retencionTipo;
            $retencionImporte = $baseImponible * $retencionTipo /100;
            $this->retencionImporte = $retencionImporte;
            $this->total = $baseImponible - $retencionImporte + $ivaTotal;
        }
        return $retorno;
       
    }

    public function verFactura($idFactura){
        
        if(isset($idFactura) && $idFactura > 0){
                        
            if($this->modeloBase->existIdInvoice($this->tabla, $idFactura) > 0){
                $cab = $this->modeloFacturaProveedor->getInvoiceData($idFactura);            
                $cabecera = (array) $cab;
                $nifProveedor = $this->modeloProveedor->getNifSupplier($cab->idproveedor);          
                $detalle['html'] = $this->construirBodyTablaGrilla($idFactura, 'factura');
                $proveedores = $this->modeloProveedor->getEnabledSuppliers();

                $clienteDescarga = $this->obtenerClienteDescarga($idFactura);        
    
                $tmp = [
                    'idFactura' => $idFactura,
                    'proveedores' => $proveedores,
                    'cliente_descarga' => $clienteDescarga                
                ];
                
                $datos = array_merge($tmp, $cabecera, $detalle);     
               
               


                $this->vista('facturasProveedor/verFactura', $datos);
    
            }else{        
                redireccionar('/FacturasProveedores');
            }
            
        }else{        
            redireccionar('/FacturasProveedores');
        }
    }

    private function obtenerClienteDescarga($idFactura)
    {
        $clienteDescarga = '';

        $idPlanFecha = $this->modeloAlbaranDetalleProveedor->getRowsDeliveryNoticeByIdInvoice($idFactura);

       

        if($idPlanFecha > 0){
          
               
            if(isset($idPlanFecha) && $idPlanFecha > 0){
                $idCliente = $this->modeloPlanificacionFechas->getDatesPlanningById($idPlanFecha)->idcliente;
                $clienteDescarga =  $this->modeloCliente->getClientById($idCliente)->nombrefiscal;
            }
        
        }

        return $clienteDescarga;
          
    }

    private function construirBodyTablaGrilla($idFactura, $tipoDoc){

        $rows = $this->modeloFacturaDetalleProveedor->getRowsInvoice($idFactura);
        $datos = [                        
            'tiposIva' => $this->modeloIva->getAllIvasActive()
        ]; 

        $html = TemplateHelper::buildGridRows($rows, $datos, $tipoDoc);
        return $html;
    }
    
    public function obtenerAlbaranesFactura(){

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    
        $respuesta['html_albaranes'] = 'No hi ha albarans vinculats.';

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idFactura = $this->fetch['id'];
        
            $sel = $this->modeloAlbaranProveedor->getDeliveryNoteByIdInvoice($idFactura);
           

            if ($sel) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';                
                $respuesta['html_albaranes'] = TemplateHelper::buildGridDeliveryNotes($sel);
                
            }
            $respuesta['diferencias'] = $this->construirDiferenciasEntreAlbaranesYFactura($idFactura);

         
        }                       
        print_r(json_encode($respuesta));
    }

    private function construirDiferenciasEntreAlbaranesYFactura($idFactura)
    {
        $datos = $this->modeloFacturaProveedor->getTotalsDeliveryNotesAndInvoiceByIdFactura($idFactura);
        
        return TemplateHelper::buildDifferencesBetweenDeliveryNotesAndInvoice($datos);
    }
    

    public function facturacionMasiva()
    {        
        $proveedores = $this->modeloProveedor->getEnabledSuppliers();    
        $datos = [            
            'proveedores' => $proveedores,
            'nif_proveedor_default' => (PROVEEDOR_DEFAULT > 0)? $this->modeloProveedor->getNifSupplier(PROVEEDOR_DEFAULT): ''                
        ];
        $this->vista('facturasProveedor/facturacionMasiva', $datos);
    }

    public function actualizarFactura()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;   
      
        if(isset($_POST['id']) && $_POST['id'] != '' && $_POST['id'] > 0 && isset($_POST['numeroOrden']) && count($_POST['numeroOrden']) > 0 ){
                            
            $idFactura = $_POST['id'];

            //$where1 = " idfactura = $idFactura ";
            //$delRows = $this->modeloBase->deleteRow($this->tablaRows, $where1); //AQUI ME QUEDÉ, ELIMINAR ESTO Y ACTUALIZAR POR ID FILA FACTURA DETALLE
            //if($delRows){

                $updRows = $this->guardarFilasProductosFactura($idFactura, $_POST);

                if($updRows){                    
                    
                    $arrWhere['id'] = $idFactura;        
                   
                    $_POST['proveedor'] = $this->modeloProveedor->getNameSupplier($_POST['idproveedor']);
                    $_POST['baseimponible'] = $this->baseImponible;
                    $_POST['ivatotal'] = $this->ivaTotal;
                    $_POST['total'] = $this->total;
                    
                    $_POST['retenciontipo'] = $this->retencionTipo;
                    $_POST['retencionimporte'] = $this->retencionImporte;
                                    
                    $_POST['diaspago'] = $_POST['dias_albaran_proveedor'];
                    $_POST['fecha'] = $_POST['fecha_factura_proveedor'];                    
                    
                    $stringQueries = UtilsHelper::buildStringsUpdateQuery($_POST, $this->arrFieldsUpdate);
                    $ok = $stringQueries['ok'];                                           
                         
                    $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
                    $okw = $stringWhere['ok'];    
                                  
                    if($ok && $okw){
                        $strFieldsValues = $stringQueries['strFieldsValues'];
                        $strWhere = $stringWhere['strWhere'];
        
                        $upd = $this->modeloBase->updateRow($this->tabla, $strFieldsValues, $strWhere);
        
                        if($upd){                            
                            
                            $estado = $this->actualizarEstadoFactura($idFactura);

                            $respuesta['html'] = $this->construirBodyTablaGrilla($idFactura, 'factura');
                            $respuesta['error'] = false;
                            $respuesta['mensaje'] = OK_ACTUALIZACION;
                            $new = $this->modeloFacturaProveedor->getInvoiceData($idFactura);   
                            $respuesta['baseimponible'] = number_format($new->baseimponible,2,",",".");
                            $respuesta['ivatotal'] = number_format($new->ivatotal,2,",",".");
                            $respuesta['total'] = number_format($new->total,2,",",".") ;
                            $respuesta['retencionimporte'] = number_format($new->retencionimporte,2,",",".");
                            $respuesta['retenciontipo'] = number_format($new->retenciontipo,2,",",".");
                            $respuesta['estado'] = $estado;
                                                   
                        }
                    }   

                }
            //}
                     
        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
        } 
        
        echo json_encode($respuesta);
    }

    private function actualizarEstadoFactura($idFactura)
    {
        $totalFactura = $this->modeloFacturaProveedor->getTotalAmountInvoice($idFactura);
        $totalRecibos = $this->modeloReciboProveedor->getTotalAmountPaidReceiptsByInvoice($idFactura);
               
        $estado = 'pendiente';

        if($totalRecibos == 0){
            $estado = 'pendiente';
        }else if($totalFactura > 0 && $totalRecibos < $totalFactura ){
            $estado = 'pagada parcial';
        }else if($totalFactura > 0 && $totalRecibos == $totalFactura ){
            $estado = 'pagada';
        }
        $this->modeloBase->updateFieldTabla($this->tabla, 'estado', $estado, $idFactura);
        
        return $estado;

    }

    public function obtenerAlbaranesConFiltros()
    {

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   
        
        if(isset($_POST['fechainicio']) && $_POST['fechainicio'] != '' && isset($_POST['fechafin']) && $_POST['fechafin'] != '' && isset($_POST['idproveedorsearch']) && $_POST['idproveedorsearch'] > 0){
            
            $idProveedor = $_POST['idproveedorsearch'];
            $query_search = " idproveedor = '$idProveedor' ";

            $fechaInicio = $_POST['fechainicio'];
            $fechaFin = $_POST['fechafin'];

            $query_search .= "AND ( fecha BETWEEN '$fechaInicio' AND '$fechaFin' ) ";
            
            if (isset($_POST['estado_albaran']) && $_POST['estado_albaran'] == 'todos'){
                $query_search .= ' AND 1 ';
            }else{
                $estado= $_POST['estado_albaran'];
                $query_search .= "AND estado = '$estado' ";
            }

         

            $resultado = $this->modeloAlbaranProveedor->getDeliveryNotesSearch($query_search);

            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';                
            $respuesta['html_albaranes'] = TemplateHelper::buildGridDeliveryNotesSearch($resultado);

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
        }
        
        
        echo json_encode($respuesta);
    }

    public function obtenerAlbaranFila(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;               

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
         
            $idAlbaran = $this->fetch['id'];
        
            $sel = $this->modeloAlbaranProveedor->getAlbaranData($idAlbaran);
            if ($sel) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';                
                $respuesta['html_albaran'] = TemplateHelper::buildGridDeliveryNotesToInvoice($sel);
            }
        }                       
        print_r(json_encode($respuesta));
    }

    public function crearFacturaMasivaProveedor(){

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;        
        
        if(isset($_POST['idproveedor']) && $_POST['idproveedor'] != '' && $_POST['idproveedor'] > 0 && $_POST['fecha_factura_proveedor'] != '' && trim($_POST['fecha_factura_proveedor']) != '' && trim($_POST['numero_factura_proveedor']) != ''  && isset($_POST['idAlbaranSelected']) && count($_POST['idAlbaranSelected']) > 0 && trim($_POST['dias_albaran_proveedor']) != '' && trim($_POST['vencimiento']) != ''){

            $strIdesAlbaran = implode(",", $_POST['idAlbaranSelected']);

            $validaProveedor = $this->modeloAlbaranProveedor->buscarProveedorEnAlbaranes($strIdesAlbaran, $_POST['idproveedor']);
            if($validaProveedor > 0 &&  $validaProveedor==count($_POST['idAlbaranSelected'])){

                $ins = $this->crearCabeceraFacturaMasiva($_POST);        
            
                if($ins){
    
                    $totalesFactura = $this->totalizarTodosLosAlbaranesUnaFactura($_POST['idAlbaranSelected'],false);
    
                    $this->modeloBase->updateFieldTabla($this->tabla, 'baseimponible', $totalesFactura->suma_subtotal, $ins);
                    $this->modeloBase->updateFieldTabla($this->tabla, 'ivatotal', $totalesFactura->importe_iva, $ins);
                    $this->modeloBase->updateFieldTabla($this->tabla, 'total', $totalesFactura->suma_total, $ins);

                    $totalesFilas = $this->modeloAlbaranProveedor->getDeliveryNoteRowsToCreateInvoice($strIdesAlbaran);
                    $insRows = $this->guardarFilasFacturaMasiva($ins, $totalesFilas);
                    
                    if($insRows){                                        
    
                        $this->modeloBase->updateFieldTablaByStringIn($this->tabla_proveedores_albaranes, 'estado', 'facturado', $strIdesAlbaran);
                        $this->modeloBase->updateFieldTablaByStringIn($this->tabla_proveedores_albaranes, 'idfactura', $ins, $strIdesAlbaran);
                        $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_proveedores_albaranes_det, 'idfactura', $ins, 'idalbaran', $strIdesAlbaran);

                        $this->crearReciboDesdeFactura($ins);
                        
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_CREACION; 
                        $respuesta['idfactura'] = $ins; 
                        
                    }                 
                }                                    

            }else{                
                $respuesta['error'] = true;
                $respuesta['mensaje'] = 'El proveïdor de la factura no correspon al proveïdor dels albarans seleccionats'; 
            }

    
        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;    
        }
        
        print_r(json_encode($respuesta));

    }
    
    private function crearCabeceraFacturaMasiva($post)
    {
            $ins = false;            
            $arrValues['numero'] = $post['numero_factura_proveedor'];            
            $arrValues['idproveedor'] = $post['idproveedor'];
            $arrValues['proveedor'] = $this->modeloProveedor->getNameSupplier($post['idproveedor']);
            $arrValues['fecha'] = $post['fecha_factura_proveedor'];
            $arrValues['baseimponible'] = 0;
            $arrValues['ivatotal'] = 0;
            $arrValues['total'] = 0;
            $arrValues['observaciones'] = (trim($post['observaciones'])!= '')? $post['observaciones']: '';
            $arrValues['diaspago'] = (trim($post['dias_albaran_proveedor']) != '')? $post['dias_albaran_proveedor']: 0;  
                      
            $vencimiento = $post['vencimiento'];
            if($post['vencimiento'] == '' && $arrValues['diaspago'] == 0)    {
                $vencimiento = $post['fecha_factura_proveedor'];
            }
            $arrValues['vencimiento'] = $vencimiento;       

            $stringQueries = UtilsHelper::buildStringsInsertQuery($arrValues, $this->arrFieldsCreate);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];
                       
            if($ok){
                $ins = $this->modeloBase->insertRow($this->tabla, $strFields, $strValues);                
            }                                
            return $ins;        
    }  

    private function guardarFilasFacturaMasiva($idFactura, $filas)
    {
        $retorno = false;
        $cont = 0;                   

        foreach ($filas as $key) {
            
            $tmp = [];

            $tmp['idfactura'] = $idFactura;            
            $tmp['idproducto'] = $key->idproducto;
            $tmp['descripcion'] = $key->descripcion;
            $tmp['unidad'] = $key->unidad; 
            $cantidad = $key->cantidad;
            $tmp['cantidad'] = $cantidad;
            $subtotal = $key->subtotal;
            $precio = ($cantidad > 0)? round($subtotal/$cantidad,2): 0;
            $tmp['precio'] = $precio;
            $tmp['subtotal'] = $subtotal;
            $tmp['ivatipo'] = $key->ivatipo;                
            $tmp['idfilaalbaran'] = $key->id; 
            $tmp['idalbaran'] = $key->idalbaran;
            
            $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $this->arrFieldsRowsCreate);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];

            if($ok){
                $insRows = $this->modeloBase->insertRow($this->tablaRows, $strFields, $strValues);
                if($insRows){
                    $cont++;
                }
            }                        
        }

        if($cont == count($filas)){            
            $retorno = true;           
        }
        return $retorno;       
    }

    private function totalizarTodosLosAlbaranesUnaFactura($idAlbaranSelected, $group=false)
    {
        $strIdesAlbaran = implode(",", $idAlbaranSelected);
        $groupby = '';
        if($group){
            $groupby = ' GROUP BY det.descripcion ASC, det.ivatipo  ';  
        }
        $totales = $this->modeloAlbaranProveedor->getTotalsInvoices($strIdesAlbaran, $groupby);       
        return $totales;
    }

    public function eliminarAlbaranFactura()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ELIMINACION;
        
        if(isset($this->fetch) && $this->fetch['id'] > 0){
            
            $updIdFactura = $this->modeloBase->updateFieldTabla($this->tabla_proveedores_albaranes,'idfactura',0, $this->fetch['id']);  

            $updIdFacturaDetAlbaran = $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_proveedores_albaranes_det,'idfactura',0,'idalbaran',$this->fetch['id']);
            
            $updEstado = $this->modeloBase->updateFieldTabla($this->tabla_proveedores_albaranes,'estado','pendiente', $this->fetch['id']);

            if($updIdFactura && $updEstado && $updIdFacturaDetAlbaran){
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;
                $respuesta['diferencias'] = $this->construirDiferenciasEntreAlbaranesYFactura($this->fetch['idFactura']);
            }            
        }
        print_r(json_encode($respuesta));
    }

    
    public function datosFacturaProveedorParaRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;        

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';                

            $idFactura = $this->fetch['id'];            
            $datos = $this->modeloFacturaProveedor->getInvoiceData($idFactura);
            $respuesta['datos'] = $datos;
            $respuesta['librado'] =  $this->modeloConfiguracion->getBusinessName();
            $respuesta['fecha_actual'] = date('Y-m-d');
            $respuesta['vencimiento'] = (isset($datos->vencimiento) || $datos->vencimiento !='')? $datos->vencimiento: DateTimeHelper::calcularFechaFin($datos->fecha, $datos->diaspago);
        }                       
        print_r(json_encode($respuesta));
    }

    public function calcularFechaVencimientoFacturaProveedor()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = 'No és possible calcular la data de venciment.';

        if(isset($this->fetch) && isset($this->fetch['dias']) && $this->fetch['fecha'] != '') {
            $dias_pago = (trim($this->fetch['dias']) != '')? trim($this->fetch['dias']): 0;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['fechaVecimiento'] = DateTimeHelper::calcularFechaFin($this->fetch['fecha'], $dias_pago);
        }                
        echo json_encode($respuesta);
    }

    public function calcularDiasCobroFacturaProveedor()
    {   
        $respuesta['error'] = true;
        $respuesta['mensaje'] = 'No es poden calcular els dies de cobrament.';

        if(isset($this->fetch) && isset($this->fetch['vencimiento']) && isset($this->fetch['fecha_factura_proveedor'])) {            
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['dias_albaran_proveedor'] = DateTimeHelper::calcularDiasEntreFechas($this->fetch['fecha_factura_proveedor'], $this->fetch['vencimiento']);
        }                
        echo json_encode($respuesta);
    }

    public function calcularFechaVencimientoFacturaProveedorCambiarFecha(){

        $respuesta['error'] = true;
        $respuesta['mensaje'] = 'No és possible calcular la data de venciment.';

        if(isset($this->fetch) && isset($this->fetch['fecha_factura_proveedor']) && $this->fetch['fecha_factura_proveedor'] != '') {
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            if(trim($this->fetch['dias']) != ''){
                $dias_pago = trim($this->fetch['dias']);
                //calcular fecha de vencimiento              
                $respuesta['fechaVecimiento'] = DateTimeHelper::calcularFechaFin($this->fetch['fecha_factura_proveedor'], $dias_pago);
            }else{
                                
                if(isset($this->fetch['vencimiento']) && $this->fetch['vencimiento'] != ''){
                    //calcular dias                   
                    $respuesta['dias_albaran_proveedor'] = DateTimeHelper::calcularDiasEntreFechas($this->fetch['fecha_factura_proveedor'], $this->fetch['vencimiento']);
                }else{
                    //calcule días y vencimiento                  
                    $respuesta['fechaVecimiento'] = $this->fetch['fecha_factura_proveedor'];
                    $respuesta['dias_albaran_proveedor'] = 0;
                }

            }                       
        }                
        echo json_encode($respuesta);

    }

    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);                                  
            $where = str_replace("fecha like", "DATE_FORMAT(fac.fecha, '%d/%m/%Y') like", $where);             
            $where = str_replace("nom_cli like", "planif_cliente.nom_cli like", $where);  

            $order = " ORDER BY fac.fecha ASC ";          
         
            $datos = $this->modeloFacturaProveedor->obtenerFacturasProveedoresExportar($order,$where);
            $nombreReporte = '_FacturasProveedores';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }

    public function consultarFacturaParaEliminar()
    {
        $respuesta['eliminar'] = false;
        $respuesta['mensaje'] = '';

        
        if(isset($this->fetch) && isset($this->fetch['id']) && $this->fetch['id'] > 0) {
            $idFactura = $this->fetch['id'];
            $recibos = $this->modeloReciboProveedor->getReceiptsByIdInvoice($idFactura);

            if(count($recibos) > 0){
                $respuesta['eliminar'] = false;
                $respuesta['mensaje'] = 'No es pot eliminar la factura perquè hi ha rebuts vinculats: ';
                $respuesta['recibos'] = $recibos;
            }else{
                $respuesta['eliminar'] = true;
                $respuesta['albaranes'] = $this->modeloAlbaranProveedor->getDeliveryNoteByIdInvoice($idFactura);
            }
        }
       
        print_r(json_encode($respuesta));
    }

    public function eliminarFactura()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['idFacturaEliminar']) && $_POST['idFacturaEliminar'] > 0) {

            $idFactura = $_POST['idFacturaEliminar'];
            $where1 = " id = $idFactura ";  
            $where2 = " idfactura = $idFactura ";  
            
            $updCabecera = $this->modeloBase->deleteRow($this->tabla, $where1);
            $updFilas = $this->modeloBase->deleteRow($this->tablaRows, $where2);

            if ($updCabecera && $updFilas) {
                $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_proveedores_albaranes, 'estado', 'pendiente', 'idfactura', $idFactura);
                $this->desvincularAlbaranesCabecera($idFactura);
                $this->desvincularAlbaranesDetalle($idFactura);                
                
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }    

    private function desvincularAlbaranesCabecera($idFactura)
    {
        $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_proveedores_albaranes, 'idfactura', 0, 'idfactura', $idFactura);
    }

    private function desvincularAlbaranesDetalle($idFactura)
    {
        $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_proveedores_albaranes_det, 'idfactura', 0, 'idfactura', $idFactura);
    }
    

}
