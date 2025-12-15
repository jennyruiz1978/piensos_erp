<?php


class ModeloFormasPago{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function obtenerFormasPagoTabla($page,$order,$where,$limit)
    {        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT * FROM formas_pago $where $order LIMIT $pagina , $limit ");
        $filas = $this->db->registros();
        return $filas;
    }

    public function obtenerTotalFormasPago($where)
    {
        $this->db->query("SELECT count(*) as contador FROM formas_pago $where ");
        $filas = $this->db->registro();
        return $filas->contador;
    }
    public function getPaymentForms()
    {        
        $this->db->query("SELECT * FROM formas_pago");
        $filas = $this->db->registros();
        return $filas;
    }

    
}