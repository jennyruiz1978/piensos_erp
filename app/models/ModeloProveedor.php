<?php


class ModeloProveedor{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }


    public function getSuppliers(){
        $this->db->query('SELECT id, nombrefiscal FROM proveedores ');
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function getEnabledSuppliers()
    {
        $this->db->query("SELECT id, nombrefiscal FROM proveedores WHERE status = 'activo' ORDER BY nombrefiscal ASC ");
        $resultado = $this->db->registros();
        return $resultado;
    }

    public function obtenerProveedoresTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT id, nombrefiscal, nif, direccion, codigopostal, poblacion, email, status
                        FROM proveedores                        
                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalProveedores($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM proveedores                      
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }

    public function addSupplier($arr){

        $nombrefiscal = $arr['nombrefiscal'];
        $nif = $arr['nif'];
        $direccion = $arr['direccion'];
        $poblacion = $arr['poblacion'];
        $codigopostal = $arr['codigopostal'];
        $provincia = $arr['provincia'];
        $telefono = $arr['telefono'];
        $email = $arr['email'];
        $observaciones = $arr['observaciones'];
        $status = $arr['status'];

        $this->db->query("INSERT INTO proveedores (nombrefiscal,nif,direccion,poblacion,codigopostal,provincia,telefono,email,observaciones,status) 
                        VALUES ('$nombrefiscal','$nif','$direccion','$poblacion','$codigopostal','$provincia','$telefono','$email','$observaciones','$status')");
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return 0;
        }

    }    

    public function getSupplierById($id){
        $this->db->query("SELECT * FROM proveedores WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila;
    }

    public function getNifSupplier($id){
        $this->db->query("SELECT nif FROM proveedores WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila->nif;
    }

    public function getNameSupplier($id){
        $this->db->query("SELECT nombrefiscal FROM proveedores WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila->nombrefiscal;
    }
    
    public function deleteSupplierById($id){

        $this->db->query("UPDATE proveedores SET status = 'eliminado' WHERE id = $id ");        

        if($this->db->execute()){
            return 1;
        }else {
            return 0;
        }
    }

    public function updateSupplier($datos){

        $id = $datos['id'];
        $nombrefiscal = $datos['nombrefiscal'];
        $nif = $datos['nif'];
        $direccion = $datos['direccion'];
        $poblacion = $datos['poblacion'];
        $codigopostal = $datos['codigopostal'];
        $provincia = $datos['provincia'];
        $telefono = $datos['telefono'];
        $email = $datos['email'];
        $observaciones = $datos['observaciones'];
        $status = $datos['status'];

        $this->db->query("UPDATE proveedores SET nombrefiscal = '$nombrefiscal', nif = '$nif', direccion = '$direccion', poblacion = '$poblacion', codigopostal = '$codigopostal', provincia = '$provincia', telefono = '$telefono', email = '$email', observaciones = '$observaciones', status = '$status'
                        WHERE id = $id ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

        
    public function obtenerProveedoresExportar($order,$where){       
                
        $this->db->query("SELECT id, nombrefiscal, nif, direccion, codigopostal, poblacion, email, status
                        FROM proveedores                        
                        $where $order ");

        $filas = $this->db->registros();

        return $filas;
    }

}