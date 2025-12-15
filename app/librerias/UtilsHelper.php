<?php

class UtilsHelper {
        

    public static function random_password($length = 6 ){

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    
    }
    
    public static function buildStringsInsertQuery($arrValues, $arrFields){            

        $retorno['ok'] = false;

        $str = " ( ";
        $val = " ( ";        
        $cont = 0;       
                   
        if(count($arrValues) > 0 && count($arrFields) > 0 && count($arrValues)==count($arrFields)){
                   
            foreach ($arrValues as $key => $value) {
        
                if(in_array($key,$arrFields)){
                    $cont++;
                    if ($cont == count($arrValues)  ) {
                        $str .= " $key )";
                        $val .= " '$value' )";
                    } else {
                        $str .= " $key ,";
                        $val .= " '$value' ,";
                    }
                }
            }    

            if($cont == count($arrFields)){
                $retorno['ok'] = true;
                $retorno['strFields'] = $str;
                $retorno['strValues'] = $val;
            }        
        }       
        return $retorno;
    }
    
    public static function buildStringsInsertQueryNuevo($arrValues, $arrFields){
                       
        $retorno['ok'] = false;

        $str = " ( ";
        $val = " ( ";        
        $cont = 0;

        if(count($arrValues) > 0 && count($arrFields) > 0){
            
            foreach ($arrValues as $key => $value) {
        
                if(in_array($key,$arrFields)){
                    $cont++;
                    if ($cont == count($arrValues) || $cont == count($arrValues)-1 ) {
                        $str .= " $key )";
                        $val .= " '$value' )";
                    } else {
                        $str .= " $key ,";
                        $val .= " '$value' ,";
                    }
                }
            }    

            if($cont == count($arrFields)){
                $retorno['ok'] = true;
                $retorno['strFields'] = $str;
                $retorno['strValues'] = $val;
            }
        }       
        return $retorno;
    }

    public static function buildStringsInsertQueryNuevo2($arrValues, $arrFields){
                       
        $retorno['ok'] = false;

        $str = " ( ";
        $val = " ( ";        
        $cont = 0;        

        if(count($arrValues) > 0 && count($arrFields) > 0){
            
            foreach ($arrValues as $key => $value) {
        
                if(in_array($key,$arrFields)){
                    $cont++;                   

                    if ($cont == count($arrFields) ) {
                        $str .= " $key )";
                        $val .= " '$value' )";
                    } else {
                        $str .= " $key ,";
                        $val .= " '$value' ,";
                    }
                }
            }    

            if($cont == count($arrFields)){
                $retorno['ok'] = true;
                $retorno['strFields'] = $str;
                $retorno['strValues'] = $val;
            }
        }       
        return $retorno;
    }
        
    
    public static function buildStringsUpdateQuery($arrValues, $arrFields){
     

        $retorno['ok'] = false;
        

        $str = " ";        
        $cont = 0;

        if(count($arrFields) > 0 && count($arrValues) > 0 ){
            
            foreach ($arrFields as $k) {
        
                if (array_key_exists($k,$arrValues)) {
               
                    $cont++;
                    
                    if ($cont == count($arrFields)  ) {
                        $str .= " $k = '".$arrValues[$k]."' ";
                        
                    } else {
                        $str .= " $k = '".$arrValues[$k]."' , ";
                    }
                    
                }
            }              
            
            if($cont == count($arrFields)){
                $retorno['ok'] = true;
                $retorno['strFieldsValues'] = $str;            
            }  
        }
       
        return $retorno;
    }
    

    public static function buildStringsWhereQuery($arrWhere = []){

        $str = "";
        $cont = 0;        
        $retorno['ok'] = false;

        if(count($arrWhere) > 0){
            
            foreach ($arrWhere as $w => $v) {
                $cont++;
                   
                    if ($cont == count($arrWhere)  ) {
                        $str .= " $w = '$v' ";

                    } else {
                        $str .= " $w = '$v' AND ";
                    }
            }
            if($cont == count($arrWhere)){
                $retorno['ok'] = true;
                $retorno['strWhere'] = $str;
            }
        }         
        return $retorno;
    }

    public static function validateRequiredFields($post, $arrFieldsValidate)
    {
           
        $arrError = []; 
      
        if(count($arrFieldsValidate) > 0 && count($post) > 0){                       

            foreach ($arrFieldsValidate as $key) {
                 
                    if(!isset($post[$key]) || trim($post[$key]) == ''){
                       
                        array_push($arrError, $key);
                    }                
            }              
        }                     
        return $arrError;
    }      

    
    public static function validateRequiredFieldsSettingSuppliers($post, $arrFieldsValidate)
    {
           
        $arrError = []; 
      
        if(count($arrFieldsValidate) > 0 && count($post) > 0){                       

            foreach ($arrFieldsValidate as $key) {
                                    
                    if(!isset($post[$key]) || trim($post[$key]) == ''){                    
                        array_push($arrError, $key);                        
                    }else if($key == 'precioprovfab' && $post[$key] == 0){
                        array_push($arrError, $key);                        
                    }else if($key == 'idproductotransp' && $post[$key] == 0){
                        array_push($arrError, $key);                        
                    }else if($key == 'idproductofab' && $post[$key] == 0){
                        array_push($arrError, $key);                        
                    }
            }              
        }                     
        return $arrError;
    }   

    public static function numberToWords($number) {
        $units = array("", "mil", "millón", "mil millones", "billón", "mil billones", "trillón");
        $maxUnits = count($units) - 1;
        
        $integerPart = floor($number);
        $decimalPart = round(($number - $integerPart) * 100);
        
        $integerWords = self::toWords($integerPart, $units);
        $decimalWords = self::toWords($decimalPart, array("céntimos"));
        
        $result = $integerWords. " euros ";
        if ($decimalPart > 0) {
            $result .= " con $decimalWords";
        }
        
        return $result;
    }
    
    public static function toWords($number, $units) {
        if ($number == 0) {
            return "cero";
        }
        
        $result = "";
        $currentUnit = 0;
        
        while ($number > 0) {
            $chunk = $number % 1000;
            
            if ($chunk > 0) {
                if ($chunk == 1 && $currentUnit == 1) {
                    $result = $units[$currentUnit] . " " . $result;
                } else {
                    $result = self::convertChunkToWords($chunk) . " " . $units[$currentUnit] . " " . $result;
                }
            }
            
            $number = floor($number / 1000);
            $currentUnit++;
        }
        
        return trim($result);
    }
    
    public static function convertChunkToWords($number) {
        $ones = array("", "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve");
        $teens = array("diez", "once", "doce", "trece", "catorce", "quince", "dieciséis", "diecisiete", "dieciocho", "diecinueve");
        $tens = array("", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa");
        $hundreds = array("", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos");
        
        $result = "";
        
        $hundred = floor($number / 100);
        $number %= 100;
        
        if ($hundred > 0) {
            $result .= $hundreds[$hundred] . " ";
            if ($number > 0) {
                $result .= "y ";
            }
        }
        
        if ($number >= 10 && $number <= 19) {
            $result .= $teens[$number - 10] . " ";
        } else {
            $ten = floor($number / 10);
            $number %= 10;
            
            if ($ten > 0) {
                $result .= $tens[$ten] . " y ";
            }
            
            if ($number > 0) {
                $result .= $ones[$number] . " ";
            }
        }
        
        return trim($result);
    }
    
    public static function buildStringsFieldsUpdateQuery($arrFieldsValues){
     
        $retorno = '';
        $str = " ";        
        $cont = 0;

        if(count($arrFieldsValues) > 0 ){
            
            foreach ($arrFieldsValues as $k => $v) {
                                    
                $cont++;
                if ($cont == count($arrFieldsValues)  ) {
                    $str .= " $k = '".$v."' ";                  
                } else {
                    $str .= " $k = '".$v."' , ";
                }
                                                   
            }                                     
            $retorno = $str;            
           
        }
       
        return $retorno;
    }
        
    public static function buildStringsWhereQueryOnly($arrWhere = []){

        $str = "";
        $cont = 0;        
        $retorno = '';

        if(count($arrWhere) > 0){
            
            foreach ($arrWhere as $w => $v) {
                $cont++;
                   
                    if ($cont == count($arrWhere)  ) {
                        $str .= " $w = '$v' ";

                    } else {
                        $str .= " $w = '$v' AND ";
                    }
            }
            if($cont == count($arrWhere)){               
                $retorno = $str;
            }
        }         
        return $retorno;
    }
    
    public static function getWeekNumberByDate($date)
    {
        // Convierte la fecha a un timestamp
        $timestamp = strtotime($date);

        // Obtiene el número de la semana usando la función date
        $numeroSemana = date('W', $timestamp);

        return $numeroSemana;
    }

    public static function redondearNumero($importe, $numDec)
    {
        $retorno = 0;
        $redondeo = (isset($numDec) && $numDec != '' && $numDec > 0)? $numDec: 0;
        if($importe != 0){
            $retorno = round($importe, $redondeo);
        }
        return $retorno;
    }
  

}