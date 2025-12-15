<?php
//Cargamos la libreria
require_once('config/configurar.php');

require_once('helpers/url_helpers.php');


//Autoload php
/* spl_autoload_register(function($nombreClase){
    require_once('librerias/' . $nombreClase . '.php');
}); */

spl_autoload_register(function($nombreClase){
    $paths = [
        __DIR__ . '/librerias/',
        __DIR__ . '/controlers/'
    ];

    foreach ($paths as $path) {
        $archivo = $path . $nombreClase . '.php';
        if (file_exists($archivo)) {
            require_once $archivo;
            return;
        }
    }
});