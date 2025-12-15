<?php

class FacturasClientes extends Controlador {

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

        $this->tabla = 'clientes_facturas';
        $this->tablaRows = 'clientes_facturas_det';
        $this->tabla_clientes_albaranes = 'clientes_albaranes';
        $this->tabla_clientes_albaranes_det = 'clientes_albaranes_det';
        
        $this->arrFieldsCreate = ['numerointerno','numero','idcliente','cliente','fecha','baseimponible','ivatotal','total','observaciones','diascobro','vencimiento','idcuentabancaria','idformacobro'];                
        $this->arrFieldsRowsCreate = ['idproducto','idfactura','descripcion','unidad','cantidad','precio','ivatipo','subtotal','idfilaalbaran','idalbaran','descuentotipo'];
        
        $this->arrFieldsValidate = ['cliente','nif','fecha','diascobro','vencimiento'];
        $this->arrFieldsValidateUpdate = ['idcliente','nif','fecha','diascobro','vencimiento'];
        $this->fieldsValidateNegInvoice = ['idFacturaOrigen','fecha'];
        $this->modeloFacturaCliente = $this->modelo('ModeloFacturaCliente');          
        $this->modeloAlbaranCliente = $this->modelo('ModeloAlbaranCliente');
        $this->modeloFacturaDetalleCliente = $this->modelo('ModeloFacturaDetalleCliente');  
        $this->modeloAlbaranDetalleCliente = $this->modelo('ModeloAlbaranDetalleCliente');                  
        $this->modeloBase = $this->modelo('ModeloBase');         
        $this->modeloIva = $this->modelo('ModeloIva');
        $this->modeloCliente = $this->modelo('ModeloCliente');
        $this->modeloProductoVenta = $this->modelo('ModeloProductoVenta');
        $this->modeloReciboCliente = $this->modelo('ModeloReciboCliente');                
        $this->arrFieldsUpdate = ['idcliente','cliente','fecha','diascobro','observaciones','total','ivatotal','baseimponible','vencimiento','idcuentabancaria','idformacobro','descuentotipo','descuentoimporte'];
        $this->arrFieldsRowsUpdate = ['idproducto','descripcion','unidad','cantidad','precio', 'ivatipo','subtotal','descuentotipo'];
        $this->arrFieldsRowsUpdateAlbaran = ['idproducto','descripcion','unidad','cantidad','precio', 'ivatipo','subtotal'];   
        $this->modeloCondiciones = $this->modelo('ModeloCondiciones');
        $this->modeloFormasPago = $this->modelo('ModeloFormasPago');
        $this->modeloCuentasBancarias = $this->modelo('ModeloCuentasBancarias');
        $this->arrFieldsCreateRectificativa = ['numerointerno','numero','idcliente','cliente','fecha','baseimponible','ivatotal','total','observaciones','diascobro','vencimiento','idcuentabancaria','idformacobro','idfacturaorigen']; 
        $this->tablaClientesRecibos = 'clientes_recibos';         
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');

        $this->arrFieldsCreateAlbaran = ['numerointerno','numero','idcliente','cliente','fecha','observaciones','total','ivatotal','baseimponible'];
        $this->arrFieldsRowsCreateAlbaran = ['idalbaran','idproducto','descripcion','unidad','cantidad','precio','ivatipo','subtotal','idfactura'];

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        } 
    }

    public function index() {
        $datos = [];
        $this->vista('facturasCliente/facturas', $datos);
    }

    public function tablaFacturasCliente()
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
        
        $facturas = $this->modeloFacturaCliente->obtenerFacturasClientesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloFacturaCliente->obtenerTotalFacturasCliente($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    } 

    public function calcularFechaVencimientoFacturaCliente()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = 'No és possible calcular la data de venciment.';

        if(isset($this->fetch) && isset($this->fetch['dias']) && $this->fetch['fecha'] != '') {
            $dias_cobro = (trim($this->fetch['dias']) != '')? trim($this->fetch['dias']): 0;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['fechaVecimiento'] = DateTimeHelper::calcularFechaFin($this->fetch['fecha'], $dias_cobro);
        }                
        echo json_encode($respuesta);
    }

    public function calcularDiasCobroFacturaCliente()
    {   
        $respuesta['error'] = true;
        $respuesta['mensaje'] = 'No es poden calcular els dies de cobrament.';

        if(isset($this->fetch) && isset($this->fetch['vencimiento']) && isset($this->fetch['fecha_factura_cliente'])) {            
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['dias_albaran_cliente'] = DateTimeHelper::calcularDiasEntreFechas($this->fetch['fecha_factura_cliente'], $this->fetch['vencimiento']);
        }                
        echo json_encode($respuesta);
    }
   
    public function crearFacturaClienteDesdeAlbaran()
    {            

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;    

        $estado = $this->modeloAlbaranCliente->getAlbaranStatus($_POST['idAlbaranCliente']);
        
        if(isset($estado) && $estado == 'facturado'){

            $respuesta['error'] = true;
            $respuesta['mensaje'] = ALBARAN_FACTURADO; 

        }else{

            if(isset($_POST['idAlbaranCliente']) && $_POST['idAlbaranCliente'] != '' && $_POST['idAlbaranCliente'] > 0 && $_POST['fecha_factura_cliente'] != '' && trim($_POST['fecha_factura_cliente']) != ''  && isset($_POST['numeroOrden']) && count($_POST['numeroOrden']) > 0 && $_POST['fecha_vencimiento_cliente'] != '' && trim($_POST['dias_albaran_cliente']) != ''){

                $filasAlbaran = $this->modeloAlbaranDetalleCliente->getRowsAlbaran($_POST['idAlbaranCliente']);
                
                if($filasAlbaran && count($filasAlbaran) > 0){

                    $ins = $this->crearCabeceraFacturaDesdeAlbaran($_POST);

                    if($ins){
                        $insRows = $this->guardarFilasProductosFacturaNueva($ins, $_POST['idAlbaranCliente'], $_POST);
                        
                        if($insRows){
                        
                            $this->modeloBase->updateFieldTabla($this->tabla, 'baseimponible', $this->baseImponible, $ins);
                            $this->modeloBase->updateFieldTabla($this->tabla, 'ivatotal', $this->ivaTotal, $ins);
                            $this->modeloBase->updateFieldTabla($this->tabla, 'total', $this->total, $ins);
                            $this->modeloBase->updateFieldTabla($this->tabla_clientes_albaranes, 'estado', 'facturado', $_POST['idAlbaranCliente']);
                            $this->modeloBase->updateFieldTabla($this->tabla_clientes_albaranes, 'idfactura', $ins, $_POST['idAlbaranCliente']);
                            $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_clientes_albaranes_det, 'idfactura', $ins, 'idalbaran', $_POST['idAlbaranCliente']);
    
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

    private function crearReciboDesdeFactura($idFactura)
    {      
        $factura = $this->modeloFacturaCliente->getInvoiceData($idFactura);
        
        if($factura->fecha != '' && $factura->total != '' && $factura->numero != ''){
            
            $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tablaClientesRecibos, date("Y",strtotime($factura->fecha)));

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
                'librado' => $factura->cliente, 
                'librador'   => $this->modeloConfiguracion->getBusinessName(),
                'estado' => 'pendiente', //pagado,impagado,pendiente
                'vencimiento' => $factura->vencimiento
            ];                                     

            $arrFields = ['numero','numerointerno','fecha','importe','idfactura','concepto','lugarexpedicion','librado','librador','estado','vencimiento'];            

            $r = UtilsHelper::buildStringsInsertQuery($arrValues, $arrFields);
            if($r['ok']){
                $ins = $this->modeloReciboCliente->createReceiptFromInvoice($r['strValues'], $r['strFields']);        
                
                if($ins){              
                    $this->actualizarEstadoFactura($factura->id);
                }                              
            }
        }
    }

    private function construirArrayCamposObligatorio($post)
    {              
        $arrValidar['cliente'] = trim($post['nombre_cliente']);
        $arrValidar['nif'] = trim($post['nif_cliente']);
        $arrValidar['fecha'] = $post['fecha_factura_cliente'];        
        $arrValidar['diascobro'] = (trim($post['dias_albaran_cliente']) != '')? $post['dias_albaran_cliente']: 0;       
        $arrValidar['vencimiento'] = $post['fecha_vencimiento_cliente'];        
        return $arrValidar;
    }

   

    private function crearCabeceraFacturaDesdeAlbaran($post)
    {
            $ins = false;         
            $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla, date("Y",strtotime($_POST['fecha_factura_cliente'])));
            $arrValues['numerointerno'] = $nextCodeInterno;
            $ceros = '';
            if($nextCodeInterno >= 1 && $nextCodeInterno <= 9){
                $ceros = '00';
            }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
                $ceros = '0';
            }
            $arrValues['numero'] = "FRA".date("y",strtotime($_POST['fecha_factura_cliente']))."/".$ceros.$nextCodeInterno;
            $arrValues['idcliente'] = $this->modeloAlbaranCliente->getNombreClienteByIdAlbaran($post['idAlbaranCliente']);
            $arrValues['cliente'] = $post['nombre_cliente'];
            $arrValues['fecha'] = $post['fecha_factura_cliente'];
            $arrValues['baseimponible'] = 0;
            $arrValues['ivatotal'] = 0;
            $arrValues['total'] = 0;
            $arrValues['observaciones'] = (trim($post['observaciones_factura_cliente'])!= '')? $post['observaciones_factura_cliente']: '';
            $diascobro = (isset($post['dias_albaran_cliente']) && trim($post['dias_albaran_cliente']) != '')? $post['dias_albaran_cliente']: 0;
            $arrValues['diascobro'] = $diascobro;          
            $arrValues['vencimiento'] = (isset($post['fecha_vencimiento_cliente']) && $post['fecha_vencimiento_cliente'] != '')? $post['fecha_vencimiento_cliente']: '';
            $arrValues['idcuentabancaria'] = (isset($post['cuentabancaria']) && trim($post['cuentabancaria']) != '')? $post['cuentabancaria']: '';
            $arrValues['idformacobro'] = (isset($post['formacobro']) && $post['formacobro'] != '')? $post['formacobro']: '';

            $stringQueries = UtilsHelper::buildStringsInsertQuery($arrValues, $this->arrFieldsCreate);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];
                       
            if($ok){
                $ins = $this->modeloBase->insertRow($this->tabla, $strFields, $strValues);                
            }                                
            return $ins;        
    }  

    private function guardarFilasProductosFacturaNueva($idFactura, $idAlbaran, $post)
    {          
        $retorno = false;
        $cont = 0;            
        $baseImponible = 0;        
        $ivaTotal = 0;
        $total = 0;   

        $factor = 1;
        if(isset($post['idOrigen']) && $post['idOrigen'] > 0){
            $factor = -1;
        } 
        
        $filasAlbaran = $this->modeloAlbaranDetalleCliente->getRowsAlbaran($idAlbaran);
        
        foreach ($filasAlbaran as $key) {                  
            
                $tmp = [];            
            
                $tmp['idfactura'] = $idFactura;  
                $tmp['idproducto'] = $key->idproducto;          
                $tmp['descripcion'] = $key->descripcion;
                $tmp['unidad'] = $key->unidad;                                 
                $cantidad = $key->cantidad * $factor;
                $tmp['cantidad'] = $cantidad;            
                $tmp['precio'] = $key->precio;
                $ivatipo = ($key->ivatipo != '')? $key->ivatipo: 0;
                $tmp['ivatipo'] = $ivatipo;
                $subTotal = $cantidad * $key->precio;
                $tmp['subtotal'] = $subTotal;
                $tmp['descuentotipo'] = 0;
    
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

    
    public function verFactura($idFactura){
        
        if(isset($idFactura) && $idFactura > 0){
                        
            if($this->modeloBase->existIdInvoice($this->tabla, $idFactura) > 0){
                $cab = $this->modeloFacturaCliente->getInvoiceData($idFactura);            
                $tipo = (isset($cab->idfacturaorigen) && $cab->idfacturaorigen > 0)? 'facturanegativa': 'factura_cliente';
                $cabecera = (array) $cab;

                $detalle['html'] = $this->construirBodyTablaGrilla($idFactura, $tipo);
                $clientes = $this->modeloCliente->getEnabledClients();
    
                $tmp = [
                    'idFactura' => $idFactura,
                    'clientes' => $clientes,                    
                    'formacobro' => $this->modeloFormasPago->getPaymentForms(),
                    'cuentasbancarias' => $this->modeloCuentasBancarias->getBankAccounts()
                ];
                
                $datos = array_merge($tmp, $cabecera, $detalle);     
               
                $this->vista('facturasCliente/verFactura', $datos);
    
            }else{        
                redireccionar('/FacturasClientes');
            }
            
        }else{        
            redireccionar('/FacturasClientes');
        }
    }

    
    private function construirBodyTablaGrilla($idFactura, $tipoDoc){

        $rows = $this->modeloFacturaDetalleCliente->getRowsInvoice($idFactura);
        $datos = [                        
            'productos' => $this->modeloProductoVenta->getAllSaleProducts(),
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
        
            $sel = $this->modeloAlbaranCliente->getDeliveryNoteByIdInvoice($idFactura);
            
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
       $datos = $this->modeloFacturaCliente->getTotalsDeliveryNotesAndInvoiceByIdFactura($idFactura);
      
       
        return TemplateHelper::buildDifferencesBetweenDeliveryNotesAndInvoice($datos);
    }
        

    public function datosFacturaClienteParaRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;        

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';                

            $idFactura = $this->fetch['id'];            
            $datos = $this->modeloFacturaCliente->getInvoiceData($idFactura);
            $respuesta['datos'] = $datos;
            $respuesta['librador'] =  $this->modeloConfiguracion->getBusinessName();                  
            $respuesta['fecha_actual'] = date('Y-m-d');
        }                       
        print_r(json_encode($respuesta));
    }

    public function eliminarAlbaranFactura()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ELIMINACION;
        
        if(isset($this->fetch) && $this->fetch['id'] > 0){
            
            $updIdFactura = $this->modeloBase->updateFieldTabla($this->tabla_clientes_albaranes,'idfactura',0, $this->fetch['id']);
            $updEstado = $this->modeloBase->updateFieldTabla($this->tabla_clientes_albaranes,'estado','pendiente', $this->fetch['id']);            
            $updIdFacturaDetAlbaran = $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_clientes_albaranes_det,'idfactura',0,'idalbaran',$this->fetch['id']);
            
            $idFactura = $this->fetch['idFactura'];

            if($updIdFactura && $updEstado && $updIdFacturaDetAlbaran){
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;
                
                                
                $idAlbaran = $this->fetch['id'];
                $this->modeloBase->deleteRow($this->tablaRows, " idalbaran = '$idAlbaran' ");              

                $totalesFactura = $this->modeloFacturaDetalleCliente->getTotalsInvoice($idFactura);                        
                       
                $suma_base_imponible = (isset($totalesFactura->suma_base_imponible) && $totalesFactura->suma_base_imponible > 0)? $totalesFactura->suma_base_imponible: 0;
                $suma_iva = (isset($totalesFactura->suma_iva) && $totalesFactura->suma_iva > 0)? $totalesFactura->suma_iva: 0;
                $total_final = (isset($totalesFactura->total_final) && $totalesFactura->total_final > 0)? $totalesFactura->total_final: 0;                

                $this->modeloBase->updateFieldTabla($this->tabla, 'baseimponible', $suma_base_imponible, $idFactura);
                $this->modeloBase->updateFieldTabla($this->tabla, 'ivatotal', $suma_iva, $idFactura);
                $this->modeloBase->updateFieldTabla($this->tabla, 'total', $total_final, $idFactura);

                $respuesta['total'] = $total_final;
                $respuesta['ivatotal'] = $suma_iva;
                $respuesta['baseimponible'] = $suma_base_imponible; 
                $estado = $this->actualizarEstadoFactura($idFactura);
                $respuesta['estado'] = $estado;
                $respuesta['html'] = $this->construirBodyTablaGrilla($idFactura, 'factura_cliente');
                $respuesta['diferencias'] = $this->construirDiferenciasEntreAlbaranesYFactura($idFactura);
            }            
        }
        print_r(json_encode($respuesta));
    }

    private function construirArrayCamposObligatorioUpdate($post)
    {           
        $arrValidar['idcliente'] = $post['idcliente'];
        $arrValidar['nif'] = trim($post['nif_factura']);
        $arrValidar['fecha'] = $post['fecha'];        
        $arrValidar['diascobro'] = (trim($post['dias']) != '')? $post['dias']: 0;       
        $arrValidar['vencimiento'] = $post['vencimiento'];        
        return $arrValidar;
    }

    private function crearActualizarFilasProductosFactura($idFactura, $post)
    {           

        $retorno = false;
        $cont = 0;            
        $baseImponible = 0;        
        $ivaTotal = 0;
        $total = 0;   
        $descuentoAcumulado = 0;

        $descuentoTipo = (isset($post['descuentotipo']) && $post['descuentotipo'] > 0)? str_replace(",", ".", $post['descuentotipo']): 0;

        $factor = 1;
        if(isset($post['idOrigen']) && $post['idOrigen'] > 0){
            $factor = -1;
        }         
        
        foreach ($post['numeroOrden'] as $key => $value) {
            $tmp = [];                                      
            
            $tmp['idfactura'] = $idFactura;  
            $tmp['idproducto'] = $post['idArticulo'][$key];          
            $tmp['descripcion'] = $this->modeloProductoVenta->getNameProduct($post['idArticulo'][$key]);
            $tmp['unidad'] = $post['unidadArticulo'][$key];     
                
            $cantidadAntes = ($post['cantidadArticulo'][$key] != '')? str_replace(",", ".", $post['cantidadArticulo'][$key]): 0;
            $cantidad = $cantidadAntes * $factor;
            $tmp['cantidad'] = $cantidad;
            $precio = ($post['precioArticulo'][$key] != '')? str_replace(",", ".", $post['precioArticulo'][$key]): 0;
            $tmp['precio'] = $precio;
            $ivatipo = ($post['iva'][$key] != '')? $post['iva'][$key]: 0;
            $tmp['ivatipo'] = $ivatipo;
            $subTotal = UtilsHelper::redondearNumero($cantidad * $precio, REDONDEO_IMPORTE);   
                   
            $tmp['subtotal'] = $subTotal;
                        
            $tmp['descuentotipo'] = $post['descuentotipo'];
            $descuentoImporte = UtilsHelper::redondearNumero($subTotal * $descuentoTipo /100, REDONDEO_IMPORTE);            
            $descuentoAcumulado = $descuentoAcumulado + $descuentoImporte;            

            $baseImponible = $baseImponible + $subTotal;
            $bImpConDscto = $subTotal - $descuentoImporte;
            $ivaTotal = $ivaTotal + UtilsHelper::redondearNumero(($ivatipo * $bImpConDscto / 100), REDONDEO_IMPORTE);
         
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

                    $insRows = $this->modeloBase->updateRow($this->tablaRows, $strFieldsValues, $strWhere);

                    if($insRows){
                        $cont++;
                        $this->actualizarFilaAlbaranDesdeFilaFactura($post['idFila'][$key], $tmp);
                        $this->actualizarTotalesAlbaranesDesdeFilaFactura($post['idFila'][$key]);
                        
                    }
                }                    
            }else{

                
                $tmp['idfilaalbaran'] = 0;
                $tmp['idalbaran'] = 0;
            

                $arraFields = $this->arrFieldsRowsCreate; 

                $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $arraFields);
          

                $ok = $stringQueries['ok'];
                $strFields = $stringQueries['strFields'];
                $strValues = $stringQueries['strValues'];
                if($ok){
                    $insRows = $this->modeloBase->insertRow($this->tablaRows, $strFields, $strValues);
                    if($insRows){
                        $cont++;
                        $this->crearAlbaranDesdeFilaFactura($tmp, $insRows, $post);                
                    }
                }                      
            }                         
                        
        }        

        if($cont == count($post['numeroOrden'])){            
            $retorno = true;
            $this->baseImponible = $baseImponible;                   
            $this->descuentoTipo = $descuentoTipo;            
            $this->descuentoAcumulado = $descuentoAcumulado;            
            $this->ivaTotal = $ivaTotal;
            $this->total = $baseImponible - $descuentoAcumulado + $ivaTotal;
        }
        return $retorno;
       
    }    
    
    private function crearAlbaranDesdeFilaFactura($tmp, $idFilaFactura, $post)
    {
        $idAlbaran = $this->crearCabeceraAlbaranDesdeFactura($tmp, $idFilaFactura, $post);
        if($idAlbaran > 0){
            $idFilaAlbaran = $this->crearFilaAlbaranDesdeFactura($tmp, $idFilaFactura, $idAlbaran);    
            if($idFilaAlbaran > 0){
                $this->actualizarFilaFactura($idFilaFactura, $idAlbaran, $idFilaAlbaran);
            }        
        }        
    }

    private function crearCabeceraAlbaranDesdeFactura($datosFila, $idFilaFactura, $post)
    {
        $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla_clientes_albaranes, date("Y",strtotime($post['fecha'])));
        $ceros = '';
        if($nextCodeInterno==1 || ($nextCodeInterno > 1 && $nextCodeInterno <= 9)){
            $ceros = '00';
        }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
            $ceros = '0';
        }
        $post['numerointerno'] = $nextCodeInterno;                
        $post['numero'] = "ALB".date("y",strtotime($post['fecha'])).".".$ceros.$nextCodeInterno;
        $post['cliente'] = $this->modeloCliente->getNameClient($post['idcliente']);

        $cantidad = ($datosFila['cantidad'] != '')? str_replace(",", ".", $datosFila['cantidad']): 0;
        $precio = ($datosFila['precio'] != '')? str_replace(",", ".", $datosFila['precio']): 0;
        $ivatipo = ($datosFila['ivatipo'] != '')? $datosFila['ivatipo']: 0;   
        
        $baseImponible = $cantidad * $precio;            
        $ivaTotal = $ivatipo * $baseImponible / 100;
        $total = $baseImponible + $ivaTotal;

        $post['total'] = $total;
        $post['ivatotal'] = $ivaTotal;
        $post['baseimponible'] = $baseImponible;       

      

        $stringQueries = UtilsHelper::buildStringsInsertQueryNuevo2($post, $this->arrFieldsCreateAlbaran);      

        $ok = $stringQueries['ok'];
        $strFields = $stringQueries['strFields'];
        $strValues = $stringQueries['strValues'];
              
        $idAlbaran = 0;
        if($ok){
            $idAlbaran = $this->modeloBase->insertRow($this->tabla_clientes_albaranes, $strFields, $strValues);
            $idFactura = $datosFila['idfactura'];
            $this->modeloBase->updateRow($this->tabla_clientes_albaranes, " idfactura = '$idFactura' ", " id = '$idAlbaran' ");
            $this->modeloBase->updateRow($this->tabla_clientes_albaranes, " estado = 'facturado' ", " id = '$idAlbaran' ");
        }       
        return $idAlbaran;   
    }

    private function crearFilaAlbaranDesdeFactura($datosFila, $idFilaFactura, $idAlbaran)
    {                
        $datosFila['idalbaran'] = $idAlbaran;
        $stringQueries = UtilsHelper::buildStringsInsertQueryNuevo2($datosFila, $this->arrFieldsRowsCreateAlbaran);
                           
        $ok = $stringQueries['ok'];
        $strFields = $stringQueries['strFields'];
        $strValues = $stringQueries['strValues'];
        $idFilaAlbaran = 0;
        if($ok){
            $idFilaAlbaran = $this->modeloBase->insertRow($this->tabla_clientes_albaranes_det, $strFields, $strValues);            
        }   
        return $idFilaAlbaran;
    }

    private function actualizarFilaFactura($idFilaFactura, $idAlbaran, $idFilaAlbaran)
    {        
        $this->modeloBase->updateRow($this->tablaRows, " idfilaalbaran = '$idFilaAlbaran', idalbaran = '$idAlbaran' ", " id = '$idFilaFactura' ");
    }

    private function actualizarTotalesAlbaranesDesdeFilaFactura($idFilaFactura)
    {
        $idAlbaran = $this->modeloFacturaDetalleCliente->getIdDeliveryInvoiceByIdRowInvoice($idFilaFactura);        
        $totales = $this->modeloAlbaranDetalleCliente->getTotalsAlbaran($idAlbaran);

        $datos['baseimponible'] = $totales->suma_base_imponible;
        $datos['ivatotal'] = $totales->suma_iva;
        $datos['total'] = $totales->total_final;        
        $datos['id'] = $idAlbaran; 

        $this->modeloAlbaranCliente->updateDeliveryNoticeHead($datos);

    }

    
    private function actualizarFilaAlbaranDesdeFilaFactura($idFilaFactura, $fila)
    {
        $idFilaAlbaran = $this->modeloFacturaDetalleCliente->getIdFilaAlbaran($idFilaFactura);        
             
        $arraFields = $this->arrFieldsRowsUpdateAlbaran;
        $arrWhere['id'] = $idFilaAlbaran; 

        $stringQueries = UtilsHelper::buildStringsUpdateQuery($fila, $arraFields);
        $ok = $stringQueries['ok'];                        
            
        $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
        $okw = $stringWhere['ok'];                                           
                    
        if($ok && $okw){
            $strFieldsValues = $stringQueries['strFieldsValues'];
            $strWhere = $stringWhere['strWhere'];                  

            $ins = $this->modeloBase->updateRow($this->tabla_clientes_albaranes_det, $strFieldsValues, $strWhere);    
        }            
        
    }

    public function actualizarFactura()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;     
             
        
        if(isset($_POST['id']) && $_POST['id'] != '' && $_POST['id'] > 0 && isset($_POST['idcliente']) && $_POST['idcliente'] > 0 && trim($_POST['nif_factura']) != '' && $_POST['fecha'] != '' && trim($_POST['dias']) != '' && $_POST['vencimiento'] != '' && isset($_POST['numeroOrden']) && count($_POST['numeroOrden']) > 0 ){
                            
                $idFactura = $_POST['id'];
          
                $idOrigen = $this->modeloFacturaCliente->getIdInvoiceOriginToNegative($_POST['id']);
                $_POST['idOrigen'] = $idOrigen;
                $updRows = $this->crearActualizarFilasProductosFactura($idFactura, $_POST);

                if($updRows){                    

                    $arrWhere['id'] = $idFactura;        
                   
                    $_POST['cliente'] = $this->modeloCliente->getNameClient($_POST['idcliente']);
                    
                    $_POST['ivatotal'] = $this->ivaTotal;
                    $_POST['baseimponible'] = $this->baseImponible; 
                    $_POST['total'] = $this->total;                    
                    $_POST['descuentotipo'] = $this->descuentoTipo;
                    $_POST['descuentoimporte'] = $this->descuentoAcumulado;
                    
                    $dias = (isset($_POST['dias']) && $_POST['dias'] != '' && $_POST['dias'] > 0)? $_POST['dias']: 0;
                    $_POST['diascobro'] = $_POST['dias'];
                    $_POST['vencimiento'] =  DateTimeHelper::calcularFechaFin($_POST['fecha'],$dias);
                    
                    $_POST['idcuentabancaria'] = (isset($_POST['cuentabancaria']) && trim($_POST['cuentabancaria']) != '')? $_POST['cuentabancaria']: '';
                    $_POST['idformacobro'] = (isset($_POST['formacobro']) && $_POST['formacobro'] != '')? $_POST['formacobro']: '';

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

                            $respuesta['html'] = $this->construirBodyTablaGrilla($idFactura, 'factura_cliente');
                            $respuesta['error'] = false;
                            $respuesta['mensaje'] = OK_ACTUALIZACION;
                            $new = $this->modeloFacturaCliente->getInvoiceData($idFactura);   
                            $respuesta['total'] = number_format($new->total,2,",",".");
                            $respuesta['ivatotal'] = number_format($new->ivatotal,2,",",".");
                            $respuesta['baseimponible'] = number_format($new->baseimponible,2,",",".");
                            $respuesta['descuentotipo'] = number_format($new->descuentotipo,2,",",".");
                            $signo = ($new->descuentoimporte > 0)? "- ": "";
                            $respuesta['descuentoimporte'] = $signo . number_format($new->descuentoimporte,2,",",".");
                            $respuesta['estado'] = $estado;
                            $respuesta['formacobro'] = $new->idformacobro;                              
                        }
                    }   

                }
                                 
        }else{            
            $arrValidar = $this->construirArrayCamposObligatorioUpdate($_POST);
            $fieldsValidate = UtilsHelper::validateRequiredFields($arrValidar, $this->arrFieldsValidateUpdate);
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
            $respuesta['fieldsValidate'] = $fieldsValidate;                   
        } 
        
        echo json_encode($respuesta);
    }

    private function actualizarEstadoFactura($idFactura)
    {
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

      
        
        $this->modeloBase->updateFieldTabla($this->tabla, 'estado', $estado, $idFactura);
        
        return $estado;

    }

    public function facturacionMasiva()
    {        
        $clientes = $this->modeloCliente->getEnabledClients();
        $dias_cobro  = $this->modeloCondiciones->getPaymentConditions();
        $datos = [            
            'clientes' => $clientes,
            'dias_cobro' => $dias_cobro,
            'formacobro' => $this->modeloFormasPago->getPaymentForms(),
            'cuentasbancarias' => $this->modeloCuentasBancarias->getBankAccounts()
        ];
        $this->vista('facturasCliente/facturacionMasiva', $datos);
    }

    public function obtenerAlbaranesConFiltros()
    {

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   
        
        if(isset($_POST['fechainicio']) && $_POST['fechainicio'] != '' && isset($_POST['fechafin']) && $_POST['fechafin'] != '' && isset($_POST['idclientesearch']) && $_POST['idclientesearch'] > 0){
            
            $idCliente = $_POST['idclientesearch'];
            $query_search = " alb.idcliente = '$idCliente' ";

            $fechaInicio = $_POST['fechainicio'];
            $fechaFin = $_POST['fechafin'];

            $query_search .= "AND ( alb.fecha BETWEEN '$fechaInicio' AND '$fechaFin' ) ";
            
            if (isset($_POST['estado_albaran']) && $_POST['estado_albaran'] == 'todos'){
                $query_search .= ' AND 1 ';
            }else{
                $estado= $_POST['estado_albaran'];
                $query_search .= "AND alb.estado = '$estado' ";
            }

         

            $resultado = $this->modeloAlbaranCliente->getDeliveryNotesSearchModified($query_search);

            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';                
            $respuesta['html_albaranes'] = TemplateHelper::buildGridDeliveryNotesSearchModified($resultado);

        }else{
            $respuesta['mensaje'] = 'Heu de seleccionar la menys un client';
        }
        
        
        echo json_encode($respuesta);
    }

    public function obtenerAlbaranesConFiltrosParaAlbaranCliente()
    {

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   
        
        if(isset($_POST['fechainicio']) && $_POST['fechainicio'] != '' && isset($_POST['fechafin']) && $_POST['fechafin'] != '' && isset($_POST['idFacturaAgregarAlbaran']) && $_POST['idFacturaAgregarAlbaran'] > 0){
            
            $idFactura = $_POST['idFacturaAgregarAlbaran'];
            $idCliente = $this->modeloFacturaCliente->getInvoiceData($idFactura)->idcliente;
            $query_search = " idcliente = '$idCliente' ";

            $fechaInicio = $_POST['fechainicio'];
            $fechaFin = $_POST['fechafin'];

            $query_search .= "AND ( fecha BETWEEN '$fechaInicio' AND '$fechaFin' ) ";
            
            $estado= $_POST['estado_albaran'];
            $query_search .= "AND estado = '$estado' ";         
            $resultado = $this->modeloAlbaranCliente->getDeliveryNotesSearch($query_search);

            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';                
            $respuesta['html_albaranes'] = TemplateHelper::buildGridDeliveryNotesSearch($resultado);

        }                
        echo json_encode($respuesta);
    }

    public function obtenerAlbaranFila(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;               

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
         
            $idAlbaran = $this->fetch['id'];
        
            $sel = $this->modeloAlbaranCliente->getAlbaranDataFacturaMasiva($idAlbaran);
            if ($sel) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';                
                $respuesta['html_albaran'] = TemplateHelper::buildGridDeliveryNotesToInvoice($sel);
            }
        }                       
        print_r(json_encode($respuesta));
    }

    private function clienteCabeceraClienteAlbaranesCorrecto($post)
    {
        $strIdesAlbaran = implode(",", $post['idAlbaranSelected']);              
              
        $clientesAlbaranes = $this->modeloAlbaranCliente->getClientFromStringDeliveryNotesToInvoices($strIdesAlbaran, $post['idcliente']);
      
        if(isset($clientesAlbaranes)&& count($clientesAlbaranes) > 0){
            $arr = [];
            foreach ($clientesAlbaranes as $key) {
                array_push($arr, $key->numero);
            }           
            $retorno['correcto'] = false;
            $albaranes = implode("  ", $arr);
            $cliente = $this->modeloCliente->getNameClient($post['idcliente']);
            $retorno['mensaje'] = 'Els albarans següents no pertanyen al client '.$cliente.' : '.$albaranes;

        }else{
            $retorno['correcto'] = true;
        }        
        return $retorno;
    }
 
    
    public function crearFacturaMasivaCliente(){

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_CREACION;
       
        
        if(isset($_POST['idcliente']) && $_POST['idcliente'] != '' && $_POST['idcliente'] > 0 && trim($_POST['nif_factura']) != '' && $_POST['fecha_factura_cliente'] != '' && trim($_POST['fecha_factura_cliente']) != '' && trim($_POST['dias_albaran_cliente']) != '' && $_POST['vencimiento_cliente']!= ''  && isset($_POST['idAlbaranSelected']) && count($_POST['idAlbaranSelected']) > 0 ){

            $verificarCliente = $this->clienteCabeceraClienteAlbaranesCorrecto($_POST);
         
            if($verificarCliente['correcto']){
                $ins = $this->crearCabeceraFacturaMasiva($_POST);
            
                if($ins){

                    $totalesFactura = $this->totalizarTodosLosAlbaranesUnaFactura($_POST['idAlbaranSelected'],false);
    
                    $this->modeloBase->updateFieldTabla($this->tabla, 'baseimponible', $totalesFactura->suma_subtotal, $ins);
                    $this->modeloBase->updateFieldTabla($this->tabla, 'ivatotal', $totalesFactura->importe_iva, $ins);
                    $this->modeloBase->updateFieldTabla($this->tabla, 'total', $totalesFactura->suma_total, $ins);
    
                    $todasLasFilas = $this->todosLosDetallesAlbaranesSeleccionados($_POST['idAlbaranSelected']);
                    
                    $insRows = $this->guardarFilasFacturaMasiva($ins, $todasLasFilas);
                    
                    if($insRows){                    
                        $strIdesAlbaran = implode(",", $_POST['idAlbaranSelected']);
    
                        $this->modeloBase->updateFieldTablaByStringIn($this->tabla_clientes_albaranes, 'estado', 'facturado', $strIdesAlbaran);
                        $this->modeloBase->updateFieldTablaByStringIn($this->tabla_clientes_albaranes, 'idfactura', $ins, $strIdesAlbaran);
                        $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_clientes_albaranes_det, 'idfactura', $ins, 'idalbaran', $strIdesAlbaran);
    
                        $this->crearReciboDesdeFactura($ins);
                        
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_CREACION; 
                        $respuesta['idfactura'] = $ins; 
                        
                    }   
                             
                }
            
            }else{
                $respuesta['error'] = true;                                
                $respuesta['mensaje'] = $verificarCliente['mensaje'];
            }

        }else{                                       

            $arrValidar = $this->construirArrayCamposObligatorioFacturaMasiva($_POST);
            $fieldsValidate = UtilsHelper::validateRequiredFields($arrValidar, $this->arrFieldsValidateUpdate);
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
            $respuesta['fieldsValidate'] = $fieldsValidate;
        }        
        print_r(json_encode($respuesta));
    }

    public function agregarAlbaranesPendientesAFactura(){

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_CREACION;       
        
        if(isset($_POST['idFacturaEnviarAlbaran']) && $_POST['idFacturaEnviarAlbaran'] != '' && $_POST['idFacturaEnviarAlbaran'] > 0 && isset($_POST['idAlbaranSelected']) && count($_POST['idAlbaranSelected']) > 0 ){            
                     
                    $idFactura = $_POST['idFacturaEnviarAlbaran'];                   
    
                    $todasLasFilas = $this->todosLosDetallesAlbaranesSeleccionados($_POST['idAlbaranSelected']);
                    
                    $insRows = $this->guardarFilasFacturaMasiva($idFactura, $todasLasFilas);
                    
                    if($insRows){                         
                        $totalesFactura = $this->modeloFacturaDetalleCliente->getTotalsInvoice($idFactura);                      
                       
                        $suma_base_imponible = (isset($totalesFactura->suma_base_imponible) && $totalesFactura->suma_base_imponible > 0)? $totalesFactura->suma_base_imponible: 0;

                        $suma_descuento = (isset($totalesFactura->suma_descuento) && $totalesFactura->suma_descuento > 0)? $totalesFactura->suma_descuento: 0;

                        $suma_iva = (isset($totalesFactura->suma_iva) && $totalesFactura->suma_iva > 0)? $totalesFactura->suma_iva: 0;
                        $total_final = (isset($totalesFactura->total_final) && $totalesFactura->total_final > 0)? $totalesFactura->total_final: 0;
                        

                        $this->modeloBase->updateFieldTabla($this->tabla, 'baseimponible', $suma_base_imponible, $idFactura);
                        $this->modeloBase->updateFieldTabla($this->tabla, 'ivatotal', $suma_iva, $idFactura);
                        $this->modeloBase->updateFieldTabla($this->tabla, 'total', $total_final, $idFactura);
                        $this->modeloBase->updateFieldTabla($this->tabla, 'descuentoimporte', $suma_descuento, $idFactura);


                        $strIdesAlbaran = implode(",", $_POST['idAlbaranSelected']);    
                        $this->modeloBase->updateFieldTablaByStringIn($this->tabla_clientes_albaranes, 'estado', 'facturado', $strIdesAlbaran);
                        $this->modeloBase->updateFieldTablaByStringIn($this->tabla_clientes_albaranes, 'idfactura', $idFactura, $strIdesAlbaran);
                        $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_clientes_albaranes_det, 'idfactura', $idFactura, 'idalbaran', $strIdesAlbaran);
                        
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_CREACION; 
                        $respuesta['idfactura'] = $idFactura; 
                        $respuesta['html'] = $this->construirBodyTablaGrilla($idFactura, 'factura_cliente');
                        $respuesta['total'] = $total_final;
                        $respuesta['ivatotal'] = $suma_iva;
                        $respuesta['baseimponible'] = $suma_base_imponible;
                        $respuesta['descuentoimporte'] = $suma_descuento;
                        $estado = $this->actualizarEstadoFactura($idFactura);
                        $respuesta['estado'] = $estado;
                        
                    }                                           

        }else{                                             
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }        
        print_r(json_encode($respuesta));

    }
    
    private function construirArrayCamposObligatorioFacturaMasiva($post)
    {           
        $arrValidar['idcliente'] = (isset($post['idcliente']))? $post['idcliente']:'' ;
        $arrValidar['nif'] = trim($post['nif_factura']);
        $arrValidar['fecha'] = $post['fecha_factura_cliente'];        
        $arrValidar['diascobro'] = (trim($post['dias_albaran_cliente']) != '')? $post['dias_albaran_cliente']: 0;       
        $arrValidar['vencimiento'] = $post['vencimiento_cliente'];        
        return $arrValidar;
    }

    private function crearCabeceraFacturaMasiva($post)
    {
            $ins = false;  
            
            $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla, date("Y",strtotime($post['fecha_factura_cliente'])));
            $arrValues['numerointerno'] = $nextCodeInterno;  
            $ceros = '';
            if($nextCodeInterno >= 1 && $nextCodeInterno <= 9){
                $ceros = '00';
            }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
                $ceros = '0';
            }                 
            $arrValues['numero'] = "FRA".date("y",strtotime($_POST['fecha_factura_cliente']))."/".$ceros.$nextCodeInterno;      
            $arrValues['idcliente'] = $post['idcliente'];
            $arrValues['cliente'] = $this->modeloCliente->getNameClient($post['idcliente']);
            $arrValues['fecha'] = $post['fecha_factura_cliente'];
            $arrValues['baseimponible'] = 0;
            $arrValues['ivatotal'] = 0;
            $arrValues['total'] = 0;
            $arrValues['observaciones'] = (trim($post['observaciones'])!= '')? $post['observaciones']: '';               
            $diascobro = (trim($post['dias_albaran_cliente']) != '')? trim($post['dias_albaran_cliente']): 0;
            $arrValues['diascobro'] = $diascobro;
            $arrValues['vencimiento'] = DateTimeHelper::calcularFechaFin($_POST['fecha_factura_cliente'], $diascobro);   
            $arrValues['idcuentabancaria'] = (isset($post['cuentabancaria']) && $post['cuentabancaria'] > 0)? $post['cuentabancaria']: '';
            $arrValues['idformacobro'] = (isset($post['formacobro']) && $post['formacobro'] != '')? $post['formacobro']: '';
            
            $stringQueries = UtilsHelper::buildStringsInsertQuery($arrValues, $this->arrFieldsCreate);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];

            /*
            echo"<br>entra a crear cabecera<br>";
            print_r($stringQueries);
            die;
            */
                       
            if($ok){
                $ins = $this->modeloBase->insertRow($this->tabla, $strFields, $strValues);                
            }                                
            return $ins;        
    }  

    private function todosLosDetallesAlbaranesSeleccionados($idAlbaranSelected)
    {
        $strIdesAlbaran = implode(",", $idAlbaranSelected);
        $rows = $this->modeloAlbaranCliente->getRowsDeliveryNotesSelected($strIdesAlbaran);
        return $rows;        
    }

    private function totalizarTodosLosAlbaranesUnaFactura($idAlbaranSelected, $group=false)
    {
        $strIdesAlbaran = implode(",", $idAlbaranSelected);
        $groupby = '';
        if($group){
            $groupby = '  GROUP BY det.descripcion ASC, det.precio, det.ivatipo ASC,   ';  
        }
        $totales = $this->modeloAlbaranCliente->getTotalsInvoices($strIdesAlbaran, $groupby);       
        return $totales;
    }

    private function guardarFilasFacturaMasiva($ins, $filas)
    {
        $retorno = false;
        $cont = 0;              

        $descuentoTipo = $this->modeloFacturaCliente->getDiscountTypetInvoice($ins);

        foreach ($filas as $key) {
            
            $tmp = [];
            
            $tmp['idfactura'] = $ins;     
            $tmp['idproducto'] = $key->idproducto;       
            $tmp['descripcion'] = $key->descripcion;
            $tmp['unidad'] = $key->unidad; 
            $cantidad = $key->suma_cantidad;
            $tmp['cantidad'] = $cantidad;
            $subtotal = $key->suma_subtotal;
            $precio = ($cantidad > 0)? round($subtotal/$cantidad,2): 0;
            $tmp['precio'] = $precio;
            $tmp['subtotal'] = $subtotal;
            $tmp['ivatipo'] = $key->ivatipo;     
            $tmp['idfilaalbaran'] = $key->idfilaalbaran;            
            $tmp['idalbaran'] = $key->idalbaran;                       

            $tmp['descuentotipo'] = $descuentoTipo;   

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

    public function exportarPdfFactura($idFactura)
    {                        
        $cabecera = $this->modeloFacturaCliente->getInvoiceDataDocument($idFactura);       
        $datos['cabecera'] = $cabecera;
        $datos['detalle'] = $this->modeloFacturaDetalleCliente->getRowsInvoiceWithRowsDatesNoticesDelivery($idFactura);        
        $datos['razonsocialpiensos'] = $this->modeloConfiguracion->getBusinessName();
        $datos['tipo'] = 'factura';
        $datos['tiporazonsocial'] = 'cliente';



        $rectificativa = 0;
        if(isset($cabecera->idfacturaorigen) && $cabecera->idfacturaorigen > 0){
            $rectificativa = $cabecera->idfacturaorigen;
            $datos['numFacturaOrigen'] = $this->modeloFacturaCliente->getInvoiceNumberByIdFactura($cabecera->idfacturaorigen);
        }
        $datos['rectificativa'] = $rectificativa;
                
        generarPdf::documentoPDFExportar('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0), true, 'documentos', 'factura.php', $datos);        
    }

    public function altaFacturaRectificativa()
    {          
        if(isset($_POST['idOrigenRectificativa']) && $_POST['idOrigenRectificativa'] > 0){            
            $_SESSION['idOrigenRectificativa'] = $_POST['idOrigenRectificativa'];
            redireccionar('/FacturasClientes/crearFacturaRectificativa/');        
        }        
    }

    public function crearFacturaRectificativa(){
    
        $idFacturaOrigen = $_SESSION['idOrigenRectificativa'];
        $datos = [];
        if($this->modeloBase->existIdInvoice($this->tabla, $idFacturaOrigen) > 0){
            $cab = $this->modeloFacturaCliente->getInvoiceData($idFacturaOrigen);            
            $cabecera = (array) $cab;                
            $detalle['html'] = $this->construirBodyTablaGrillaFacturaNegativa($idFacturaOrigen, 'factura');            

            $tmp = [
                'idFacturaOrigen' => $idFacturaOrigen,                
                'formacobro' => $this->modeloFormasPago->getPaymentForms(),
                'cuentasbancarias' => $this->modeloCuentasBancarias->getBankAccounts()
            ];
            
            $datos = array_merge($tmp, $cabecera, $detalle);   
        }       
        $this->vista('facturasCliente/altaFacturaRectificativa', $datos);
    }

    private function construirBodyTablaGrillaFacturaNegativa($idFactura, $tipoDoc){

        $rows = $this->modeloFacturaDetalleCliente->getRowsInvoice($idFactura);
        $datos = [                        
            'tiposIva' => $this->modeloIva->getAllIvasActive()
        ]; 

        $html = TemplateHelper::buildGridRowsNegativeInvoice($rows, $datos, $tipoDoc);
        return $html;
    }

    public function guardarFacturaRectificativa()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;     

          
        if(isset($_POST['idFacturaOrigen']) && $_POST['idFacturaOrigen'] != '' && $_POST['idFacturaOrigen'] > 0 && $_POST['fecha'] != '' && isset($_POST['numeroOrden']) && count($_POST['numeroOrden']) > 0 ){
                            
                    $idFactura = $_POST['idFacturaOrigen'];
                    $ins = $this->crearCabeceraFacturaRectificativa($_POST);

                    if($ins){
                        $updRows = $this->guardarFilasProductosFacturaectificativa($ins, $_POST, $idFactura);
                        if($updRows){
                            $respuesta['error'] = false;
                            $respuesta['mensaje'] = OK_ACTUALIZACION;
                            $respuesta['idfactura'] = $ins;

                            $this->modeloBase->updateFieldTabla($this->tabla, 'baseimponible', $this->baseImponible, $ins);
                            $this->modeloBase->updateFieldTabla($this->tabla, 'ivatotal', $this->ivaTotal, $ins);
                            $this->modeloBase->updateFieldTabla($this->tabla, 'total', $this->total, $ins);
                        }
                    }                                                    
                     
        }else{            
            $arrValidar = $this->construirArrayCamposObligatoriosRectificativa($_POST);
            $fieldsValidate = UtilsHelper::validateRequiredFields($arrValidar, $this->fieldsValidateNegInvoice);
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
            $respuesta['fieldsValidate'] = $fieldsValidate;                   
        } 
        
        echo json_encode($respuesta);
    }

    private function construirArrayCamposObligatoriosRectificativa($post)
    {                           
        $arrValidar['fecha'] = $post['fecha'];                
        $arrValidar['idFacturaOrigen'] = $post['idFacturaOrigen'];        
        return $arrValidar;
    }

    private function crearCabeceraFacturaRectificativa($post)
    {
            $ins = false;  
            
            $dataInvoice = $this->modeloFacturaCliente->getInvoiceData($post['idFacturaOrigen']);

            $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla, date("Y",strtotime($post['fecha'])));
            $arrValues['numerointerno'] = $nextCodeInterno;
            $ceros = '';
            if($nextCodeInterno >= 1 && $nextCodeInterno <= 9){
                $ceros = '00';
            }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
                $ceros = '0';
            }
            $arrValues['numero'] = "FRA".date("y",strtotime($post['fecha']))."/".$ceros.$nextCodeInterno;      
            $arrValues['idcliente'] = $dataInvoice->idcliente;
            $arrValues['cliente'] = $this->modeloCliente->getNameClient($dataInvoice->idcliente);
            $arrValues['fecha'] = $post['fecha'];
            $arrValues['baseimponible'] = 0;
            $arrValues['ivatotal'] = 0;
            $arrValues['total'] = 0;
            $arrValues['observaciones'] = (trim($post['observaciones'])!= '')? $post['observaciones']: '';               
            $diascobro = 0;
            $arrValues['diascobro'] = $diascobro;
            $arrValues['vencimiento'] = DateTimeHelper::calcularFechaFin($post['fecha'], $diascobro);   
            $arrValues['idcuentabancaria'] = '';
            $arrValues['idformacobro'] = '';
            $arrValues['idfacturaorigen'] = $post['idFacturaOrigen'];
                        
            $stringQueries = UtilsHelper::buildStringsInsertQuery($arrValues, $this->arrFieldsCreateRectificativa);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];
                       
            if($ok){
                $ins = $this->modeloBase->insertRow($this->tabla, $strFields, $strValues);                
            }                                
            return $ins;        
    }  

    
    private function guardarFilasProductosFacturaectificativa($ins, $post, $idFactura)
    {
        $retorno = false;
        $cont = 0;            
        $baseImponible = 0;        
        $ivaTotal = 0;
        $total = 0;   

        $descuentoTipo = $this->modeloFacturaCliente->getDiscountTypetInvoice($idFactura);

        foreach ($post['numeroOrden'] as $key => $value) {
            $tmp = [];            
            
            $tmp['idfactura'] = $ins;            
            $tmp['descripcion'] = $post['descripcion'][$key];
            $tmp['unidad'] = $post['unidadArticulo'][$key];           
            $cantidadPositiva = ($post['cantidadArticulo'][$key] != '')? str_replace(",", ".", $post['cantidadArticulo'][$key]): 0;
            $cantidad = $cantidadPositiva * -1;
            $tmp['cantidad'] = $cantidad;
            $precio = ($post['precioArticulo'][$key] != '')? str_replace(",", ".", $post['precioArticulo'][$key]): 0;            
            $tmp['precio'] = $precio;
            $ivatipo = ($post['iva'][$key] != '')? $post['iva'][$key]: 0;
            $tmp['descuentotipo'] = $descuentoTipo;
            $tmp['ivatipo'] = $ivatipo;
            $subTotal = $cantidad * $precio;
            $tmp['subtotal'] = $subTotal;
            $tmp['idfilaalbaran'] = 0; //se asigna cero porque la fila puedeser modificada desde la factura
            $tmp['idalbaran'] = 0; //se asigna cero porque la fila puedeser modificada desde la factura
                             
            $baseImponible = $baseImponible + $subTotal;
            $ivaTotal = $ivaTotal + ($ivatipo * $subTotal / 100);                  

            $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $this->arrFieldsRowsCreate);
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

        if($cont == count($post['numeroOrden'])){            
            $retorno = true;
            $this->baseImponible = $baseImponible;
            $this->ivaTotal = $ivaTotal;
            $this->total = $baseImponible + $ivaTotal;
        }
        return $retorno;
       
    }


    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);          
            
            $where = str_replace("fecha like", "DATE_FORMAT(fac.fecha, '%d/%m/%Y') like", $where);        

            $order = " ORDER BY fac.fecha ASC ";          
         
            $datos = $this->modeloFacturaCliente->obtenerFacturasClientesExportar($order,$where);
            $nombreReporte = '_FacturasClientes';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }

    public function obtenerDatosEnvioEmailFactura()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idFactura = $this->fetch['id'];               
            
            $cabecera = $this->modeloFacturaCliente->getInvoiceData($idFactura);
            

            if(isset($cabecera->id) && $cabecera->id > 0 && $cabecera->numero != "" && $cabecera->idcliente > 0 ){
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';
            }

            // Agregar los contactos del cliente
            $idCliente = $cabecera->idcliente;
            $respuesta['contactos'] = json_decode($this->modeloCliente->getClientById($idCliente)->contactos);

        }                
        echo json_encode($respuesta);
    }


    public function eliminarFilaDetalle()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($this->fetch['idFila']) && $this->fetch['idFila'] > 0 && $this->fetch['idFactura'] > 0) {

            $idFilaFactura = $this->fetch['idFila'];            
            $idFilaAlbaranEliminar = $this->modeloFacturaDetalleCliente->getIdFilaAlbaran($idFilaFactura);
            $idAlbaran = $this->modeloFacturaDetalleCliente->getIdDeliveryInvoiceByIdRowInvoice($idFilaFactura);
            
            $where = " id = $idFilaFactura ";                        
            $delFila = $this->modeloBase->deleteRow($this->tablaRows, $where);        

            if ($delFila) {
                                
                $this->actualizarTotalesFactura($this->fetch['idFactura']);

                if($idFilaAlbaranEliminar > 0){                    

                    //$this->modeloBase->deleteRow($this->tabla_clientes_albaranes_det, "id = ".$idFilaAlbaranEliminar);
                    $this->modeloBase->updateFieldTabla($this->tabla_clientes_albaranes_det, 'idfactura', 0, $idFilaAlbaranEliminar);
                    $this->modeloBase->updateFieldTabla($this->tabla_clientes_albaranes, 'idfactura', 0, $idAlbaran);
                    $this->modeloBase->updateFieldTabla($this->tabla_clientes_albaranes, 'estado', 'pendiente', $idAlbaran);

                    $this->actualizarTotalesAlbaran($idAlbaran);
                    $this->verificarEliminarCabeceraAlbaran($idAlbaran);   
                    $idFactura = $this->fetch['idFactura'];                    
                    $estado = $this->actualizarEstadoFactura($idFactura);
                    $respuesta['estado'] = $estado;
                }                

                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;       
                $respuesta['datos'] = $this->modeloFacturaDetalleCliente->getTotalsInvoiceFormat($this->fetch['idFactura']);
                

            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }

    private function verificarEliminarCabeceraAlbaran($idAlbaran)
    {
        $albaran = $this->modeloAlbaranCliente->getAlbaranData($idAlbaran);
        if(!isset($albaran->total) || $albaran->total == 0){
            $this->modeloBase->deleteRow($this->tabla_clientes_albaranes, " id = '$idAlbaran' ");
        }
    }

    public function actualizarTotalesFactura($idFactura)
    {
        $totales = $this->modeloFacturaDetalleCliente->getTotalsInvoice($idFactura);      

        $arrFieldsValues['baseimponible'] = $totales->suma_base_imponible;      
        $arrFieldsValues['descuentoimporte'] = $totales->suma_descuento;
        $arrFieldsValues['ivatotal'] = $totales->suma_iva;
        $arrFieldsValues['total'] = $totales->total_final;
        $fieldsValuesString = UtilsHelper::buildStringsFieldsUpdateQuery($arrFieldsValues);
       
        $arrWhere['id'] = $idFactura;
        $whereString = UtilsHelper::buildStringsWhereQueryOnly($arrWhere);   
        $upd = $this->modeloBase->updateRow($this->tabla, $fieldsValuesString, $whereString);
    }

    public function actualizarTotalesAlbaran($idAlbaran)
    {
        $totales = $this->modeloAlbaranDetalleCliente->getTotalsAlbaran($idAlbaran);

        $datos['baseimponible'] = (isset($totales->suma_base_imponible) && $totales->suma_base_imponible > 0)? $totales->suma_base_imponible : 0;
        $datos['ivatotal'] = (isset($totales->suma_iva) && $totales->suma_iva > 0)? $totales->suma_iva : 0;
        $datos['total'] = (isset($totales->total_final) && $totales->total_final > 0)? $totales->total_final: 0;
       
        $datos['id'] = $idAlbaran;

        $this->modeloAlbaranCliente->updateDeliveryNoticeHead($datos);
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

    public function consultarFacturaParaEliminar()
    {
        $respuesta['eliminar'] = false;
        $respuesta['mensaje'] = '';

        
        if(isset($this->fetch) && isset($this->fetch['id']) && $this->fetch['id'] > 0) {
            $idFactura = $this->fetch['id'];
            $recibos = $this->modeloReciboCliente->getReceiptsByIdInvoice($idFactura);

            if(count($recibos) > 0){
                $respuesta['eliminar'] = false;
                $respuesta['mensaje'] = 'No es pot eliminar la factura perquè hi ha rebuts vinculats: ';
                $respuesta['recibos'] = $recibos;
            }else{
                $respuesta['eliminar'] = true;
                $respuesta['albaranes'] = $this->modeloAlbaranCliente->getDeliveryNoteByIdInvoice($idFactura);
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
                $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_clientes_albaranes, 'estado', 'pendiente', 'idfactura', $idFactura);
                $this->desvincularAlbaranesCabecera($idFactura);
                $this->desvincularAlbaranesDetalle($idFactura);                
                //VALIDACION PARA FACTURAS RECTIFICATIVAS ---> FALTA ESTO
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
        $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_clientes_albaranes, 'idfactura', 0, 'idfactura', $idFactura);
    }

    private function desvincularAlbaranesDetalle($idFactura)
    {
        $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tabla_clientes_albaranes_det, 'idfactura', 0, 'idfactura', $idFactura);
    }

    public function obtenerEmailsFacturasEnviadas()
    {     
        $respuesta['error'] = true;        
        $respuesta['html'] = '';

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idFactura = $this->fetch['id'];               
            
            $envios = $this->modeloBase->getAllFieldsTablaByFieldFilter('emails_clientes_facturas', 'iddoc', $idFactura);

            if(isset($envios) && count($envios) > 0){
                $respuesta['error'] = false;
                $respuesta['html'] = TemplateHelper::buildHTMLListSentEmailsDocumento($envios);                
            }
        }                
        echo json_encode($respuesta);

    }
    
    

}
