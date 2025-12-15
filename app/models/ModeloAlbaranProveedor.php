<?php


class ModeloAlbaranProveedor{

    private $db;

    

    public function __construct(){
        $this->db = new Base;
    }   

    public function obtenerAlbaranesProveedoresTabla($page,$order,$where,$limit){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT alb.id, alb.numero, alb.proveedor, DATE_FORMAT(alb.fecha, '%d/%m/%Y') as fecha, alb.total, alb.estado,
                        IF(alb.idfactura > 0,fac.numero,'') as numerofactura,
                        IF(planif_cliente.nom_cli IS NOT NULL,planif_cliente.nom_cli,'') nom_cli
                        FROM proveedores_albaranes alb
                        LEFT JOIN proveedores_facturas fac ON alb.idfactura = fac.id

                        LEFT JOIN (
								
                                SELECT pad.idalbaran AS idalbaran_det, pad.idplanfecha AS idplanfecha_pad, cli.nombrefiscal AS nom_cli                                    
                                FROM proveedores_albaranes_det pad 
                                LEFT JOIN planificaciones_fechas pf ON pad.idplanfecha=pf.id
                                LEFT JOIN clientes cli ON pf.idcliente = cli.id
                                WHERE pad.idplanfecha > 0 AND pf.idcliente > 0
                                GROUP BY pad.idalbaran                                    
                            
                            ) planif_cliente ON planif_cliente.idalbaran_det = alb.id

                        $where $order LIMIT $pagina , $limit ");
        
        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalAlbaranesProveedor($where)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM proveedores_albaranes alb      
                        LEFT JOIN proveedores_facturas fac ON alb.idfactura = fac.id      


                        LEFT JOIN (
								
                                SELECT pad.idalbaran AS idalbaran_det, pad.idplanfecha AS idplanfecha_pad, cli.nombrefiscal AS nom_cli                                    
                                FROM proveedores_albaranes_det pad 
                                LEFT JOIN planificaciones_fechas pf ON pad.idplanfecha=pf.id
                                LEFT JOIN clientes cli ON pf.idcliente = cli.id
                                WHERE pad.idplanfecha > 0 AND pf.idcliente > 0
                                GROUP BY pad.idalbaran                                    
                            
                            ) planif_cliente ON planif_cliente.idalbaran_det = alb.id

                        $where
                        ");

        $filas = $this->db->registro();
        return $filas->contador;
    }

    public function getAlbaranData($idAlbaran){
        $this->db->query("SELECT alb.*, (SELECT pro.nif FROM proveedores pro WHERE alb.idproveedor = pro.id ) as nif FROM proveedores_albaranes alb WHERE alb.id = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    public function getNombreProveedorByIdAlbaran($idAlbaran)
    {
        $this->db->query("SELECT idproveedor FROM proveedores_albaranes WHERE id = '$idAlbaran' ");                

        $fila = $this->db->registro();
        return $fila->idproveedor;
    }

    public function getDeliveryNoteByIdInvoice($idFactura)
    {
        $this->db->query("SELECT * FROM proveedores_albaranes WHERE idfactura = '$idFactura' ");                

        $filas = $this->db->registros();
        return $filas;
    }

    public function getDeliveryNotesSearch($query_search)
    {

        $this->db->query("SELECT * FROM proveedores_albaranes WHERE $query_search ");

        $filas = $this->db->registros();
        return $filas;

    }

    public function getTotalsInvoices($strIdesAlbaran, $groupby)
    {

        $this->db->query("SELECT 
                        det.descripcion, det.unidad, det.ivatipo,
                        SUM(det.cantidad) AS suma_cantidad, 
                        SUM(det.subtotal) AS suma_subtotal, 
                        SUM((det.subtotal * det.ivatipo/100)) AS importe_iva, 
                        SUM((det.subtotal + (det.subtotal * det.ivatipo/100))) AS suma_total        
                        FROM proveedores_albaranes_det det WHERE det.idalbaran IN ( $strIdesAlbaran ) 
                        $groupby
                        ");

        if($groupby != ''){
            $filas = $this->db->registros();
        }else{
            $filas = $this->db->registro();
        }              
        return $filas;

    }



    public function getDeliveryNoteRowsToCreateInvoice($strIdesAlbaran)
    {

        $this->db->query("SELECT det.idproducto, det.descripcion, det.unidad, det.ivatipo, 
                        det.cantidad, det.subtotal,                         
                        (det.subtotal * det.ivatipo/100) AS importe_iva, 
                        (det.subtotal + (det.subtotal * det.ivatipo/100)) AS suma_total,
                        det.id, det.idalbaran 
                        FROM proveedores_albaranes_det det                         
                        WHERE det.idalbaran IN ( $strIdesAlbaran ) ");
        
        $filas = $this->db->registros();                      
        return $filas;

    }

    public function getAlbaranStatus($idAlbaran){
        $this->db->query("SELECT estado FROM proveedores_albaranes alb WHERE alb.id = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas->estado;
    }

    public function obtenerAlbaranesProveedores($order,$where){       
                
        $this->db->query("SELECT alb.id, alb.numero, alb.proveedor, DATE_FORMAT(alb.fecha, '%d/%m/%Y') as fecha, alb.total, alb.estado, 
                        IF(alb.idfactura > 0,fac.numero,'') as numerofactura,                        
                        IF(planif_cliente.nom_cli IS NOT NULL,planif_cliente.nom_cli,'') nom_cli
                        FROM proveedores_albaranes alb        
                        LEFT JOIN proveedores_facturas fac ON alb.idfactura = fac.id
                        LEFT JOIN (
								
                                SELECT pad.idalbaran AS idalbaran_det, pad.idplanfecha AS idplanfecha_pad, cli.nombrefiscal AS nom_cli                                    
                                FROM proveedores_albaranes_det pad 
                                LEFT JOIN planificaciones_fechas pf ON pad.idplanfecha=pf.id
                                LEFT JOIN clientes cli ON pf.idcliente = cli.id
                                WHERE pad.idplanfecha > 0 AND pf.idcliente > 0
                                GROUP BY pad.idalbaran                                    
                            
                            ) planif_cliente ON planif_cliente.idalbaran_det = alb.id
                            
                        $where $order ");

        $filas = $this->db->registros();

        return $filas;
    }


    public function buscarProveedorEnAlbaranes($strIdesAlbaran, $idproveedor)
    {
        $this->db->query("SELECT COUNT(*) AS contador FROM proveedores_albaranes WHERE id IN (".$strIdesAlbaran.") AND idproveedor = '$idproveedor' ");

        $fila = $this->db->registro();

        return $fila->contador;
    }

    public function getInvoiceNumberByDeliveryNoteById($idAlbaran)
    {
        $this->db->query("SELECT idfactura FROM proveedores_albaranes WHERE id = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas->idfactura;
    }

    
    public function getInvoiceNumberByDeliveryNoteByPlanningDateId($idPlanFecha)
    {
        $this->db->query("SELECT idfactura FROM proveedores_albaranes WHERE idplanfecha = '$idPlanFecha' ");                

        $fila = $this->db->registro();
        $idFactura = 0;
        if(isset($fila->idfactura) && $fila->idfactura != '' && $fila->idfactura >0){
            $idFactura = $fila->idfactura;
        }
        return $idFactura;
    }

    public function deleteDeliveryNotice($id){
        
        $this->db->query("DELETE FROM proveedores_albaranes WHERE id = '$id' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }    

    public function deleteDeliveryNotes($idAlbaran){

        $this->db->query("DELETE FROM proveedores_albaranes WHERE id = '$idAlbaran' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }      

     ///////////////=============================///////////////
    // métodos para corregir datos en base de datos
    ///////////////=============================///////////////

    public function testObtenerTodosLosAlbaranesProveedores(){       
                
        $this->db->query("SELECT * FROM proveedores_albaranes alb ");

        $filas = $this->db->registros();

        return $filas;
    }


  
}