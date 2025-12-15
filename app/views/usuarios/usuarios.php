      <?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Usuaris</h2>
                  
                  <dlm-tabla id="tablaUsuarios" url="<?php echo RUTA_URL."/Usuarios/tablaUsuarios"; ?>" urlEditar= "<?php echo RUTA_URL."/Usuarios/actualizarUsuario"; ?>" titulos="Num.,Nombre,Apellidos,Correu,Rol" hiddencol="[]" crud="cub" fechas=""></dlm-tabla>

                  <script>
                  </script>

               </div>
            </main>

         </div>
         
      
      
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/usuarios/formEliminarUsuario.php'); ?>
<?php require(RUTA_APP . '/views/usuarios/formEditUsuario.php'); ?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
