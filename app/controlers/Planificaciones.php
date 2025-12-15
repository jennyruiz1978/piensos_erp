<?php

class Planificaciones extends Controlador {       

    private $fetch;

    public function __construct() {        
       
        session_start();       

        /*
        echo"<br>imprimo this<br>";
        print_r($this);
        */
        
        $this->modeloPlanificacion = $this->modelo('ModeloPlanificacion');
       
        $this->modeloPlanificacionFechas = $this->modelo('ModeloPlanificacionFechas');
       
        $this->modeloPlanificacionRecojos = $this->modelo('ModeloPlanificacionRecojos');
         
 
        $this->modeloProductoCompra = $this->modelo('ModeloProductoCompra');
        $this->modeloProductoVenta= $this->modelo('ModeloProductoVenta');        

        $this->modeloProveedor = $this->modelo('ModeloProveedor');
        $this->modeloCliente = $this->modelo('ModeloCliente');
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');
        $this->modeloBase = $this->modelo('ModeloBase');                
        $this->modeloAlbaranCliente = $this->modelo('ModeloAlbaranCliente');
        $this->modeloAlbaranProveedor = $this->modelo('ModeloAlbaranProveedor');
        $this->modeloFacturaProveedor = $this->modelo('ModeloFacturaProveedor');
        $this->modeloReciboProveedor = $this->modelo('ModeloReciboProveedor');
        $this->modeloAlbaranDetalleProveedor = $this->modelo('ModeloAlbaranDetalleProveedor');  
        $this->modeloAlbaranDetalleCliente = $this->modelo('ModeloAlbaranDetalleCliente'); 
        $this->tabla = 'planificaciones';           
        $this->tabla_proveedores_albaranes = 'proveedores_albaranes';    
        $this->tabla_proveedores_albaranes_det = 'proveedores_albaranes_det';   
        $this->tabla_proveedores_facturas = 'proveedores_facturas';     
        $this->tabla_proveedores_facturas_det = 'proveedores_facturas_det';     
        $this->tabla_clientes_albaranes = 'clientes_albaranes';    
        $this->tabla_clientes_albaranes_det = 'clientes_albaranes_det';
        $this->fieldsCreateAlbProv = ['numerointerno','numero','idproveedor','proveedor','fecha','total','ivatotal','baseimponible'];
        $this->fieldsRowsCreateAlbaran = ['idalbaran','idproducto','descripcion','unidad','cantidad','precio','ivatipo','subtotal','idplanfecha','idplanfechafab','albaranfabrica'];
        $this->fieldsUpdateAlbaranProv = ['idproveedor','proveedor','total','ivatotal','baseimponible'];
        $this->fieldsRowsUpdateAlbaranProv = ['cantidad','precio','ivatipo','subtotal'];        
        $this->fieldsCreateAlbCli = ['numerointerno','numero','idcliente','cliente','fecha','total','ivatotal','baseimponible'];
        $this->fieldsRowsCreateAlbaranCli = ['idalbaran','idproducto','descripcion','unidad','cantidad','precio','ivatipo','subtotal','idplanfecha'];

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        }              
        
    }    

    public function index()
    {
        
        
        $planificaciones = $this->modeloPlanificacion->obtenerPlanificaciones();

        $datos = [];

        $this->vista('planificaciones/planificaciones', $datos);
    }

    public function tablaPlanificaciones()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        if($order == ''){
            $order = ' ORDER BY semana DESC ';
        }
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);
        
        $where = str_replace("fechainicio like", "DATE_FORMAT(fechainicio, '%d/%m/%Y') like", $where);
        $where = str_replace("fechafin like", "DATE_FORMAT(fechafin, '%d/%m/%Y') like", $where);
                
        if ($where == "") {
            $where = " WHERE status <> 'eliminado' ";
        }else{
            $where .= " AND status <> 'eliminado' ";
        }                            
        
        $planificaciones = $this->modeloPlanificacion->obtenerPlanificacionesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloPlanificacion->obtenerTotalPlanificaciones($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $planificaciones
        ];

        print_r(json_encode($salida));
    }

    public function altaPlanificaciones()
    {        
        $idPlanificacion = $this->crearPlanificacion();    
        if(isset($idPlanificacion) && $idPlanificacion > 0){
            redireccionar('/Planificaciones/altaPlanificacion/'.$idPlanificacion);
        }else{
            redireccionar('/Planificaciones');
        }
                 
    }

    public function altaPlanificacion($idPlanificacion)
    {
        if(isset($idPlanificacion) && $idPlanificacion > 0){
            $idProductoDefault = $this->modeloConfiguracion->getProductCarrierOrProductPlanningDefault();
            $datos = [
                'idPlanificacion' => $idPlanificacion,
                'idProducto' => $idProductoDefault,
                'nombreProducto' => $this->obtenerNombreProductoCompra($idProductoDefault),
                'semana' => $this->modeloPlanificacion->getWeekPlanningById($idPlanificacion)
            ];
            
      

            $verificador = $this->modeloPlanificacionFechas->countPlanningDates($idPlanificacion);

            if($verificador == 0){
            
                $this->vista('planificaciones/altaPlanificaciones', $datos);
            }else{
                redireccionar('/Planificaciones/verPlanificacion/'.$idPlanificacion);                
            }
            
        }else{
            redireccionar('/Planificaciones');
        }
        
    }
    
    private function crearPlanificacion()
    {
        $ins = false;
        $idProductoDefault = $this->modeloConfiguracion->getProductCarrierOrProductPlanningDefault();

        if($idProductoDefault > 0){
            $nombreProducto = $this->obtenerNombreProductoCompra($idProductoDefault);                        
            $nextCode = $this->modeloPlanificacion->getNextPlanningCode();                
    
            if($nextCode){
    
                $arr['codigo'] = $nextCode;
                $arr['status'] = 'activo';
                $arr['idproducto'] = $idProductoDefault;
                $arr['nombreproducto'] = $nombreProducto;
    
                $ins = $this->modeloPlanificacion->addPlanning($arr);            
            }
        }

                      
        return $ins;        
    }

    public function verificarSiFechaInicioYFechasSemanaExisten($fechaInicio)
    {        
        $fechasEntreLunesYDomingo = $this->calcularFechasEntreLunesYDomingo($fechaInicio);
        $r['existe']=false;
        foreach ($fechasEntreLunesYDomingo as $f) {
            $fecha_existe = $this->modeloPlanificacionFechas->verifyPlanningDataExist($f);
            if($fecha_existe > 0){       
                $r['existe']=true;
                return $r;
            }else{
                $r['existe']=false;
            }
        }
        $r['fechas']=$fechasEntreLunesYDomingo;

        
        
        
        return $r;
    }

    public function crearRangoFechas(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;
        $respuesta['html'] = '';

        if(isset($_POST['id']) && $_POST['id'] > 0 && $_POST['fecha_inicio'] != '') {                        
                  
            $r = $this->verificarSiFechaInicioYFechasSemanaExisten($_POST['fecha_inicio']);                    

            if($r['existe']){
             
                $respuesta['error'] = true;
                $respuesta['mensaje'] = 'Ja existeix programació per a la data o dates de la setmana seleccionada';
                              
            }else{
                
                $fechas = $r['fechas'];

                $planning = $this->modeloPlanificacion->getPlanningById($_POST['id']);                                              

                $tirarFechas = $this->crearFechasInicioFin($_POST['id'], $fechas);

                $semana = $this->calcularYActualizarSemana($_POST['id']);

                
                if($tirarFechas){             

                    $respuesta['error'] = false;
                    $respuesta['mensaje'] = '';
                    $retorno = $this->construirDatosTablaPlanificacionSemanal($_POST['id']);
                    $respuesta['html'] = $retorno;
                    $respuesta['semana'] = $semana;
                }
              
            }
            
        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }        
       
        print_r(json_encode($respuesta));
    }

    private function calcularYActualizarSemana($idPlanificacion)
    {
        $planificacion = $this->modeloPlanificacion->getPlanningById($idPlanificacion);
        $fechaInicio = $planificacion->fechainicio;

        //////////////
        $fechaFin = $planificacion->fechafin;
        // Obtener años de las fechas
        $anioInicio = date("Y", strtotime($fechaInicio));
        $anioFin = date("Y", strtotime($fechaFin));
        
        if($anioInicio === $anioFin){
            $semana_number = UtilsHelper::getWeekNumberByDate($fechaInicio);
            $semana_string = date("y",strtotime($fechaInicio))."/".$semana_number;
        }else{
            $semana_number = '01';
            $semana_string = date("y",strtotime($fechaFin))."/".$semana_number;
        }
        //////////////        

        $retorno ='';
        $upd = $this->modeloBase->updateFieldTabla($this->tabla, 'semana', $semana_string, $idPlanificacion);
        if($upd){
            $retorno = $semana_string;
        }
        return $retorno;
    }
    
    
    private function crearFechasInicioFin($idPlanning, $fechas){

        $retorno=false;       

        $fechaInicio = $fechas[0];
        $fechaFin = $fechas[6];           

        $upd = $this->modeloPlanificacion->updateStartEndDates($fechaInicio, $fechaFin, $idPlanning);       

        if($upd){

         
            if(isset($fechas) && count($fechas) > 0){

                $recojo = $this->modeloPlanificacionRecojos->createCollectionPlanning($idPlanning);
                $cont = 0;              

                foreach ($fechas as $ord => $fecha) {
                    $diaSemana = DateTimeHelper::diaTextoCorto($ord+1);
                    $create = $this->modeloPlanificacionFechas->createDatesPlanningById($idPlanning, $fecha, $diaSemana, $recojo);
                    if($create) {
                        $cont++;
                    }
                }

                if($cont == count($fechas) > 0){
                    $retorno=true;                    
                }
            }
                        
        }
        return $retorno;        
    }

        
    // Función para calcular el lunes anterior más cercano
    public function calcularLunesAnterior($fecha) {
        $diaSemana = date('N', strtotime($fecha));
        // Calcula la fecha del lunes anterior
        $diasHastaLunes = $diaSemana - 1;
        return date('Y-m-d', strtotime("-$diasHastaLunes days", strtotime($fecha)));
    }

    // Función para calcular el domingo posterior más cercano
    public function calcularDomingoPosterior($fecha) {
        $diaSemana = date('N', strtotime($fecha));
        // Calcula la fecha del domingo posterior
        $diasHastaDomingo = 7 - $diaSemana;
        return date('Y-m-d', strtotime("+$diasHastaDomingo days", strtotime($fecha)));
    }

    // Función principal que recibe la fecha como entrada
    public function calcularFechasEntreLunesYDomingo($fecha) {
        // Verificar qué día de la semana es la fecha dada
        $diaSemana = date('N', strtotime($fecha));


        // Calcula el lunes anterior más cercano
        $lunesAnterior = $this->calcularLunesAnterior($fecha);

        

        // Calcula el domingo posterior más cercano
        $domingoPosterior = $this->calcularDomingoPosterior($fecha);
   

        // Calcula las fechas entre el lunes y el domingo
        $fechas = [];
        $fechaActual = $lunesAnterior;
    
        while ($fechaActual <= $domingoPosterior) {
            $fechas[] = $fechaActual;
            $fechaActual = date('Y-m-d', strtotime("+1 day", strtotime($fechaActual)));
           
        }
       
        return $fechas;
    }

    private function construirDatosTablaPlanificacionSemanal($idPlanning){

        $arr=[];
        $html = '<div class="table-responsive"><table class="table table-bordered" id="tabla_planificacion">';
        
        $recojos = $this->modeloPlanificacionRecojos->getCollectionPlanning($idPlanning);       
       
        $fechas = $this->modeloPlanificacion->getPlanningById($idPlanning);

        $cabeceraTabla = TemplateHelper::construirCabeceraTabla($idPlanning, $fechas);
        
        $html .= $cabeceraTabla;       

        $html .= '<tbody id="body_tabla_planificacion">';
        if(isset($recojos) && count($recojos) > 0){

            foreach ($recojos as $recojo) {               
                $html .= $this->construirHtmlFilaPlanificacion($recojo->id, $idPlanning);
            }            

        }    

        $html .= '</tbody>';

        $html .= '</table></div>';
               
        return $html;

    }

    private function construirHtmlFilaPlanificacion($recojoId, $idPlanning)
    {
        $proveedores = $this->modeloProveedor->getEnabledSuppliers();
        $clientes = $this->modeloCliente->getEnabledClients();

        $transportistaDefecto = $this->modeloConfiguracion->getIdTransportistaDefault();               
        
        $html = '<tr  data-index="'.$recojoId.'" id="recojo_'.$recojoId.'">';
               
        $diasRecojos = $this->modeloPlanificacionFechas->getDateByIdPlanningAndIdRecojo($idPlanning, $recojoId);

        $unidad = $this->modeloProductoCompra->getPurchaseProduct($this->modeloPlanificacion->getPlanningById($idPlanning)->idproducto)->abrev_unidad;
       
        if(isset($diasRecojos) && count($diasRecojos) > 0){
            
            foreach ($diasRecojos as $dr) {

                $albProv = $this->modeloAlbaranDetalleProveedor->searchNoticeDeliveryIdExist($dr->id);                
                $idAlbaranDet = 0;
                $idAlbaranProv = 0;
                if($albProv){
                    $idAlbaranDet = $albProv->id;
                    $idAlbaranProv = $albProv->idalbaran;
                }
                
                $albCli = $this->modeloAlbaranDetalleCliente->searchNoticeDeliveryClientIdExist($dr->id);                
                $idAlbaranDetCli = 0;
                $idAlbaranCli = 0;
                if($albCli){
                    $idAlbaranDetCli = $albCli->id;
                    $idAlbaranCli = $albCli->idalbaran;
                }                
                $idAlbaranFabrica = $this->modeloAlbaranDetalleProveedor->searchNoticeDeliveryFactorySupplierIdExist($dr->id);
               
                $cliente = 0;
                $datosCliente = false;
                if(isset($dr->idcliente) && $dr->idcliente > 0){
                    $cliente = $dr->idcliente;
                    $datosCliente = $this->buscarDatosParaPlanificacionByIdPlanFecha($dr->idcliente);
                }
                $transportista = (isset($dr->idtransportista) && $dr->idtransportista > 0)? $dr->idtransportista:$transportistaDefecto;

                $html .= TemplateHelper::construirFilaTablaPlanificacion($dr, $clientes, $cliente, $datosCliente, $proveedores, $transportista, $unidad, $idAlbaranDet, $idAlbaranDetCli, $idAlbaranCli, $idAlbaranProv, $idAlbaranFabrica);
                                
            }
            $html .= '<td class="celdaEliminarPlan" data-idrecojo="'.$recojoId.'"><span class="btnEliminarPlan">x</span></td>';
       }
       $html .= '</tr>';
       
       return $html;
    }


    private function crearFechasInicioFinAntes($idPlanning, $fechaInicio, $fechaFin){

        $retorno=false;       

        if($fechaFin == ''){
            $fechaFin = DateTimeHelper::calcularFechaFin($fechaInicio,4);       
        }        

        $upd = $this->modeloPlanificacion->updateStartEndDates($fechaInicio, $fechaFin, $idPlanning);       

        if($upd){

            $fechas = DateTimeHelper::buscarDiasEntreFechaInicioYFin($fechaInicio, $fechaFin);
         
            if(isset($fechas) && count($fechas) > 0){

                $recojo = $this->modeloPlanificacionRecojos->createCollectionPlanning($idPlanning);
                $cont = 0;              

                foreach ($fechas as $fecha) {

                    $create = $this->modeloPlanificacionFechas->createDatesPlanningById($idPlanning, $fecha['fecha'], $fecha['dia'], $recojo);
                    if($create) {
                        $cont++;
                    }
                }

                if($cont == count($fechas) > 0){
                    $retorno=true;                    
                }
            }
                        
        }
        return $retorno;        
    }

    private function construirArrayDatosPlanificacionFecha($post)
    {
        $validar = false;
        $arr['id'] = $post['id'];
        $arr['carga'] = (trim($post['input_value']) != '')? str_replace(",", ".", $post['input_value']): 0;
        $arr['idplanificacion'] = $post['idplanificacion'];
        $arr['idtransportista'] = $post['idtransportista'];
        $arr['idcliente'] = $post['idcliente'];
        
        $zona = $this->modeloCliente->getZoneDataByClientId($post['idcliente']);        
        $idZona = (isset($zona->id) && $zona->id > 0)? $zona->id:0;
        $precioZona = (isset($zona->margen) && $zona->margen > 0)? $zona->margen:0;
        $precioCliente = $this->modeloCliente->getPriceByClientId($post['idcliente']);        
        
        $arr['idzona'] = $idZona;
        $arr['preciocliente'] =  $precioCliente;
        $arr['preciozona'] = $precioZona;
        
        if($idZona > 0 /* && $precioZona > 0 */ && $precioCliente > 0){
            $validar = true;            
        }        
        $arr['$validar'] = $validar;
        return $arr;
    }


    
    public function actualizarCeldaPlanificacion(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;   
        $msg = '';

        if(isset($_POST['id']) && $_POST['id'] > 0 && isset($_POST['input_value']) && trim($_POST['input_value']) >0 && isset($_POST['idplanificacion']) && $_POST['idplanificacion'] > 0 && isset($_POST['idtransportista']) && $_POST['idtransportista'] > 0 && $_POST['id_producto_compra'] > 0 && isset($_POST['idcliente']) && $_POST['idcliente'] > 0) {
            

            $arr = $this->construirArrayDatosPlanificacionFecha($_POST);
            
            if($arr['$validar']){              

                $upd = $this->actualizarCeldaPlanificacionYTotales($arr);
          
                if ($upd) {
    
                    $idalbarandet = $_POST['idalbarandet'];
                  
                    if(isset($idalbarandet) && $idalbarandet > 0){                    
                        $alb = $this->actualizarAlbaranProveedorDesdePlanificacion($_POST, $idalbarandet);
                        if($alb['error'] == false){
                            $msg .= "S'ha desat la planificació, l'albarà de proveïdor ";
                            
                            $albFabrica = $this->actualizarAlbaranProveedorFabricaDesdePlanificacion($_POST, $idalbarandet);
                            if($albFabrica['error'] == false){
                                $msg .= " i l'albarà proveïdor fàbrica.";
                            }else{
                                $msg .= " però no l'albarà del proveïdor fàbrica.";
                            }

                            $respuesta['error'] = false;
                            $respuesta['idalbarandet'] = $idalbarandet;
                            
                            //POR TERMINAR
                            /*                           
                            $idFactura = $this->modeloAlbaranProveedor->getInvoiceNumberByDeliveryNoteByPlanningDateId($_POST['id']);
                            if($idFactura > 0){
                                $updFact = $this->actualizarFacturaDesdePlanificacion($idFactura, $_POST['input_value'], $_POST['idalbarandet']);
                                $msg .= $updFact['mensaje'];                               
                            }
                            */
                            
                        }else{
                            $msg .= "S'ha actualitzat la planificació, però s'ha produït un error i no s'ha desat l'albarà.";
                            $respuesta['error'] = true;
                        }
                        $respuesta['idalbaranprov'] = $this->modeloAlbaranDetalleProveedor->getIdAlbaranByIdAlbaranDet($idalbarandet);
                        $respuesta['idalbaranfabrica'] = $this->obtenerIdAlbaranFabrica($idalbarandet);
                        
                    }else{
                        
                        $alb = $this->crearAlbaranProveedorDesdePlanificacion($_POST);
                        if($alb['error'] == false){
                            
                            $msg .= "S'ha desat la planificació, l'albarà de transportista";

                            $albFabrica = $this->crearAlbaranProveedorFabricaDesdePlanificacion($_POST, $alb['idalbaranprov']);
                            if($albFabrica['error'] == false){
                                $msg .= " i l'albarà proveïdor fàbrica.";
                            }else{
                                $msg .= " però no l'albarà del proveïdor fàbrica.";
                            }
                            $respuesta['error'] = false;
                            $respuesta['idalbarandet'] = $alb['idalbarandet'];
                            $respuesta['idalbaranprov'] = $alb['idalbaranprov'];
                            $respuesta['idalbaranfabrica'] = $this->obtenerIdAlbaranFabrica($alb['idalbarandet']);

                        }else{
                            $msg .= "S'ha actualitzat la planificació, però s'ha produït un error i no s'ha desat l'albarà de transportista ni el de proveïdor fàbrica.";
                            $respuesta['error'] = true;
                        }
    
                    }
                                    
                    $respuesta['mensaje'] = $msg;
                    $respuesta['total'] = $this->modeloPlanificacionFechas->sumTotalPlanning($_POST['idplanificacion']);
                }
    

            }else{

                $respuesta['mensaje'] = "Verifiqueu que la quantitat, el preu, el client i el proveïdor són vàlids";     

            }
            

        }else{
            $respuesta['mensaje'] = "Verifiqueu que la quantitat, el preu, el client i el proveïdor són vàlids";            
        }             
        print_r(json_encode($respuesta));
    }        

    private function obtenerIdAlbaranFabrica($idalbarandet)
    {
        $idAlbaranFabrica = 0;
        if($idalbarandet > 0){
            $idplanfecha = $this->modeloAlbaranDetalleProveedor->getIdPlanDateByIdAlbaranDet($idalbarandet); 
            if(isset($idplanfecha) && $idplanfecha > 0){
                $idAlbaranFabrica = $this->modeloAlbaranDetalleProveedor->getIdDeliveryNoticeByIdPlanDate($idplanfecha); 
            }
        }
        return $idAlbaranFabrica;
    }

    private function actualizarFacturaDesdePlanificacion($idFactura, $cantidadPlanificacion, $idalbarandet)
    {
        //FALTA TERMINAR TODO ESE APARTADO, Falta agregar un campo que relacione la fila albarán proveedor con la fila factura proveedor.
        $this->modeloBase->updateFieldTabla($this->proveedores_facturas_det, 'cantidad', $cantidadPlanificacion, $idalbarandet);
        $idAlbaran = $this->modeloAlbaranDetalleProveedor->getIdAlbaranByIdAlbaranDet($idalbarandet);
        $this->modeloBase->updateFieldTabla($this->proveedores_facturas, 'cantidad', $cantidadPlanificacion, $idAlbaran);
        
         $updFact = $this->actualizarFacturaProveedor($idFactura); 
         if($updFact){
             $msg .= " A més, s'ha actualitzat la factura corresponent.";
         }         
    }

    private function actualizarFacturaProveedor($idFactura)
    {
        $totalFactura = $this->modeloFacturaProveedor->getTotalAmountInvoice($idFactura);
        $totalRecibos = $this->modeloReciboProveedor->getTotalAmountPaidReceiptsByInvoice($idFactura);                      

        $estado = 'pendiente';

        if($totalRecibos == 0){
            $estado = 'pendiente';
        }else if($totalFactura > 0 && $totalRecibos < $totalFactura ){
            $estado = 'cobrada parcial';
        }else if($totalFactura > 0 && $totalRecibos == $totalFactura ){
            $estado = 'cobrada';
        }
              
        $upd = $this->modeloBase->updateFieldTabla($this->tabla_proveedores_facturas, 'estado', $estado, $idFactura);
        
        return $upd;
    }

    private function actualizarCeldaPlanificacionYTotales($arr){
        $upd = $this->modeloPlanificacionFechas->updateCellPlanning($arr);        
        $total = $this->modeloPlanificacionFechas->sumTotalPlanning($arr['idplanificacion']);        
        $updTotal = $this->modeloPlanificacion->updateTotalPlanning($arr['idplanificacion'], $total);        

        $retorno = 0;
        if($upd && $updTotal){
            $retorno = 1;
        }
        return $retorno;
    }    

    private function crearCabeceraAlbaranPoveedor($post, $datosCelda, $iva)
    {
        $ins = false;
        $idPlanifFecha = $post['id'];                    
        $fechaCelda = $datosCelda->fecha;                    
                
        $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla_proveedores_albaranes, date("Y",strtotime($fechaCelda)));
       
        $arrCabecera['numerointerno'] = $nextCodeInterno;
        $ceros = '';
        if($nextCodeInterno==1 || ($nextCodeInterno > 1 && $nextCodeInterno <= 9)){
            $ceros = '00';
        }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
            $ceros = '0';
        }
        $arrCabecera['numero'] = "ALB".date("y",strtotime($fechaCelda)).".".$ceros.$nextCodeInterno;
        $arrCabecera['idproveedor'] = $post['idtransportista'];
        $arrCabecera['proveedor'] = $this->modeloProveedor->getNameSupplier($post['idtransportista']);
        $arrCabecera['fecha'] = $fechaCelda;

        $baseimponible = $datosCelda->carga * $datosCelda->preciozona;
        
        $arrCabecera['baseimponible'] = $baseimponible;            
        $ivaTotal = $baseimponible * $iva /100;
        $arrCabecera['ivatotal'] = $ivaTotal;
        $arrCabecera['total'] = $baseimponible + $ivaTotal;            
        
        $stringQueries = UtilsHelper::buildStringsInsertQuery($arrCabecera, $this->fieldsCreateAlbProv);
        $ok = $stringQueries['ok'];
        $strFields = $stringQueries['strFields'];
        $strValues = $stringQueries['strValues'];
                   
        if($ok){
            $ins = $this->modeloBase->insertRow($this->tabla_proveedores_albaranes, $strFields, $strValues);                
        }                
        return $ins;
    }

    private function crearFilaDetalleAlbaranProveedor($idAlbaran, $post, $datosCelda, $iva, $tipoProv)
    {
        $tmp = [];            
        $insRow = false;
        $tmp['idalbaran'] = $idAlbaran;

        $precio = $datosCelda->preciozona;    
        
        $tmp['precio'] = $precio;
        
        $subTotal = $datosCelda->carga * $precio;
        $tmp['subtotal'] = $datosCelda->carga * $precio;
        $tmp['albaranfabrica'] = $tipoProv;
        
        if($tipoProv==0){
            $idplanfecha = $post['id'];
            $idplanfechafab = 0;
            $idproducto = $post['id_producto_compra'];
        }else{
            $idplanfecha = 0;
            $idplanfechafab = $post['id'];
            $idproducto = $this->modeloConfiguracion->getProductFactorySupplierIdDefault();           
        }

        $tmp['idproducto'] = $idproducto;
        $tmp['descripcion'] = $this->modeloProductoCompra->getNameProduct($idproducto);       
        $tmp['unidad'] = $this->modeloProductoCompra->getPurchaseProduct($idproducto)->abrev_unidad;
        $tmp['ivatipo'] = $this->modeloProductoCompra->getPurchaseProduct($idproducto)->iva;

        $tmp['cantidad'] = $datosCelda->carga;  
        
        $tmp['idplanfecha'] = $idplanfecha;
        $tmp['idplanfechafab'] = $idplanfechafab;        
  
        $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $this->fieldsRowsCreateAlbaran);
        $ok = $stringQueries['ok'];
        $strFields = $stringQueries['strFields'];
        $strValues = $stringQueries['strValues'];
        if($ok){
            $insRow = $this->modeloBase->insertRow($this->tabla_proveedores_albaranes_det, $strFields, $strValues);
        }     
        return $insRow;   
    }

    private function crearAlbaranProveedorDesdePlanificacion($post)
    {                        
            $respuesta['error'] = true;
            $respuesta['idalbarandet'] = false;
            $datosCelda = $this->modeloPlanificacionFechas->getDatesPlanningById($post['id']);                        
            $id_producto_compra = $post['id_producto_compra'];
            $ivaProductoCompra = $this->modeloProductoCompra->getPurchaseProduct($id_producto_compra)->iva;

            $crear = $this->crearCabeceraAlbaranPoveedor($post, $datosCelda, $ivaProductoCompra);

            if($crear){
                $filaDetalle = $this->crearFilaDetalleAlbaranProveedor($crear, $post, $datosCelda, $ivaProductoCompra, 0);
                if($filaDetalle){
                    $respuesta['error'] = false;
                    $respuesta['idalbarandet'] = $filaDetalle;
                    $respuesta['idalbaranprov'] = $crear;
                }
            }           
            return $respuesta;
    }

    private function crearAlbaranProveedorFabricaDesdePlanificacion($post, $idalbaran) 
    {
        $respuesta['error'] = true;
        $respuesta['idalbarandet'] = false;
        $datosCelda = $this->modeloPlanificacionFechas->getDatesPlanningById($post['id']); 
        
        $precioProvFabrica = $this->modeloConfiguracion->getPriceFactorySupplierDefault();
        $datosCelda->preciozona = $precioProvFabrica;
                
        $id_producto_compra = $this->modeloConfiguracion->getProductFactorySupplierIdDefault();           

        $post['idtransportista'] = $this->modeloConfiguracion->getIdFactorySupplierDefault();

        $ivaProductoCompra = $this->modeloProductoCompra->getPurchaseProduct($id_producto_compra)->iva;

        $crear = $this->crearCabeceraAlbaranPoveedor($post, $datosCelda, $ivaProductoCompra);

        if($crear){
            $this->modeloBase->updateFieldTabla('proveedores_albaranes', 'albaranfabrica', 1, $crear);
            $filaDetalle = $this->crearFilaDetalleAlbaranProveedor($crear, $post, $datosCelda, $ivaProductoCompra, 1);
            if($filaDetalle){
                $respuesta['error'] = false;
                $respuesta['idalbarandet'] = $filaDetalle;
                $respuesta['idalbaranprov'] = $crear;
            }
        }           
        return $respuesta;        
    }

    private function actualizarAlbaranProveedorDesdePlanificacion($post, $idalbarandet)
    {                        
            $respuesta['error'] = true;            
            $datosCelda = $this->modeloPlanificacionFechas->getDatesPlanningById($post['id']);            
            $id_producto_compra = $post['id_producto_compra'];
            $ivaProductoCompra = $this->modeloProductoCompra->getPurchaseProduct($id_producto_compra)->iva;
            
            $filaDetalle = $this->actualizarFilaDetalleAlbaranProveedor($post, $datosCelda, $ivaProductoCompra, $idalbarandet, 0);
            if($filaDetalle){
                $upd = $this->actualizarCabeceraAlbaranProveedor($post, $datosCelda, $ivaProductoCompra, $idalbarandet);
                if($upd){
                    $respuesta['error'] = false;
                    $respuesta['idalbarandet'] = $filaDetalle;
                }
            }           
            return $respuesta;
    }    

    private function actualizarAlbaranProveedorFabricaDesdePlanificacion($post, $idalbarandet)
    {    
        $respuesta['error'] = true;    
        $idplanfecha = $this->modeloAlbaranDetalleProveedor->getIdPlanDateByIdAlbaranDet($idalbarandet); 

        if(isset($idplanfecha) && $idplanfecha > 0){

            $idalbarandetFabrica = $this->modeloAlbaranDetalleProveedor->searchNoticeDeliveryDataFactorySupplierIdExist($idplanfecha);            
                            
            $datosCelda = $this->modeloPlanificacionFechas->getDatesPlanningById($post['id']);            
            $id_producto_compra = $this->modeloConfiguracion->getProductFactorySupplierIdDefault();
            $ivaProductoCompra = $this->modeloProductoCompra->getPurchaseProduct($id_producto_compra)->iva;
            
            $filaDetalle = $this->actualizarFilaDetalleAlbaranProveedor($post, $datosCelda, $ivaProductoCompra, $idalbarandetFabrica, 1);
            if($filaDetalle){

                $post['idtransportista'] = $this->modeloConfiguracion->getIdFactorySupplierDefault();

                $upd = $this->actualizarCabeceraAlbaranProveedor($post, $datosCelda, $ivaProductoCompra, $idalbarandetFabrica);

                if($upd){
                    $respuesta['error'] = false;
                    $respuesta['idalbarandet'] = $filaDetalle;
                }
            }  
        }

        return $respuesta;
    }

    private function actualizarCabeceraAlbaranProveedor($post, $datosCelda, $iva, $idalbarandet)
    {
        $upd = false;                
        $arrCabecera['idproveedor'] = $post['idtransportista'];
        $arrCabecera['proveedor'] = $this->modeloProveedor->getNameSupplier($post['idtransportista']);        


        $idAlbaran = $this->modeloAlbaranDetalleProveedor->getIdAlbaranByIdAlbaranDet($idalbarandet);

        $totales = $this->modeloAlbaranDetalleProveedor->getTotalsRowNoticeDelivery($idAlbaran);

        $baseimponible = $totales->base_imponible;        
        $arrCabecera['baseimponible'] = $baseimponible;            
        $ivaTotal = $totales->importe_iva;
        $arrCabecera['ivatotal'] = $ivaTotal;
        $arrCabecera['total'] = $totales->suma_total;
        
        $arrWhere['id'] = $idAlbaran;  
        $stringQueries = UtilsHelper::buildStringsUpdateQuery($arrCabecera, $this->fieldsUpdateAlbaranProv);
        $ok = $stringQueries['ok'];                        
             
        $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
        $okw = $stringWhere['ok'];    
                      
        if($ok && $okw){
            $strFieldsValues = $stringQueries['strFieldsValues'];
            $strWhere = $stringWhere['strWhere'];

            $upd = $this->modeloBase->updateRow($this->tabla_proveedores_albaranes, $strFieldsValues, $strWhere);
        }                
        return $upd;
    }

    private function actualizarFilaDetalleAlbaranProveedor($post, $datosCelda, $iva, $idalbarandet, $tipoProv)
    {
        $tmp = [];            
        $insUpd = false;                        
        $tmp['cantidad'] = $datosCelda->carga;      
            
        if($tipoProv==0){
            $precio = $datosCelda->preciozona;  
        }else{
            $precio = $this->modeloConfiguracion->getPriceFactorySupplierDefault();            
        }
        
        $tmp['precio'] = $precio;
        $tmp['ivatipo'] = $iva;
        $subTotal = $datosCelda->carga * $precio;
        $tmp['subtotal'] = $datosCelda->carga * $precio;
    
        $arrWhere['id'] = $idalbarandet;   

        $stringQueries = UtilsHelper::buildStringsUpdateQuery($tmp, $this->fieldsRowsUpdateAlbaranProv);
        $ok = $stringQueries['ok'];                        
            
        $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
        $okw = $stringWhere['ok'];                                           
                    
        if($ok && $okw){
            $strFieldsValues = $stringQueries['strFieldsValues'];
            $strWhere = $stringWhere['strWhere'];                  

            $insUpd = $this->modeloBase->updateRow($this->tabla_proveedores_albaranes_det, $strFieldsValues, $strWhere);
        }

        return $insUpd;   
    }

    public function verPlanificacion($idPlanificacion)
    {
        if(isset($idPlanificacion) && $idPlanificacion > 0){

            $idProductoDefault = $this->modeloConfiguracion->getProductCarrierOrProductPlanningDefault();

            $datos = [
                'idPlanificacion' => $idPlanificacion,
                'html' => $this->construirDatosTablaPlanificacionSemanal($idPlanificacion),
                'detalles' => $this->modeloPlanificacion->getPlanningById($idPlanificacion),
                'tieneDatos' => $this->modeloPlanificacionFechas->countPlanningDates($idPlanificacion),
                'unidad' => 'Kg',
                'idProducto' => $idProductoDefault,
                'nombreProducto' => $this->obtenerNombreProductoCompra($idProductoDefault)
            ];
            $this->vista('planificaciones/verPlanificacion', $datos);
        }else{
            redireccionar('/Planificaciones');
        }
        
    }

    public function obtenerDatosParaFilaNueva()
    {                               
            $retorno = [            
                'clientes' => $this->modeloProductoVenta->getAllSaleProducts(),
                'transportistas' => $this->modeloIva->getAllIvasActive()
            ]; 
        
        echo json_encode($retorno);  
    }

    public function obtenerNombreProductoCompra($idProducto = false){

        $nombre = '';
        if($idProducto){
            $nombre = $this->modeloProductoCompra->getNameProduct($idProducto);
        }
        return $nombre;
    }

    public function eliminarPlanificacion()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['id']) && $_POST['id'] > 0) {

            $existenAlbaranes_prov = $this->modeloPlanificacion->searchDeliveryNoticesSuppliersWithIdPlanning($_POST['id']);
            $existenAlbaranes_cli = $this->modeloPlanificacion->searchDeliveryNoticesClientssWithIdPlanning($_POST['id']);

            $upd = $this->modeloPlanificacion->deletePlanningById($_POST['id']);
            $updFechas = $this->modeloPlanificacionFechas->deletePlanningDatesByIdPlanning($_POST['id']);
            $updRecojos = $this->modeloPlanificacionRecojos->deletePlanningLoadByIdPlannings($_POST['id']);

            if ($upd && $updFechas && $updRecojos) {

                if($existenAlbaranes_prov && count($existenAlbaranes_prov) > 0){                    
                    $this->eliminarAlbaranesProveedorPlanificacionFechas($existenAlbaranes_prov);
                }
                
                if($existenAlbaranes_cli && count($existenAlbaranes_cli) > 0){                    
                    $this->eliminarAlbaranesClientesPlanificacionFechas($existenAlbaranes_cli);
                }

                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;
            }

        }
        print_r(json_encode($respuesta));   
    }

    public function actualizarPrecioPlanificacion(){
        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;   

        if(isset($_POST['id']) && $_POST['id'] > 0 && isset($_POST['precio']) && trim($_POST['precio']) != '') {

            $id = $_POST['id'];
            $precio = (trim($_POST['precio']) != '')? $_POST['precio']: 0;            

            $upd = $this->modeloPlanificacion->updateFieldTabla($id, 'precio', $precio);

            if ($upd) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ACTUALIZACION;                
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }

   
    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);          
                      
            $where = str_replace("fechainicio like", "DATE_FORMAT(fechainicio, '%d/%m/%Y') like", $where);
            $where = str_replace("fechafin like", "DATE_FORMAT(fechafin, '%d/%m/%Y') like", $where);
            
            $order = " ORDER BY planificaciones.semana DESC ";          
         
            $datos = $this->modeloPlanificacion->obtenerPlanificacionesExportar($order,$where);
            $nombreReporte = '_Planificaciones';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }

    public function crearFilaNuevaPlanificacion()
    { 
        $respuesta['error'] = true;
        $respuesta['mensaje'] = OK_CREACION;
        $respuesta['html'] = '';

        if(isset($this->fetch) && $this->fetch['idPlanificacion'] > 0){
            
            $idPlanning = $this->fetch['idPlanificacion'];
            $dataPlanificacion = $this->modeloPlanificacion->getPlanningById($idPlanning);
            
            $fechaInicio = $dataPlanificacion->fechainicio;
            $fechaFin = $dataPlanificacion->fechafin;          

            if(isset($fechaInicio) && $fechaInicio > 0 && $fechaInicio != ''){
                $idRecojo = $this->modeloPlanificacionRecojos->createCollectionPlanning($idPlanning); 
                $fechas = DateTimeHelper::buscarDiasEntreFechaInicioYFin($fechaInicio, $fechaFin);

                if(isset($fechas) && count($fechas) > 0){

                    $cont = 0;              
    
                    foreach ($fechas as $fecha) {
    
                        $create = $this->modeloPlanificacionFechas->createDatesPlanningById($idPlanning, $fecha['fecha'], $fecha['dia'], $idRecojo);
                        if($create) {
                            $cont++;                            
                        }
                    }
    
                    if($cont == count($fechas) > 0){
                        $respuesta['error'] = false;
                        $html = $this->construirHtmlFilaPlanificacion($idRecojo, $idPlanning);
                        $respuesta['html'] = $html;
                    }
                }                                
            }    
        }
        echo json_encode($respuesta);
    }

    public function eliminarRecojo()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = OK_CREACION;

        if(isset($this->fetch) && $this->fetch['idrecojo'] > 0){

            $idRecojo = $this->fetch['idrecojo'];
            $existenAlbaranesProv = $this->modeloPlanificacion->searchDeliveryNoticesSuppliersWithIdPlanningDate($this->fetch['idrecojo']);
            $existenAlbaranesCli = $this->modeloPlanificacion->searchDeliveryNoticesClientsWithIdPlanningDate($this->fetch['idrecojo']);
            $idplanificacion = $this->modeloPlanificacionRecojos->getPlanningIdByCollectionId($idRecojo);

            $delRecojo = $this->modeloPlanificacionRecojos->deletePlanningLoadByIdCollection($idRecojo);
            if($delRecojo){
                $delFechasRecojo = $this->modeloPlanificacionFechas->deletePlanningLoadByIdPlanningDate($idRecojo);
                if($delFechasRecojo){
                    
                    if($existenAlbaranesProv && count($existenAlbaranesProv) > 0){
                        $this->eliminarAlbaranesProveedorPlanificacionFechas($existenAlbaranesProv);
                    }
                    if($existenAlbaranesCli && count($existenAlbaranesCli) > 0){
                        $this->eliminarAlbaranesClientesPlanificacionFechas($existenAlbaranesCli);
                    }                    
                    $nuevoTotal = $this->modeloPlanificacionFechas->sumTotalPlanning($idplanificacion);
                    $this->modeloPlanificacion->updateTotalPlanning($idplanificacion, $nuevoTotal);
                    $respuesta['total'] = $nuevoTotal;
                    $respuesta['error'] = false;
                    $respuesta['mensaje'] = '';            
                }
            }
        }
        echo json_encode($respuesta);
    }

    public function eliminarAlbaranesProveedorPlanificacionFechas($albaranesArray)
    {
        foreach ($albaranesArray as $key) {
            $idAlbaran = $key->idalbaran;
            $where1 = " id = $idAlbaran ";  
            $where2 = " idalbaran = $idAlbaran ";  
            
            $updCabecera = $this->modeloBase->deleteRow($this->tabla_proveedores_albaranes, $where1);
            $updFilas = $this->modeloBase->deleteRow($this->tabla_proveedores_albaranes_det, $where2);

        }
    }

    public function eliminarAlbaranesClientesPlanificacionFechas($albaranesArray)
    {
        foreach ($albaranesArray as $key) {
            $idAlbaran = $key->idalbaran;
            $where1 = " id = $idAlbaran ";  
            $where2 = " idalbaran = $idAlbaran ";  
            $updCabecera = $this->modeloBase->deleteRow($this->tabla_clientes_albaranes, $where1);
            $updFilas = $this->modeloBase->deleteRow($this->tabla_clientes_albaranes_det, $where2);

        }
    }    

    public function buscarDatosParaPlanificacionCelda()
    {                
        $respuesta['datos'] = [];
        
        if(isset($this->fetch) && $this->fetch['idcelda'] > 0 && $this->fetch['idcliente'] > 0 && $this->fetch['idcliente'] > 0) {                        

            $respuesta['datos'] = $this->buscarDatosParaPlanificacionByIdPlanFecha($this->fetch['idcliente']);

        }

        echo json_encode($respuesta);
    }

    public function buscarDatosParaPlanificacionByIdPlanFecha($idcliente)
    {
       
        $zona = $this->modeloCliente->getZoneDataByClientId($idcliente);
        $nombreZona = (isset($zona->zona) && $zona->zona != '')? $zona->zona:'';
        $idZona = (isset($zona->id) && $zona->id > 0)? $zona->id:0;
        $precioZona = (isset($zona->margen) && $zona->margen > 0)? $zona->margen:0;

        $precio = $this->modeloCliente->getPriceByClientId($idcliente);
        $precioFormat = number_format($precio,2,",",".");

        $msgPrecioCliente = 'Client sense preu';
        if($precio > 0){
            $msgPrecioCliente = "Preu client: $precioFormat €";
        }

        $msgZona = 'Sense zona';
        if($idZona > 0){
            $msgZona = "Zona: $nombreZona";
        }
        $msgCosteZona = 'Zona sense preu';
        if($precioZona > 0){
            $msgCosteZona = "Coste: $precioZona";
        }

        $msgTransportista = $msgZona." - ".$msgCosteZona;

        $datos = [
            'nombreZona' => $nombreZona,
            'idZona' => $idZona,
            'precioCosteZona' => $precioZona,
            'precioCliente' => $precio,
            'msgPrecioCliente' => $msgPrecioCliente,
            'msgTransportista' => $msgTransportista
        ]; 
        return $datos;
    }

    public function crearAlbaranClienteDesdeCeldaPlanificacion(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;   


        if(isset($_POST['id']) && $_POST['id'] > 0 && isset($_POST['input_value']) && trim($_POST['input_value']) >0 && isset($_POST['idplanificacion']) && $_POST['idplanificacion'] > 0 && isset($_POST['idtransportista']) && $_POST['idtransportista'] > 0 && $_POST['id_producto_compra'] > 0 && isset($_POST['idcliente']) && $_POST['idcliente'] > 0) {

            $arr = $this->construirArrayDatosPlanificacionFecha($_POST);
            
            if($arr['$validar']){

                $upd = $this->actualizarCeldaPlanificacionYTotales($arr);
          
                if ($upd) {
                    
                    $idalbarandetcli = $_POST['idalbarandetcli'];

                  
                    if(isset($idalbarandetcli) && $idalbarandetcli > 0){                    
                       
                        $msg .= "No es pot actualitzar un albarà client des de la planificació. Consulteu amb l'administrador";
                        $respuesta['error'] = true;                       
    
                    }else{
                        
                        $alb = $this->crearAlbaranClienteDesdePlanificacion($_POST);
                        if($alb['error'] == false){
                            $msg = "S'ha desat la planificació i l'albarà de proveïdor";
                            $respuesta['error'] = false;
                            $respuesta['idalbarandetcli'] = $alb['idalbarandetcli'];
                            $respuesta['idalbarancli'] = $alb['idalbarancli'];
                            
                        }else{
                            $msg .= "S'ha actualitzat la planificació, però s'ha produït un error i no s'ha desat l'albarà.";
                            $respuesta['error'] = true;
                        }
    
                    }
                                    
                    $respuesta['mensaje'] = $msg;
                    $respuesta['total'] = $this->modeloPlanificacionFechas->sumTotalPlanning($_POST['idplanificacion']);
                }
    

            }else{

                $respuesta['mensaje'] = "Verifiqueu que la quantitat, el preu, el client i el proveïdor són vàlids";     

            }
            

        }else{
            $respuesta['mensaje'] = "Verifiqueu que la quantitat, el preu, el client i el proveïdor són vàlids";            
        }             
        print_r(json_encode($respuesta));
    }        
    


    private function crearAlbaranClienteDesdePlanificacion($post)
    {
        $respuesta['error'] = true;
        $respuesta['idalbarandetcli'] = false;
        $datosCelda = $this->modeloPlanificacionFechas->getDatesPlanningById($post['id']);
        $datosCliente = $this->modeloCliente->getClientById($post['idcliente']);

        $id_producto_compra = $post['id_producto_compra'];
        $productoVenta = $this->modeloProductoVenta->getSaleProductByPurchaseProductId($id_producto_compra);

        $crear = $this->crearCabeceraAlbaranCliente($datosCelda, $datosCliente, $productoVenta);

        if($crear){
            $filaDetalle = $this->crearFilaDetalleAlbaranCliente($crear, $post, $datosCelda, $productoVenta);
            if($filaDetalle){
                $respuesta['error'] = false;
                $respuesta['idalbarandetcli'] = $filaDetalle;
                $respuesta['idalbarancli'] = $crear;

                if(API_GOOGLE_DRIVE==1){
                    //crear PDF a GoogleDrive
                    $albaran = $this->modeloAlbaranCliente->getAlbaranData($crear);
                    $numAlb = $albaran->numero;
                    $nombreArchivo = "Albaran_{$numAlb}.pdf";
                    GoogleDriveUploader::subirArchivo($crear, $datosCelda->fecha, 'Albaranes Cliente', $nombreArchivo);
                }

            }
        }           
        return $respuesta;
    }

    private function crearCabeceraAlbaranCliente($datosCelda, $datosCliente, $productoVenta)
    {
        $ins = false;                        
        $fechaCelda = $datosCelda->fecha;                    
                
        $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla_clientes_albaranes, date("Y",strtotime($fechaCelda)));
       
        $arrCabecera['numerointerno'] = $nextCodeInterno;   
        $ceros = '';
        if($nextCodeInterno==1 || ($nextCodeInterno > 1 && $nextCodeInterno <= 9)){
            $ceros = '00';
        }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
            $ceros = '0';
        }             
        $arrCabecera['numero'] = "ALB".date("y",strtotime($fechaCelda)).".".$ceros.$nextCodeInterno;
        $arrCabecera['idcliente'] = $datosCelda->idcliente;
        $arrCabecera['cliente'] = $datosCliente->nombrefiscal;
        $arrCabecera['fecha'] = $fechaCelda;
        $precioCliente = $datosCelda->preciocliente;

        $baseimponible = $datosCelda->carga * $precioCliente;
        
        $arrCabecera['baseimponible'] = $baseimponible;            
        $iva = $productoVenta->iva;
        $ivaTotal = $baseimponible * $iva /100;
        $arrCabecera['ivatotal'] = $ivaTotal;
        $arrCabecera['total'] = $baseimponible + $ivaTotal;            

        $stringQueries = UtilsHelper::buildStringsInsertQuery($arrCabecera, $this->fieldsCreateAlbCli);
        $ok = $stringQueries['ok'];
        $strFields = $stringQueries['strFields'];
        $strValues = $stringQueries['strValues'];
                   
        if($ok){
            $ins = $this->modeloBase->insertRow($this->tabla_clientes_albaranes, $strFields, $strValues);                
        }                
        return $ins;
    }

    private function crearFilaDetalleAlbaranCliente($idAlbaran, $post, $datosCelda, $productoVenta)
    {
        $tmp = [];            
        $insRow = false;
        $tmp['idalbaran'] = $idAlbaran;
        $tmp['idproducto'] = $productoVenta->id;
        $tmp['descripcion'] = $productoVenta->descripcion;
        $tmp['unidad'] = $productoVenta->abrev_unidad;        
        $tmp['cantidad'] = $datosCelda->carga;    
        $precio = $datosCelda->preciocliente;    
        $tmp['precio'] = $precio;
        $tmp['ivatipo'] = $productoVenta->iva;
        $subTotal = $datosCelda->carga * $precio;
        $tmp['subtotal'] = $datosCelda->carga * $precio;       
        $tmp['idplanfecha'] = $post['id'];

        $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $this->fieldsRowsCreateAlbaranCli);
        $ok = $stringQueries['ok'];
        $strFields = $stringQueries['strFields'];
        $strValues = $stringQueries['strValues'];
        if($ok){
            $insRow = $this->modeloBase->insertRow($this->tabla_clientes_albaranes_det, $strFields, $strValues);
        }     
        return $insRow;   
    }

    public function verificarSiExistenAlbaranesPorIdRecojo()
    {
        $respuesta['existe'] = [];
        
        if(isset($this->fetch) && $this->fetch['idrecojo'] > 0) {                        
            $respuesta['existe_proveedor'] = $this->modeloPlanificacion->searchDeliveryNoticesSuppliersWithIdPlanningDate($this->fetch['idrecojo']);
            $respuesta['existe_cliente'] = $this->modeloPlanificacion->searchDeliveryNoticesClientsWithIdPlanningDate($this->fetch['idrecojo']);

        }
        echo json_encode($respuesta);
    }
    
    public function verificarSiExistenAlbaranesPorIdPlanificacion()
    {
        $respuesta['existe'] = [];
        
        if(isset($this->fetch) && $this->fetch['idPlanificacion'] > 0) {                        

            $respuesta['existe_proveedor'] = $this->modeloPlanificacion->searchDeliveryNoticesSuppliersWithIdPlanning($this->fetch['idPlanificacion']);
            $respuesta['existe_cliente'] = $this->modeloPlanificacion->searchDeliveryNoticesClientssWithIdPlanning($this->fetch['idPlanificacion']);

        }
        echo json_encode($respuesta);
    }

    public function verificarSiPlanificacionSePuedeEliminar()
    {
        $respuesta['error'] = false;
        
        if(isset($this->fetch) && $this->fetch['idPlanificacion'] > 0) {                        

            $idPlanificacion = $this->fetch['idPlanificacion'];
            $planificacion = $this->modeloPlanificacion->getPlanningById($idPlanificacion);
            $fechas = $this->modeloPlanificacionFechas->countPlanningDates($idPlanificacion);
            $recojos = $this->modeloPlanificacionRecojos->countCollectionPlanningDates($idPlanificacion);
                        
            if((!isset($planificacion->fechainicio) || !isset($planificacion->fechafin)) && $fechas == 0 && $recojos==0){
                $this->modeloPlanificacion->deletePlanningById($idPlanificacion);            
            }
        }
        echo json_encode($respuesta);
    }



    ///////////////=============================///////////////
    // métodos para corregir datos en base de datos
    ///////////////=============================///////////////
    public function testCrearAlbaranesFabrica()
    {              

        die;
        
        $albaranesTrans = $this->modeloAlbaranProveedor->testObtenerTodosLosAlbaranesProveedores();

        echo"<br>entra a testCrearAlbaranesFabrica<br>";
        echo"<br>imprime albaranesTrans<br>";
        print_r($albaranesTrans);
      

        $cont = 0;
        $contCab = count($albaranesTrans);

        echo"<br>imprime contCab<br>";
        print_r($contCab);
      
        //die;

        foreach ($albaranesTrans as $key) {                    

            $idAlbaranCab = false;                         
            $fechaCelda = $key->fecha;                    
                    
            $nextCodeInterno = $this->modeloBase->maximoNumDocumentoAnio('numerointerno',$this->tabla_proveedores_albaranes, date("Y",strtotime($fechaCelda)));
           
            $arrCabecera['numerointerno'] = $nextCodeInterno;
            $ceros = '';
            if($nextCodeInterno==1 || ($nextCodeInterno > 1 && $nextCodeInterno <= 9)){
                $ceros = '00';
            }else if($nextCodeInterno > 9 && $nextCodeInterno <= 99){
                $ceros = '0';
            }
            $arrCabecera['numero'] = "ALB".date("y",strtotime($fechaCelda)).".".$ceros.$nextCodeInterno;
            $arrCabecera['idproveedor'] = 1;
            $arrCabecera['proveedor'] = $this->modeloProveedor->getNameSupplier(1);
            $arrCabecera['fecha'] = $fechaCelda;
    
            $baseimponible = 0;
            
            $arrCabecera['baseimponible'] = $baseimponible;            
            $ivaTotal = 0;
            $arrCabecera['ivatotal'] = $ivaTotal;
            $arrCabecera['total'] = $baseimponible + $ivaTotal;            
            
            $stringQueries = UtilsHelper::buildStringsInsertQuery($arrCabecera, $this->fieldsCreateAlbProv);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];
                       
            if($ok){
                $idAlbaranCab = $this->modeloBase->insertRow($this->tabla_proveedores_albaranes, $strFields, $strValues);  

                if($idAlbaranCab > 0)           {
                    $cont++;
                    
                    ////////////crear filas                         
                    $rowsOrigen = $this->modeloAlbaranDetalleProveedor->getRowsAlbaran($key->id);

                    
                    /*echo"<br>imprime rowsOrigen<br>";
                    print_r($rowsOrigen);*/
                    //die;


                    foreach ($rowsOrigen as $r) {
                        
                        if(isset($r) && isset($r->idplanfecha) && $r->idplanfecha > 0){

                            $insRow = false;
                            $tmp['idalbaran'] = $idAlbaranCab;
                    
                            $precio = 33;    
                            
                            $tmp['precio'] = $precio;
                            
                            $subTotal = $r->cantidad * $precio;
                            $tmp['subtotal'] = $r->cantidad * $precio;
                            $tmp['albaranfabrica'] = 1;
                        
                            $idplanfecha = 0;
                            $idplanfechafab = $r->idplanfecha;
                            $idproducto = $this->modeloConfiguracion->getProductFactorySupplierIdDefault();           
                        
                    
                            $tmp['idproducto'] = $idproducto;
                            $tmp['descripcion'] = $this->modeloProductoCompra->getNameProduct($idproducto);       
                            $tmp['unidad'] = $this->modeloProductoCompra->getPurchaseProduct($idproducto)->abrev_unidad;
                            $tmp['ivatipo'] = $this->modeloProductoCompra->getPurchaseProduct($idproducto)->iva;
                    
                            $tmp['cantidad'] = $r->cantidad;  
                            
                            $tmp['idplanfecha'] = $idplanfecha;
                            $tmp['idplanfechafab'] = $idplanfechafab;        
                      
                            $stringQueries = UtilsHelper::buildStringsInsertQuery($tmp, $this->fieldsRowsCreateAlbaran);
                            $ok = $stringQueries['ok'];
                            $strFields = $stringQueries['strFields'];
                            $strValues = $stringQueries['strValues'];
                            if($ok){
                                $insRow = $this->modeloBase->insertRow($this->tabla_proveedores_albaranes_det, $strFields, $strValues);
                            }               

                        }
                    }
                                                  
                    //actualizar cabecera
                    $this->modeloBase->updateFieldTabla('proveedores_albaranes', 'albaranfabrica', 1, $idAlbaranCab);                    

                    $arrCabecera['idproveedor'] = 1;
                    $arrCabecera['proveedor'] = $this->modeloProveedor->getNameSupplier(1);        
            
                    $totales = $this->modeloAlbaranDetalleProveedor->getTotalsRowNoticeDelivery($idAlbaranCab);

                    $baseimponible = $totales->base_imponible;        
                    $arrCabecera['baseimponible'] = $baseimponible;            
                    $ivaTotal = $totales->importe_iva;
                    $arrCabecera['ivatotal'] = $ivaTotal;
                    $arrCabecera['total'] = $totales->suma_total;

                    $arrWhere['id'] = $idAlbaranCab;  
                    $stringQueries = UtilsHelper::buildStringsUpdateQuery($arrCabecera, $this->fieldsUpdateAlbaranProv);
                    $ok1 = $stringQueries['ok'];                        
                         
                    $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
                    $okw = $stringWhere['ok'];    
                                  
                    if($ok1 && $okw){
                        $strFieldsValues = $stringQueries['strFieldsValues'];
                        $strWhere = $stringWhere['strWhere'];
            
                        $upd = $this->modeloBase->updateRow($this->tabla_proveedores_albaranes, $strFieldsValues, $strWhere);
                    }         

                }
            }                    
        }

        echo"<br>Se han contado: " .$contCab. " albaranes de Eusebio<br>";
        echo"<br>Se han creado: " .$cont. " albaranes de Cervecería<br>";

    }

    public function testCrearSemanaEnPlanificaciones(){ //correr este método para actualizar BD

        $planificaciones = $this->modeloPlanificacion->obtenerPlanificaciones();

        if(isset($planificaciones) && count($planificaciones) > 0){
            $cont=0;
            foreach ($planificaciones as $plan) {
                
                if(isset($plan->fechainicio) && $plan->fechainicio != '' && $plan->fechainicio > 0 ){
                
                    $semana_number = UtilsHelper::getWeekNumberByDate($plan->fechainicio);
                    $semana_string = date("y",strtotime($plan->fechainicio))."/".$semana_number;

                    $updSemana = $this->modeloBase->updateFieldTabla($this->tabla, 'semana', $semana_string, $plan->id);
                    if($updSemana){
                        $cont++;
                    }
                }

            }
            
        }
        echo"Se han contado ". count($planificaciones). " planificaciones. Y se han actualizado ".$cont." planificaciones. ";

    }

    
    public function testCrearSemanaEnPlanificacionesTwo(){ 

        $planificaciones = $this->modeloPlanificacion->obtenerPlanificaciones();

        if(isset($planificaciones) && count($planificaciones) > 0){
            $cont=0;
            foreach ($planificaciones as $plan) {
                
                if(isset($plan->fechainicio) && $plan->fechainicio != '' && $plan->fechainicio > 0 ){
                
                    $semana_number_ini = UtilsHelper::getWeekNumberByDate($plan->fechainicio);
                    //$semana_string = date("y",strtotime($plan->fechainicio))."/".$semana_number_ini;

                    $semana_number_fin = UtilsHelper::getWeekNumberByDate($plan->fechafin);
                    //$semana_string = date("y",strtotime($plan->fechafin))."/".$semana_number_fin;

                    echo"Semana fecha inicio ".$semana_number_ini." - Semana fecha fin ".$semana_number_fin."<br>";
                }

            }
            
        }        
    }

        
    public function eliminarCeldaPlanificacionFecha()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = "S'ha produït un error i no s'ha pogut eliminar la planificació seleccionada";

        if(isset($_POST['id']) && $_POST['id'] > 0 && isset($_POST['idplanificacion']) && $_POST['idplanificacion'] > 0) {
            
            $idsAlbProv = $this->modeloAlbaranDetalleProveedor->getDeliveryNotesIdsSupplierByIdPlanDates($_POST['id']);
            $facturaMayorACeroProv = !empty(array_filter($idsAlbProv, function($row) {
                return $row->idfactura > 0;
            }));

            $idAlbCli = $this->modeloAlbaranDetalleCliente->getDeliveryNotesIdsCliByIdPlanDates($_POST['id']);
            $facturaMayorACeroCli = !empty(array_filter($idsAlbProv, function($row) {
                return $row->idfactura > 0;
            }));

           
            if ($facturaMayorACeroProv || $facturaMayorACeroCli) {

                $respuesta['mensaje'] = "Hi ha factures vinculades als albarans generats. No es pot eliminar la planificació.";

            } else {                             
               
                $_POST['carga'] = 0;
                $_POST['idtransportista']= 0;
                $_POST['idcliente'] = 0;
                $_POST['idzona'] = 0;
                $_POST['preciocliente'] = 0;
                $_POST['preciozona'] = 0;
    
                if(!empty($idsAlbProv)){                
                    foreach ($idsAlbProv as $idAlbProv) {
                        $this->modeloAlbaranDetalleProveedor->deleteDeliveryNotesLines($idAlbProv->idalbaran);
                        $this->modeloAlbaranProveedor->deleteDeliveryNotes($idAlbProv->idalbaran);
                    }
                }               

                if(!empty($idAlbCli)){                
                    foreach ($idAlbCli as $idAlbCli) {
                        $this->modeloAlbaranDetalleCliente->deleteDeliveryNotesLines($idAlbCli->idalbaran);
                        $this->modeloAlbaranCliente->deleteDeliveryNotes($idAlbCli->idalbaran);
                    }
                }   
        
                $upd = $this->actualizarCeldaPlanificacionYTotales($_POST);
                  
                if ($upd) {
                    $respuesta['error'] = false;
                    $respuesta['mensaje'] = "S'ha eliminat la planificació seleccionada";
                    $respuesta['total'] = $this->modeloPlanificacionFechas->sumTotalPlanning($_POST['idplanificacion']);
                }                
            }            

        }
        print_r(json_encode($respuesta));
    }


}
