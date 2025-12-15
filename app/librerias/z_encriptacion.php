<?php

class encriptacion {
/** GENERA ENCRIPTACIÓN DE UNA CADENA USANDO UNA CLAVE, LA MISMA QUE DEBEREMOS USAR PARA DESENCRIPTARLA POSTERIORMENTE */
 public static function encrypt($string, $key) {
    $result = '';
    for($i=0; $i<strlen($string); $i++) {
       $char = substr($string, $i, 1);
       $keychar = substr($key, ($i % strlen($key))-1, 1);
       $char = chr(ord($char)+ord($keychar));
       $result.=$char;
    }
    return base64_encode($result);
 }
/** DESENCRIPTA UNA CADENA USANDO UNA CLAVE, LA MISMA QUE USAMOS AL ENCRIPTARLA */
public static function decrypt($string, $key) {
  $result = '';
  $string = base64_decode($string);
  for($i=0; $i<strlen($string); $i++) {
     $char = substr($string, $i, 1);
     $keychar = substr($key, ($i % strlen($key))-1, 1);
     $char = chr(ord($char)-ord($keychar));
     $result.=$char;
  }
  return $result;
}

/**GENERACIÓN DE TOKEN IDENTIFICATIVO, SE USA EL MOMENTO ACTUAL, DE ESTA FORMA EVITAREMOS QUE BAJO NINGÚN CONCEPTO SEA DUPLICADO */

public static function generarToken(){
  $now=date('YmdHis');
  $token=base64_encode(md5(uniqid())).$now;
  return $token;
}

/**LA IDEA ES ENVIAR UN PDF COMO ELEMENTO Y EMITIRSELO AL CLIENTE EN BASE 64, para que lo almacene en su BBDD*/
public static function elementoToBase64($elemento){
  return  base64_encode($elemento);

}

public static function base64ToElemento($elementoB64){
  return  base64_decode($elementoB64);
}

}