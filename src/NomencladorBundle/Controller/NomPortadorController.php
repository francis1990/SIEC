<?php

namespace NomencladorBundle\Controller;


use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use NomencladorBundle\Entity\NomPortador;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomPortador controller.
 *
 */
class NomPortadorController extends Controller
{
    /**
     * Lists all NomPortador entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PORTADOR')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomportador:index.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PORTADOR')")
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
        if (!in_array("activo:0", $filters_raw) && !in_array("activo:1", $filters_raw) && !in_array("activo:2", $filters_raw)) {
            $where .= " AND g.activo = 1";
        }
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] != "activo") {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND g.' . $sp[0] . ' LIKE \'%' . $alias . '%\'';
                } elseif ($sp[0] == "activo") {
                    if ($sp[1] < 2) {
                        $where .= 'AND g.activo = ' . $sp[1];
                    }
                }
            }
        }
        $dql = 'SELECT g
            FROM NomencladorBundle:NomPortador g
            WHERE ' . $where . 'ORDER BY g.codigo';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIdportador();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getAlcance();
            $res[] = $pr->getDia();
            $res[] = $pr->getMadrugada();
            $res[] = $pr->getPico();
            $res[] = $pr->getInventario();
            $res[] = $pr->getEntrada();
            $res[] = $pr->getExistencia();
            $res[] = $pr->getActivo();
            $array[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * Creates a new NomPortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PORTADOR')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $portador = $request->request->get('nom_portador');
        $nomPortador = new NomPortador();
        $form = $this->createForm('NomencladorBundle\Form\NomPortadorType', $nomPortador);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomPortador->setActivo(true);
            $em->persist($nomPortador);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('portador_index',	array('remember'=> 1));
            else
                return $this->redirectToRoute('portador_new');
        }
        return $this->render('NomencladorBundle:nomportador:new.html.twig', array(
            'nomPortador' => $nomPortador,
            'form' => $form->createView(),
            'accion' => 'Adicionar'
        ));
    }

    /**
     * Finds and displays a NomPortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PORTADOR')")
     */
    public function showAction(NomPortador $nomPortador)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomPortador);
        return $this->render('NomencladorBundle:nomportador:show.html.twig', array(
            'nomPortador' => $nomPortador,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomPortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PORTADOR')")
     */
    public function editAction(Request $request, NomPortador $nomPortador)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($nomPortador);
        $editForm = $this->createForm('NomencladorBundle\Form\NomPortadorType', $nomPortador);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($nomPortador);
            $em->flush();
            return $this->redirectToRoute('portador_index',	array('remember'=> 1));
        }
        return $this->render('NomencladorBundle:nomportador:new.html.twig', array(
            'nomPortador' => $nomPortador,
            'form' => $editForm->createView(),
            'accion' => 'Editar'
        ));
    }

    /**
     * Deletes a NomPortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PORTADOR')")
     */
    public function deleteAction(Request $request, NomPortador $nomPortador)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomPortador);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomPortador);
            $em->flush();
        }
        return $this->redirectToRoute('portador_index');
    }

    /**
     * Creates a form to delete a NomPortador entity.
     *
     * @param NomPortador $nomPortador The NomPortador entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PORTADOR')")
     */
    private function createDeleteForm(NomPortador $nomPortador)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('portador_delete', array('id' => $nomPortador->getIdportador())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PORTADOR')")
     */
    public function deleteSelectAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $portador = $request->request->get('id');

        $params['campoId'] = "idportador";
        $params['tabla'] = "NomPortador";
        $params['valor'] = $portador;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        $params['nomenclador'] = "el elemento seleccionado.";

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso']) {
            $result['mensaje'] = 'No se pudo eliminar el Portador energético seleccionado ya que está en uso. Verifique en el Parte Diario de Portadores energéticos.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PORTADOR')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $portador = $request->request->get('id');

        $params['campoId'] = "idportador";
        $params['tabla'] = "NomPortador";
        $params['valor'] = $portador;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Portadores energéticos que no están en uso. Verifique en el Parte Diario de Portadores energéticos.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Portador energético seleccionado ya que está en uso. Verifique en el Parte Diario de Portadores energéticos.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PORTADOR')")
     */
    public function listadoAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $nom = $em->getRepository('NomencladorBundle:NomPortador')->findAll();
        return $this->render('NomencladorBundle:nomportador:listado.html.twig', array('portador' => $nom));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PORTADOR')")
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
        for ($i = 0; $i < count($elementos); $i++) {
            $tipespecifico = $em->getRepository('NomencladorBundle:NomPortador')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PORTADOR')")
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
        $data = $em->getRepository('NomencladorBundle:NomPortador')->listarDelExportarPortadores($filtroCodigo, $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Portador')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador portador sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador portador')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Portadores energéticos');

        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'UM')
            ->setCellValue('D5', 'Alcance')
            ->setCellValue('E5', 'Día')
            ->setCellValue('F5', 'Madrugada')
            ->setCellValue('G5', 'Pico')
            ->setCellValue('H5', 'Inventario')
            ->setCellValue('I5', 'Entrada')
            ->setCellValue('J5', 'Existencia')
            ->setCellValue('K5', 'Activo');
        $celda->setTitle('portador');
        $celda->fromArray($data, '', 'A6');
        $header = 'a5:k' . (count($data) + 5);
        $cabecera = 'a5:k5';
        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
        $style = array(
            /*'font' => array('bold' => true,),*/
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,),
            'borders' => array('outline' => array('style' => \PHPExcel_Style_Border::BORDER_THICK, 'color' => array('argb' => 'FF000000'))),
        );
        $style2 = array(
            'font' => array('bold' => true,),
            'borders' => array('outline' => array('style' => \PHPExcel_Style_Border::BORDER_THICK, 'color' => array('argb' => 'FF000000'))),
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
        $params['nameArchivo'] = "portadores";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}
