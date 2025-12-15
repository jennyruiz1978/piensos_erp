      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">    
               <!-- ****** CONTENIDO DE CADA PAGINA ****** -->                                    
               <div class="container mx-auto px-1">
                  
                  <h2 class="mr-2 mt-4">Productes</h2>                                    

                  <div class="productos_category">

                     <div class="producto_compra">   
                        <h4>Producte de compra</h4>
                        <form id="formulario_producto_compra">
                           <div class="row">
                              <div class="col-md-12 cont_field_prod">
                                 <label for="nombre_prod_compra" class="form-label">Nom producte</label>
                                 <input class="form-control" name="nombre_prod_compra" id="nombre_prod_compra" value="<?php echo $datos['productoCompra']->descripcion?>">
                                 <input type="hidden" value="1" name="id_prod_compra" id="id_prod_compra">
                              </div>
                              <div class="col-md-6 cont_field_prod">
                                 <label for="unidades_prod_compra" class="form-label">Unitats</label>
                                 <input class="form-control" id="kilos" disabled value="<?php echo $datos['productoCompra']->abrev_unidad; ?>">                           
                              </div>
                              <div class="col-6 cont_field_prod">
                                 <label for="iva_compras" class="form-label">IVA</label>
                                 <div class="block_iva">
                                    <input class="form-control" name="iva_compras" id="iva_compras" value="<?php echo $datos['productoCompra']->iva; ?>"><span>%</span>
                                 </div>                                 
                              </div>                        
                           </div>
                           <div class="cont_button_product">
                              <button type="submit" class="button_submit btn_submit_producto" id="guardar_prod_compra">Actualitzar</button>
                           </div>
                        </form>
                     </div>

                     <div class="producto_venta">
                        <h4>Producte de venda</h4>
                        <form id="formulario_producto_venta">
                           <div  class="row">
                              <div class="col-md-12 cont_field_prod">
                                 <label for="nombre_prod_venta" class="form-label">Nom producte</label>
                                 <input class="form-control" name="nombre_prod_venta" id="nombre_prod_venta" value="<?php echo $datos['productoVenta']->descripcion?>">
                                 <input type="hidden" value="1" name="id_prod_venta" id="id_prod_venta">
                              </div>
                              <div class="col-md-6 cont_field_prod">
                                 <label for="unidades_prod_venta" class="form-label">Unitats</label>
                                 <input class="form-control" id="toneladas" disabled value="<?php echo $datos['productoVenta']->abrev_unidad; ?>"> 
                              </div>
                              <div class="col-6 cont_field_prod">
                                 <label for="iva_ventas" class="form-label">IVA</label>
                                 <div class="block_iva">
                                 <input class="form-control" name="iva_ventas" id="iva_ventas" value="<?php echo $datos['productoVenta']->iva; ?>"><span>%</span>
                                 </div>                                 
                              </div> 
                           </div>
                           <div class="cont_button_product">
                              <button type="submit" class="button_submit btn_submit_producto" id="guardar_prod_venta">Actualitzar</button>
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
