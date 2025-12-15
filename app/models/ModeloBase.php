<?php


class ModeloBase{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    

    public function insertRow($tabla, $strFields, $strValues){

        $this->db->query("INSERT INTO $tabla  $strFields VALUES $strValues ");

        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return false;
        }

    }          

    
    public function updateRow($tabla, $fieldsValues, $where){

        $this->db->query("UPDATE $tabla SET $fieldsValues WHERE $where ");      

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }       

  

    
    public function updateFieldTabla($tabla, $field, $value, $id){
        $this->db->query("UPDATE $tabla SET $field = '$value' WHERE id = '$id' ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function updateFieldTablaWithCustomizeWhere($tabla, $field, $value, $whereField, $whereValueString){
        $this->db->query("UPDATE $tabla SET $field = '$value' WHERE $whereField IN ( $whereValueString ) ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function updateFieldTablaByStringIn($tabla, $field, $value, $string){
        $this->db->query("UPDATE $tabla SET $field = '$value' WHERE id IN ( $string ) ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function deleteRow($tabla, $where){

        $this->db->query("DELETE FROM $tabla WHERE $where ");        

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function getFieldTabla($tabla, $field, $id){
        $this->db->query("SELECT $field FROM $tabla  WHERE id = '$id' ");
        
        $row = $this->db->registro();
        return $row;
    }

    public function existIdInvoice($tabla, $idDoc)
    {
        $this->db->query("SELECT id FROM $tabla  WHERE id = '$idDoc' ");        
        $row = $this->db->registro();        
        return (isset($row->id))? $row->id: 0;
    }

    public function max($query){

        $this->db->query("$query");        

        $fila = $this->db->registro();
        $maximo = 1;
        if(isset($fila->maximo) && $fila->maximo > 0){
            $maximo = $fila->maximo + 1;
        }
        return $maximo;

    }

    public function maximoIdTabla($field, $tabla)
    {        
        $this->db->query("SELECT MAX($field) AS maximo FROM $tabla ");        
        
        $fila = $this->db->registro();
        $maximo = 1;
        if(isset($fila->maximo) && $fila->maximo > 0){
            $maximo = $fila->maximo + 1;
        }
        return $maximo;
    }

    public function maximoNumDocumentoAnio($field, $tabla, $anio)
    {        
        $this->db->query("SELECT MAX($field) AS maximo FROM $tabla WHERE YEAR(fecha) = '$anio' ");
        
        $fila = $this->db->registro();
        $maximo = 1;
        if(isset($fila->maximo) && $fila->maximo > 0){
            $maximo = $fila->maximo + 1;
        }
        return $maximo;
    }

    public function getAllFieldsTablaByFieldFilter($tabla, $fieldFilter, $fieldValue){
        $this->db->query("SELECT * FROM $tabla  WHERE $fieldFilter = '$fieldValue' ");        
        $rows = $this->db->registros();
        return $rows;
    }

 
    public function insertRowEmailSent($tabla, $strFields, $strValues)
    {           
        // Convertir los campos y valores a arrays
        $fields = array_map('trim', explode(',', trim($strFields, '() ')));
        $values = str_getcsv(trim($strValues, '() '), ',', "'");
    
        // Limpiar los valores
        $values = array_map(function($val) {
            return trim($val, " '");
        }, $values);
    
        // Crear los placeholders para la consulta preparada
        $placeholders = array_map(function($field) {
            return ':' . str_replace(' ', '', $field);
        }, $fields);
    
        // Construir la consulta
        $query = "INSERT INTO $tabla (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
    
        $this->db->query($query);
    
        // Vincular los valores usando el método bind existente
        foreach($fields as $index => $field) {
            $field = str_replace(' ', '', $field); // Eliminar espacios del nombre del campo
            $this->db->bind(':' . $field, $values[$index]);
        }            
    
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return false;
        }

    }      

}