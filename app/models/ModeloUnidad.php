<?php


class ModeloUnidad{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function getAllUnits()
    {        
        $this->db->query("SELECT * FROM unidades ");
        $filas = $this->db->registros();
        return $filas;
    }


    
}