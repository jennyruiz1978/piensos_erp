<?php


class ModeloIva{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function getAllIvasActive()
    {        
        $this->db->query("SELECT * FROM tiposiva WHERE status = 'activo' ");
        $filas = $this->db->registros();
        return $filas;
    }


    
}