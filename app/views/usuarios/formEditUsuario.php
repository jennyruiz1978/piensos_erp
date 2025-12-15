<div class="modal" tabindex="-1" id="modalFormEditUsuario">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Veure / Editar usuari</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_editar_usuario">
                            <input type="hidden" id="idUsuarioEditar" name="idUsuarioEditar">
                                <div class="row">

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="nombre_usuario" class="form-label">Nom</label>
                                        <input class="form-control" name="nombre_usuario" id="nombre_usuario" value="">
                                    </div>
                                    
                                    <div class="col-md-6 cont_field_prod">
                                        <label for="apellidos_usuario" class="form-label">Cognoms</label>
                                        <input class="form-control" name="apellidos_usuario" id="apellidos_usuario" value="">
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="email_usuario" class="form-label">Correu</label>
                                        <input type="email" class="form-control" name="email_usuario" id="email_usuario" value="">
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="password_usuario" class="form-label">Contrasenya</label>
                                        <input type="text" regexp="[a-zA-Z0-9\-/*?¿¡!$_%+]{1,50}" class="form-control" name="password_usuario" id="password_usuario" value="">
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="rol_usuario" class="form-label">Rol</label>
                                        <select class="form-control" name="rol_usuario" id="rol_usuario">
                                            <option selected value="">Seleccionar</option>
                                            <option value="1">Admin</option>                                            
                                        </select>                                        
                                    </div>

                                    <div class="col-md-6 cont_field_prod">
                                        <label for="estado_usuario" class="form-label">Situació</label>
                                        <select class="form-control" name="estado_usuario" id="estado_usuario">
                                            <option selected value="">Seleccionar</option>
                                            <option value="activo">Actiu</option>
                                            <option value="inactivo">Inactiu</option> 
                                        </select>                                        
                                    </div>
                                        
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