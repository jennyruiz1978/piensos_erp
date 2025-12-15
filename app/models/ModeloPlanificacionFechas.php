<?php


class ModeloPlanificacionFechas{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }


    public function getDatesPlanningById($id){
        $this->db->query("SELECT * FROM planificaciones_fechas WHERE id = '$id' ");        
        $filas = $this->db->registro();
        return $filas;
    }

    public function createDatesPlanningById($idPlanning, $fecha, $dia, $recojo){

        $this->db->query("INSERT INTO planificaciones_fechas (idplanificacion, fecha, dia, recojo) 
                        VALUES ('$idPlanning', '$fecha', '$dia', '$recojo')");
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return 0;
        }
    }

    public function getDateByIdPlanningAndIdRecojo($idPlanning, $idRecojo){
        $this->db->query("SELECT * FROM planificaciones_fechas WHERE idplanificacion = '$idPlanning' AND recojo = '$idRecojo' ORDEr BY id ASC ");
        $filas = $this->db->registros();
        return $filas;
    }        
    
    public function updateCellPlanning($datos){

        $id = $datos['id'];
        $carga = $datos['carga'];
        
        $idcliente = $datos['idcliente'];
        $idzona = $datos['idzona'];
        $idtransportista = $datos['idtransportista'];
        $preciocliente = $datos['preciocliente'];
        $preciozona = $datos['preciozona'];

        $this->db->query("UPDATE planificaciones_fechas 
                        SET carga='$carga', idcliente='$idcliente', idzona='$idzona', idtransportista='$idtransportista',
                        preciocliente='$preciocliente', preciozona='$preciozona'
                        WHERE id = $id ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function countPlanningDates($idPlanificacion){
        $this->db->query("SELECT COUNT(*) as contador FROM planificaciones_fechas WHERE idplanificacion = '$idPlanificacion' ");
        $fila = $this->db->registro();        
        return $fila->contador;
    }

    public function sumTotalPlanning($idPlanificacion){
        $this->db->query("SELECT SUM(fe.carga) AS total FROM planificaciones_fechas fe WHERE fe.idplanificacion='$idPlanificacion'");
        $fila = $this->db->registro();   
        
        $total = (isset($fila->total))? $fila->total: 0;             
        return $total;
    }

    public function verifyPlanningDataExist($fecha){
        $this->db->query("SELECT count(*) as contador FROM planificaciones_fechas WHERE fecha = '$fecha' ");
        $fila = $this->db->registro();                
        return $fila->contador;
    }

    public function deletePlanningDatesByIdPlanning($idPlanning){
        
        $this->db->query("DELETE FROM planificaciones_fechas WHERE idplanificacion = '$idPlanning' ");
        
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function deletePlanningLoadByIdPlanningDate($idRecojo)
    {
        $this->db->query("DELETE FROM planificaciones_fechas WHERE recojo = '$idRecojo' ");
        
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    


}