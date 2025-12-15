<?php


class ModeloAlbaranDetalleProveedor{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function getRowsAlbaran($idAlbaran){
        $this->db->query("SELECT * FROM proveedores_albaranes_det WHERE idalbaran = '$idAlbaran' ");                

        $filas = $this->db->registros();
        return $filas;
    }

    public function getIdAlbaranByIdAlbaranDet($idalbarandet)
    {
        $this->db->query("SELECT idalbaran FROM proveedores_albaranes_det WHERE id = '$idalbarandet' ");                

        $fila = $this->db->registro();
        return $fila->idalbaran;
    }

    public function getPriceRowByIdAlbaranDet($idalbarandet)
    {
        $this->db->query("SELECT precio FROM proveedores_albaranes_det WHERE id = '$idalbarandet' ");                

        $fila = $this->db->registro();
        return (isset($fila->precio) && $fila->precio != '')? $fila->precio: 0;
    }

    public function getIdPlanDateByIdAlbaranDet($idalbarandet)
    {
        $this->db->query("SELECT idplanfecha FROM proveedores_albaranes_det WHERE id = '$idalbarandet' ");                

        $fila = $this->db->registro();
        return $fila->idplanfecha;
    }

    public function getIdDeliveryNoticeByIdPlanDate($idplanfecha)
    {
        $this->db->query("SELECT idalbaran FROM proveedores_albaranes_det WHERE idplanfechafab = '$idplanfecha' ");                

        $fila = $this->db->registro();
        return $fila->idalbaran;
    }        

    public function getTotalsAlbaran($idAlbaran){
        $this->db->query("SELECT 
                        ROUND(SUM(subtotal),2) AS suma_base_imponible,
                        ROUND(SUM(subtotal * ivatipo / 100),2) AS suma_iva,
                        ROUND(SUM(subtotal * (1+ivatipo/100)),2) AS total_final
                        FROM proveedores_albaranes_det 
                        WHERE idalbaran = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    
    public function getTotalsAlbaranFormat($idAlbaran){
        $this->db->query("SELECT 
                        FORMAT(SUM(subtotal),2,'es_ES') AS baseimponible,
                        FORMAT(SUM(subtotal * ivatipo / 100),2,'es_ES') AS ivatotal,
                        FORMAT(SUM(subtotal * (1+ivatipo/100)),2,'es_ES') AS total
                        FROM proveedores_albaranes_det 
                        WHERE idalbaran = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    public function searchNoticeDeliveryIdExist($idPlanDate)
    {
        $this->db->query("SELECT id, idalbaran, albaranfabrica FROM proveedores_albaranes_det alb WHERE alb.idplanfecha = '$idPlanDate' ");                

       
        $fila = $this->db->registro();        
        $r = false;
        if(isset($fila->id) && $fila->id > 0){
            $r = $fila;
        }
        return $r;
    }

    public function searchNoticeDeliveryFactorySupplierIdExist($idPlanDate)
    {
        $this->db->query("SELECT idalbaran FROM proveedores_albaranes_det alb WHERE alb.idplanfechafab = '$idPlanDate' ");                

       
        $fila = $this->db->registro();        
        $r = 0;
        if(isset($fila->idalbaran) && $fila->idalbaran > 0){
            $r = $fila->idalbaran;
        }
        return $r;
    }    

    public function searchNoticeDeliveryDataFactorySupplierIdExist($idPlanDate)
    {
        $this->db->query("SELECT id FROM proveedores_albaranes_det alb WHERE alb.idplanfechafab = '$idPlanDate' ");                

       
        $fila = $this->db->registro();        
        $r = 0;
        if(isset($fila->id) && $fila->id > 0){
            $r = $fila->id;
        }
        return $r;
    }    
    
    public function getTotalsRowNoticeDelivery($idAlbaran)
    {

        $this->db->query("SELECT                                                 
                        SUM(det.subtotal) AS base_imponible, 
                        SUM((det.subtotal * det.ivatipo/100)) AS importe_iva, 
                        SUM((det.subtotal + (det.subtotal * det.ivatipo/100))) AS suma_total        
                        FROM proveedores_albaranes_det det WHERE det.idalbaran = '$idAlbaran'
                        ");

        $fila = $this->db->registro();
        return $fila;

    }    

    public function getRowsDeliveryNoticeByIdInvoice($idFactura)
    {
        $this->db->query("SELECT idplanfecha
        FROM proveedores_albaranes_det 
        WHERE idfactura = '$idFactura' AND idplanfecha IS NOT NULL AND idplanfecha > 0 AND idplanfecha <> '' ");

        $fila = $this->db->registro();
        $idPlanFecha = 0;
        if(isset($fila->idplanfecha) && $fila->idplanfecha > 0){
            $idPlanFecha = $fila->idplanfecha;
        }
        return $idPlanFecha;
    }

    public function getIdPlanDateRowsByIdDeliveryNoticeId($idAlbaran){
        $this->db->query("SELECT idplanfecha, id FROM proveedores_albaranes_det 
                        WHERE idalbaran = '$idAlbaran' AND (idplanfecha IS NOT NULL AND idplanfecha > 0) ");

        $filas = $this->db->registros();
        $r = false;
        if(isset($filas) && count($filas) > 0 && isset($filas[0]->idplanfecha) && $filas[0]->idplanfecha != '' && $filas[0]->idplanfecha > 0){
            $r = $filas;
        }
        return $r;
    }

    public function getDataAlbaranDetailFactoryDeliveryNoticeByIdPlanDate($idplanfecha)
    {
        $this->db->query("SELECT * FROM proveedores_albaranes_det 
                        WHERE idplanfechafab = '$idplanfecha' 
                        AND (idplanfechafab IS NOT NULL AND idplanfechafab > 0) ");

        $fila = $this->db->registro();
        $r = false;
        if(isset($fila->id) && $fila->id > 0){
            $r = $fila;
        }
        return $r;
    }
    
    public function getIdPlanDateFactorySupplierOneRowByIdDeliveryNoticeId($idAlbaran){
        $this->db->query("SELECT idplanfechafab, id FROM proveedores_albaranes_det 
                        WHERE idalbaran = '$idAlbaran' AND (idplanfechafab IS NOT NULL AND idplanfechafab > 0) ");

        $filas = $this->db->registros();
        $r = false;
        if(isset($filas) && count($filas) > 0 && isset($filas[0]->idplanfechafab) && $filas[0]->idplanfechafab != '' && $filas[0]->idplanfechafab > 0){
            $r = $filas;
        }
        return $r;
    }

    
    public function getDataAlbaranDetailNoticeByIdPlanDate($idplanfecha)
    {
        $this->db->query("SELECT * FROM proveedores_albaranes_det 
                        WHERE idplanfecha = '$idplanfecha' 
                        AND (idplanfecha IS NOT NULL AND idplanfecha > 0) ");

        $fila = $this->db->registro();
        $r = false;
        if(isset($fila->id) && $fila->id > 0){
            $r = $fila;
        }
        return $r;
    }

    public function getDeliveryNotesIdsSupplierByIdPlanDates($idplanfecha)
    {
        $this->db->query("SELECT idalbaran, idfactura FROM proveedores_albaranes_det pad WHERE pad.idplanfecha='$idplanfecha' OR pad.idplanfechafab='$idplanfecha' ");
        $filas = $this->db->registros();
        return $filas;
    }     

    public function deleteDeliveryNotesLines($idAlbaran){

        $this->db->query("DELETE FROM proveedores_albaranes_det WHERE idalbaran = '$idAlbaran' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }    

}