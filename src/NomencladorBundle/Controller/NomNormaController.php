<?php

namespace NomencladorBundle\Controller;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use NomencladorBundle\Entity\DatNormaAseguramiento;
use NomencladorBundle\Util\Util;
use ParteDiarioBundle\Entity\DatConsumoAseguramiento;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use NomencladorBundle\Entity\NomNorma;
use NomencladorBundle\Form\NomNormaType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomNorma controller.
 *
 */
class NomNormaController extends Controller
{
    /**
     * Lists all NomNorma entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_NORMACONSUMO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        return $this->render('NomencladorBundle:nomnorma:index.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_NORMACONSUMO')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $where = '1=1';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'producto') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND p.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'valornorma') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND g.valornorma LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'umnorma') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND (um.abreviatura LIKE \'%' . $alias . '%\' OR um.alias LIKE \'%' . $alias . '%\')';
                } elseif ($sp[0] == 'tiponorma') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND tn.alias LIKE \'%' . $alias . '%\'';
                }
            }
        }
        $dql = 'SELECT g,p
            FROM NomencladorBundle:NomNorma g
            JOIN g.producto p
            JOIN g.umnorma um
            JOIN g.tiponorma tn
            WHERE ' . $where . ' ORDER BY p.idproducto';
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $array = array();
        foreach ($nom as $pr) {
            if (isset($filters['producto']) && !strpos(strtolower($pr->getProducto()->getNombre()), strtolower($filters['producto']))) {
                continue;
            }
            $res = array();
            $res[] = $pr->getIdnorma();
            $producto = $pr->getProducto();
            $res[] = $producto->getNombre();
            $tiponc = $pr->getTipoNorma();
            $res[] = $tiponc->getNombre();
            $res[] = $pr->getValornorma();
            $res[] = ($pr->getUmnorma() != null) ? $pr->getUmnorma()->getAbreviatura() : '';
            $array[] = $res;
        }

        if ($total > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => array(), 'total' => 0));
        }

    }

    /**
     * Creates a new NomNorma entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_NORMACONSUMO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomNorma = new NomNorma();
        $form = $this->createForm('NomencladorBundle\Form\NomNormaType', $nomNorma);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomNorma);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('norma_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('norma_new');
        }
        return $this->render('NomencladorBundle:nomnorma:new.html.twig', array(
            'action' => 'Adicionar',
            'form' => $form->createView(),
            'accion' => 'Adicionar'
        ));
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_NORMACONSUMO')")
     */
    public function editAction(Request $request, NomNorma $norma)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedAseguramientos = clone $norma->getAseguramientos();
        $deleteForm = $this->createDeleteForm($norma);
        $editForm = $this->createForm('NomencladorBundle\Form\NomNormaType', $norma, array(
            'action' => $this->generateUrl('norma_edit', array('id' => $norma->getIdnorma()))
        ));
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->beginTransaction();
                try {
                    // Se eliminan los aseguramientos que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedAseguramientos as $savedConsumo) {
                        if ($norma->getAseguramientos()->contains($savedConsumo) === false) {
                            $em->remove($savedConsumo);
                        }
                    }
                    $em->persist($norma);
                    $em->flush();
                    $em->commit();

                    return $this->redirectToRoute('norma_index',array('remember'=> 1));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }

        return $this->render('NomencladorBundle:nomnorma:new.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'accion' => 'Editar',
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_NORMACONSUMO')")
     */
    private function createDeleteForm(NomNorma $nomnorma)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('norma_delete', array('id' => $nomnorma->getIdnorma())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_NORMACONSUMO')")
     */
    public function deleteAction(Request $request, NomNorma $nomnorma)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomnorma);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomnorma);
            $em->flush();
        }
        return $this->redirectToRoute('norma_index');
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_NORMACONSUMO')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $norma = $request->request->get('id');

        $params['campoId'] = "idnorma";
        $params['tabla'] = "NomNorma";
        $params['valor'] = $norma;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo las Normas que no están en uso. Verifique en el Parte Diaro de Consumo de Materias Primas.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar la Norma seleccionada ya que está en uso. Verifique en el Parte Diaro de Consumo de Materias Primas.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_NORMACONSUMO')")
     */
    public function duplicarAction(Request $request, NomNorma $norma)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $normaNew = $this->cloneNorma($norma);
        $duplicarForm = $this->createForm('NomencladorBundle\Form\NomNormaType', $normaNew, array(
            'action' => $this->generateUrl('norma_duplicar', array('id' => $norma->getIdnorma()))
        ));

        $duplicarForm->remove('guardar');
        $duplicarForm->handleRequest($request);
        if ($duplicarForm->isSubmitted() && $duplicarForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($normaNew);
            $em->flush();
            return $this->redirectToRoute('norma_index');
        }

        return $this->render('NomencladorBundle:nomnorma:new.html.twig', array(
            'form' => $duplicarForm->createView(),
            'accion' => 'Duplicar',
        ));
    }

    private function cloneNorma($oldNorma)
    {
        $normaNew = new NomNorma();
        $normaNew = clone $oldNorma;
        $normaNew->setIdnorma(null);

        foreach ($normaNew->getAseguramientos() as $normaaseg) {
            $normaAsegNew = new DatNormaAseguramiento();
            $normaAsegNew = clone $normaaseg;
            $normaAsegNew->setIdnormaaseg(null);
            $normaNew->addAseguramiento($normaAsegNew);
            $normaNew->removeAseguramiento($normaaseg);
        }

        return $normaNew;
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_NORMACONSUMO')")
     */
    public function exportarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $filtroProducto = $request->query->get("producto");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomNorma')->listarDelExportarNormas($filtroProducto);
        $allProductos = array();
        foreach ($data as $index => $value) {
            $allProductos[$index]['idproducto'] = $value['idproducto'];
            $allProductos[$index]['codigo'] = $value['codigoPro'];
            $allProductos[$index]['nombre'] = $value['nombrePro'];
            $allProductos[$index]['valornorma'] = $value['valornorma'];
            $allProductos[$index]['umnorma'] = $value['umnorma'];
            $allProductos[$index]['tiponorma'] = $value['tiponorma'];
        }
        $allProductos = Util::array_unique_callback($allProductos, function ($criterio) {
            return $criterio['idproducto'];
        });
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Norma de consumos')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador norma de consumo sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador norma consumo')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Normas de Consumo');
        $styleCentrar = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'font' => array('bold' => true, '')
        );
        $style3 = array(
            'font' => array('bold' => true, '')
        );

        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
        $celda->getStyle($encabezado)->applyFromArray($style3);
        $celda->getStyle($encabezado1)->applyFromArray($style3);
        $contFilasPintadas = 5;
        foreach ($allProductos as $valueProd) {

            $coordProductos = 'A' . $contFilasPintadas . ':D' . $contFilasPintadas;
            $celda->mergeCells($coordProductos);
            $celda->getStyle($coordProductos)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('54ae86');
            $objPHPExcel->getActiveSheet()->getStyle($coordProductos)->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->getStyle($coordProductos)->getFont()->setSize(12);
            $celda->getStyle($coordProductos)->applyFromArray($styleCentrar);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $contFilasPintadas, $valueProd['codigo']
                . ' - ' . $valueProd['nombre'] . ' CADA ' . $valueProd['valornorma'] . ' ' . $valueProd['umnorma'] . ' (Tipo NC ' . $valueProd['tiponorma'] . ')');
            $contFilasPintadas++;


            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $contFilasPintadas, 'Aseguramiento');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $contFilasPintadas, 'UM');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $contFilasPintadas, 'Moneda');
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $contFilasPintadas, 'Valor');
            $headerTabla = 'A' . $contFilasPintadas . ':D' . $contFilasPintadas;
            $celda->getStyle($headerTabla)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('52d689');
            $coordEncabTable = 'A' . $contFilasPintadas . ':D' . $contFilasPintadas;
            $objPHPExcel->getActiveSheet()->getStyle($coordEncabTable)->getFont()->setName('Arial');
            $objPHPExcel->getActiveSheet()->getStyle($coordEncabTable)->getFont()->setSize(10);
            $contFilasPintadas++;

            $tieneAseg = false;
            foreach ($data as $valueAseg) {
                if ($valueAseg['idproducto'] == $valueProd['idproducto']) {
                    /*Datos productos asociados al grupo de interes*/
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $contFilasPintadas, $valueAseg['aseguramiento']);
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $contFilasPintadas, $valueAseg['um']);
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $contFilasPintadas, $valueAseg['moneda']);
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $contFilasPintadas, $valueAseg['valor']);
                    $coordDatos = 'A' . $contFilasPintadas . ':D' . $contFilasPintadas;
                    $objPHPExcel->getActiveSheet()->getStyle($coordDatos)->getFont()->setName('Arial');
                    $objPHPExcel->getActiveSheet()->getStyle($coordDatos)->getFont()->setSize(10);
                    $contFilasPintadas++;
                    $tieneAseg = true;
                }
            }

            if (!$tieneAseg) {
                $contFilasPintadas = $contFilasPintadas - 2;
                $objPHPExcel->getActiveSheet()->removeRow($contFilasPintadas, 2);
                $rango = 'A' . $contFilasPintadas . ':D' . $contFilasPintadas;
                $objPHPExcel->getActiveSheet()->unmergeCells($rango);
            }
        }

        $params['nameArchivo'] = "normas";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);

    }

    public function getAseguramientosByTipoNCAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestAux = $request->request;
        $tipoNC = $requestAux->get('idtiponc');
        $producto = $requestAux->get('producto');
        $accion = $requestAux->get('accion');

        if ($tipoNC != "") {
            $normaResult = $this->getDoctrine()->getRepository('NomencladorBundle:NomNorma')->getAseguramientosByTipoNC($tipoNC,$producto);
            if (count($normaResult) != 0) {
                $norma = new NomNorma();
                if (count($normaResult->getAseguramientos()) > 0) {
                    foreach ($normaResult->getAseguramientos() as $aseg) {
                        $norma->addAseguramiento($aseg);
                    }
                }

                $form = $this->createForm('NomencladorBundle\Form\NomNormaType', $norma);
                $form->handleRequest($request);

                return $this->render('@Nomenclador/nomnorma/new.html.twig', array(
                    'form' => $form->createView(),
                    'accion' => $accion
                ));

            } else {
                return new JsonResponse(array('respuesta' => true));
            }
        } else {
            return new JsonResponse(array('respuesta' => true));
        }

    }

}
