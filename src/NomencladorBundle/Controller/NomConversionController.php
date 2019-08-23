<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use NomencladorBundle\Entity\NomConversion;
use NomencladorBundle\Form\NomConversionType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomConversion controller.
 *
 */
class NomConversionController extends Controller
{
    /**
     * Lists all NomConversion entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_CONVERSION')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:NomConversion:index.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CONVERSION')")
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
                if ($sp[0] == 'uminicial') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND (ini.abreviatura LIKE \'%' . $sp[1] . '%\' OR ini.alias LIKE \'%' . $alias . '%\')';
                } else if ($sp[0] == 'umfinal') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND (fin.abreviatura LIKE \'%' . $sp[1] . '%\' OR fin.alias LIKE \'%' . $alias . '%\')';
                } else if ($sp[0] == 'factor') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND g.factor  LIKE \'%' . $alias . '%\' ';
                } elseif ($sp[0] == "activo") {
                    if ($sp[1] < 2) {
                        $where .= 'AND g.activo = ' . $sp[1];
                    }
                }

            }
        }

        $dql = 'SELECT g
            FROM NomencladorBundle:NomConversion g            
            join g.iduminicio ini
            join g.idumfin fin
            WHERE  ' . $where . ' ORDER BY g.iduminicio';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);

        $nom = $consulta->getResult();

        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIdconversion();

            $umi = $pr->getIduminicio();
            $res[] = $umi ? $umi->getNombre() : '';

            $res[] = $pr->getFactor();

            $umf = $pr->getIdumfin();
            $res[] = $umf ? $umf->getNombre() : '';

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
     * Creates a new NomConversion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_CONVERSION')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $NomConversion = new NomConversion();
        $form = $this->createForm('NomencladorBundle\Form\NomConversionType', $NomConversion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $NomConversion->setActivo(true);
            $em->persist($NomConversion);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('conversion_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('conversion_new');
        }
        return $this->render('NomencladorBundle:NomConversion:new.html.twig', array(
            'NomConversion' => $NomConversion,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CONVERSION')")
     */
    public function listarUMAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $tipoum = $request->request->get('idtipoum');
        $unidades = $em->getRepository('NomencladorBundle:NomUnidadmedida')->findBy(array('idtipoum' => $tipoum));
        $arr = array();
        foreach ($unidades as $u) {
            $arr[] = array(
                'id' => $u->getIdunidadmedida(),
                'nombre' => $u->getNombre()
            );
        }

        return new JsonResponse($arr);
    }

    /**
     * Finds and displays a NomConversion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_CONVERSION')")
     */
    public function showAction(NomConversion $NomConversion)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($NomConversion);
        return $this->render('NomencladorBundle:NomConversion:show.html.twig', array(
            'NomConversion' => $NomConversion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomConversion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_CONVERSION')")
     */
    public function editAction(Request $request, NomConversion $NomConversion)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($NomConversion);
        $editForm = $this->createForm('NomencladorBundle\Form\NomConversionType', $NomConversion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $parametros = $request->request->get('nom_conversion');
            $parametros['id'] = $NomConversion->getIdconversion();
            $em = $this->getDoctrine()->getManager();
            $em->persist($NomConversion);
            $em->flush();
            return $this->redirectToRoute('conversion_index',array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:nomconversion:new.html.twig', array(
            'NomConversion' => $NomConversion,
            'form' => $editForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * Deletes a NomConversion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_CONVERSION')")
     */
    public function deleteAction(Request $request, NomConversion $NomConversion)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($NomConversion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            return $this->redirectToRoute('conversion_index');
            $em->remove($NomConversion);
            $em->flush();
        }

    }

    /**
     * Creates a form to delete a NomConversion entity.
     *
     * @param NomConversion $NomConversion The NomConversion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_CONVERSION')")
     */
    private function createDeleteForm(NomConversion $NomConversion)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('conversion_delete', array('id' => $NomConversion->getIdconversion())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_CONVERSION')")
     */
    public function deleteSelectAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $elemento = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($elemento != '') {
            try {
                $GLOBALS['kernel']->getContainer()->get('parte_diario.comun_service')->deleteOne('NomencladorBundle:NomConversion', $elemento);
                return new JsonResponse(array('respuesta' => 'exito'));
            } catch (ForeignKeyConstraintViolationException $e) {
                return new JsonResponse(array('respuesta' => 'error'));
            }
        } else {
            return new JsonResponse(array('respuesta' => 'error'));
        }

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_CONVERSION')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $conversion = $request->request->get('id');

        $params['campoId'] = "idconversion";
        $params['tabla'] = "NomConversion";
        $params['valor'] = $conversion;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo las Conversiones que no están en uso. Verifique en los Nomencladores de Producto, Precio, Aseguramiento, Normas de Consumo
                                Portadores energéticos, así como en los Planes y en todos los Partes Diario excepto en los de Acopio, Cuentas por Cobrar, Cuentas Contable y Transporte.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar la Conversión seleccionada ya que está en uso. Verifique en los Nomencladores de Producto, Precio, Aseguramiento, Normas de Consumo
                                Portadores energéticos, así como en los Planes y en todos los Partes Diario excepto en los de Acopio, Cuentas por Cobrar, Cuentas Contable y Transporte.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CONVERSION')")
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
            $tipespecifico = $em->getRepository('NomencladorBundle:NomConversion')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CONVERSION')")
     */
    public function exportarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $filtroOrigen = $request->query->get("umorigen");
        $filtroDestino = $request->query->get("umdestino");
        $filtroFactor = $request->query->get("factor");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomConversion')->listarDelExportarConversion($filtroOrigen, $filtroDestino, $filtroFactor);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Tipo entidad')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador concepto sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('Exportar Nomenclador Conversión')
            ->setCategory('Exportar');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Conversión');

        $celda->setCellValue('A5', 'Origen')
            ->setCellValue('B5', 'Destino')
            ->setCellValue('C5', 'Factor')
            ->setCellValue('D5', 'Activo');
        $celda->setTitle('Conversiones');
        $celda->fromArray($data, '', 'A6');
        $objPHPExcel->setActiveSheetIndex(0);
        $header = 'a5:d' . (count($data) + 5);
        $cabecera = 'a5:d5';
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

        $params['nameArchivo'] = "conversiones";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }


}
