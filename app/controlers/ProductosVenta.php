<?php

class ProductosVenta extends Controlador
{

    public function __construct()
    {
        session_start();        
        $this->modeloProductoVenta = $this->modelo('ModeloProductoVenta');        
        $this->modeloProductoCompra = $this->modelo('ModeloProductoCompra');  
        $this->modeloIva = $this->modelo('ModeloIva');
        $this->modeloUnidad = $this->modelo('ModeloUnidad');
        $this->arrFieldsCreate = ['descripcion','iva','idunidad','idproductocompra'];       
        $this->arrFieldsUpdate = ['id','descripcion','iva','idunidad','idproductocompra'];   
        $this->modeloBase = $this->modelo('ModeloBase');  
        $this->tabla = 'productos_ventas';   
        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        }    
    }

    public function index()
    {        
        $datos = [    
            'tiposiva' => $this->modeloIva->getAllIvasActive(),
            'unidades' => $this->modeloUnidad->getAllUnits(),
            'productos_compra' => $this->modeloProductoCompra->getAllPurchaseProducts()
        ];        
        $this->vista('productos/productosVenta', $datos);
    }

    public function tablaProductosVenta()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);       
        
        $where = str_replace("fecha like", "DATE_FORMAT(fecha, '%d/%m/%Y') like", $where);        
        
        $productos = $this->modeloProductoVenta->obtenerProductosVentaTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloProductoVenta->obtenerTotalProductosVenta($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $productos
        ];

        print_r(json_encode($salida));
    }    

    
    public function crearActualizarProducto()
    {                      
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;   

    
        if(trim($_POST['descripcion']) != '' && isset($_POST['iva']) && $_POST['iva'] > 0 && isset($_POST['idunidad']) && $_POST['idunidad'] > 0 && isset($_POST['idproductocompra']) && $_POST['idproductocompra'] > 0){

            if($_POST['id'] != '' && $_POST['id'] > 0){
                
                $arrWhere['id'] = $_POST['id'];
                            
                $stringQueries = UtilsHelper::buildStringsUpdateQuery($_POST, $this->arrFieldsUpdate);
                $ok = $stringQueries['ok'];                        
                    
                $stringWhere = UtilsHelper::buildStringsWhereQuery($arrWhere);
                $okw = $stringWhere['ok'];    
                            
                if($ok && $okw){
                    $strFieldsValues = $stringQueries['strFieldsValues'];
                    $strWhere = $stringWhere['strWhere'];

                    $upd = $this->modeloBase->updateRow($this->tabla, $strFieldsValues, $strWhere);

                    if($upd){
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_ACTUALIZACION;
                        $respuesta['id'] = $_POST['id'];  
                    }
                }     

            }else{

                $stringQueries = UtilsHelper::buildStringsInsertQueryNuevo($_POST, $this->arrFieldsCreate);

                $ok = $stringQueries['ok'];
                $strFields = $stringQueries['strFields'];
                $strValues = $stringQueries['strValues'];


                           
                if($ok){
                    $ins = $this->modeloBase->insertRow($this->tabla, $strFields, $strValues);
                    if($ins){
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_CREACION;    
                        $respuesta['id'] = $ins;  
                    }
                }                                

            }                                                 

        }else{
            $fieldsValidate = UtilsHelper::validateRequiredFields($_POST, $this->arrFieldsCreate);
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
            $respuesta['fieldsValidate'] = $fieldsValidate;
        } 
        echo json_encode($respuesta);
    }

    public function obtenerProducto()
    {    
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   
               
        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            
            $idProducto = $this->fetch['id'];
            $datosProducto = $this->modeloProductoVenta->getSaleProduct($idProducto);
            
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['datos'] = $datosProducto;

        }

        print_r(json_encode($respuesta));
    }

   
}
