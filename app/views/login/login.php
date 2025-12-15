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
                    <h3 class="title_login">Accés a la plataforma</h3>

                    <form class="form_login" action="<?php echo RUTA_URL ?>/Login/acceder" method="POST" autocomplete="off" id="form_login">
                        <span id="mensajeLogin" class="mensaje_general"></span>   
                        <input type="hidden" id="rutaIni" value="<?php echo RUTA_URL; ?>">
                        
                        <div class="card_login_email">
                            <label for="email" class="label_text_login">Correu electrònic</label>
                            <input type="email" id="email" name="mail" autofocus="" class="input_login px-4 py-2">
                        </div>


                        <div class="card_login_pass">
                            <div class="flex items-center justify-between">
                                <label for="password" class="text-sm font-semibold text-gray-500">Contrasenya</label>              
                            </div>               
                                
                            <div class="eye_icon_login">
                                <!--<div class="absolute inset-y-0 right-0 flex items-center px-2">-->
                                <div class="eye_icon_check px-2">
                                <input class="hidden js-pass-toggles" id="toggles" type="checkbox">
                                <label class="px-2 py-1 js-pass-label" for="toggles"><svg class="svg-inline--fa fa-eye" aria-hidden="true" focusable="false" data-prefix="far" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M288 144a110.94 110.94 0 0 0-31.24 5 55.4 55.4 0 0 1 7.24 27 56 56 0 0 1-56 56 55.4 55.4 0 0 1-27-7.24A111.71 111.71 0 1 0 288 144zm284.52 97.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400c-98.65 0-189.09-55-237.93-144C98.91 167 189.34 112 288 112s189.09 55 237.93 144C477.1 345 386.66 400 288 400z"></path></svg><!-- <i class="far fa-eye texto-violeta-oscuro text-base xl:text-xl"></i> Font Awesome fontawesome.com --></label>

                            </div>
                            
                            <input type="password" id="password" name="pass" class="input_login js-pass  px-4 py-2" autocomplete="off">
                            
                            </div>
                            
                        </div>


                        <div>
                        <button type="submit" class="button_login_submit">
                        Accedir
                        </button>
                        
                        </div>                        
                        <a class="link_forgot_pass" href="<?php echo RUTA_URL.'/Login/recuperarConstrasenia';?>">Has oblidat la contrasenya?</a>

                    </form>

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