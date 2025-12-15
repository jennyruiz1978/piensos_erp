      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">    
               <!-- ****** CONTENIDO DE CADA PAGINA ****** -->                                    
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Proveedores / Facturas</h2>
                  
                  <dlm-tabla id="tablaFacturasProveedores" url="<?php echo RUTA_URL."/FacturasProveedores/tablaFacturasProveedor"; ?>" urlEditar= "<?php echo RUTA_URL."/FacturasProveedores/exportarExcel"; ?>" titulos="Id,Número,Proveedor,NIF,Fecha,B.Imp.,Retenc.,IVA,Total,Situació" hiddencol="[0]" crud="ub" fechas=""></dlm-tabla>


               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/facturasProveedor/formEliminarFactura.php'); ?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
