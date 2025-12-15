<?php

require_once __DIR__ . '/GoogleDriveClient.php';

use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile;

class GoogleDriveUploader
{
    /**
     * Sube un archivo a una carpeta organizada por año y tipo
     */
    
    private static $carpetaDrive = '1NJW0z_sFm40dnXgCPWtJUzR7O9l-cBQM';
    
    public static function subirArchivo($idDoc, $fechaDoc, $tipo, $nombreArchivo)    
    {
        $emailDoc = new EmailsDocumentos();

        $metodoPorTipo = self::obtenerMetodoPorTipoDocumento($tipo);        

        //$contenidoPDF = $emailDoc->generarPdfFacturaParaEmail($idDoc);
        $contenidoPDF = $emailDoc->$metodoPorTipo($idDoc);

        $anio = date('Y', strtotime($fechaDoc));

        $client = GoogleDriveClient::obtenerCliente();
        $service = new Google_Service_Drive($client);                

        $carpetaAnioID = self::obtenerOCrearCarpeta($service, $anio, self::$carpetaDrive);
        $carpetaTipoID = self::obtenerOCrearCarpeta($service, $tipo, $carpetaAnioID);

        $archivo = new DriveFile([
            'name' => $nombreArchivo,
            'parents' => [$carpetaTipoID]
        ]);

        $service->files->create($archivo, [
            'data' => $contenidoPDF,
            'mimeType' => 'application/pdf',
            'uploadType' => 'multipart'
        ]);
    }

    /**
     * Busca o crea una carpeta con nombre $nombre en la carpeta padre $parentId
     */
    private static function obtenerOCrearCarpeta($service, $nombre, $parentId = null)
    {
        $q = "mimeType='application/vnd.google-apps.folder' and name='{$nombre}' and trashed = false";
        if ($parentId) {
            $q .= " and '{$parentId}' in parents";
        }

        $result = $service->files->listFiles([
            'q' => $q,
            'fields' => 'files(id, name)',
        ]);

        if (count($result->getFiles()) > 0) {
            return $result->getFiles()[0]->getId();
        }

        $carpeta = new DriveFile([
            'name' => $nombre,
            'mimeType' => 'application/vnd.google-apps.folder'
        ]);

        if ($parentId) {
            $carpeta->setParents([$parentId]);
        }

        $carpetaCreada = $service->files->create($carpeta, [
            'fields' => 'id'
        ]);

        return $carpetaCreada->getId();
    }
    
    public static function reemplazarArchivoConUnaSolaFecha($idDoc, $fechaDoc, $tipo, $nombreArchivo)
    {     
        $emailDoc = new EmailsDocumentos();
        //$contenidoPDF = $emailDoc->generarPdfFacturaParaEmail($idDoc);
        $metodoPorTipo = self::obtenerMetodoPorTipoDocumento($tipo);                
        $contenidoPDF = $emailDoc->$metodoPorTipo($idDoc);

        $anio = date('Y', strtotime($fechaDoc));
        
        $client = GoogleDriveClient::obtenerCliente();
        $service = new Google_Service_Drive($client);

        $carpetaAnioID = self::obtenerOCrearCarpeta($service, $anio, self::$carpetaDrive);
        $carpetaTipoID = self::obtenerOCrearCarpeta($service, $tipo, $carpetaAnioID);

        // Paso 1: Buscar archivo existente con el mismo nombre
        $query = "name='{$nombreArchivo}' and '{$carpetaTipoID}' in parents and trashed = false";
        $result = $service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)'
        ]);

        if (count($result->getFiles()) > 0) {
            // Paso 2: Eliminar archivo existente
            $fileId = $result->getFiles()[0]->getId();
            $service->files->delete($fileId);
        }

        // Paso 3: Subir nuevo archivo        
        $archivo = new DriveFile([
            'name' => $nombreArchivo,
            'parents' => [$carpetaTipoID]
        ]);

        $service->files->create($archivo, [
            'data' => $contenidoPDF,
            'mimeType' => 'application/pdf',
            'uploadType' => 'multipart'
        ]);

    }
    
    public static function reemplazarArchivo($idDoc, $fechaDocNueva, $tipo, $nombreArchivo, $fechaDocAnterior)
    {     

        $emailDoc = new EmailsDocumentos();
        $metodoPorTipo = self::obtenerMetodoPorTipoDocumento($tipo);                
        $contenidoPDF = $emailDoc->$metodoPorTipo($idDoc);

        $anioAnterior = date('Y', strtotime($fechaDocAnterior));
        $anioNuevo = date('Y', strtotime($fechaDocNueva));

        $client = GoogleDriveClient::obtenerCliente();
        $service = new Google_Service_Drive($client);


        // Buscar y eliminar el archivo anterior (si existe)
        $carpetaAnioAnt = self::obtenerOCrearCarpeta($service, $anioAnterior, self::$carpetaDrive);
        $carpetaTipoAnt = self::obtenerOCrearCarpeta($service, $tipo, $carpetaAnioAnt);

        $query = "name='{$nombreArchivo}' and '{$carpetaTipoAnt}' in parents and trashed = false";
        $result = $service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)'
        ]);

        if (count($result->getFiles()) > 0) {
            $fileId = $result->getFiles()[0]->getId();
            $service->files->delete($fileId);
        }

        // Subir el nuevo archivo en la carpeta del año nuevo
        $carpetaAnioNueva = self::obtenerOCrearCarpeta($service, $anioNuevo, self::$carpetaDrive);
        $carpetaTipoNueva = self::obtenerOCrearCarpeta($service, $tipo, $carpetaAnioNueva);

        $archivo = new DriveFile([
            'name' => $nombreArchivo,
            'parents' => [$carpetaTipoNueva]
        ]);

        $service->files->create($archivo, [
            'data' => $contenidoPDF,
            'mimeType' => 'application/pdf',
            'uploadType' => 'multipart'
        ]);
    }



    public static function eliminarArchivo($fechaDoc, $tipo, $nombreArchivo)
    {
        $anio = date('Y', strtotime($fechaDoc));
        
        $client = GoogleDriveClient::obtenerCliente();
        $service = new Google_Service_Drive($client);
                
        $carpetaAnioID = self::obtenerOCrearCarpeta($service, $anio, self::$carpetaDrive);
        $carpetaTipoID = self::obtenerOCrearCarpeta($service, $tipo, $carpetaAnioID);

        // Buscar archivo existente con el mismo nombre
        $query = "name='{$nombreArchivo}' and '{$carpetaTipoID}' in parents and trashed = false";
        $result = $service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)'
        ]);

        if (count($result->getFiles()) > 0) {
            $fileId = $result->getFiles()[0]->getId();
            $service->files->delete($fileId);
        }
    }

    private static function obtenerMetodoPorTipoDocumento($tipo)
    {
        switch ($tipo) {
            case 'Facturas Cliente':
                $metodo = 'generarPdfFacturaParaEmail';
                break;

            case 'Albaranes Cliente':
                $metodo = 'generarPdfAlbaranParaEmail';
                break;     
                
            case 'Recibos Cliente':
                $metodo = 'generarPdfReciboParaEmail';
                break;                  
            
            default:
                # code...
                break;
        }

        return $metodo;
    }


}
