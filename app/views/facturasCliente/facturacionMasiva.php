      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

  
         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Facturació massiva clients </h2>                
                    
                  <div class="container_fact_masiva">


                     <form id="formulario_filtros_buscar_albaranes">

                        <div class="row mx-0 mb-4">                     

                           <div class="col-md-2 cont_field_prod">
                              <label for="fechainicio" class="form-label label_form_grilla">Data albarà inici </label>
                              <input type="date" class="form-control" name="fechainicio" id="fechainicio" value="<?php echo date("Y-m-d");?>">
                           </div>

                           <div class="col-md-2 cont_field_prod">
                              <label for="fechafin" class="form-label label_form_grilla">Data albarà fi </label>
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
                              <label for="estado_albaran" class="form-label label_form_grilla">Situació albarà</label>                                        

                              <select class="form-control" name="estado_albaran" id="estado_albaran">
                                 <option value='todos'>Todos</option>
                                 <option value='pendiente'>pendiente</option>
                                 <option value='facturado'>facturado</option>            
                              </select>                                           

                           </div>

                           <div class="col-md-2 cont_field_prod cont_button_search">
                              <a class="button_update" name="buscarAlbaranesCli" id="buscarAlbaranesCli">Cerca</a>
                           </div>

                        </div>
                     </form>

                     <div class="row mx-0 cont_main_fact mb-4">
                     
                        <div class="col-md-6 cont_side_fact">
                           <label class="form-label label_form_grilla">Albarans</label>
                           <div id="resultado_busqueda_alb">
                              <table class="table table-bordered table-hover" id="tablaGrillaAlbaranesFactura">
                                 <thead>
                                    <tr class="thead-light">                    
                                       <th style="display:none;">idalbaran</th>
                                       <th style="display:cell;" class="text-left">Nº albarà</th>
                                       <th>Data</th>
                                       <th>Quant.</th>
                                       <th>Unit.</th>
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
                           <label class="form-label label_form_grilla">Dades per a la factura</label>

                           <form id="formulario_crear_factura_masiva" class="cont_crear_factura_masiva">
                           
                           
                              <div id="loader_factura" style="display:none; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
                                 <img src="<?php echo RUTA_URL;?>/public/img/load-spinner.gif" alt="Cargando..." />
                              </div>

                                <div class="row">

                                    <div class="col-md-8 cont_field_prod">
                                       <label for="nif_cliente" class="form-label">Client</label>
                                       <select class="form-control" name="idcliente" id="idcliente">
                                          <option selected disabled value="">Seleccionar</option>
                                          <?php
                                             if(isset($datos['clientes']) && count($datos['clientes']) > 0){
                                                foreach ($datos['clientes'] as $prov) {
                                                   echo"<option value='".$prov->id."'>".$prov->nombrefiscal."</option>";
                                                }
                                             }
                                          ?>                                            
                                       </select>  
                                       <span class="mensaje_required" id="error_idcliente"></span>
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="nif_factura" class="form-label">NIF (*)</label>
                                        <input class="form-control" name="nif_factura" id="nif_factura" value="">
                                        <span class="mensaje_required" id="error_nif"></span>
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="fecha_factura_cliente" class="form-label">Data factura (*)</label>
                                        <input class="form-control" type="date" name="fecha_factura_cliente" id="fecha_factura_cliente" value="<?php echo date('Y-m-d');?>">
                                        <span class="mensaje_required" id="error_fecha"></span>
                                    </div>                                  

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="dias_albaran_cliente" class="form-label">Cobrament a (*)</label>
                                        <?php
                                        /*
                                        <select class="form-control" name="dias_albaran_cliente" id="dias_albaran_cliente">
                                            <option selected disabled value="">Seleccionar</option>
                                            <?php
                                                if(isset($datos['dias_cobro']) && count($datos['dias_cobro']) > 0){
                                                    foreach ($datos['dias_cobro'] as $d) {
                                                        echo'<option value="'.$d->dias.'">'.$d->dias.' dies</option>';
                                                    }
                                                }
                                            ?>
                                        </select>  
                                        */
                                        ?>
                                        <div class="cont_dias_pago">
                                            <input class="form-control" type="text" regexp="[0-9]{1,3}" name="dias_albaran_cliente" id="dias_albaran_cliente"><span>dies</span>
                                       </div>
                                        <span class="mensaje_required" id="error_diascobro"></span>
                                    </div>

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="vencimiento_cliente" class="form-label">Venciment (*)</label>
                                        <input class="form-control" type="date" name="vencimiento_cliente" id="vencimiento_cliente" value="<?php echo date('Y-m-d');?>" >
                                        <span class="mensaje_required" id="error_vencimiento"></span>                                        
                                    </div> 


                                    <div class="col-md-4 cont_field_prod">
                                        <label for="" class="form-label">Forma cobrament</label>                                        
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

                                    <div class="col-md-4 cont_field_prod">
                                        <label for="cuentabancaria" class="form-label">Compte bancari</label>                                        
                                        <select class="form-control" name="cuentabancaria" id="cuentabancaria">   
                                        <option selected disabled value="">Seleccionar</option>
                                             <?php
                                                if(isset($datos['cuentasbancarias']) && count($datos['cuentasbancarias']) > 0){
                                                    foreach ($datos['cuentasbancarias'] as $cta) {
                                                        echo'<option value="'.$cta->id.'">'.$cta->numerocuenta.'</option>';
                                                    }
                                                }
                                             ?>                                          
                                        </select>
                                    </div>



                                    <div class="col-md-12 container_detalle_factura" id="container_detalle_factura">
                                        
                                        <div id="productos" class="col-md-12 table-responsive productos_fact_modal" >
                                            <table class='table table-bordered table-hover' id='tablaGrillaFactura'>
                                                <thead>
                                                    <tr class="thead-light">                    
                                                        <th style="display:none;">idalbaran</th>
                                                        <th class="text-left">Nº albarà</th>
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

                                    <div class="col-md-12 cont_field_prod">
                                        <label for="observaciones" class="form-label">Observacions de la factura</label>
                                        <textarea class="form-control" name="observaciones" id="observaciones" rows="2"></textarea>                                        
                                    </div>                                                      
                                        
                                </div>        

                                
                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>
                                
                                <div class="cont_button_product">                                 
                                    <a class="button_update btn_form_crear" href="<?php echo RUTA_URL.'/FacturasClientes';?>">Tancar</a>
                                    <button type="submit" class="button_submit">Crear factura</button>
                                </div>
                                
                            </form>

                        </div>
                     
                     </div>

                     

                  </div>

               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
