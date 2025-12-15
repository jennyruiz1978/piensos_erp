<?php


class ModeloReciboProveedor{

    private $db;

    public function __construct(){
        $this->db = new Base;
    }

    public function obtenerRecibosProveedoresTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }           
        $this->db->query("SELECT rec.id, rec.numero, DATE_FORMAT(rec.fecha, '%d/%m/%Y') as fecha, rec.importe, 
                        fac.numero as numerofactura, rec.concepto,
                        (IF(rec.estado='pagado',rec.estado,IF(rec.vencimiento < CURDATE(),'impagado','pendiente'))) as estadoactual 
                        FROM proveedores_recibos rec
                        LEFT JOIN proveedores_facturas fac ON rec.idfactura=fac.id                        
                        $where $order LIMIT $pagina , $limit ");
        
        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalRecibosProveedor($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM proveedores_recibos rec
                        LEFT JOIN proveedores_facturas fac ON rec.idfactura=fac.id                      
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }
 

    public function getReceiptsByIdInvoice($idFactura)
    {
        $this->db->query("SELECT *,
                        (IF(estado='pagado',estado,IF(vencimiento < CURDATE(),'impagado','pendiente'))) as estadoactual
                        FROM proveedores_recibos WHERE idfactura = '$idFactura' ");                

        $filas = $this->db->registros();
        return $filas;
    }

    public function getTotalAmountReceiptsByInvoice($idFactura)
    {
        $this->db->query("SELECT SUM(importe) as total_recibos FROM proveedores_recibos WHERE idfactura = '$idFactura' ");

        $fila = $this->db->registro();
        return (isset($fila->total_recibos) && $fila->total_recibos > 0 )? $fila->total_recibos: 0;
    }

    public function getTotalAmountPaidReceiptsByInvoice($idFactura)
    {
        $this->db->query("SELECT SUM(importe) as total_recibos 
                        FROM proveedores_recibos WHERE idfactura = '$idFactura' 
                        AND estado = 'pagado' ");

        $fila = $this->db->registro();
        return (isset($fila->total_recibos) && $fila->total_recibos > 0 )? $fila->total_recibos: 0;
    }

    public function getIdInvoiceByIdRecepit($idRecibo){

        $this->db->query("SELECT idfactura FROM proveedores_recibos WHERE id = '$idRecibo' ");

        $fila = $this->db->registro();
        return $fila->idfactura;
    }

    public function getRecepitById($idRecibo)
    {
        $this->db->query("SELECT *,
                        (IF(estado='pagado',estado,IF(vencimiento < CURDATE(),'impagado','pendiente'))) as estadoactual
                        FROM proveedores_recibos WHERE id = '$idRecibo' ");

        $fila = $this->db->registro();
        return $fila;
    }

    public function obtenerRecibosProveedoresExportar($order,$where)
    {
        $this->db->query("SELECT rec.id, rec.numero, DATE_FORMAT(rec.fecha, '%d/%m/%Y') as fecha, rec.importe, 
                        fac.numero as numerofactura, rec.concepto ,
                        (IF(rec.estado='pagado',rec.estado,IF(rec.vencimiento < CURDATE(),'impagado','pendiente'))) as estadoactual 
                        FROM proveedores_recibos rec
                        LEFT JOIN proveedores_facturas fac ON rec.idfactura=fac.id 
                        $where $order ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function changeStatusReceipt($idRecibo,$estado)
    {
        $this->db->query("UPDATE proveedores_recibos SET estado = '$estado' WHERE id= '$idRecibo' ");

        
        if($this->db->execute()){
            return 1;
        } else {
            return 0;
        }
    }

    public function createReceiptFromInvoice($strValues,$fields)
    {           

        $this->db->query("INSERT INTO proveedores_recibos  $fields VALUES $strValues ");

        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
}