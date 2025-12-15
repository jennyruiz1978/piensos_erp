<?php


class ModeloPlanificacion{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }


    public function obtenerPlanificaciones(){
        $this->db->query('SELECT * FROM planificaciones');

        $resultado = $this->db->registros();

        return $resultado;
    }

    public function obtenerPlanificacionesTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT id, semana,IF(fechainicio is null,'',DATE_FORMAT(fechainicio, '%d/%m/%Y')) as fechainicio, 
                        IF(fechafin is null,'',DATE_FORMAT(fechafin, '%d/%m/%Y')) as fechafin, total
                        FROM planificaciones 
                        $where $order LIMIT $pagina , $limit ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalPlanificaciones($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM planificaciones                      
                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }

    public function getNextPlanningCode(){
        $this->db->query("SELECT MAX(codigo) AS maximo FROM planificaciones");
        $filas = $this->db->registro();
        $maximo = 1;
        if(isset($filas->maximo) && $filas->maximo > 0){
            $maximo = $filas->maximo + 1;
        }
        return $maximo;
    }

    public function addPlanning($arr){

        $codigo = $arr['codigo'];        
        $status = $arr['status'];
        $idproducto = $arr['idproducto'];
        $nombreproducto = $arr['nombreproducto'];

        $this->db->query("INSERT INTO planificaciones (codigo,status,idproducto,nombreproducto) 
                        VALUES ('$codigo','$status', '$idproducto', '$nombreproducto')");
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        } else {
            return 0;
        }

    }    

    public function getPlanningById($id){
        $this->db->query("SELECT * FROM planificaciones WHERE id = '$id' ");        
        $fila = $this->db->registro();
        return $fila;
    }

    public function updateStartEndDates($fechaInicio, $fechaFin, $id){
        
        $this->db->query("UPDATE  planificaciones SET fechainicio = '$fechaInicio', fechafin = '$fechaFin' WHERE  id = $id ");
        
        if($this->db->execute()){
            return 1;
        } else {
            return 0;
        }

    }

    public function updateTotalPlanning($id, $total){

        $this->db->query("UPDATE planificaciones SET total = '$total'
                        WHERE id = $id ");
        
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function updatePricePlanning($id, $precio)
    {
        $this->db->query("UPDATE planificaciones SET precio = '$precio' WHERE id = $id ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function deletePlanningById($id){
        
        $this->db->query("DELETE FROM planificaciones WHERE id = '$id' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function updateFieldTabla($id, $field, $value){
        $this->db->query("UPDATE planificaciones SET $field = '$value' WHERE id = $id ");

        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }

    public function getPricePlanning($idplanificacion)
    {
        $this->db->query("SELECT precio FROM planificaciones 
                        WHERE id = '$idplanificacion' ");

        $fila = $this->db->registro();
        return $fila->precio;
    }

    public function getProductPurchasePriceByPlanningDate($idProducto,$fecha){
        $this->db->query("SELECT FORMAT(precio,2,'es_ES') AS precio  FROM planificaciones_fechas pf 
                        LEFT JOIN planificaciones pla ON pf.idplanificacion=pla.id
                        WHERE pla.idproducto = '$idProducto' AND fecha = '$fecha' ");

        $filas = $this->db->registro();
        return (isset($filas->precio) && $filas->precio != '')? $filas->precio: 0;
    }

    public function getProductPurchasePriceByPlanningDateNotFormat($idProducto,$fecha){
        $this->db->query("SELECT precio  FROM planificaciones_fechas pf 
                        LEFT JOIN planificaciones pla ON pf.idplanificacion=pla.id
                        WHERE pla.idproducto = '$idProducto' AND fecha = '$fecha' ");

        $filas = $this->db->registro();
        return (isset($filas->precio) && $filas->precio != '')? $filas->precio: 0;
    }

    
    public function obtenerPlanificacionesExportar($order,$where){       
                
        $this->db->query("SELECT id, semana,IF(fechainicio is null,'',DATE_FORMAT(fechainicio, '%d/%m/%Y')) as fechainicio, 
                        IF(fechafin is null,'',DATE_FORMAT(fechafin, '%d/%m/%Y')) as fechafin, total
                        FROM planificaciones 
                        $where $order ");

        $filas = $this->db->registros();

        return $filas;
    }

    public function getStartDate($idPlanificacion)
    {
        $this->db->query("SELECT fechainicio FROM planificaciones WHERE id = '$idPlanificacion' ");
        $fila = $this->db->registro();
        return $fila->fechainicio;
    }

        
    public function searchDeliveryNoticesSuppliersWithIdPlanningDate($idrecojo){       
                
        $this->db->query("SELECT plan.recojo,
                        (SELECT pro.numero FROM proveedores_albaranes pro WHERE pro.id=det.idalbaran) AS numalbaran,
                        det.idalbaran
                        FROM proveedores_albaranes_det det 
                        LEFT JOIN planificaciones_fechas plan ON det.idplanfecha=plan.id OR det.idplanfechafab=plan.id
                        WHERE plan.recojo = '$idrecojo' ");

        $filas = $this->db->registros();
        return $filas;
    }

    public function searchDeliveryNoticesClientsWithIdPlanningDate($idrecojo){       
                
        $this->db->query("SELECT plan.recojo,
                        (SELECT pro.numero FROM clientes_albaranes pro WHERE pro.id=det.idalbaran) AS numalbaran,
                        det.idalbaran
                        FROM clientes_albaranes_det det 
                        LEFT JOIN planificaciones_fechas plan ON det.idplanfecha=plan.id
                        WHERE plan.recojo = '$idrecojo' ");

        $filas = $this->db->registros();
        return $filas;
    }
        
    public function searchDeliveryNoticesSuppliersWithIdPlanning($idPlanning){       
                
        $this->db->query("SELECT plan.recojo,
                        (SELECT pro.numero FROM proveedores_albaranes pro WHERE pro.id=det.idalbaran) AS numalbaran,
                        det.idalbaran
                        FROM proveedores_albaranes_det det 
                        LEFT JOIN planificaciones_fechas plan ON det.idplanfecha=plan.id OR det.idplanfechafab=plan.id
                        WHERE plan.idplanificacion = '$idPlanning' ");

        $filas = $this->db->registros();
        return $filas;
    }    
    
    public function searchDeliveryNoticesClientssWithIdPlanning($idPlanning){       
        $this->db->query("SELECT plan.recojo,
                        (SELECT pro.numero FROM clientes_albaranes pro WHERE pro.id=det.idalbaran) AS numalbaran,
                        det.idalbaran
                        FROM clientes_albaranes_det det 
                        LEFT JOIN planificaciones_fechas plan ON det.idplanfecha=plan.id
                        WHERE plan.idplanificacion = '$idPlanning' ");

        $filas = $this->db->registros();
        return $filas;
    }    

    public function getWeekPlanningById($id){
        $this->db->query("SELECT semana FROM planificaciones WHERE id = '$id' ");        
        $fila = $this->db->registro();
        $semana = '';
        if(isset($fila->semana) && $fila->semana != ''){
            $semana = $fila->semana;
        }
        return $semana;
    }

}