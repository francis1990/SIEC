<?php

namespace NomencladorBundle\Controller;

use Doctrine\Common\Util\Debug;
use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use NomencladorBundle\Entity\NomEntidad;
use NomencladorBundle\Entity\NomGrupointeres;
use NomencladorBundle\Form\NomEntidadType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomEntidad controller.
 *
 */
class NomEntidadController extends Controller
{
    /**
     * Lists all NomEntidad entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ENTIDAD')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomentidad:index.html.twig', array('remember' => $remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ENTIDAD')")
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

        $dat = $request->query->get('dat');
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $where = '1=1';
        $filters_raw = explode('|', $request->query->get('filters'));
        if (!in_array("activo:0", $filters_raw) && !in_array("activo:1", $filters_raw) && !in_array("activo:2", $filters_raw)) {
            $where .= " AND cp.activo = 1";
        }
        if ($request->query->get('filters')) {
            if ($filters_raw) {
                foreach ($filters_raw as $f) {
                    $sp = explode(':', $f);
                    if ($sp[0] == 'idpa') {
                        $alias = Util::getSlug($sp[1]);
                        $where .= ' AND dpa.alias LIKE \'%' . $alias . '%\'';
                    } else if ($sp[0] == 'idtipoentidad') {
                        $alias = Util::getSlug($sp[1]);
                        $where .= ' AND tipo.alias LIKE \'%' . $alias . '%\'';
                    } elseif ($sp[0] == "activo") {
                        if ($sp[1] < 2) {
                            $where .= 'AND cp.activo = ' . $sp[1];
                        }
                    } else {
                        $alias = Util::getSlug($sp[1]);
                        $where .= ' AND cp.' . $sp[0] . ' LIKE \'%' . $alias . '%\'';
                    }
                }
                $parent = -1;
            }

        }
        $where .= $parent != -1 ? ' AND cp.idpadre=' . $parent : "";

        $nomEntidads = $em->getRepository('NomencladorBundle:NomEntidad')->listarEnt($st, $lm, $where);

        $array = array();
        foreach ($nomEntidads['datos'] as $pr) {
            $res = array();
            $res[] = $pr->getIdentidad();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = ($pr->getSiglas() != null) ? $pr->getSiglas() : '';
            ($pr->getIddpa() != null) ? $dpa = $em->getRepository('NomencladorBundle:NomDpa')->find($pr->getIddpa()) : '';
            ($dpa != null) ? $res[] = $dpa->getNombre() : $res[] = '';
            ($pr->getIdtipoentidad() != null) ? $res[] = $pr->getIdtipoentidad()->getNombre() : $res[] = '';
            $res[] = $pr->getVinculo();
            $res[] = $pr->getEstatal();
            $res[] = $pr->getAcopio();
            $res[] = $pr->getReceptor();
            $res[] = $pr->getDiasvencidos();
            $res[] = $pr->getActivo();
            $res[] = $request->query->get('filters') ? 1 : $pr->getHoja();
            $array[] = $res;
        }
        if ($nomEntidads['count'] > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $nomEntidads['count']));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ENTIDAD')")
     */
    public function listarEntAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();
        $start = $request->request->get('start');
        $limit = $request->request->get('limit');

        $nomEntidads = $em->getRepository('NomencladorBundle:NomEntidad')->listarEnt($start, $limit);

        return $this->render('NomencladorBundle:nomentidad:index.html.twig', array(
            'nomResults' => $nomEntidads['datos'],
            'jsonData' => $nomEntidads['datos']
        ));
    }

    /**
     * Creates a new NomEntidad entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_ENTIDAD')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomEntidad = new NomEntidad();
        $form = $this->createForm('NomencladorBundle\Form\NomEntidadType', $nomEntidad);
        $form->handleRequest($request);
        $entidadRe = $request->request->get('nom_entidad');
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomEntidad->setIdpadre($entidadRe['idpadre'] == '' ? 0 : $entidadRe['idpadre']);
            $nomEntidad->setHoja(1);
            $nomEntidad->setActivo(1);
            if ($entidadRe['idpadre'] != 0) {
                $padre = $em->getRepository('NomencladorBundle:NomEntidad')->find($entidadRe['idpadre']);
                ($padre->getNivel() != null) ? $nomEntidad->setNivel($padre->getNivel() + 1) : $nomEntidad->setNivel(1);
                $padre->setHoja(0);
                $em->persist($padre);
            } else {
                $nomEntidad->setNivel(0);
            }
            $em->persist($nomEntidad);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                $params['tabla'] = 'NomEntidad';
                $params['campoId'] = 'identidad';
                $padres = $this->get('nomencladores')->getObjPadres($nomEntidad, [], $params);
                $padres = array_reverse($padres);
                array_push($padres, $nomEntidad->getIdentidad());
                $this->get('session')->set('padres', $padres);
                return $this->redirectToRoute('entidad_index', array('remember' => 1));
            } else
                return $this->redirectToRoute('entidad_new');
        }

        return $this->render('NomencladorBundle:nomentidad:new.html.twig', array(
            'nomEntidad' => $nomEntidad,
            'form' => $form->createView(),
            'accion' => 'Adicionar'
        ));
    }

    /**
     * Finds and displays a NomEntidad entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ENTIDAD')")
     */
    public function showAction(NomEntidad $nomEntidad)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomEntidad);

        return $this->render('NomencladorBundle:nomentidad:show.html.twig', array(
            'nomEntidad' => $nomEntidad,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomEntidad entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_ENTIDAD')")
     */
    public function editAction(Request $request, NomEntidad $nomEntidad)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomEntidad);

        $editForm = $this->createForm('NomencladorBundle\Form\NomEntidadType', $nomEntidad);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $dat = $request->request->get('nom_entidad');
            $pad = empty($dat['entsuperior']) ? 0 : $dat['entsuperior'];
            if ($pad == 0) {
                if ($nomEntidad->getIdpadre() != 0) {
                    $oldpadre = $em->getRepository('NomencladorBundle:NomEntidad')->find($nomEntidad->getIdpadre());
                    $hijos = $em->getRepository('NomencladorBundle:NomEntidad')->findByIdpadre($nomEntidad->getIdpadre());
                    if (count($hijos) == 1) {
                        $oldpadre->setHoja(1);
                        $em->persist($oldpadre);
                    }
                }
                $nomEntidad->setNivel(0);
            } else {
                $padre = $em->getRepository('NomencladorBundle:NomEntidad')->find($pad);
                $nomEntidad->setNivel($padre->getNivel() + 1);
                $padre->setHoja(0);
                $em->persist($padre);
            }
            $nomEntidad->setIdpadre($pad);
            $em->persist($nomEntidad);
            $em->flush();
            $params['tabla'] = 'NomEntidad';
            $params['campoId'] = 'identidad';
            $padres = $this->get('nomencladores')->getObjPadres($nomEntidad, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $nomEntidad->getIdentidad());
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('entidad_index', array('remember' => 1));
        }
        return $this->render('NomencladorBundle:nomentidad:new.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'idpadre' => $nomEntidad->getIdpadre(),
            'accion' => 'Editar'
        ));
    }

    /**
     * Deletes a NomEntidad entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ENTIDAD')")
     */
    public function deleteAction(Request $request, NomEntidad $nomEntidad)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $form = $this->createDeleteForm($nomEntidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomEntidad);
            $em->flush();
        }

        return $this->redirectToRoute('entidad_index');
    }

    /**
     * Creates a form to delete a NomEntidad entity.
     *
     * @param NomEntidad $nomEntidad The NomEntidad entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ENTIDAD')")
     */
    private function createDeleteForm(NomEntidad $nomEntidad)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('entidad_delete', array('id' => $nomEntidad->getIdentidad())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_ENTIDAD')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $entidad = $request->request->get('id');

        $params['campoId'] = "identidad";
        $params['tabla'] = "NomEntidad";
        $params['valor'] = $entidad;
        $params['arbol'] = true;
        $params['servicio'] = 'nomenclador.nomentidad';
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo las Entidades que no están en uso. Verifique en el Nomenclador de Ruta y en los Partes Diarios
                                 de Mercancía por Vínculo, Venta, Cuentas por cobrar y Acopio.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar la Entidad seleccionada ya que está en uso. Verifique en el Nomenclador de Ruta y en los Partes Diarios
                                 de Mercancía por Vínculo, Venta, Cuentas por cobrar y Acopio.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ENTIDAD')")
     */
    public function setEstadoAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $elementos = $request->request->get('ids');
        $accion = $request->request->get('accion');
        $accion == 1 ? $estado = 1 : $estado = 0;
        $em = $this->getDoctrine()->getManager();
        if (!is_array($elementos))
            $elementos = [$elementos];
        foreach ($elementos as $value) {
            $hijos = array();
            $entidad = $em->getRepository('NomencladorBundle:NomEntidad')->find($value);
            if (!$entidad->getHoja()) {
                $hijosEnt = $this->get('nomenclador.nomentidad')->getEntidadesHijasDadoEnt($entidad, $hijos, true, false);
                foreach ($hijosEnt as $value) {
                    $dqlhijos = 'UPDATE NomencladorBundle:NomEntidad g SET g.activo =:activo WHERE g.identidad  = :identidad';
                    $consulta = $em->createQuery($dqlhijos);
                    $consulta->setParameter('activo', $estado);
                    $consulta->setParameter('identidad', $value);
                    $consulta->execute();
                }
            } else {
                $entidad->setActivo($estado);
                $em->persist($entidad);
                $em->flush();
            }
        }
        return new JsonResponse(array('respuesta' => 'exito'));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_ENTIDAD')")
     */
    public
    function deleteSelectAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $entidad = $request->request->get('id');

        $params['campoId'] = "identidad";
        $params['tabla'] = "NomEntidad";
        $params['valor'] = $entidad;
        $params['arbol'] = true;
        $params['servicio'] = 'nomenclador.nomentidad';
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        $params['nomenclador'] = "el elemento seleccionado.";

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);
        if ($result['enUso']) {
            $result['mensaje'] = 'No se pudo eliminar la Entidad seleccionada ya que está en uso. Verifique en el Nomenclador de Ruta y en los Partes Diarios
                                 de Mercancía por Vínculo, Venta, Cuentas por cobrar y Acopio.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ENTIDAD')")
     */
    public
    function listadoAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();

        $nomEnt = $em->getRepository('NomencladorBundle:NomEntidad')->findAll();

        return $this->render('NomencladorBundle:nomentidad:listaentidad.html.twig', array(
            'ents' => $nomEnt
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ENTIDAD')")
     */
    public
    function listarhojasAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();

        $nom = $em->getRepository('NomencladorBundle:NomEntidad')->listarEntiHojas();

        return $this->render('NomencladorBundle:nomentidad:listarhojas.html.twig', array('ents' => $nom));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ENTIDAD')")
     */
    public
    function exportarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);

        $filtroCodigo = $request->query->get("codigo");
        $filtroNombre = $request->query->get("nombre");
        $filtroSiglas = $request->query->get("siglas");
        $filtroDpa = $request->query->get("dpa");
        $filtroTipoentidad = $request->query->get("tipoentidad");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomEntidad')->listarDelExportarEntidad($filtroCodigo, $filtroNombre, $filtroSiglas, $filtroDpa, $filtroTipoentidad);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Entidads')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador entidad sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador entidad')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(100);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Entidad');

        $celda->setCellValue('A5', 'Reup')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Dirección')
            ->setCellValue('D5', 'Siglas')
            ->setCellValue('E5', 'DPA')
            ->setCellValue('F5', 'Tipo entidad')
            ->setCellValue('G5', 'Vínculo')
            ->setCellValue('H5', 'Estatal')
            ->setCellValue('I5', 'Acopio')
            ->setCellValue('J5', 'Activo');
        $celda->setTitle('Entidad');
        $celda->fromArray($data['data'], '', 'A6');
        $header = 'A5:J' . (count($data['data']) + 5);
        $cabecera = 'A5:J5';
        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
        $style = array(
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
        $params['nameArchivo'] = "entidades";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}
