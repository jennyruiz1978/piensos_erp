<div id="productos" class="col-md-12 table-responsive" >
    <table class='table table-bordered table-hover' id='tablaGrilla'>
        <thead>
            <tr class="thead-light">                    
                <th style="display:none;">Lin</th>
                <th style="display:cell;" class="text-left">Codi</th>
                <!--<th class="text-left">Descripció</th>-->
                <th>Quantitat</th>
                <th>Unitat</th>
                <th>Preu</th>                
                <th>Total</th>
                <th>%Iva</th>
                <!--<th>Acciones</th>-->
            </tr>
        </thead>
        <tbody id="tablaGrillaBody">

        <?php
        
            if($datos['existe'] == 1){

               print($datos['html']);

            }else{
        ?>

            <tr class="thead-light">                        
                                
                <td style="display:none;">
                    <input class="shortWidthField inputGrillaAuto numeroOrden" name="numeroOrden[]" id="numeroOrden1" value="1" readonly>
                </td>

                <td style="display:cell;">
                    <select class="shortWidthField inputGrillaAuto articulo" name="idArticulo[]" id="idArticulo1" data-idorden="1">
                        <option value="" disabled selected>Seleccionar</option>
                        <?php
                            if(isset($datos['productos']) && count($datos['productos']) > 0){
                                foreach ($datos['productos'] as $producto) {
                                    echo"<option value='".$producto->id."' ".(($datos['productDefault'] && $producto->id == $datos['productDefault']->id)? 'selected':'' ).">".$producto->id." - ".$producto->descripcion."</option>";   
                                }                                
                            }
                        ?>                            
                    </select>                        
                </td>

                <!--<td class="celdaDescripcion">
                        <textarea type="text" name="descripcion[]" id="descripcion1" class="largeWidthField inputGrillaDescripcion dblClickInput" readonly><?php //echo (($datos['productDefault'])? $datos['productDefault']->descripcion: '') ;?></textarea>
                </td>-->

                <td>
                    <input type="number" class="shortWidthField2 inputGrillaAuto cantidad dblClickInput" name="cantidadArticulo[]" id="cantidadArticulo1" step="0.01" value="0">
                </td>

                <td>
                    <input type="text" class="shortWidthField2 inputGrillaAuto unidad dblClickInput" name="unidadArticulo[]" id="unidadArticulo1" value="<?php echo (($datos['productDefault'])? $datos['productDefault']->abrev_unidad: '') ;?>" readonly>
                </td>

                <td>
                    <input type="number" class="shortWidthField2 inputGrillaAuto precio dblClickInput" name="precioArticulo[]" id="precioArticulo1" step="0.01" value="<?php echo ((isset($datos['precioDefault']))? $datos['precioDefault']: 0) ;?>">
                </td>                    

                <td>
                    <input type="number" class="shortWidthField2 inputGrillaAuto totalLinea dblClickInput" step="0.01" name="totalLinea[]" id="totalLinea1" value="0" readonly>
                </td>                                

                <td class="lineaIva">
                    <select class="inputGrillaAuto iva" name="iva[]" id="iva1">
                        <option value='' selected disabled></option>
                    <?php
                        if(isset($datos['tiposIva']) && count($datos['tiposIva']) > 0){                                
                            foreach ($datos['tiposIva'] as $tipo) {
                                echo"<option value='".$tipo->tipo."' ".(($datos['productDefault'] && $tipo->tipo == $datos['productDefault']->iva)? 'selected': '').">".$tipo->tipo." %</option>";
                            }
                        }
                    ?>                            
                    </select>
                </td>

            </tr>

        <?php
                
            }
        
        ?>
                            

        </tbody>
    </table>
</div>