      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

      <div class="w-full overflow-x-hidden border-t flex flex-col">

         <main class="w-full flex-grow p-6">    
            <!-- ****** CONTENIDO DE CADA PAGINA ****** -->                                    
            <div class="container mx-auto px-1 xl:px-2">
               
               <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Factures pagades - Proveïdors</h2>

               <div class='contenedor_enlaces_dashboard flex flex-wrap gap-2 pt-2 ml-3' >

                  <a href="<?php echo RUTA_URL."/Inicio/reportesFacturacionProveedoresPendientes"; ?>" class='btn_list_dw text-white px-4 py-1'>Pendents</a>

                  <a href="<?php echo RUTA_URL."/Inicio/reportesFacturacionProveedoresPagadas"; ?>" class='btn_list_dw active text-white px-4 py-1'>Pagades</a>    

                  <a href="<?php echo RUTA_URL."/Inicio/reportesFacturacionProveedoresPagadasParcial"; ?>" class='btn_list_dw text-white px-4 py-1'>Pagades Parc.</a>    

                  <a href="<?php echo RUTA_URL."/Inicio/reportesFacturacionProveedores"; ?>"  id="todasLasFacturas" class='btn_list_dw text-white px-4 py-1'>Totes</a>                                  

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
               
             

               <dlm-tabla id="tablaReportesFacturasProveedoresPagadas" url="<?php echo RUTA_URL."/Inicio/tablaReporteFacturasProveedoresPagadas"; ?>" urlEditar= "<?php echo RUTA_URL."/Inicio/exportarExcelFacturasProveedoresPagadas"; ?>" titulos="Id,Número,Proveedor,NIF,Fecha,Venciment,B.Imp.,Retenc,IVA,Total,Situació,Pagado,Por pagar" hiddencol="[0,11]" crud="" fechas="fecha" botonExcel="si"></dlm-tabla>              

            </div>
         </main>

      </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
