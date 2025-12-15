<?php


class ModeloAlbaranCliente{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }   

    public function obtenerAlbaranesClientesTabla($page,$order,$where,$limit, $havingSumCant){

        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT 
                        alb.id, alb.numero, alb.cliente, 
                        DATE_FORMAT(alb.fecha, '%d/%m/%Y') as fecha,  COALESCE(SUM(det.cantidad), 0) as sumacantidad, alb.estado                        
                        FROM clientes_albaranes alb
                        LEFT JOIN clientes_albaranes_det det on alb.id=det.idalbaran
                        $where                        
                        GROUP BY alb.id
                        $havingSumCant
                        $order LIMIT $pagina , $limit ");

       
                        

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerTotalAlbaranesCliente($where, $havingSumCant)
    {
        $this->db->query("SELECT count(*) as contador
                        FROM clientes_albaranes 
                        LEFT JOIN clientes_albaranes_det det on clientes_albaranes.id=det.idalbaran
                        $where                         
                        GROUP BY clientes_albaranes.id
                        $havingSumCant
                        ");

      
        

        $filas = $this->db->registros();
        return $filas;
    }    

    public function getAlbaranData($idAlbaran){
        $this->db->query("SELECT alb.*, (SELECT cli.nif FROM clientes cli WHERE alb.idcliente = cli.id ) as nif FROM clientes_albaranes alb WHERE alb.id = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    
    public function getAlbaranDataFacturaMasiva($idAlbaran){
        $this->db->query("SELECT alb.*, 
        (SELECT cli.nif 
        FROM clientes cli 
        WHERE alb.idcliente = cli.id ) as nif , 
    SUM(det.cantidad) AS sumacantidad, det.unidad
    FROM clientes_albaranes alb 
    LEFT JOIN clientes_albaranes_det det ON alb.id=det.idalbaran
    WHERE alb.id = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    public function getAlbaranDataDocumento($idAlbaran){
        $this->db->query("SELECT alb.*, 
                        cli.nombrefiscal, cli.nif, cli.direccion, cli.poblacion, cli.codigopostal, cli.provincia                         
                        FROM clientes_albaranes alb 
                        LEFT JOIN clientes cli ON alb.idcliente = cli.id 
                        WHERE alb.id = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas;
    }

    public function getNombreClienteByIdAlbaran($idAlbaran)
    {
        $this->db->query("SELECT idcliente FROM clientes_albaranes WHERE id = '$idAlbaran' ");                

        $fila = $this->db->registro();
        return $fila->idcliente;
    }

    public function getDeliveryNoteByIdInvoice($idFactura)
    {
        $this->db->query("SELECT * FROM clientes_albaranes WHERE idfactura = '$idFactura' ");                

        $filas = $this->db->registros();
        return $filas;
    }

    public function getDeliveryNotesSearch($query_search)
    {

        $this->db->query("SELECT * FROM clientes_albaranes WHERE $query_search ");

        $filas = $this->db->registros();
        return $filas;

    }

    public function getDeliveryNotesSearchModified($query_search)
    {

        $this->db->query("SELECT alb.*, 
                        SUM(det.cantidad) AS sumacantidad, det.unidad, fac.vencimiento
                        FROM clientes_albaranes alb 
                        LEFT JOIN clientes_albaranes_det det ON alb.id=det.idalbaran
                        LEFT JOIN clientes_facturas fac ON alb.idfactura=fac.id
                        WHERE $query_search 
                        GROUP BY alb.id ");

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
                        FROM clientes_albaranes_det det WHERE det.idalbaran IN ( $strIdesAlbaran ) 
                        $groupby
                        ");

        if($groupby != ''){
            $filas = $this->db->registros();
        }else{
            $filas = $this->db->registro();
        }        
        return $filas;

    }

    public function getRowsDeliveryNotesSelected($strIdesAlbaran)
    {

        $this->db->query("SELECT det.id as idfilaalbaran, det.idalbaran,
                        det.idproducto, det.descripcion, det.unidad, det.ivatipo,
                        det.cantidad AS suma_cantidad, 
                        det.subtotal AS suma_subtotal, 
                        (det.subtotal * det.ivatipo/100) AS importe_iva, 
                        (det.subtotal + (det.subtotal * det.ivatipo/100)) AS suma_total        
                        FROM clientes_albaranes_det det WHERE det.idalbaran IN ( $strIdesAlbaran )                         
                        ");
        
        $filas = $this->db->registros();              
        return $filas;

    }
        
    public function getClientById($id){
        $this->db->query("SELECT clientes.*, zonas.margen FROM clientes 
        LEFT JOIN zonas ON clientes.idzona = zonas.id 
        WHERE clientes.id = '$id' ");        
        $fila = $this->db->registro();
        return $fila;
    }

    public function getClientFromStringDeliveryNotesToInvoices($strIdesAlbaran, $idCliente)
    {

        $this->db->query("SELECT cli.numero, 
                        if(cli.idcliente = '$idCliente',1,0) AS verificador 
                        FROM clientes_albaranes cli 
                        WHERE cli.id IN ( $strIdesAlbaran )
                        HAVING verificador = 0 ");

        $filas = $this->db->registros();             
        return $filas;

    }

    public function getAlbaranStatus($idAlbaran){
        $this->db->query("SELECT estado FROM clientes_albaranes alb WHERE alb.id = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas->estado;
    }

    public function getAlbaranNumber($idAlbaran){
        $this->db->query("SELECT numero FROM clientes_albaranes alb WHERE alb.id = '$idAlbaran' ");                

        $filas = $this->db->registro();
        return $filas->numero;
    }

    
    public function obtenerAlbaranesClientesExportarAntes($order,$where){                   

        $this->db->query("SELECT id, numero, cliente, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha,  COALESCE(SUM(cantidad), 0) as total, estado
                        FROM clientes_albaranes       
                        $where $order ");                        

        $filas = $this->db->registros();

        return $filas;
    }

    public function obtenerAlbaranesClientesExportar($order, $where)
{
    $this->db->query("SELECT 
                        alb.id,
                        alb.numero,
                        alb.cliente,
                        DATE_FORMAT(alb.fecha, '%d/%m/%Y') as fecha,
                        COALESCE(SUM(det.cantidad), 0) as sumacantidad,
                        alb.estado
                    FROM clientes_albaranes alb
                    LEFT JOIN clientes_albaranes_det det ON alb.id = det.idalbaran
                    $where
                    GROUP BY alb.id
                    $order");

    $filas = $this->db->registros();
    return $filas;
}


    public function getInvoiceNumberByDeliveryNoteById($idAlbaran)
    {
        $this->db->query("SELECT idfactura FROM clientes_albaranes WHERE id = '$idAlbaran' ");                

        $fila = $this->db->registro();
        return (isset($fila->idfactura) && $fila->idfactura > 0)? $fila->idfactura: 0;
    }

    public function deleteDeliveryNotice($id){
        
        $this->db->query("DELETE FROM clientes_albaranes WHERE id = '$id' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }    

    public function updateDeliveryNoticeHead($datos){
        
        $baseimponible = $datos['baseimponible'];
        $ivatotal = $datos['ivatotal'];
        $total = $datos['total'];
        $id = $datos['id'];

        $this->db->query("UPDATE clientes_albaranes 
                        SET baseimponible = '$baseimponible', ivatotal= $ivatotal, total = '$total'
                        WHERE id = '$id' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }    

    public function deleteDeliveryNotes($idAlbaran){

        $this->db->query("DELETE FROM clientes_albaranes WHERE id = '$idAlbaran' ");
               
        if($this->db->execute()){
            return true;
        }else {
            return false;
        }
    }      
 
   

}