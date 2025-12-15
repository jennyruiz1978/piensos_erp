<div class="modal" tabindex="-1" id="modalFormAgregarAlbaranFactura">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Afegir albarà</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

                            <form id="formulario_buscar_albaranes_factura">
                                
                                <input type="hidden" id="idFacturaAgregarAlbaran" name="idFacturaAgregarAlbaran">

                                <div class="row">                                 
                                        
                                    <div class="col-md-2 cont_field_prod">
                                        <label for="fechainicio" class="form-label label_form_grilla">Data albarà inici </label>
                                        <input type="date" class="form-control" name="fechainicio" id="fechainicio" value="<?php echo date("Y-m-d");?>">
                                    </div>

                                    <div class="col-md-2 cont_field_prod">
                                        <label for="fechafin" class="form-label label_form_grilla">Data albarà fi </label>
                                        <input type="date" class="form-control" name="fechafin" id="fechafin" value="<?php echo date("Y-m-d");?>">
                                    </div>                                                          
                                    
                                    <div class="col-md-2 cont_field_prod">
                                        <label for="estado_albaran" class="form-label label_form_grilla">Situació albarà</label>                                        

                                        <select class="form-control" name="estado_albaran" id="estado_albaran">                                            
                                            <option value='pendiente'>pendiente</option>                                            
                                        </select>                                           

                                    </div>

                                    <div class="col-md-2 cont_field_prod cont_button_search">
                                        <a class="button_update" name="buscarAlbaranesParaFactura" id="buscarAlbaranesParaFactura">Cerca</a>
                                    </div>
                                    
                                        
                                </div>     

                            </form>
                                
                            <form id="formulario_agregar_albaranes" class="cont_crear_factura_masiva">

                                <input type="hidden" id="idFacturaEnviarAlbaran" name="idFacturaEnviarAlbaran">

                                <div class="row">                                 
                                        
                                    <div class="col-12 col-xl-6">

                                        <label class="form-label label_form_grilla">Albarans encontrados</label>
                                        <div id="albaranes_buscados_container" class="table-responsive">
                                            <table class="table table-bordered table-hover" id="tablaGrillaAlbaranesFacturaBuscar">
                                                <thead>
                                                    <tr class="thead-light">                    
                                                    <th style="display:none;">idalbaran</th>
                                                    <th style="display:cell;" class="text-left">Nº albarà</th>
                                                    <th>Data</th>
                                                    <th>Base Imp.</th>
                                                    <th>IVA</th>
                                                    <th>Total</th>
                                                    <th>Situació</th>
                                                    <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>   

                                    </div>

                                    <div class="col-12 col-xl-6">
                                        
                                        <label class="form-label label_form_grilla">Albarans seleccionados</label>
                                        <div id="albaranes_seleccionados_container" class="col-md-12 table-responsive productos_fact_modal" >
                                            <table class='table table-bordered table-hover' id='tablaGrillaFacturaSeleccionados'>
                                                <thead>
                                                    <tr class="thead-light">                    
                                                        <th style="display:none;">idalbaran</th>
                                                        <th class="text-left">Nº albarà</th>
                                                        <th>Data</th>
                                                        <th>B.Imp.</th>
                                                        <th>IVA</th>                
                                                        <th>Total</th> 
                                                        <th>Accions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>  

                                    </div>
                                    

                                </div>                                

                                <div class="cont_button_product">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tancar</button>
                                    <button type="submit" class="button_submit">Afegir albarans</button>
                                </div>

                            </form>
                            



      </div>
     
    </div>
  </div>
</div>