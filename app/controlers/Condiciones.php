<?php

class Condiciones extends Controlador
{

    public function __construct()
    {
        session_start();        
        $this->modeloCondiciones = $this->modelo('ModeloCondiciones');
    }

    public function index()
    {        
        $datos = [];
        $this->vista('condiciones/condiciones', $datos);
    }

    public function tablaCondiciones()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);
                        
        
        $condiciones = $this->modeloCondiciones->obtenerCondicionesTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloCondiciones->obtenerTotalCondiciones($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $condiciones
        ];

        print_r(json_encode($salida));
    }

}
