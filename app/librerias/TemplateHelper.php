<?php

class TemplateHelper {
        

    public static function buildGridRows($rowsObject, $datos, $tipoDoc='')
    {

        $html = '';
        if($rowsObject && count($rowsObject) > 0){
            $cont=0;
            foreach ($rowsObject as $key) {
                $cont++;
                $html.='

                    <tr class="thead-light" id="fila_grilla_id_'.$cont.'">              
                        
                        <td style="display:none;">
                            <input class="shortWidthField inputGrillaAuto numeroOrden" name="numeroOrden[]" id="numeroOrden'.$cont.'" value="'.$cont.'" readonly>
                            <input name="idFila[]" id="idFila'.$cont.'" value="'.$key->id.'" readonly>
                        </td>';

                        if($tipoDoc =='albaran'){
                            $html.='
                            <td style="display:cell;">
                                <select class="shortWidthField inputGrillaAuto articulo" name="idArticulo[]" id="idArticulo'.$cont.'">
                                    <option value="" disabled selected></option>';
                                    
                                        if(isset($datos['productos']) && count($datos['productos']) > 0){
                                            foreach ($datos['productos'] as $producto) {
                                                $html.="<option value='".$producto->id."' ".(($producto->id == $key->idproducto)? 'selected':'' ).">".$producto->id." - ".$producto->descripcion."</option>";   
                                            }                                
                                        }
                                        $html.='
                                </select>                        
                            </td>';

                        }else if($tipoDoc =='factura_cliente'){
                            $html.='
                            <td class="celdaDescripcion">
                                <div class="cont_prod_del">
                                    
                                    <select class="shortWidthField inputGrillaAuto articulo" name="idArticulo[]" id="idArticulo'.$cont.'">
                                        <option value="" disabled selected></option>';
                                        
                                            if(isset($datos['productos']) && count($datos['productos']) > 0){
                                                foreach ($datos['productos'] as $producto) {
                                                    $html.="<option value='".$producto->id."' ".(($producto->id == $key->idproducto)? 'selected':'' ).">".$producto->id." - ".$producto->descripcion."</option>";   
                                                }                                
                                            }
                                            $html.='
                                    </select> 
                                    
                                    <span class="eliminar_fila" data-idfila="'.$cont.'">x</span>
                                </div>
                            </td>';
                                                    
                        }else if($tipoDoc =='factura' || $tipoDoc =='facturanegativa'){
                            $html.='
                            <td class="celdaDescripcion">
                                    <textarea type="text" name="descripcion[]" id="descripcion'.$cont.'" class="largeWidthField inputGrillaDescripcion dblClickInput" readonly>'. $key->descripcion .'</textarea>
                            </td>';
                        }

                        
                        if($tipoDoc == 'facturanegativa'){
                            $html.='<td><div class="cont_negativo"><span class="signo_negativo"> - </span><input type="number" class="shortWidthField2 inputGrillaAuto cantidad dblClickInput" name="cantidadArticulo[]" id="cantidadArticulo'.$cont.'" value="'.abs($key->cantidad).'" step="0.01"></td>';
                        
                        }else{
                            $html.='<td><input type="number" class="shortWidthField2 inputGrillaAuto cantidad dblClickInput" name="cantidadArticulo[]" id="cantidadArticulo'.$cont.'" value="'.$key->cantidad.'" step="0.01"></td>';
                        }                   
                        
                        $html.='
                        <td>
                            <input type="text" class="shortWidthField2 inputGrillaAuto unidad dblClickInput" name="unidadArticulo[]" id="unidadArticulo'.$cont.'" value="'.$key->unidad.'" readonly>
                        </td>
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto precio dblClickInput" name="precioArticulo[]" id="precioArticulo'.$cont.'" value="'.$key->precio.'" step="0.01">
                        </td>                    
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto totalLinea dblClickInput" step="0.01" name="totalLinea[]" id="totalLinea'.$cont.'" value="'.$key->subtotal.'" readonly>
                        </td>                                
                        
                        <td class="lineaIva">
                            <select class="inputGrillaAuto iva" name="iva[]" id="iva'.$cont.'">
                                <option value="" selected disabled></option>';

                                if(isset($datos['tiposIva']) && count($datos['tiposIva']) > 0){                                
                                    foreach ($datos['tiposIva'] as $tipo) {
                                        $html.="<option value='".$tipo->tipo."' ".(($tipo->tipo == $key->ivatipo)? 'selected': '').">".$tipo->tipo." %</option>";
                                    }
                                }
                                $html.='                          
                            </select>
                        </td>
            
                    </tr>';

            }
        }

        return $html;
    
    }

    public static function buildGridDeliveryNotes($rowsObject){

        
        $html = '';
        if($rowsObject && count($rowsObject) > 0){           
            foreach ($rowsObject as $key) {
                
                $html.='

                    <tr class="thead-light" id="fila_alb_fact_ver_'.$key->id.'">              
                        
                        <td style="display:none;">'.$key->id.'</td>

                        <td>'.$key->numero.'</td>
                        
                        <td>'.date("d/m/Y",strtotime($key->fecha)).'</td>
                                                
                        <td>'.number_format($key->baseimponible, 2, ",", ".").'</td>

                        <td>'.number_format($key->ivatotal, 2, ",", ".").'</td>

                        <td>'.number_format($key->total, 2, ",", ".").'</td>    
                        
                        <td>
                            <div class="d-flex" style="justify-content: center;">

                                <a class="button_small_add_row eliminar_alb_fact" data-idalbaran="'.$key->id.'" style="cursor:pointer;" >Eliminar</a>

                            </div> 
                        </td>    

            
                    </tr>';

            }
        } else{
            $html .= 'No hi ha resultats per a la cerca';    
        }               
        return $html;                
    }    
    
    public static function buildGridDeliveryNotesSearch($rowsObject){
        
        $html = '
        ';
        if($rowsObject && count($rowsObject) > 0){           
            foreach ($rowsObject as $key) {
                
                $html.='

                    <tr class="thead-light" id="fila_alb_'.$key->id.'">              
                        
                        <td style="display:none;">'.$key->id.'</td>

                        <td>'.$key->numero.'</td>
                        
                        <td>'.date("d/m/Y",strtotime($key->fecha)).'</td>
                                                
                        <td>'.number_format($key->baseimponible, 2, ",", ".").'</td>

                        <td>'.number_format($key->ivatotal, 2, ",", ".").'</td>

                        <td>'.number_format($key->total, 2, ",", ".").'</td>   
                        
                        <td>'.($key->estado).'</td>  
                        
                        <td>
                            <div class="d-flex" style="justify-content: center;">';
                            
                            if($key->estado == 'pendiente'){
                                $html .= '<a class="button_small_add_row agregar_alb_fact" data-idalbaran="'.$key->id.'">Afegir</a>';
                            }

                        $html .='
                            </div> 
                        </td>    

            
                    </tr>';
                    ///<i class="fa fa-plus"></i>

            }
        } else{
            $html .= 'No hi ha resultats per a la cerca';    
        }       
        $html .= '';

        return $html;                
    }    

    public static function buildGridDeliveryNotesSearchModified($rowsObject){
        
        $html = '
        ';
        if($rowsObject && count($rowsObject) > 0){           
            foreach ($rowsObject as $key) {
                
                $html.='

                    <tr class="thead-light" id="fila_alb_'.$key->id.'">              
                        
                        <td style="display:none;">'.$key->id.'</td>

                        <td>'.$key->numero.'</td>
                        
                        <td>'.date("d/m/Y",strtotime($key->fecha)).'</td>
                                                
                        <td>'.number_format($key->sumacantidad, 2, ",", ".").'</td>

                        <td>'.$key->unidad.'</td>

                        <td>'.number_format($key->total, 2, ",", ".").'</td>   
                        
                        <td>'.($key->estado).'</td>  
                        
                        <td>'.(!empty($key->vencimiento) ? date("d/m/Y", strtotime($key->vencimiento)) : '').'</td> 
                        
                        <td>
                            <div class="d-flex" style="justify-content: center;">';
                            
                            if($key->estado == 'pendiente'){
                                $html .= '<a class="button_small_add_row agregar_alb_fact" data-idalbaran="'.$key->id.'">Afegir</a>';
                            }

                        $html .='
                            </div> 
                        </td>    

            
                    </tr>';
                    ///<i class="fa fa-plus"></i>

            }
        } else{
            $html .= 'No hi ha resultats per a la cerca';    
        }       
        $html .= '';

        return $html;                
    } 
    
    public static function buildGridDeliveryNotesToInvoice($rowsObject){
        
        $html = '
        ';
        if($rowsObject){                       
                
                $html.='

                    <tr class="thead-light fila_alb_inv" id="fila_alb_inv_'.$rowsObject->id.'" data-idalbaran="'.$rowsObject->id.'">
                        
                        <td style="display:none;"><input type="hidden" name ="idAlbaranSelected[]" value="'.$rowsObject->id.'"></td>

                        <td>'.$rowsObject->numero.'</td>
                        
                        <td>'.date("d/m/Y",strtotime($rowsObject->fecha)).'</td>
                                                
                                                  
                        <td>'.number_format($rowsObject->sumacantidad, 2, ",", ".").'</td>

                        <td>'.$rowsObject->unidad.'</td>

                        <td>'.number_format($rowsObject->total, 2, ",", ".").'</td>    
                        
                        <td>
                            <div class="d-flex" style="justify-content: center;">
                                <a class="button_small_add_row eliminar_alb_fact" data-idalbaran="'.$rowsObject->id.'">Eliminar</a>
                            </div> 
                        </td>    

            
                    </tr>';                    

            
        } else{
            $html .= 'No hi ha resultats per a la cerca';    
        }       
        $html .= '';

        return $html;                
    }    
    
    public static function buildGridReceipt($rows, $tipo=false){

        
        $html = '';
        if($rows && count($rows) > 0){           
            foreach ($rows as $key) {
                
                $html.='

                    <tr class="thead-light" id="fila_recibo_fact_ver_'.$key->id.'">              
                        
                        <td style="display:none;">'.$key->id.'</td>

                        <td>'.$key->numero.'</td>
                        
                        <td>'.((isset($key->vencimiento))? date("d/m/Y",strtotime($key->vencimiento)): '' ).'</td>
                                                
                        <td>'.number_format($key->importe, 2, ",", ".").'</td>

                        <td>'.$key->concepto.'</td>                       
                        
                        <td>'.$key->estadoactual.'</td>
                        
                        <td>
                            <div class="d-flex" style="justify-content: center;">';


                            if($tipo == 'cliente'){
                                $html.='
                                <a class="button_small_click_receipt ver_recibo_cliente" data-idrecibo="'.$key->id.'" style="cursor:pointer;">Ver</a>';
                            }                            

                            $html.='
                                <a class="button_small_add_row eliminar_recibo_fact" data-idrecibo="'.$key->id.'" style="cursor:pointer;">Eliminar</a>

                            </div> 
                        </td>    

            
                    </tr>';

            }
        } else{
            $html .= 'No hi ha resultats per a la cerca';    
        }               
        return $html;                
    }    

    public static function buildGridRowsDeliveryNotesSuppliers($rowsObject, $datos, $tipoDoc='')
    {

        $html = '';
        if($rowsObject && count($rowsObject) > 0){
            $cont=0;                  

            foreach ($rowsObject as $key) {
                $cont++;
                $html.='

                    <tr class="thead-light" id="fila_grilla_id_'.$cont.'">              
                        
                        <td style="display:none;">
                            <input class="shortWidthField inputGrillaAuto numeroOrden" name="numeroOrden[]" id="numeroOrdendata-idorden="${filaOrden}"" value="'.$cont.'" readonly>';

                        if($tipoDoc =='albaran' || $tipoDoc =='factura'){
                            $html.='
                            <input name="idFila[]" id="idFila'.$cont.'" value="'.$key->id.'" readonly>';
                        }
                        $html.='
                        </td>';                      

                        if($tipoDoc =='albaran'){
                            $html.='
                            <td style="display:cell;">
                                <div class="cont_prod_del">
                                    <select class="shortWidthField inputGrillaAuto articulo" data-idorden="'.$cont.'" name="idArticulo[]" id="idArticulo'.$cont.'">
                                        <option value="" disabled selected></option>';                                    
                                            if(isset($datos['productos']) && count($datos['productos']) > 0){
                                                foreach ($datos['productos'] as $producto) {
                                                    $html.="<option value='".$producto->id."' ".(($producto->id == $key->idproducto)? 'selected':'' ).">".$producto->id." - ".$producto->descripcion."</option>";   
                                                }                                
                                            }
                                            $html.='
                                    </select>
                                    <span class="eliminar_fila" data-idfila="'.$cont.'">x</span>
                                </div>                                      
                            </td>';
                        }

                        if($tipoDoc =='factura'){
                        
                            $html.='
                            <td class="celdaDescripcion">
                                <textarea type="text" name="descripcion[]" id="descripcion'.$cont.'" class="largeWidthField inputGrillaDescripcion dblClickInput" readonly>'. $key->descripcion .'</textarea>
                            </td>';
                        }
                        
                        if($tipoDoc =='factura_cliente'){                        
                                $html.='
                                <td class="celdaDescripcion">
                                    <textarea type="text" name="descripcion[]" id="descripcion'.$cont.'" class="largeWidthField inputGrillaDescripcion dblClickInput" readonly rows="1">'. $key->descripcion .'</textarea>
                                    <input type="hidden" name="idArticulo[]" id="idArticulo'.$cont.'" value="'. $key->idproducto .'">
                                </td>';
                        }
                        
                        
                        
                        $html.='
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto cantidad dblClickInput" name="cantidadArticulo[]" id="cantidadArticulo'.$cont.'" value="'.$key->cantidad.'" step="0.01">
                        </td>
                        
                        <td>
                            <input type="text" class="shortWidthField2 inputGrillaAuto unidad dblClickInput" name="unidadArticulo[]" id="unidadArticulo'.$cont.'" value="'.$key->unidad.'" readonly>
                        </td>
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto precio dblClickInput" name="precioArticulo[]" id="precioArticulo'.$cont.'" value="'.$key->precio.'" step="0.01">
                        </td>                    
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto totalLinea dblClickInput" step="0.01" name="totalLinea[]" id="totalLinea'.$cont.'" value="'.$key->subtotal.'" readonly>
                        </td>                                
                        
                        <td class="lineaIva">
                            <select class="inputGrillaAuto iva" name="iva[]" id="iva'.$cont.'" >
                                <option value="" selected disabled></option>';

                                if(isset($datos['tiposIva']) && count($datos['tiposIva']) > 0){                                
                                    foreach ($datos['tiposIva'] as $tipo) {
                                        $html.="<option value='".$tipo->tipo."' ".(($tipo->tipo == $key->ivatipo)? 'selected': '').">".$tipo->tipo." %</option>";
                                    }
                                }
                                $html.='                          
                            </select>
                        </td>
            
                    </tr>';

            }
        }

        return $html;
    
    }

    public static function buildGridRowsInvoiceFromDeliveryNotes($rowsObject, $tipoDoc='')
    {
        
        $html = '';
        if($rowsObject && count($rowsObject) > 0){
            $cont=0;                  

            foreach ($rowsObject as $key) {
                $cont++;
                $html.='

                    <tr class="thead-light" id="fila_grilla_id_'.$cont.'">              
                        
                        <td style="display:none;">
                            <input class="shortWidthField inputGrillaAuto numeroOrden" id="numeroOrden" name="numeroOrden[]" value="'.$cont.'" readonly>';                        
                        $html.='
                        </td>';                   
                        
                        if($tipoDoc =='factura_cliente' || $tipoDoc =='factura_proveedor'){
                        
                                $html.='
                                <td class="celdaDescripcion">
                                    <textarea type="text" id="descripcion'.$cont.'" class="largeWidthField inputGrillaDescripcion dblClickInput" readonly rows="1">'. $key->descripcion .'</textarea>
                                    <input type="hidden" id="idArticulo'.$cont.'" value="'. $key->idproducto .'" readonly>
                                </td>';
                        }
                        
                        
                        
                        $html.='
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto cantidad dblClickInput" id="cantidadArticulo'.$cont.'" value="'.$key->cantidad.'" step="0.01" readonly>
                        </td>
                        
                        <td>
                            <input type="text" class="shortWidthField2 inputGrillaAuto unidad dblClickInput" id="unidadArticulo'.$cont.'" value="'.$key->unidad.'" readonly>
                        </td>
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto precio dblClickInput"  id="precioArticulo'.$cont.'" value="'.$key->precio.'" step="0.01" readonly>
                        </td>                    
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto totalLinea dblClickInput" step="0.01" id="totalLinea'.$cont.'" value="'.$key->subtotal.'" readonly>
                        </td>                                
                        
                        <td class="lineaIva">
                            <select class="inputGrillaAuto iva"  id="iva'.$cont.'" >
                                <option value="'.$key->ivatipo.'">'.$key->ivatipo.' %</option>';

                                /*
                                if(isset($datos['tiposIva']) && count($datos['tiposIva']) > 0){                                
                                    foreach ($datos['tiposIva'] as $tipo) {
                                        $html.="<option value='".$tipo->tipo."' ".(($tipo->tipo == $key->ivatipo)? 'selected': '').">".$tipo->tipo." %</option>";
                                    }
                                }*/
                                $html.='                          
                            </select>
                        </td>
            
                    </tr>';

            }
        }

        return $html;
    }

    public static function buildGridRowsNegativeInvoice($rowsObject, $datos, $tipoDoc='')
    {

        $html = '';
        if($rowsObject && count($rowsObject) > 0){
            $cont=0;
            foreach ($rowsObject as $key) {
                $cont++;
                $html.='

                    <tr class="thead-light">              
                        
                        <td style="display:none;">
                            <input class="shortWidthField inputGrillaAuto numeroOrden" name="numeroOrden[]" id="numeroOrden'.$cont.'" value="'.$cont.'" readonly>
                            <input name="idFila[]" id="idFila'.$cont.'" value="'.$key->id.'" readonly>
                        </td>';                  

                        if($tipoDoc =='factura'){
                        
                            $html.='
                            <td class="celdaDescripcion">
                                    <textarea type="text" name="descripcion[]" id="descripcion'.$cont.'" class="largeWidthField inputGrillaDescripcion dblClickInput" readonly>'. $key->descripcion .'</textarea>
                            </td>';
                        }
                        
                        
                        $html.='
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto cantidad dblClickInput no_edit" value="'.$key->cantidad.'" step="0.01" readonly>
                        </td>

                        <td>
                            <div class="cont_negativo">
                                <span class="signo_negativo"> - </span>
                                <input type="number" class="shortWidthField2 inputGrillaAuto cantidad dblClickInput" name="cantidadArticulo[]" id="cantidadArticulo'.$cont.'" value="'.$key->cantidad.'" step="0.01">
                            </div>
                        </td>
                        
                        <td>
                            <input type="text" class="shortWidthField2 inputGrillaAuto unidad dblClickInput no_edit" name="unidadArticulo[]" id="unidadArticulo'.$cont.'" value="'.$key->unidad.'" readonly>
                        </td>
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto precio dblClickInput no_edit" value="'.$key->precio.'" step="0.01" readonly>
                        </td>                    
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto precio dblClickInput" name="precioArticulo[]" id="precioArticulo'.$cont.'" value="'.$key->precio.'" step="0.01">
                        </td>  
                        
                        <td>
                            <input type="number" class="shortWidthField2 inputGrillaAuto totalLinea dblClickInput no_edit" step="0.01" name="totalLinea[]" id="totalLinea'.$cont.'" value="'.$key->subtotal.'" readonly>
                        </td>                                
                        
                        <td class="lineaIva">
                            <select class="inputGrillaAuto iva" name="iva[]" id="iva'.$cont.'">
                                <option value="" selected disabled></option>';

                                if(isset($datos['tiposIva']) && count($datos['tiposIva']) > 0){                                
                                    foreach ($datos['tiposIva'] as $tipo) {
                                        $html.="<option value='".$tipo->tipo."' ".(($tipo->tipo == $key->ivatipo)? 'selected': '').">".$tipo->tipo." %</option>";
                                    }
                                }
                                $html.='                          
                            </select>
                        </td>
            
                    </tr>';

            }
        }

        return $html;
    }
        
    public static function construirCabeceraTabla($idPlanning, $fechas){

        $html = '<thead>';

        

        if(isset($fechas->fechainicio) && $fechas->fechainicio > 0 && isset($fechas->fechafin) && $fechas->fechafin > 0){
            $fechas = DateTimeHelper::buscarDiasEntreFechaInicioYFin($fechas->fechainicio, $fechas->fechafin);

            
            $html .= '<tr>';
            foreach ($fechas as $di) {
                $html .= '<td>'.$di['dia'].'</td>';
            }
            $html .= '</tr>';

            $html .= '<tr>';
            foreach ($fechas as $fecha) {
                $html .= '<td>'.date('d-m-Y',strtotime($fecha['fecha'])).'</td>';
            }
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        $html .= '</thead>';

        return $html;

    }

    public static function construirFilaTablaPlanificacion($dr, $clientes, $cliente, $datosCliente, $proveedores, $transportista, $unidad, $idAlbaranDet, $idAlbaranDetCli, $idAlbaranCli, $idAlbaranProv, $idAlbaranFabrica)
    {
        $html = '<td>
        <div class="celda_planif">
            <div class="fila_cantidad_unidad">
                <input type="number" step="0.01" value="'.$dr->carga.'" class="form-control form-control-sm input_planif" id="celda_'.$dr->id.'"><label>'.$unidad.'</label>
            </div>
            <div class="fila_cliente">
                <label>Cliente</label>
                <select class="form-control form-control-sm cliente_select_plan" data-idcelda="'.$dr->id.'" id="cliente_'.$dr->id.'">
                    <option selected disabled>Seleccionar</option>';

                    if(isset($clientes) && count($clientes) > 0){
                        foreach ($clientes as $cli) {
                            $html .= '<option value="'.$cli->id.'" '.(($cli->id==$cliente)? 'selected':'' ).'>'.$cli->nombrefiscal.'</option>';
                        }
                    }            
             
                $html .='
                </select>';

                $msgPrecioCliente = '';
                if($datosCliente){
                    $msgPrecioCliente = $datosCliente['msgPrecioCliente'];                  
                }            

                $html .= '
                <p class="zona_plan" id="precio_cliente_'.$dr->id.'">'.$msgPrecioCliente.'</p>
            </div>
            <div class="fila_transportista">
                <label>Transportista </label>
                <select class="form-control form-control-sm" id="transportista_'.$dr->id.'">
                    <option disabled>Seleccionar</option>';

        if(isset($proveedores) && count($proveedores) > 0){
            foreach ($proveedores as $prov) {
                $html .= '<option value="'.$prov->id.'" '.(($prov->id == $transportista)? 'selected':'' ).'>'.$prov->nombrefiscal.'</option>';
            }
        }

        $html .=
                '</select>';

        $msgTransportista = '';
        if($datosCliente){
            $msgTransportista = $datosCliente['msgTransportista'];
        }        

        $html .= '
                <p class="zona_plan" id="zona_precio_transportista_'.$dr->id.'">'.$msgTransportista.'</p>
            </div>
            <input type="hidden" id="idalbarandet_'.$dr->id.'" value="'.$idAlbaranDet.'">
            <input type="hidden" id="idalbarandetcli_'.$dr->id.'" value="'.$idAlbaranDetCli.'">
            <div class="btnes_celda">

                <div class="contenedor_botones">
                    <a class="btn_guardar_carga px-2" data-idcelda="'.$dr->id.'">Guardar</a>';

                    if ($idAlbaranDet > 0){
                        $html .= '<a class="btn_eliminar_carga px-2" data-idcelda="'.$dr->id.'">Eliminar</a>';
                    }
                    
                $html .= '
                </div>';
                
                $mostrar1 = ($idAlbaranDet > 0)? 'block': 'none';
                $rutaAlbaranProv = ($idAlbaranProv > 0)? RUTA_URL.'/AlbaranesProveedores/verAlbaran/'.$idAlbaranProv: '';
                $rutaAlbaranFabrica = ($idAlbaranFabrica > 0)? RUTA_URL.'/AlbaranesProveedores/verAlbaran/'.$idAlbaranFabrica: '';
                
                $html .='<a class="btn_ver_albaran_prov px-2" id="btn_ver_albaran_prov_'.$dr->id.'" style="display:'.$mostrar1.';" data-idcelda="'.$dr->id.'" href="'.$rutaAlbaranProv.'">Veure albará Prov.</a>';
                  
                $html .='<a class="btn_ver_albaran_fab px-2" id="btn_ver_albaran_fab_'.$dr->id.'" style="display:'.$mostrar1.';" data-idcelda="'.$dr->id.'" href="'.$rutaAlbaranFabrica.'">Veure albará Fab.</a>';


                $mostrar2 = ($idAlbaranCli == 0 && $idAlbaranDet > 0)? 'block': 'none';                              

                $html .='<a class="btn_albaran_cliente px-2" id="btn_albaran_cliente_'.$dr->id.'" data-idcelda="'.$dr->id.'" style="display:'.$mostrar2.';">Crear albará client</a>
            </div>';  

            if($idAlbaranDetCli > 0){
                $html .='<a class="btn_ver_albaran_cliente px-2" id="btn_ver_albaran_cliente_'.$dr->id.'" data-idcelda="'.$dr->id.'" href="'.RUTA_URL.'/AlbaranesClientes/verAlbaran/'.$idAlbaranCli.'">Veure albará client</a>
                </div>';
            }
       
            $html .='
        </div>';      

        $html .='
        </div>
        </td>';

        return $html;
    }

    public static function construirFilaTablaPlanificacionBackup($dr, $clientes, $cliente, $datosCliente, $proveedores, $transportista, $unidad, $idAlbaranDet, $idAlbaranDetCli, $idAlbaranCli, $idAlbaranProv)
    {
        $html = '<td>
        <div class="celda_planif">
            <div class="fila_cantidad_unidad">
                <input type="number" step="0.01" value="'.$dr->carga.'" class="form-control form-control-sm input_planif" id="celda_'.$dr->id.'"><label>'.$unidad.'</label>
            </div>
            <div class="fila_cliente">
                <label>Cliente</label>
                <select class="form-control form-control-sm cliente_select_plan" data-idcelda="'.$dr->id.'" id="cliente_'.$dr->id.'">
                    <option selected disabled>Seleccionar</option>';

                    if(isset($clientes) && count($clientes) > 0){
                        foreach ($clientes as $cli) {
                            $html .= '<option value="'.$cli->id.'" '.(($cli->id==$cliente)? 'selected':'' ).'>'.$cli->nombrefiscal.'</option>';
                        }
                    }            
             
                $html .='
                </select>';

                $msgPrecioCliente = '';
                if($datosCliente){
                    $msgPrecioCliente = $datosCliente['msgPrecioCliente'];                  
                }            

                $html .= '
                <p class="zona_plan" id="precio_cliente_'.$dr->id.'">'.$msgPrecioCliente.'</p>
            </div>
            <div class="fila_transportista">
                <label>Transportista </label>
                <select class="form-control form-control-sm" id="transportista_'.$dr->id.'">
                    <option disabled>Seleccionar</option>';

        if(isset($proveedores) && count($proveedores) > 0){
            foreach ($proveedores as $prov) {
                $html .= '<option value="'.$prov->id.'" '.(($prov->id == $transportista)? 'selected':'' ).'>'.$prov->nombrefiscal.'</option>';
            }
        }

        $html .=
                '</select>';

        $msgTransportista = '';
        if($datosCliente){
            $msgTransportista = $datosCliente['msgTransportista'];
        }        

        $html .= '
                <p class="zona_plan" id="zona_precio_transportista_'.$dr->id.'">'.$msgTransportista.'</p>
            </div>
            <input type="hidden" id="idalbarandet_'.$dr->id.'" value="'.$idAlbaranDet.'">
            <input type="hidden" id="idalbarandetcli_'.$dr->id.'" value="'.$idAlbaranDetCli.'">
            <div class="btnes_celda">
                <a class="btn_guardar_carga px-2" data-idcelda="'.$dr->id.'">Guardar</a>';
                
                $mostrar1 = ($idAlbaranDet > 0)? 'block': 'none';
                $rutaAlbaranProv = ($idAlbaranProv > 0)? RUTA_URL.'/AlbaranesProveedores/verAlbaran/'.$idAlbaranProv: '';
                
                $html .='<a class="btn_ver_albaran_prov px-2" id="btn_ver_albaran_prov_'.$dr->id.'" style="display:'.$mostrar1.';" data-idcelda="'.$dr->id.'" href="'.$rutaAlbaranProv.'">Veure albará Prov.</a>';

                $mostrar2 = ($idAlbaranCli == 0 && $idAlbaranDet > 0)? 'block': 'none';                              

                $html .='<a class="btn_albaran_cliente px-2" id="btn_albaran_cliente_'.$dr->id.'" data-idcelda="'.$dr->id.'" style="display:'.$mostrar2.';">Crear albará client</a>
            </div>';  

            if($idAlbaranDetCli > 0){
                $html .='<a class="btn_ver_albaran_cliente px-2" id="btn_ver_albaran_cliente_'.$dr->id.'" data-idcelda="'.$dr->id.'" href="'.RUTA_URL.'/AlbaranesClientes/verAlbaran/'.$idAlbaranCli.'">Veure albará client</a>
                </div>';
            }
       
            $html .='
        </div>';      

        $html .='
        </div>
        </td>';

        return $html;
    }    

    public static function buildDifferencesBetweenDeliveryNotesAndInvoice($datos)
    {
        $html = '';
      

        if($datos && count($datos) > 0){
            
            foreach ($datos as $key) {

                $cantidadAlb = 0;
                $cantidadFact = 0;
                if(isset($key->suma_cantidad) && $key->suma_cantidad > 0){
                    $cantidadAlb = $key->suma_cantidad;
                }
                if(isset($key->suma_cantidad_factura) && $key->suma_cantidad_factura > 0){
                    $cantidadFact = $key->suma_cantidad_factura;
                }
                $diferenciaCantidad = $cantidadFact - $cantidadAlb;
    
                $importeAlb = 0;
                $importeFact = 0;
                if(isset($key->suma_total) && $key->suma_total > 0){
                    $importeAlb = $key->suma_total;
                }
                if(isset($key->suma_total_factura) && $key->suma_total_factura > 0){
                    $importeFact = $key->suma_total_factura;
                }
                $diferenciaImporte = $importeFact - $importeAlb;        
                
                $descripcion = $key->descripcion.": " ;
                /*if(count($datos) > 1){
                    $descripcion = $key->descripcion.": " ;
                }*/
                $html .= '<p class="texto_producto mb-0">'.$descripcion.'</p><p class="mb-0 datos_diferencias">Albaranes '.number_format($importeAlb,2,",",".").'€; Factura '.number_format($importeFact,2,",",".").'€; Dif: '.number_format($diferenciaImporte,2,",",".").' 
                <p class="mb-0 datos_diferencias">Albaranes '.number_format($cantidadAlb,2,",",".").$key->unidad.'; Factura '.number_format($cantidadFact,2,",",".").$key->unidad. '; Dif: '.number_format($diferenciaCantidad,2,",",".");
                    
            }
        }else{
            $html .= '<p>Albaranes 0; Factura: 0; Diferencia 0</p>';
        }
        
        return $html;
    }

    public static function buildHTMLListSentEmailsDocumento($envios)
    {
            $html = '<div class="container_emails">';            
            
            foreach ($envios as $key) {                               
                $html2 = '<div class="fila_email">
                <div class="field_text"><span class="etiqueta_email">Data:</span> '.DateTimeHelper::convertDateTimeToFormat($key->fecha).'</div>
                <div class="field_text"><span class="etiqueta_email">Assumpte:</span> '.$key->asunto.'</div>                
                <div class="field_text"><span class="etiqueta_email">Remitent:</span> '.$key->correoremitente.' - '.$key->nomremitente.'</div>
                <div class="field_text"><span class="etiqueta_email">Destinataris:</span> '.self::quitarCorchetes($key->destinatarios).'</div>
                <div class="field_text"><span class="etiqueta_email">Missatge:</span> '.$key->mensaje.'</div>
                <div class="field_text"><span class="etiqueta_email">Document enviat:</span>: '.$key->nomfichero.'</div>
                </div>';
                $html .= $html2;
            }        
            $html .= '</div>';
        
            return $html;
    }

    public static function quitarCorchetes($jsonString) {        
        $array = json_decode($jsonString, true);            
        if (is_array($array)) {            
            $resultString = implode(', ', $array);
            return $resultString;
        } else {            
            return '';
        }
    }

}