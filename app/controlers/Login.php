<?php

class Login extends Controlador {

    public function __construct() {
        $this->usuarioModelo = $this->modelo('modelologin');
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');
    }

    public function index() {                      
            $this->vista('login/login');
    }
    

    public function acceder() {

       

        if (isset($_POST['mail']) && trim($_POST['mail']) != '' && isset($_POST['pass']) && trim($_POST['pass']) != '') {
           
            $mail = $_POST['mail'];
            $pass = $_POST['pass'];
        
            $validacion = $this->usuarioModelo->comprobarLogin($mail, $pass);

           

            if ($validacion == false) {

                redireccionar('/Login');
            } else {        
                
              
                session_start();
                $_SESSION['inicio'] = date("Y-n-j H:i:s");
                
                $_SESSION['usuario'] = $validacion->email;
                $_SESSION['token_control'] = 1;
                
                if($validacion->idRol == 1){
                    $_SESSION['permisos'] = $this->construirPermisos('Admin');
                }
               
                redireccionar('/Inicio');
            
              

                
            }
        }else{
            redireccionar('/Login');  
        }
    }


    public function vaciar(){
        
        session_start();
        session_unset();
        session_destroy();
        if(headers_sent()){
        return "<script>window.location.href=" . RUTA_URL . "</script>";    
        } else {
        redireccionar('/Login');    
        }
        
    }

    public function construirPermisos($rol){    
        $data = file_get_contents(RUTA_APP."/config/configroles.json");
        $permisosUsuario = json_decode($data, true)[$rol];                
        return $permisosUsuario;
    }

    
    public function recuperarConstrasenia(){
        $datos = [];
        $this->vista('login/recuperar_password', $datos);
    }

    public function resetearContrasenia(){
        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_VALIDACION_EMAIL;              

        if (isset($_POST['email']) && trim($_POST['email']) != '' ) {                        
        
            $valida = $this->usuarioModelo->comprobarEmail($_POST['email']);
            
            $passAntes = $valida->pass;
            
            if ($valida != false) {                
                $respuesta['mensaje'] = ERROR_RECUPERAR_PASS;
                $pass = UtilsHelper::random_password(8);
                if($pass){
                    $upd = $this->usuarioModelo->actualizarContraseniaUsuario($pass, $valida->id);
                    $send = $this->enviarCorreoEmailusuario($valida->email, $pass);                
                    if($upd && $send){                        
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = OK_RECUPERAR_PASS;                        
                    }else{
                        $this->usuarioModelo->actualizarContraseniaUsuario($passAntes, $valida->id);
                        $respuesta['error'] = true;
                        $respuesta['mensaje'] = ERROR_RECUPERAR_PASS2;   
                    }     
                    
                }
            }
        }
        print_r(json_encode($respuesta));  
    }

    public function enviarCorreoEmailusuario($correo, $pass)    
    {                       
            $retorno = 0;       
            //contruyo array con datos de envío:
            $datosCorreo = $this->modeloConfiguracion->getDataConfiguration();  
            
            $puesto = '';
            $movil = '';
            $telefono = '';

            if(isset($datosCorreo) && isset($datosCorreo->correo) && $datosCorreo->correo != ''){

                $asunto = 'Recuperació de contrasenya';
                $emailsDestino = [$correo];

                $plantilla = file_get_contents(DIR_PLANTILLAS."recuperarPassword.php"); 
                       
                
                $contenido = 'Benvolgut usuari, a sol·licitud vostra, hem restablert la contrasenya. La nova contrasenya és: '.$pass;

                $cambiar = ['{CONTENIDO}','{NOMBRE}','{PUESTO}','{MOVIL}'];
                $nombreRemitente = $datosCorreo->remitente;
                $cambio = [$contenido, $nombreRemitente, $puesto, $movil, $telefono];
                $mensaje = str_replace($cambiar,$cambio,$plantilla);                    
                $message = html_entity_decode($mensaje);                

                $envio = enviarEmail::enviarEmailDestinatario($emailsDestino, $asunto, $message, $datosCorreo);
                
                if ($envio) {                    
                    $retorno = 1;
                }else{
                    $retorno = 0;
                }    
            }       
            return $retorno;                   
    }

    

}
