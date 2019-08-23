<?php

namespace NomencladorBundle\Controller;


use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use NomencladorBundle\Entity\NomDpa;
use NomencladorBundle\Form\NomDpaType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Doctrine\Common\Util\Debug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

class NomDpaController extends Controller
{
    /**
     * Lists all NomDpa entities.
     *
     */

    /**
     * @Security("is_granted('ROLE_LISTAR_DPA')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomdpa:index.html.twig', array('remember' => $remember));
    }

    /**
     * Creates a new NomDpa entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_DPA')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomdpa = new NomDpa();
        $form = $this->createForm('NomencladorBundle\Form\NomDpaType', $nomdpa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomdpa->setHoja(1);
            $nomdpa->setActivo(1);
            $padre = $nomdpa->getPadre();
            if ($padre == null) {
                $nomdpa->setNivel(0);
                $nomdpa->setIdpadre(0);
            } else {
                $nomdpa->setNivel($nomdpa->getPadre()->getNivel() + 1);
                $padre->setHoja(0);
                $nomdpa->setIdpadre($nomdpa->getPadre()->getIddpa());
                $em->persist($padre);
            }

            $em->persist($nomdpa);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                if (!is_null($nomdpa->getIddpa())) {
                    $params['tabla'] = 'NomDpa';
                    $params['campoId'] = 'iddpa';
                    $padres = $this->get('nomencladores')->getObjPadres($nomdpa, [], $params);
                    $padres = array_reverse($padres);
                    array_push($padres, $nomdpa->getIddpa());
                    $this->get('session')->set('padres', $padres);
                }
                return $this->redirectToRoute('dpa_index', array('remember' => 1));
            } else
                return $this->redirectToRoute('dpa_new');
        }
        return $this->render('NomencladorBundle:nomdpa:new.html.twig', array(
            'nomProducto' => $nomdpa,
            'form' => $form->createView(),
            'accion' => 'Adicionar'
        ));
    }

    /**
     * Finds and displays a NomDpa entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_DPA')")
     */
    public function showAction(NomDpa $nomdpa)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomdpa);
        return $this->render('NomencladorBundle:nomdpa:show.html.twig', array(
            'nomProducto' => $nomdpa,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomDpa entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_DPA')")
     */
    public function editAction(Request $request, NomDpa $nomdpa)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm('NomencladorBundle\Form\NomDpaType', $nomdpa);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $padre = $nomdpa->getPadre();
            if ($padre == null) {
                $nomdpa->setIdpadre(0);
                $nomdpa->setNivel(0);
            } else {
                $nomdpa->setNivel($nomdpa->getPadre()->getNivel() + 1);
                $nomdpa->getPadre()->setHoja(0);
                $nomdpa->setIdpadre($nomdpa->getPadre()->getIddpa());
                $em->persist($nomdpa);
            }

            $em->flush();
            $params['tabla'] = 'NomDpa';
            $params['campoId'] = 'iddpa';
            $padres = $this->get('nomencladores')->getObjPadres($nomdpa, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $nomdpa->getIddpa());
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('dpa_index', array('remember' => 1));
        }
        return $this->render('NomencladorBundle:NomDpa:edit.html.twig', array(
            'NomDpa' => $nomdpa,
            'form' => $editForm->createView(),
            'accion' => "Editar"
        ));
    }

    /**
     * Deletes a NomDpa entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_DPA')")
     */
    public function deleteAction(Request $request, NomDpa $nomdpa)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomdpa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomdpa);
            $em->flush();
        }

        return $this->redirectToRoute('dpa_index');
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_DPA')")
     */
    function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $dpa = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $params['campoId'] = "iddpa";
        $params['tabla'] = "NomDpa";
        $params['valor'] = $dpa;
        $params['arbol'] = true;
        $params['servicio'] = 'nomenclador.nomdpa';
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Dpas que no están en uso. Verifique en el Nomenclador de Entidades.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Dpa seleccionado ya que está en uso. Verifique en el Nomenclador de Entidades.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_DPA')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $filters = array();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $where = '1=1 ';
        $filters_raw = explode('|', $request->query->get('filters'));
        if (!in_array("activo:0", $filters_raw) && !in_array("activo:1", $filters_raw) && !in_array("activo:2", $filters_raw)) {
            $where .= " AND g.activo = 1";
        }
        if ($request->query->get('filters')) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == "codigo") {
                    $where .= 'AND g.codigo LIKE \'%' . $sp[1] . '%\'';
                } elseif ($sp[0] == "activo") {
                    if ($sp[1] < 2) {
                        $where .= 'AND g.activo = ' . $sp[1];
                    }
                } else {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND g.alias LIKE \'%' . $alias . '%\'';
                }
                $parent = -1;
            }
        }

        if ($dat != '' && $dat != 0) {
            $where .= ' AND  g.nivel=' . $dat;
        }

        $where .= $parent != -1 ? ' AND g.idpadre=' . $parent : '';
        $dql = 'SELECT g
            FROM NomencladorBundle:NomDpa g
            WHERE ' . $where . ' ORDER BY g.prioridad, g.codigo';
        $consulta = $em->createQuery($dql);

        $tol = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nomdpas = $consulta->getResult();

        $dpa = array();
        foreach ($nomdpas as $pr) {
            $res = array();
            $res[] = $pr->getIddpa();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getActivo();
            $res[] = $request->query->get('filters') ? 1 : ($pr->getHoja());
            $dpa[] = $res;
        }

        if ($tol > 0) {
            return new JsonResponse(array('data' => $dpa, 'total' => $tol));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * Creates a form to delete a NomDpa entity.
     *
     * @param NomDpa $nomdpa The NomDpa entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(NomDpa $nomdpa)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dpa_delete', array('id' => $nomdpa->getIddpa())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_DPA')")
     */
    public function setActivoAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $elementos = $request->request->get('id');
        $accion = $request->request->get('accion');
        $em = $this->getDoctrine()->getManager();
        $accion == 'btn_activar' ? $estado = true : $estado = false;
        if (!is_array($elementos))
            $elementos = [$elementos];
        foreach ($elementos as $value) {
            $dpa = $em->getRepository('NomencladorBundle:NomDpa')->find($value);
            if (!$dpa->getHoja()) {
                $hijos = array();
                $dpaHij = $this->get('nomenclador.nomdpa')->getDpaHijos($dpa, $hijos, true);
                foreach ($dpaHij as $valueDpa) {
                    $dqlhijos = 'UPDATE NomencladorBundle:NomDpa g SET g.activo =:activo WHERE g.iddpa  = :iddpa';
                    $consulta = $em->createQuery($dqlhijos);
                    $consulta->setParameter('activo', $estado);
                    $consulta->setParameter('iddpa', $valueDpa);
                    $consulta->execute();
                }
            } else {
                $dpa->setActivo($estado);
                $em->persist($dpa);
                $em->flush();
            }
        }
        return new JsonResponse(array('respuesta' => 'exito'));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_DPA')")
     */
    public function deleteSelectAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $elemento = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();

        try {

            $this->eliminarHijos($elemento);

            $grupo = $em->getRepository('NomencladorBundle:NomDpa')->getPadre('NomencladorBundle:NomDpa', 'iddpa', $elemento);

            $em->getRepository('NomencladorBundle:NomDpa')->deleteAll('NomencladorBundle:NomDpa', 'iddpa', $elemento);


            if (count($grupo) > 0) {
                $cantH = $em->getRepository('NomencladorBundle:NomDpa')->listarDpaHijos($grupo[0]['idpadre']);

                if (count($cantH) == 0) {
                    $grupoPadre = $em->getRepository('NomencladorBundle:NomDpa')->find($grupo[0]['idpadre']);
                    if ($grupoPadre) {
                        $grupoPadre->setHoja(1);
                        $em->persist($grupoPadre);
                        $em->flush();
                    }
                }

            }
            return new JsonResponse(array('respuesta' => 'exito'));
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse(array('respuesta' => 'error'));
        }

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_DPA')")
     */
    public function exportarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $filtroCodigo = $request->query->get("codigo");
        $filtroNombre = $request->query->get("nombre");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomDpa')->listarDelExportarDPA($filtroCodigo, $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Dpa')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador dpa sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador dpa')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador DPA');
        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Activo');
        $celda->setTitle('Dpa');
        $celda->fromArray($data['data'], '', 'A6');
        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
        $header = 'a5:c' . (count($data['data']) + 5);
        $cabecera = 'a5:c5';
        $style = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,),
            'borders' => array('outline' => array('style' => \PHPExcel_Style_Border::BORDER_THICK, 'color' => array('argb' => 'FF000000')))
        );
        $style2 = array(
            'font' => array('bold' => true,),
            'borders' => array('outline' => array('style' => \PHPExcel_Style_Border::BORDER_THICK, 'color' => array('argb' => 'FF000000')))
        );
        $style3 = array(
            'font' => array('bold' => true)
        );
        $celda->getStyle($header)->applyFromArray($style);
        $celda->getStyle($cabecera)->applyFromArray($style2);
        $celda->getStyle($encabezado)->applyFromArray($style3);
        $celda->getStyle($encabezado1)->applyFromArray($style3);
        $objPHPExcel->setActiveSheetIndex(0);
        $celda->getStyle($cabecera)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('54ae86');
        $params['nameArchivo'] = "dpa";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}
