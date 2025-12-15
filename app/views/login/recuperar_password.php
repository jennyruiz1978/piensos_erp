<!DOCTYPE html>
<html lang="es">

    <head>
        <title><?php echo NOMBRE_SITIO; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equive="X-UA-Compatible" content="id=edge">
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL; ?>/public/vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL; ?>/public/vendor/fortawesome/font-awesome/css/all.min.css">    
        <link rel="stylesheet" type="text/css" href="<?php echo RUTA_URL ?>/public/css/login.css">             
    </head>

    <body>        

        <div class="main_container">
            <div class="card_login">
                <div class="login_logo card_login_box">
                    <div class="img_logo">
                    <img src="<?php echo RUTA_URL;?>/public/img/logo_piensos2.jpg">
                    </div>
                    
                </div>
                <div class="login_inputs card_login_box">
                    <h3 class="title_login">Recuperar contrasenya</h3>

                    <form class="form_login" action="<?php echo RUTA_URL ?>/Login/acceder" method="POST" autocomplete="off" id="form_forgot_pass">
                        <span id="mensajeLogin" class="mensaje_general"></span>   
                        <input type="hidden" id="rutaIni" value="<?php echo RUTA_URL; ?>">
                        
                        <div class="card_login_email">
                            <label for="email" class="label_text_login">Correu electrònic</label>
                            <input type="email" id="email" name="mail" autofocus="" class="input_login px-4 py-2">
                        </div>                       
                                                
                        <div class="card_login_buttons">
                            <button type="submit" class="button_login_submit">Enviar</button>        
                            <a type="submit" class="button_login_secondary" href="<?php echo RUTA_URL.'/Login';?>">Tornar al login</a>
                        </div>                        

                    </form>

                    <div class="msgErrores" id="msgErrores"></div>

                </div>
            </div>
        </div>

        
    <script src="<?php echo RUTA_URL; ?>/public/vendor/components/jquery/jquery.min.js"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>-->
    <script type="text/javascript" src="<?php echo RUTA_URL ?>/public/js/popper.min.js"></script>
    <script src="<?php echo RUTA_URL; ?>/public/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <scrip src="<?php echo RUTA_URL; ?>/public/vendor/fortawesome/font-awesome/js/all.min.js"></scrip>          
    <script type="text/javascript" src="<?php echo RUTA_URL ?>/public//js/login.js"></script>



</body>

</html>