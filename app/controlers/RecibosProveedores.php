<?php

class RecibosProveedores extends Controlador {

   

    public function __construct() {
        session_start();
        $this->tabla = 'proveedores_recibos'; 
        $this->tablaFacturasProveedor = 'proveedores_facturas';   
        $this->arrFieldsCreate = ['numero','fecha','importe','idfactura','concepto','lugarexpedicion','librado', 'librador','estado','vencimiento'];
        $this->arrFieldsUpdate = ['numero','fecha','importe','concepto','lugarexpedicion','librado', 'librador','vencimiento'];
        $this->modeloReciboProveedor = $this->modelo('ModeloReciboProveedor');    
        $this->modeloFacturaProveedor = $this->modelo('ModeloFacturaProveedor');                      
        $this->modeloBase = $this->modelo('ModeloBase');              
        
        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        }   
    
    }

    public function index($msg=0) {
        $datos = [         
        ];
        $this->vista('recibosProveedor/recibos', $datos);
    }
   
    public function tablaRecibosProveedor()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);       
        
        $where = str_replace("fecha like", "DATE_FORMAT(rec.fecha, '%d/%m/%Y') like", $where);
        $where = str_replace("numerofactura like", "fac.numero like", $where);
        $where = str_replace("estadoactual like", "IF(rec.estado='pagado',rec.estado,IF(rec.vencimiento < CURDATE(),'impagado','pendiente')) like", $where);
        
        $recibos = $this->modeloReciboProveedor->obtenerRecibosProveedoresTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReciboProveedor->obtenerTotalRecibosProveedor($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $recibos
        ];

        print_r(json_encode($salida));
    }  

    public function crearRecibo()
    {
        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_CREACION; 
        
        if(isset($_POST['idFactura']) && $_POST['idFactura'] != '' && trim($_POST['numero_recibo']) != '' && $_POST['fecha_recibo'] != '' && $_POST['importe_recibo'] != '' && $_POST['concepto_recibo'] != '' && $_POST['vencimiento_recibo'] != '' && isset($_POST['estado_recibo']) && $_POST['estado_recibo'] != ''){
            
            $arrValues =[
                'numero' => $_POST['numero_recibo'],
                'fecha' => $_POST['fecha_recibo'],
                'importe' => $_POST['importe_recibo'],
                'idfactura' => $_POST['idFactura'],
                'concepto' => $_POST['concepto_recibo'],
                'lugarexpedicion' => $_POST['lugar_recibo'],
                'librado' => $_POST['nombre_librado'],
                'librador'   => $_POST['nombre_librador'],
                'estado' => $_POST['estado_recibo'], //pagado,impagado,pendiente
                'vencimiento' => $_POST['vencimiento_recibo']
            ];

            $stringQueries = UtilsHelper::buildStringsInsertQuery($arrValues, $this->arrFieldsCreate);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];
                       
            if($ok){
                $ins = $this->modeloBase->insertRow($this->tabla, $strFields, $strValues);        
                
                if($ins){
                    $estado = $this->actualizarEstadoFactura($_POST['idFactura']);
                    $respuesta['error'] = false;
                    $respuesta['mensaje'] = OK_CREACION;
                    $recibos = $this->modeloReciboProveedor->getReceiptsByIdInvoice($_POST['idFactura']);
                    $respuesta['html_recibos'] = TemplateHelper::buildGridReceipt($recibos,'proveedor');
                    $respuesta['estado'] = $estado;
                }        
            }                       
        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO; 
        }
        
        print_r(json_encode($respuesta));
    }

    public function obtenerRecibosFactura()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    
        $respuesta['html_recibos'] = 'No hi ha recibos vinculats.';

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idFactura = $this->fetch['id'];
        
            $sel = $this->modeloReciboProveedor->getReceiptsByIdInvoice($idFactura);
            if ($sel) {
               
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';                
                $respuesta['html_recibos'] = TemplateHelper::buildGridReceipt($sel);
                
            }
        }                       
        print_r(json_encode($respuesta));
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
        $this->modeloBase->updateFieldTabla($this->tablaFacturasProveedor, 'estado', $estado, $idFactura);
        
        return $estado;

    }
  
    public function eliminarReciboFactura(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    
        $respuesta['html_recibos'] = 'No hi ha recibos vinculats.';

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idRecibo = $this->fetch['id'];
            $idFactura = $this->modeloReciboProveedor->getIdInvoiceByIdRecepit($idRecibo);
        
            $where = " id = $idRecibo ";
            $del = $this->modeloBase->deleteRow($this->tabla, $where);
            if ($del) {
                $estado = $this->actualizarEstadoFactura($idFactura);
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
                $recibos = $this->modeloReciboProveedor->getReceiptsByIdInvoice($idFactura);
                $respuesta['html_recibos'] = TemplateHelper::buildGridReceipt($recibos,'proveedor');
                $respuesta['estado'] = $estado;
            }
        }else{
            $respuesta['mensaje'] = ERROR_ELIMINACION;
        }
        print_r(json_encode($respuesta));
    }

    public function obtenerRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;        

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idRecibo = $this->fetch['id'];
            $datos = $this->modeloReciboProveedor->getRecepitById($idRecibo);        
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['datos'] = $datos;            
        }

        print_r(json_encode($respuesta));
    }

    public function actualizarRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;   
        
              
        if(isset($_POST['idRecibo']) && $_POST['idRecibo'] != '' && $_POST['idRecibo'] > 0 && trim($_POST['numero_recibo']) != '' && $_POST['fecha_recibo'] != '' && $_POST['fecha_recibo'] > 0 && $_POST['importe_recibo'] > 0 && $_POST['concepto_recibo'] != '' && $_POST['vencimiento'] != ''){
                            
            $idRecibo = $_POST['idRecibo'];       
            $idFactura = $this->modeloReciboProveedor->getIdInvoiceByIdRecepit($idRecibo);

            if($_POST['importe_recibo'] > $this->modeloFacturaProveedor->getTotalAmountInvoice($idFactura) ){
                $respuesta['error'] = true;
                $numeroFactura = $this->modeloFacturaProveedor->getInvoiceNumberByIdFactura($idFactura);
                $respuesta['mensaje'] = "L'import del recibo és major que l'import de la factura Nº ".$numeroFactura;

            }else{

                $arrValues =[
                    'numero' => $_POST['numero_recibo'],
                    'fecha' => $_POST['fecha_recibo'],
                    'importe' => $_POST['importe_recibo'],
                    //'idRecibo' => $_POST['idRecibo'],
                    'concepto' => $_POST['concepto_recibo'],
                    'lugarexpedicion' => $_POST['lugar_recibo'],
                    'librado' => $_POST['nombre_librado'],
                    'librador'   => $_POST['nombre_librador'],
                    'vencimiento'   => $_POST['vencimiento']
                ];                                
    
                $arrWhere['id'] = $idRecibo;  
    
                $stringQueries = UtilsHelper::buildStringsUpdateQuery($arrValues, $this->arrFieldsUpdate);
                $ok = $stringQueries['ok'];             
    
                $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
                $okw = $stringWhere['ok'];               
    
                if($ok && $okw){
                    $strFieldsValues = $stringQueries['strFieldsValues'];
                    $strWhere = $stringWhere['strWhere'];
            
                    $upd = $this->modeloBase->updateRow($this->tabla, $strFieldsValues, $strWhere);
            
                    if($upd){     
                        
                        $estado = $this->actualizarEstadoFactura($idFactura);
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_ACTUALIZACION;                              
                    }
                }   
    

            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
        } 
        
        echo json_encode($respuesta);
    }

    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);          
            
            $where = str_replace("fecha like", "DATE_FORMAT(rec.fecha, '%d/%m/%Y') like", $where);
            $where = str_replace("numerofactura like", "fac.numero like", $where);            
            $where = str_replace("estadoactual like", "IF(rec.estado='pagado',rec.estado,IF(rec.vencimiento < CURDATE(),'impagado','pendiente')) like", $where);

            $order = " ORDER BY rec.fecha ASC ";          
         
            $datos = $this->modeloReciboProveedor->obtenerRecibosProveedoresExportar($order,$where);
            $nombreReporte = '_RecibosProveedores';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }

    public function cambiarEstadoPagadoRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;        

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idRecibo = $this->fetch['id'];
            $upd = $this->modeloReciboProveedor->changeStatusReceipt($idRecibo,'pagado');
            if($upd){
                $idFactura = $this->modeloReciboProveedor->getIdInvoiceByIdRecepit($idRecibo);
                $this->actualizarEstadoFactura($idFactura);
                $respuesta['error'] = false;
                $respuesta['estado'] = $this->modeloReciboProveedor->getRecepitById($idRecibo)->estadoactual;
                $respuesta['mensaje'] = OK_ACTUALIZACION;
            }            
        }
        print_r(json_encode($respuesta));
    }

    public function cambiarEstadoNoPagadoRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;        

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idRecibo = $this->fetch['id'];
            $upd = $this->modeloReciboProveedor->changeStatusReceipt($idRecibo,'pendiente');
            if($upd){
                $respuesta['error'] = false;
                $idFactura = $this->modeloReciboProveedor->getIdInvoiceByIdRecepit($idRecibo);
                $this->actualizarEstadoFactura($idFactura);
                $respuesta['estado'] = $this->modeloReciboProveedor->getRecepitById($idRecibo)->estadoactual;
                $respuesta['mensaje'] = OK_ACTUALIZACION;
            }            
        }
        print_r(json_encode($respuesta));
    }


    
    public function eliminarFilaRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;            

        if(isset($this->fetch) && $this->fetch['idRecibo'] > 0) {
            $idRecibo = $this->fetch['idRecibo'];
            $idFactura = $this->modeloReciboProveedor->getIdInvoiceByIdRecepit($idRecibo);
        
            $where = " id = $idRecibo ";
            $del = $this->modeloBase->deleteRow($this->tabla, $where);
            if ($del) {
                $estado = $this->actualizarEstadoFactura($idFactura);
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
            }
        }else{
            $respuesta['mensaje'] = ERROR_ELIMINACION;
        }
        print_r(json_encode($respuesta));
    }

}
