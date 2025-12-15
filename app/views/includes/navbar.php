<?php    

if (!isset($_SESSION)) {
  session_start();
}

date_default_timezone_set("Europe/Madrid");
$ahora = date("Y-n-j H:i:s");
$duracion = (strtotime($ahora)-strtotime($_SESSION['inicio']));

if($duracion >= 28800) {    // 86400 = 24 horas (expresado en segundos)
    session_destroy();
    //header("location:index.html");
    header('location: ' . RUTA_URL);
}

?>
<input type="hidden" id="ruta" value="<?php echo RUTA_URL;  ?>">
<nav class="navbar_main">
  <div class="mx-auto px-2">
    <div class="navbar_items py-2">

      <div class="hamburger_container">
        
        <button id="sidebarBtn" class="px-3 py-2 text-white">
          <i class="fas fa-bars"></i>
        </button>
        <input id="idUser" type="hidden" value="<?php //echo $_SESSION['idusuario'];?>"> 
        <input type="hidden" id="rolUsuarioFinalizar" value="<?php //echo $_SESSION['nombrerol'];?>">                            
      </div>

      <div class="icons_container pr-2">       
              
        <div class="relative">
          <div>
            <button type="button" class="profileBtn" id="profileBtn">
              <a class="text-white icon_profile"><i class="fas fa-user"></i></a>
            </button>
          </div>

          <div id="profileDiv" class="hidden mt-2 py-1">
            
            <a href="#" class="block px-4 py-2 text-sm text-gray-700">
              <i class="fas fa-user mr-2"></i>Test<?php //echo $_SESSION['usuario'];?>
              <input type="hidden" id="nombreUsuario" value="Jenny<?php //echo $_SESSION['usuario'];?>">
            </a>          
            <a href="<?php echo RUTA_URL; ?>/Login/vaciar" class="block px-4 py-2 text-sm text-gray-700">
              <i class="fas fa-sign-out-alt mr-2"></i>Sortir
            </a>
            
          </div>

        </div>
        
      </div>

    </div>
  </div>
</nav>
