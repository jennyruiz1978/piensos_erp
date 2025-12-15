      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">    
               <!-- ****** CONTENIDO DE CADA PAGINA ****** -->                                    
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Clients / Rebuts</h2>
                  
                  <div id="loader_recibo" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
                     <img src="<?php echo RUTA_URL;?>/public/img/load-spinner.gif" alt="Cargando..." />
                  </div>

                  <dlm-tabla id="tablaRecibosClientes" url="<?php echo RUTA_URL."/RecibosClientes/tablaRecibosCliente"; ?>" urlEditar= "<?php echo RUTA_URL."/RecibosClientes/exportarExcel"; ?>" titulos="Id,Número,Data Venc.,Importe,Quant.,Nº Factura,Cliente,Estado" hiddencol="[0]" crud="ubp" fechas=""></dlm-tabla>


               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/recibosCliente/formEditarRecibo.php'); ?>
<?php require(RUTA_APP . '/views/albaranesCliente/formEnviarDocumentoCliente.php'); ?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
