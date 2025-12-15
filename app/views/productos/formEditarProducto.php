<div class="modal" tabindex="-1" id="modalFormEditarProducto">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_editar_producto">
                                <input type="hidden" id="id" name="id" value="">

                                <div class="row">

                                
                                    <div class="col-md-12 cont_field_prod">
                                        <label for="descripcion" class="form-label">Nom producte</label>
                                        <input class="form-control" type="text" 
                                        regexp="[a-zA-ZäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙçÇñÑ0-9\s]{1,100}" name="descripcion" id="descripcion">
                                        <span class="mensaje_required" id="error_descripcion"></span>
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="idunidad" class="form-label">Unitat</label>                                        
                                        <select class="form-control" name="idunidad" id="idunidad">
                                            <option selected disabled value="">Seleccionar</option>
                                            <?php
                                                if(isset($datos['unidades']) && count($datos['unidades']) > 0){
                                                    foreach ($datos['unidades'] as $unidad) {
                                                        echo"<option value='".$unidad->id."'>".$unidad->descripcion."</option>";
                                                    }
                                                }
                                            ;?>                                            
                                        </select> 
                                        <span class="mensaje_required" id="error_idunidad"></span>
                                    </div>

                                    <div class="col-6 cont_field_prod">
                                        <label for="iva" class="form-label">IVA</label>

                                        <select class="form-control" name="iva" id="iva">
                                            <option selected disabled value="">Seleccionar</option>
                                            <?php
                                                if(isset($datos['tiposiva']) && count($datos['tiposiva']) > 0){
                                                    foreach ($datos['tiposiva'] as $tipo) {
                                                        echo"<option value='".$tipo->tipo."'>".$tipo->tipo."</option>";
                                                    }
                                                }
                                            ;?>                                            
                                        </select>                                                

                                        <span class="mensaje_required" id="error_iva"></span>                              
                                    </div>         
                                    
                                    
                                    
                                    <div class="col-12 cont_field_prod" id="cont_prod_compra" style="display:none;">
                                        <label for="idproductocompra" class="form-label">Associar producte de compra</label>

                                        <select class="form-control" name="idproductocompra" id="idproductocompra">
                                            <option selected disabled value="">Seleccionar</option>
                                            <?php
                                                if(isset($datos['productos_compra']) && count($datos['productos_compra']) > 0){
                                                    foreach ($datos['productos_compra'] as $tipo) {
                                                        echo"<option value='".$tipo->id."'>".$tipo->descripcion."</option>";
                                                    }
                                                }
                                            ;?>                                            
                                        </select>                                                

                                        <span class="mensaje_required" id="error_idproductocompra"></span>                              
                                    </div>     
                                        
                                </div>        
                                
                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>
                                
                                <div class="cont_button_product">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tancar</button>
                                    <button type="submit" class="button_submit">Guardar</button>
                                </div>

                                
                            </form>



      </div>
     
    </div>
  </div>
</div>