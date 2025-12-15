<div class="modal" tabindex="-1" id="modalFormEditarRecibo">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Veure rebut</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_editar_recibo">
                                <input type="hidden" id="idRecibo" name="idRecibo">
                                <!--<input name="eventoClick" id="eventoClick">-->
                                <div class="row">

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="numero_recibo" class="form-label">Número rebut (*)</label>
                                        <input type='text' regexp="[a-zA-Z0-9-/_]{1,10}" class="form-control" name="numero_recibo" id="numero_recibo" style="text-transform:uppercase;">
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="vencimiento" class="form-label">Venciment (*)</label>
                                        <input type="date" class="form-control" name="vencimiento" id="vencimiento" value="<?php echo date('Y-m-d');?>">
                                    </div>                                    
                                    
                                    <div class="col-md-6 cont_field_prod">
                                        <label for="estado" class="form-label">Estado (*)</label>
                                        <input class="form-control" name="estado" id="estado" readonly>
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

                                    <div class="col-md-12 cont_field_prod">
                                        <label for="concepto_recibo" class="form-label">Concepte (*)</label>
                                        <input class="form-control" name="concepto_recibo" id="concepto_recibo" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="nombre_librado" class="form-label">Nom librado (*)</label>
                                        <input class="form-control" name="nombre_librado" id="nombre_librado" >
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="nombre_librador" class="form-label">Nom librador (*)</label>
                                        <input class="form-control" name="nombre_librador" id="nombre_librador">
                                    </div>                                                      
                                        
                                </div>        

                                
                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>
                                
                                <div class="cont_button_product">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tancar</button>
                                    <button type="submit" class="button_submit">Actualizar</button>
                                    <a class="button_small_bar" id="recibo_pagado"><span>Pagat</span></a>
                                    <a class="button_small_bar" id="recibo_no_pagado"><span>No pagat</span></a>
                                </div>

                                
                            </form>



      </div>
     
    </div>
  </div>
</div>