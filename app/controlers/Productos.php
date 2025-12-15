<?php

class Productos extends Controlador
{



    public function __construct()
    {
        session_start();        
        $this->modeloProductosVenta = $this->modelo('ModeloProductoVenta');
        $this->modeloProductosCompra = $this->modelo('ModeloProductoCompra');        
    }

    public function index()
    {        

        $idProductoCompra = 1;
        $idProductoVenta = 1;
        $productoCompra = $this->modeloProductosCompra->getPurchaseProduct($idProductoCompra);
        $productoVenta = $this->modeloProductosVenta->getSaleProduct($idProductoVenta);

        $datos = [
            "productoCompra" => $productoCompra,
            "productoVenta" => $productoVenta
        ];

        $this->vista('productos/productos', $datos);
    }

    public function actualizarProductoCompra(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;
        $upd = false;
        if(isset($_POST['nombre_prod_compra']) && isset($_POST['iva_compras']) && isset($_POST['id_prod_compra']) && $_POST['id_prod_compra'] > 0){
            $upd = $this->modeloProductosCompra->updatePurchaseProduct($_POST['nombre_prod_compra'], $_POST['iva_compras'], $_POST['id_prod_compra']);
        }
        if($upd){
            $respuesta['error'] = false;
            $respuesta['mensaje'] = OK_ACTUALIZACION;
        }
        print_r(json_encode($respuesta));
    }

    public function actualizarProductoVenta(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;
        $upd = false;
        if(isset($_POST['nombre_prod_venta']) && isset($_POST['iva_ventas']) && isset($_POST['id_prod_venta']) && $_POST['id_prod_venta'] > 0){
            $upd = $this->modeloProductosVenta->updateSaleProduct($_POST['nombre_prod_venta'], $_POST['iva_ventas'], $_POST['id_prod_venta']);
        }
        if($upd){
            $respuesta['error'] = false;
            $respuesta['mensaje'] = OK_ACTUALIZACION;
        }
        print_r(json_encode($respuesta));
    }
   
}
