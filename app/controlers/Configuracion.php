<?php

class Configuracion extends Controlador
{



    public function __construct()
    {
        session_start();    
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');
        $this->arrFieldsValidateCompany = ['razonsocialpiensos','cifpiensos','direccionpiensos','codigopostalpiensos','localidadpiensos','provinciapiensos'];
        $this->arrFieldsValidateEmail = ['remitente','correo','passwordcorreo','host','puerto','protocolo'];
        $this->arrFieldsValidateProveedor = ['idtransportista','idprovfabrica','precioprovfab','idproductotransp','idproductofab'];
        $this->modeloProveedor = $this->modelo('ModeloProveedor');
        $this->modeloProductoCompra = $this->modelo('ModeloProductoCompra');     
    }
        
    public function index()
    {        
        $datos = $this->modeloConfiguracion->getDataConfiguration();
        $datos->proveedores = $this->modeloProveedor->getEnabledSuppliers();
        $datos->productos = $this->modeloProductoCompra->getAllPurchaseProducts();
        $this->vista('configuracion/configuracion', $datos);
    }

    public function actualizarDatosConfiguracionEmpresa()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;   

        
        if(trim($_POST['razonsocialpiensos']) != '' && trim($_POST['cifpiensos']) != '' && trim($_POST['direccionpiensos']) != '' && trim($_POST['codigopostalpiensos']) != '' && trim($_POST['localidadpiensos']) != '' && trim($_POST['provinciapiensos']) != ''){

            $datos['razonsocialpiensos'] = trim($_POST['razonsocialpiensos']);
            $datos['cifpiensos'] = trim($_POST['cifpiensos']);
            $datos['direccionpiensos'] = trim($_POST['direccionpiensos']);
            $datos['codigopostalpiensos'] = trim($_POST['codigopostalpiensos']);
            $datos['localidadpiensos'] = trim($_POST['localidadpiensos']);
            $datos['provinciapiensos'] = trim($_POST['provinciapiensos']);
                                                 
            
            $upd = $this->modeloConfiguracion->updateConfigurationCompany($datos);

            if($upd){
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ACTUALIZACION;              
                $respuesta['datos'] = $this->modeloConfiguracion->getDataConfiguration();
            }                        

        }else{
            $fieldsValidate = UtilsHelper::validateRequiredFields($_POST, $this->arrFieldsValidateCompany);
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
            $respuesta['fieldsValidate'] = $fieldsValidate;
        } 
        echo json_encode($respuesta);
    }

    public function actualizarDatosConfiguracionCorreo()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;           

        if(trim($_POST['remitente']) != '' && trim($_POST['correo']) != '' && trim($_POST['passwordcorreo']) != '' && trim($_POST['host']) != '' && trim($_POST['puerto']) != '' && trim($_POST['protocolo']) != ''){

            $datos['remitente'] = trim($_POST['remitente']);
            $datos['correo'] = trim($_POST['correo']);
            $datos['passwordcorreo'] = trim($_POST['passwordcorreo']);
            $datos['host'] = trim($_POST['host']);
            $datos['puerto'] = trim($_POST['puerto']);
            $datos['protocolo'] = trim($_POST['protocolo']);
                     
            $upd = $this->modeloConfiguracion->updateConfigurationDataEmailAccount($datos);

            if($upd){
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ACTUALIZACION;  
                $respuesta['datos'] = $this->modeloConfiguracion->getDataConfiguration();            
            }                        

        }else{
            $fieldsValidate = UtilsHelper::validateRequiredFields($_POST, $this->arrFieldsValidateEmail);
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
            $respuesta['fieldsValidate'] = $fieldsValidate;
        } 
        echo json_encode($respuesta);
    }

    public function actualizarDatosConfiguracionTransportista()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;   
       
        if(isset($_POST)){
            $validate = $this->validarDatosConfiguracionproveedor($_POST);
                   
            
            /*echo"<br>validate <br>";
            print_r($validate);
            die;*/
            

            if($validate==1){
                      
                $upd = $this->modeloConfiguracion->updateConfigurationCarrier($_POST);

                if($upd){
                    $respuesta['error'] = false;
                    $respuesta['mensaje'] = OK_ACTUALIZACION;  
                    $respuesta['datos'] = $this->modeloConfiguracion->getDataConfiguration();            
                }                        

            }else{
                $fieldsValidate = UtilsHelper::validateRequiredFieldsSettingSuppliers($_POST, $this->arrFieldsValidateProveedor);
                $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;
                $respuesta['fieldsValidate'] = $fieldsValidate;
            } 

        }
        

        echo json_encode($respuesta);
    }

    private function validarDatosConfiguracionproveedor($post)
    {
     
        $retorno = 0;
        if(isset($post['idtransportista']) && $post['idtransportista'] > 0 && isset($post['idprovfabrica']) && $post['idprovfabrica'] > 0 && $post['precioprovfab'] > 0 && isset($post['idproductotransp']) && isset($post['idproductofab']) ){           
            $retorno = 1;
        }
        return $retorno;
    }


    public function guardarCopiaDeSeguridad()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_GUARDADO;           

        if($_POST['guardarcopia']){
            $create = $this->generarCopiaDeSeguridad();            
            if($create){
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ACTUALIZACION;
            }            
            
        }
        echo json_encode($respuesta);
    }
    
    private function generarCopiaDeSeguridad()
    {        
        // Configuración de la base de datos
        $host = DB_HOST; // Cambia esto al nombre de tu servidor de base de datos
        $usuario = DB_USUARIO; // Cambia esto al nombre de usuario de tu base de datos
        $contrasena = DB_PASSWORD; // Cambia esto a tu contraseña de base de datos
        $base_de_datos = DB_NOMBRE; // Cambia esto al nombre de tu base de datos                
        
        // Ruta donde se almacenará la copia de seguridad (asegúrate de que el directorio exista)
        $ruta_resp = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF'])."/bases/";        
        
        // Nombre del archivo de respaldo
        $archivo_resp = $base_de_datos.'_respaldo_'.date('Y-m-d-His').'.sql';
        
        chmod($ruta_resp, 755);        
        
        // Comando para realizar la copia de seguridad utilizando mysqldump
        $comando = "mysqldump --host=$host --user='$usuario' --password='$contrasena' --databases $base_de_datos > $ruta_resp$archivo_resp";                
        
        // Ejecutar el comando para crear la copia de seguridad
        exec($comando, $output, $resultado);
        
        // Verificar si la copia de seguridad se creó correctamente
        if ($resultado === 0 && file_exists($ruta_resp.$archivo_resp) && filesize($ruta_resp.$archivo_resp) > 0) {
            // Otorgar permisos de escritura al usuario actual       
            return true;            
        } else {
            return false;            
        }                      
    }
    

   

}
