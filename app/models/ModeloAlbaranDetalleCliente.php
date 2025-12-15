<?php


class ModeloAlbaranDetalleCliente{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function getRowsAlbaran($idAlbaran){
        $this->db->query("SELECT * FROM clientes_albaranes_det WHERE idalbaran = '$idAlbaran' ");

        $filas = $this->db->registros();
        return $filas;
    }

    public function getTotalsAlbaran($idAlbaran){
        $this->db->query("SELECT 
                        ROUND(SUM(subtotal),2) AS suma_base_imponible,
                        ROUND(SUM(subtotal * ivatipo / 100),2) AS suma_iva,
                        ROUND(SUM(subtotal * (1+ivatipo/100)),2) AS total_final
                        FROM clientes_albaranes_det 
                        WHERE idalbaran = '$idAlbaran' ");       

        $filas = $this->db->registro();
        return $filas;
    }

    public function getTotalsAlbaranFormat($idAlbaran){
        $this->db->query("SELECT 
                        FORMAT(SUM(subtotal),2,'es_ES') AS baseimponible,
                        FORMAT(SUM(subtotal * ivatipo / 100),2,'es_ES') AS ivatotal,
                        FORMAT(SUM(subtotal * (1+ivatipo/100)),2,'es_ES') AS total
                        FROM clientes_albaranes_det 
                        WHERE idalbaran = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    public function searchNoticeDeliveryClientIdExist($idPlanDate)
    {
        $this->db->query("SELECT id, idalbaran FROM clientes_albaranes_det alb WHERE alb.idplanfecha = '$idPlanDate' ");
       
        $fila = $this->db->registro();        
        $r = false;
        if(isset($fila->id) && $fila->id > 0){
            $r = $fila;
        }
        return $r;
    }

    
    public function getDeliveryNotesIdsCliByIdPlanDates($idplanfecha)
    {
        $this->db->query("SELECT idalbaran, idfactura FROM clientes_albaranes_det pad WHERE pad.idplanfecha='$idplanfecha' ");
        $filas = $this->db->registros();
        return $filas;
    }   

    public function deleteDeliveryNotesLines($idAlbaran){

        $this->db->query("DELETE FROM clientes_albaranes_det WHERE idalbaran = '$idAlbaran' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }    

    
    

}