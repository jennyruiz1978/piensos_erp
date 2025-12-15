<?php

require '../public/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

class generarPdf {

    public static function documentoPDF($orientacion,$formato,$idioma,$unicode,
            $codificacion,$margenes, $pdfa, $rutaCarpetasViews, $fichero) {

        ob_start();
        include_once '../app/views/' . $rutaCarpetasViews . '/' . $fichero;

        $html = ob_get_clean();
        $html2pdf = new Html2Pdf($orientacion,$formato,$idioma,$unicode,$codificacion,$margenes,$pdfa);
        $html2pdf->writeHTML($html);
        $html2pdf->output('ejemplo.pdf');
    }
    

    public static function documentoPDFParaEmail($orientacion,$formato,$idioma,$unicode,$codificacion,$margenes, $pdfa, $rutaCarpetasViews, $fichero, $datos) {
            
        ob_start();
        include_once '../app/views/' . $rutaCarpetasViews . '/' . $fichero;

        $html = ob_get_clean();    
        $html2pdf = new Html2Pdf($orientacion,$formato,$idioma,$unicode,$codificacion,$margenes,$pdfa);
        $html2pdf->writeHTML($html);        
        $attachment = $html2pdf->output('factura.pdf', 'S');
        return $attachment;
    }

    public static function documentoPDFParaEmailParaElBucleGoogle($orientacion, $formato, $idioma, $unicode, $codificacion, $margenes, $pdfa, $rutaCarpetasViews, $fichero, $datos) 
    {
        // Asegúrate de limpiar cualquier buffer previo
        if (ob_get_level()) {
            ob_end_clean();
        }

        ob_start();
        include '../app/views/' . $rutaCarpetasViews . '/' . $fichero;
        $html = ob_get_clean();

        $html2pdf = new Html2Pdf($orientacion, $formato, $idioma, $unicode, $codificacion, $margenes, $pdfa);
        $html2pdf->writeHTML($html);
        $attachment = $html2pdf->output('factura.pdf', 'S');
        return $attachment;
    }

    public static function documentoPDFExportar($orientacion,$formato,$idioma,$unicode,
    $codificacion,$margenes, $pdfa, $rutaCarpetasViews, $fichero, $datos) 
    {         
        ob_start();
        include_once '../app/views/' . $rutaCarpetasViews . '/' . $fichero;

        $html = ob_get_clean();
        $html2pdf = new Html2Pdf($orientacion,$formato,$idioma,$unicode,$codificacion,$margenes,$pdfa);     
        $html2pdf->writeHTML($html);    
        $html2pdf->output('documento.pdf');
    }


}