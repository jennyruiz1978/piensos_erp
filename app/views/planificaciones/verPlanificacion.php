<?php require(RUTA_APP . '/views/includes/header.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/navbar.php'); ?> 
      <?php require(RUTA_APP . '/views/includes/sidebar.php'); 
      
     
      ?> 

         <div class="w-full overflow-x-hidden border-t flex flex-col">

            <main class="w-full flex-grow p-6">                                             
               <div class="container mx-auto px-1 xl:px-2">
                  
                  <h2 class="text-2xl font-semibold leading-tight flex-1 mr-2 p-2 mt-4">Editar planificació</h2>                  
                    
                    <div class="planificacion_main">

                        <div class="container_form_crear_planning">          
                            
                            <div id="loader_planificacion" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
                                <img src="<?php echo RUTA_URL;?>/public/img/load-spinner.gif" alt="Cargando..." />
                            </div>
                            
                            <form id="formulario_crear_planificacion">
                                <input type="hidden" id="idPlanificacion" value="<?php echo $datos['idPlanificacion'];?>">

                                <div class="row container_fechas">

                                    <div class="col-12 col-xl-2 cont_field_prod">
                                        <label for="semana" class="form-label" style="font-weight: 700;">Semana</label> 
                                        <input class="form-control" id="semana" readonly value="<?php echo $datos['detalles']->semana;?>">                                        
                                    </div>                                

                                    <div class="col-12 col-xl-4 cont_field_prod">
                                        <label for="nombre_producto_compra" class="form-label" style="font-weight: 700;">Producto</label> 
                                        <input class="form-control" readonly value="<?php echo $datos['nombreProducto'];?>">                                       
                                        <input type="hidden" name="id_producto_compra" id="id_producto_compra" value="<?php echo $datos['idProducto'];?>">
                                    </div>

                                    <div class="col-6 col-md-3 cont_field_prod">
                                        <label for="fecha_inicio" class="form-label" style="font-weight: 700;">Data inici</label>
                                        <input class="form-control" type="date" <?php echo ($datos['tieneDatos'] > 0)? 'disabled':'' ;?>  name="fecha_inicio" id="fecha_inicio" value="<?php echo $datos['detalles']->fechainicio ;?>">
                                    </div>
                                    
                                    
                                    <?php
                                        if($datos['tieneDatos'] == 0){
                                    ?>
                                    <div class="col-6 col-md-2 btn_calcular_fechas">
                                        <a class="button_update button_add" id="calcular_fechas">Calcular dates</a>
                                    </div>
                                    <?php
                                        }
                                    ?>                             

                                </div>

                                <div class="row mb-4 items-center">
                                    <div class="col-10 col-xl-11">
                                        <label for="comentario_planificacion" class="form-label" style="font-weight: 700;">Comentaris</label>
                                        <textarea 
                                            name="comentario_planificacion" 
                                            id="comentario_planificacion" 
                                            class="form-control" 
                                            rows="2" 
                                            placeholder="Escriu un comentari..."><?php echo $datos['detalles']->comentario ?? ''; ?></textarea>
                                    </div>

                                    <div class="col-2 col-xl-1 d-flex align-items-end justify-content-center" id="btnlisto">
                                        <button type="button" id="btn_confirmar_accion" class="hover:scale-105 transition-transform">
                                            <img src="<?php echo RUTA_URL;?>/public/img/comprobado.png" alt="Confirmar" style="width: 45px; height: 45px;">
                                        </button>
                                    </div>
                                </div>
                                        
                                <div class="cont_button_product_left pl-3" id="container_agregar_fila">
                                    <a class="button_small_bar colorverde" id="agregar_linea"><i class="fas fa-plus"></i><span>Afegeix línia</span></a> 
                                </div>     
                                
                                <div class="contenedor-planificacion-relativo">
                                    
                                    <a href="<?php echo ($datos['idAnterior']) ? RUTA_URL.'/Planificaciones/verPlanificacion/'.$datos['idAnterior'] : 'javascript:void(0)'; ?>" 
                                    id="btn_scroll_izq" 
                                    class="btn-nav flecha-navegacion flecha-izq <?php echo (!$datos['idAnterior']) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                    data-mensaje="No hi ha cap planificació anterior.">
                                        <img src="<?php echo RUTA_URL;?>/public/img/flecha-izquierda.png" alt="Anterior">
                                    </a>

                                    <div class="row pl-3" id="container_fechas_cantidades_recojos">
                                        <?php echo $datos['html'];?>    
                                    </div>

                                    <a href="<?php echo ($datos['idSiguiente']) ? RUTA_URL.'/Planificaciones/verPlanificacion/'.$datos['idSiguiente'] : 'javascript:void(0)'; ?>" 
                                    id="btn_scroll_der" 
                                    class="btn-nav flecha-navegacion flecha-der <?php echo (!$datos['idSiguiente']) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                    data-mensaje="No hi ha cap planificació següent.">
                                        <img src="<?php echo RUTA_URL;?>/public/img/flecha-derecha.png" alt="Siguiente">
                                    </a>

                                </div>

                                <div class="row pl-3 pt-3" id="container_totales">
                                    <div class="total_box"><span>Total: </span> <span id="suma_total"><?php echo $datos['detalles']->total;?></span> <span><?php echo $datos['unidad']; ?></span></div>
                                </div>

                                <div class="cont_button_product_left pl-3">
                                    <a class="button_descartar btn_form_crear" data-accion="editar" style="cursor:pointer;" id="descartar_planificacion">Descartar</a>
                                    <a class="button_update btn_form_crear" id="cerrar_planificacion" >Tancar</a>
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
