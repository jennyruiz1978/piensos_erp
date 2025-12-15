<?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php');
      
      
      ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Crear client</h2>                  
                    
                    <div class="productos_category">

                        <div class="producto_compra">                               
                            <form id="formulario_crear_cliente">
                                <div class="row">

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="nombre_cliente" class="form-label">Nom fiscal (*)</label>
                                        <input class="form-control" type='text' regexp="[a-zA-ZäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙçÇñÑ0-9\s]{1,100}" name="nombre_cliente" id="nombre_cliente"  style="text-transform:uppercase;">
                                    </div>
                                    
                                    <div class="col-md-3 cont_field_prod">
                                        <label for="nif_cliente" class="form-label">NIF (*)</label>
                                        <input type='text' regexp="[a-zA-Z0-9]{0,9}" class="form-control" name="nif_cliente" id="nif_cliente" >
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="zona_cliente" class="form-label">Zona (*)</label>
                                        <select class="form-control" name="zona_cliente" id="zona_cliente">
                                            <option disabled selected value="">Seleccionar</option>
                                            <?php
                                                if(isset($datos['zonas']) && count($datos['zonas']) > 0){
                                                    $zonas = $datos['zonas'];
                                                    foreach ($zonas as $zona) {
                                                        echo"<option value='".$zona->id."'>".$zona->zona." - ".$zona->margen." €</option>";
                                                    }
                                                }
                                            ?>                                            
                                        </select>                                        
                                    </div>

                                    <div class="col-md-2 cont_field_prod">
                                        <label for="precio_cliente" class="form-label">Preu (*)</label>
                                        <input type='number' step="0.01" class="form-control" name="precio_cliente" id="precio_cliente" >
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="direccion_cliente" class="form-label">Direcció</label>
                                        <input class="form-control" name="direccion_cliente" id="direccion_cliente" >
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="poblacion_cliente" class="form-label">Població</label>
                                        <input class="form-control" name="poblacion_cliente" id="poblacion_cliente" >
                                    </div>

                                    <div class="col-md-2 cont_field_prod">
                                        <label for="codigo_postal_cliente" class="form-label">Codi postal</label>
                                        <input type='text' regexp="[0-9]{0,5}" class="form-control" name="codigo_postal_cliente" id="codigo_postal_cliente" >
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="provincia_cliente" class="form-label">Província</label>
                                        <input class="form-control" name="provincia_cliente" id="provincia_cliente" >
                                    </div>

                                   

                                    <div class="col-md-2 cont_field_prod">
                                        <label for="telefono_cliente" class="form-label">Telèfon</label>
                                        <input type='text' regexp="[0-9]{0,9}" class="form-control" name="telefono_cliente" id="telefono_cliente" >
                                    </div>


                                    <div class="col-md-3 cont_field_prod">
                                        <label for="estado_cliente" class="form-label">Situació</label>
                                        <select class="form-control" name="estado_cliente" id="estado_cliente">                                            
                                            <option value="activo">Actiu</option>
                                            <option value="inactivo">Inactiu</option> 
                                        </select>                                        
                                    </div>






                                    <div class="col-md-3 cont_field_prod">
                                        <label class="form-label">Forma cobrament</label>                                              
                                        <select class="form-control" name="formacobro" id="formacobro"> 
                                                <option selected disabled value="">Seleccionar</option>
                                                <?php
                                                    if(isset($datos['formacobro']) && count($datos['formacobro']) > 0){
                                                        foreach ($datos['formacobro'] as $d) {
                                                            echo'<option value="'.$d->id.'">'.$d->formadepago.'</option>';
                                                        }
                                                    }
                                                ?>
                                        </select>                                            
                                    </div>  



                                   
                                    <div class="col-md-6 cont_field_prod">
                                        <label for="observaciones_cliente" class="form-label">Observacions</label>
                                        <textarea class="form-control" name="observaciones_cliente" id="observaciones_cliente" rows="2"></textarea>                                        
                                    </div>
                                    

                                        
                                </div>


                                <div class="row">                                     
                                    <div class="col-md-4 cont_field_prod" style="display: none;">
                                        <label for="email_cliente" class="form-label">Correu</label>
                                        <input type="email" class="form-control" name="email_cliente" id="email_cliente" >
                                    </div>
                                    
                                    <div class="col-md-6 cont_field_prod">
                                        <?php require_once(RUTA_APP . '/views/components/contactos_component.php'); ?>
                                    </div>                                   
                                </div>



                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>

                                <div class="cont_button_product">
                                    <button type="submit" class="button_submit btn_form_crear" id="guardar_cliente">Crear</button>
                                    <a class="button_update btn_form_crear" id="actualizar_cliente" href="<?php echo RUTA_URL.'/Clientes';?>">Cerrar</a>
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
