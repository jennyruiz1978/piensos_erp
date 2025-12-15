
    <style type="text/css">
        .datosEmpresas
        {
            border-collapse: collapse;
            width: 100%;
        }        

        .datosEmpresas td        
        {
            border: 1px solid #ddf5f8;
            padding: 8px;
        }        
        .datosEmpresas th
        {
            border: 1px solid #ddf5f8;
            padding: 8px;
            
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

        td.sinborde {
            width: 140px;
            text-align: left;
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: solid 1px #00a79a;
            
        }
        td.cliente {
            width: 140px;
            text-align: left;  
            border: 1px solid #00a79a;
            background-color:#ddf5f8;          
        }

        td.sinbordefoot {
            width: 315px;            
            text-align: left;
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: 1px solid #00a79a;;
        }
        td.clientefoot {
            width: 315px;
            height: 50px;
            text-align: left; 
            border: 1px solid #00a79a;
            background-color:#ddf5f8;        
            text-align: center; 
            vertical-align: bottom;     
        }      
        td.clientefootpiensos{
            width: 315px;
            height: 50px;
            text-align: left; 
            border: 1px solid #00a79a;
            background-color:#ddf5f8;        
            text-align: center; 
            vertical-align: bottom;  
        }
        .tablesub{
            margin-left: 65px;
        }
                      
        td.conceptoyletras{
            width: 600px;            
            text-align: left; 
            border: 1px solid #00a79a;
            background-color:#ddf5f8;                    
            vertical-align: bottom;
            
        }
        .concepto{
            width: 270px;
            text-align: left; 
            border: 1px solid #00a79a;
            background-color:#ddf5f8;
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
       
        .principal {
            margin-left: 34px;  
            font-size: 13px;          
        }

        .contLogo {
            margin-left: 34px;
        }

        .divisor {
            width: 700px;
            margin-left: 34px;
            border: 2px solid #00a79a;
        }               
    </style>
    
    <page style="font-family: Arial, sans-serif;"  footer='page' backtop="10mm">

            <?php
                $cabecera = $datos['cabecera'];                
            ?>

            <table class="contLogo">
                <tr>                    
                    <td class='titulo' style="text-transform:uppercase;">
                        <b><?php echo $datos['tipo'];?></b>
                    </td>                 
                </tr>                
            </table>

            <br>

            <div class="divisor"></div>
            
            <div class="principal">
                <?php
                    echo "                        
                            <div class='contenedor'> 
                                <br>

                                <table class='datosEmpresas'>
                                    
                                    <tbody>
                                        <tr>
                                            <td class='sinborde'><b>Rebut número</b></td>
                                            <td class='sinborde'><b>Data expedició</b></td>
                                            <td class='sinborde'><b>Lloc expedició</b></td>
                                            <td class='sinborde'><b>Import</b></td>
                                        </tr>
                                        <tr>
                                            <td class='cliente'>" . $cabecera->numero . "</td>
                                            <td class='cliente'>" . date('d-m-Y', strtotime($cabecera->fecha)) . "</td>
                                            <td class='cliente'>" . $cabecera->lugarexpedicion . "</td>
                                            <td class='cliente'>" . number_format($cabecera->importe,2,',','.') . "</td>
                                        </tr>      
                                    </tbody>
                                </table>

                            </div>                 
                        
                      
                            
                            <div class='contenedor'>
                                <div>
                                    <label><b>La quantitat d'euros</b></label>
                                    <br><br>
                                    <table class='datosEmpresas tablesub'>                                
                                        <tbody>
                                            <tr>
                                                <td class='conceptoyletras'>" . $datos['importe_letras']. "</td>
                                            </tr>            
                                        </tbody>
                                    </table>

                                </div>
                                <div>
                                    <label><b>Concepte</b></label>
                                    <br><br>    
                                    <table class='datosEmpresas tablesub'>                                
                                        <tbody>
                                            <tr>
                                                <td class='conceptoyletras'>" . $cabecera->concepto. "</td>
                                            </tr>            
                                        </tbody>
                                    </table>

                                </div>
                                                              
                            </div>                                                

                            <div class='contenedor'>                                                                       
                            
                            <br>

                            <table class='datosEmpresas'>
                                
                                <tbody>
                                    <tr>
                                        <td class='sinbordefoot'><b>Nom i adreça del lliurat - NIF</b></td>
                                        <td class='sinbordefoot'><b>Signatura i Nom del lliurador</b></td>                                       
                                    </tr>
                                    <tr>";
                                    
                                    $direccionCompleta = '';
                                    if(trim($cabecera->direccion) != ''){
                                        $codpost = ($cabecera->codigopostal > 0 && trim($cabecera->codigopostal)!='')? $cabecera->codigopostal: '';
                                        $nif = (trim($cabecera->nif) != '')? " - ".$cabecera->nif: '';
                                        $direccionCompleta = $cabecera->direccion." ".$cabecera->poblacion." ".$codpost." ".$cabecera->provincia.$nif;
                                    }
                                    echo"
                                        <td class='clientefoot'>".$cabecera->librado." ".$direccionCompleta."</td>
                                        <td class='clientefootpiensos' style='text-transform:uppercase;'>" .$datos['razonsocialpiensos']. "</td>
                                    </tr>                                                                               
                                </tbody>
                            </table>

                        </div>     ";                                        
                ?>
            </div>              

        <page_footer>
            <div class="rgpd">
            </div>            
        </page_footer>
    </page>
