<?php
/*
 Mapear la url ingresada en el navegador
1º.- controlador
2º.- metodo
3º.- parametro
Ejemplo: /articulo/actualizar/4
 */



class Core {


   protected $controladorActual = 'Login';
   protected $metodoActual = 'index';
   protected $parametros = [];

   public function __construct(){

       $url = $this->getUrl();
       //print_r($this->getUrl());
       // buscar en controladores si el controlador existe

       if(isset($url[0]) && file_exists('../app/controlers/' . ucwords($url[0] . '.php'))){
            // si existe se configura como controlador por defecto
            $this->controladorActual = ucwords($url[0]);

            //unset indice
            unset($url[0]);
       }
       // requerir el nuevo controlador
        require_once('../app/controlers/' . $this->controladorActual . '.php');

       $this->controladorActual = new $this->controladorActual;

       // chequear la segunda parte de la url, que seria el metodo
       if(isset($url[1])){
           if(method_exists($this->controladorActual, $url[1])){
               // seteamos el metodo
               $this->metodoActual = $url[1];
               unset($url[1]);
           }
       }
       // para probar imprimir metodo
       //echo $this->metodoActual;
       $this->parametros = $url ? array_values($url) : [];

       // llamar callback con parametros array
       call_user_func_array([$this->controladorActual, $this->metodoActual], $this->parametros);
   }

   public function getUrl(){
       //echo $_GET['url'];
        $url = "";
       if(isset($_GET['url'])){
           $url = rtrim($_GET['url'], '/');
           $url = filter_var($url, FILTER_SANITIZE_URL);
           $url = explode('/', $url);

       }
       return $url;
   }





}
