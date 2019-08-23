<?php

namespace NomencladorBundle\Controller;

use Doctrine\Common\Util\Debug;
use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use NomencladorBundle\Entity\NomAseguramiento;
use NomencladorBundle\Form\NomAseguramientoType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomAseguramiento controller.
 *
 */
class NomAseguramientoController extends Controller
{
    /**
     * Lists all NomAseguramiento entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ASEGURAMIENTO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomaseguramiento:index.html.twig', array('remember' => $remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ASEGURAMIENTO')")
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
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $where = '1=1 ';
        $wherefiltro = ' ';
        $confiltro = false;
        $filters_raw = explode('|', $request->query->get('filters'));
        if (!in_array("activo:0", $filters_raw) && !in_array("activo:1", $filters_raw) && !in_array("activo:2", $filters_raw)) {
            $where .= " AND g.activo = 1";
        }
        if ($request->query->get('filters')) {
            if ($filters_raw) {
                $confiltro = true;
                foreach ($filters_raw as $f) {
                    $sp = explode(':', $f);
                    if ($sp[0] == 'nombre') {
                        $alias = Util::getSlug($sp[1]);
                        $wherefiltro .= ' AND g.alias LIKE \'%' . $alias . '%\'';
                    } else if ($sp[0] == 'um') {
                        $alias = Util::getSlug($sp[1]);
                        $wherefiltro .= ' AND (ume.abreviatura LIKE \'%' . $alias . '%\' OR ume.alias LIKE \'%' . $alias . '%\')';
                    } elseif ($sp[0] == "activo") {
                        if ($sp[1] < 2) {
                            $where .= 'AND g.activo = ' . $sp[1];
                        }
                    } else {
                        $alias = Util::getSlug($sp[1]);
                        $wherefiltro .= ' AND g.' . $sp[0] . ' LIKE \'%' . $alias . '%\'';
                    }
                }
            }
        }
        if (!$confiltro)
            $where .= ' AND g.idpadre=' . $parent;
        else if ($parent != 0)
            $where .= $wherefiltro . ' AND g.idpadre=' . $parent;
        else
            $where .= $wherefiltro;
        $dql = 'SELECT g.nombre,g.idaseguramiento,g.nivel,g.codigo,g.activo,g.hoja,ume.nombre as um,
                g.mpb,g.ordenmpb,g.precio_cup,g.precio_cuc
            FROM NomencladorBundle:NomAseguramiento g            
            JOIN g.idunidadmedida  ume
            WHERE ' . $where . ' ORDER BY g.codigo';

        $consulta = $em->createQuery($dql);
        $tol = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nomAseg = $consulta->getResult();

        $prod = array();
        foreach ($nomAseg as $pr) {
            $res = array();
            $res[] = $pr['idaseguramiento'];
            $res[] = $pr['codigo'];
            $res[] = $pr['nombre'];
            $res[] = $pr['um'];
            $res[] = $pr['mpb'];
            $res[] = ($pr['ordenmpb'] != null) ? $pr['ordenmpb'] : '';
            $res[] = ($pr['precio_cup'] != null) ? $pr['precio_cup'] : '';
            $res[] = ($pr['precio_cuc'] != null) ? $pr['precio_cuc'] : '';
            $res[] = $pr['activo'];
            $res[] = $request->query->get('filters') ? 1 : $pr['hoja'];
            $prod[] = $res;
        }

        if ($tol > 0) {
            return new JsonResponse(array('data' => $prod, 'total' => $tol));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }


    /**
     * Creates a new NomAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_ASEGURAMIENTO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomAseguramiento = new NomAseguramiento();
        $form = $this->createForm('NomencladorBundle\Form\NomAseguramientoType', $nomAseguramiento);
        $form->handleRequest($request);
        $dato = $request->request->get('nom_aseguramiento');
        if ($form->isSubmitted() && $form->isValid()) {
            $nomAseguramiento->setActivo(1);
            $nomAseguramiento->setIdpadre(empty($dato['padre']) ? 0 : $dato['padre']);
            $nomAseguramiento->setHoja(1);
            $em = $this->getDoctrine()->getManager();
            if (!empty($dato['padre'])) {
                $padre = $em->getRepository('NomencladorBundle:NomAseguramiento')->find($dato['padre']);
                ($padre->getNivel() != null) ? $nomAseguramiento->setNivel($padre->getNivel() + 1) : $nomAseguramiento->setNivel(1);
                $padre->setHoja(0);
                $em->persist($padre);
            } else {
                $nomAseguramiento->setNivel(0);
            }
            $em->persist($nomAseguramiento);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                $params['tabla'] = 'NomAseguramiento';
                $params['campoId'] = 'idaseguramiento';
                $padres = $this->get('nomencladores')->getObjPadres($nomAseguramiento, [], $params);
                $padres = array_reverse($padres);
                array_push($padres, $nomAseguramiento->getIdaseguramiento());
                $this->get('session')->set('padres', $padres);
                return $this->redirectToRoute('aseguramiento_index', array('remember' => 1));
            } else
                return $this->redirectToRoute('aseguramiento_new');
        }

        return $this->render('NomencladorBundle:nomaseguramiento:new.html.twig', array(
            'nomAseguramiento' => $nomAseguramiento,
            'form' => $form->createView(),
            'idpadre' => 0,
            'accion' => 'Adicionar'
        ));
    }

    /**
     * Finds and displays a NomAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ASEGURAMIENTO')")
     */
    public function showAction(NomAseguramiento $nomAseguramiento)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $deleteForm = $this->createDeleteForm($nomAseguramiento);

        return $this->render('NomencladorBundle:nomaseguramiento:show.html.twig', array(
            'nomAseguramiento' => $nomAseguramiento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_ASEGURAMIENTO')")
     */
    public function editAction(Request $request, NomAseguramiento $nomAseguramiento)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm('NomencladorBundle\Form\NomAseguramientoType', $nomAseguramiento);
        $editForm->remove('agregar');
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $dato = $request->request->get('nom_aseguramiento');
            $pad = $dato['padre'] == '' ? 0 : $dato['padre'];
            if ($pad == 0) {
                $nomAseguramiento->setNivel(0);
            } else {
                $padre = $em->getRepository('NomencladorBundle:NomAseguramiento')->find($pad);
                $nomAseguramiento->setNivel($padre->getNivel() + 1);
                $padre->setHoja(0);
                $em->persist($padre);
            }
            $nomAseguramiento->setIdpadre($pad);

            $em->persist($nomAseguramiento);
            $em->flush();
            $params['tabla'] = 'NomAseguramiento';
            $params['campoId'] = 'idaseguramiento';
            $padres = $this->get('nomencladores')->getObjPadres($nomAseguramiento, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $nomAseguramiento->getIdaseguramiento());
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('aseguramiento_index', array('remember' => 1));
        }

        return $this->render('NomencladorBundle:nomaseguramiento:new.html.twig', array(
            'form' => $editForm->createView(),
            'padre' => $nomAseguramiento->getIdpadre(),
            'accion' => 'Editar'
        ));
    }

    /**
     * Deletes a NomAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ASEGURAMIENTO')")
     */
    public function deleteAction(Request $request, NomAseguramiento $nomAseguramiento)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $form = $this->createDeleteForm($nomAseguramiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomAseguramiento);
            $em->flush();
        }

        return $this->redirectToRoute('aseguramiento_index');
    }

    /**
     * Creates a form to delete a NomAseguramiento entity.
     *
     * @param NomAseguramiento $nomAseguramiento The NomAseguramiento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ASEGURAMIENTO')")
     */
    private function createDeleteForm(NomAseguramiento $nomAseguramiento)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('aseguramiento_delete', array('id' => $nomAseguramiento->getIdaseguramiento())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_ASEGURAMIENTO')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $grupo = $request->request->get('id');
        $params['campoId'] = "idaseguramiento";
        $params['tabla'] = "NomAseguramiento";
        $params['valor'] = $grupo;
        $params['arbol'] = true;
        $params['servicio'] = 'nomenclador.nomaseguramiento';
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Aseguramientos que no están en uso. Verifique en el Plan de Aseguramiento y el Parte Diario de Aseguramiento.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Aseguramiento seleccionado ya que está en uso. Verifique en el Plan de Aseguramiento y el Parte Diario de Aseguramiento.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_ASEGURAMIENTO')")
     */
    public function deleteSelectAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $grupo = $request->request->get('id');

        $params['campoId'] = "idaseguramiento";
        $params['tabla'] = "NomAseguramiento";
        $params['valor'] = $grupo;
        $params['arbol'] = true;
        $params['servicio'] = 'nomenclador.nomaseguramiento';
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        $params['nomenclador'] = "el elemento seleccionado.";

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso']) {
            $result['mensaje'] = 'No se pudo eliminar el Aseguramiento seleccionado ya que está en uso. Verifique en el Plan de Aseguramiento y el Parte Diario de Aseguramiento.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ASEGURAMIENTO')")
     */
    public function listadoAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $nomAseguramientos = $em->getRepository('NomencladorBundle:NomAseguramiento')->findAll();

        return $this->render('NomencladorBundle:nomaseguramiento:listadoaseg.html.twig', array(
            'asegs' => $nomAseguramientos,
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ASEGURAMIENTO')")
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
        $accion == 'btn_activar' ? $estado = 1 : $estado = 0;
        if (!is_array($elementos))
            $elementos = [$elementos];
        foreach ($elementos as $value) {
            $hijas = array();
            $aseg = $em->getRepository('NomencladorBundle:NomAseguramiento')->find($value);
            if (!$aseg->getHoja()) {
                $asegHij = $this->get('nomenclador.nomaseguramiento')->getAseguramientoHijas($aseg, $hijas, null, true);
                foreach ($asegHij as $valueAse) {
                    $dqlhijos = 'UPDATE NomencladorBundle:NomAseguramiento g SET g.activo =:activo WHERE g.idaseguramiento  = :idaseguramiento';
                    $consulta = $em->createQuery($dqlhijos);
                    $consulta->setParameter('activo', $estado);
                    $consulta->setParameter('idaseguramiento', $valueAse);
                    $consulta->execute();
                }
            } else {
                $aseg->setActivo($estado);
                $em->persist($aseg);
                $em->flush();
            }
        }
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ASEGURAMIENTO')")
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
        //$filtroTipo = $request->query->get("tipo");
        $filtroTipo = null;
        $filtroUm = $request->query->get("um");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomAseguramiento')->listarDelExportarAseguramientos($filtroCodigo, $filtroNombre, $filtroTipo, $filtroUm);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Aseguramiento')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador aseguramiento sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador aseguramiento')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Aseguramientos');
        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'UM')
            ->setCellValue('D5', 'Activo');
        $celda->setTitle('Aseguramiento');
        $celda->fromArray($data['data'], '', 'A6');
        $header = 'a5:d' . (count($data['data']) + 5);
        $cabecera = 'a5:d5';
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
        $params['nameArchivo'] = "aseguramientos";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

    /*Aqui no se tuvo en cuenta la seguridad ya que se usa en los reportes, especifícamente para el árbol*/
    public function getTreeAction(Request $request)
    {
        $node = $request->get('id') === '#' ? 0 : $request->get('id');
        $data = $this->get('nomenclador.nomaseguramiento')->getChildren($node);
        return new JsonResponse($data);
    }

    /*Aqui no se tuvo en cuenta la seguridad ya que se usa en los reportes, especifícamente para el árbol*/
    public function getTreeSinAsegMPBAction(Request $request)
    {
        $node = $request->get('id') === '#' ? 0 : $request->get('id');
        $data = $this->get('nomenclador.nomaseguramiento')->getChildrenSinAsegMPB($node);
        return new JsonResponse($data);
    }

}
