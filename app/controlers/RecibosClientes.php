<?php

class RecibosClientes extends Controlador {

   

    public function __construct() {
        session_start();        
        $this->tabla = 'clientes_recibos'; 
        $this->tablaFacturasCliente = 'clientes_facturas';   
        $this->arrFieldsCreate = ['numero','numerointerno','fecha','importe','idfactura','concepto','lugarexpedicion','librado', 'librador','estado','vencimiento'];
        $this->arrFieldsUpdate = ['fecha','importe','concepto','lugarexpedicion','librado', 'librador','vencimiento'];
        $this->modeloReciboCliente = $this->modelo('ModeloReciboCliente');    
        $this->modeloFacturaCliente = $this->modelo('ModeloFacturaCliente');  
        $this->modeloBase = $this->modelo('ModeloBase');              
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');
        $this->modeloCliente = $this->modelo('ModeloCliente');

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        }  
    }

    public function index() {
        $datos = [];        
        $this->vista('recibosCliente/recibos', $datos);
    }
    
    public function tablaRecibosCliente()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY rec.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);       
        
        $where = str_replace("numero like", "rec.numero like", $where);
        $where = str_replace("vencimiento like", "DATE_FORMAT(rec.vencimiento, '%d/%m/%Y') like", $where);
        $where = str_replace("numerofactura like", "fac.numero like", $where);
        $where = str_replace("estadoactual like", "IF(rec.estado='pagado',rec.estado,IF(rec.vencimiento < CURDATE(),'impagado','pendiente')) like", $where);        
        //$where = str_replace("numero like", "rec.numero like", $where);
        $where = str_replace("librado like", "fac.cliente like", $where);

        $recibos = $this->modeloReciboCliente->obtenerRecibosClientesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReciboCliente->obtenerTotalRecibosCliente($where);

        $salida = [
            'totalRegistros' => count($totalRegistros),
            'registros' => $recibos
        ];

        print_r(json_encode($salida));
    }  

    public function crearRecibo()
    {
        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_CREACION; 
        
        if(isset($_POST['idFactura']) && $_POST['idFactura'] != '' && $_POST['fecha_recibo'] != '' && $_POST['importe_recibo'] != '' && $_POST['fecha_recibo'] != '' && $_POST['vencimiento_recibo'] != '' && isset($_POST['estado_recibo']) && $_POST['estado_recibo'] != ''){
            
            $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla, date("Y",strtotime($_POST['fecha_recibo'])));
            
            $arrValues['numerointerno'] = $nextCodeInterno;  
            $ceros = '';
            if($nextCodeInterno >= 1 && $nextCodeInterno <= 9){
                $ceros = '00';
            }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
                $ceros = '0';
            } 

            $arrValues =[                               
                'numerointerno' => $nextCodeInterno,
                'numero' => date("y",strtotime($_POST['fecha_recibo'])).".".$ceros.$nextCodeInterno,
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
                    $recibos = $this->modeloReciboCliente->getReceiptsByIdInvoice($_POST['idFactura']);
                    $respuesta['html_recibos'] = TemplateHelper::buildGridReceipt($recibos, 'cliente');
                    $respuesta['estado'] = $estado;

                    if(API_GOOGLE_DRIVE==1){
                        //crear pdf recibo en drive
                        $recibo = $this->modeloReciboCliente->getRecepitDataDocument($ins);
                        $numRecibo = $recibo->numero;
                        $nombreArchivo = "Recibo_{$numRecibo}.pdf";
                        GoogleDriveUploader::subirArchivo($ins, $recibo->fecha, 'Recibos Cliente', $nombreArchivo);                    
                    }

                }        
            }                       
        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO; 
        }
        
        print_r(json_encode($respuesta));
    }

    private function actualizarEstadoFactura($idFactura)
    {
       
        $totalFactura = $this->modeloFacturaCliente->getTotalAmountInvoice($idFactura);
        $totalRecibos = $this->modeloReciboCliente->getTotalAmountPaidReceiptsByInvoice($idFactura);      
        $estado = 'pendiente';

        if($totalRecibos == 0){
            $estado = 'pendiente';
        }else if(abs($totalFactura) > 0 && abs($totalRecibos) < abs($totalFactura) ){
            $estado = 'cobrada parcial';
        }else if(abs($totalFactura) > 0 && abs($totalRecibos) == abs($totalFactura) ){
            $estado = 'cobrada';
        }
        $this->modeloBase->updateFieldTabla($this->tablaFacturasCliente, 'estado', $estado, $idFactura);
        
        return $estado;

    }

    public function obtenerRecibosFactura()
    {
        $respuesta['error'] = true;

        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    
        $respuesta['html_recibos'] = 'No hi ha recibos vinculats.';

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idFactura = $this->fetch['id'];
        
            $sel = $this->modeloReciboCliente->getReceiptsByIdInvoice($idFactura);
            if ($sel) {
               
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';                
                $respuesta['html_recibos'] = TemplateHelper::buildGridReceipt($sel, 'cliente');
                
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
            $modeloRecibo = $this->modeloReciboCliente->getRecepitDataDocument($idRecibo);
            $idFactura = $this->modeloReciboCliente->getIdInvoiceByIdRecepit($idRecibo);            
        
            $where = " id = $idRecibo ";
            $del = $this->modeloBase->deleteRow($this->tabla, $where);
            if ($del) {
                $estado = $this->actualizarEstadoFactura($idFactura);
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;   
                            
                if(API_GOOGLE_DRIVE==1){
                    //eliminar pdf recibo en drive                              
                    $numRecibo = $modeloRecibo->numero;
                    $nombreArchivo = "Recibo_{$numRecibo}.pdf";                
                    GoogleDriveUploader::eliminarArchivo($modeloRecibo->fecha, 'Recibos Cliente', $nombreArchivo);
                }
            }
        }else{
            $respuesta['mensaje'] = ERROR_ELIMINACION;
        }
        print_r(json_encode($respuesta));
    }

    public function eliminarReciboFactura(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    
        $respuesta['html_recibos'] = 'No hi ha recibos vinculats.';

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idRecibo = $this->fetch['id'];
            $modeloRecibo = $this->modeloReciboCliente->getRecepitDataDocument($idRecibo);
            $idFactura = $this->modeloReciboCliente->getIdInvoiceByIdRecepit($idRecibo);
        
            $where = " id = $idRecibo ";
            $del = $this->modeloBase->deleteRow($this->tabla, $where);
            if ($del) {
                $estado = $this->actualizarEstadoFactura($idFactura);
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
                $recibos = $this->modeloReciboCliente->getReceiptsByIdInvoice($idFactura);
                $respuesta['html_recibos'] = TemplateHelper::buildGridReceipt($recibos, 'cliente');
                $respuesta['estado'] = $estado;

                if(API_GOOGLE_DRIVE==1){
                    //eliminar pdf recibo en drive                              
                    $numRecibo = $modeloRecibo->numero;
                    $nombreArchivo = "Recibo_{$numRecibo}.pdf";                
                    GoogleDriveUploader::eliminarArchivo($modeloRecibo->fecha, 'Recibos Cliente', $nombreArchivo);                
                }
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
            $datos = $this->modeloReciboCliente->getRecepitById($idRecibo);        
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['datos'] = $datos;

            if(isset($datos->idfactura) && $datos->idfactura > 0){
                $factura = $this->modeloFacturaCliente->getInvoiceData($datos->idfactura);
                $respuesta['formacobro'] = $factura->idformacobro;
            }
            

        }

        print_r(json_encode($respuesta));
    }

    public function actualizarRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;   
              
        if(isset($_POST['idRecibo']) && $_POST['idRecibo'] != '' && $_POST['idRecibo'] > 0 && $_POST['fecha_recibo'] != '' && $_POST['fecha_recibo'] > 0 && $_POST['importe_recibo'] > 0 && $_POST['concepto_recibo'] != '' && $_POST['vencimiento'] != ''){
                            
            $idRecibo = $_POST['idRecibo'];
            $recibo = $this->modeloReciboCliente->getRecepitDataDocument($idRecibo);
            $idFactura = $this->modeloReciboCliente->getIdInvoiceByIdRecepit($idRecibo);

            if($_POST['importe_recibo'] > $this->modeloFacturaCliente->getTotalAmountInvoice($idFactura) ){
                $respuesta['error'] = true;
                $numeroFactura = $this->modeloFacturaCliente->getInvoiceNumberByIdFactura($idFactura);
                $respuesta['mensaje'] = "L'import del recibo és major que l'import de la factura Nº ".$numeroFactura;

            }else{

                $arrValues =[                
                    'fecha' => $_POST['fecha_recibo'],
                    'importe' => $_POST['importe_recibo'],
                    'idRecibo' => $_POST['idRecibo'],
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
                        $respuesta['estado_factura'] = $this->modeloFacturaCliente->getStatusInvoice($idFactura);
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_ACTUALIZACION;            
                        
                        if(API_GOOGLE_DRIVE==1){
                            //crear pdf recibo en drive                        
                            $numRecibo = $recibo->numero;
                            $nombreArchivo = "Recibo_{$numRecibo}.pdf";              
                            GoogleDriveUploader::reemplazarArchivo($idRecibo, $_POST['fecha_recibo'], 'Recibos Cliente', $nombreArchivo, $recibo->fecha);
                        }
                    }
                }                   

            }


        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
        } 
        
        echo json_encode($respuesta);
    }

    public function exportarPdfRecibo($idRecibo)
    {
        $datos_recibo = $this->modeloReciboCliente->getRecepitDataDocument($idRecibo);
        $datos['cabecera'] = $datos_recibo;
        $datos['tipo'] = 'rebut';
        $datos['importe_letras'] = UtilsHelper::numberToWords($datos_recibo->importe);
        $datos['razonsocialpiensos'] = $this->modeloConfiguracion->getBusinessName();
        
        generarPdf::documentoPDFExportar('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0), true, 'documentos', 'recibo.php', $datos);

    }


    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);          
                    
            $where = str_replace("vencimiento like", "DATE_FORMAT(rec.vencimiento, '%d/%m/%Y') like", $where);
            $where = str_replace("numerofactura like", "fac.numero like", $where);
            $where = str_replace("estadoactual like", "IF(rec.estado='pagado',rec.estado,IF(rec.vencimiento < CURDATE(),'impagado','pendiente')) like", $where);

            $order = " ORDER BY rec.id DESC ";          
         
            $datos = $this->modeloReciboCliente->obtenerRecibosClientesExportar($order,$where);
            $nombreReporte = '_RecibosClientes';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }

    public function cambiarEstadoPagadoRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;        

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idRecibo = $this->fetch['id'];
            $upd = $this->modeloReciboCliente->changeStatusReceipt($idRecibo,'pagado');
            if($upd){
                $idFactura = $this->modeloReciboCliente->getIdInvoiceByIdRecepit($idRecibo);
                $this->actualizarEstadoFactura($idFactura);
                $respuesta['error'] = false;
                $respuesta['estado'] = $this->modeloReciboCliente->getRecepitById($idRecibo)->estadoactual;
                $respuesta['mensaje'] = OK_ACTUALIZACION;
                $respuesta['estado_factura'] = $this->modeloFacturaCliente->getStatusInvoice($idFactura);
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
            $upd = $this->modeloReciboCliente->changeStatusReceipt($idRecibo,'pendiente');
            if($upd){
                $respuesta['error'] = false;
                $idFactura = $this->modeloReciboCliente->getIdInvoiceByIdRecepit($idRecibo);
                $this->actualizarEstadoFactura($idFactura);
                $respuesta['estado'] = $this->modeloReciboCliente->getRecepitById($idRecibo)->estadoactual;
                $respuesta['mensaje'] = OK_ACTUALIZACION;    
                $respuesta['estado_factura'] = $this->modeloFacturaCliente->getStatusInvoice($idFactura);                        
            }            
        }
        print_r(json_encode($respuesta));
    }

    public function obtenerDatosEnvioEmailRecibo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;    

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idRecibo = $this->fetch['id'];               
            
            $cabecera = $this->modeloReciboCliente->getRecepitDataDocument($idRecibo);

            if(isset($cabecera->id) && $cabecera->id > 0){
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';
            }

            // Agregar los contactos del cliente
            $idFactura = $cabecera->idfactura;
            $factura = $this->modeloFacturaCliente->getInvoiceData($idFactura);
            $idCliente = $factura->idcliente;
            $respuesta['contactos'] = json_decode($this->modeloCliente->getClientById($idCliente)->contactos);
        }                
        echo json_encode($respuesta);
    }
   
    
    /* public function crearDocumentosAnio2025()
    {
        echo"<br><br>entra a crearDocumentosAnio2025<br>";
        $todos = $this->modeloReciboCliente->obtenerTodosLosAlbaranesPerAnio();
        //print_r($todos);        
        $cont=0;

        if(!empty($todos)){            
            foreach ($todos as $t) {
                $cont++;              
                $numRecibo = $t->numero;
                $nombreArchivo = "Recibo_{$numRecibo}.pdf";
                // Aquí asegura limpieza entre iteraciones
                gc_collect_cycles();
                GoogleDriveUploader::subirArchivo($t->id, $t->fecha, 'Recibos Cliente', $nombreArchivo);
            }
        }
        echo"<br>ha terminado $cont <br>";
    } */
   
}
