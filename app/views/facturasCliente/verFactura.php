      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

  
         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Veure factura client</h2>            
                    
                  <div class="productos_category">

                     <div class="producto_compra productos_grilla"> 
                        
                            <div id="loader_factura" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
                                <img src="<?php echo RUTA_URL;?>/public/img/load-spinner.gif" alt="Cargando..." />
                            </div>

                        <form id="formulario_ver_factura_cli">
                           <input type="hidden" name="id" id="id" value="<?php echo $datos['idFactura'];?>">
       
                           <div class="cont_button_product">                                 
                                 <a type="submit" class="button_small_pdf" id="generar_pdf"><i class="fas fa-file-pdf"></i><span>PDF</span></a> 
                                 <a class="button_small_bar btn_enviar_email" id="enviar_factura_proveedor"><i class="far fa-envelope"></i><span>Email</span></a> 
                                 <a class="button_small_bar_sent" id="ver_emails_enviados"><i class="fa fa-envelope-open" aria-hidden="true"></i><span>Enviados</span></a> 
                           </div>

                           


                           <div class="row">

                              <?php require(RUTA_APP . '/views/facturasCliente/cabeceraEditar.php'); ?>
                                 
                           </div>



                           <div class="cont_button_product_left">
                              <a type="submit" class="button_small_bar colorverde" id="agregar_linea"><i class="fas fa-plus"></i><span>Afegeix línia</span></a> 
                           </div>                             

                           <div class="row container_products">
                           <?php require(RUTA_APP . '/views/facturasCliente/grilla_desktop.php'); ?>
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
                                                         <td class="cell_total">
                                                            <div class="fila_retencion_input">
                                                               <strong>Dscto.</strong>
                                                               <input type="text" regexp="[0-9,]{1,5}" name="descuentotipo" id="descuentotipo" value="<?php echo (isset($datos['descuentotipo']))? number_format($datos['descuentotipo'],2,',',''): 0;?>">
                                                               <strong>%.</strong>
                                                            </div>                 
                                                         </td>
                                                         <td class="cell_total_amount"><span id="descuento_importe"><?php echo (isset($datos['descuentoimporte']))? (($datos['descuentoimporte'] >0)? "- ":""). number_format($datos['descuentoimporte'],2,',',''): 0;?></span></td>                                                
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

                                 <div>                                    
                                       <a class="button_update btn_form_crear" id="submit_rectificativa">Rectificativa</a>                                                     
                                 </div>
                                       
                           </div>    
                           
                           <br><br>

                           <div class="row">

                                 <div class="col-md-6 mt-3 cont_albaranes_fact">
                                    
                                    <div class="row_title mb-3">
                                       <label class="form-label label_form_grilla mb-0">Albarans vinculats</label>
                                       <a class="button_small_invoice ver" id="ver_albaranes_factura" style="cursor:pointer;"><i class="fa fa-eye"></i></a> 
                                       <a class="button_small_invoice agregar" id="agregar_albaran_factura" style="cursor:pointer;"><i class="fa fa-plus"></i></a>
                                    </div>

                                    <div class="mb-3 texto-diferencias" id="diferencias"></div>
                                    
                                    <div id="container_albaranes" class="col-md-12 table-responsive px-0" >
                                       <table class="table table-bordered table-hover" id="tablaVerAlbaranesFactura">
                                          <thead>
                                             <tr class="thead-light">                    
                                                <th style="display:none;">idalbaran</th>
                                                <th style="display:cell;" class="text-left">Número</th>
                                                <th>Data</th>
                                                <th>Base Imp.</th>
                                                <th>IVA</th>
                                                <th>Total</th>
                                                <th>Accions</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                          </tbody>
                                       </table>
                                    </div>
                                                                  
                                 </div>    
                                 
                                 <div class="col-md-6 mt-3 cont_recibos_fact">
                                    
                                    <div class="row_title mb-3">
                                       <label class="form-label label_form_grilla mb-0">Rebuts de cobrament</label>
                                       <a class="button_small_invoice ver" id="ver_recibos_factura" style="cursor:pointer;"><i class="fa fa-eye"></i></a> 
                                       <a class="button_small_invoice agregar" id="agregar_recibo_factura" style="cursor:pointer;"><i class="fa fa-plus"></i></a>
                                    </div>
                                    
                                    <div id="container_recibos" class="col-md-12 table-responsive px-0" >
                                       <table class="table table-bordered table-hover" id="tablaVerRecibosFactura">
                                          <thead>
                                             <tr class="thead-light">                    
                                                <th style="display:none;">idfactura</th>
                                                <th style="display:cell;" class="text-left">Número</th>
                                                <th>Venc.</th>
                                                <th>Import</th>
                                                <th>Concepte</th>                                                
                                                <th>Situació</th>   
                                                <th>Accions</th>
                                             </tr>
                                          </thead>
                                          <tbody>
                                          </tbody>
                                       </table>
                                    </div>
                                                                  
                                 </div>    

                           </div>
                           


                        </form>
                        <div style="display:none;">
                           <form id="formulario_crear_factura_rectificativa" action="<?php echo RUTA_URL.'/FacturasClientes/altaFacturaRectificativa';?>" method="post">
                              <input type="hidden" name="idOrigenRectificativa" id="idOrigenRectificativa" value="<?php echo $datos['idFactura'];?>">
                           </form>
                        </div>
                     </div>

                  </div>

               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/facturasCliente/formAgregarRecibo.php'); ?>
<?php require(RUTA_APP . '/views/albaranesCliente/formEnviarDocumentoCliente.php'); ?>
<?php require(RUTA_APP . '/views/facturasCliente/formEditarRecibo.php'); ?>
<?php require(RUTA_APP . '/views/facturasCliente/formAgregarAlbaran.php'); ?>
<?php require(RUTA_APP . '/views/facturasCliente/formEnvioFacturaEmail.php'); ?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
