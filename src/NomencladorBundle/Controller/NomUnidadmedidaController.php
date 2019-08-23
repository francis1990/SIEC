<?php

namespace NomencladorBundle\Controller;

use EnumsBundle\Entity\EnumTipoUnidadMedida;
use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Form\NomUnidadmedidaType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomUnidadmedida controller.
 *
 */
class NomUnidadmedidaController extends Controller
{
    /**
     * Lists all NomUnidadmedida entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_UM')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomunidadmedida:index.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_UM')")
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
                if ($sp[0] == 'tipoum') {
                    $objEnum = new EnumTipoUnidadMedida();
                    $resultIds = $objEnum->obtenerIdUMDadoName($sp[1]);
                    $where .= ' AND g.idtipoum in(' . $resultIds . ')';
                } elseif ($sp[0] == "activo") {
                    if ($sp[1] < 2) {
                        $where .= 'AND g.activo = ' . $sp[1];
                    }
                } else {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND g.' . $sp[0] . ' LIKE \'%' . $alias . '%\'';
                }
            }
        }
        $dql = 'SELECT g FROM NomencladorBundle:NomUnidadmedida g        
        WHERE ' . $where . 'ORDER BY g.codigo';
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIdunidadmedida();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getAbreviatura();
            $res[] = $pr->getStrTiposUnidadMedida();
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
     * Creates a new NomUnidadmedida entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_UM')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomUnidadmedida = new NomUnidadmedida();
        $form = $this->createForm('NomencladorBundle\Form\NomUnidadmedidaType', $nomUnidadmedida);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomUnidadmedida->setActivo(1);
            $em->persist($nomUnidadmedida);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('unidadmedida_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('unidadmedida_new');
        }

        return $this->render('NomencladorBundle:nomunidadmedida:new.html.twig', array(
            'nomUnidadmedida' => $nomUnidadmedida,
            'form' => $form->createView(),
            'accion' => "Adicionar"
        ));
    }

    /**
     * Finds and displays a NomUnidadmedida entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_UM')")
     */

    public function showAction(NomUnidadmedida $nomUnidadmedida)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomUnidadmedida);

        return $this->render('NomencladorBundle:nomunidadmedida:show.html.twig', array(
            'nomUnidadmedida' => $nomUnidadmedida,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomUnidadmedida entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_UM')")
     */
    public function editAction(Request $request, NomUnidadmedida $nomUnidadmedida)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomUnidadmedida);
        $editForm = $this->createForm('NomencladorBundle\Form\NomUnidadmedidaType', $nomUnidadmedida);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomUnidadmedida);
            $em->flush();
            return $this->redirectToRoute('unidadmedida_index',array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:NomUnidadmedida:new.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'accion' => "Editar",

        ));
    }

    /**
     * Deletes a NomUnidadmedida entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_UM')")
     */
    public function deleteAction(Request $request, NomUnidadmedida $nomUnidadmedida)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomUnidadmedida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomUnidadmedida);
            $em->flush();
        }

        return $this->redirectToRoute('unidadmedida_index');
    }

    /**
     * Creates a form to delete a NomUnidadmedida entity.
     *
     * @param NomUnidadmedida $nomUnidadmedida The NomUnidadmedida entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_UM')")
     */
    private function createDeleteForm(NomUnidadmedida $nomUnidadmedida)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('unidadmedida_delete', array('id' => $nomUnidadmedida->getIdunidadmedida())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_UM')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $um = $request->request->get('id');

        $params['campoId'] = "idunidadmedida";
        $params['tabla'] = "NomUnidadmedida";
        $params['valor'] = $um;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo las Unidades de Medida que no están en uso. Verifique en los Nomencladores de Producto, Precio, Aseguramiento, Normas de Consumo
                                Portadores, así como en los Planes y en todos los Partes Diario excepto en los de Acopio, Cuentas por Cobrar, Cuentas Contable y Transporte.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar la Unidad de Medida seleccionada ya que está en uso. Verifique en los Nomencladores de Producto, Precio, Aseguramiento, Normas de Consumo
                                Portadores, así como en los Planes y en todos los Partes Diario excepto en los de Acopio, Cuentas por Cobrar, Cuentas Contable y Transporte.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }


    /**
     * @Security("is_granted('ROLE_LISTAR_UM')")
     */

    public function listadoAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $nomUnidadmedidas = $em->getRepository('NomencladorBundle:NomUnidadmedida')->findAll();

        return $this->render('NomencladorBundle:nomunidadmedida:listaum.html.twig', array(
            'ums' => $nomUnidadmedidas,
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_UM')")
     */

    public function listadoUMTipoVolumenAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $nomUnidadmedidas = $em->getRepository('NomencladorBundle:NomUnidadmedida')->findBy(array('idtipoum' => 3));

        return $this->render('NomencladorBundle:nomunidadmedida:listaum.html.twig', array(
            'ums' => $nomUnidadmedidas,
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_UM')")
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
            $tipespecifico = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_UM')")
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
        $filtroAbrev = $request->query->get("abreviatura");
        $filtroTipo = $request->query->get("tipoum");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomGrupointeres')->listarDelExportarUnidadMedida($filtroCodigo, $filtroNombre, $filtroAbrev, $filtroTipo);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('unidadmedidas')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador unidadmedida sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador unidadmedida')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Unidad de Medida');
        $celda->setTitle('unidadmedida');
        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Abreviatura')
            ->setCellValue('D5', 'Tipo UM')
            ->setCellValue('E5', 'Activo');
        $celda->fromArray($data, '', 'A6');
        $objPHPExcel->setActiveSheetIndex(0);
        $header = 'a5:e' . (count($data) + 5);
        $cabecera = 'a5:e5';
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
        $params['nameArchivo'] = "unidad_medida";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}
