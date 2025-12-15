<?php


class ModeloPlanificacionRecojos{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }


    public function getCollectionPlanning($idPlanning){
        $this->db->query("SELECT * FROM planificaciones_recojos WHERE idplanificacion = '$idPlanning' ");
        $filas = $this->db->registros();
        return $filas;
    }

    public function createCollectionPlanning($idPlanning){

        $this->db->query("INSERT INTO planificaciones_recojos (idplanificacion) 
                        VALUES ('$idPlanning')");
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return 0;
        }
    }

    public function deletePlanningLoadByIdPlannings($idPlanning){
        
        $this->db->query("DELETE FROM planificaciones_recojos WHERE idplanificacion = '$idPlanning' ");
        
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    
    public function deletePlanningLoadByIdCollection($idCollection){
        
        $this->db->query("DELETE FROM planificaciones_recojos WHERE id = '$idCollection' ");
        
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    } 
 
    public function getPlanningIdByCollectionId($idCollection){
        $this->db->query("SELECT idplanificacion FROM planificaciones_recojos WHERE id = '$idCollection' ");
        $fila = $this->db->registro();
        return $fila->idplanificacion;
    }   

    public function countCollectionPlanningDates($idPlanificacion){
        $this->db->query("SELECT COUNT(*) as contador FROM planificaciones_recojos WHERE idplanificacion = '$idPlanificacion' ");
        $fila = $this->db->registro();        
        return $fila->contador;
    }    


}