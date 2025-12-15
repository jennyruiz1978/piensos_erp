<?php


class ModeloProductoVenta{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    
    public function getSaleProduct($id){
        $this->db->query("SELECT pv.*, uni.unidad as abrev_unidad, uni.equivalencia
                        FROM productos_ventas pv 
                        LEFT JOIN unidades uni ON pv.idunidad = uni.id
                        WHERE pv.id = $id");        
        $fila = $this->db->registro();
        return $fila;
    }

    public function getSaleProductByPurchaseProductId($idProductoCompra){
        $this->db->query("SELECT pv.*, uni.unidad as abrev_unidad, uni.equivalencia
                        FROM productos_ventas pv 
                        LEFT JOIN unidades uni ON pv.idunidad = uni.id
                        WHERE pv.idproductocompra = $idProductoCompra");        
        $fila = $this->db->registro();
        return $fila;
    }

    public function getSaleProductData($id){
        $this->db->query("SELECT pv.*, 
                        (SELECT unidades.equivalencia FROM unidades WHERE pv.idunidad = unidades.id) AS equivalencia
                        FROM productos_ventas pv                                          
                        WHERE pv.id = $id");        
        $fila = $this->db->registro();
        return $fila;
    }        

    public function getAllSaleProducts(){
        $this->db->query("SELECT pc.*, uni.unidad as abrev_unidad 
                        FROM productos_ventas pc
                        LEFT JOIN unidades uni ON pc.idunidad = uni.id ");        
        $filas = $this->db->registros();
        return $filas;
    }

    public function updateSaleProduct($descripcion, $iva, $id){
        $this->db->query("UPDATE productos_ventas
                        SET descripcion = '$descripcion' , iva = '$iva'
                        WHERE id = $id ");
        
        return $this->db->execute();
    }

    public function getNameProduct($id){
        $this->db->query("SELECT descripcion FROM productos_ventas WHERE id = $id ");        
        $fila = $this->db->registro();
        return (isset($fila->descripcion))? $fila->descripcion: 0;
    }


    public function obtenerProductosVentaTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT  pv.id, pv.descripcion, pv.iva, 
                        (SELECT uni.descripcion FROM unidades uni WHERE uni.id = pv.idunidad) AS nombre_unidad/*,
                        (SELECT pc.descripcion FROM productos_compras pc WHERE pc.id=pv.idproductocompra) AS prodcompra*/ 
                        FROM productos_ventas pv                       
                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalProductosVenta($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM productos_ventas                      
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }    
}