<?php
    require '../public/vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    class ExportImportExcel {


        public static function prepareDataToExportExcel($datos, $nombreListado)
        {    
            if ($datos && $datos != '') {
    
                $datosList = [];
                
                foreach ($datos as $key => $val) {
                    $tmp1 = [];
                    foreach ($val as $field => $value) {
                        $tmp[$field] = $value;                                                                                   
                    }
                    $tmp1[] = $tmp;                                       
                    $datosList[] = $tmp1;
                }       
    
                $titulos = $datosList[0]; //obtengo un elemento del array para extraer los nombres para la cabecera del fichero                             

                $d = 'A'; //se refiere a la columna A del excel donde se recibirán los datos
    
                $inicio = 1; //se refiere a la fila 1 del excel donde se escribirán las cabeceras
    
                $cabeceras = [];
                //incorporo las letras correlativamente empezando en 'A'
                foreach ($titulos[0] as $key => $value) {
                    $cabeceras[$d . $inicio] = $key;
                    ++$d . PHP_EOL;
                }

                self::exportToExcel($cabeceras, $datosList, $nombreListado);
            } else {
                //echo "no hay datos que exportar";                
                echo "<script>alert('No hay datos que exportar'); window.history.back();</script>";
                exit;
            }
        }
    

        public static function exportToExcel($cabecerasTmp,$lineas,$nombreListado)
        {     
    
            $file = new Spreadsheet();
    
            $active_sheet = $file->getActiveSheet();                
            
            $active_sheet = $file->getActiveSheet();
            
            //escribimos las cabeceras
            foreach ($cabecerasTmp as $letra => $cabecera) {                
                $active_sheet->setCellValue($letra, $cabecera);                
            }
            $count = 2;

            //escribimos las filas
            foreach($lineas as $row)
            {                                
                foreach ($row[0] as $key => $val) {

                    if ($clave = array_search($key,$cabecerasTmp)) {
                        $columna= $clave[0];
                    }
                    
                    $active_sheet->setCellValue($columna . $count, $val);
                    
                }
                $count = $count + 1;

            }
          
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($file, 'Xlsx');
            date_default_timezone_set("Europe/Madrid");
            $file_name = date('Y-m-d_H_i_s') . $nombreListado . '.' . strtolower('Xlsx');
          
            //$writer->save($file_name);
            $full_path  = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $file_name;
            $writer->save($full_path );

          
           /*  header('Content-Type: application/x-www-form-urlencoded');
            header('Content-Transfer-Encoding: Binary');          
            header("Content-disposition: attachment; filename=\"".$file_name."\"");  */        
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$file_name\"");
            header('Content-Length: ' . filesize($full_path)); 
            readfile($full_path );          
            unlink($full_path );
          
            exit;
        }

        
    }