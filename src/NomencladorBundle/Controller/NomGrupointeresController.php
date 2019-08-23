<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use NomencladorBundle\Entity\NomGrupointeres;
use NomencladorBundle\Entity\NomEntidad;
use NomencladorBundle\Form\NomEntidadType;
use NomencladorBundle\Form\NomGrupointeresType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);


/**
 * NomGrupointeres controller.
 *
 */
class NomGrupointeresController extends Controller
{
    /**
     * Lists all NomGrupointeres entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
     */

    public function indexAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomgrupointeres/new.html.twig', array());
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
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
        $where = '1=1';

        if ($request->query->get('filters')) {
            $filters_raw = explode('|', $request->query->get('filters'));
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                $where .= 'AND g.' . $sp[0] . ' LIKE \'%' . $sp[1] . '%\'';

            }
        }

        $where .= ' AND g.idpadre=' . $parent;
        $dql = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE ' . $where;

        $consulta = $em->createQuery($dql);
        $tol = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);

        $NomGrupointeress = $consulta->getResult();

        $dpa = array();
        foreach ($NomGrupointeress as $pr) {
            $res = array();
            $res[] = $pr->getIdgrupointeres();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getActivo();
            $res[] = $pr->getHoja();
            $dpa[] = $res;
        }

        if ($tol > 0) {
            return new JsonResponse(array('data' => $dpa, 'total' => $tol));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
     */
    public function listGruposAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();
        $filters = array();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $where = '1=1';
        $filters_raw = explode('|', $request->query->get('filters'));
        if (!in_array("activo:0", $filters_raw) && !in_array("activo:1", $filters_raw) && !in_array("activo:2", $filters_raw)) {
            $where .= " AND g.activo = 1";
        }
        if ($request->query->get('filters')) {
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
                $parent = -1;
            }
        }

        $where .= $parent != -1 ? ' AND g.idpadre=' . $parent : '';
        $dql = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE ' . $where . '  ORDER BY g.codigo';

        $consulta = $em->createQuery($dql);
        $tol = count($consulta->getResult());

        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);

        $NomGrupointeress = $consulta->getResult();

        $dpa = array();
        foreach ($NomGrupointeress as $pr) {
            $res = array();
            $res[] = $pr->getIdgrupointeres();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getActivo();
            $res[] = $request->query->get('filters') ? 1 : $pr->getHoja();
            $dpa[] = $res;
        }
        if ($tol > 0) {
            return new JsonResponse(array('data' => $dpa, 'total' => $tol));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * Creates a new NomGrupointeres entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_GRUPOINTERES')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomNomGrupointeres = new NomGrupointeres();
        $form = $this->createForm('NomencladorBundle\Form\NomGrupointeresType', $nomNomGrupointeres);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomGrupo = $request->request->get('nom_grupointeres');
            $grupos = json_decode($nomGrupo['grupos']);
            $nomNomGrupointeres->setActivo(1);
            $nomNomGrupointeres->setHoja(1);
            $nomNomGrupointeres->setIdpadre(0);
            $nomNomGrupointeres->setIdentidad(null);
            if (count($grupos) > 0) {
                foreach ($grupos as $grupo) {
                    $nomNomGrupointeres->setIdpadre($grupo);
                    $grupop = $em->getRepository('NomencladorBundle:NomGrupointeres')->find($grupo);
                    $nomNomGrupointeres->setNivel($grupop->getNivel() + 1);
                    $em->persist($grupop);
                    $em->persist($nomNomGrupointeres);
                }
            } else {
                $nomNomGrupointeres->setNivel(0);
                $em->persist($nomNomGrupointeres);
            }
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            return $this->redirectToRoute('grupointeres_new');
        }

        return $this->render('NomencladorBundle:nomgrupointeres:new.html.twig', array(
            'form' => $form->createView(),
            'accion' => 'Adicionar'
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
     */
    public function listarEntAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();
        $filters = array();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 20;
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;

        $where = '1=1';
        if ($request->query->get('filters')) {
            $filters_raw = explode('|', $request->query->get('filters'));
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                $alias = Util::getSlug($sp[1]);
                $where .= 'AND cp.' . $sp[0] . ' LIKE \'%' . $alias . '%\'';
                $parent = -1;
            }
        }

        $where .= $parent != -1 ? ' AND cp.idpadre=' . $parent : '';
        $nomEntidads = $em->getRepository('NomencladorBundle:NomEntidad')->listardatosEnt($st, $lm, $where);

        $array = array();
        foreach ($nomEntidads['datos'] as $pr) {
            $res = array();
            $res[] = $pr['identidad'];
            $res[] = $pr['codigo'];
            $res[] = $pr['nombre'];
            $res[] = $pr['hoja'];

            $array[] = $res;
        }
        if ($nomEntidads['count'] > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $nomEntidads['count']));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_GRUPOINTERES')")
     */
    public function guardarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $grupo = $request->request->get('grupos');
        $entidades = $request->request->get('entidades');
        if ($grupo[0] && $entidades[0]) {
            foreach ($grupo as $grupt) {
                $grupoPadre = $padre = $padreEnt = 0;
                $arrayE = array();
                $arrayG = array();
                foreach ($entidades as $ent) {
                    $grupo = new NomGrupointeres();
                    $entidad = $em->getRepository('NomencladorBundle:NomEntidad')->find($ent);
                    $con = array_search($entidad->getIdpadre(), $arrayE);
                    $nodo = ($con === false) ? 1 : 0;
                    $ccCod = $em->getRepository('NomencladorBundle:NomGrupointeres')->codigoExitsXPadre('NomencladorBundle:NomGrupointeres',
                        $entidad->getCodigo(), 'codigo', $grupt);
                    if ($ccCod == -1) {//no existe
                        if (($padre == $entidad->getIdpadre()) && ($padre != 0)) {
                            $grupo->setIdpadre($grupoPadre->getIdgrupointeres());
                            $grupoPadre->setHoja(0);
                            $em->persist($grupoPadre);
                            $grupo->setNivel($grupoPadre->getNivel() + 1);
                        } else {
                            if ($entidad->getIdpadre() != 0 && $nodo == 0) {
                                $grupoP = $em->getRepository('NomencladorBundle:NomGrupointeres')->find($arrayG[$con]);
                                if ($grupoP) {
                                    $grupoP->setHoja(0);
                                    $em->persist($grupoP);
                                }
                                $grupo->setIdpadre($grupoP->getIdgrupointeres());
                                if ($entidad->getIdpadre() == $padreEnt) {
                                    $grupo->setNivel($grupoPadre->getNivel() + 1);
                                } else {
                                    $grupo->setNivel($grupoP->getNivel() + 1);
                                }
                            } else {

                                $grupop = $em->getRepository('NomencladorBundle:NomGrupointeres')->find($grupt);
                                $grupo->setIdpadre($grupt);
                                $grupop->setHoja(0);
                                $em->persist($grupop);
                                $grupo->setNivel($grupop->getNivel() + 1);
                            }
                        }
                        $grupo->setNombre($entidad->getNombre());
                        $grupo->setCodigo($entidad->getCodigo());
                        $grupo->setActivo(1);
                        $grupo->setHoja(1);
                        $grupo->setIdentidad($entidad);
                        $em->persist($grupo);
                        $em->flush();
                        $padre = $ent;
                        $padreEnt = $entidad->getIdpadre();
                        $arrayE[] = $entidad->getIdentidad();
                        $arrayG[] = $grupo->getIdgrupointeres();
                        $grupoPadre = $grupo;
                    } else {
                        $this->addFlash('danger', 'Existe un elemento con el mismo código.');
                        return new JsonResponse(array('respuesta' => -1));
                    }
                }
                $this->get('nomenclador.nomprecio')->preciosEmpresa($grupt, $arrayG);
            }
        }
        $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
        return new JsonResponse(array('respuesta' => 1));
    }

    /**
     * Finds and displays a NomGrupointeres entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
     */
    public function showAction(NomGrupointeres $nomGrupointere)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $deleteForm = $this->createDeleteForm($nomGrupointere);

        return $this->render('NomencladorBundle:nomgrupointeres/show.html.twig', array(
            'nomGrupointere' => $nomGrupointere,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomGrupointeres entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_GRUPOINTERES')")
     */
    public function editAction(Request $request, NomGrupointeres $nomGrupointere)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomGrupointere);
        $editForm = $this->createForm('NomencladorBundle\Form\NomGrupointeresType', $nomGrupointere);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomGrupointere);
            $em->flush();
            $params['tabla'] = 'NomGrupointeres';
            $params['campoId'] = 'idgrupointeres';
            $padres = $this->get('nomencladores')->getObjPadres($nomGrupointere, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $nomGrupointere->getIdgrupointeres());
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('grupointeres_new', array('id' => $nomGrupointere->getIdgrupointeres()));
        }
        return $this->render('NomencladorBundle:nomgrupointeres/new.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'accion' => 'Editar'
        ));
    }

    /**
     * Deletes a NomGrupointeres entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_GRUPOINTERES')")
     */
    public function deleteAction(Request $request, NomGrupointeres $nomGrupointere)
    {

        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomGrupointere);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomGrupointere);
            $em->flush();
        }
        return $this->redirectToRoute('grupointeres_new');
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_GRUPOINTERES')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $grupo = $request->request->get('id');

        $params['campoId'] = "idgrupointeres";
        $params['tabla'] = "NomGrupointeres";
        $params['valor'] = $grupo;
        $params['arbol'] = true;
        $params['servicio'] = 'nomenclador.nomgrupointeres';
        if (!is_array($params['valor'])) {
            $params['valor'] = [$params['valor']];
        }
        $precios = $this->getDoctrine()->getRepository('NomencladorBundle:NomPrecio')->findAll();
        $gruposAux = array();
        foreach ($precios as $value) {
            $gruposAux = array_merge($gruposAux, $value->getGrupo());
        }
        $grupos = array_unique($gruposAux);
        $elementosPadresNoEliminar = array();
        foreach ($params['valor'] as $valueObj) {
            //Compruebo si esta en uso o no
            if (in_array($valueObj, $grupos)) {
                $result['enUso'] = true;
                $padres = array();
                $objEnt = $this->getDoctrine()->getRepository("NomencladorBundle:" . $params['tabla'])->find($valueObj);
                $padresObj = $this->get('nomencladores')->getObjPadres($objEnt, $padres, $params);
                $elementosPadresNoEliminar = array_merge($elementosPadresNoEliminar, $padresObj);
                $padresObj = array();
            } else {
                if (!in_array($valueObj, $elementosPadresNoEliminar)) {
                    $params['hijo'] = $valueObj;
                    $this->get('nomencladores')->consultaEliminarObj($params);
                }
            }
        }

        if ($result['enUso']) {
            $result['msg'] = 'error';
        } else {
            $result['msg'] = 'exito';
        }

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Grupos de Interés que no están en uso. Verifique en el Nomenclador Precio.';
        } else {
            if ($result['enUso'] && count($params['valor']) == 1) {
                $result['mensaje'] = 'No se pudo eliminar el Grupo de Interés seleccionado ya que está en uso. Verifique en el Nomenclador Precio.';
            }
        }

        if ($result['msg'] == 'exito' && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron satisfactoriamente los elementos seleccionados.';
        } else {
            if ($result['msg'] == 'exito' && count($params['valor']) == 1) {
                $result['mensaje'] = 'Se eliminó satisfactoriamente el elemento seleccionado.';
            }
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * Creates a form to delete a NomGrupointeres entity.
     *
     * @param NomGrupointeres $nomGrupointere The NomGrupointeres entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_GRUPOINTERES')")
     */
    private function createDeleteForm(NomGrupointeres $nomGrupointere)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('grupointeres_delete', array('id' => $nomGrupointere->getIdgrupointeres())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
     */
    public function SetActivoAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $elementos = $request->request->get('id');
        $accion = $request->request->get('accion');
        $em = $this->getDoctrine()->getManager();
        $accion == 'btn_activar' ? $estado = 1 : $estado = 0;
        if (!is_array($elementos)) {
            $elementos = [$elementos];
        }
        foreach ($elementos as $value) {
            $hijas = array();
            $grupoInteres = $em->getRepository('NomencladorBundle:NomGrupointeres')->find($value);
            if ($grupoInteres != null) {
                if (!$grupoInteres->getHoja()) {
                    $hijosGrupo = $this->get('nomenclador.nomgrupointeres')->getGruposHijos($grupoInteres, $hijas, null,
                        true);
                    foreach ($hijosGrupo as $valueGru) {
                        $dqlhijos = 'UPDATE NomencladorBundle:NomGrupointeres g SET g.activo =:activo WHERE g.idgrupointeres  = :idgrupointeres';
                        $consulta = $em->createQuery($dqlhijos);
                        $consulta->setParameter('activo', $estado);
                        $consulta->setParameter('idgrupointeres', $valueGru);
                        $consulta->execute();
                    }
                } else {
                    $grupoInteres->setActivo($estado);
                    $em->persist($grupoInteres);
                    $em->flush();
                }
            }
        }
        return new JsonResponse(array('respuesta' => 'exito'));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
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
        $data = $em->getRepository('NomencladorBundle:NomGrupointeres')->listarDelExportarGruposIntereses($filtroCodigo,
            $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Grupo de Interés')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador grupointeres sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador grupointeres')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(80);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Grupos de Interés');

        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Activo');
        $celda->setTitle('Grupointeres');
        $celda->fromArray($data['data'], '', 'A6');
        $header = 'a5:c' . (count($data['data']) + 5);
        $cabecera = 'a5:c5';
        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
        $style = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,),
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => 'FF000000')
                )
            ),
        );
        $style2 = array(
            'font' => array('bold' => true,),
            'borders' => array(
                'outline' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array('argb' => 'FF000000')
                )
            ),
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
        $params['nameArchivo'] = "grupos_interés";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
     */
    public function listagrupointeresAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();
        $nom = $em->getRepository('NomencladorBundle:NomGrupointeres')->findAll();
        return $this->render('NomencladorBundle:nomGrupointeres:listagrupointeres.html.twig', array('grupos' => $nom));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_GRUPOINTERES')")
     */
    public function listarhojasAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $nom = $em->getRepository('NomencladorBundle:NomGrupointeres')->listarGrupoHojas();
        return $this->render('NomencladorBundle:nomGrupointeres:listagrupointeres.html.twig', array('grupos' => $nom));
    }

    public function getTreeAction(Request $request)
    {
        $node = $request->get('id') === '#' ? 0 : $request->get('id');
        $data = $this->get('nomenclador.nomgrupointeres')->getChildren($node);
        return new JsonResponse($data);
    }
}
