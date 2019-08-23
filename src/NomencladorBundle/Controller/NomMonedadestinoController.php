<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Entity\NomMonedadestino;
use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomMonedadestino controller.
 *
 */
class NomMonedadestinoController extends Controller
{
    /**
     * Lists all NomMonedadestino entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_MONEDADESTINO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $nomMonedadestinos = $em->getRepository('NomencladorBundle:NomMonedadestino')->findAllOrderedByCodigo();

        return $this->render('NomencladorBundle:nommonedadestino:index.html.twig', array(
            'nomMonedadestinos' => $nomMonedadestinos,
            'remember'=>$remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_MONEDADESTINO')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

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
            FROM NomencladorBundle:NomMonedadestino g
            WHERE ' . $where . ' ORDER BY g.codigo ';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nomgenerico = $consulta->getResult();
        $gene = array();
        foreach ($nomgenerico as $pr) {
            $res = array();
            $res[] = $pr->getId();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getActivo();
            $gene[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $gene, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * Creates a new NomMonedadestino entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_MONEDADESTINO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomMonedadestino = new NomMonedadestino();
        $form = $this->createForm('NomencladorBundle\Form\NomMonedadestinoType', $nomMonedadestino);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomMonedadestino->setActivo(true);
            $em->persist($nomMonedadestino);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('monedadestino_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('monedadestino_new');

        }
        return $this->render('NomencladorBundle:nommonedadestino:new.html.twig', array(
            'nomMonedadestino' => $nomMonedadestino,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * Finds and displays a NomMonedadestino entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_MONEDADESTINO')")
     */
    public function showAction(NomMonedadestino $nomMonedadestino)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomMonedadestino);
        return $this->render('NomencladorBundle:nommonedadestino:show.html.twig', array(
            'nomMonedadestino' => $nomMonedadestino,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomMonedadestino entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_MONEDADESTINO')")
     */
    public function editAction(Request $request, NomMonedadestino $nomMonedadestino)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomMonedadestino);
        $editForm = $this->createForm('NomencladorBundle\Form\NomMonedadestinoType', $nomMonedadestino);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomMonedadestino);
            $em->flush();
            return $this->redirectToRoute('monedadestino_index',array('remember'=> 1));
        }
        return $this->render('NomencladorBundle:nommonedadestino:new.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * Deletes a NomMonedadestino entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_MONEDADESTINO')")
     */
    public function deleteAction(Request $request, NomMonedadestino $nomMonedadestino)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $form = $this->createDeleteForm($nomMonedadestino);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomMonedadestino);
            $em->flush();
        }
        return $this->redirectToRoute('monedadestino_index');
    }

    /**
     * Creates a form to delete a NomMonedadestino entity.
     *
     * @param NomMonedadestino $nomMonedadestino The NomMonedadestino entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_MONEDADESTINO')")
     */
    private function createDeleteForm(NomMonedadestino $nomMonedadestino)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('monedadestino_delete', array('id' => $nomMonedadestino->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_MONEDADESTINO')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $moneda = $request->request->get('id');

        $params['campoId'] = "id";
        $params['tabla'] = "NomMonedadestino";
        $params['valor'] = $moneda;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo las Monedas/Destinos que no están en uso. Verifique en el Nomenclador de Norma de Consumo, 
                                  y también en los Planes y Partes Diario.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar la las Moneda/Destino seleccionado ya que está en uso. Verifique en el Nomenclador de Norma de Consumo, 
                                  y también en los Planes y Partes Diario.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_MONEDADESTINO')")
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
            $tipespecifico = $em->getRepository('NomencladorBundle:NomMonedadestino')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = 1 : $estado = 0;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_MONEDADESTINO')")
     */
    public function listadoAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $nom = $em->getRepository('NomencladorBundle:NomMonedadestino')->findAll();
        return $this->render('NomencladorBundle:nommonedadestino:listado.html.twig', array('md' => $nom));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_MONEDADESTINO')")
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
        $data = $em->getRepository('NomencladorBundle:NomMonedadestino')->listarDelExportarMonedaDestino($filtroCodigo, $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Tipo entidad')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador concepto sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('Exportar Nomenclador Medidor')
            ->setCategory('Exportar');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Medidor');

        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Activo');
        $celda->setTitle('Medidor');
        $celda->fromArray($data, '', 'A6');
        $objPHPExcel->setActiveSheetIndex(0);
        $header = 'a5:c' . (count($data) + 5);
        $cabecera = 'a5:c5';
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

        $params['nameArchivo'] = "monedas_destinos";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }
}
