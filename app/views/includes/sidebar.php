<main class="container_layout">

  <!--<aside class="side_bar block" id="side_bar">-->
      <aside class="side_bar" style="display:none;" id="side_bar">
        <div class="p-4">
            <img class="logo_sidebar" src="<?php echo RUTA_URL;  ?>/public/img/logo_piensos2.jpg">
        </div>
        <nav class="text-white text-base font-semibold pt-1" id="menu">
              
              <?php     
              
                foreach($_SESSION['permisos'] as $menu => $detalle){ 
                
                  echo'
                  <div class="m-2">
                    <div class="text-white py-1 nav-item menu-btn text-menu" style="cursor:pointer;">';
                    
                      print '<span><i class=" '. $detalle['icono'] . ' mr-3 icon_menu_left"></i></span>';
                      print($detalle['nombre']);
                      
                      echo'
                      <span><i class="fas fa-angle-right ml-3"></i></span>
                    </div>';
                      

                      
                    if(isset($detalle['menus']) && count($detalle['menus']) > 0){ 
                        echo'
                        <div class="hidden dropdown_submenu">';
                          if(isset($detalle['menus']) && count($detalle['menus']) > 0){
                            foreach ($detalle['menus'] as $detalle_menu) {
                              echo'<a href="'.RUTA_URL . $detalle_menu['ruta'].'" class="px-3 py-1 mt-1 link_submenu">';
                              print($detalle_menu['nombre']); 
                              echo'</a>';                              
                            }                          
                          }
                        echo'
                        </div>';                       
                    }                       
                  echo'                    
                  </div>';
                }

              ?>
        </nav>
  </aside>
  
  <div class="dynamic_content">