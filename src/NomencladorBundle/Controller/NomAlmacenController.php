<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use NomencladorBundle\Entity\NomAlmacen;
use NomencladorBundle\Form\NomAlmacenType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomAlmacen controller.
 *
 */
class NomAlmacenController extends Controller
{
    /**
     * Lists all NomAlmacen entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ALMACEN')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();

        $nomAlmacenes = $em->getRepository('NomencladorBundle:NomAlmacen')->findAll();

        return $this->render('NomencladorBundle:nomalmacen:index.html.twig', array(
            'nomAlmacenes' => $nomAlmacenes,
            'remember'=>$remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ALMACEN')")
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
                if ($sp[0] == "ueb") {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND ub.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == "nombre") {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND g.alias LIKE \'%' . $alias . '%\'';
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
        $dql = 'SELECT g
            FROM NomencladorBundle:NomAlmacen g
            JOIN g.ueb ub
            WHERE ' . $where . ' ORDER BY g.codigo ';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIdalmacen();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getNevera();
            $res[] = $pr->getUeb()->getNombre();
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
     * Creates a new NomAlmacen entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_ALMACEN')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nom = new NomAlmacen();
        $form = $this->createForm('NomencladorBundle\Form\NomAlmacenType', $nom);
        $form->handleRequest($request);
        $almacenDat = $request->request->get('nom_almacen');
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $almacen = new NomAlmacen();
            $almacen->setActivo(true);
            $almacen->setCodigo($almacenDat['codigo']);
            $almacen->setNombre($almacenDat['nombre']);
            $ueb = $em->getRepository('NomencladorBundle:NomUeb')->find($almacenDat['ueb']);
            $almacen->setUeb($ueb);
            $almacenDat['nevera'] == 'true' || $almacenDat['nevera'] == 1 ? $nev = true : $nev = false;
            $almacen->setNevera($nev);
            $em->persist($almacen);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('almacen_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('almacen_new');
        }
        return $this->render('NomencladorBundle:nomalmacen:new.html.twig', array(
            'nomAlmacen' => $nom,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * Displays a form to edit an existing NomAlmacen entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_ALMACEN')")
     */
    public function editAction(Request $request, NomAlmacen $nomAlmacen)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $editForm = $this->createForm('NomencladorBundle\Form\NomAlmacenType', $nomAlmacen);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomAlmacen);
            $em->flush();
            return $this->redirectToRoute('almacen_index',array('remember'=> 1));
        }
        return $this->render('NomencladorBundle:nomalmacen:new.html.twig', array(
            'nomAlmacen' => $nomAlmacen,
            'form' => $editForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * Deletes a NomAlmacen entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ALMACEN')")
     */
    public function deleteAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $almacen = $request->request->get('id');

        $params['campoId'] = "idalmacen";
        $params['tabla'] = "NomAlmacen";
        $params['valor'] = $almacen;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Almacenes que no están en uso. Verifique en los Partes Diarios de Venta, Movimiento de Almacén 
                                  y Mercancía por Vínculo.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Almacén seleccionado ya que está en uso. Verifique en los Partes Diarios de Venta, Movimiento de Almacén 
                                  ,Mercancía por Vínculo y Producción.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ALMACEN')")
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
            $alm = $em->getRepository('NomencladorBundle:NomAlmacen')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $alm->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ALMACEN')")
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
        $filtroUeb = $request->query->get("ueb");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomAlmacen')->listarDelExportarAlmacen($filtroCodigo, $filtroNombre, $filtroUeb);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Almacenes')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador almacen del sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador almacen')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Almacén');
        $celda->setCellValue('A5', 'Código');
        $celda->setCellValue('B5', 'Nombre');
        $celda->setCellValue('C5', 'Ueb');
        $celda->setCellValue('D5', 'Nevera');
        $celda->setCellValue('E5', 'Activo');
        $celda->fromArray($data, '', 'A6');
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

        $params['nameArchivo'] = "almacen";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}
