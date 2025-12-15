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
                                        <span class="mensaje_required" id="error_cliente"></span>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="nif_factura" class="form-label label_form_grilla">NIF (*)</label>
                                        <input type='text' regexp="[a-zA-Z0-9]{0,9}" class="form-control" name="nif_factura" id="nif_factura" value="<?php echo (isset($datos['nif']))? $datos['nif']:'' ;?>">
                                        <span class="mensaje_required" id="error_nif"></span>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="fecha" class="form-label label_form_grilla">Data factura  (*)</label>
                                        <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo $datos['fecha'];?>">
                                        <span class="mensaje_required" id="error_fecha"></span>
                                    </div>

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="numero" class="form-label label_form_grilla">Número factura </label>
                                        <input class="form-control" name="numero" id="numero" value="<?php echo $datos['numero'];?>" readonly>
                                    </div>       

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="estado" class="form-label label_form_grilla">Situació</label>
                                        <input readonly class="form-control" name="estado" id="estado" value="<?php echo $datos['estado'];?>" readonly>
                                    </div>                                        

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="dias" class="form-label label_form_grilla">Cobrament a (*)</label>
                                        <div class="cont_dias_pago">
                                            <input class="form-control" type="text" regexp="[0-9]{1,3}" name="dias" id="dias" value="<?php echo $datos['diascobro'];?>"><span>dies</span></div>
                                        <span class="mensaje_required" id="error_diascobro"></span>
                                        
                                    </div>  

                                    <div class="col-md-6 col-lg-2 cont_field_prod">
                                        <label for="vencimiento" class="form-label label_form_grilla">Venciment (*)</label>
                                        <input class="form-control" type="date" name="vencimiento" id="vencimiento" value="<?php echo $datos['vencimiento'];?>">
                                        <span class="mensaje_required" id="error_vencimiento"></span>
                                    </div>

                                    <div class="col-md-6 col-lg-3 cont_field_prod">
                                        <label for="" class="form-label label_form_grilla">Forma cobrament</label>      
                                        


                                        <div class="container_formacobro" id="container_formacobro">
                                            <select class="form-control" name="formacobro" id="formacobro"> 
                                                <option selected disabled value="">Seleccionar</option>
                                                <?php
                                                    if(isset($datos['formacobro']) && count($datos['formacobro']) > 0){
                                                        foreach ($datos['formacobro'] as $d) {
                                                            echo'<option value="'.$d->id.'" '.(($d->id==$datos['idformacobro'])? "selected":"").'>'.$d->formadepago.'</option>';
                                                        }
                                                    }
                                                ?>
                                            </select>
                                            <?php
                                                if($datos['idformacobro'] == 2){                                             
                                            ?>
                                            <label id="etiqueta_alavista">A la vista</label>
                                            <?php
                                                }
                                            ?>
                                        </div>                                                                                 

                                    </div>                                    

                                    <div class="col-md-6 col-lg-3 cont_field_prod">
                                        <label for="cuentabancaria" class="form-label label_form_grilla">Compte bancari</label>                                        
                                        <select class="form-control" name="cuentabancaria" id="cuentabancaria">   
                                        <option selected disabled value="">Seleccionar</option>
                                        <?php
                                                if(isset($datos['cuentasbancarias']) && count($datos['cuentasbancarias']) > 0){
                                                    foreach ($datos['cuentasbancarias'] as $cta) {
                                                        echo'<option value="'.$cta->id.'" '.(($cta->id==$datos['idcuentabancaria'])? "selected":"").'>'.$cta->numerocuenta.'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>                                    