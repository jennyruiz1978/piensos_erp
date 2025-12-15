      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

      <div class="w-full overflow-x-hidden border-t flex flex-col">

         <main class="w-full flex-grow p-6">    
            <!-- ****** CONTENIDO DE CADA PAGINA ****** -->                                    
            <div class="container mx-auto px-1 xl:px-2">
               
               <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Factures cobrades - Clients</h2>

               <div class='contenedor_enlaces_dashboard flex flex-wrap gap-2 pt-2 ml-3' >

                  <a href="<?php echo RUTA_URL."/Inicio/reportesFacturacionClientesPendientes"; ?>" class='btn_list_dw text-white px-4 py-1'>Pendents</a>

                  <a href="<?php echo RUTA_URL."/Inicio/reportesFacturacionClientesCobradas"; ?>" class='btn_list_dw active text-white px-4 py-1'>Cobrades</a>    

                  <a href="<?php echo RUTA_URL."/Inicio/reportesFacturacionClientesCobradasParcial"; ?>" class='btn_list_dw text-white px-4 py-1'>Cobrades Parc.</a>    

                  <a href="<?php echo RUTA_URL."/Inicio/reportesFacturacionClientes"; ?>"  id="todasLasFacturas" class='btn_list_dw text-white px-4 py-1'>Totes</a>                                  

               </div>               

               <?php 

                  $fechaInicio = new DateTime();
                  $fechaInicio->modify('first day of this month');
                  $fechaIni = $fechaInicio->format('Y-m-d');
                  
                     
                  $fechaFinal = new DateTime();
                  $fechaFinal->modify('last day of this month');
                  $fechaFin = $fechaFinal->format('Y-m-d');                                       
               ?>               


               <input type="hidden" id="fechaIniDashboardSession" value="<?php echo $fechaIni;?>">
               <input type="hidden" id="fechaFinDashboardSession" value="<?php echo $fechaFin;?>">
               
             
               <div id="container_tablas_clientes">
                  <dlm-tabla id="tablaReportesFacturasClientesCobradas" url="<?php echo RUTA_URL."/Inicio/tablaReporteFacturasClientesCobradas"; ?>" urlEditar= "<?php echo RUTA_URL."/Inicio/exportarExcelFacturasClientesCobradas"; ?>" titulos="Id,Número,Cliente,NIF,Fecha,Venciment,B.Imp.,Dscto,IVA,Total,Situació,Cobrado,Por cobrar" hiddencol="[0,11]" crud="" fechas="fecha" botonExcel="si"></dlm-tabla>              
               </div>

               

            </div>
         </main>

      </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
