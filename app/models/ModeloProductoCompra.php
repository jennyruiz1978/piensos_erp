<?php


class ModeloProductoCompra{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    
    public function getPurchaseProduct($id){
        $this->db->query("SELECT pc.*, uni.unidad as abrev_unidad 
                        FROM productos_compras pc
                        LEFT JOIN unidades uni ON pc.idunidad = uni.id
                        WHERE pc.id = $id ");        
        $fila = $this->db->registro();
        return $fila;
    }

    public function getAllPurchaseProducts(){
        $this->db->query("SELECT pc.*, uni.unidad as abrev_unidad 
                        FROM productos_compras pc
                        LEFT JOIN unidades uni ON pc.idunidad = uni.id ");        
        $filas = $this->db->registros();
        return $filas;
    }

    function updatePurchaseProduct($descripcion, $iva, $id){
        $this->db->query("UPDATE productos_compras
                        SET descripcion = '$descripcion' , iva = '$iva'
                        WHERE id = $id ");
        
        return $this->db->execute();
    }

    public function getNameProduct($id){
        $this->db->query("SELECT descripcion FROM productos_compras WHERE id = $id ");        
        $fila = $this->db->registro();
        return (isset($fila->descripcion))? $fila->descripcion: 0;
    }

    public function obtenerProductosCompraTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT  pc.id, pc.descripcion, pc.iva, 
                        /*(SELECT pro.nombrefiscal FROM proveedores pro WHERE pro.id=pc.idproveedor) AS nombre_proveedor,*/
                        (SELECT uni.descripcion FROM unidades uni WHERE uni.id = pc.idunidad) AS nombre_unidad
                        FROM productos_compras pc                       
                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalProductosCompra($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM productos_compras                      
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }
    
}