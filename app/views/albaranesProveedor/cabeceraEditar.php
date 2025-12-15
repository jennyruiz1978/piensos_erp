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
                                        <span class="mensaje_required" id="error_idproveedor"></span>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="nif_albaran" class="form-label label_form_grilla">NIF</label>
                                        <input type='text' regexp="[a-zA-Z0-9]{0,9}" class="form-control" name="nif_albaran" id="nif_albaran" value="<?php echo (isset($datos['nifProveedor']))? $datos['nifProveedor']:'' ;?>">
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="fecha" class="form-label label_form_grilla">Data albarà  (*)</label>
                                        <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo $datos['fecha'];?>">
                                        <span class="mensaje_required" id="error_fecha"></span>
                                    </div>

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="numero" class="form-label label_form_grilla">Número albarà  (*)</label>
                                        <input type='text' class="form-control"  id="numero" value="<?php echo $datos['numero'];?>" readonly>                                        
                                    </div>       

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="estado" class="form-label label_form_grilla">Situació</label>
                                        <input readonly class="form-control" name="estado" id="estado" value="<?php echo $datos['estado'];?>">
                                        
                                    </div>    

                                    <?php
                                        if($datos['numFactura'] && $datos['numFactura'] != ''){                                        
                                    ?>
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="numeroFactura" class="form-label label_form_grilla">Número factura </label>
                                        <input class="form-control" value="<?php echo $datos['numFactura'];?>" readonly>
                                    </div>       
                                    <?php
                                        }
                                    ?>

                                    <div class="col-md-6 col-lg-4 cont_field_prod">
                                        <label for="cliente_descarga" class="form-label label_form_grilla">Nom cliente (*)</label>    
                                        <input class="form-control" value="<?php echo $datos['cliente_descarga'];?>" readonly>
                                    </div>                                         
