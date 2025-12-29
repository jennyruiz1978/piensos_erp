<?php


class ModeloFacturasClientesCSV{

    private $db;

    public function __construct(){
        $this->db = new Base;
    }

    public function getInvoices(){
        $this->db->query("SELECT * FROM clientes_facturas");

        $filas = $this->db->registros();

        return $filas;
    }

    public function getFacturasConFiltros($query_search)
    {
        $this->db->query("
            SELECT 
                fac.id,
                fac.numero,
                fac.fecha,
                fac.total,
                fac.estado,
                fac.vencimiento,
                fac.estado_exportar
            FROM clientes_facturas fac
            WHERE $query_search
            ORDER BY fac.fecha DESC
        ");

        return $this->db->registros();
    }

    public function obtenerFacturaPorId($id) 
    {
        $this->db->query("SELECT * FROM clientes_facturas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->registro();
    }

    public function updateFieldTablaByStringIn($tabla, $field, $value, $string)
    {
        if(empty($string) || trim($string) == '') {
            return false;
        }
        $string = preg_replace('/[^0-9,]/', '', $string);
        
        $this->db->query("UPDATE $tabla SET $field = :value WHERE id IN ($string)");
        $this->db->bind(':value', $value);
        
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function getFacturasPorIdString($stringIds) {
        if(empty($stringIds)) return [];
        
        $stringIds = preg_replace('/[^0-9,]/', '', $stringIds);
        
        $this->db->query("
            SELECT 
                fac.*,
                conf.cifpiensos, conf.razonsocialpiensos, conf.direccionpiensos, 
                conf.codigopostalpiensos, conf.localidadpiensos, conf.provinciapiensos,
                cli.nif AS cliente_nif, cli.nombrefiscal AS cliente_nombre, 
                cli.direccion AS cliente_direccion, cli.codigopostal AS cliente_cp, 
                cli.poblacion AS cliente_poblacion, cli.provincia AS cliente_provincia,
                fp.codigob2brouter,
                cb.numerocuenta,
                fac.descuentotipo, fac.descuentoimporte,
                (SELECT prov.nif FROM proveedores prov LIMIT 1) as proveedor_nif
            FROM clientes_facturas fac
            CROSS JOIN configuracion conf
            LEFT JOIN clientes cli ON fac.idcliente = cli.id
            LEFT JOIN formas_pago fp ON fac.idformacobro = fp.id
            LEFT JOIN cuentas_bancarias cb ON fac.idcuentabancaria = cb.id
            WHERE fac.id IN ($stringIds)
        ");
        
        return $this->db->registros();
    }

    public function getLineasFactura($idFactura) 
    {
        $this->db->query("
            SELECT 
                det.*,
				det.descuentotipo * det.subtotal / 100 AS descuentolinea,
				(det.subtotal * (det.ivatipo)/100) AS ivaimporte,
				(det.subtotal * (100 + det.ivatipo)/100) - ( det.descuentotipo * det.subtotal / 100 ) AS importetotal,
                fac.fecha , fac.vencimiento
            FROM clientes_facturas_det det
            INNER JOIN clientes_facturas fac ON det.idfactura = fac.id
            WHERE det.idfactura = :idfactura
        ");
        $this->db->bind(':idfactura', $idFactura);
        return $this->db->registros();
    }



}