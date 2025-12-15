<div class="modal" tabindex="-1" id="modalFormEditarCuenta">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_editar_cuenta">
                                <input type="hidden" id="id" name="id" value="">

                                <div class="row">

                                
                                    <div class="col-md-7 cont_field_prod">
                                        <label for="numerocuenta" class="form-label">Número de compte</label>
                                        <input class="form-control" type="text" 
                                        regexp="[a-zA-Z0-9]{0,24}" name="numerocuenta" id="numerocuenta">
                                        <span class="mensaje_required" id="error_numerocuenta"></span>
                                    </div>

                                    <div class="col-md-5 cont_field_prod">
                                        <label for="banco" class="form-label">Banc</label>
                                        <input class="form-control" type="text" 
                                        regexp="[a-zA-ZäÄëËïÏöÖüÜáéíóúáéíóúÁÉÍÓÚÂÊÎÔÛâêîôûàèìòùÀÈÌÒÙçÇñÑ0-9\s]{1,50}" name="banco" id="banco">
                                        <span class="mensaje_required" id="error_banco"></span>
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