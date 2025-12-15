                                    <div class="col-md-6 col-lg-4 cont_field_prod">
                                        <label for="idproveedor" class="form-label label_form_grilla">Nom proveïdor (*)</label>                                        

                                        <select class="form-control" name="idproveedor" id="idproveedor">
                                            <option selected disabled value="">Seleccionar</option>
                                            <?php
                                                if(isset($datos['proveedores']) && count($datos['proveedores']) > 0){
                                                    foreach ($datos['proveedores'] as $prov) {
                                                        echo"<option value='".$prov->id."' ".((isset($datos['idproveedor']) && $prov->id == $datos['idproveedor'])? 'selected': '').">".$prov->nombrefiscal."</option>";
                                                    }
                                                }
                                            ;?>                                            
                                        </select>                                           

                                    </div>
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="nif_factura" class="form-label label_form_grilla">NIF (*)</label>
                                        <input type='text' regexp="[a-zA-Z0-9]{0,9}" class="form-control" name="nif_factura" id="nif_factura" value="<?php echo (isset($datos['nif']))? $datos['nif']:'' ;?>">
                                    </div>
                                  

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="fecha_factura_proveedor" class="form-label label_form_grilla">Data factura  (*)</label>
                                        <input type="date" class="form-control" name="fecha_factura_proveedor" id="fecha_factura_proveedor" value="<?php echo $datos['fecha'];?>">
                                    </div>

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="numero" class="form-label label_form_grilla">Número factura  (*)</label>
                                        <input type='text' regexp="[a-zA-Z0-9-/_]{1,10}" class="form-control" name="numero" id="numero" value="<?php echo $datos['numero'];?>">
                                    </div>       

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="estado" class="form-label label_form_grilla">Situació</label>
                                        <input readonly class="form-control" name="estado" id="estado" value="<?php echo $datos['estado'];?>" readonly>
                                    </div>                                        

                                 
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="dias_albaran_proveedor" class="form-label label_form_grilla">Pagament a</label>
                                        <div class="cont_dias_pago">
                                            <input class="form-control" type='text' regexp="[0-9]{0,3}" name="dias_albaran_proveedor" id="dias_albaran_proveedor" value="<?php echo $datos['diaspago'];?>"><span>dies</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="vencimiento" class="form-label label_form_grilla">Data venciment</label>
                                        <input type="date" class="form-control" name="vencimiento" id="vencimiento" value="<?php echo $datos['vencimiento'];?>">
                                    </div>

                                    
                                    <div class="col-md-6 col-lg-4 cont_field_prod">
                                        <label for="cliente_descarga" class="form-label label_form_grilla">Nom cliente (*)</label>    
                                        <input class="form-control" value="<?php echo $datos['cliente_descarga'];?>" readonly>
                                    </div>                                         
