<?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Crear zona</h2>                  
                    
                    <div class="productos_category">

                        <div class="producto_compra">                               
                            <form id="formulario_crear_zona">
                                <div class="row">

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="nombre_zona" class="form-label">Zona (*)</label>
                                        <input class="form-control" name="nombre_zona" id="nombre_zona">
                                    </div>
                                    
                                    <div class="col-md-6 cont_field_prod">
                                        <label for="precio_zona" class="form-label">Precio (*)</label>
                                        <input type="number" step="0.01" class="form-control" name="precio_zona" id="precio_zona">
                                    </div>
                                                                                               

                                        
                                </div>

                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>

                                <div class="cont_button_product">
                                    <button type="submit" class="button_submit btn_form_crear" id="guardar_zona">Crear</button>
                                    <a class="button_update btn_form_crear" id="actualizar_zona" href="<?php echo RUTA_URL.'/Zonas';?>">Cerrar</a>
                                </div>

                            </form>
                        </div>
        
                    </div>

               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
