
class RowsGrid{

    
    constructor(){
        
    }
    
    async get(params){      

        let filaDinamica;                                         
        let tBody = document.getElementById(params.tbody);
        let numRows = tBody.rows.length;     
                
        if (numRows && numRows > 0) {
            
            let lastRow = $(tBody).find("tr").last();
            filaDinamica = lastRow.find('input').eq(0).val();                      
            
        } else {                        
            filaDinamica = 0;
        }

        var filaOrden = parseInt(filaDinamica) + 1;
        let html = this.buildHtmlRowBodyTabla(filaOrden, params);
        return html;

    }    

    buildSelectProducts(productos, productdefault)
    {
        let options = '';
        if(productos && productos.length > 0){
            for (let index = 0; index < productos.length; index++) {                    
                let selected = (productdefault && productdefault.id==productos[index].id)? 'selected': '';
                options += `<option value="${productos[index].id}" ${selected}>${productos[index].id} - ${productos[index].descripcion}</option>`;                
            }
        }
        return options;
    }

    buildSelectIva(tiposiva, productdefault)
    {
       
        let options = '';
        if(tiposiva && tiposiva.length > 0){
            for (let index = 0; index < tiposiva.length; index++) { 
                console.log('productdefault.iva=> ', productdefault.iva);
                console.log('tiposiva[index].tipo=> ', tiposiva[index].tipo);
                let ivaSelected = (productdefault && productdefault.iva==tiposiva[index].tipo)? 'selected': '';
                options += `<option value="${tiposiva[index].tipo}" ${ivaSelected}>${tiposiva[index].tipo} %</option>`;
            }
        }
        return options;         
    }

    buildHtmlRowBodyTabla(filaOrden, params)
    {        
        let optionProducts = this.buildSelectProducts(params.productos, params.productdefault);
        let optionIva = this.buildSelectIva(params.tiposivas, params.productdefault);

        let html = `
        <tr class="thead-light" id="fila_grilla_id_${filaOrden}">                        
                                
                <td style="display:none;">
                    <input class="shortWidthField inputGrillaAuto numeroOrden" name="numeroOrden[]" id="numeroOrden${filaOrden}" value="${filaOrden}" readonly="">
                </td>

                <td style="display:cell;">
                    <div class="cont_prod_del">
                        <select class="shortWidthField inputGrillaAuto articulo" data-idorden="${filaOrden}" name="idArticulo[]" id="idArticulo${filaOrden}">
                            <option value="" disabled="" >Seleccionar</option>
                            ${optionProducts}                           
                        </select>
                        <span class="eliminar_fila" data-idfila="${filaOrden}">x</span>
                    </div>              
                </td>

                <!--<td class="celdaDescripcion">
                        <textarea type="text" name="descripcion[]" id="descripcion${filaOrden}" class="largeWidthField inputGrillaDescripcion dblClickInput" readonly></textarea>
                </td>-->

                <td>
                    <input type="number" class="shortWidthField2 inputGrillaAuto cantidad dblClickInput" name="cantidadArticulo[]" id="cantidadArticulo${filaOrden}" step="0.01" value="0">
                </td>

                <td>
                    <input type="text" class="shortWidthField2 inputGrillaAuto unidad dblClickInput" name="unidadArticulo[]" id="unidadArticulo${filaOrden}" value="${params.productdefault.abrev_unidad}" readonly>
                </td>

                <td>
                    <input type="number" class="shortWidthField2 inputGrillaAuto precio dblClickInput" name="precioArticulo[]" id="precioArticulo${filaOrden}" step="0.01" value="0">
                </td>                    

                <td>
                    <input type="number" class="shortWidthField2 inputGrillaAuto totalLinea dblClickInput" step="0.01" name="totalLinea[]" id="totalLinea${filaOrden}" value="0" readonly>
                </td>                                

                <td class="lineaIva">
                    <select class="inputGrillaAuto iva" name="iva[]" id="iva${filaOrden}" >                        
                        ${optionIva}                       
                    </select>
                </td>

            </tr>
        `;

        return html;
    }

    async getFilaNext(params){ //sin usar
        let filaDinamica;                                         
        let tBody = document.getElementById(params.tbody);
        let numRows = tBody.rows.length;     
                
        if (numRows && numRows > 0) {
            
            let lastRow = $(tBody).find("tr").last();            
            filaDinamica = lastRow[0].dataset.index
            
        } else {                        
            filaDinamica = 0;
        }

        var filaOrden = parseInt(filaDinamica) + 1;        
        return filaOrden;
    }

}
export default RowsGrid;