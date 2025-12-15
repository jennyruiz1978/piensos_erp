<?php


class ModeloReciboCliente{

    private $db;

    public function __construct(){
        $this->db = new Base;
    }

    public function obtenerRecibosClientesTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }           
        $this->db->query("SELECT rec.id, rec.numero, DATE_FORMAT(rec.vencimiento, '%d/%m/%Y') as vencimiento, rec.importe, 
         CONCAT(FORMAT(SUM(det.cantidad),2,'es_ES'),' Tn') AS sumacantidad,
                        fac.numero as numerofactura, /* rec.librado */ fac.cliente AS librado, 
                        (IF(rec.estado='pagado',rec.estado,IF(rec.vencimiento < CURDATE(),'impagado','pendiente'))) as estadoactual
                        FROM clientes_recibos rec
                        LEFT JOIN clientes_facturas fac ON rec.idfactura=fac.id 
                        LEFT JOIN clientes_facturas_det det ON fac.id=det.idfactura                       
                        $where GROUP BY rec.id $order LIMIT $pagina , $limit ");
        
        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalRecibosCliente($where)
    {
        $this->db->query("SELECT rec.*
                        FROM clientes_recibos rec
                        LEFT JOIN clientes_facturas fac ON rec.idfactura=fac.id  
                        LEFT JOIN clientes_facturas_det det ON fac.id=det.idfactura                    
                        $where GROUP BY rec.id
                        ");

        
        $filas = $this->db->registros();
        return $filas;
    }
 
   

    public function getReceiptsByIdInvoice($idFactura)
    {
        $this->db->query("SELECT *,
                        (IF(estado='pagado',estado,IF(vencimiento < CURDATE(),'impagado','pendiente'))) as estadoactual
                        FROM clientes_recibos WHERE idfactura = '$idFactura' ");                

        $filas = $this->db->registros();
        return $filas;
    }
     

    public function getTotalAmountReceiptsByInvoice($idFactura)
    {
        $this->db->query("SELECT SUM(importe) as total_recibos FROM clientes_recibos WHERE idfactura = '$idFactura' ");
        $fila = $this->db->registro();
        return (isset($fila->total_recibos) )? $fila->total_recibos: 0;
    }

    public function getTotalAmountPaidReceiptsByInvoice($idFactura)
    {
        $this->db->query("SELECT SUM(importe) as total_recibos 
                        FROM clientes_recibos WHERE idfactura = '$idFactura'
                        AND estado = 'pagado' 
                         ");
        $fila = $this->db->registro();
        return (isset($fila->total_recibos) && $fila->total_recibos > 0 )? $fila->total_recibos: 0;
    }

    public function getIdInvoiceByIdRecepit($idRecibo){

        $this->db->query("SELECT idfactura FROM clientes_recibos WHERE id = '$idRecibo' ");

        $fila = $this->db->registro();
        return $fila->idfactura;
    }

    public function getRecepitById($idRecibo)
    {
        

        $this->db->query("SELECT cr.*,
                        (IF(cr.estado='pagado',cr.estado,IF(cr.vencimiento < CURDATE(),'impagado','pendiente'))) as estadoactual, f.cliente AS librado
                        FROM clientes_recibos cr LEFT JOIN clientes_facturas f ON cr.idfactura=f.id WHERE cr.id = '$idRecibo' ");

        $fila = $this->db->registro();
        return $fila;
    }

    public function getReceiptNumberByIdReceipt($idRecibo)
    {
        $this->db->query("SELECT numero FROM clientes_recibos WHERE id = '$idRecibo' ");                

        $fila = $this->db->registro();
        return $fila->numero;
    }

    public function getRecepitDataDocument($idRecibo)
    {
        $this->db->query("SELECT re.*, cli.direccion,cli.poblacion, cli.codigopostal, cli.provincia, cli.nif
        FROM clientes_recibos re 
        LEFT JOIN clientes cli ON cli.id = (SELECT fac.idcliente FROM clientes_facturas fac WHERE fac.id=re.idfactura)
        WHERE re.id = '$idRecibo' ");

        $fila = $this->db->registro();
        return $fila;
    }
 
    public function obtenerRecibosClientesExportar($order,$where){       
                
        $this->db->query("SELECT rec.id, rec.numero, DATE_FORMAT(rec.vencimiento, '%d/%m/%Y') as vencimiento, rec.importe, 
                        fac.numero as numerofactura, rec.concepto,
                        (IF(rec.estado='pagado',rec.estado,IF(rec.vencimiento < CURDATE(),'impagado','pendiente'))) as estadoactual 
                        FROM clientes_recibos rec
                        LEFT JOIN clientes_facturas fac ON rec.idfactura=fac.id     
                        $where $order ");

        $filas = $this->db->registros();

        return $filas;
    }
    

    public function createReceiptFromInvoice($strValues,$fields)
    {           

        $this->db->query("INSERT INTO clientes_recibos  $fields VALUES $strValues ");

        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function changeStatusReceipt($idRecibo,$estado)
    {
        $this->db->query("UPDATE clientes_recibos SET estado = '$estado' WHERE id= '$idRecibo' ");

        
        if($this->db->execute()){
            return 1;
        } else {
            return 0;
        }
    }

   /*  public function obtenerTodosLosAlbaranesPerAnio()
    {
        $this->db->query("SELECT * FROM clientes_recibos WHERE id BETWEEN 294 AND 400 AND YEAR(fecha) = '2025' order by id DESC");                   
        $filas = $this->db->registros();
        return $filas;
    } */



}