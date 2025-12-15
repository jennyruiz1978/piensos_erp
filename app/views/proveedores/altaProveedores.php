<?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Crear proveïdor</h2>                  
                    
                    <div class="productos_category">

                        <div class="producto_compra">                               
                            <form id="formulario_crear_proveedor">
                                <div class="row">

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="nombre_proveedor" class="form-label">Nom fiscal (*)</label>
                                        <input type='text' regexp="[a-zA-ZäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙçÇñÑ0-9\s]{1,100}" class="form-control" name="nombre_proveedor" id="nombre_proveedor" value="<?php ;?>" style="text-transform:uppercase;">
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="nif_proveedor" class="form-label">NIF (*)</label>
                                        <input type='text' regexp="[a-zA-Z0-9]{0,9}" class="form-control" name="nif_proveedor" id="nif_proveedor" value="<?php ;?>">
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="direccion_proveedor" class="form-label">Direcció</label>
                                        <input class="form-control" name="direccion_proveedor" id="direccion_proveedor" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="poblacion_proveedor" class="form-label">Població</label>
                                        <input class="form-control" name="poblacion_proveedor" id="poblacion_proveedor" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="codigo_postal_proveedor" class="form-label">Codi postal</label>
                                        <input type='text' regexp="[0-9]{0,5}" class="form-control" name="codigo_postal_proveedor" id="codigo_postal_proveedor" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="provincia_proveedor" class="form-label">Província</label>
                                        <input class="form-control" name="provincia_proveedor" id="provincia_proveedor" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="telefono_proveedor" class="form-label">Telèfon</label>
                                        <input type='text' regexp="[0-9]{0,9}" class="form-control" name="telefono_proveedor" id="telefono_proveedor" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="email_proveedor" class="form-label">Correu</label>
                                        <input type="email" class="form-control" name="email_proveedor" id="email_proveedor" value="<?php ;?>">
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="estado_proveedor" class="form-label">Situació</label>
                                        <select class="form-control" name="estado_proveedor" id="estado_proveedor">
                                            <option selected value="" disabled>Seleccionar</option>
                                            <option value="activo">Actiu</option>
                                            <option value="inactivo">Inactiu</option> 
                                        </select>                                        
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="observaciones_proveedor" class="form-label">Observacions</label>
                                        <textarea class="form-control" name="observaciones_proveedor" id="observaciones_proveedor" rows="2"></textarea>                                        
                                    </div>
                                                                            
                                </div>

                                
                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>

                                <div class="cont_button_product">
                                    <button type="submit" class="button_submit btn_form_crear" id="guardar_proveedor">Crear</button>
                                    <a class="button_update btn_form_crear" id="actualizar_proveedor" href="<?php echo RUTA_URL.'/Proveedores';?>">Cerrar</a>
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
