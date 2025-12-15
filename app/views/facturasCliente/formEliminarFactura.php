<div class="modal" tabindex="-1" id="modalFormEliminarFactura">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Eliminar factura</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <div id="loader_factura" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
                                <img src="<?php echo RUTA_URL;?>/public/img/load-spinner.gif" alt="Cargando..." />
                            </div>


                            <form id="formulario_eliminar_factura">
                              <input type="hidden" id="idFacturaEliminar" name="idFacturaEliminar">
                              <input type="hidden" id="eliminarFila" name="eliminarFila" value="0">
                                <div class="row text_pargraph_flex">
                                        <div id="mensaje_eliminar_factura">Esteu segur d'eliminar la factura?</div>
                                </div>          
                                                                
                                <div class="cont_button_product">
                                  <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal" >Tancar</button>
                                  <button type="submit" class="button_delete btn_form_eliminar">Eliminar</button>
                                </div>
                                
                            </form>

      </div>
      
    </div>
  </div>
</div>