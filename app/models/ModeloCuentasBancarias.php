<?php


class ModeloCuentasBancarias{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function obtenerCuentasBancariasTabla($page,$order,$where,$limit)
    {        
        if ($page == 1) {            
            $pagina = (intval($page) - 1);
        }else{
            $pagina = $limit * (intval($page) - 1);
        }   

        $this->db->query("SELECT * FROM cuentas_bancarias $where $order LIMIT $pagina , $limit ");
        $filas = $this->db->registros();
        return $filas;
    }

    public function obtenerTotalCuentasBancarias($where)
    {
        $this->db->query("SELECT count(*) as contador FROM cuentas_bancarias $where ");
        $filas = $this->db->registro();
        return $filas->contador;
    }

    public function getBankAccounts()
    {        
        $this->db->query("SELECT * FROM cuentas_bancarias");
        $filas = $this->db->registros();
        return $filas;
    }

    public function getBankAccount($idCuenta)
    {
        $this->db->query("SELECT * FROM cuentas_bancarias WHERE id= '$idCuenta' ");        
        $fila = $this->db->registro();
        return $fila;
    }

    public function getBankAccountFromDeliveryNoticesClients($idCuenta)
    {
        $this->db->query("SELECT COUNT(*) AS contador FROM clientes_facturas WHERE idcuentabancaria= '$idCuenta' ");        
        $fila = $this->db->registro();
        return $fila->contador;
    }

    public function deleteAccountBankById($id)
    {
        $this->db->query("DELETE FROM cuentas_bancarias WHERE id = '$id' ");

        if($this->db->execute()){
            return 1;
        }else {
            return 0;
        }
    }
    
}