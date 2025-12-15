<?php


class ModeloCondiciones{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function obtenerCondicionesTabla($page,$order,$where,$limit)
    {        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT * FROM condiciones $where $order LIMIT $pagina , $limit ");
        $filas = $this->db->registros();
        return $filas;
    }

    public function obtenerTotalCondiciones($where)
    {
        $this->db->query("SELECT count(*) as contador FROM condiciones $where ");
        $filas = $this->db->registro();
        return $filas->contador;
    }

    
    public function getPaymentConditions()
    {
        $this->db->query("SELECT * FROM condiciones");
        $filas = $this->db->registros();
        return $filas;
    }

    
}