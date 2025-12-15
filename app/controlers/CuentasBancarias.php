<?php

class CuentasBancarias extends Controlador
{

    public function __construct()
    {
        session_start();        
        $this->modeloCuentasBancarias = $this->modelo('ModeloCuentasBancarias');
        $this->arrFieldsCreate = ['numerocuenta','banco'];       
        $this->arrFieldsUpdate = ['id','numerocuenta','banco']; 
        $this->tabla = 'cuentas_bancarias';
        $this->modeloBase = $this->modelo('ModeloBase');        
        $this->tablaFacturasCliente = 'clientes_facturas';  
        
        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        } 
    }

    public function index()
    {        
        $datos = [];
        $this->vista('cuentasBancarias/cuentasBancarias', $datos);
    }

    public function tablaCuentasBancarias()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat';
        $pos = strpos($mystring, $findme);
                        
        
        $cuentasBancarias = $this->modeloCuentasBancarias->obtenerCuentasBancariasTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloCuentasBancarias->obtenerTotalCuentasBancarias($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $cuentasBancarias
        ];

        print_r(json_encode($salida));
    }

    public function crearActualizarCuentaBancaria()
    {                      
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;   

    
        if(trim($_POST['numerocuenta']) != '' && trim($_POST['banco']) != ''){

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

    public function obtenerCuenta()
    {    
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   
               
        if(isset($this->fetch) && $this->fetch['id'] > 0) {
            
            $idCuenta = $this->fetch['id'];
            $datosCuenta = $this->modeloCuentasBancarias->getBankAccount($idCuenta);
            
            $respuesta['error'] = false;
            $respuesta['mensaje'] = '';
            $respuesta['datos'] = $datosCuenta;

        }

        print_r(json_encode($respuesta));
    }   

    public function consultarCuentaBancariaParaEliminar()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;
        
        if(isset($this->fetch) && isset($this->fetch['id']) && $this->fetch['id'] > 0) {

            $respuesta['error'] = false;
            $idCuenta = $this->fetch['id'];
            $cuentaEnAlbaran = $this->modeloCuentasBancarias->getBankAccountFromDeliveryNoticesClients($idCuenta);

            if($cuentaEnAlbaran > 0){                
                $respuesta['mensaje'] = "Aquest compte està sent utilitzat en albarans de clients. Esteu segur(a) d'eliminar el compte?";
            }else{                
                $respuesta['mensaje'] = "Esteu segur d'eliminar el compte?";
            }
        }
       
        print_r(json_encode($respuesta));
    }

    public function eliminarCuenta()
    {    
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['idCuentaEliminar']) && $_POST['idCuentaEliminar'] > 0) {

            $idCuenta = $_POST['idCuentaEliminar'];

            $upd = $this->modeloCuentasBancarias->deleteAccountBankById($idCuenta);
            if ($upd) {
                $this->modeloBase->updateFieldTablaWithCustomizeWhere($this->tablaFacturasCliente, 'idcuentabancaria', null, 'idcuentabancaria', $idCuenta);
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }   


}
