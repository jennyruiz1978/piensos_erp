      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">    
               <!-- ****** CONTENIDO DE CADA PAGINA ****** -->                                    
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Configuració</h2>
                  
                  <div class="productos_category_config">

                     <div class="container_configuracion">   
                        <h4>Configuració de dades de la companyia</h4>
                        <form id="formulario_config_piensos">
                           <div class="row">

                              <div class="col-12 col-md-8 cont_field_prod">
                                 <label for="razonsocialpiensos" class="form-label">Nom fiscal</label>
                                 <input class="form-control" name="razonsocialpiensos" id="razonsocialpiensos" value="<?php echo $datos->razonsocialpiensos;?>">
                                 <span class="mensaje_required" id="error_razonsocialpiensos"></span>
                              </div>

                              <div class="col-12 col-md-4 cont_field_prod">
                                 <label for="cifpiensos" class="form-label">CIF</label>
                                 <input class="form-control" name="cifpiensos" id="cifpiensos" value="<?php echo $datos->cifpiensos;?>">
                                 <span class="mensaje_required" id="error_cifpiensos"></span>
                              </div>

                              <div class="col-12 col-md-8 cont_field_prod">
                                 <label for="direccionpiensos" class="form-label">Direcció</label>
                                 <input class="form-control" name="direccionpiensos" id="direccionpiensos" value="<?php echo $datos->direccionpiensos;?>">
                                 <span class="mensaje_required" id="error_direccionpiensos"></span>
                              </div>                

                              <div class="col-6 col-md-4 cont_field_prod">
                                 <label for="codigopostalpiensos" class="form-label">Codi postal</label>
                                 <input class="form-control" name="codigopostalpiensos" id="codigopostalpiensos" value="<?php echo $datos->codigopostalpiensos;?>">
                                 <span class="mensaje_required" id="error_codigopostalpiensos"></span>
                              </div>

                              <div class="col-6 col-md-5 cont_field_prod">
                                 <label for="localidadpiensos" class="form-label">Localitat</label>
                                 <input class="form-control" name="localidadpiensos" id="localidadpiensos" value="<?php echo $datos->localidadpiensos;?>">
                                 <span class="mensaje_required" id="error_localidadpiensos"></span>
                              </div>                              

                              <div class="col-6 col-md-5 cont_field_prod">
                                 <label for="provinciapiensos" class="form-label">Província</label>
                                 <input class="form-control" name="provinciapiensos" id="provinciapiensos" value="<?php echo $datos->provinciapiensos;?>">
                                 <span class="mensaje_required" id="error_provinciapiensos"></span>
                              </div>                              


                           </div>
                           <div class="cont_button_product">
                              <button type="submit" class="button_submit btn_submit_producto" id="guardar_config_piensos">Actualitzar</button>
                           </div>
                        </form>
                     </div>

                     <div class="container_configuracion">   
                        <h4>Configuració de correo para mensajería</h4>
                        <form id="formulario_config_correo">
                           <div class="row">

                              <div class="col-12 col-md-4 cont_field_prod">
                                 <label for="remitente" class="form-label">Remitent</label>
                                 <input class="form-control" name="remitente" id="remitente" value="<?php echo $datos->remitente;?>">
                                 <span class="mensaje_required" id="error_remitente"></span>
                              </div>

                              <div class="col-12 col-md-8 cont_field_prod">
                                 <label for="correo" class="form-label">Correu</label>
                                 <input class="form-control" name="correo" id="correo" value="<?php echo $datos->correo;?>">
                                 <span class="mensaje_required" id="error_correo"></span>
                              </div>

                              <div class="col-12 col-md-5 cont_field_prod">
                                 <label for="passwordcorreo" class="form-label">Contrasenya</label>
                                 <input class="form-control" name="passwordcorreo" id="passwordcorreo" value="<?php echo $datos->passwordcorreo;?>">
                                 <span class="mensaje_required" id="error_passwordcorreo"></span>
                              </div>                

                              <div class="col-12 col-md-5 cont_field_prod">
                                 <label for="host" class="form-label">Host</label>
                                 <input class="form-control" name="host" id="host" value="<?php echo $datos->host;?>">
                                 <span class="mensaje_required" id="error_codigopostalpiensos"></span>
                              </div>

                              <div class="col-6 col-md-2 cont_field_prod">
                                 <label for="puerto" class="form-label">Port</label>
                                 <input class="form-control" name="puerto" id="puerto" value="<?php echo $datos->puerto;?>">
                                 <span class="mensaje_required" id="error_puerto"></span>
                              </div>                              

                              <div class="col-6 col-md-4 cont_field_prod">
                                 <label for="protocolo" class="form-label">Protocol</label>
                                 <input class="form-control" name="protocolo" id="protocolo" value="<?php echo $datos->protocolo;?>">
                                 <span class="mensaje_required" id="error_protocolo"></span>
                              </div>                              


                           </div>
                           <div class="cont_button_product">
                              <button type="submit" class="button_submit btn_submit_producto" id="guardar_config_correo">Actualitzar</button>
                           </div>
                        </form>
                     </div>     

                  </div>

                  <div class="productos_category_config">
                     
                     <div class="container_configuracion">   
                        <h4>Configuració proveïdors</h4>
                        <form id="formulario_config_transportista">
                           <div class="row">

                              <div class="col-12 cont_field_prod">
                                 <label for="idtransportista" class="form-label">Transportista per defecte</label>
                                 <select class="form-control" name="idtransportista" id="idtransportista">  
                                    <option value="" selected disabled>Seleccionar</option>  
                                    <?php
                                       if(isset($datos->proveedores) && count($datos->proveedores) > 0){
                                          foreach ($datos->proveedores as $prov) {
                                             echo"<option value='".$prov->id."' ".((isset($datos->idtransportista) && $prov->id == $datos->idtransportista)? 'selected':'').">".$prov->nombrefiscal."</option>";
                                          }
                                       }
                                    ?>                                
                                 </select>    
                                 <span class="mensaje_required" id="error_idtransportista"></span>                             
                              </div>               
                              
                              
                              <div class="col-12 cont_field_prod">
                                 <label for="idproductotransp" class="form-label">Producte transportista i planificació per defecte</label>
                                 <select class="form-control" name="idproductotransp" id="idproductotransp">  
                                    <option value="" selected disabled>Seleccionar</option>  
                                    <?php
                                       if(isset($datos->productos) && count($datos->productos) > 0){
                                          foreach ($datos->productos as $prod) {
                                             echo"<option value='".$prod->id."' ".((isset($datos->idproductotransp) && $prod->id == $datos->idproductotransp)? 'selected':'').">".$prod->descripcion."</option>";
                                          }
                                       }
                                    ?>                                
                                 </select>    
                                 <span class="mensaje_required" id="error_idproductotransp"></span>                             
                              </div>  
                              

                              <div class="col-12 cont_field_prod">
                                 <label for="idprovfabrica" class="form-label">Proveïdor fàbrica per defecte</label>
                                 <select class="form-control" name="idprovfabrica" id="idprovfabrica">  
                                    <option value="" selected disabled>Seleccionar</option>  
                                    <?php
                                       if(isset($datos->proveedores) && count($datos->proveedores) > 0){
                                          foreach ($datos->proveedores as $prov) {
                                             echo"<option value='".$prov->id."' ".((isset($datos->idprovfabrica) && $prov->id == $datos->idprovfabrica)? 'selected':'').">".$prov->nombrefiscal."</option>";
                                          }
                                       }
                                    ?>                                
                                 </select>  
                                 <span class="mensaje_required" id="error_idprovfabrica"></span>                               
                              </div>            
                              
         
                              <div class="col-12 cont_field_prod">
                                 <label for="idproductofab" class="form-label">Producte fàbrica per defecte</label>
                                 <select class="form-control" name="idproductofab" id="idproductofab">  
                                    <option value="" selected disabled>Seleccionar</option>  
                                    <?php
                                       if(isset($datos->productos) && count($datos->productos) > 0){
                                          foreach ($datos->productos as $prod) {
                                             echo"<option value='".$prod->id."' ".((isset($datos->idproductofab) && $prod->id == $datos->idproductofab)? 'selected':'').">".$prod->descripcion."</option>";
                                          }
                                       }
                                    ?>                                
                                 </select>    
                                 <span class="mensaje_required" id="error_idproductofab"></span>                             
                              </div>  
                                                            
                              
                              <div class="col-12 cont_field_prod">
                                 <label for="precioprovfab" class="form-label">Preu x Tn proveïdor fàbrica per defecte</label>
                                 <input type="number" step="0.01" class="form-control" name="precioprovfab" id="precioprovfab" value="<?php echo (isset($datos->precioprovfab))? $datos->precioprovfab: 0;?>">  
                                 <span class="mensaje_required" id="error_precioprovfab"></span>
                              </div>


                           </div>
                           <div class="cont_button_product">
                              <button type="submit" class="button_submit btn_submit_producto" id="guardar_config_transportista">Actualitzar</button>
                           </div>
                        </form>
                     </div>                       

                     <div class="container_configuracion" style="background: #eaedee;border: none;"></div>

                  </div>
                  
                  <div class="productos_category_config" style="display:none;">

                     <div class="container_configuracion">   
                        <h4>Copia de seguridad de base de datos</h4>
                        <form id="formulario_copia_seguridad">
                           <div class="row">
                              <div class="col-md-12 cont_field_prod">
                                 <label for="razonsocialpiensos" class="form-label">Haga click en el cotón actualizar si desea guardar una copia de seguridad de la base de datos</label>                                 
                                 <input type="hidden" name="guardarcopia" id="guardarcopia">
                              </div>                                                 
                           </div>
                           <div class="cont_button_product">
                              <button type="submit" class="button_submit btn_submit_producto" id="guardar_copia_seguridad">Actualitzar</button>
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
