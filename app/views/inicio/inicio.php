      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

      <div class="w-full overflow-x-hidden border-t flex flex-col">

         <main class="w-full flex-grow p-6">    
            <!-- ****** CONTENIDO DE CADA PAGINA ****** -->                                    
            <div class="container mx-auto px-1 xl:px-2">
               
               <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Inicio</h2>
               
               <?php 
                  $fechaInicio = new DateTime();
                  $fechaInicio->modify('first day of this month');
                  $fechaIni = $fechaInicio->format('Y-m-d');
                     
                  $fechaFinal = new DateTime();
                  $fechaFinal->modify('last day of this month');
                  $fechaFin = $fechaFinal->format('Y-m-d');                                       
               ?>    
               

               <div class="container_search_fechas_dashboard p-2">
                            
                  <div class="grids_search_fechas_dash">
                      <label class="uppercase md:text-sm text-xs text-gray-500 font-semibold">Desde</label>
                      <input type="date" id="fechaIni" class="p-1 rounded-button border-2 border-gray-200 mt-1 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent" value="<?php echo $fechaIni;?>">
                  </div>
                
                  <div class="grids_search_fechas_dash">
                     <label class="uppercase md:text-sm text-xs text-gray-500 font-semibold">Hasta</label>
                      <input type="date" id="fechaFin" class="p-1 rounded-button border-2 border-gray-200 mt-1 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:border-transparent" value="<?php echo $fechaFin;?>">
                  </div>
                                   
                  
                  <div class="grids_search_fechas_dash button">
                     <a class="button_dashboard px-3 py-1" id="buscarfechasDashboard"><i class="fa fa-search" aria-hidden="true"></i><span>Buscar</span></a>                       
                  </div>    

                  
                  <div class="grids_search_fechas_dash button">
                     <a class="button_dashboard px-3 py-1" id="generar_pdf_dashboard"><i class="fas fa-file-pdf"></i><span>Exportar</span></a>                       
                  </div>                            
                                                                                    
               </div>               

               <div class="mx-2 p-3 container_flex_dashboard">  

                  <div class="container_cli_pro">
                     <h4>Proveedores</h4>
                     <div class="container_flex_pills">
                        <div class="container_pills">
                           <div class="pill">

                              <div class="pill_only albaranados border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">Albaranados</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="total_kilos_albaranados"><?php echo $datos['total_kilos_albaranados'];?> <i class="far fa-file-alt"></i></span>
                                            <span id="total_euros_albaranados"><?php echo $datos['total_euros_albaranados'];?> €</span>                                            
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>

                           </div>
                        </div>
                        <div class="container_pills_tres">
                           <div class="pill">
                              <div class="pill_only facturados border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">Facturados</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="kilos_facturados"><?php echo $datos['kilos_facturados'];?> <i class="far fa-file-alt"></i></span>
                                            <span id="euros_facturados"><?php echo $datos['euros_facturados'];?> €</span>                                            
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                              
                           </div>

                           <div class="pill">
                              <div class="pill_only iva border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">IVA facturado</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="euros_ivafacturado"><?php echo $datos['euros_ivafacturado'];?> €</span>                                            
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>

                           <div class="pill">
                              <div class="pill_only sinfacturar border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">Sin facturar</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="kilos_sin_facturar"><?php echo $datos['kilos_sin_facturar'];?> <i class="far fa-file-alt"></i></span>              
                                            <span id="euros_sin_facturar"><?php echo $datos['euros_sin_facturar'];?> €</span>                                            
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>
                           
                        </div>

                        <div class="container_pills_tres">
                           
                           <div class="pill">
                              <div class="pill_only pagadas border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600" title="Facturas pagadas">F. Pagadas</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="euros_pagados"><?php echo $datos['euros_pagados'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>
                        
                           <div class="pill">
                              <div class="pill_only pagadasparc border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600" title="Facturas c/pago parcial">F. Pagadas parc.</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="euros_pago_parcial"><?php echo $datos['euros_pago_parcial'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>
                           
                           <div class="pill">
                              <div class="pill_only pendientes border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600" title="Facturas pendientes de pago">F. Pendientes</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="num_facturas_pago_pendiente"><?php echo $datos['num_facturas_pago_pendiente'];?> <i class="far fa-file-alt"></i></span>
                                            <span id="euros_pago_pendiente"><?php echo $datos['euros_pago_pendiente'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>
                                                      
                        </div>

                        <div class="container_pills">                           
                           <div class="pill">
                              <div class="pill_only vencidas border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">Recibo vencidos</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <!--<span id="num_facturas_vencidas"><?php //echo $datos['num_facturas_vencidas'];?> <i class="far fa-file-alt"></i></span>-->
                                            <span id="euros_vencidos"><?php echo $datos['euros_vencidos'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>

                        </div>
                     </div>
                  </div>

                  <div class="container_cli_pro">
                     <h4>Clientes</h4>
                     <div class="container_flex_pills">
                        <div class="container_pills">
                           <div class="pill">

                              <div class="pill_only albaranados border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">Albaranados</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="total_kilos_albaranados_cli"><?php echo $datos['total_kilos_albaranados_cli'];?> <i class="far fa-file-alt"></i></span>
                                            <span id="total_euros_albaranados_cli"><?php echo $datos['total_euros_albaranados_cli'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>

                           </div>
                        </div>
                        <div class="container_pills_tres">
                           <div class="pill">
                              <div class="pill_only facturados border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">Facturados</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="kilos_facturados_cli"><?php echo $datos['kilos_facturados_cli'];?> <i class="far fa-file-alt"></i></span>
                                            <span id="euros_facturados_cli"><?php echo $datos['euros_facturados_cli'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                              
                           </div>

                           <div class="pill">
                              <div class="pill_only iva border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">IVA facturado</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="euros_ivafacturado_cli"><?php echo $datos['euros_ivafacturado_cli'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>

                           <div class="pill">
                              <div class="pill_only sinfacturar border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">Sin facturar</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="kilos_sin_facturar_cli"><?php echo $datos['kilos_sin_facturar_cli'];?> <i class="far fa-file-alt"></i></span>
                                            <span id="euros_sin_facturar_cli"><?php echo $datos['euros_sin_facturar_cli'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>
                           
                        </div>

                        <div class="container_pills_tres">
                           
                           <div class="pill">
                              <div class="pill_only pagadas border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600" title="Facturas cobradas">F. Cobradas</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="euros_pagados_cli"><?php echo $datos['euros_pagados_cli'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>
                        
                           <div class="pill">
                              <div class="pill_only pagadasparc border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600" title="Facturas c/cobro parcial">F. Cobradas parc.</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="euros_pago_parcial_cli"><?php echo $datos['euros_pago_parcial_cli'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>
                           
                           <div class="pill">
                              <div class="pill_only pendientes border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600" title="Facturas pendientes de cobro">F. Pendientes</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <span id="num_facturas_pago_pendiente_cli"><?php echo $datos['num_facturas_pago_pendiente_cli'];?> <i class="far fa-file-alt"></i></span>
                                            <span id="euros_pago_pendiente_cli"><?php echo $datos['euros_pago_pendiente_cli'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>
                                                      
                        </div>

                        <div class="container_pills">                           
                           <div class="pill">
                              <div class="pill_only vencidas border-b-4 border-green rounded-lg" style="cursor:pointer">
                                    <div class="flex items-center">
                                      <div class="flex-auto text-center">
                                          <h5 class="text-sm 2xl:text-base font-semibold text-gray-600">Recibos vencidos</h5>
                                          <h3 class="content-pill font-bold xs:text-sm lg:text-lg">
                                            <!--<span id="num_facturas_vencidas_cli"><?php //echo $datos['num_facturas_vencidas_cli'];?> <i class="far fa-file-alt"></i></span>-->
                                            <span id="euros_vencidos_cli"><?php echo $datos['euros_vencidos_cli'];?> €</span>
                                          </h3>
                                      </div>                          
                                    </div>
                              </div>                                                   
                           </div>

                        </div>
                     </div>
                  </div>                 

               </div>


            </div>
         </main>

      </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
