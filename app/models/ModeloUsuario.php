<?php


class ModeloUsuario{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }


    public function obtenerUsuarios(){
        $this->db->query('SELECT * FROM usuarios');

        $resultado = $this->db->registros();

        return $resultado;
    }

    public function obtenerUsuariosTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT usu.id,usu.user_name,usu.apellidos,usu.email,roles.rol AS nombre_rol
                        FROM usuarios usu
                        LEFT JOIN roles roles ON usu.idRol=roles.idRol
                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalUsuarios($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM usuarios usu
                        LEFT JOIN roles roles ON usu.idRol=roles.idRol
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }

    public function addUser($datos){

        $user_name = $datos['nombre_usuario'];
        $apellidos = $datos['apellidos_usuario'];
        $email = $datos['email_usuario'];
        $pass = $datos['password_usuario'];
        $idRol = $datos['rol_usuario'];
        $status = $datos['estado_usuario'];

        $this->db->query("INSERT INTO usuarios (user_name,apellidos,email,pass,idRol,status) 
                        VALUES ('$user_name','$apellidos','$email','$pass','$idRol','$status')");
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return 0;
        }

    }    

    public function getUserById($id){
        $this->db->query("SELECT * FROM usuarios WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila;
    }

    
    public function deleteUserById($id){

        $this->db->query("UPDATE usuarios SET status = 'eliminado' WHERE id = $id ");        

        if($this->db->execute()){
            return 1;
        }else {
            return 0;
        }
    }

    public function updateUser($datos){

        $id = $datos['id'];
        $user_name = $datos['nombre_usuario'];
        $apellidos = $datos['apellidos_usuario'];
        $email = $datos['email_usuario'];
        $pass = $datos['password_usuario'];
        $idRol = $datos['rol_usuario'];
        $status = $datos['estado_usuario'];

        $this->db->query("UPDATE usuarios SET user_name = '$user_name', apellidos = '$apellidos', email = '$email', pass = '$pass', status = '$status', idRol = '$idRol' 
                        WHERE id = $id ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }


}