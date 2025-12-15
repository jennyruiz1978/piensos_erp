<?php


class ModeloFacturaProveedor{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function obtenerFacturasProveedoresTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT fac.id, fac.numero, fac.proveedor, prov.nif,
                        DATE_FORMAT(fac.fecha, '%d/%m/%Y') as fecha,
                        fac.baseimponible,
                        IF(fac.retencionimporte IS NOT NULL AND fac.retencionimporte <>'',fac.retencionimporte,0) as retencionimporte,  
                        fac.ivatotal, fac.total, fac.estado
                        
                        FROM proveedores_facturas fac   
                        LEFT JOIN proveedores prov ON fac.idproveedor=prov.id                   

                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalFacturasProveedor($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM proveedores_facturas fac        
                        LEFT JOIN proveedores prov ON fac.idproveedor=prov.id  
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }
 
    public function getInvoiceData($idFactura){
        $this->db->query("SELECT fac.*, (SELECT pro.nif FROM proveedores pro WHERE fac.idproveedor = pro.id ) as nif FROM proveedores_facturas fac WHERE fac.id = '$idFactura' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    public function getInvoiceNumber($idFactura){
        $this->db->query("SELECT numero FROM proveedores_facturas WHERE id = '$idFactura' ");                

        $fila = $this->db->registro();
        return $fila->numero;
    }

    public function getTotalAmountInvoice($idFactura)
    {
        $this->db->query("SELECT total FROM proveedores_facturas WHERE id = '$idFactura' ");                

        $fila = $this->db->registro();
        return $fila->total;
    }

    
    public function getInvoiceNumberByIdFactura($idFactura)
    {
        $this->db->query("SELECT numero FROM proveedores_facturas WHERE id = '$idFactura' ");                

        $fila = $this->db->registro();
        return $fila->numero;
    }

    
    public function obtenerFacturasProveedoresExportar($order,$where){       
                
        $this->db->query("SELECT fac.id, fac.numero, fac.proveedor, prov.nif,
                        DATE_FORMAT(fac.fecha, '%d/%m/%Y') as fecha,
                        fac.baseimponible,
                        IF(fac.retencionimporte IS NOT NULL AND fac.retencionimporte <>'',fac.retencionimporte,0) as retencionimporte,  
                        fac.ivatotal, fac.total, fac.estado

                        FROM proveedores_facturas fac

                        LEFT JOIN proveedores prov ON fac.idproveedor=prov.id                     

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
                        FROM proveedores_albaranes_det det
        
                        LEFT JOIN (
                                SELECT fdet.idfactura, fdet.descripcion AS descripcionfact,
                                SUM(fdet.cantidad) AS suma_cantidad_factura, 
                                SUM(fdet.subtotal + (fdet.subtotal * fdet.ivatipo/100)) AS suma_total_factura
                                FROM proveedores_facturas_det fdet WHERE fdet.idfactura='$idFactura'
                                GROUP BY fdet.descripcion
                        ) AS f ON det.idfactura=f.idfactura AND f.descripcionfact = det.descripcion
                                
                        WHERE det.idfactura='$idFactura'
                        GROUP BY det.descripcion
                    ");
       
        $filas = $this->db->registros();
        return $filas;

    }



}