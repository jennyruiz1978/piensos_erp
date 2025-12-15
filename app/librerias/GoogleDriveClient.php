<?php

require_once '../public/vendor/autoload.php'; // Ruta al autoload de Composer

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;

class GoogleDriveClient
{    

    public static function obtenerCliente()
    {
        $client = new Google_Client();
        $client->setAuthConfig(__DIR__ . '/service-account.json'); // nombre correcto
        $client->addScope(Google_Service_Drive::DRIVE);
        $client->useApplicationDefaultCredentials(); // 👈 importante para cuentas de servicio

        // Opcional: evitar verificación SSL si estás en localhost
        $client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));

        return $client;
    }

}
