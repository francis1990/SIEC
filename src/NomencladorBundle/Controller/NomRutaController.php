<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use NomencladorBundle\Entity\NomRuta;
use NomencladorBundle\Entity\NomRutaSuministrador;
use NomencladorBundle\Form\NomRutaType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomRuta controller.
 *
 */
class NomRutaController extends Controller
{
    /**
     * Lists all NomRuta entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_RUTA')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $nomRutas = $em->getRepository('NomencladorBundle:NomRuta')->findAll();

        return $this->render('NomencladorBundle:nomruta:index.html.twig', array(
            'nomResults' => $nomRutas,
            'remember'=>$remember
        ));
    }

    /**
     * Creates a new NomRuta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_RUTA')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomRutum = new NomRuta();
        $form = $this->createForm('NomencladorBundle\Form\NomRutaType', $nomRutum);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $nomRutum->setActivo(true);
            $em->persist($nomRutum);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('ruta_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('ruta_new');
        }

        return $this->render('NomencladorBundle:nomruta:new.html.twig', array(
            'nomRutum' => $nomRutum,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_RUTA')")
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
            FROM NomencladorBundle:NomRuta g  WHERE ' . $where . 'ORDER BY g.codigo';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();

        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIdruta();
            $res[] = $pr->getCodigo() == null ? '' : $pr->getCodigo();
            $res[] = $pr->getNombre();
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
     * Finds and displays a NomRuta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_RUTA')")
     */
    public function showAction(NomRuta $nomRutum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomRutum);

        return $this->render('NomencladorBundle:nomruta:show.html.twig', array(
            'nomRutum' => $nomRutum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomRuta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_RUTA')")
     */
    public function editAction(Request $request, NomRuta $nomRutum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $editForm = $this->createForm('NomencladorBundle\Form\NomRutaType', $nomRutum);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomRutum);
            $em->flush();
            return $this->redirectToRoute('ruta_index',array('remember'=> 1));
        }
        return $this->render('NomencladorBundle:nomruta:new.html.twig', array(
            'nomRutum' => $nomRutum,
            'form' => $editForm->createView(),
            "accion" => "Editar"
        ));
    }


    /**
     * Deletes a NomRuta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_RUTA')")
     */
    public function deleteAction(Request $request, NomRuta $nomRutum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomRutum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomRutum);
            $em->flush();
        }

        return $this->redirectToRoute('ruta_index');
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_RUTA')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $idruta = $request->request->get('id');
        $params['campoId'] = "idruta";
        $params['tabla'] = "NomRuta";
        $params['valor'] = $idruta;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo las Rutas que no están en uso. Verifique en el Parte Diaro de Acopio.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar la Ruta seleccionada ya que está en uso. Verifique en el Parte Diaro de Acopio.';
        }
        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_RUTA')")
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
                $GLOBALS['kernel']->getContainer()->get('parte_diario.comun_service')->deleteOne('NomencladorBundle:NomRuta', $elemento);
                return new JsonResponse(array('respuesta' => 'exito'));
            } catch (ForeignKeyConstraintViolationException $e) {
                return new JsonResponse(array('respuesta' => 'error'));
            }
        } else {
            return new JsonResponse(array('respuesta' => 'error'));
        }

    }

    /**
     * Creates a form to delete a NomRuta entity.
     *
     * @param NomRuta $nomRutum The NomRuta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_RUTA')")
     */
    private function createDeleteForm(NomRuta $nomRutum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ruta_delete', array('id' => $nomRutum->getIdruta())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_RUTA')")
     */
    public function listadoAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $nom = $em->getRepository('NomencladorBundle:NomRuta')->findAll();

        return $this->render('NomencladorBundle:nomruta:listarutas.html.twig', array('rutas' => $nom));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_RUTA')")
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
        $data = $em->getRepository('NomencladorBundle:NomRutaSuministrador')->listarDelExportarRutas($filtroCodigo, $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Genéricos')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador genérico sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador genérico')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(80);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(80);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Ruta');
        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Producto')
            ->setCellValue('D5', 'Entidad')
            ->setCellValue('E5', 'Activo');
        $celda->setTitle('Ruta');
        $celda->fromArray($data, '', 'A6');
        $header = 'a5:e' . (count($data) + 5);
        $cabecera = 'a5:e5';
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
        $params['nameArchivo'] = "rutas";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_RUTA')")
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
            $ruta = $em->getRepository('NomencladorBundle:NomRuta')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $ruta->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_RUTA')")
     */
    public function mostrarAction(Request $request, NomRuta $ruta)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $where = '1=1';

        $dql = 'SELECT p,g,ent,r
            FROM NomencladorBundle:NomRutaSuministrador g
            JOIN g.producto p
            JOIN g.entidad ent
            join g.ruta r
            WHERE r=:ruta order by ent.nombre';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ruta', $ruta->getIdruta());
        $sum = $consulta->getResult();


        return $this->render('NomencladorBundle:nomruta:mostrar.html.twig',
            array('suministradores' => $sum,
                'ruta' => $ruta));
    }
}
