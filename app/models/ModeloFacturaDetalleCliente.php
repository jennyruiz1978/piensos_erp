<?php


class ModeloFacturaDetalleCliente{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function getRowsInvoice($idFactura){
        $this->db->query("SELECT * FROM clientes_facturas_det WHERE idfactura = '$idFactura' ");                

        $filas = $this->db->registros();
        return $filas;
    }


    public function getRowsInvoiceWithRowsDatesNoticesDelivery($idFactura){
        $this->db->query("SELECT fd.*, (SELECT ca.fecha FROM clientes_albaranes ca WHERE ca.id=ad.idalbaran) AS fecha
                        FROM clientes_facturas_det fd
                        LEFT JOIN clientes_albaranes_det ad ON ad.id=fd.idfilaalbaran
                        WHERE fd.idfactura = '$idFactura' ");                

        $filas = $this->db->registros();
        return $filas;
    }    

    public function getTotalsInvoice($idFactura){
        $this->db->query("SELECT 
                        /*ROUND(SUM(subtotal),2) AS suma_base_imponible,
                        ROUND(SUM(subtotal * ivatipo / 100),2) AS suma_iva,
                        ROUND(SUM(subtotal * (1+ivatipo/100)),2) AS total_final*/
                        ROUND(SUM(subtotal),2) AS suma_base_imponible,
                        ROUND(SUM(subtotal * descuentotipo / 100),2) AS suma_descuento,
                        ROUND(SUM(subtotal * (1-descuentotipo/100)),2) AS suma_base_imp_con_dscto,
                        ROUND(SUM(subtotal * (1-descuentotipo/100) * ivatipo / 100),2) AS suma_iva,
                        ROUND(SUM(subtotal * (1-descuentotipo/100)),2) + ROUND(SUM(subtotal * (1-descuentotipo/100) * ivatipo / 100),2) AS total_final
                        FROM clientes_facturas_det 
                        WHERE idfactura = '$idFactura' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    
  
    public function getTotalsInvoiceFormat($idFactura){
        $this->db->query("SELECT 
                        /*
                        FORMAT(SUM(subtotal),2,'es_ES') AS baseimponible,
                        FORMAT(SUM(subtotal * ivatipo / 100),2,'es_ES') AS ivatotal,
                        FORMAT(SUM(subtotal * (1+ivatipo/100)),2,'es_ES') AS total
                        */
                        ROUND(SUM(subtotal),2) AS baseimponible,                                                
                        ROUND(SUM(subtotal * (1-descuentotipo/100) * ivatipo / 100),2) AS ivatotal,
                        ROUND(SUM(subtotal * (1-descuentotipo/100)),2) + ROUND(SUM(subtotal * (1-descuentotipo/100) * ivatipo / 100),2) AS total

                        FROM clientes_facturas_det 
                        WHERE idfactura = '$idFactura' ");                

        $fila = $this->db->registro();
        return $fila;
    }

    public function getIdFilaAlbaran($id)
    {
        $this->db->query("SELECT idfilaalbaran FROM clientes_facturas_det WHERE id = '$id' ");
        $fila = $this->db->registro();
        $idfilaalbaran = 0;
        if(isset($fila->idfilaalbaran) && $fila->idfilaalbaran > 0){
            $idfilaalbaran = $fila->idfilaalbaran;
        }
        return $idfilaalbaran;
    }

    public function getIdFilaAlbaranByIdAlbaran($idAlbaran)
    {
        $this->db->query("SELECT idfilaalbaran FROM clientes_facturas_det WHERE idalbaran = '$idAlbaran' ");
        $filas = $this->db->registros();
        $idFilasAlbaran = [];
        if(isset($filas[0]) && $filas[0]->idfilaalbaran > 0){
            $idFilasAlbaran = $filas;
        }
        return $idFilasAlbaran;
    }

    public function getIdDeliveryInvoiceByIdRowInvoice($id)
    {
        $this->db->query("SELECT idalbaran FROM clientes_facturas_det WHERE id = '$id' ");
        $fila = $this->db->registro();
        $idAlbaran = 0;
        if(isset($fila->idalbaran) && $fila->idalbaran > 0){
            $idAlbaran = $fila->idalbaran;
        }
        return $idAlbaran;
    }

    
    public function getIdRowInvoiceByIdRowDeliveryNotice($id)
    {
        $this->db->query("SELECT id FROM clientes_facturas_det WHERE idfilaalbaran = '$id' ");
        $fila = $this->db->registro();
        $id = 0;
        if(isset($fila->id) && $fila->id > 0 && $fila->id != null){
            $id = $fila->id;
        }
        return $id;
    }

    public function getIdInvoiceByIdRowDeliveryNotice($id)
    {
        $this->db->query("SELECT idfactura FROM clientes_facturas_det WHERE idfilaalbaran = '$id' ");
        $fila = $this->db->registro();
        $idFactura = 0;
        if(isset($fila->idfactura) && $fila->idfactura > 0){
            $idFactura = $fila->idfactura;
        }
        return $idFactura;
    }

    /*
    public function removeIdFilaAlbaran($idAlbaran)
    {
        $this->db->query("UPDATE clientes_facturas_det 
                        SET idfilaalbaran = 0
                        WHERE idalbaran = '$idAlbaran' ");
        
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }    
    
    public function removeIdAlbaran($idAlbaran)
    {
        $this->db->query("UPDATE clientes_facturas_det 
                        SET idalbaran = 0
                        WHERE idalbaran = '$idAlbaran' ");
        
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }  
    */

}