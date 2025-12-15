<?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Crear planificació</h2>   
                 
                    <div class="planificacion_main">

                        <div class="container_form_crear_planning">                               
                            <form id="formulario_crear_planificacion">
                                <input type="hidden" id="idPlanificacion" value="<?php echo $datos['idPlanificacion'];?>">

                                <div class="row container_fechas">

                                    <div class="col-12 col-xl-2 cont_field_prod">
                                        <label for="semana" class="form-label" style="font-weight: 700;">Semana</label> 
                                        <input class="form-control" id="semana" readonly value="<?php echo $datos['semana'];?>">                                        
                                    </div>

                                    <div class="col-12 col-xl-4 cont_field_prod">
                                        <label for="nombre_producto_compra" class="form-label" style="font-weight: 700;">Producto</label> 
                                        <input class="form-control" readonly value="<?php echo $datos['nombreProducto'];?>">                                       
                                        <input type="hidden" name="id_producto_compra" id="id_producto_compra" value="<?php echo $datos['idProducto'];?>">
                                    </div>

                                    <div class="col-6 col-md-3 cont_field_prod">
                                        <label for="fecha_inicio" class="form-label" style="font-weight: 700;">Data inici</label>
                                        <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio" value="<?php ;?>">
                                    </div>    
                                    
                                    <div class="col-6 col-md-2 btn_calcular_fechas">
                                        <a class="button_update button_add" id="calcular_fechas">Calcular dates</a>
                                    </div>

                                    <!--<div class="col-md-2 cont_field_prod">
                                        <label for="precio_semana_plan" class="form-label label_input_nb" style="font-weight: 700;">Preu setmana</label>
                                        <div class="input_precio_plan">
                                            <input class="form-control input_price_no_border" type="number" name="precio_semana_plan" id="precio_semana_plan" value="<?php //echo (isset($datos['detalles']->precio))? $datos['detalles']->precio: 0 ;?>">
                                            <a style="cursor: pointer;color: #007bff;" id="btn_actualizar_precio">Guardar</a>
                                        </div>
                                        <div class="msgErrores" id="msgErrores_precio"></div>
                                    </div>-->
                                    

                                </div>

                                        
                                <div class="cont_button_product_left pl-3" id="container_agregar_fila" style="display:none;">
                                    <a type="submit" class="button_small_bar colorverde" id="agregar_linea"><i class="fas fa-plus"></i><span>Afegeix línia</span></a> 
                                </div>     

                                
                                <div class="row pl-3" id="container_fechas_cantidades_recojos">

                                </div>

                                <div class="row pl-3 pt-3" id="container_totales" style="display:none;">
                                    <div class="total_box"><span>Total: </span> <span id="suma_total"></span> <span></span></div>
                                </div>
    
                 

                                <div class="cont_button_product_left pl-3">
                                    <a class="button_descartar btn_form_crear" data-accion="crear" style="cursor:pointer;" id="descartar_planificacion">Descartar</a>
                                    <a class="button_update btn_form_crear" id="cerrar_planificacion"  >Tancar</a>
                                </div>

                            </form>
                        </div>
        
                    </div>

               </div>
            </main>

         </div>
         
   </div>

</main> <!--Esta etiqueta Main es el fin del sidebar -->

<?php require(RUTA_APP . '/views/planificaciones/formEliminarPlanificacion.php'); ?>
<?php require(RUTA_APP . '/views/includes/footer.php'); ?>
