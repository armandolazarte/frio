ob_start();
<?php
require_once "common/libs/PHPExcel/Classes/PHPExcel.php";


class ExcelReport extends View {
  public $estilo_titulo = "";
  public $estilo_titulo_reporte = "";
  public $estilo_titulo_columnas = "";
  public $estilo_informacion = "";
  public $abecedario = array("B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z", 
                             "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU",
                             "AV","AW","AX","AY","AZ");

  function extraer_informe_conjunto($subtitulo, $array_exportacion) {
    date_default_timezone_set('America/Mexico_City');
    if (PHP_SAPI == 'cli') die('Este archivo solo se puede ver desde un navegador web');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("DHARMA")
                                 ->setLastModifiedBy("DHARMA")
                                 ->setTitle("infDHTordo")
                                 ->setSubject("infDHTordo")
                                 ->setDescription("infDHTordo")
                                 ->setKeywords("infDHTordo")
                                 ->setCategory("infDHTordo");

    $tituloReporte = "RR Distribuciones";
    $fechaReporte = date("d-m-Y");
    $softReporte = "dhTordo";
    $tituloWeb = $tituloReporte;
    $titulosColumnas = array_shift($array_exportacion);
    $cantidadColumnas = count($titulosColumnas);
    $cantidadColumnas = $cantidadColumnas - 1;
    $ultimaLetraPosicion = "";
    $this->estilo();

    foreach ($this->abecedario as $clave=>$valor) {
      if ($clave <= $cantidadColumnas) {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("{$valor}3",  $titulosColumnas[$clave]);
        $ultimaLetraPosicion = $valor;
      }
    }

    $objPHPExcel->setActiveSheetIndex(0)
                ->setShowGridlines(false)
                ->mergeCells("B1:E1")
                ->mergeCells("F1:{$ultimaLetraPosicion}1")
                ->setCellValue("B1", $tituloReporte)
                ->setCellValue("F1", $fechaReporte)
                ->mergeCells("B2:{$ultimaLetraPosicion}2")
                ->setCellValue("B2", $subtitulo);

    $l = 4;
    $breack_row_temp = '';
    $breack_row_ant = '';
    $color_temp = 'second_info_style';
    foreach ($array_exportacion as $registro) {
      foreach ($registro as $clave=>$valor) {
        $color = $registro[1];
        $breack_row_temp = ($registro[0] != '') ? $registro[0] : $breack_row_temp;
        $posicion = $this->abecedario[$clave].$l;
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($posicion, $registro[$clave]);
        $objPHPExcel->getActiveSheet()->setSharedStyle($this->first_info_style, "B{$l}:{$ultimaLetraPosicion}{$l}");
      }

      $l++;
    }

    $celdas_titulos = "B3:{$ultimaLetraPosicion}3";
    $celdas_informacion = "B4:{$ultimaLetraPosicion}".($l-1);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($this->estilo_titulo);
    $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($this->estilo_fecha);
    $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($this->estilo_subtitulo);

    //ALTOS Y ANCHOS
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(2);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(32);
    $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(53);

    foreach ($this->abecedario as $clave=>$valor) {
      if ($clave <= $cantidadColumnas) $objPHPExcel->getActiveSheet()->getStyle("{$valor}3")->applyFromArray($this->estilo_titulo_columnas);
    }

    //$objPHPExcel->getActiveSheet()->setSharedStyle($this->estilo_informacion, "{$celdas_informacion}");
    $objPHPExcel->getActiveSheet()->setTitle("infDHTordo");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(2,4);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="infDHTordo.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    file_put_contents('depuracion.txt', ob_get_contents());
    ob_end_clean();
    $objWriter->save('php://output');
  }

  function extraer_informe_conjunto_remanente($subtitulo, $array_exportacion,$array_exportacion2) {
    date_default_timezone_set('America/Mexico_City');
    if (PHP_SAPI == 'cli') die('Este archivo solo se puede ver desde un navegador web');
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("DHARMA")
                                 ->setLastModifiedBy("DHARMA")
                                 ->setTitle("infDHTordo")
                                 ->setSubject("infDHTordo")
                                 ->setDescription("infDHTordo")
                                 ->setKeywords("infDHTordo")
                                 ->setCategory("infDHTordo");

    $tituloReporte = "RR Distribuciones";
    $fechaReporte = date("d-m-Y");
    $softReporte = "dhTordo";
    $tituloWeb = $tituloReporte;
    $titulosColumnas = array_shift($array_exportacion);
    $cantidadColumnas = count($titulosColumnas);
    $cantidadColumnas = $cantidadColumnas - 1;
    $ultimaLetraPosicion = "";
    $this->estilo();

    foreach ($this->abecedario as $clave=>$valor) {
      if ($clave <= $cantidadColumnas) {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("{$valor}3",  $titulosColumnas[$clave]);
        $ultimaLetraPosicion = $valor;
      }
    }

    $objPHPExcel->setActiveSheetIndex(0)
                ->setShowGridlines(false)
                ->mergeCells("B1:E1")
                ->mergeCells("F1:{$ultimaLetraPosicion}1")
                ->setCellValue("B1", $tituloReporte)
                ->setCellValue("F1", $fechaReporte)
                ->mergeCells("B2:{$ultimaLetraPosicion}2")
                ->setCellValue("B2", $subtitulo);

    $l = 4;
    $breack_row_temp = '';
    $breack_row_ant = '';
    $color_temp = 'second_info_style';
    foreach ($array_exportacion as $registro) {
      foreach ($registro as $clave=>$valor) {
        $color = $registro[1];
        $breack_row_temp = ($registro[0] != '') ? $registro[0] : $breack_row_temp;
        $posicion = $this->abecedario[$clave].$l;
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($posicion, $registro[$clave]);
        $objPHPExcel->getActiveSheet()->setSharedStyle($this->first_info_style, "B{$l}:{$ultimaLetraPosicion}{$l}");
      }

      $l++;
    }

    $celdas_titulos = "B3:{$ultimaLetraPosicion}3";
    $celdas_informacion = "B4:{$ultimaLetraPosicion}".($l-1);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($this->estilo_titulo);
    $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($this->estilo_fecha);
    $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($this->estilo_subtitulo);

    //ALTOS Y ANCHOS
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(2);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(7);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(300);

    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(32);
    $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(53);

    foreach ($this->abecedario as $clave=>$valor) {
      if ($clave <= $cantidadColumnas) $objPHPExcel->getActiveSheet()->getStyle("{$valor}3")->applyFromArray($this->estilo_titulo_columnas);
    }

    //$objPHPExcel->getActiveSheet()->setSharedStyle($this->estilo_informacion, "{$celdas_informacion}");
    $objPHPExcel->getActiveSheet()->setTitle("infDHTordo");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    $objPHPExcel->createSheet();

    $tituloReporte = "RR Distribuciones";
    $fechaReporte = date("d-m-Y");
    $softReporte = "dhTordo";
    $tituloWeb = $tituloReporte;
    $titulosColumnas = array_shift($array_exportacion2);
    $cantidadColumnas = count($titulosColumnas);
    $cantidadColumnas = $cantidadColumnas - 1;
    $ultimaLetraPosicion = "";
    $this->estilo();

    foreach ($this->abecedario as $clave=>$valor) {
      if ($clave <= $cantidadColumnas) {
        $objPHPExcel->setActiveSheetIndex(1)
                    ->setCellValue("{$valor}3",  $titulosColumnas[$clave]);
        $ultimaLetraPosicion = $valor;
      }
    }

    $objPHPExcel->setActiveSheetIndex(1)
                ->setShowGridlines(false)
                ->mergeCells("B1:E1")
                ->mergeCells("F1:{$ultimaLetraPosicion}1")
                ->setCellValue("B1", $tituloReporte)
                ->setCellValue("F1", $fechaReporte)
                ->mergeCells("B2:{$ultimaLetraPosicion}2")
                ->setCellValue("B2", $subtitulo);

    $l = 4;
    $breack_row_temp = '';
    $breack_row_ant = '';
    $color_temp = 'second_info_style';
    foreach ($array_exportacion2 as $registro) {
      foreach ($registro as $clave=>$valor) {
        $color = $registro[1];
        $breack_row_temp = ($registro[0] != '') ? $registro[0] : $breack_row_temp;
        $posicion = $this->abecedario[$clave].$l;
        $objPHPExcel->setActiveSheetIndex(1)
                    ->setCellValue($posicion, $registro[$clave]);
        $objPHPExcel->getActiveSheet()->setSharedStyle($this->first_info_style, "B{$l}:{$ultimaLetraPosicion}{$l}");
      }

      $l++;
    }

    $celdas_titulos = "B3:{$ultimaLetraPosicion}3";
    $celdas_informacion = "B4:{$ultimaLetraPosicion}".($l-1);
    $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($this->estilo_titulo);
    $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($this->estilo_fecha);
    $objPHPExcel->getActiveSheet()->getStyle('B2')->applyFromArray($this->estilo_subtitulo);

    //ALTOS Y ANCHOS
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(2);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(7);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(300);

    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(32);
    $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(53);

    foreach ($this->abecedario as $clave=>$valor) {
      if ($clave <= $cantidadColumnas) $objPHPExcel->getActiveSheet()->getStyle("{$valor}3")->applyFromArray($this->estilo_titulo_columnas);
    }

    $objPHPExcel->getActiveSheet()->setTitle('Consolidado');
    $objPHPExcel->setActiveSheetIndex(1);
    $objPHPExcel->getActiveSheet(1)->freezePaneByColumnAndRow(0,4);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="infDHTordo.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    file_put_contents('depuracion.txt', ob_get_contents());
    ob_end_clean();
    $objWriter->save('php://output');
  }


  /* ESTILO DE EXCEL */
  function estilo() {

    $this->estilo_titulo = array(
                            'font'=>array(
                                'name'=>'Bookman Old Style',
                                'bold'=>true,
                                'size'=>13,
                                'color'=>array('rgb'=>'FFFFFF')),
                            'fill'=>array(
                                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                'color'=>array('rgb' => '635C50')),
                            'alignment'=>array(
                                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER) );

    $this->estilo_fecha = array(
                            'font'=>array(
                                'name'=>'Bookman Old Style',
                                'bold'=>true,
                                'size'=>10,
                                'color'=>array('rgb'=>'FFFFFF')),
                            'fill'=>array(
                                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                'color'=>array('rgb' => '635C50')),
                            'alignment'=>array(
                                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER) );

    $this->estilo_soft = array(
                            'font'=>array(
                                'name'=>'Bookman Old Style',
                                'bold'=>true,
                                'size'=>10,
                                'color'=>array('rgb'=>'FFFFFF')),
                            'fill'=>array(
                                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                'color'=>array('rgb' => '635C50')),
                            'alignment'=>array(
                                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER) );

    $this->estilo_subtitulo = array(
                                    'font'=>array(
                                        'name'=>'Bookman Old Style',
                                        'size'=>17,
                                        'color'=>array('rgb' => '000000')),
                                    'fill'=>array(
                                        'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'=>array('rgb' => 'FFFFFF')),
                                    'alignment'=>array(
                                        'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                        'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER) );

    $this->estilo_titulo_legajo = array(
                                    'font'=>array(
                                        'name'=>'Arial',
                                        'size'=>9,
                                        'bold'=>false,
                                        'color'=>array('rgb' => '635C50')),
                                    'fill'=>array(
                                        'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'=>array('rgb' => 'FFFFFF')),
                                    'alignment'=>array(
                                        'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                        'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER));

    $this->estilo_legajo = array(
                                    'font'=>array(
                                        'name'=>'Arial',
                                        'size'=>10,
                                        'bold'=>true,
                                        'color'=>array('rgb' => '808080')),
                                    'fill'=>array(
                                        'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'=>array('rgb' => 'FFFFFF')),
                                    'alignment'=>array(
                                        'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                        'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER));

    $this->estilo_titulo_columnas = array(
                                      'font'=>array(
                                          'name'=>'Arial',
                                          'size'=>8,
                                          'bold'=>false,
                                          'color'=>array('rgb'=>'FFFFFF')),
                                      'fill'=>array(
                                          'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                          'rotation'=>90,
                                          'color'=>array('rgb' => '635C50')),
                                      'borders'=>array(
                                          'top'=>array(
                                              'style'=>PHPExcel_Style_Border::BORDER_MEDIUM,
                                              'color'=>array('rgb' => 'FFFFFF')),
                                          'bottom'=>array(
                                              'style'=>PHPExcel_Style_Border::BORDER_MEDIUM,
                                              'color'=>array('rgb' => 'FFFFFF'))),
                                      'alignment' =>  array(
                                          'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                          'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER) );

    $this->first_info_style = new PHPExcel_Style();
    $this->first_info_style->applyFromArray(
                            array(
                              'font'=>array(
                                  'name'=>'Arial',
                                  'size'=>9,
                                  'color'=>array('rgb'=>'000000')),
                              'fill'=>array(
                                  'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                  'color'=>array('rgb'=>'E8E7EF')),
                              'borders'=>array(
                                  'allborders'=>array(
                                      'style'=>PHPExcel_Style_Border::BORDER_THIN,
                                      'color'=>array('rgb' => 'FFFFFF')))) );

    $this->second_info_style = new PHPExcel_Style();
    $this->second_info_style->applyFromArray(
                            array(
                              'font'=>array(
                                  'name'=>'Arial',
                                  'size'=>9,
                                  'color'=>array('rgb'=>'000000')),
                              'fill'=>array(
                                  'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                  'color'=>array('rgb'=>'D9D9D9')),
                              'borders'=>array(
                                  'allborders'=>array(
                                      'style'=>PHPExcel_Style_Border::BORDER_THIN,
                                      'color'=>array('rgb' => 'FFFFFF')))) );
  }
}

function ExcelReport() { return new ExcelReport(); }
?>
