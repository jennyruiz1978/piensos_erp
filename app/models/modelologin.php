<?php

class modelologin {

    private $db;

    public function __construct() {
        $this->db = new Base;
    }
    
    
    public function comprobarLogin($mail, $pass){
        
        $this->db->query("SELECT * FROM usuarios WHERE email = '$mail' AND pass = '$pass' AND status = 'activo' ");        
        $fila = $this->db->registro();        
        $retorno = (isset($fila->id) && $fila->id > 0)? $fila->id: 0;
        return $fila;

    }

    public function comprobarEmail($email){
        
        $this->db->query("SELECT * FROM usuarios WHERE email = '$email' and status = 'activo' ");
        $fila = $this->db->registro();
        $retorno = (isset($fila->email) && $fila->email != null && $fila->email != '')? $fila: false;
        return $retorno;

    }


    
    public function comprobarLoginX($mail, $pass) {
        //Ahora trabajamos con un array, en producción lo haremos con una tabla de la base de datos
        $usuarios = ["test@piensos.com", "Admin", "test"];

        if (in_array($mail, $usuarios) && in_array($pass, $usuarios)) {
            //return $usuarios;
            return 1;
        } else {
            return 0;
        }
    }

    public function actualizarContraseniaUsuario($pass, $id){
        $this->db->query("UPDATE usuarios SET pass = '$pass' WHERE id = '$id' ");
        $retorno = ($this->db->execute())? true:false; 
        return $retorno;        
    }

    

}
