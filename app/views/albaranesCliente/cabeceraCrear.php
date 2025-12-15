
                                    <div class="col-md-6 col-lg-4 cont_field_prod">
                                        <label for="idcliente" class="form-label label_form_grilla">Nom client (*)</label>                                        

                                        <select class="form-control" name="idcliente" id="idcliente">
                                            <option selected disabled value="">Seleccionar</option>
                                            <?php
                                                if(isset($datos['clientes']) && count($datos['clientes']) > 0){
                                                    foreach ($datos['clientes'] as $cli) {
                                                        echo"<option value='".$cli->id."' ".((isset($datos['idcliente']) && $cli->id == $datos['idcliente'])? 'selected': '').">".$cli->nombrefiscal."</option>";
                                                    }
                                                }
                                            ;?>                                            
                                        </select>                                           
                                        <span class="mensaje_required" id="error_idcliente"></span>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="nif_albaran" class="form-label label_form_grilla">NIF/CIF</label>
                                        <input type='text' regexp="[a-zA-Z0-9]{0,9}" class="form-control" name="nif_albaran" id="nif_albaran" value="<?php echo (isset($datos['nifCliente']))? $datos['nifCliente']:'' ;?>">
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="fecha" class="form-label label_form_grilla">Data albarà  (*)</label>
                                        <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo $datos['fecha'];?>">
                                        <span class="mensaje_required" id="error_fecha"></span>
                                    </div>

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="numero" class="form-label label_form_grilla">Número albarà </label>
                                        <input type='text' regexp="[a-zA-Z0-9-/_]{1,10}" class="form-control" name="numero" id="numero" value="" readonly>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="zona" class="form-label label_form_grilla">Zona</label>
                                        <input readonly class="form-control" id="zona" readonly>
                                    </div>  

                                             
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="margen" class="form-label label_form_grilla">Cost</label>
                                        <input readonly class="form-control" id="margen" readonly>
                                    </div>  
