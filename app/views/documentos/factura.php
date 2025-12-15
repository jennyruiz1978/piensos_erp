
    <style type="text/css">
        #datosEmpresas,
        #numFactura,
        #contenido {
            border-collapse: collapse;
            width: 100%;
        }

        .pie {
            background-color: #00BCD4;
            margin-top: 20px;
            height: 50px;
        }

        #datosEmpresas td,
        #numFactura td,
        #contenido td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #numFactura th,
        #datosEmpresas th,
        #contenido th {
            border: 1px solid #ddd;
            padding: 8px;
            background-color: #f0f8ff;
        }

        .titulo {
            font-size: 18px;
            font-weight: 1000;
        }

        .contenedor {
            margin-bottom: 20px;
            margin-right: 20px;
            
        }

        .izquierda {
            text-align: left;
        }

        .derecha {
            text-align: right;
        }

        td.informa {
            width: 245px;
            text-align: left;
        }

        td.cliente {
            width: 280px;
            text-align: left;
        }

       

        td.subtitulo,
        th.subtitulo {
            width: 150px;
            font-size: 14px;            
        }

        td.subtitulo_cabecera_derecha,
        th.subtitulo_cabecera_derecha {
            width: 150px;
            font-size: 14px;            
        }

        td.subtitulo_cabecera_izquierda,
        th.subtitulo_cabecera_izquierda {
            width: 100px;
            font-size: 14px;            
        }

        th.vacio {
            width: 75px;
        }

        .imagen {
            width: 320px;
        }

        th.conceptoCol,
        td.conceptoCol {
            width: 440px;
        }

        th.importeCol,
        td.importeCol {
            width: 85px;
        }
        th.importeCol{
            text-align: center;
        }

        .rgpd {
            font-size: 10px;
            margin-left: 40px;
            margin-right: 40px;
            color: #606060;
            margin-top: 5px;
            margin-bottom: 60px;        
            text-align: justify;
        }

        .cuerpo{
            top: 80px;
        }

        .subrayado {
            border-bottom: solid 3px blue;
        }

        .principal {
            margin-left: 34px;  
            font-size: 13px;          
        }

        .contLogo {
            margin-left: 70px;                      
        }

        .divisor {
            width: 710px;
            margin-left: 34px;
            border: 2px solid #00a79a;
        }
        .cont_logo{                                    
            width: 180px;
            height: auto;
            position:absolute;
            left: 25px;
            top: -40px;            
        }
        img.logo_documento{
            width: 100%;
        }
        .contTextoslogo {
            margin-left: 35px;  
                                      
        }
        .texto-logo-derecha{
            font-size: 10px;         
            width: 40px;   
        }
        
        .texto-logo-izquierda{
            font-size: 10px;         
            width: 200px;   
        }
    </style>
    
    <page style="font-family: Arial, sans-serif;"  footer='page' backtop="20mm">

            <?php
                $cabecera = $datos['cabecera'];                
            ?>

            <div class="cont_logo">
                <img class="logo_documento" src="<?php echo $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']);  ?>/img/LOGO_NOU_doc.jpg">
            </div>
            

            <table class="contLogo">
                <tr>
                    <td rowspan='11' class='imagen'></td>
                    <td class='titulo' style="text-transform:uppercase;">
                        <b>
                            <?php                             
                            if(isset($datos['rectificativa']) && $datos['rectificativa'] > 0){
                                echo $datos['tipo']." rectificativa";
                            }else{
                                echo $datos['tipo'];
                            }                            
                                
                            ?>
                        </b>
                    </td>
                    <td class='titulo'></td>
                </tr>
                <?php
                echo "
                    <tr>
                        <td class='subtitulo_cabecera_izquierda'><b>  Nº <span style='text-transform:capitalize;'>".$datos['tipo']."</span>:</b></td>
                        <td class='subtitulo_cabecera_derecha'>" . $cabecera->numero . "</td> 
                    </tr>
                    <tr>
                        <td class='subtitulo_cabecera_izquierda'><b>  Data ".$datos['tipo'].":</b></td>
                        <td class='subtitulo_cabecera_derecha'>" . date('d-m-Y', strtotime($cabecera->fecha)) . "</td>
                    </tr>";
                ?>
            </table>

            <br>

            <table class="contTextoslogo">
                <tr>
                    <td class="texto-logo-derecha">Telèfons</td>
                    <td>:</td>
                    <td class="texto-logo-izquierda">  656925956 / 633661366 </td>
                </tr>       
                <tr>
                    <td class="texto-logo-derecha">Email</td>
                    <td>:</td>
                    <td class="texto-logo-izquierda">  tarre@pinsosdev.com</td>
                </tr>                      
            </table>

            <br>

            <div class="divisor"></div>
            
            <div class="principal">
                <?php
                    echo "                        
                            <div class='contenedor'>             
                            
                                <div style='margin-left:415px;'><b>PER:</b></div>
                            
                                <br>

                                <table id='datosEmpresas'>
                                    
                                    <tbody>
                                        <tr>
                                            <th scope='row'>EMPRESA</th>
                                            <td class='informa'>".$datos['razonsocialpiensos']."</td>
                                            <td class='cliente'>" . $cabecera->cliente . "</td>
                                        </tr>
                                        <tr>
                                            <th scope='row'>CIF</th>
                                            <td>".NIF_PIENSOS."</td>
                                            <td>" . $cabecera->nif . "</td>
                                        </tr>
                                        <tr>
                                            <th scope='row'>DIRECCIÓ</th>
                                            <td>".DIRECCION_PIENSOS."</td>
                                            <td>" . $cabecera->direccion . "</td>
                                        </tr>
                                        <tr>
                                            <th scope='row'>CP i LOCALITAT</th>
                                            <td>".CODIGO_POSTAL_PIENSOS." ".LOCALIDAD_PIENSOS."</td>
                                            <td>" . $cabecera->codigopostal . " - " . $cabecera->poblacion . "</td>
                                        </tr>
                                        <tr>
                                            <th scope='row'>PROVÍNCIA</th>
                                            <td>".PROVINCIA_PIENSOS."</td>
                                            <td>" . $cabecera->provincia . "</td>
                                        </tr>                                            
                                    </tbody>
                                </table>

                            </div>                       
                        
                      

                            <div class='contenedor'>
                                <table id='contenido'>
                                    <thead>
                                        <tr>
                                            <th scope='col' class='subtitulo conceptoCol'>CONCEPTE</th>
                                            <th scope='col' class='subtitulo importeCol'>PREU</th>
                                            <th scope='col' class='subtitulo importeCol'>SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                                        if(isset($datos['detalle']) && count($datos['detalle']) > 0){
                                            foreach ($datos['detalle'] as $fila) {
                                                echo"
                                                <tr>                                        
                                                    <td class='conceptoCol'>Dia " . date("d-m-Y",strtotime($fila->fecha)) .": <span>&nbsp;&nbsp;</span>".number_format($fila->cantidad,'2',',','.')." ".$fila->unidad." de ".$fila->descripcion."</td>
                                                    <td class='derecha'>".number_format($fila->precio,'2',',','.')." €/".$fila->unidad."</td>
                                                    <td class='derecha'>".number_format($fila->subtotal,'2',',','.')." €</td>                                        
                                                </tr>
                                                ";
                                            }
                                        }
                                    echo"
                                        <tr>
                                            <td colspan='2' class='derecha conceptoCol subtitulo'><b>BASE IMPOSABLE</b></td>
                                            <td class='derecha'>" . number_format($cabecera->baseimponible, '2', ',', '.') . " €</td>
                                        </tr>";

                                    if(isset($datos['tiporazonsocial']) && $datos['tiporazonsocial'] == 'cliente'){
                                        echo"
                                        <tr>
                                            <td colspan='2' class='derecha conceptoCol subtitulo'><b>DESCOMPTE ".((isset($cabecera->descuentotipo) && $cabecera->descuentotipo != '')? number_format($cabecera->descuentotipo, '2', ',', '.'): 0)."%</b></td>
                                            <td class='derecha'>" . ((isset($cabecera->descuentoimporte) && $cabecera->descuentoimporte !='')? "- ". number_format($cabecera->descuentoimporte, '2', ',', '.'): 0) . " €</td>
                                        </tr>";   
                                    }


                                    echo"
                                        <tr>
                                            <td colspan='2' class='derecha conceptoCol subtitulo'><b>IVA 10%</b></td>
                                            <td class='derecha'>" . number_format($cabecera->ivatotal, '2', ',', '.') . " €</td>
                                        </tr>";                                    
                                    
                                    echo "                                                            
                                        <tr>
                                            <td colspan='2' class='derecha conceptoCol subtitulo total' style='background-color:#c8d0c8; color:#053669;'><b>TOTAL</b></td>                                            
                                            <td class='derecha total' style='background-color:#c8d0c8; color:#053669;'><b>" . number_format($cabecera->total, '2', ',', '.') . " €</b></td>
                                        </tr>  
                                    </tbody>
                                </table>

                                <br><br>"; 
                                
                                if(isset($cabecera->observaciones) && $cabecera->observaciones != ''){
                                    echo"<div>Observacions: ".$cabecera->observaciones." </div>";
                                }

                                if(isset($cabecera->vencimiento)){
                                    echo"
                                    <div>Data de venciment: ".(($datos['tipo'] == 'factura')? date("d-m-Y",strtotime($cabecera->vencimiento)): '')." </div>";
                                }

                                if(isset($cabecera->formapago)){
                                    echo"
                                    <div>
                                    Forma de pagament: ".(($cabecera->formapago && $cabecera->formapago != '')? $cabecera->formapago: '')." </div>
                                    <div>".((isset($datos['rectificativa']) && $datos['rectificativa'] > 0)? 'Factura origen: '.$datos['numFacturaOrigen']: '')." </div>";
                                }

                                if(!empty($cabecera->ctabancaria)){
                                    echo"<div>Cuenta bancaria: ".$cabecera->ctabancaria." </div>";                                    
                                }
                               

                                echo"
                            </div>                                                
                            ";

                ?>
            </div>
        
      


        <page_footer>
            <div class="rgpd">
            </div>            
        </page_footer>
    </page>
