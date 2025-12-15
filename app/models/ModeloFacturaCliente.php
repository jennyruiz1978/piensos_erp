<?php


class ModeloFacturaCliente{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function obtenerFacturasClientesTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT fac.id, fac.numero, fac.cliente, cli.nif, 
                        DATE_FORMAT(fac.fecha, '%d/%m/%Y') as fecha, fac.baseimponible,
                        IF(fac.descuentoimporte IS NOT NULL AND fac.descuentoimporte<>'',fac.descuentoimporte,0) AS descuentoimporte,
                        fac.ivatotal, fac.total, fac.estado
                        FROM clientes_facturas fac
                        LEFT JOIN clientes cli ON fac.idcliente=cli.id                       
                        $where $order LIMIT $pagina , $limit ");

      
        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalFacturasCliente($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM clientes_facturas fac   
                        LEFT JOIN clientes cli ON fac.idcliente=cli.id                
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }
    
    public function getInvoiceData($idFactura){
        $this->db->query("SELECT fac.*, (SELECT cli.nif FROM clientes cli WHERE fac.idcliente = cli.id ) as nif FROM clientes_facturas fac WHERE fac.id = '$idFactura' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    public function getInvoiceDataDocument($idFactura){
        $this->db->query("SELECT fac.*, 
                        cli.nombrefiscal, cli.nif, cli.direccion, cli.poblacion, cli.codigopostal, cli.provincia,
                        IF(fac.idformacobro > 0,(SELECT fp.formadepago FROM formas_pago fp WHERE fp.id =fac.idformacobro),'') as formapago,
                        IF(fac.idcuentabancaria IS NOT NULL AND fac.idcuentabancaria > 0,(SELECT c.numerocuenta FROM cuentas_bancarias c WHERE c.id=fac.idcuentabancaria),'') AS ctabancaria
                        FROM clientes_facturas fac 
                        LEFT JOIN clientes cli ON fac.idcliente = cli.id  
                        WHERE fac.id = '$idFactura' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    public function getTotalAmountInvoice($idFactura)
    {
        $this->db->query("SELECT total FROM clientes_facturas WHERE id = '$idFactura' ");                

        $fila = $this->db->registro();
        $total = 0;
        if(isset($fila->total) && $fila->total > 0){
            $total = $fila->total;
        }
        return $total;
    }

    public function getIdInvoiceOriginToNegative($idFactura)
    {
        $this->db->query("SELECT idfacturaorigen FROM clientes_facturas WHERE id = '$idFactura' ");                

        $fila = $this->db->registro();
        return $fila->idfacturaorigen;
    }

    public function getStatusInvoice($idFactura)
    {
        $this->db->query("SELECT estado FROM clientes_facturas WHERE id = '$idFactura' ");                

        $fila = $this->db->registro();
        return $fila->estado;
    }

    public function getInvoiceNumberByIdFactura($idFactura)
    {
        $this->db->query("SELECT numero FROM clientes_facturas WHERE id = '$idFactura' ");                

        $fila = $this->db->registro();
        return $fila->numero;
    }

    public function obtenerFacturasClientesExportar($order,$where){       
                
        $this->db->query("SELECT fac.id, fac.numero, fac.cliente, cli.nif, 
                        DATE_FORMAT(fac.fecha, '%d/%m/%Y') as fecha, fac.baseimponible,
                        IF(fac.descuentoimporte IS NOT NULL AND fac.descuentoimporte<>'',fac.descuentoimporte,0) AS descuentoimporte,
                        fac.ivatotal, fac.total, fac.estado

                        FROM clientes_facturas fac
                        LEFT JOIN clientes cli ON fac.idcliente = cli.id  

                        $where $order ");
        $filas = $this->db->registros();

        return $filas;
    }

        
    public function getTotalsDeliveryNotesAndInvoiceByIdFactura($idFactura)
    {

        $this->db->query("SELECT 	                                               
                        det.descripcion, det.unidad, det.ivatipo,
                        SUM(det.cantidad) AS suma_cantidad, 
                        SUM((det.subtotal + (det.subtotal * det.ivatipo/100))) AS suma_total ,                                
                        f.idfactura,
                        f.suma_cantidad_factura, 
                        f.suma_total_factura                                                   
                        FROM clientes_albaranes_det det

                        LEFT JOIN (
                                SELECT fdet.idfactura, fdet.descripcion AS descripcionfact,
                                SUM(fdet.cantidad) AS suma_cantidad_factura, 
                                SUM(fdet.subtotal + (fdet.subtotal * fdet.ivatipo/100)) AS suma_total_factura
                                FROM clientes_facturas_det fdet WHERE fdet.idfactura='$idFactura'
                                GROUP BY fdet.descripcion
                        ) AS f ON det.idfactura=f.idfactura AND f.descripcionfact = det.descripcion
                                
                        WHERE det.idfactura='$idFactura'
                        GROUP BY det.descripcion
                    ");
       
        $filas = $this->db->registros();
        return $filas;

    }


    public function updateInvoiceHead($datos){
        
        $baseimponible = $datos['baseimponible'];
        $ivatotal = $datos['ivatotal'];
        $total = $datos['total'];
        $id = $datos['id'];

        $this->db->query("UPDATE clientes_facturas 
                        SET baseimponible = '$baseimponible', ivatotal= $ivatotal, total = '$total'
                        WHERE id = '$id' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }        

    public function getDiscountTypetInvoice($idFactura)
    {
        $this->db->query("SELECT descuentotipo
                        FROM clientes_facturas              
                        WHERE id = '$idFactura' ");

        $fila = $this->db->registro();
        $descuentotipo = 0;
        if(isset($fila->descuentotipo) && $fila->descuentotipo > 0){
            $descuentotipo = $fila->descuentotipo;
        }
        return $descuentotipo;
    }
    
 

}