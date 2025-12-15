<?php

class Usuarios extends Controlador
{



    public function __construct()
    {
        session_start();        
        $this->modeloUsuarios = $this->modelo('ModeloUsuario');
    }

    public function index()
    {
        $users = $this->modeloUsuarios->obtenerUsuarios();

        $datos = [];

        $this->vista('usuarios/usuarios', $datos);
    }

    public function tablaUsuarios()
    {  
        $page = $_POST['numPagina'];
        $order = $_POST['orden'];
        $where = base64_decode($_POST['where']);
        $limit = $_POST['numRegistrosPagina'];                          

        $mystring = $where;
        $findme   = 'lower(concat'; //viene de la clásula general
        $pos = strpos($mystring, $findme);

        if ($pos !== false) {            
            $where = str_replace("lower(concat( ' ',id,' ',usuario,' ',apellidos,' ',correo,' ',rol ))", "lower(concat( ' ',usu.id,' ',usu.user_name,' ',apellidos,' ',usu.correo,' ',roles.rol ))", $where);
        }
        $where = str_replace("id like", "usu.id like", $where);
        $where = str_replace("usuario like", "usu.usuario like", $where);
        $where = str_replace("apellidos like", "usu.apellidos like", $where);
        $where = str_replace("nombre_rol like", "roles.rol like", $where);
                
        if ($where == "") {
            $where = " WHERE usu.status <> 'eliminado' ";
        }else{
            $where .= " AND usu.status <> 'eliminado' ";
        }                            
        
        $users = $this->modeloUsuarios->obtenerUsuariosTabla($page,$order,$where,$limit);
        $totalRegistros = $this->modeloUsuarios->obtenerTotalUsuarios($where);

        $salida = [
            'totalRegistros' => $totalRegistros,
            'registros' => $users
        ];

        print_r(json_encode($salida));
    }

    public function altaUsuarios()
    {
        $this->vista('usuarios/altaUsuarios');
    }

    
    public function crearUsuario()
    {
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_CREACION;   

        if($_POST['nombre_usuario'] != '' && $_POST['apellidos_usuario'] != '' && $_POST['email_usuario'] != '' && $_POST['password_usuario'] != '' && $_POST['rol_usuario'] != '' && $_POST['estado_usuario'] != '') {


            $ins = $this->modeloUsuarios->addUser($_POST);
            if ($ins && $ins >0) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_CREACION;
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }

    
    public function obtenerUsuario()
    {

        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['id']) && $_POST['id'] > 0) {

            $upd = $this->modeloUsuarios->getUserById($_POST['id']);
            if ($upd) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = '';
                $respuesta['datos'] = $upd;
            }

        }
        print_r(json_encode($respuesta));
    }  

    public function actualizarUsuario(){
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_ACTUALIZACION;   

        if(isset($_POST['id']) && $_POST['id'] > 0 && $_POST['nombre_usuario'] != '' && $_POST['apellidos_usuario'] != '' && $_POST['email_usuario'] != '' && $_POST['password_usuario'] != '' && $_POST['rol_usuario'] != '' && $_POST['estado_usuario'] != '') {


            $upd = $this->modeloUsuarios->updateUser($_POST);
            if ($upd && $upd >0) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ACTUALIZACION;
            }

        }else{
            $respuesta['mensaje'] = ERROR_FORM_INCOMPLETO;            
        }             
        print_r(json_encode($respuesta));
    }

    public function eliminarUsuario()
    {
        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = ERROR_DOESNT_EXIST;   

        if(isset($_POST['id']) && $_POST['id'] > 0) {

            $upd = $this->modeloUsuarios->deleteUserById($_POST['id']);
            if ($upd) {
                $respuesta['error'] = false;
                $respuesta['mensaje'] = OK_ELIMINACION;                
            }else{
                $respuesta['mensaje'] = ERROR_ELIMINACION;   
            }

        }
        print_r(json_encode($respuesta));   
    }






     

  


}
