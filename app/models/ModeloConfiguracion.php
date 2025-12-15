<?php


class ModeloConfiguracion{

    private $db;


    public function __construct(){
        $this->db = new Base;
    }

    public function updateConfigurationCompany($datos)
    {
        $razonsocialpiensos = $datos['razonsocialpiensos'];
        $cifpiensos = $datos['cifpiensos'];
        $direccionpiensos = $datos['direccionpiensos'];
        $codigopostalpiensos = $datos['codigopostalpiensos'];
        $localidadpiensos = $datos['localidadpiensos'];
        $provinciapiensos = $datos['provinciapiensos'];
                                             
        $this->db->query("UPDATE configuracion SET razonsocialpiensos='$razonsocialpiensos', cifpiensos='$cifpiensos', direccionpiensos='$direccionpiensos', codigopostalpiensos='$codigopostalpiensos', localidadpiensos='$localidadpiensos', provinciapiensos='$provinciapiensos' ");

        if($this->db->execute()){
            return 1;
        } else {
            return 0;
        }
    }

    public function getDataConfiguration()
    {        
        $this->db->query("SELECT * FROM configuracion ");        
        $fila = $this->db->registro();
        return $fila;     
    }

    public function getBusinessName()
    {
        $this->db->query("SELECT razonsocialpiensos FROM configuracion ");        
        $fila = $this->db->registro();
        return $fila->razonsocialpiensos;
    }

    public function updateConfigurationDataEmailAccount($datos)
    {
        $remitente = $datos['remitente'];
        $correo = $datos['correo'];
        $passwordcorreo = $datos['passwordcorreo'];
        $host = $datos['host'];
        $puerto = $datos['puerto'];
        $protocolo = $datos['protocolo'];
                                             
        $this->db->query("UPDATE configuracion SET remitente='$remitente',correo='$correo',passwordcorreo='$passwordcorreo',host='$host',puerto='$puerto',protocolo='$protocolo' ");
        
        if($this->db->execute()){
            return 1;
        } else {
            return 0;
        }
    }

    public function updateConfigurationCarrier($datos)
    {        
        $idtransportista = $datos['idtransportista'];    
        $idprovfabrica = $datos['idprovfabrica'];
        $precioprovfab  = $datos['precioprovfab'];
        $idproductotransp  = $datos['idproductotransp'];
        $idproductofab  = $datos['idproductofab'];
                                             
        $this->db->query("UPDATE configuracion 
                        SET idtransportista='$idtransportista', idprovfabrica='$idprovfabrica', precioprovfab='$precioprovfab', 
                        idproductotransp ='$idproductotransp', idproductofab = '$idproductofab' ");
        
        if($this->db->execute()){
            return 1;
        } else {
            return 0;
        }
    }

    public function getIdTransportistaDefault()
    {
        $this->db->query("SELECT idtransportista FROM configuracion ");        
        $fila = $this->db->registro();
        $retorno = (isset($fila->idtransportista) && $fila->idtransportista > 0)? $fila->idtransportista: 0;
        return $retorno;
    }

    public function getPriceFactorySupplierDefault()
    {
        $this->db->query("SELECT precioprovfab FROM configuracion ");        
        $fila = $this->db->registro();
        $retorno = (isset($fila->precioprovfab) && $fila->precioprovfab > 0)? $fila->precioprovfab: 0;
        return $retorno;
    }

    public function getIdFactorySupplierDefault()
    {
        $this->db->query("SELECT idprovfabrica FROM configuracion ");        
        $fila = $this->db->registro();
        $retorno = (isset($fila->idprovfabrica) && $fila->idprovfabrica > 0)? $fila->idprovfabrica: 0;
        return $retorno;
    }

    public function getProductFactorySupplierIdDefault()
    {
        $this->db->query("SELECT idproductofab FROM configuracion ");        
        $fila = $this->db->registro();
        $retorno = (isset($fila->idproductofab) && $fila->idproductofab > 0)? $fila->idproductofab: 0;
        return $retorno;
    }

    public function getProductCarrierOrProductPlanningDefault()
    {
        $this->db->query("SELECT idproductotransp FROM configuracion ");        
        $fila = $this->db->registro();
        $retorno = (isset($fila->idproductotransp) && $fila->idproductotransp > 0)? $fila->idproductotransp: 0;
        return $retorno;
    }


    
}