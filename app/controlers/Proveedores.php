<?php

class Proveedores extends Controlador {
    
    private $fetch;

    public function __construct() {
        session_start();
        $this->modeloProveedor = $this->modelo('ModeloProveedor');

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        }        
    }    

    public function index()
    {        
        $datos = [];

        $this->vista('proveedores/proveedores', $datos);
    }

    public function obtenerProveedoresExportar(){
        $salida = $this->modeloProveedor->getSuppliers();
        print_r(json_encode($salida));
    }

    public function tablaProveedores()
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
        
        $proveedores = $this->modeloProveedor->obtenerProveedoresTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloProveedor->obtenerTotalProveedores($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $proveedores
        ];

        print_r(json_encode($salida));
    }

    public function altaProveedores()
    {
        $this->vista('proveedores/altaProveedores');
    }
    
    public function crearProveedor()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_CREACION;

        if($_POST['nombre_proveedor'] != '' && $_POST['nif_proveedor'] != '') {

            $arr['nombrefiscal'] = strtoupper($_POST['nombre_proveedor']);
            $arr['nif'] = $_POST['nif_proveedor'];
            $arr['direccion'] = (isset($_POST['direccion_proveedor']))? $_POST['direccion_proveedor']: '';
            $arr['poblacion'] = (isset($_POST['poblacion_proveedor']))? $_POST['poblacion_proveedor']: '';
            $arr['codigopostal'] = (isset($_POST['codigo_postal_proveedor']))? $_POST['codigo_postal_proveedor']: '';
            $arr['provincia'] = (isset($_POST['provincia_proveedor']))? $_POST['provincia_proveedor']: '';
            $arr['telefono'] = (isset($_POST['telefono_proveedor']))? $_POST['telefono_proveedor']: '';
            $arr['email'] = (isset($_POST['email_proveedor']))? $_POST['email_proveedor']: '';
            $arr['observaciones'] = (isset($_POST['observaciones_proveedor']))? $_POST['observaciones_proveedor']: '';
            $arr['status'] = (isset($_POST['estado_proveedor']) && $_POST['estado_proveedor'] != '')? $_POST['estado_proveedor']: 'activo';                      

            $ins = $this->modeloProveedor->addSupplier($arr);
            if ($ins && $ins >0) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_CREACION;
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }

    
    public function obtenerProveedor()
    {

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        $idProveedor = false;       

        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            $idProveedor = $this->fetch['id'];
        }else if(isset($_POST['id']) && $_POST['id'] > 0) {
            $idProveedor = $_POST['id'];
        }
        if($idProveedor){
            $upd = $this->modeloProveedor->getSupplierById($idProveedor);
            if ($upd) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';
                $respuesta['datos'] = $upd;
            }
        }                       
        print_r(json_encode($respuesta));
    }  
    
    public function actualizarProveedor(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;   

        if(isset($_POST['id']) && $_POST['id'] > 0 && $_POST['nombre_proveedor'] != '' && $_POST['nif_proveedor'] != '') {

            
            $arr['id'] = $_POST['id'];
            $arr['nombrefiscal'] = strtoupper($_POST['nombre_proveedor']);
            $arr['nif'] = $_POST['nif_proveedor'];
            $arr['direccion'] = (isset($_POST['direccion_proveedor']))? $_POST['direccion_proveedor']: '';
            $arr['poblacion'] = (isset($_POST['poblacion_proveedor']))? $_POST['poblacion_proveedor']: '';
            $arr['codigopostal'] = (isset($_POST['codigo_postal_proveedor']))? $_POST['codigo_postal_proveedor']: '';
            $arr['provincia'] = (isset($_POST['provincia_proveedor']))? $_POST['provincia_proveedor']: '';
            $arr['telefono'] = (isset($_POST['telefono_proveedor']))? $_POST['telefono_proveedor']: '';
            $arr['email'] = (isset($_POST['email_proveedor']))? $_POST['email_proveedor']: '';
            $arr['observaciones'] = (isset($_POST['observaciones_proveedor']))? $_POST['observaciones_proveedor']: '';
            $arr['status'] = (isset($_POST['estado_proveedor']) && $_POST['estado_proveedor'] != '')? $_POST['estado_proveedor']: 'activo';     

            $upd = $this->modeloProveedor->updateSupplier($arr);
            if ($upd && $upd >0) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ACTUALIZACION;
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }

    public function eliminarProveedor()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['id']) && $_POST['id'] > 0) {

            $upd = $this->modeloProveedor->deleteSupplierById($_POST['id']);
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

            $order = " ORDER BY nombrefiscal ASC ";          
         
            $datos = $this->modeloProveedor->obtenerProveedoresExportar($order,$where);
            $nombreReporte = 'Proveedores';
            ExportImportExcel::prepareDataToExportExcel($datos, $nombreReporte);            
        
    }


   
}
