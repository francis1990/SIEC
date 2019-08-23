<?php

/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 12/21/2017
 * Time: 2:27 PM
 */

namespace ReporteBundle\Util;

use EnumsBundle\Entity\EnumLetras;
use EnumsBundle\Entity\EnumMeses;
use PHPExcel;
use PHPExcel_IOFactory;


class GenerarExcel
{
    private function encabezadoReporte($objPHPExcel, $params)
    {
        $em = $GLOBALS['kernel']->getContainer()->get('doctrine');
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $celda->mergeCells('A1:C1');
        $celda->setCellValue('A1', 'Empresa: ' . count($datosEmp) > 0 ? $datosEmp[0]->getNombreEntidad() : "");
        $celda->mergeCells('A2:C2');
        $celda->setCellValue('A2', 'Reporte: ' . $params['nameReporte']);

        $uebNombre = "";

        if (isset($params['ueb']) && $params['ueb'] != "" && isset($params['existeArbolUeb']) && $params['existeArbolUeb'] == "false" && isset($params['empresa']) && !$params['empresa']) {
            $ueb = $em->getRepository('NomencladorBundle:NomUeb')->find($params['ueb']);
            $celda->setCellValue('A3', 'UEB: ' . $ueb->getCodigo());
        } else {
            if (isset($params['arbolder_array']) && count($params['arbolder_array']) > 0 && isset($params['existeArbolUeb']) && $params['existeArbolUeb'] == "true") {
                foreach ($params['arbolder_array'] as $value) {
                    $ueb = $em->getEntityManager()->getRepository('NomencladorBundle:NomUeb')->find($value);
                    if (count($ueb) > 0 && $ueb != null) {
                        $uebNombre .= $ueb->getCodigo() . ",";
                    }
                }
                $celda->setCellValue('A3', 'UEB: ' . trim($uebNombre, ','));
            } else {
                $celda->setCellValue('A3', 'UEB: Empresa');
            }
        }

        $celda->mergeCells('B4:C4');
        if (isset($params['monedaNombre']) && $params['monedaNombre'] != "" && $params['monedaNombre'] != "Seleccione...") {
            $celda->setCellValue('B4', 'Moneda/Destino: ' . $params['monedaNombre']);
        } else {
            $celda->setCellValue('B4', 'Moneda/Destino: -');
        }

        $celda->mergeCells('D4:E4');
        if (isset($params['tipoplanNombre']) && $params['tipoplanNombre'] != "" && $params['tipoplanNombre'] != "Seleccione...") {
            $celda->setCellValue('D4', 'Tipo Plan: ' . $params['tipoplanNombre']);
        } else {
            $celda->setCellValue('D4', 'Tipo Plan: -');
        }

        $celda->mergeCells('F4:G4');
        if (isset($params['ufvalorNombre']) && $params['ufvalorNombre'] != "" && $params['ufvalorNombre'] != "Seleccione...") {
            $celda->setCellValue('F4', 'UF/Valor: ' . $params['ufvalorNombre']);
        } else {
            $celda->setCellValue('F4', 'UF/Valor: -');
        }

        $celda->mergeCells('H2:J2');
        $celda->setCellValue('H2', 'Fecha: ' . date('d/m/Y'));
        $celda->mergeCells('H3:J3');
        $fecha = isset($params['fecha']) ? EnumMeses::convertfecha($params['fecha']) : EnumMeses::convertfecha(date('d/m/Y'));
        $celda->setCellValue('H3', 'Al Cierre: ' . $fecha['d'] . '/' . $fecha['m'] . '/' . $fecha['a']);
    }

    function encabezadoTabla($objPHPExcel, $params = array(), $arrDatos, $titulo)
    {
        $objEnumLetras = new EnumLetras();
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $styleCentrar = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000')
                )
            ),
            'font' => array('bold' => true, '')
        );

        $negrita = array(
            'font' => array('bold' => true, '')
        );

        if ($titulo != null) {
            $letra = $objEnumLetras->letraExcel($titulo['colInic']);
            $valor3 = $titulo['fila'];
            $celda->setCellValue($letra . $valor3, $titulo['nombre']);
            $celda->getStyle($letra . $valor3)->applyFromArray($styleCentrar);
        }
        foreach ($arrDatos as $value) {

            if ($value['mergeRow'] != 0) {
                $valor2 = ($value['fila'] + $value['mergeRow']) - 1;
                $celda->mergeCellsByColumnAndRow($value['colInic'], $value['fila'], $value['colInic'], $valor2);
            } else {
                if ($value['mergeCell'] != 0) {
                    $letra1 = $objEnumLetras->letraExcel($value['colInic']);
                    $letra2 = $objEnumLetras->letraExcel($value['colInic'] + $value['mergeCell'] - 1);
                    $celda->mergeCells($letra1 . $value['fila'] . ':' . $letra2 . $value['fila']);
                }
            }
            $letra = $objEnumLetras->letraExcel($value['colInic']);
            $valor3 = $value['fila'];
            $celda->setCellValue($letra . $valor3, $value['nombre']);

            if (isset($value['ancho']) && $value['ancho'] != 0) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth($value['ancho']);
            }

            if (isset($value['pintar']) && $value['pintar']) {
                $colInicPintar = $objEnumLetras->letraExcel($value['colInic']);
                $colFinalPintar = $objEnumLetras->letraExcel($value['colInic']);
                $coordFormato = $colInicPintar . $value['fila'] . ':' . $colFinalPintar . $value['fila'];
                $celda->getStyle($coordFormato)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('54ae86');
            }

            if (isset($value['negrita'])) {
                $fila = $value['fila'];
                $celda->getStyle($letra . $fila)->applyFromArray($negrita);
            }

            if (isset($value['centrar'])) {
                $celda->getStyle($letra . $value['fila'])->applyFromArray($styleCentrar);
            }

        }
        $colInicPintar = $objEnumLetras->letraExcel($arrDatos[0]['colInic']);
        $colFinalPintar = $objEnumLetras->letraExcel($arrDatos[count($arrDatos) - 1]['colInic']);
        $filaInicPintar = $arrDatos[0]['fila'];
        $filaFinalPintar = $arrDatos[count($arrDatos) - 1]['fila'];
        $coordFormato = $colInicPintar . $filaInicPintar . ':' . $colFinalPintar . $filaFinalPintar;
        //$celda->getStyle($coordFormato)->applyFromArray($styleCentrar);
    }

    public function pintartabla($objPHPExcel, $params, $head, $body)
    {

        if (!isset($params['title'])) {
            $params['title'] = "";
        }

        if (count($params['tablas']) == 1) {

            if (isset($params['tablas'][0][$body])) {
                self::encabezadoTabla($objPHPExcel, $params, $params['tablas'][0][$head], $params['title']);
                self::cuerpoTabla($objPHPExcel, $params, $params['tablas'][0][$body]);
            }
        } else {
            foreach ($params['tablas'] as $t) {
                if (isset($t[$body])) {
                    self::encabezadoTabla($objPHPExcel, $params, $t[$head], $t['title']);
                    self::cuerpoTabla($objPHPExcel, $params, $t[$body]);
                }
            }
        }

    }

    static public function cuerpoTabla($objPHPExcel, $params, $arrDatos)
    {
        $objEnumLetras = new EnumLetras();

        $negrita = array(
            'font' => array('bold' => true, '')
        );

        $styleCentrar = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        );

        $styleDerecha = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
        );

        $styleIzquierda = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
        );

        $celda = $objPHPExcel->setActiveSheetIndex(0);

        foreach ($arrDatos as $value) {
            $letra = $objEnumLetras->letraExcel($value['col']);
            $celda->setCellValue($letra . $value['fila'], $value['valor']);

            if (isset($value['pintar']) && $value['pintar']) {
                $colInicPintar = $objEnumLetras->letraExcel($value['col']);
                $colFinalPintar = $objEnumLetras->letraExcel($value['col']);
                $coordFormato = $colInicPintar . $value['fila'] . ':' . $colFinalPintar . $value['fila'];
                $celda->getStyle($coordFormato)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('52d689');
            }

            if (isset($value['negrita']) && $value['negrita']) {
                $fila = $value['fila'];
                $celda->getStyle($letra . $fila)->applyFromArray($negrita);
            }
            if (isset($value['formato'])) {
                $fila = $value['fila'];
                switch ($value['formato']) {
                    case 0:
                        /*Porciento*/
                        $celda->getStyle($letra . $fila)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
                        break;
                    case 1:
                        /*cantidad toneladas*/
                        $celda->getStyle($letra . $fila)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_0);
                        break;
                    case 2:
                        /*precios e importes*/
                        $celda->getStyle($letra . $fila)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                        break;
                    default:
                        /*Otros*/
                        $celda->getStyle($letra . $fila)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_000);
                }
            }


            if (isset($value['centrar'])) {
                $celda->getStyle($letra . $value['fila'])->applyFromArray($styleCentrar);
            } elseif (isset($value['derecha'])) {
                $celda->getStyle($letra . $value['fila'])->applyFromArray($styleDerecha);
            } elseif (isset($value['izquierda'])) {
                $celda->getStyle($letra . $value['fila'])->applyFromArray($styleIzquierda);
            }

            if ($value['mergeCell'] != 0) {
                $letra1 = $objEnumLetras->letraExcel($value['col']);
                $letra2 = $objEnumLetras->letraExcel($value['col'] + $value['mergeCell'] - 1);
                $celda->mergeCells($letra1 . $value['fila'] . ':' . $letra2 . $value['fila']);
            }
        }

    }

    public function exportarExcel($params)
    {
        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Reportes')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Reporte sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('Módulo de Reportes')
            ->setCategory('Exportar');

        self::encabezadoReporte($objPHPExcel, $params);
        self::pintartabla($objPHPExcel, $params, 'encabezadotabla', 'cuerpoTabla');
        self::pieDeFirma($objPHPExcel, $params, 'encabezadotabla', 'cuerpoTabla');

        self::salidaExportarExcel($objPHPExcel, $params);

    }

    public function salidaExportarExcel($objPHPExcel, $params)
    {
        header('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        header('Content-Type: application/vnd.ms-excel;');
        header("Content-type: application/x-msexcel");
        header('Pragma', 'public');
        header('Content-Disposition: attachment;filename=' . $params['nameArchivo'] . '.xls');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save('php://output');
        /*Desconecta el objeto PHPExcel para que no se quede en memoria*/
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
    }

    static public function pieDeFirma($objPHPExcel, $params, $head, $body)
    {
        $negrita = array(
            'font' => array('bold' => true, ''),
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
        );
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        if (count($params['tablas']) > 0) {
            if ($params['ultimaFilaPintada'] != 0) {
                $params['ultimaFilaPintada'] += 3;
                $celda->mergeCells("A" . $params['ultimaFilaPintada'] . ':' . "C" . $params['ultimaFilaPintada']);
                $celda->setCellValue("A" . $params['ultimaFilaPintada'], 'Nombre: ' . $params['usuarioLog']);
                $celda->getStyle("A" . $params['ultimaFilaPintada'])->applyFromArray($negrita);
                $params['ultimaFilaPintada']++;
                $celda->mergeCells("A" . $params['ultimaFilaPintada'] . ':' . "C" . $params['ultimaFilaPintada']);
                $celda->setCellValue("A" . $params['ultimaFilaPintada'], 'Cargo: ');
                $celda->getStyle("A" . $params['ultimaFilaPintada'])->applyFromArray($negrita);
                $params['ultimaFilaPintada']++;
                $celda->mergeCells("A" . $params['ultimaFilaPintada'] . ':' . "C" . $params['ultimaFilaPintada']);
                $celda->setCellValue("A" . $params['ultimaFilaPintada'], 'UEB: ' . $params['uebUserLog']->getNombre());
                $celda->getStyle("A" . $params['ultimaFilaPintada'])->applyFromArray($negrita);
            }
        }
    }
}