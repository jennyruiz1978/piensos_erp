<div class="modal" tabindex="-1" id="modalFormEditZona">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Veure / Editar zona</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_editar_zona">
                            <input type="hidden" id="idZonaEditar" name="idZonaEditar">
                                <div class="row">

                                <div class="col-md-8 cont_field_prod">
                                        <label for="nombre_zona" class="form-label">Zona (*)</label>
                                        <input class="form-control" name="nombre_zona" id="nombre_zona" style="text-transform:uppercase;">
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="precio_zona" class="form-label">Preu (*)</label>
                                        <input type="number" step="0.01" class="form-control" name="precio_zona" id="precio_zona">
                                    </div>
                                    

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="estado_zona" class="form-label">Situació</label>
                                        <select class="form-control" name="estado_zona" id="estado_zona">
                                            <option selected value="">Seleccionar</option>
                                            <option value="activo">Actiu</option>
                                            <option value="inactivo">Inactiu</option> 
                                        </select>                                        
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