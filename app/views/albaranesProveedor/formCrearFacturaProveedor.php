<div class="modal" tabindex="-1" id="modalFormCrearFacturaProveedor">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear factura proveïdor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_crear_factura_proveedor">
                                <input type="hidden" id="idAlbaranProveedor" name="idAlbaranProveedor">
                                <div class="row">

                                    <div class="col-md-8 cont_field_prod">
                                        <label for="nombre_proveedor" class="form-label">Nom fiscal (*)</label>
                                        <input class="form-control" name="nombre_proveedor" id="nombre_proveedor" style="text-transform:uppercase;" readonly>
                                        <span class="mensaje_required" id="error_proveedor"></span>
                                        
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="nif_proveedor" class="form-label">NIF (*)</label>
                                        <input class="form-control" name="nif_proveedor" id="nif_proveedor" readonly>
                                        <span class="mensaje_required" id="error_nif"></span>
                                    </div>
                                    
     
                                    <div class="col-md-3 cont_field_prod">
                                        <label for="numero_albaran_proveedor" class="form-label">Albarà origen
                                        </label>
                                        <input class="form-control" name="numero_albaran_proveedor" id="numero_albaran_proveedor" readonly>
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="fecha_factura_proveedor" class="form-label">Data factura (*)</label>
                                        <input class="form-control" type="date" name="fecha_factura_proveedor" id="fecha_factura_proveedor">
                                        <span class="mensaje_required" id="error_fecha"></span>
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="numero_factura_proveedor" class="form-label">Nº factura (*)</label>
                                        <input class="form-control" type="text" regexp="[a-zA-Z0-9-/_]{1,10}" name="numero_factura_proveedor" id="numero_factura_proveedor">                                        
                                        <span class="mensaje_required" id="error_numfactura"></span>
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="dias_albaran_proveedor" class="form-label">Pagament a (*)</label>
                                        <div class="cont_dias_pago">
                                            <input class="form-control"  type='text' regexp="[0-9]{0,3}" name="dias_albaran_proveedor" id="dias_albaran_proveedor"><span>dies</span>
                                        </div>
                                        <span class="mensaje_required" id="error_diaspago"></span>
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="vencimiento" class="form-label">Data venciment (*)</label>
                                        <input class="form-control" type="date" name="vencimiento" id="vencimiento">
                                        <span class="mensaje_required" id="error_vencimiento"></span>
                                    </div>
                               

                                    <div class="col-md-12 container_detalle_factura" id="container_detalle_factura">
                                        
                                        <div id="productos" class="col-md-12 table-responsive productos_fact_modal" >
                                            <table class='table table-bordered table-hover' id='tablaGrillaFactura'>
                                                <thead>
                                                    <tr class="thead-light">                    
                                                        <th style="display:none;">Lin</th>
                                                        <th class="text-left">Descripció</th>
                                                        <th>Quantitat</th>
                                                        <th>Unitat</th>
                                                        <th>Preu</th>                
                                                        <th>Total</th>
                                                        <th>%Iva</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>                                                

                                    </div>



                                    <div class="col-md-12 cont_field_prod">
                                        <label for="observaciones_factura_proveedor" class="form-label">Observacions de la factura</label>
                                        <textarea class="form-control" name="observaciones_factura_proveedor" id="observaciones_factura_proveedor" rows="2"></textarea>                                        
                                    </div>
                                                      
                                        
                                </div>        

                                
                                <div class="row pt-3">
                                    <div class="col-md-12" style="font-size:0.8rem;">(*) Camps obligatoris</div>                                
                                </div>
                                
                                <div class="cont_button_product">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tancar</button>
                                    <button type="submit" class="button_submit">Crear factura</button>
                                </div>

                                
                            </form>



      </div>
     
    </div>
  </div>
</div>