<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use NomencladorBundle\Entity\NomSubgenerico;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);


/**
 * NomSubgenerico controller.
 *
 */
class NomSubgenericoController extends Controller
{
    /**
     * Lists all NomSubgenerico entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_SUBGENERICO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomsubgenerico:index.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_SUBGENERICO')")
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
        $where = ' 1=1';
        $filters_raw = $request->query->get('filters');
        if (!in_array("activo:0", $filters_raw) && !in_array("activo:1", $filters_raw) && !in_array("activo:2", $filters_raw)) {
            $where .= " AND g.activo = 1";
        }
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'generico') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND gen.alias LIKE \'%' . $alias . '%\'';
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
        $datos = $em->getRepository('NomencladorBundle:NomSubgenerico')->listar($where, $st, $lm);
        $array = array();
        foreach ($datos['datos'] as $pr) {
            $res = array();
            $res[] = $pr->getIdsubgenerico();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $gene = $pr->getGenerico();
            $res[] = $gene->getNombre();
            $res[] = $pr->getEmpaque();
            $res[] = $pr->getActivo();
            $array[] = $res;
        }
        if (count($datos['total']) > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $datos['total']));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     *
     * Creates a new NomSubgenerico entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_SUBGENERICO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomSubgenerico = new NomSubgenerico();
        $form = $this->createForm('NomencladorBundle\Form\NomSubgenericoType', $nomSubgenerico);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomSubgenerico->setActivo(true);
            $em->persist($nomSubgenerico);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('subgenerico_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('subgenerico_new');
        }
        return $this->render('NomencladorBundle:nomsubgenerico:new.html.twig', array(
            'nomSubgenerico' => $nomSubgenerico,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * Finds and displays a NomSubgenerico entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_SUBGENERICO')")
     */
    public function showAction(NomSubgenerico $nomSubgenerico)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $deleteForm = $this->createDeleteForm($nomSubgenerico);

        return $this->render('NomencladorBundle:nomsubgenerico:show.html.twig', array(
            'nomSubgenerico' => $nomSubgenerico,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomSubgenerico entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_SUBGENERICO')")
     */
    public function editAction(Request $request, NomSubgenerico $nomSubgenerico)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $deleteForm = $this->createDeleteForm($nomSubgenerico);
        $editForm = $this->createForm('NomencladorBundle\Form\NomSubgenericoType', $nomSubgenerico);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomSubgenerico);
            $em->flush();
            return $this->redirectToRoute('subgenerico_index',array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:nomsubgenerico:new.html.twig', array(
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * Deletes a NomSubgenerico entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_SUBGENERICO')")
     */
    public function deleteAction(Request $request, NomSubgenerico $nomSubgenerico)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $form = $this->createDeleteForm($nomSubgenerico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomSubgenerico);
            $em->flush();
        }

        return $this->redirectToRoute('subgenerico_index');
    }

    /**
     * Creates a form to delete a NomSubgenerico entity.
     *
     * @param NomSubgenerico $nomSubgenerico The NomSubgenerico entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_SUBGENERICO')")
     */
    private function createDeleteForm(NomSubgenerico $nomSubgenerico)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('subgenerico_delete', array('id' => $nomSubgenerico->getIdsubgenerico())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_SUBGENERICO')")
     */
    public function subgenericoEliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $subgenerico = $request->request->get('id');

        $params['campoId'] = "idsubgenerico";
        $params['tabla'] = "NomSubgenerico";
        $params['valor'] = $subgenerico;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Sub-Genéricos que no están en uso. Verifique en el Nomenclador Producto.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Sub-Genérico seleccionado ya que está en uso. Verifique en el Nomenclador Producto.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_SUBGENERICO')")
     */
    public function findByIdGenericoAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $error = false;
        $id = $request->request->get('id');
        $subgenericos = $em->getRepository('NomencladorBundle:NomSubgenerico')->
        findByGenerico($id);
        $lista = [];
        foreach ($subgenericos as $sub) {
            $lista[] = [
                'id' => $sub->getIdsubgenerico(),
                'nombre' => $sub->getNombre(),
                'codigo' => $sub->getCodigo()
            ];
        }
        return new JsonResponse($lista);
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_SUBGENERICO')")
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
            $tipespecifico = $em->getRepository('NomencladorBundle:NomSubgenerico')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_SUBGENERICO')")
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
        $filtroGenerico = $request->query->get("generico");
        $em = $this->getDoctrine()->getManager();
        /*aki esta apuntando a la entidad NomGrupointeres pk esta apunta el repositorio ComunRepository q es donde esta implementado
        este metodo y no en el repositoprio NomSubgenericoRepository, ver como acceder de un repo a otro*/
        $data = $em->getRepository('NomencladorBundle:NomGrupointeres')->listarDelExportarSubGenerico($filtroCodigo, $filtroNombre, $filtroGenerico);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Subgenéricos')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador subgénerico sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador subgenérico')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Subgenéricos');

        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Genérico')
            ->setCellValue('D5', 'Empaque')
            ->setCellValue('E5', 'Activo');
        $celda->setTitle('Subgenéricos');
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
        $params['nameArchivo'] = "subgenérico";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }
}
