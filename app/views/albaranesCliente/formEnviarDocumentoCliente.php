<div class="modal" tabindex="-1" id="modalFormEnviarFacturaCliente">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="title_form_enviar"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div id="spinner"></div>
                            <form id="formulario_enviar_factura_cliente">
                                <input type="hidden" id="idAlbaranClienteEmail" name="idAlbaranClienteEmail">
                                <input type="hidden" id="tipoDocumento" name="tipoDocumento" value="">

                                <div class="row">

                                    <div class="col-md-8 cont_field_prod">
                                        <label for="contactosCliente" class="form-label label_form_grilla">Contactes disponibles:</label>
                                        <select id="contactosCliente" class="form-control">
                                            <option value="">Seleccionar contacto</option>
                                        </select>
                                    </div>




                                    <div class="col-md-8 cont_field_prod">
                                        <label for="emailNuevoAgregar" class="form-label label_form_grilla">Afegir destinataris</label>
                                        <div class="container_input_email">
                                            <input class="form-control" type="email" name="emailNuevoAgregar" placeholder="introduïu correu" id="emailNuevoAgregar">
                                            <a class="signo_mas" id="btnAddNuevoDestinatario">+</a>
                                        </div>
                                    </div>

                                    <div class="col-md-12 cont_field_prod">
                                        <div class="cont_flex_email">
                                            <label class="form-label label_form_grilla">Para: </label>
                                            <div class="flex flex-wrap rounded-lg border-2 border-gray-200 bg-gray-100" id="tablaEmailsEnvioPresupuesto"></div>
                                        </div>
                                    </div>

                                    
                                    <div class="col-md-8 cont_field_prod">
                                        <div class="cont_flex_email">
                                            <label for="asunto" class="form-label label_form_grilla">Assumpte</label>
                                            <input class="form-control" name="asunto" placeholder="introduïu assumpte" id="asunto" value="">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 cont_field_prod">
                                        <div class="cont_flex_email m-2">
                                            <label class="form-label label_form_grilla">Mensaje</label>
                                            <textarea name="mensaje" id="mensaje" class="p-1 form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                                                       
                                </div>                            
                                
                                <div class="cont_button_product">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tancar</button>
                                    <button type="submit" class="button_submit">Enviar</button>
                                </div>

                                
                            </form>



      </div>
     
    </div>
  </div>
</div>