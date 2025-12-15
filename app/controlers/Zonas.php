<?php

class Zonas extends Controlador {

   

    public function __construct() {
        session_start();        
        $this->modeloZona = $this->modelo('ModeloZona');
    }    

    public function index()
    {                
        $datos = [];
        $this->vista('zonas/zonas', $datos);
    }

    public function tablaZonas()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);
        
        $where = str_replace("margen like", "FORMAT(margen,2,'es_ES') like", $where);

        if ($where == "") {
            $where = " WHERE status <> 'eliminado' ";
        }else{
            $where .= " AND status <> 'eliminado' ";
        }                            
        
        $zonas = $this->modeloZona->getZonesTable($page,$order,$where,$limit);
        $totalRegistros = $this->modeloZona->getZonesTotal($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $zonas
        ];

        print_r(json_encode($salida));
    }

    public function altaZonas()
    {        
        $datos = [];
        $this->vista('zonas/altaZona', $datos);
    }
    
    public function crearZona()
    {
     
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_CREACION;

        if(trim($_POST['nombre_zona']) != '' && $_POST['precio_zona'] != '') {

            $arr['zona'] = $_POST['nombre_zona'];
            $arr['margen'] = $_POST['precio_zona'];                   
            $arr['status'] = (isset($_POST['estado_zona']) && $_POST['estado_zona'] != '')? $_POST['estado_zona']: 'activo';             

            $ins = $this->modeloZona->addZone($arr);
            if ($ins && $ins >0) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_CREACION;
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }

    
    public function obtenerZona()
    {

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['id']) && $_POST['id'] > 0) {

            $upd = $this->modeloZona->getZoneById($_POST['id']);
            if ($upd) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';
                $respuesta['datos'] = $upd;
            }

        }
        print_r(json_encode($respuesta));
    }  

    public function actualizarZona(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;   

        if(isset($_POST['id']) && $_POST['id'] > 0 && $_POST['nombre_zona'] != '' && $_POST['precio_zona'] != '') {


            $arr['id'] = $_POST['id'];
            $arr['zona'] = $_POST['nombre_zona'];
            $arr['margen'] = $_POST['precio_zona'];                   
            $arr['status'] = (isset($_POST['estado_zona']) && $_POST['estado_zona'] != '')? $_POST['estado_zona']: 'activo';

            $upd = $this->modeloZona->updateZone($arr);
            if ($upd && $upd >0) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ACTUALIZACION;
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }

    public function eliminarZona()
    {        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['id']) && $_POST['id'] > 0) {

            $upd = $this->modeloZona->deleteZoneById($_POST['id']);
            if ($upd) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }


   
}
