<div class="modal" tabindex="-1" id="modalFormAgregarRecibo">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Afegir rebut</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_agregar_recibo">
                            <input type="hidden" id="idFactura" name="idFactura">
                                <div class="row">                                 
                                    
                                    <div class="col-md-6 cont_field_prod">
                                        <label for="vencimiento_recibo" class="form-label">Venciment (*)</label>
                                        <input type="date" class="form-control" name="vencimiento_recibo" id="vencimiento_recibo">
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="estado_recibo" class="form-label">Estado (*)</label>
                                        <select class="form-control" name="estado_recibo" id="estado_recibo">
                                            <option value="pagado">Pagat</option>
                                            <option value="pendiente">No pagat</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="fecha_recibo" class="form-label">Data d'emissió (*)</label>
                                        <input type="date" class="form-control" name="fecha_recibo" id="fecha_recibo" value="<?php echo date('Y-m-d');?>">
                                    </div>
                                    
                                    <div class="col-md-6 cont_field_prod">
                                        <label for="lugar_recibo" class="form-label">Lloc d'expedició</label>
                                        <input class="form-control" name="lugar_recibo" id="lugar_recibo" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="importe_recibo" class="form-label">Import (*)</label>
                                        <input type="number" step="0.01" class="form-control" name="importe_recibo" id="importe_recibo" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="concepto_recibo" class="form-label">Concepte (*)</label>
                                        <textarea class="form-control" name="concepto_recibo" id="concepto_recibo"></textarea>
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="nombre_librado" class="form-label">Nom librado</label>
                                        <input class="form-control" name="nombre_librado" id="nombre_librado" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="nombre_librador" class="form-label">Nom librador</label>
                                        <input class="form-control" name="nombre_librador" id="nombre_librador" style="text-transform:uppercase;">
                                    </div>                                                      
                                        
                                </div>        

                                
                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>
                                
                                <div class="cont_button_product">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tancar</button>
                                    <button type="submit" class="button_submit">Crear</button>
                                </div>

                                
                            </form>



      </div>
     
    </div>
  </div>
</div>