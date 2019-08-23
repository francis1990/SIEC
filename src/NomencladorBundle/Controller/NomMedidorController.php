<?php

namespace NomencladorBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use NomencladorBundle\Entity\NomMedidor;
use NomencladorBundle\Form\NomMedidorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomMedidor controller.
 *
 */
class NomMedidorController extends Controller
{
    /**
     * Lists all NomMedidor entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_MEDIDORPORTADOR')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $nomMedidors = $em->getRepository('NomencladorBundle:NomMedidor')->findAll();

        return $this->render('NomencladorBundle:nommedidor:index.html.twig', array(
            'nomMedidors' => $nomMedidors,
            'remember'=>$remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_MEDIDORPORTADOR')")
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
            FROM NomencladorBundle:NomMedidor g  WHERE ' . $where . 'ORDER BY g.nombre';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();

        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getId();
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
     * Creates a new NomMedidor entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_MEDIDORPORTADOR')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomMedidor = new NomMedidor();
        $form = $this->createForm('NomencladorBundle\Form\NomMedidorType', $nomMedidor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomMedidor->setActivo(true);
            $em->persist($nomMedidor);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('nommedidor_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('nommedidor_new');
        }

        return $this->render('NomencladorBundle:nommedidor:new.html.twig', array(
            'nomMedidor' => $nomMedidor,
            'form' => $form->createView(),
            'action' => 'Adicionar'
        ));
    }

    /**
     * Displays a form to edit an existing NomMedidor entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_MEDIDORPORTADOR')")
     */
    public function editAction(Request $request, NomMedidor $nomMedidor)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $editForm = $this->createForm('NomencladorBundle\Form\NomMedidorType', $nomMedidor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomMedidor);
            $em->flush();

            return $this->redirectToRoute('nommedidor_index',array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:nommedidor:new.html.twig', array(
            'nomMedidor' => $nomMedidor,
            'form' => $editForm->createView(),
            'action' => 'Editar'
        ));
    }

    /**
     * Deletes a NomMedidor entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_MEDIDORPORTADOR')")
     */
    public function deleteAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $medidor = $request->request->get('id');

        $params['campoId'] = "id";
        $params['tabla'] = "NomMedidor";
        $params['valor'] = $medidor;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Medidores de portadores energéticos que no están en uso. Verifique en el Parte Diaro de Portadores energéticos.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Medidor de portadores energéticos seleccionado ya que está en uso. Verifique en el Parte Diaro de Portadores energéticos.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_MEDIDORPORTADOR')")
     */
    public function exportarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $filtroNombre = $request->query->get("nombre");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomMedidor')->listarDelExportarMedidor($filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Tipo entidad')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador concepto sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('Exportar Nomenclador Medidor')
            ->setCategory('Exportar');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Medidor');

        $celda->setCellValue('A5', 'Nombre')
            ->setCellValue('B5', 'Activo');
        $celda->setTitle('Medidor');
        $celda->fromArray($data, '', 'A6');
        $objPHPExcel->setActiveSheetIndex(0);
        $header = 'a5:b' . (count($data) + 5);
        $cabecera = 'a5:b5';
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

        $params['nameArchivo'] = "medidores";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_MEDIDORPORTADOR')")
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
            $tipo = $em->getRepository('NomencladorBundle:NomMedidor')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipo->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));
    }

}
