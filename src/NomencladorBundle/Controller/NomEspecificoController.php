<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


use NomencladorBundle\Entity\NomEspecifico;
use NomencladorBundle\Form\NomEspecificoType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomEspecifico controller.
 *
 */
class NomEspecificoController extends Controller
{
    /**
     * Lists all NomEspecifico entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ESPECIFICO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $nomEspecificos = $em->getRepository('NomencladorBundle:NomEspecifico')->findAll();
        return $this->render('NomencladorBundle:nomespecifico:index.html.twig', array(
            'nomEspecificos' => $nomEspecificos,
            'remember'=>$remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ESPECIFICO')")
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
            FROM NomencladorBundle:NomEspecifico g
            WHERE ' . $where . ' ORDER BY g.codigo ';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);

        $nomespecifico = $consulta->getResult();

        $gene = array();
        foreach ($nomespecifico as $pr) {
            $res = array();
            $res[] = $pr->getIdespecifico();
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
     * Creates a new NomEspecifico entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_ESPECIFICO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomEspecifico = new NomEspecifico();
        $form = $this->createForm('NomencladorBundle\Form\NomEspecificoType', $nomEspecifico);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomEspecifico->setActivo(true);
            $em->persist($nomEspecifico);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('especifico_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('especifico_new');
        }

        return $this->render('NomencladorBundle:nomespecifico:new.html.twig', array(
            'nomEspecifico' => $nomEspecifico,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * Finds and displays a NomEspecifico entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ESPECIFICO')")
     */
    public function showAction(NomEspecifico $nomEspecifico)
    {

        $deleteForm = $this->createDeleteForm($nomEspecifico);

        return $this->render('NomencladorBundle:nomespecifico:show.html.twig', array(
            'nomEspecifico' => $nomEspecifico,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomEspecifico entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_ESPECIFICO')")
     */
    public function editAction(Request $request, NomEspecifico $nomEspecifico)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $deleteForm = $this->createDeleteForm($nomEspecifico);
        $editForm = $this->createForm('NomencladorBundle\Form\NomEspecificoType', $nomEspecifico);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomEspecifico);
            $em->flush();
            return $this->redirectToRoute('especifico_index',array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:nomespecifico:new.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * Deletes a NomEspecifico entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ESPECIFICO')")
     */
    public function deleteAction(Request $request, NomEspecifico $nomEspecifico)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $form = $this->createDeleteForm($nomEspecifico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomEspecifico);
            $em->flush();
        }

        return $this->redirectToRoute('especifico_index');
    }

    /**
     * Creates a form to delete a NomEspecifico entity.
     *
     * @param NomEspecifico $nomEspecifico The NomEspecifico entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ESPECIFICO')")
     */
    private function createDeleteForm(NomEspecifico $nomEspecifico)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('especifico_delete', array('id' => $nomEspecifico->getIdespecifico())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_ESPECIFICO')")
     */
    public function especificoEliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $especifico = $request->request->get('id');

        $params['campoId'] = "idespecifico";
        $params['tabla'] = "NomEspecifico";
        $params['valor'] = $especifico;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Específicos que no están en uso. Verifique en el Nomenclador Producto.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Específico seleccionado ya que está en uso. Verifique en el Nomenclador Producto.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ESPECIFICO')")
     */
    public function tipoByIdEspAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $sub = $em->getRepository('NomencladorBundle:NomEspecifico')->find($id);
        $espec = $sub->getIdtipoespecifico();
        $lista = [];
        foreach ($espec as $e) {
            $lista[] = [
                'id' => $e->getIdtipoespecifico(),
                'nombre' => $e->getNombre(),
                'codigo' => $e->getCodigo()
            ];
        }
        return new JsonResponse($lista);
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ESPECIFICO')")
     */
    public function setActivoAction(Request $request)
    {

        $elementos = $request->request->get('id');
        $accion = $request->request->get('accion');
        $em = $this->getDoctrine()->getManager();
        for ($i = 0; $i < count($elementos); $i++) {
            $tipespecifico = $em->getRepository('NomencladorBundle:NomEspecifico')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ESPECIFICO')")
     */
    public function exportarAction(Request $request)
    {
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);

        $filtroCodigo = $request->query->get("codigo");
        $filtroNombre = $request->query->get("nombre");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomEspecifico')->listarDelExportarEspecifico($filtroCodigo, $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Específicos')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador específico sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador específico')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Específico');

        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Activo');

        $celda->setTitle('Específico');
        $celda->fromArray($data, '', 'A6');
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
        $params['nameArchivo'] = "específicos";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}
