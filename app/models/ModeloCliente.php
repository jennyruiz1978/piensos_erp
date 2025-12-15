<?php


class ModeloCliente{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    
    public function getClients(){
        $this->db->query('SELECT id, nombrefiscal FROM clientes');

        $resultado = $this->db->registros();

        return $resultado;
    }

        
    public function getEnabledClients(){
        $this->db->query("SELECT id, nombrefiscal FROM clientes WHERE status = 'activo' ORDER BY nombrefiscal ASC ");

        $resultado = $this->db->registros();

        return $resultado;
    }


    public function obtenerClientes(){
        $this->db->query('SELECT * FROM clientes');

        $resultado = $this->db->registros();

        return $resultado;
    }

    public function obtenerClientesTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT id, nombrefiscal, nif, zona, direccion, codigopostal, poblacion, email, status
                        FROM clientes                        
                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalClientes($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM clientes                      
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }

    public function addClient($arr){

        $nombrefiscal = $arr['nombrefiscal'];
        $nif = $arr['nif'];        
        $idzona = $arr['idzona'];
        $zona = $arr['zona'];
        $precio = $arr['precio'];
        $direccion = $arr['direccion'];
        $poblacion = $arr['poblacion'];
        $codigopostal = $arr['codigopostal'];
        $provincia = $arr['provincia'];
        $telefono = $arr['telefono'];
        $email = $arr['email'];
        $observaciones = $arr['observaciones'];
        $status = $arr['status'];        
        $formacobro = $arr['formacobro'];        
        $contactos = $arr['contactos']; 

        $this->db->query("INSERT INTO clientes (nombrefiscal,nif,idzona,zona,direccion,poblacion,codigopostal,provincia,telefono,email,observaciones, contactos, status,precio,formacobro) 
                        VALUES ('$nombrefiscal','$nif','$idzona','$zona','$direccion','$poblacion','$codigopostal','$provincia','$telefono','$email','$observaciones','$contactos','$status','$precio','$formacobro')");
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return 0;
        }

    }    

    public function getClientById($id){
        $this->db->query("SELECT * FROM clientes WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila;
    }

    
    public function deleteClientById($id){

        $this->db->query("UPDATE clientes SET status = 'eliminado' WHERE id = $id ");        

        if($this->db->execute()){
            return 1;
        }else {
            return 0;
        }
    }

    public function updateClient($datos){

        $id = $datos['id'];
        $nombrefiscal = $datos['nombrefiscal'];
        $nif = $datos['nif'];
        $idzona = $datos['idzona'];
        $zona = $datos['zona'];
        $precio = $datos['precio'];
        $direccion = $datos['direccion'];
        $poblacion = $datos['poblacion'];
        $codigopostal = $datos['codigopostal'];
        $provincia = $datos['provincia'];
        $telefono = $datos['telefono'];
        $email = $datos['email'];
        $observaciones = $datos['observaciones'];
        $status = $datos['status'];        
        $formacobro = $datos['formacobro'];  
        $contactos = $datos['contactos'];

        $this->db->query("UPDATE clientes SET nombrefiscal = '$nombrefiscal', idzona = '$idzona' , zona = '$zona',nif = '$nif', direccion = '$direccion', poblacion = '$poblacion', codigopostal = '$codigopostal', provincia = '$provincia', telefono = '$telefono', email = '$email', observaciones = '$observaciones', status = '$status', precio = '$precio', formacobro = '$formacobro', contactos = '$contactos'
                        WHERE id = $id ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function getNifClient($id){
        $this->db->query("SELECT nif FROM clientes WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila->nif;
    }

    public function getNameClient($id){
        $this->db->query("SELECT nombrefiscal FROM clientes WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila->nombrefiscal;
    }

    public function getClientZoneProfitMargin($id)
    {
        $this->db->query("SELECT cli.*, 
        (SELECT zon.margen FROM zonas zon WHERE zon.id=cli.idzona) AS margen
        FROM clientes cli WHERE cli.id = '$id' ");        
        $fila = $this->db->registro();
        return $fila;
    }

    public function getZoneDataByClientId($id)
    {
        $this->db->query("SELECT zon.* FROM clientes cli
        LEFT JOIN zonas zon ON zon.id=cli.idzona
        WHERE cli.id = '$id' ");        
        $fila = $this->db->registro();
       
        return $fila;
    }

    public function getPriceByClientId($id)
    {
        $this->db->query("SELECT precio FROM clientes WHERE id = '$id' ");        
        $fila = $this->db->registro();
        $precio = (isset($fila->precio) && $fila->precio != '' && $fila->precio > 0)? $fila->precio: 0;        
        return $precio;
    }
    
    public function obtenerClientesExportar($order,$where){       
                
        $this->db->query("SELECT id, nombrefiscal, nif, zona, direccion, codigopostal, poblacion, email, status
                        FROM clientes       
                        $where $order ");

        $filas = $this->db->registros();

        return $filas;
    }

    
    public function getFormaCobroClientById($id){
        $this->db->query("SELECT formacobro FROM clientes WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila->formacobro;
    }

    
      

}