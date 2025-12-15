<?php


class ModeloReportes{

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
      
        $this->db->query("SELECT 
                        fac.id, fac.numero, fac.cliente, cli.nif,
                        DATE_FORMAT(fac.fecha, '%d/%m/%Y') as fecha, 
                        DATE_FORMAT(fac.vencimiento, '%d/%m/%Y') as vencimiento, fac.baseimponible,
                        IF(fac.descuentoimporte IS NOT NULL AND fac.descuentoimporte<>'',fac.descuentoimporte,0) AS descuentoimporte, fac.ivatotal,  fac.total, fac.estado,
                        b.suma_cobrada,
                        IF(b.suma_cobrada IS NOT NULL AND b.suma_cobrada > 0 ,fac.total-b.suma_cobrada,fac.total) AS por_cobrar
                        
                        FROM clientes_facturas fac
                        
                        LEFT JOIN (SELECT re.idfactura AS idfacrecibo, SUM(re.importe) AS suma_cobrada FROM clientes_recibos re
                        WHERE re.estado = 'pagado'
                        GROUP BY re.idfactura) AS b ON fac.id = b.idfacrecibo

                        LEFT JOIN clientes cli ON fac.idcliente=cli.id
                        
                        $where $order LIMIT $pagina , $limit ");

                   

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalFacturasCliente($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM clientes_facturas fac
                        
                        LEFT JOIN (SELECT re.idfactura AS idfacrecibo, SUM(re.importe) AS suma_cobrada FROM clientes_recibos re
                        WHERE re.estado = 'pagado'
                        GROUP BY re.idfactura) AS b ON fac.id = b.idfacrecibo    

                        LEFT JOIN clientes cli ON fac.idcliente=cli.id

                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }

    public function obtenerFacturasProveedoresTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   
      
        $this->db->query("SELECT 
                        fac.id, fac.numero, fac.proveedor, prov.nif ,
                        DATE_FORMAT(fac.fecha, '%d/%m/%Y') as fecha, 
                        DATE_FORMAT(fac.vencimiento, '%d/%m/%Y') as vencimiento, fac.baseimponible,
                        IF(fac.retencionimporte IS NOT NULL AND fac.retencionimporte <> '',fac.retencionimporte,0) AS retencionimporte,
                        fac.ivatotal, fac.total, fac.estado,
                        b.suma_cobrada,
                        IF(b.suma_cobrada IS NOT NULL AND b.suma_cobrada > 0 ,fac.total-b.suma_cobrada,fac.total) AS por_cobrar                
                        
                        FROM proveedores_facturas fac
                        
                        LEFT JOIN (SELECT re.idfactura AS idfacrecibo, SUM(re.importe) AS suma_cobrada FROM proveedores_recibos re
                        WHERE re.estado = 'pagado'
                        GROUP BY re.idfactura) AS b ON fac.id = b.idfacrecibo

                        LEFT JOIN proveedores prov ON fac.idproveedor=prov.id
                        
                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalFacturasProveedor($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM proveedores_facturas fac
                        
                        LEFT JOIN (SELECT re.idfactura AS idfacrecibo, SUM(re.importe) AS suma_cobrada FROM proveedores_recibos re
                        WHERE re.estado = 'pagado'
                        GROUP BY re.idfactura) AS b ON fac.id = b.idfacrecibo         

                        LEFT JOIN proveedores prov ON fac.idproveedor=prov.id          
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }
    
    public function obtenerFacturasClientesTest($order,$where){                

        $this->db->query("SELECT id, numero, cliente, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, total, estado
                        FROM clientes_facturas                        
                        $where $order ");

        $filas = $this->db->registros();

        return $filas;
    }

    //////nuevo
    public function obtenerFacturasClientes($order,$where){
               
        $this->db->query("SELECT 
                        fac.id, fac.numero, fac.cliente, cli.nif, cli.codigopostal,
                        DATE_FORMAT(fac.fecha, '%d/%m/%Y') as fecha, 
                        DATE_FORMAT(fac.vencimiento, '%d/%m/%Y') as vencimiento,  fac.baseimponible,
                        IF(fac.descuentoimporte IS NOT NULL AND fac.descuentoimporte<>'',fac.descuentoimporte,0) AS descuentoimporte,
                        fac.ivatotal,  fac.total, fac.estado,
                        b.suma_cobrada,
                        IF(b.suma_cobrada IS NOT NULL AND b.suma_cobrada > 0 ,fac.total-b.suma_cobrada,fac.total) AS por_cobrar
                        
                        FROM clientes_facturas fac
                        
                        LEFT JOIN (
                            SELECT re.idfactura AS idfacrecibo, SUM(re.importe) AS suma_cobrada 
                            FROM clientes_recibos re
                            WHERE re.estado = 'pagado'
                            GROUP BY re.idfactura) AS b ON fac.id = b.idfacrecibo

                        LEFT JOIN clientes cli ON fac.idcliente=cli.id
                        
                        $where $order ");

      

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerFacturasProveedores($order,$where){
               
     
        $this->db->query("SELECT 
                        fac.id, fac.numero, fac.proveedor, prov.nif , prov.codigopostal,
                        DATE_FORMAT(fac.fecha, '%d/%m/%Y') as fecha, 
                        DATE_FORMAT(fac.vencimiento, '%d/%m/%Y') as vencimiento,  fac.baseimponible,fac.ivatotal, fac.total, fac.estado,
                        b.suma_cobrada,
                        IF(b.suma_cobrada IS NOT NULL AND b.suma_cobrada > 0 ,fac.total-b.suma_cobrada,fac.total) AS por_cobrar
                        
                        FROM proveedores_facturas fac
                        
                        LEFT JOIN (SELECT re.idfactura AS idfacrecibo, SUM(re.importe) AS suma_cobrada FROM proveedores_recibos re
                        WHERE re.estado = 'pagado'
                        GROUP BY re.idfactura) AS b ON fac.id = b.idfacrecibo

                        LEFT JOIN proveedores prov ON fac.idproveedor=prov.id
                        
                        $where $order ");

      

        $filas = $this->db->registros();

        return $filas;
    }

    //////

    //proveedores
    public function getDeliveryNoticesKilos($ini,$fin){
        
        $this->db->query("SELECT 
                        ROUND(SUM(IF(alb.estado='pendiente',det.cantidad,0)),0) AS kilos_sin_facturar, 
                        ROUND(SUM(det.cantidad),0) AS total_kilos_albaranados, det.unidad
                        FROM proveedores_albaranes alb
                        LEFT JOIN proveedores_albaranes_det det ON det.idalbaran=alb.id
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' ");
       
       
        $fila = $this->db->registro();
        return $fila;
    }

    public function getDeliveryNoticesEuros($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(IF(alb.estado='pendiente',alb.total,0)),0,'es_ES') AS euros_sin_facturar, 
                        FORMAT(SUM(alb.total),0,'es_ES') AS total_euros_albaranados
                        FROM proveedores_albaranes alb
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' ");
       
        $fila = $this->db->registro();
        return $fila;
    }

    public function getInvoicesData($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(det.cantidad),0,'es_ES') AS kilos_facturados, det.unidad
                        FROM proveedores_facturas alb
                        LEFT JOIN proveedores_facturas_det det ON det.idfactura=alb.id
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' ");

                    
        $fila = $this->db->registro();
        return $fila;
    }

    public function getInvoicesAmount($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(alb.total),0,'es_ES') AS euros_facturados, FORMAT(SUM(alb.ivatotal),0,'es_ES') AS euros_ivafacturado
                        FROM proveedores_facturas alb        
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' ");                

        $fila = $this->db->registro();
        return $fila;
    }
    
    public function getInvoicesWithPendingPayment($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(alb.total),0,'es_ES') AS euros_pago_pendiente, COUNT(*) AS num_facturas_pago_pendiente
                        FROM proveedores_facturas alb
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' AND alb.estado='pendiente' ");

        $fila = $this->db->registro();
        return $fila;
    }

    public function getInvoicesWithRecordedPayment($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(IF(fac.estado='pagada',re.importe,0)),0,'es_ES') AS euros_pagados,
                        FORMAT(SUM(IF(fac.estado='pagada parcial',re.importe,0)),0,'es_ES') AS euros_pago_parcial
                        FROM proveedores_recibos re
                        LEFT JOIN proveedores_facturas fac ON re.idfactura=fac.id
                        WHERE fac.fecha BETWEEN '$ini' AND '$fin' ");

        $fila = $this->db->registro();
        return $fila;
    }

    public function getOverdueInvoices($ini,$fin){
        $this->db->query("SELECT re.idfactura, SUM(re.importe) AS tot_recibos_factura, COUNT(*) AS num_facturas_vencidas,
                        (SELECT fac.vencimiento FROM proveedores_facturas fac WHERE fac.id=re.idfactura) AS fechavencimiento,
                        (SELECT fac.estado FROM proveedores_facturas fac WHERE fac.id=re.idfactura) AS estado,
                        (SELECT fac.total FROM proveedores_facturas fac WHERE fac.id=re.idfactura) AS importefactura,
                        (SELECT fac.fecha FROM proveedores_facturas fac WHERE fac.id=re.idfactura) AS fechafactura                        
                        FROM proveedores_recibos re
                        GROUP BY re.idfactura
                        HAVING estado <> 'pagada' AND fechavencimiento < CURDATE() AND fechafactura BETWEEN '$ini' AND '$fin' ");

        $fila = $this->db->registro();
        return $fila;
    }

    public function getOverdueReceipts($ini,$fin){
        $this->db->query("SELECT SUM(re.importe) AS importe_recibos 
                        FROM proveedores_recibos re
                        WHERE re.vencimiento < CURDATE()
                        AND re.fecha BETWEEN '$ini' AND '$fin' 
                        AND  re.estado <> 'pagado' ");

        $fila = $this->db->registro();
        return $fila;
    }

    ///clientes
    public function getDeliveryNoticesKilosCli($ini,$fin){
        
        $this->db->query("SELECT 
                        ROUND(SUM(IF(alb.estado='pendiente',det.cantidad,0)),0) AS kilos_sin_facturar, 
                        ROUND(SUM(det.cantidad),0) AS total_kilos_albaranados, det.unidad
                        FROM clientes_albaranes alb
                        LEFT JOIN clientes_albaranes_det det ON det.idalbaran=alb.id
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' ");
       
        $fila = $this->db->registro();
        return $fila;
    }

    public function getDeliveryNoticesEurosCli($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(IF(alb.estado='pendiente',alb.total,0)),0,'es_ES') AS euros_sin_facturar, 
                        FORMAT(SUM(alb.total),0,'es_ES') AS total_euros_albaranados
                        FROM clientes_albaranes alb
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' ");
       
        $fila = $this->db->registro();
        return $fila;
    }

    public function getInvoicesDataCli($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(det.cantidad),0,'es_ES') AS kilos_facturados, det.unidad
                        FROM clientes_facturas alb
                        LEFT JOIN clientes_facturas_det det ON det.idfactura=alb.id
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' ");

        $fila = $this->db->registro();
        return $fila;
    }

    public function getInvoicesDataAmountCli($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(alb.total),0,'es_ES') AS euros_facturados, FORMAT(SUM(alb.ivatotal),0,'es_ES') AS euros_ivafacturado
                        FROM clientes_facturas alb                        
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' ");

        $fila = $this->db->registro();
        return $fila;
    }
    
    public function getInvoicesWithPendingPaymentCli($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(alb.total),0,'es_ES') AS euros_pago_pendiente, COUNT(*) AS num_facturas_pago_pendiente
                        FROM clientes_facturas alb
                        WHERE alb.fecha BETWEEN '$ini' AND '$fin' AND alb.estado='pendiente' ");

        $fila = $this->db->registro();
        return $fila;
    }

    public function getInvoicesWithRecordedPaymentCli($ini,$fin){
        $this->db->query("SELECT 
                        FORMAT(SUM(IF(fac.estado='cobrada',re.importe,0)),0,'es_ES') AS euros_pagados,
                        FORMAT(SUM(IF(fac.estado='cobrada parcial',re.importe,0)),0,'es_ES') AS euros_pago_parcial
                        FROM clientes_recibos re
                        LEFT JOIN clientes_facturas fac ON re.idfactura=fac.id
                        WHERE fac.fecha BETWEEN '$ini' AND '$fin' ");

        $fila = $this->db->registro();
        return $fila;
    }

    public function getOverdueInvoicesCli($ini,$fin){
        $this->db->query("SELECT re.idfactura, SUM(re.importe) AS tot_recibos_factura, COUNT(*) AS num_facturas_vencidas,
                        (SELECT fac.vencimiento FROM clientes_facturas fac WHERE fac.id=re.idfactura) AS fechavencimiento,
                        (SELECT fac.estado FROM clientes_facturas fac WHERE fac.id=re.idfactura) AS estado,
                        (SELECT fac.total FROM clientes_facturas fac WHERE fac.id=re.idfactura) AS importefactura,
                        (SELECT fac.fecha FROM clientes_facturas fac WHERE fac.id=re.idfactura) AS fechafactura                        
                        FROM clientes_recibos re
                        GROUP BY re.idfactura
                        HAVING estado <> 'cobrada' AND fechavencimiento < CURDATE() AND fechafactura BETWEEN '$ini' AND '$fin' ");

        $fila = $this->db->registro();
        return $fila;
    }    

    public function getOverdueReceiptsCli($ini,$fin){
        $this->db->query("SELECT SUM(re.importe) AS importe_recibos 
                        FROM clientes_recibos re
                        WHERE re.vencimiento < CURDATE()
                        AND re.fecha BETWEEN '$ini' AND '$fin' 
                        AND  re.estado <> 'pagado' ");

        $fila = $this->db->registro();
        return $fila;
    }
}