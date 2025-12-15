<?php


class Controlador {


    public function modelo($modelo) {
        
   
        require_once('../app/models/' . $modelo . '.php');
        
        return new $modelo();
    }

    public function vista($vista, $datos = []) {


        if (file_exists('../app/views/' . $vista . '.php')) {
            require_once('../app/views/' . $vista . '.php');
        } else {

            die("la vista no existe");
        }
    }


    
    
    function eliminar_tildes($cadena){
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );

        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );

        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );

        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );

        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );

        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );

        return $cadena;
    }

    public function limpiar_string($a) {
        
        $salida = $this->eliminar_tildes($a);
        $salida = strtolower(str_replace('&', '', $salida));
        $salida = str_replace('.', '', $salida);
        $salida = str_replace(' ', '', $salida);
        $salida = str_replace('¿', '', $salida);
        $salida = str_replace('?', '', $salida);
        $salida = str_replace('(', '', $salida);
        $salida = str_replace(')', '', $salida);
        $salida = str_replace('-', '', $salida);
        $salida = str_replace('_', '', $salida);
        $salida = str_replace('/', '', $salida);

        return $salida;
    }
    
    
    public function iniciar()
    {
        session_start();
    }
    

    public function salir()
    {
        
        session_destroy();
    }

    
    public function permisos_roles()
    {
        $fichero = file_get_contents('../app/config/configroles.json');        
        $salida = json_decode($fichero);       
        return $salida;
    }

    public function control_acceso_urls($rol){
        
        foreach($this->permisos_roles() as $roles){
               if($roles == $rol){
                   return $roles;
               }
        }
       
    }
  
   

}
