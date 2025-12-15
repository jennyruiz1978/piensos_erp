
    <style type="text/css">
        
        .card{            
            border-radius: 10px;
            height: 50px;
            color: black;            
            text-align: center;
            padding: 5px;            
        }
        .text_card{
            border:1px solid red;            
        }
        .titulo_card{
            font-size: 1rem;
        }
        .albaranados{
            border-color: #08707d;                
            border-bottom: 8px solid #08707d;          
            background-color: #1ddcf4;   
             
        }
        .facturados{
            border-color: #de8603;
            border-bottom: 8px solid #de8603;
            background-color: #f4bd0f;
        }
        .pagadas{
            border-color: #408b0c;
            border-bottom: 8px solid #408b0c;
            background-color: #a8ed77;
        }
        .vencidas{        
            border-color: #dc1919;                
            border-bottom: 8px solid #dc1919;
            background-color: #fedada;
        }
        .iva{
            border-color: #29c679;                
            border-bottom: 8px solid #29c679;
            background-color: #c7f8e0;
        }
        .pagadasparc{
            border-color: #2ea1a9;                
            border-bottom: 8px solid #2ea1a9;
            background-color: #b7eaee;
        }
        .sinfacturar{
            border-color: #879496;                
            border-bottom: 8px solid #879496;
            background-color: #e5f1f2;
        }
        .pendientes        {
            border-color: #cccd07;                
            border-bottom: 8px solid #cccd07;
            background-color: #f6f76d;
        }   

        #datosProveedores {
            border-collapse: collapse;
            width: 100%;
            border: none;
            margin: auto;
        }       

        #datosProveedores td {
            border: none;
            padding: 8px;
        }
        
        #datosProveedores th {
            border: none;
            padding: 8px;
            background-color: #f0f8ff;
        }    
        
        #datosClientes {
            border-collapse: collapse;
            width: 100%;
            border: none;
            margin: auto;
        }       

        #datosClientes td {
            border: none;
            padding: 8px;
        }
        
        #datosClientes th {
            border: none;
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

        td.general {
            width: 130px;
            text-align: left;
        }       
        td.subtitulo,
        th.subtitulo {
            width: 150px;
            font-size: 14px;
        }
       
        .imagen {
            width: 320px;
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
            margin-left: 20px;
        }

        .divisor {
            width: 715px;
            margin-left: 34px;
            border: 2px solid #00a79a;
        }
        .cont_logo{                                    
            width: 180px;
            height: 122px;
            position:absolute;
            left: 25px;
            top: -40px;
        }
        img.logo_documento{
            width: 100%;
        }

        table.datos_card{
            border-collapse: collapse;
            width: 100%;
            margin: auto;        
            border: none;
        }
        table.datos_card td {            
            border: none;            
        }
    </style>
    
    <page style="font-family: Arial, sans-serif;"  footer='page' backtop="20mm">

            <?php
                $info = $datos['info'];                
            ?>

            <div class="cont_logo">
                <img class="logo_documento" src="<?php echo $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['PHP_SELF']);  ?>/img/logo_piensos_doc.jpg">
            </div>
            

            <table class="contLogo">
                <tr>
                    <td rowspan='11' class='imagen'></td>
                    <td class='titulo' style="text-transform:uppercase;">
                        <b>
                        DASHBOARD
                        </b>
                    </td>
                    <td class='titulo'></td>
                </tr>
                <?php
                echo "
                    <tr>
                        <td class='subtitulo'><b><span style='text-transform:capitalize;'>   Desde: ".$datos['ini']."</span></b></td>                        
                    </tr>
                    <tr>
                        <td class='subtitulo'><b>   Hasta:  ".$datos['fin']."</b></td>
                    </tr>";
                ?>
            </table>

            <br><br><br>

            <div class="divisor"></div>
            
            <div class="principal">
                <?php                                                                                                                                                                                                               

                    echo "                        
                            <div class='contenedor'>             
                            
                                <div style='margin-left:15px;'><b>PROVEEDORES:</b></div>
                            
                                <br>

                                <table id='datosProveedores'>
                                    
                                    <tbody>
                                        <tr>
                                            <td class='general'>
                                                <div class='card albaranados'>                                                    
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>Albaranados</b></td></tr>
                                                        <tr><td>".$info['total_kilos_albaranados']."</td></tr>
                                                        <tr><td>".$info['total_euros_albaranados']." €</td></tr>
                                                    </table>
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card facturados'>
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>Facturados</b></td></tr>
                                                        <tr><td>".$info['kilos_facturados']."</td></tr>
                                                        <tr><td>".$info['euros_facturados']." €</td></tr>
                                                    </table>                                                                                                   
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card pagadas'>
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>F. Pagadas</b></td></tr>
                                                        <tr><td>".$info['euros_pagados']." €</td></tr>                                                        
                                                    </table>
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card vencidas'>                                                 
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>Recibos vencidos</b></td></tr>
                                                        
                                                        <tr><td>".$info['euros_vencidos']." €</td></tr>
                                                    </table>                                                     
                                                </div>
                                            </td>                                                                                        
                                        </tr>
                                        <tr>
                                            <td class='general'>
                                            </td>
                                            <td class='general'>
                                                <div class='card iva'>                                                    
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>IVA facturado</b></td></tr>
                                                        <tr><td>".$info['euros_ivafacturado']." €</td></tr>                                                        
                                                    </table>                                                     
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card pagadasparc'>                                 
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>F. Pagadas parc.</b></td></tr>
                                                        <tr><td>".$info['euros_pago_parcial']." €</td></tr>                                                        
                                                    </table>
                                                </div>
                                            </td>
                                            <td class='general'>                                               
                                            </td>                                                                                                                                    
                                        </tr>
                                        <tr>
                                            <td class='general'>                                                
                                            </td>
                                            <td class='general'>
                                                <div class='card sinfacturar'>                                                                                                       
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>Sin facturar</b></td></tr>
                                                        <tr><td>".$info['kilos_sin_facturar']."</td></tr>
                                                        <tr><td>".$info['euros_sin_facturar']." €</td></tr>
                                                    </table> 
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card pendientes'>                                                    
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>F. Pendientes</b></td></tr>
                                                        <tr><td>".$info['num_facturas_pago_pendiente']." fact.</td></tr>
                                                        <tr><td>".$info['euros_pago_pendiente']." €</td></tr>
                                                    </table>                                                                                                         
                                                </div>
                                            </td>
                                            <td class='general'>                                               
                                            </td>                                                                                                                                    
                                        </tr>                                                                                                                   
                                    </tbody>

                                </table>

                            </div>                       
                                
                            
                            <div class='contenedor'>             
                            
                                <div style='margin-left:15px;'><b>CLIENTES:</b></div>
                            
                                <br>

                                <table id='datosClientes'>                                                                                     
                                                                        
                                    <tbody>
                                        <tr>
                                            <td class='general'>
                                                <div class='card albaranados'>                                                    
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>Albaranados</b></td></tr>
                                                        <tr><td>".$info["total_kilos_albaranados_cli"]."</td></tr>
                                                        <tr><td>".$info["total_euros_albaranados_cli"]." €</td></tr>
                                                    </table>
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card facturados'>
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>Facturados</b></td></tr>
                                                        <tr><td>".$info["kilos_facturados_cli"]."</td></tr>
                                                        <tr><td>".$info["euros_facturados_cli"]." €</td></tr>
                                                    </table>                                                                                                   
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card pagadas'>
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>F. Cobradas</b></td></tr>
                                                        <tr><td>".$info["euros_pagados_cli"]." €</td></tr>                                                        
                                                    </table>
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card vencidas'>                                                 
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>Recibos vencidos</b></td></tr>
                                                        
                                                        <tr><td>".$info["euros_vencidos_cli"]." €</td></tr>
                                                    </table>                                                     
                                                </div>
                                            </td>                                                                                        
                                        </tr>
                                        <tr>
                                            <td class='general'>
                                            </td>
                                            <td class='general'>
                                                <div class='card iva'>                                                    
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>IVA facturado</b></td></tr>
                                                        <tr><td>".$info["euros_ivafacturado_cli"]." €</td></tr>                                                        
                                                    </table>                                                     
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card pagadasparc'>                                 
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>F. Cobradas parc.</b></td></tr>
                                                        <tr><td>".$info["euros_pago_parcial_cli"]." €</td></tr>                                                        
                                                    </table>
                                                </div>
                                            </td>
                                            <td class='general'>                                               
                                            </td>                                                                                                                                    
                                        </tr>
                                        <tr>
                                            <td class='general'>                                                
                                            </td>
                                            <td class='general'>
                                                <div class='card sinfacturar'>                                                                                                       
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>Sin facturar</b></td></tr>
                                                        <tr><td>".$info["kilos_sin_facturar_cli"]."</td></tr>
                                                        <tr><td>".$info["euros_sin_facturar_cli"]." €</td></tr>
                                                    </table> 
                                                </div>
                                            </td>
                                            <td class='general'>
                                                <div class='card pendientes'>                                                    
                                                    <table class='datos_card'>
                                                        <tr><td class='titulo_card'><b>F. Pendientes</b></td></tr>
                                                        <tr><td>".$info["num_facturas_pago_pendiente_cli"]." fact.</td></tr>
                                                        <tr><td>".$info["euros_pago_pendiente_cli"]." €</td></tr>
                                                    </table>                                                                                                         
                                                </div>
                                            </td>
                                            <td class='general'>                                               
                                            </td>                                                                                                                                    
                                        </tr>                                                                                                                   
                                    </tbody>                                

                                </table>

                            </div>  

                            ";

                ?>
            </div>
        
      


        <page_footer>
            <div class="rgpd">
            </div>            
        </page_footer>
    </page>
