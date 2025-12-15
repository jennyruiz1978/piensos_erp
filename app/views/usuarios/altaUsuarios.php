<?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Crear usuari</h2>                  
                    
                    <div class="productos_category">

                        <div class="producto_compra">                               
                            <form id="formulario_crear_usuario">
                                <div class="row">

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="nombre_usuario" class="form-label">Nom</label>
                                        <input class="form-control" name="nombre_usuario" id="nombre_usuario" value="<?php ;?>">
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="apellidos_usuario" class="form-label">Cognoms</label>
                                        <input class="form-control" name="apellidos_usuario" id="apellidos_usuario" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="email_usuario" class="form-label">Correu</label>
                                        <input type="email" class="form-control" name="email_usuario" id="email_usuario" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="password_usuario" class="form-label">Contrasenya</label>
                                        <input type="text" regexp="[a-zA-Z0-9\-/*?¿¡!$_%+]{1,50}" class="form-control" name="password_usuario" id="password_usuario" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="rol_usuario" class="form-label">Rol</label>
                                        <select class="form-control" name="rol_usuario" id="rol_usuario">
                                            <option selected value="">Seleccionar</option>
                                            <option value="1">Admin</option>                                            
                                        </select>                                        
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="estado_usuario" class="form-label">Situació</label>
                                        <select class="form-control" name="estado_usuario" id="estado_usuario">
                                            <option selected value="">Seleccionar</option>
                                            <option value="activo">Actiu</option>
                                            <option value="inactivo">Inactiu</option> 
                                        </select>                                        
                                    </div>
                                        
                                </div>

                                <div class="cont_button_product">
                                    <button type="submit" class="button_submit btn_form_crear" id="guardar_usuario">Crear</button>
                                    <a class="button_update btn_form_crear" id="actualizar_usuario" href="<?php echo RUTA_URL.'/Usuarios';?>">Cerrar</a>
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
