      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Proveïdors</h2>
                  
                  <dlm-tabla id="tablaProveedores" url="<?php echo RUTA_URL."/Proveedores/tablaProveedores"; ?>" urlEditar= "<?php echo RUTA_URL."/Proveedores/exportarExcel"; ?>" titulos="Id,Nom. Fiscal,NIF,Direcció,Codi Postal,Població,Correu,Situació" hiddencol="[]" crud="cub" fechas=""></dlm-tabla>
                 
               </div>
            </main>

         </div>
         

      
         
   </div>
   <?php require(RUTA_APP . '/views/proveedores/formEliminarProveedor.php'); ?>
   <?php require(RUTA_APP . '/views/proveedores/formEditProveedor.php'); ?>


</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
