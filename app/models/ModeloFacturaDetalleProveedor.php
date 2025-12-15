<?php


class ModeloFacturaDetalleProveedor{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function getRowsInvoice($idFactura){
        $this->db->query("SELECT * FROM proveedores_facturas_det WHERE idfactura = '$idFactura' ");                

        $filas = $this->db->registros();
        return $filas;
    }

}