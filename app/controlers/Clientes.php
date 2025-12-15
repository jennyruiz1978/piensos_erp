<?php

class Clientes extends Controlador {

   

    public function __construct() {
        session_start();
        $this->modeloCliente = $this->modelo('ModeloCliente');
        $this->modeloZona = $this->modelo('ModeloZona');       
        $this->modeloFormasPago = $this->modelo('ModeloFormasPago');         

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        } 
    }    

    public function index()
    {        
        $zonas = $this->modeloZona->getZones();        
        $datos = ['zonas' => $zonas, 'formacobro' => $this->modeloFormasPago->getPaymentForms()];
        $this->vista('clientes/clientes', $datos);
    }

    public function obtenerClientes(){
        $salida = $this->modeloCliente->getClients();
        print_r(json_encode($salida));
    }

    public function tablaClientes()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);

        if ($where == "") {
            $where = " WHERE status <> 'eliminado' ";
        }else{
            $where .= " AND status <> 'eliminado' ";
        }                            
        
        $clients = $this->modeloCliente->obtenerClientesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloCliente->obtenerTotalClientes($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $clients
        ];

        print_r(json_encode($salida));
    }

    public function altaClientes()
    {
        $zonas = $this->modeloZona->getZones();        
        $datos = [
            'zonas' => $zonas,
            'formacobro' => $this->modeloFormasPago->getPaymentForms()
        ];
        
        $this->vista('clientes/altaClientes', $datos);
    }
    
    public function crearCliente()
    {

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_CREACION;

        if($_POST['nombre_cliente'] != '' && $_POST['nif_cliente'] != '' && isset($_POST['zona_cliente']) && $_POST['zona_cliente'] != '' && $_POST['precio_cliente'] > 0) {

            $arr['nombrefiscal'] = strtoupper($_POST['nombre_cliente']);
            $arr['nif'] = $_POST['nif_cliente'];          
            $arr['precio'] = $_POST['precio_cliente'];  
            $arr['idzona'] = (isset($_POST['zona_cliente']))? $_POST['zona_cliente']: 0;
            $arr['zona'] = (isset($_POST['zona_cliente']) && $_POST['zona_cliente'] >  0)? $this->modeloZona->getNameZoneById($_POST['zona_cliente']): '';
            $arr['direccion'] = (isset($_POST['direccion_cliente']))? $_POST['direccion_cliente']: '';
            $arr['poblacion'] = (isset($_POST['poblacion_cliente']))? $_POST['poblacion_cliente']: '';
            $arr['codigopostal'] = (isset($_POST['codigo_postal_cliente']))? $_POST['codigo_postal_cliente']: '';
            $arr['provincia'] = (isset($_POST['provincia_cliente']))? $_POST['provincia_cliente']: '';
            $arr['telefono'] = (isset($_POST['telefono_cliente']))? $_POST['telefono_cliente']: '';
            $arr['email'] = (isset($_POST['email_cliente']))? $_POST['email_cliente']: '';
            $arr['observaciones'] = (isset($_POST['observaciones_cliente']))? $_POST['observaciones_cliente']: '';
            $arr['status'] = (isset($_POST['estado_cliente']) && $_POST['estado_cliente'] != '')? $_POST['estado_cliente']: 'activo';
            $arr['formacobro'] = (isset($_POST['formacobro']) && $_POST['formacobro'] != '')? $_POST['formacobro']: null;

            // Procesar los contactos
            $arr['contactos'] = (isset($_POST['contactos']))? $_POST['contactos']: '[]';

            $ins = $this->modeloCliente->addClient($arr);
            if ($ins && $ins >0) {               
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_CREACION;
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }   

    
    public function obtenerCliente()
    {           
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        $idCliente = false;       

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idCliente = $this->fetch['id'];
        }else if(isset($_POST['id']) && $_POST['id'] > 0) {
            $idCliente = $_POST['id'];
        }
        if($idCliente){
            $upd = $this->modeloCliente->getClientById($idCliente);
            if ($upd) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';
                $respuesta['datos'] = $upd;
            }
        }                       
        print_r(json_encode($respuesta));
    }  

    public function actualizarCliente(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;   

        if(isset($_POST['id']) && $_POST['id'] > 0 && $_POST['nombre_cliente'] != '' && $_POST['nif_cliente'] != '' && isset($_POST['zona_cliente']) && $_POST['zona_cliente'] != '' && $_POST['precio_cliente'] > 0) {    

            $arr['id'] = $_POST['id'];
            $arr['nombrefiscal'] = strtoupper($_POST['nombre_cliente']);
            $arr['nif'] = $_POST['nif_cliente'];
            $arr['precio'] = $_POST['precio_cliente'];  
            $arr['idzona'] = (isset($_POST['zona_cliente']))? $_POST['zona_cliente']: 0;
            $arr['zona'] = (isset($_POST['zona_cliente']) && $_POST['zona_cliente'] >  0)? $this->modeloZona->getNameZoneById($_POST['zona_cliente']): '';
            $arr['direccion'] = (isset($_POST['direccion_cliente']))? $_POST['direccion_cliente']: '';
            $arr['poblacion'] = (isset($_POST['poblacion_cliente']))? $_POST['poblacion_cliente']: '';
            $arr['codigopostal'] = (isset($_POST['codigo_postal_cliente']))? $_POST['codigo_postal_cliente']: '';
            $arr['provincia'] = (isset($_POST['provincia_cliente']))? $_POST['provincia_cliente']: '';
            $arr['telefono'] = (isset($_POST['telefono_cliente']))? $_POST['telefono_cliente']: '';
            $arr['email'] = (isset($_POST['email_cliente']))? $_POST['email_cliente']: '';
            $arr['observaciones'] = (isset($_POST['observaciones_cliente']))? $_POST['observaciones_cliente']: '';
            $arr['status'] = (isset($_POST['estado_cliente']) && $_POST['estado_cliente'] != '')? $_POST['estado_cliente']: 'activo';                   
            $arr['formacobro'] = (isset($_POST['formacobro']) && $_POST['formacobro'] != '')? $_POST['formacobro']: null;   

            // Procesar los contactos
            $arr['contactos'] = (isset($_POST['contactos']))? $_POST['contactos']: '[]';

            $upd = $this->modeloCliente->updateClient($arr);
            if ($upd && $upd >0) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ACTUALIZACION;
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }

    public function eliminarCliente()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['id']) && $_POST['id'] > 0) {

            $upd = $this->modeloCliente->deleteClientById($_POST['id']);
            if ($upd) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }


    public function exportarExcel()
    {             
            $where = base64_decode($_POST['cadenaCriterios']);          
            
            if ($where == "") {
                $where = " WHERE status <> 'eliminado' ";
            }else{
                $where .= " AND status <> 'eliminado' ";
            }             
            $order = " ORDER BY nombrefiscal ASC ";          
         
            $datos = $this->modeloCliente->obtenerClientesExportar($order,$where);
            $nombreReporte = '_Clientes';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }


   
}
