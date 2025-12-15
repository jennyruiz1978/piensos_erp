                                    <div class="col-md-2 cont_field_prod">
                                        <label for="fecha" class="form-label label_form_grilla">Data factura Rectif.(*)</label>
                                        <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo $datos['fecha'];?>">
                                        <span class="mensaje_required" id="error_fecha"></span>
                                    </div>                                    

                                    <div class="col-md-4 cont_field_prod">
                                        <label class="form-label label_form_grilla">Nom client</label>                                        

                                        <select class="form-control" readonly>                                            
                                            <?php
                                                if(isset($datos['idcliente']) && isset($datos['cliente'])){                                                    
                                                    echo"<option value='".$datos['idcliente']."' selected>".$datos['cliente']."</option>";
                                                }else{
                                                    echo"<option selected disabled value=''>Seleccionar</option>";
                                                }
                                            ;?>                                            
                                        </select>                                           
                                        
                                    </div>
                                    
                                    <div class="col-md-2 cont_field_prod">
                                        <label  class="form-label label_form_grilla">NIF</label>
                                        <input type='text' regexp="[a-zA-Z0-9]{0,9}" class="form-control"  value="<?php echo (isset($datos['nif']))? $datos['nif']:'' ;?>" readonly>
                                        
                                    </div>
                                    
                                    <div class="col-md-2 cont_field_prod">
                                        <label class="form-label label_form_grilla">Data factura origen</label>
                                        <input type="date" class="form-control" value="<?php echo $datos['fecha'];?>" readonly>
                                        
                                    </div>

                                    <div class="col-md-2 cont_field_prod">
                                        <label for="numero" class="form-label label_form_grilla">Número factura origen </label>
                                        <input class="form-control" name="numero" id="numero" value="<?php echo $datos['numero'];?>" readonly>
                                    </div>       

                                    <div class="col-md-2 cont_field_prod">
                                        <label for="estado" class="form-label label_form_grilla">Situació</label>
                                        <input readonly class="form-control" name="estado" id="estado" value="<?php echo $datos['estado'];?>" readonly>
                                    </div>                                        

                                                                     