<?php

class DateTimeHelper {
        
    public static function buscarDiasEntreFechaInicioYFin($fechaInicio,$fechaFin)
    {    
        $fechaInicio=strtotime($fechaInicio);
        $fechaFin=strtotime($fechaFin);               

        $arrFechas = [];        
        for($i=$fechaInicio; $i <= $fechaFin ; $i+=86400){            
            //Sacar el dia de la semana con el modificador N de la funcion date            
            $dia = date('N', $i);  
            $diaTxt = self::diaTextoCorto($dia);                      
            $tmp['fecha'] = date("Y-m-d", $i);
            $tmp['dia'] = $diaTxt;
            $arrFechas[] = $tmp;            
        }       
        return $arrFechas;
    }

    public static function calcularFechaFin($fechaInicio, $numDias){       
                
        if(!isset($fechaInicio) || $fechaInicio==''){
            $fechaInicio = date('Y-m-d');
        }
        if(!isset($numDias) || $numDias==''){
            $numDias = 0;
        }

        $fecha = date_create($fechaInicio);
        date_add($fecha, date_interval_create_from_date_string(" $numDias days"));
        return date_format($fecha,"Y-m-d");

    }

    public static function diaTextoCorto($numDia){
        
        switch ($numDia) {
            case '1':
                $txt = "Lun";
                break;
            case '2':
                $txt = "Mar";
                break;                
            case '3':
                $txt = "Mie";
                break;     
            case '4':
                $txt = "Jue";
                break;     
            case '5':
                $txt = "Vie";
                break;
            case '6':
                $txt = "Sab";
                break;     
            case '7':
                $txt = "Dom";
                break;                                                        
            default:
                $txt = "";
                break;
            
        }
        return $txt;

    }
    
    public static function extraerAnioDeFecha($fecha)
    {
        $fecha_format = strtotime($fecha);
        $anio = date("Y", $fecha_format);        
        return $anio;
    }

    public static function calcularDiasEntreFechas($ini, $fin)
    {
        $fechaIni= new DateTime($ini);
        $fechaFin= new DateTime($fin);        
      
        $numeroDias = $fechaIni->diff($fechaFin)->days;  
        return $numeroDias;
    }
    
    public static function fechaInicioMesActual()
    {
        $fechaInicio = new DateTime();
        $fechaInicio->modify('first day of this month');
        $fechaIni = $fechaInicio->format('Y-m-d');
        return $fechaIni;
    }

    public static function fechaFinalMesActual()
    {      
        $fechaFinal = new DateTime();
        $fechaFinal->modify('last day of this month');
        $fechaFin = $fechaFinal->format('Y-m-d'); 
        return $fechaFin;
    }

    public static function fechasMesActualString()
    {
        $fechaIni = self::fechaInicioMesActual();
        $fechaFin = self::fechaFinalMesActual();
        return " BETWEEN '$fechaIni' AND '$fechaFin' ";
    }

    public static function convertDateTimeToFormat($dateTime)
    {           
        $timestamp = strtotime($dateTime);        
        $date_formatted = date("d/m/Y H:i:s", $timestamp);        
        return $date_formatted;
    }

}