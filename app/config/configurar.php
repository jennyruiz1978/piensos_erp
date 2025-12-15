<?php
// Configuracion de acceso a la base de datos

/*TEST*/

 
define('DB_HOST','127.0.0.1:3307');
define('DB_USUARIO','root');
define('DB_PASSWORD','');
define('DB_NOMBRE','piensos');


// Ruta de la aplicacion
define('RUTA_APP', dirname(dirname(__FILE__)));

define('RUTA_URL','http://localhost:8080/piensos_erp');
// NOMBRE DEL SITIO
define('NOMBRE_SITIO', 'PIENSOS DE VIENTRE');

//Ruta para subida de ficheros:
define("DOCUMENTOS_PRIVADOS", RUTA_APP."/documentos/");

//DATOS PARA ENVÍO DE CORREOS TEST
//nombre remitente
define("NOMBRE_CORREO", "Piensos de Vientre");
//cuenta correo para envío

/* define("CORREO_ADMIN", "automatizotunegocioinfo@gmail.com");
define("PASSWORD_CORREO", "arfgqipefinaibxx");
define("HOST_CORREO", "smtp.gmail.com");
define("PUERTO", 465);
define("PROTOCOLO", "SSL"); */

//define("CORREO_ADMIN", "jcarolina141278@hotmail.com");
//define("PASSWORD_CORREO", "J3nny$2023$");
//define("PASSWORD_CORREO", "Jt666333");
//define("HOST_CORREO", "smtp.live.com");
//define("HOST_CORREO", "SMTP.Office365.com");

define("CORREO_ADMIN", "comercial@pinsosdev.com");
define("PASSWORD_CORREO", "q%!2GoAD0vqq");
define("HOST_CORREO", "smtp.pinsosdev.com");
define("PUERTO", 587);
define("PROTOCOLO", "TLS");

/*Comercial*/
/*
define("CORREO_ADMIN", "tarre@pinsosdev.com");
define("PASSWORD_CORREO", "VZ97hJ#397uM");
define("HOST_CORREO", "smtp.pinsosdev.com");
//puerto config correo
define("PUERTO", 587);
*/

//protocolo
//define("PROTOCOLO", "STARTTLS");

//dir plantilla
define("DIR_PLANTILLAS", $_SERVER['DOCUMENT_ROOT']."/piensos_erp/public/plantillas/plantillasEmail/");

define("PROVEEDOR_DEFAULT", 1); //Compañía Cervecera DAMM
define("PURCHASE_PRODUCT_DEFAULT", 1); // Bagazo de cervecería
define("SALE_PRODUCT_DEFAULT", 1);
define("PRODUCT_PLANNING_DEFAULT", 1); //Bagazo de cervecería,con este dato se carga la planificación
define("IVA_COMPRAS", 21);
define("IVA_VENTAS", 10);

//Tipos de error
define("ERROR_CREACION", "S'ha produït un error i no s'ha creat el registre");
define("OK_CREACION", "Es crea el registre correctament");

define("ERROR_ACTUALIZACION", "S'ha produït un error i no s'han actualitzat les dades");
define("OK_ACTUALIZACION", "S'ha actualitzat correctament el registre");

define("ERROR_ELIMINACION", "No s'ha eliminat el registre");
define("OK_ELIMINACION", "S'ha eliminat el registre");

define("ERROR_GUARDADO", "S'ha produït un error i no s'han desat les dades");

define("ERROR_FORM_INCOMPLETO", "No es pot guardar el registre perquè manquen dades al formulari");


define("ERROR_DOESNT_EXIST", "No hi ha dades per a la consulta");

define("ERROR_VALIDACION_EMAIL", "El correu indicat no existeix");
define("ERROR_RECUPERAR_PASS", "Error en recuperar contrasenya");
define("OK_RECUPERAR_PASS", "Hem enviat un correu per restablir la contrasenya");
define("ERROR_RECUPERAR_PASS2", "No s'ha pogut restablir la contrasenya");

define("ALBARAN_FACTURADO", "Aquest albarà ja ha estat facturat");

define("NOMBRE_FISCAL_PIENSOS", "Piensos de Vientre S.L.");
define("NIF_PIENSOS", "B-58707423");
define("DIRECCION_PIENSOS", "Provença, 395, 5º 1ª ");
define("CODIGO_POSTAL_PIENSOS", "08025");
define("LOCALIDAD_PIENSOS", "Barcelona");
define("PROVINCIA_PIENSOS", "Barcelona");

define("REDONDEO_IMPORTE", 2);
define("API_GOOGLE_DRIVE",0); //1 FUNCIONA, 0 NO FUNCIONA

//RewriteBase /piensos_erp/public