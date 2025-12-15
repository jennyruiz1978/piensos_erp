<?php


class ModeloZona{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }


    public function getZones(){
        $this->db->query('SELECT * FROM zonas');

        $resultado = $this->db->registros();

        return $resultado;
    }

    public function getZonesTable($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT id, zona, FORMAT(margen,2,'es_ES') AS margen, status
                        FROM zonas                        
                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function getZonesTotal($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM zonas                      
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }

    public function getNameZoneById($id){
        $this->db->query("SELECT zona FROM zonas WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila->zona;
    }

    public function addZone($arr){

        $zona = $arr['zona'];
        $margen = $arr['margen'];
        

        $this->db->query("INSERT INTO zonas (zona,margen) 
                        VALUES ('$zona','$margen')");
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return 0;
        }

    }    

    public function getZoneById($id){
        $this->db->query("SELECT * FROM zonas WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila;
    }

    
    public function deleteZoneById($id){

        $this->db->query("UPDATE zonas SET status = 'eliminado' WHERE id = $id ");

        if($this->db->execute()){
            return 1;
        }else {
            return 0;
        }
    }

    public function updateZone($datos){

        $id = $datos['id'];
        $zona = $datos['zona'];
        $margen = $datos['margen'];
        $status = $datos['status'];

        $this->db->query("UPDATE zonas SET zona = '$zona', margen = '$margen', status = '$status'
                        WHERE id = $id ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }


}