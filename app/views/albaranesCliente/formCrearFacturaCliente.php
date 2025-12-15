<div class="modal" tabindex="-1" id="modalFormCrearFacturaCliente">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear factura client</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <div id="loader" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
                                <img src="<?php echo RUTA_URL;?>/public/img/load-spinner.gif" alt="Cargando..." />
                            </div>

                            <form id="formulario_crear_factura_cliente">
                                <input type="hidden" id="idAlbaranCliente" name="idAlbaranCliente">
                                <div class="row">

                                    <div class="col-md-8 cont_field_prod">
                                        <label for="nombre_cliente" class="form-label">Nom fiscal (*)</label>
                                        <input class="form-control" name="nombre_cliente" id="nombre_cliente" style="text-transform:uppercase;" readonly>
                                        <span class="mensaje_required" id="error_cliente"></span>
                                    </div>
                                    
                                    <div class="col-md-4 cont_field_prod">
                                        <label for="nif_cliente" class="form-label">NIF/CIF (*)</label>
                                        <input class="form-control" name="nif_cliente" id="nif_cliente" readonly>
                                        <span class="mensaje_required" id="error_nif"></span>
                                    </div>
                                    
                                    <div class="col-md-3 cont_field_prod">
                                        <label for="fecha_factura_cliente" class="form-label">Data factura (*)</label>
                                        <input class="form-control" type="date" name="fecha_factura_cliente" id="fecha_factura_cliente">
                                        <span class="mensaje_required" id="error_fecha"></span>
                                    </div>

                                    <div class="col-md-3 cont_field_prod" style="display:none;">
                                        <label for="numero_factura_cliente" class="form-label">Nº factura (*)</label>
                                        <input class="form-control" type="text" regexp="[a-zA-Z0-9-/_]{1,10}" name="numero_factura_cliente" id="numero_factura_cliente" readonly>
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="dias_albaran_cliente" class="form-label">Cobrament a (*)</label>
                                        <div class="cont_dias_pago">
                                            <input class="form-control" type="text" regexp="[0-9]{1,3}" name="dias_albaran_cliente" id="dias_albaran_cliente"><span>dies</span></div>
                                        <?php
                                        /*<select class="form-control" name="dias_albaran_cliente" id="dias_albaran_cliente">                                        
                                        </select>*/
                                        ?>
                                        <span class="mensaje_required" id="error_diascobro"></span>
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="fecha_vencimiento_cliente" class="form-label">Venciment (*)</label>
                                        <input class="form-control" type="date" name="fecha_vencimiento_cliente" id="fecha_vencimiento_cliente">
                                        <span class="mensaje_required" id="error_vencimiento"></span>
                                    </div>
                                    
                                    <div class="col-md-3 cont_field_prod">
                                        <label for="numero_albaran_cliente" class="form-label">Albarà origen
                                        </label>
                                        <input class="form-control" name="numero_albaran_cliente" id="numero_albaran_cliente" readonly>
                                    </div>

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="" class="form-label">Forma cobrament</label>                                        
                                        <select class="form-control" name="formacobro" id="formacobro"> 
                                        </select>
                                    </div>                                    

                                    <div class="col-md-3 cont_field_prod">
                                        <label for="cuentabancaria" class="form-label">Compte bancari</label>                                        
                                        <select class="form-control" name="cuentabancaria" id="cuentabancaria">   
                                        </select>
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
                                        <label for="observaciones_factura_cliente" class="form-label">Observacions de la factura</label>
                                        <textarea class="form-control" name="observaciones_factura_cliente" id="observaciones_factura_cliente" rows="2"></textarea>                                        
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