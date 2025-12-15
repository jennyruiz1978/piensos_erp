<?php

class EmailsDocumentos extends Controlador {

   
    private $arrFields;
    private $tabla;
    private $fetch;

    public function __construct() {
        
        //session_start();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->modeloAlbaranCliente = $this->modelo('ModeloAlbaranCliente');
        $this->modeloAlbaranDetalleCliente = $this->modelo('ModeloAlbaranDetalleCliente');                
        $this->modeloFacturaCliente = $this->modelo('ModeloFacturaCliente');
        $this->modeloFacturaDetalleCliente = $this->modelo('ModeloFacturaDetalleCliente');
        $this->modeloConfiguracion = $this->modelo('ModeloConfiguracion');
        $this->modeloReciboCliente = $this->modelo('ModeloReciboCliente');
        $this->modeloBase = $this->modelo('ModeloBase'); 
        $this->arrFieldsEmailSent = ['iddoc','fecha','tipodoc','nomfichero','destinatarios','asunto','mensaje','correoremitente','nomremitente']; 

        if(file_get_contents("php://input")){
            $payload = file_get_contents("php://input");    
            $this->fetch = json_decode($payload, true);
        } 
    }  
       
    public function enviarEmailDocumento()
    {
        
        $respuesta['error'] = true;
        $respuesta['mensaje'] = "S'ha produït un error i no s'ha enviat el document";

        if(isset($_POST['idAlbaranClienteEmail']) && $_POST['idAlbaranClienteEmail'] > 0 && $_POST['tipoDocumento']!= ''){

                if(trim($_POST['asunto']) == '' || trim($_POST['mensaje']) == ''){
                    $respuesta['mensaje'] = "Heu d'afegir l'assumpte i el missatge";
                }else if(!isset($_POST['emailEnviar'])){
                    $respuesta['mensaje'] = "Heu d'afegir almenys un destinatari";
                }else{
                    
                    $enviar = $this->enviarEmailDocumentoPdf($_POST);
                    if($enviar){
                        $respuesta['error'] = false;
                        $respuesta['mensaje'] = 'Correu enviat';                
                    }
                }            

        }else{
            $respuesta['error'] = true;
            $respuesta['mensaje'] = "Les dades del document no són correctes";
        }

        echo json_encode($respuesta);

    }

    private function enviarEmailDocumentoPdf($post){

      
        $retorno = 0;         
        $nombreFirma = 'JORDI TARRÉ';

        $datosCorreo = $this->modeloConfiguracion->getDataConfiguration();                
        $nombreEmpresa = $datosCorreo->remitente;        
        $movil = '';        

        $idDocumento = $post['idAlbaranClienteEmail'];        
        
        if(isset($datosCorreo) && isset($datosCorreo->correo) && $datosCorreo->correo != ''){

            $asunto = $post['asunto'];
            $emailsDestino = $post['emailEnviar'];

            $plantilla = file_get_contents(DIR_PLANTILLAS."envioDocumento.php");                               
            $contenido = $post['mensaje'];

            $cambiar = ['{CONTENIDO}','{NOMBRE}','{NOMBREEMPRESA}','{MOVIL}'];
            $cambio = [$contenido, $nombreFirma, $nombreEmpresa, $movil];
            $mensaje = str_replace($cambiar,$cambio,$plantilla);                    
            $message = html_entity_decode($mensaje);            
            
            switch ($post['tipoDocumento']) {
                
                case 'albaran':
                    $attachment = $this->generarPdfAlbaranParaEmail($idDocumento);
                    $nombreFichero = "Albaran_".$this->modeloAlbaranCliente->getAlbaranNumber($idDocumento)."_".strtotime("now").".pdf";
                    break;

                case 'factura':
                    $attachment = $this->generarPdfFacturaParaEmail($idDocumento);
                    $nombreFichero = "Factura_".$this->modeloFacturaCliente->getInvoiceNumberByIdFactura($idDocumento)."_".strtotime("now").".pdf";
                    break;   
                    

                case 'recibo':
                    $attachment = $this->generarPdfReciboParaEmail($idDocumento);
                    $nombreFichero = "Recibo_".$this->modeloReciboCliente->getReceiptNumberByIdReceipt($idDocumento)."_".strtotime("now").".pdf";
                    break;                        
                
                default:
                    # code...
                    break;
            }          

            $tmp['documento'] = $attachment;
            $tmp['nombreDocumento'] = $nombreFichero;
    
            $attachmentArray[] = $tmp;   

            $envio = enviarEmail::enviarEmailConDocumentos($emailsDestino, $asunto, $message, $attachmentArray, $datosCorreo);            
            
            if ($envio) {                    
                $retorno = 1;                
                $this->guardarDatosEnvioFactura($idDocumento, $post['tipoDocumento'], $nombreFichero, $emailsDestino, $asunto, $contenido, $datosCorreo);                
            }   
        }       
        return $retorno;     

    }

    private function guardarDatosEnvioFactura($idDocumento, $tipoDocumento, $nombreFichero, $emailsDestino, $asunto, $message, $datosCorreo)
    {
       
        $arrValues['iddoc'] = $idDocumento;        
        $arrValues['fecha'] = date("Y-m-d H:i:s");
        $arrValues['tipodoc'] = $tipoDocumento;
        $arrValues['nomfichero'] = $nombreFichero;
        $arrValues['destinatarios'] = json_encode($emailsDestino);
        $arrValues['asunto'] = $asunto;
        $arrValues['mensaje'] = $message;
        $arrValues['correoremitente'] = $datosCorreo->correo;
        $arrValues['nomremitente'] = $datosCorreo->remitente;        

        $stringQueries = UtilsHelper::buildStringsInsertQuery($arrValues, $this->arrFieldsEmailSent);
            $ok = $stringQueries['ok'];
            $strFields = $stringQueries['strFields'];
            $strValues = $stringQueries['strValues'];
                       
            if($ok){
                $this->modeloBase->insertRowEmailSent('emails_clientes_facturas', $strFields, $strValues);
            }    
    }

    public function generarPdfAlbaranParaEmail($idAlbarab)
    {                       
        $datos['cabecera'] = $this->modeloAlbaranCliente->getAlbaranDataDocumento($idAlbarab);
        $datos['detalle'] = $this->modeloAlbaranDetalleCliente->getRowsAlbaran($idAlbarab);        
        $datos['tipo'] = 'albarán';
        $datos['razonsocialpiensos'] = $this->modeloConfiguracion->getBusinessName();

        $exp = generarPdf::documentoPDFParaEmail('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0), true, 'documentos', 'albaran.php', $datos);
        return $exp;
    }

    public function generarPdfFacturaParaEmail($idFactura)
    {                        
        $cabecera = $this->modeloFacturaCliente->getInvoiceDataDocument($idFactura);       
        $datos['cabecera'] = $cabecera;
        $datos['detalle'] = $this->modeloFacturaDetalleCliente->getRowsInvoiceWithRowsDatesNoticesDelivery($idFactura);        
        $datos['razonsocialpiensos'] = $this->modeloConfiguracion->getBusinessName();
        $datos['tipo'] = 'factura';        
        $datos['tiporazonsocial'] = 'cliente';

        $rectificativa = 0;
        if(isset($cabecera->idfacturaorigen) && $cabecera->idfacturaorigen > 0){
            $rectificativa = $cabecera->idfacturaorigen;
            $datos['numFacturaOrigen'] = $this->modeloFacturaCliente->getInvoiceNumberByIdFactura($cabecera->idfacturaorigen);
        }
        $datos['rectificativa'] = $rectificativa;
                
        $exp = generarPdf::documentoPDFParaEmail('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0), true, 'documentos', 'factura.php', $datos);    
        return $exp;   
    }

    public function generarPdfReciboParaEmail($idRecibo)
    {
        $datos_recibo = $this->modeloReciboCliente->getRecepitDataDocument($idRecibo);
        $datos['cabecera'] = $datos_recibo;
        $datos['tipo'] = 'recibo';
        $datos['importe_letras'] = UtilsHelper::numberToWords($datos_recibo->importe);
        $datos['razonsocialpiensos'] = $this->modeloConfiguracion->getBusinessName();
        
        $exp = generarPdf::documentoPDFParaEmail('P', 'A4', 'es', true, 'UTF-8', array(0, 0, 0, 0), true, 'documentos', 'recibo.php', $datos);    
        return $exp;

    }

    
}
