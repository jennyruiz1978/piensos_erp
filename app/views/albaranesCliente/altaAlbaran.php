      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

  
         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4"><?php echo (($datos['existe'] == 1)? 'Edita':'Crear');?> albarà client</h2>            
                    
                  <div class="productos_category">

                     <div class="producto_compra productos_grilla">     
                                                             

                        <div id="loader_albaran" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
                           <img src="<?php echo RUTA_URL;?>/public/img/load-spinner.gif" alt="Cargando..." />
                        </div>

                        <form id="formulario_crear_albaran_cli">
                           <input type="hidden" name="id" id="id" value="<?php echo $datos['idAlbaran'];?>">
                           
                           <div class="cont_button_product">           
                                 <a type="submit" class="button_small_bar" id="generar_factura_cliente"><i class="fas fa-file-invoice-dollar"></i><span>Facturar</span></a> 
                                 <a type="submit" class="button_small_pdf" id="generar_pdf"><i class="fas fa-file-pdf"></i><span>PDF</span></a> 
                                 <a class="button_small_bar btn_enviar_email" id="enviar_factura_proveedor"><i class="far fa-envelope"></i><span>Email</span></a> 
                           </div>


                           <div class="row">

                                 <?php
                                    if($datos['existe'] == 1){
                                       require(RUTA_APP . '/views/albaranesCliente/cabeceraEditar.php');
                                    }else{
                                       require(RUTA_APP . '/views/albaranesCliente/cabeceraCrear.php');
                                    }
                                 
                                 ?>
                                 
                           </div>


                           <div class="cont_button_product_left">
                              <a type="submit" class="button_small_bar colorverde" id="agregar_linea"><i class="fas fa-plus"></i><span>Afegeix línia</span></a> 
                           </div>                           

                           <div class="row container_products">
                           <?php require(RUTA_APP . '/views/albaranesCliente/grilla_desktop.php'); ?>
                           </div>                                                                           

                           <div class="row">
                                 <div class="col-md-6 mt-3">
                                    <label for="observaciones" class="form-label label_form_grilla">Observacions</label>
                                    <textarea type="text" regexp="[a-zA-Z0-9\-/*?¿¡!$_%+]{1,50}"  class="form-control" name="observaciones" id="observaciones" rows="2"><?php echo (($datos['observaciones'])? $datos['observaciones']:'' ) ;?></textarea>                     
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

                                 <button type="submit" class="button_submit btn_form_crear" id="guardar_albaran">Guardar</button>

                                 <a class="button_update btn_form_crear" id="cerrar_albaran" href="<?php echo RUTA_URL.'/AlbaranesClientes';?>">Tancar</a>

                                 <a type="submit" class="button_delete btn_form_eliminar" id="eliminar_albaran">Eliminar</a>
                                 
                           </div>

                        </form>
                     </div>

                  </div>

               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->
<?php require(RUTA_APP . '/views/albaranesCliente/formEliminarAlbaran.php'); ?>
<?php require(RUTA_APP . '/views/albaranesCliente/formCrearFacturaCliente.php'); ?>
<?php require(RUTA_APP . '/views/albaranesCliente/formEnviarDocumentoCliente.php'); ?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
