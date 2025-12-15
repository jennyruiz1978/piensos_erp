<?php

    require '../public/librerias/PHPMailer-master/src/Exception.php';
    require '../public/librerias/PHPMailer-master/src/PHPMailer.php';
    require '../public/librerias/PHPMailer-master/src/SMTP.php'; 
        
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class enviarEmail
    {
            
      public static function enviarEmailDestinatario($emailDestinatario, $asunto, $message, $datosCorreo)
      {
   
        /*       
        echo"<br>emailDestinatario<br>";
        print_r($emailDestinatario);
        echo"<br>asuntotinatario";
        print_r($asunto);
        echo"<br>message<br>";
        print_r($message);
        */
        
        try {

       
         
          $mail = new PHPMailer;
          $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only       
          $mail->IsSMTP(); // enable SMTP        
          $mail->SMTPAuth = true; // authentication enabled
          $mail->SMTPOptions = array(
            'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
            )
          );
          $mail->SMTPSecure = $datosCorreo->protocolo; // secure transfer enabled REQUIRED for Gmail
          $mail->Host = $datosCorreo->host;
          $mail->Port = $datosCorreo->puerto;
          $mail->IsHTML(true);
          $emailRemitente = $datosCorreo->correo;
          $mail->Username = $emailRemitente;
          $mail->Password = $datosCorreo->passwordcorreo;
          $mail->Sender = $emailRemitente;
          $nombreRemitente = (isset($datosCorreo->remitente) && $datosCorreo->remitente != '')? strtoupper($datosCorreo->remitente): '';
          $mail->SetFrom($emailRemitente, $nombreRemitente);          
          $mail->Subject = html_entity_decode($asunto);          
          $mail->Body =  html_entity_decode($message);
    
    
          $emails = $emailDestinatario;
          $nombreDestinatario = '';              
          foreach ($emails as $row) {
            $mail->addAddress($row, $nombreDestinatario);
          }

          $mail->CharSet = 'UTF-8';
          if (!$mail->Send()) {
            /*echo"<br>entra<br>";
            print_r($mail);*/


            return 0;
          } else {
            return 1;
          }
        } catch (Exception $exception) {
          return $exception->getMessage();
        }
      }     
    
      public static function enviarEmailConDocumentos($emailDestinatario, $asunto, $message, $attachmentArray, $datosCorreo)
      {
        /////
        /*        
        echo"<br>emailDestinatario<br>";
        print_r($emailDestinatario);
        echo"<br>asunto<br>";
        print_r($asunto);
        echo"<br>message<br>";
        print_r($message);
        echo"<br>attachmentArray<br>";
        print_r($attachmentArray);
        
        die;
        */
        
        /////
    
        try {
          $mail = new PHPMailer;
          $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only       
          $mail->IsSMTP(); // enable SMTP        
          $mail->SMTPAuth = true; // authentication enabled
          $mail->SMTPOptions = array(
            'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
            )
          );
          $mail->SMTPSecure = $datosCorreo->protocolo; // secure transfer enabled REQUIRED for Gmail
          $mail->Host = $datosCorreo->host;
          $mail->Port = $datosCorreo->puerto;
          $mail->IsHTML(true);
          $emailRemitente = $datosCorreo->correo;
          $mail->Username = $emailRemitente;
          $mail->Password = $datosCorreo->passwordcorreo;
          $mail->Sender = $emailRemitente;
          $nombreRemitente = (isset($datosCorreo->remitente) && $datosCorreo->remitente != '')? strtoupper($datosCorreo->remitente): '';
          $mail->SetFrom($emailRemitente, $nombreRemitente);          
          $mail->Subject = html_entity_decode($asunto);          
          $mail->Body =  html_entity_decode($message);
                  
          $emails = $emailDestinatario;
          $nombreDestinatario = '';            
          
          foreach ($attachmentArray as $attachment) {
            $mail->AddStringAttachment($attachment['documento'], $attachment['nombreDocumento']);         
          }      
          foreach ($emails as $row) {
            $mail->addAddress($row, $nombreDestinatario);
          }
    
          // Activo condificacción utf-8
          $mail->CharSet = 'UTF-8';
          if (!$mail->Send()) {
            return 0;
          } else {
            return 1;
          }
        } catch (Exception $exception) {
          return $exception->getMessage();
        }
      }      

    }
    