<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use NomencladorBundle\Entity\NomFormato;
use NomencladorBundle\Form\NomFormatoType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomFormato controller.
 *
 */
class NomFormatoController extends Controller
{
    /**
     * Lists all NomFormato entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_FORMATO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomformato:index.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_FORMATO')")
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
                if ($sp[0] == 'um') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND (um.abreviatura LIKE \'%' . $sp[1] . '%\' OR um.alias LIKE \'%' . $alias . '%\')';
                } elseif ($sp[0] == "activo") {
                    if ($sp[1] < 2) {
                        $where .= 'AND g.activo = ' . $sp[1];
                    }
                } else {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND g.' . $sp[0] . ' LIKE \'%' . $alias . '%\'';
                }
            }
        }
        $dql = 'SELECT g
            FROM NomencladorBundle:NomFormato g
            JOIN g.idunidadmedida um
            WHERE ' . $where . ' ORDER BY g.codigo ';

        $consulta = $em->createQuery($dql);

        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $for = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getId();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getPeso();
            $um = $pr->getIdunidadmedida();
            $res[] = $um->getNombre();
            $res[] = $pr->getActivo();
            $for[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $for, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * Creates a new NomFormato entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_FORMATO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomFormato = new NomFormato();
        $form = $this->createForm('NomencladorBundle\Form\NomFormatoType', $nomFormato);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomFormato->setActivo(true);
            $em->persist($nomFormato);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('formato_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('formato_new');
        }
        return $this->render('NomencladorBundle:nomformato:new.html.twig', array(
            'nomFormato' => $nomFormato,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * Finds and displays a NomFormato entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_FORMATO')")
     */
    public function showAction(NomFormato $nomFormato)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomFormato);

        return $this->render('NomencladorBundle:nomformato:show.html.twig', array(
            'nomFormato' => $nomFormato,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomFormato entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_FORMATO')")
     */
    public function editAction(Request $request, NomFormato $nomFormato)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomFormato);
        $editForm = $this->createForm('NomencladorBundle\Form\NomFormatoType', $nomFormato);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomFormato);
            $em->flush();
            return $this->redirectToRoute('formato_index',array('remember'=> 1));
        }
        return $this->render('NomencladorBundle:nomformato:new.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * Deletes a NomFormato entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_FORMATO')")
     */
    public function deleteAction(Request $request, NomFormato $nomFormato)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomFormato);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomFormato);
            $em->flush();
        }

        return $this->redirectToRoute('formato_index');
    }

    /**
     * Creates a form to delete a NomFormato entity.
     *
     * @param NomFormato $nomFormato The NomFormato entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_FORMATO')")
     */
    private function createDeleteForm(NomFormato $nomFormato)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('formato_delete', array('id' => $nomFormato->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_FORMATO')")
     */
    public function formatoEliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $formato = $request->request->get('id');

        $params['campoId'] = "id";
        $params['tabla'] = "NomFormato";
        $params['valor'] = $formato;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Formatos que no están en uso. Verifique en el Nomenclador Producto.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Formato seleccionado ya que está en uso. Verifique en el Nomenclador Producto.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_FORMATO')")
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
            $tipespecifico = $em->getRepository('NomencladorBundle:NomFormato')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_FORMATO')")
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
        $filtroValor = $request->query->get("peso");
        $filtroUm = $request->query->get("um");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomFormato')->listarDelExportarFormato($filtroCodigo, $filtroNombre, $filtroValor, $filtroUm);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Formatos')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador formato sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador formato')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Formatos');
        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Peso')
            ->setCellValue('D5', 'UM')
            ->setCellValue('E5', 'Activo');
        $celda->setTitle('Formatos');
        $celda->fromArray($data, '', 'A6');
        $header = 'a5:e' . (count($data) + 5);
        $cabecera = 'a5:e5';
        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
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
        $params['nameArchivo'] = "formatos";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }
}
