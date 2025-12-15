<div class="modal" tabindex="-1" id="modalFormEditCliente">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Veure / Editar client</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_editar_cliente">
                                <input type="hidden" id="idClienteEditar" name="idClienteEditar">
                                <div class="row">

                                <div class="col-md-8 cont_field_prod">
                                        <label for="nombre_cliente" class="form-label">Nom fiscal (*)</label>
                                        <input class="form-control" type='text' regexp="[a-zA-ZäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙçÇñÑ0-9\s]{1,100}" name="nombre_cliente" id="nombre_cliente" style="text-transform:uppercase;">
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="nif_cliente" class="form-label">NIF (*)</label>
                                        <input type='text' regexp="[a-zA-Z0-9]{0,9}" class="form-control" name="nif_cliente" id="nif_cliente" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="zona_cliente" class="form-label">Zona (*)</label>
                                        <select class="form-control" name="zona_cliente" id="zona_cliente">
                                            <option selected value="">Seleccionar</option>
                                            <?php
                                                if(isset($datos['zonas']) && count($datos['zonas']) > 0){
                                                    $zonas = $datos['zonas'];
                                                    foreach ($zonas as $zona) {
                                                        echo"<option value='".$zona->id."'>".$zona->zona. " - ".$zona->margen." €</option>";
                                                    }
                                                }
                                            ?>                                            
                                        </select>                                        
                                    </div>            
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="precio_cliente" class="form-label">Preu (*)</label>
                                        <input type='number' step="0.01" class="form-control" name="precio_cliente" id="precio_cliente" >
                                    </div>

                                    
                                    <div class="col-md-12 cont_field_prod">
                                        <label for="direccion_cliente" class="form-label">Direcció</label>
                                        <input class="form-control" name="direccion_cliente" id="direccion_cliente" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="poblacion_cliente" class="form-label">Població</label>
                                        <input class="form-control" name="poblacion_cliente" id="poblacion_cliente" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="codigo_postal_cliente" class="form-label">Codi postal</label>
                                        <input type='text' regexp="[0-9]{0,5}" class="form-control" name="codigo_postal_cliente" id="codigo_postal_cliente" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="provincia_cliente" class="form-label">Província</label>
                                        <input class="form-control" name="provincia_cliente" id="provincia_cliente" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="telefono_cliente" class="form-label">Telèfon</label>
                                        <input type='text' regexp="[0-9]{0,9}" class="form-control" name="telefono_cliente" id="telefono_cliente" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod" style="display: none;">
                                        <label for="email_cliente" class="form-label">Correu</label>
                                        <input type="email" class="form-control" name="email_cliente" id="email_cliente" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="estado_cliente" class="form-label">Situació</label>
                                        <select class="form-control" name="estado_cliente" id="estado_cliente">
                                            <option selected value="">Seleccionar</option>
                                            <option value="activo">Actiu</option>
                                            <option value="inactivo">Inactiu</option> 
                                        </select>                                        
                                    </div>                                                     
                                    
                                    
                                    
                                    <div class="col-md-6 cont_field_prod">
                                        <label class="form-label">Forma cobrament</label>                                              
                                        <select class="form-control" name="formacobro" id="formacobro"> 
                                                <option selected disabled value="">Seleccionar</option>
                                                <?php
                                                    if(isset($datos['formacobro']) && count($datos['formacobro']) > 0){
                                                        foreach ($datos['formacobro'] as $d) {
                                                            echo'<option value="'.$d->id.'">'.$d->formadepago.'</option>';
                                                        }
                                                    }
                                                ?>
                                        </select>                                            
                                    </div>  



                                    <div class="col-md-12 cont_field_prod">
                                        <label for="observaciones_cliente" class="form-label">Observacions</label>
                                        <textarea class="form-control" name="observaciones_cliente" id="observaciones_cliente" rows="2"></textarea>                                        
                                    </div>


                                    <!-- Agregar componente de contactos -->
                                    <div class="col-md-12 cont_field_prod">
                                        <?php include(RUTA_APP . '/views/components/contactos_component.php'); ?>
                                    </div>
                                                      
                                        
                                </div>        
                                
                                
                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>

                                <div class="cont_button_product">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tancar</button>
                                    <button type="submit" class="button_submit">Actualitzar</button>
                                </div>

                                
                            </form>



      </div>
     
    </div>
  </div>
</div>