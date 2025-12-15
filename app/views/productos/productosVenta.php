      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">    
               <!-- ****** CONTENIDO DE CADA PAGINA ****** -->                                    
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Productos de venta</h2>
                  
                  <dlm-tabla id="tablaProductosVenta" url="<?php echo RUTA_URL."/ProductosVenta/tablaProductosVenta"; ?>" urlEditar= "<?php echo RUTA_URL."/ProductosVenta/actualizarProductosVenta"; ?>" titulos="Id,Descripción,IVA,Unidad" hiddencol="[0]" crud="cu" fechas=""></dlm-tabla>


               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->
<?php require(RUTA_APP . '/views/productos/formEditarProducto.php'); ?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
