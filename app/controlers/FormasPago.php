<?php

class FormasPago extends Controlador
{

    public function __construct()
    {
        session_start();        
        $this->modeloFormasPago = $this->modelo('ModeloFormasPago');
    }

    public function index()
    {        
        $datos = [];
        $this->vista('formasPago/formasPago', $datos);
    }

    public function tablaFormasPago()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);
                        
        
        $formasPago = $this->modeloFormasPago->obtenerFormasPagoTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloFormasPago->obtenerTotalFormasPago($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $formasPago
        ];

        print_r(json_encode($salida));
    }

}
