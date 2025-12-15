      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

  
         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Crear factura rectificativa client</h2>          
                    
                  <div class="productos_category">

                     <div class="producto_compra productos_grilla">                               
                        <form id="formulario_crear_factura_rectificativa">
                           <input type="hidden" name="idFacturaOrigen" id="idFacturaOrigen" value="<?php echo $datos['idFacturaOrigen'];?>">
                           <input type="hidden" name="id" id="id" value="<?php echo (isset($datos['idFacturaRectificativa']) && $datos['idFacturaRectificativa'] > 0)? $datos['idFacturaRectificativa']: '';?>">

                           <div class="row">

                              <?php require(RUTA_APP . '/views/facturasCliente/cabeceraRectificativa.php'); ?>
                                 
                           </div>

                           <div class="row container_products">
                           <?php require(RUTA_APP . '/views/facturasCliente/grilla_rectificativa.php'); ?>
                           </div>                                                                           

                           
                           <div class="row">
                                 <div class="col-md-6 mt-3">
                                    <label for="observaciones" class="form-label label_form_grilla">Observacions</label>
                                    <textarea type="text" regexp="[a-zA-Z0-9\-/*?¿¡!$_%+]{1,50}"  class="form-control" name="observaciones" id="observaciones" rows="2"></textarea>                     
                                 </div>
                                 <div class="col-md-6 mt-3">
                                    <div class="container_totales">
                                       <div class="cont_table_totales">
                                             <table class="table table-sm table-bordered">                 
                                                   <tr>                                            
                                                         <td class="cell_total"><strong>Base Imp.</strong></td>
                                                         <td class="cell_total_amount"><span id="baseimponible_importe"><?php echo (isset($datos['baseimponible']))? number_format($datos['baseimponible'],2,',',''): 0;?></span></td>
                                                   </tr>       
                                                   <tr>                                            
                                                         <td class="cell_total"><strong>IVA</strong></td>
                                                         <td class="cell_total_amount"><span id="ivatotal_importe"><?php echo (isset($datos['ivatotal']))? number_format($datos['ivatotal'],2,',',''): 0;?></span></td>                                                
                                                   </tr>       
                                                   <tr>                                            
                                                         <td class="cell_total"><strong>Total</strong></td>
                                                         <td class="cell_total_amount"><span id="total_importe"><?php echo (isset($datos['total']))? number_format($datos['total'],2,',',''): 0;?></span></td>
                                                   </tr>                                        
                                                </tbody>
                                             </table>
                                       </div>
                                    </div>  
                                 </div>
                           </div>


                           <div class="row pt-3">
                                 <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                           </div>

                           <div class="cont_button_product">

                                 <button type="submit" class="button_submit btn_form_crear" id="guardar_factura">Guardar</button>

                                 <a class="button_update btn_form_crear" href="<?php echo RUTA_URL.'/FacturasClientes';?>">Tancar</a>
                                
                                       
                           </div>    
                           
                           <br><br>

                        


                        </form>
                        
                     </div>

                  </div>

               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/facturasCliente/formAgregarRecibo.php'); ?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
