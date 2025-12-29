      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Fitxers de clients CSV </h2>                
                    
                  <div class="container_fact_masiva">

                     <form id="formulario_filtros_buscar_facturas">

                        <div class="row mx-0 mb-4">                     

                           <div class="col-md-2 cont_field_prod">
                              <label for="fechainicio" class="form-label label_form_grilla">Data factura inici </label>
                              <input type="date" class="form-control" name="fechainicio" id="fechainicio" value="<?php echo date("Y-m-d");?>">
                           </div>

                           <div class="col-md-2 cont_field_prod">
                              <label for="fechafin" class="form-label label_form_grilla">Data factura fi </label>
                              <input type="date" class="form-control" name="fechafin" id="fechafin" value="<?php echo date("Y-m-d");?>">
                           </div>                        

                           <div class="col-md-4 cont_field_prod">
                              <label for="idclientesearch" class="form-label label_form_grilla">Client</label>                                        

                              <select class="form-control" name="idclientesearch" id="idclientesearch">
                                 <option selected disabled value="">Seleccionar</option>
                                 <?php
                                    if(isset($datos['clientes']) && count($datos['clientes']) > 0){
                                       foreach ($datos['clientes'] as $cli) {
                                          echo"<option value='".$cli->id."'>".$cli->nombrefiscal."</option>";
                                       }
                                    }
                                 ?>                                            
                              </select>                                           

                           </div>
                        
                           <div class="col-md-2 cont_field_prod">
                              <label for="estado_factura" class="form-label label_form_grilla">Situació factura</label>                                        

                              <select class="form-control" name="estado_factura" id="estado_factura">
                                 <option value='todos'>Todos</option>
                                 <option value='exportada'>Exportada</option>           
                                 <option value='pendiente de exportar'>Pendiente de exportar</option>           
                              </select>                                           

                           </div>

                           <div class="col-md-2 cont_field_prod cont_button_search">
                              <a class="button_update" name="buscarfacturaCli" id="buscarfacturaCli">Cerca</a>
                           </div>

                        </div>
                     </form>

                     <div class="row mx-0 cont_main_fact mb-4">
                     
                        <div class="col-md-6 cont_side_fact">
                           <label class="form-label label_form_grilla">Facturas</label>
                           <div id="resultado_busqueda_alb">
                              <table class="table table-bordered table-hover" id="tablaGrillaFacturaPrincipal">
                                 <thead>
                                    <tr class="thead-light">                    
                                       <th style="display:none;">idFactura</th>
                                       <th style="display:cell;" class="text-left">Nº factura</th>
                                       <th>Data</th>
                                       <th>Total</th>
                                       <th>Situació</th>
                                       <th>Venciment</th>
                                       <th>Acciones</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        
                        <div class="col-md-6 cont_side_fact">
                           <label class="form-label label_form_grilla">Factures a exportar</label>

                           <form id="formulario_crear_factura_masiva" class="cont_crear_factura_masiva">
                           
                           
                              <div id="loader_factura" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
                                 <img src="<?php echo RUTA_URL;?>/public/img/load-spinner.gif" alt="Cargando..." />
                              </div>

                                <div class="row">

                                    

                                    <div class="col-md-12 container_detalle_factura" id="container_detalle_factura">
                                        
                                        <div id="productos" class="col-md-12 table-responsive productos_fact_modal" >
                                            <table class='table table-bordered table-hover' id='tablaGrillaFactura'>
                                                <thead>
                                                    <tr class="thead-light">                    
                                                        <th style="display:none;">idFactura</th>
                                                        <th class="text-left">Nº factura</th>
                                                        <th>Data</th>
                                                        <th>Quant.</th>
                                                         <th>Unit.</th>               
                                                        <th>Total</th> 
                                                        <th>Accions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>                                                

                                    </div>

                                    
                                </div>        

                               
                                
                                <div class="cont_button_product">                                 
                                    <a class="button_update btn_form_crear" href="<?php echo RUTA_URL.'/FacturasClientes';?>">Tancar</a>
                                    <button type="submit" class="button_submit">Exportar CSV</button>
                                </div>
                                
                            </form>

                        </div>
                     
                     </div>   

                  </div>

               </div>
            </main>

         </div>
         
   </div>

</main>

<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
