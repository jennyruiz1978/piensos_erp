<div id="productos" class="col-md-12 table-responsive" >
    <table class='table table-bordered table-hover' id='tablaGrilla'>
        <thead>
            <tr class="thead-light">                    
                <th style="display:none;">Lin</th>
                <!--<th style="display:cell;" class="text-left">Codi</th>-->
                <th class="text-left">Descripció</th>
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
                   
            print($datos['html']);           
        
        ?>
                            

        </tbody>
    </table>
</div>