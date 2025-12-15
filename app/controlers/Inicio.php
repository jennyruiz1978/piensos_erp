<?php

class Inicio extends Controlador {

   

    public function __construct() {
        
        session_start();        
        $this->modeloReportes = $this->modelo('ModeloReportes');

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        } 

    }

    public function index() {      
        $datos = $this->obtenerDatosDashboard($ini=false,$fin=false);
        $this->vista('inicio/inicio', $datos);
        
    }

    public function reportesFacturacionClientes(){
        $datos = [];
        $this->vista('inicio/facturacionClientes', $datos);
    }    

    public function reportesFacturacionClientesPendientes(){
        $datos = [];
        $this->vista('inicio/facturacionClientesPendientes', $datos);
    }  

    public function reportesFacturacionClientesCobradas(){
        $datos = [];
        $this->vista('inicio/facturacionClientesCobradas', $datos);
    }  

    public function reportesFacturacionClientesCobradasParcial(){
        $datos = [];
        $this->vista('inicio/facturacionClientesCobradasParcial', $datos);
    }  

    public function reportesFacturacionProveedores(){
        $datos = [];
        $this->vista('inicio/facturacionProveedores', $datos);
    }

    public function reportesFacturacionProveedoresPendientes(){
        $datos = [];
        $this->vista('inicio/facturacionProveedoresPendientes', $datos);
    }

    public function reportesFacturacionProveedoresPagadas(){
        $datos = [];
        $this->vista('inicio/facturacionProveedoresPagadas', $datos);
    }

    public function reportesFacturacionProveedoresPagadasParcial(){
        $datos = [];
        $this->vista('inicio/facturacionProveedoresPagadasParcial', $datos);
    }





    public function obtenerDatosDashboard($fechaIni,$fechaFin){
        
        $ini = DateTimeHelper::fechaInicioMesActual();
        $fin = DateTimeHelper::fechaFinalMesActual();
        if($fechaIni && $fechaFin){
            $ini = $fechaIni;
            $fin = $fechaFin;
        }
       
        //proveedores
        $kilosAlbaranes = $this->modeloReportes->getDeliveryNoticesKilos($ini,$fin);        
        $datos["total_kilos_albaranados"] = $kilosAlbaranes->total_kilos_albaranados." ".$kilosAlbaranes->unidad;
        $datos["kilos_sin_facturar"] = $kilosAlbaranes->kilos_sin_facturar." ".$kilosAlbaranes->unidad;

        $eurosAlbaranes = $this->modeloReportes->getDeliveryNoticesEuros($ini,$fin);        
        $datos["total_euros_albaranados"] = number_format($eurosAlbaranes->total_euros_albaranados,0,",",".");
        $datos["euros_sin_facturar"] = number_format($eurosAlbaranes->euros_sin_facturar,0,",",".");

        $facturados = $this->modeloReportes->getInvoicesData($ini,$fin);       
        $facturadosImporte = $this->modeloReportes->getInvoicesAmount($ini,$fin);           
        
        $datos["kilos_facturados"] = $facturados->kilos_facturados." ".$kilosAlbaranes->unidad;
        $datos["euros_facturados"] = number_format($facturadosImporte->euros_facturados,0,",",".");
        $datos["euros_ivafacturado"] = number_format($facturadosImporte->euros_ivafacturado,0,",",".");

        $facturaPagoPendiente = $this->modeloReportes->getInvoicesWithPendingPayment($ini,$fin);        
        $datos["euros_pago_pendiente"] = number_format($facturaPagoPendiente->euros_pago_pendiente,0,",",".");
        $datos["num_facturas_pago_pendiente"] = $facturaPagoPendiente->num_facturas_pago_pendiente;

        $pagadosPagoParcial = $this->modeloReportes->getInvoicesWithRecordedPayment($ini,$fin);        
        $datos["euros_pagados"] = isset($pagadosPagoParcial->euros_pagados)? number_format($pagadosPagoParcial->euros_pagados,0,",","."):0;
        $datos["euros_pago_parcial"] = isset($pagadosPagoParcial->euros_pago_parcial)? number_format($pagadosPagoParcial->euros_pago_parcial,0,",","."): 0;

        //$vencidas = $this->modeloReportes->getOverdueInvoices($ini,$fin);        
        $vencidas = $this->modeloReportes->getOverdueReceipts($ini,$fin);        
        $datos["euros_vencidos"] = (isset($vencidas->importe_recibos) && $vencidas->importe_recibos > 0)? number_format($vencidas->importe_recibos,0,',','.'):0;
        //$datos['num_facturas_vencidas'] = (isset($vencidas->num_facturas_vencidas))? $vencidas->num_facturas_vencidas: 0;


        //clientes
              
        $kilosAlbaranesCli = $this->modeloReportes->getDeliveryNoticesKilosCli($ini,$fin);        
        $datos["total_kilos_albaranados_cli"] = $kilosAlbaranesCli->total_kilos_albaranados." ".$kilosAlbaranesCli->unidad;
        $datos["kilos_sin_facturar_cli"] = $kilosAlbaranesCli->kilos_sin_facturar." ".$kilosAlbaranesCli->unidad;

        $eurosAlbaranesCli = $this->modeloReportes->getDeliveryNoticesEurosCli($ini,$fin);        
        $datos["total_euros_albaranados_cli"] = number_format($eurosAlbaranesCli->total_euros_albaranados,0,",",".");
        $datos["euros_sin_facturar_cli"] = number_format($eurosAlbaranesCli->euros_sin_facturar,0,",",".");

        $facturadosCli = $this->modeloReportes->getInvoicesDataCli($ini,$fin);        
        $facturadosImporteCli = $this->modeloReportes->getInvoicesDataAmountCli($ini,$fin);

        $datos["kilos_facturados_cli"] = $facturadosCli->kilos_facturados." ".$facturadosCli->unidad;
        $datos["euros_facturados_cli"] = number_format($facturadosImporteCli->euros_facturados,0,",",".");
        $datos["euros_ivafacturado_cli"] = number_format($facturadosImporteCli->euros_ivafacturado,0,",",".");

        $facturaPagoPendienteCli = $this->modeloReportes->getInvoicesWithPendingPaymentCli($ini,$fin);        
        $datos["euros_pago_pendiente_cli"] = number_format($facturaPagoPendienteCli->euros_pago_pendiente,0,",",".");
        $datos["num_facturas_pago_pendiente_cli"] = $facturaPagoPendienteCli->num_facturas_pago_pendiente;

        $pagadosPagoParcialCli = $this->modeloReportes->getInvoicesWithRecordedPaymentCli($ini,$fin);        
        $datos["euros_pagados_cli"] = isset($pagadosPagoParcialCli->euros_pagados)? number_format($pagadosPagoParcialCli->euros_pagados,0,",","."): 0;
        $datos["euros_pago_parcial_cli"] = isset($pagadosPagoParcialCli->euros_pago_parcial) ? number_format($pagadosPagoParcialCli->euros_pago_parcial,0,",","."):0 ;

        //$vencidasCli = $this->modeloReportes->getOverdueInvoicesCli($ini,$fin); 
        $vencidasCli = $this->modeloReportes->getOverdueReceiptsCli($ini,$fin); 
        $datos["euros_vencidos_cli"] = (isset($vencidasCli->importe_recibos) && $vencidasCli->importe_recibos > 0)? number_format($vencidasCli->importe_recibos,0,',','.') : 0;
        //$datos["num_facturas_vencidas_cli"] = (isset($vencidasCli->num_facturas_vencidas))? $vencidasCli->num_facturas_vencidas: 0;


        return $datos;

    }

    public function buscarDatosDashboardSegunFechas(){

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;  
        $respuesta['datos'] = [];
        if(isset($this->fetch) && $this->fetch['ini'] != '' && $this->fetch['fin'] != '') {
            $respuesta['error'] = false;
            $respuesta['mensaje'] = ''; 
            $respuesta['datos'] = $this->obtenerDatosDashboard($this->fetch['ini'],$this->fetch['fin']);
        }
        print_r(json_encode($respuesta));
    }

    public function tablaReporteFacturasClientes()
    {               
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                

        $where = $this->configurarFiltrosFacturasClientes($where);                  

        if ($where == '') {
            $fechas = DateTimeHelper::fechasMesActualString();
            $where = " WHERE fecha $fechas  ";
        }               
        
        $facturas = $this->modeloReportes->obtenerFacturasClientesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReportes->obtenerTotalFacturasCliente($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }

    public function tablaReporteFacturasClientesPendientes()
    {               
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];      
        
        $where = $this->configurarFiltrosFacturasClientes($where);
                    
        if ($where == '') {
            $fechas = DateTimeHelper::fechasMesActualString();
            $where = " WHERE fecha $fechas AND estado = 'pendiente'  ";
        }else{
            $where .= " AND estado = 'pendiente'  ";
        }

        $facturas = $this->modeloReportes->obtenerFacturasClientesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReportes->obtenerTotalFacturasCliente($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }

    public function tablaReporteFacturasClientesCobradas()
    {               
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];      
        
        $where = $this->configurarFiltrosFacturasClientes($where);
                    
        if ($where == '') {
            $fechas = DateTimeHelper::fechasMesActualString();
            $where = " WHERE fecha $fechas AND estado = 'cobrada'  ";
        }else{
            $where .= " AND estado = 'cobrada'  ";
        }



        $facturas = $this->modeloReportes->obtenerFacturasClientesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReportes->obtenerTotalFacturasCliente($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }    
    
    public function tablaReporteFacturasClientesCobradasParcial()
    {               
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];      
        
        $where = $this->configurarFiltrosFacturasClientes($where);
                    
        if ($where == '') {
            $fechas = DateTimeHelper::fechasMesActualString();
            $where = " WHERE fecha $fechas AND estado = 'cobrada parcial'  ";
        }else{
            $where .= " AND estado = 'cobrada parcial'  ";
        }



        $facturas = $this->modeloReportes->obtenerFacturasClientesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReportes->obtenerTotalFacturasCliente($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }   

    private function configurarFiltrosFacturasClientes($where){

        $where = str_replace("numero like", "fac.numero like", $where);
        $where = str_replace("cliente like", "fac.cliente like", $where);
        $where = str_replace("fecha like", "DATE_FORMAT(fac.fecha, '%d/%m/%Y') like", $where);
        $where = str_replace("vencimiento like", "DATE_FORMAT(vencimiento, '%d/%m/%Y') like", $where);  
        $where = str_replace("total like", "fac.total like", $where);
        $where = str_replace("estado like", "fac.estado like", $where);
        $where = str_replace("por_cobrar like", "IF(b.suma_cobrada IS NOT NULL AND b.suma_cobrada > 0 ,fac.total-b.suma_cobrada,fac.total) like", $where);
        $where = str_replace("nif like", "cli.nif like", $where);

        return $where;
    }
    
    private function configurarFiltrosFacturasProveedores($where){

        $where = str_replace("fecha like", "DATE_FORMAT(fac.fecha, '%d/%m/%Y') like", $where);
        $where = str_replace("vencimiento like", "DATE_FORMAT(fac.vencimiento, '%d/%m/%Y') like", $where);   
        $where = str_replace("nombre_proveedor like", "DATE_FORMAT(fac.vencimiento, '%d/%m/%Y') like", $where);   
        $where = str_replace("nif like", "prov.nif like", $where);   
        
        return $where;
    }
    

    public function tablaReporteFacturasProveedores()
    {               
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                         
     
        $where = $this->configurarFiltrosFacturasProveedores($where);              
        
        if ($where == '') {
            $fechas = DateTimeHelper::fechasMesActualString();
            $where = " WHERE fac.fecha $fechas  ";
        }    
        
        $facturas = $this->modeloReportes->obtenerFacturasProveedoresTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReportes->obtenerTotalFacturasProveedor($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }

    public function tablaReporteFacturasProveedoresPendientes()
    {               
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                         
     
        $where = $this->configurarFiltrosFacturasProveedores($where);                      
                  
        if ($where == '') {
            $fechas = DateTimeHelper::fechasMesActualString();
            $where = " WHERE fac.fecha $fechas AND fac.estado = 'pendiente'  ";
        }else{
            $where .= " AND fac.estado = 'pendiente'  ";
        } 
        
        $facturas = $this->modeloReportes->obtenerFacturasProveedoresTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReportes->obtenerTotalFacturasProveedor($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }

    public function tablaReporteFacturasProveedoresPagadas()
    {               
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                         
     
        $where = $this->configurarFiltrosFacturasProveedores($where);                      
                  
        if ($where == '') {
            $fechas = DateTimeHelper::fechasMesActualString();
            $where = " WHERE fac.fecha $fechas AND fac.estado = 'pagada'  ";
        }else{
            $where .= " AND fac.estado = 'pagada'  ";
        } 
        
        $facturas = $this->modeloReportes->obtenerFacturasProveedoresTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReportes->obtenerTotalFacturasProveedor($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }

    public function tablaReporteFacturasProveedoresPagadasParcial()
    {               
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY fac.id DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                         
     
        $where = $this->configurarFiltrosFacturasProveedores($where);                      
                  
        if ($where == '') {
            $fechas = DateTimeHelper::fechasMesActualString();
            $where = " WHERE fac.fecha $fechas AND fac.estado = 'pagada parcial'  ";
        }else{
            $where .= " AND fac.estado = 'pagada parcial'  ";
        } 
        
        $facturas = $this->modeloReportes->obtenerFacturasProveedoresTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloReportes->obtenerTotalFacturasProveedor($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $facturas
        ];

        print_r(json_encode($salida));
    }

    

    
    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);           

            $order = " ORDER BY fecha ASC ";               

            if ($where == '') {
                $fechas = DateTimeHelper::fechasMesActualString();
                $where = " WHERE fecha $fechas  ";
            }
            $datos = $this->modeloReportes->obtenerFacturasClientesTest($order,$where);
            $nombreReporte = '_FacturasClientes';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }

    public function exportarExcelFacturasClientes()
    {                       
            
            $where = base64_decode($_POST['cadenaCriterios']);                           
            $where = $this->configurarFiltrosFacturasClientesExport($where);
            
            if(isset($_POST['fechasfiltro']) && $_POST['fechasfiltro'] != ''){
                $fechas = $_POST['fechasfiltro'];                
            }else{
                $fechas = DateTimeHelper::fechasMesActualString();                
            }
            if ($where == '') {    
                $where = " WHERE fecha $fechas ";            
            }else{
                $where .= " AND fecha $fechas ";
            }                  

            $order = " ORDER BY fac.numero ASC ";

            $datos = $this->modeloReportes->obtenerFacturasClientes($order,$where);
            $nombreReporte = '_FacturasClientes';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
    
    }

    public function exportarExcelFacturasClientesCobradas()
    {
                    
        $where = base64_decode($_POST['cadenaCriterios']);                           
        $where = $this->configurarFiltrosFacturasClientesExport($where);

        if(isset($_POST['fechasfiltro']) && $_POST['fechasfiltro'] != ''){
            $fechas = $_POST['fechasfiltro'];                
        }else{
            $fechas = DateTimeHelper::fechasMesActualString();                
        }

        if ($where == '') {    
            $where = " WHERE fecha $fechas  AND estado = 'cobrada' ";            
        }else{
            $where .= " AND fecha $fechas AND estado = 'cobrada'  ";
        }      
       
        $order = " ORDER BY fac.numero ASC ";

        $datos = $this->modeloReportes->obtenerFacturasClientes($order,$where);
        $nombreReporte = '_FacturasClientes';
        ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);           
    }

    public function exportarExcelFacturasClientesCobradasParcial()
    {
                    
        $where = base64_decode($_POST['cadenaCriterios']);                           
        $where = $this->configurarFiltrosFacturasClientesExport($where);

        if(isset($_POST['fechasfiltro']) && $_POST['fechasfiltro'] != ''){
            $fechas = $_POST['fechasfiltro'];                
        }else{
            $fechas = DateTimeHelper::fechasMesActualString();                
        }

        if ($where == '') {    
            $where = " WHERE fecha $fechas  AND estado = 'cobrada parcial' ";            
        }else{
            $where .= " AND fecha $fechas AND estado = 'cobrada parcial'  ";
        }    

        $order = " ORDER BY fac.numero ASC ";

        $datos = $this->modeloReportes->obtenerFacturasClientes($order,$where);
        $nombreReporte = '_FacturasClientes';
        ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);           
    }
    
    public function exportarExcelFacturasClientesPendientes()
    {
                    
        $where = base64_decode($_POST['cadenaCriterios']);            
        $where = $this->configurarFiltrosFacturasClientesExport($where);       

        if(isset($_POST['fechasfiltro']) && $_POST['fechasfiltro'] != ''){
            $fechas = $_POST['fechasfiltro'];                
        }else{
            $fechas = DateTimeHelper::fechasMesActualString();                
        }

        if ($where == '') {    
            $where = " WHERE fecha $fechas  AND estado = 'pendiente' ";            
        }else{
            $where .= " AND fecha $fechas AND estado = 'pendiente'  ";
        }      
       
        $order = " ORDER BY fac.numero ASC ";

        $datos = $this->modeloReportes->obtenerFacturasClientes($order,$where);
        $nombreReporte = '_FacturasClientes';
        ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);           
    }

    private function configurarFiltrosFacturasClientesExport($where){

        $where = str_replace("numero like", "fac.numero like", $where);
        $where = str_replace("cliente like", "fac.cliente like", $where);
        $where = str_replace("fecha like", "DATE_FORMAT(fac.fecha, '%d/%m/%Y') like", $where);
        $where = str_replace("vencimiento like", "DATE_FORMAT(vencimiento, '%d/%m/%Y') like", $where);  
        $where = str_replace("total like", "fac.total like", $where);
        $where = str_replace("estado like", "fac.estado like", $where);
        $where = str_replace("por_cobrar like", "IF(b.suma_cobrada IS NOT NULL AND b.suma_cobrada > 0 ,fac.total-b.suma_cobrada,fac.total) like", $where);
        $where = str_replace("nif like", "cli.nif like", $where);
        return $where;
    }
    
    
    public function exportarExcelFacturasProveedores()
    {                       
            
            $where = base64_decode($_POST['cadenaCriterios']);                           
            $where = $this->configurarFiltrosFacturasProveedores($where);
            
            if(isset($_POST['fechasfiltro']) && $_POST['fechasfiltro'] != ''){
                $fechas = $_POST['fechasfiltro'];                
            }else{
                $fechas = DateTimeHelper::fechasMesActualString();                
            }
            if ($where == '') {    
                $where = " WHERE fac.fecha $fechas ";            
            }else{
                $where .= " AND fac.fecha $fechas ";
            }                  

            $order = " ORDER BY fac.numero ASC ";

            $datos = $this->modeloReportes->obtenerFacturasProveedores($order,$where);
            $nombreReporte = '_FacturasProveedores';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
    
    }

    public function exportarExcelFacturasProveedoresPendientes()
    {                       
            
            $where = base64_decode($_POST['cadenaCriterios']);                           
            $where = $this->configurarFiltrosFacturasProveedores($where);
            
            if(isset($_POST['fechasfiltro']) && $_POST['fechasfiltro'] != ''){
                $fechas = $_POST['fechasfiltro'];                
            }else{
                $fechas = DateTimeHelper::fechasMesActualString();                
            }
            if ($where == '') {    
                $where = " WHERE fac.fecha $fechas AND fac.estado = 'pendiente' ";            
            }else{
                $where .= " AND fac.fecha $fechas AND fac.estado = 'pendiente' ";
            }                  

            $order = " ORDER BY fac.numero ASC ";

            $datos = $this->modeloReportes->obtenerFacturasProveedores($order,$where);
            $nombreReporte = '_FacturasProveedores';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
    
    }

    public function exportarExcelFacturasProveedoresPagadas()
    {                       
            
            $where = base64_decode($_POST['cadenaCriterios']);                           
            $where = $this->configurarFiltrosFacturasProveedores($where);
            
            if(isset($_POST['fechasfiltro']) && $_POST['fechasfiltro'] != ''){
                $fechas = $_POST['fechasfiltro'];                
            }else{
                $fechas = DateTimeHelper::fechasMesActualString();                
            }
            if ($where == '') {    
                $where = " WHERE fac.fecha $fechas AND fac.estado = 'pagada' ";            
            }else{
                $where .= " AND fac.fecha $fechas AND fac.estado = 'pagada' ";
            }                  

            $order = " ORDER BY fac.numero ASC ";

            $datos = $this->modeloReportes->obtenerFacturasProveedores($order,$where);
            $nombreReporte = '_FacturasProveedores';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
    
    }

    public function exportarExcelFacturasProveedoresPagadasParcial()
    {                       
            
            $where = base64_decode($_POST['cadenaCriterios']);                           
            $where = $this->configurarFiltrosFacturasProveedores($where);
            
            if(isset($_POST['fechasfiltro']) && $_POST['fechasfiltro'] != ''){
                $fechas = $_POST['fechasfiltro'];                
            }else{
                $fechas = DateTimeHelper::fechasMesActualString();                
            }
            if ($where == '') {    
                $where = " WHERE fac.fecha $fechas AND fac.estado = 'pagada parcial' ";            
            }else{
                $where .= " AND fac.fecha $fechas AND fac.estado = 'pagada parcial' ";
            }                  

            $order = " ORDER BY fac.numero ASC ";

            $datos = $this->modeloReportes->obtenerFacturasProveedores($order,$where);
            $nombreReporte = '_FacturasProveedores';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
    
    }

    
   public function exportarPdfDashboard($ini=false,$fin=false)
   {
        if($ini==false || $fin==false){
            echo"las fechas solicitadas no son correctas";
            return;
        }else{

            $datos['info'] = $this->obtenerDatosDashboard($ini,$fin);
            $datos['ini'] = date("d-m-Y",strtotime($ini));
            $datos['fin'] = date("d-m-Y",strtotime($fin));;
                    
            generarPdf::documentoPDFExportar('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0), true, 'documentos', 'dashboard.php', $datos);  
    
        }

   }
   
}

