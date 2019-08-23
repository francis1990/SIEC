<?php

namespace NomencladorBundle\Controller;

use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use NomencladorBundle\Entity\NomDetalle;
use NomencladorBundle\Form\NomDetalleType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/*No notificar errores*/
error_reporting(0);

/**
 * NomDetalle controller.
 *
 */
class NomDetalleController extends Controller
{
    /**
     * Lists all NomDetalle entities.
     *
     */
    public function indexAction($remember)
    {
        $em = $this->getDoctrine()->getManager();

        $nomDetalles = $em->getRepository('NomencladorBundle:NomDetalle')->findAll();

        return $this->render('NomencladorBundle:nomdetalle/index.html.twig', array(
            'remember'=>$remember,
            'nomResults' => $nomDetalles,
        ));
    }

    public function listarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $where = '1=1';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                $where .= 'AND g.' . $sp[0] . ' LIKE \'%' . $sp[1] . '%\'';
            }
        }
        $dql = 'SELECT g
            FROM NomencladorBundle:NomDetalle g
            WHERE ' . $where . 'ORDER BY g.codigo';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);

        $nom = $consulta->getResult();

        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIddetalle();
            $res[] = $pr->getCodigo();
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
     * Creates a new NomDetalle entity.
     *
     */
    public function newAction(Request $request)
    {
        $nomDetalle = new NomDetalle();
        $form = $this->createForm('NomencladorBundle\Form\NomDetalleType', $nomDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomDetalle);
            $em->flush();

            return $this->redirectToRoute('detalle_index', array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:nomdetalle/new.html.twig', array(
            'nomDetalle' => $nomDetalle,
            'form' => $form->createView(),
        ));
    }

    public function insertAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $detalleRe = $request->request->get('detalle');
        $detalle = new NomDetalle();

        $Cod = $em->getRepository('NomencladorBundle:NomDetalle')->codigoExits('NomencladorBundle:NomDetalle', $detalleRe[0]);
        if ($Cod == -1) {//no existe
            $nomDet = $em->getRepository('NomencladorBundle:NomDetalle')->codigoExits('NomencladorBundle:NomDetalle', $detalleRe[1], 'nombre');
            if ($nomDet == -1) {//no existe
                $detalle->setNombre($detalleRe[1]);
                $detalle->setCodigo($detalleRe[0]);
                $detalle->setActivo(1);
                $em->persist($detalle);
                $em->flush();
                $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            } else {
                $Cod = '22';
            }
        }
        return new JsonResponse(array('respuesta' => $Cod));
    }

    /**
     * Finds and displays a NomDetalle entity.
     *
     */
    public function showAction(NomDetalle $nomDetalle)
    {
        $deleteForm = $this->createDeleteForm($nomDetalle);

        return $this->render('NomencladorBundle:nomdetalle/show.html.twig', array(
            'nomDetalle' => $nomDetalle,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomDetalle entity.
     *
     */
    public function editAction(Request $request, NomDetalle $nomDetalle)
    {
        $em = $this->getDoctrine()->getManager();
        $val = $em->getRepository('NomencladorBundle:NomDetalle')->find($nomDetalle->getidDetalle());
        $cod = $val->getCodigo();
        $nom = $val->getNombre();
        $deleteForm = $this->createDeleteForm($nomDetalle);
        $editForm = $this->createForm('NomencladorBundle\Form\NomDetalleType', $nomDetalle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $ccCod = $em->getRepository('NomencladorBundle:NomDetalle')->codigoCant('NomencladorBundle:NomDetalle', $val->getCodigo());
            if (($ccCod > 0 && $cod != $nomDetalle->getCodigo())) {
                $this->addFlash('danger', 'Existe un elemento con el mismo código.');
                return $this->redirectToRoute('detalle_edit', array('id' => $nomDetalle->getidDetalle()));
            } else {
                $nombre = $em->getRepository('NomencladorBundle:NomDetalle')->codigoCant('NomencladorBundle:NomDetalle', $val->getNombre(), 'nombre');
                if (($nombre > 0 && $nom != $nomDetalle->getNombre())) {
                    $this->addFlash('danger', 'Existe un elemento con el mismo nombre.');
                    return $this->redirectToRoute('detalle_edit', array('id' => $nomDetalle->getidDetalle()));
                }
                $em->persist($nomDetalle);
                $em->flush();
            }

            return $this->redirectToRoute('detalle_index', array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:nomdetalle/edit.html.twig', array(
            'nomDetalle' => $nomDetalle,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a NomDetalle entity.
     *
     */
    public function deleteAction(Request $request, NomDetalle $nomDetalle)
    {
        $form = $this->createDeleteForm($nomDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomDetalle);
            $em->flush();
        }

        return $this->redirectToRoute('detalle_index');
    }

    /**
     * Creates a form to delete a NomDetalle entity.
     *
     * @param NomDetalle $nomDetalle The NomDetalle entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(NomDetalle $nomDetalle)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('detalle_delete', array('id' => $nomDetalle->getidDetalle())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function eliminarAction(Request $request)
    {
        $almacen = $request->request->get('id');

        $params['campoId'] = "iddetalle";
        $params['tabla'] = "NomDetalle";
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
            $result['mensaje'] = 'Se eliminaron solo los Tipos de Aseguramientos que no están en uso. Verifique en el Nomenclador Aseguramiento.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Tipo de Aseguramiento seleccionado ya que está en uso. Verifique en el Nomenclador Aseguramiento.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    public function deleteSelectAction(Request $request)
    {
        $almacen = $request->request->get('id');

        $params['campoId'] = "iddetalle";
        $params['tabla'] = "NomDetalle";
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
            $result['mensaje'] = 'Se eliminaron solo los Tipos de Aseguramientos que no están en uso. Verifique en el Nomenclador Aseguramiento.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Tipo de Aseguramiento seleccionado ya que está en uso. Verifique en el Nomenclador Aseguramiento.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    public
    function setActivoAction(Request $request)
    {
        $elementos = $request->request->get('id');
        $accion = $request->request->get('accion');
        $em = $this->getDoctrine()->getManager();
        for ($i = 0; $i < count($elementos); $i++) {
            $tipespecifico = $em->getRepository('NomencladorBundle:NomDetalle')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    public function exportarAction(Request $request)
    {
        ini_set('memory_limit','3072M');        set_time_limit(3600);
       
        $filtroCodigo = $request->query->get("codigo");
        $filtroNombre = $request->query->get("nombre");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomDetalle')->listarDelExportarTiposaseguramientos($filtroCodigo, $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Tipo aseguramiento')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador detalle sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador detalle')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel ->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel ->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2','Empresa: ' . count($datosEmp) > 0 ? $datosEmp[0]->getNombreEntidad() : "");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Tipo aseguramiento');
        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Activo');
        $celda->setTitle('Tipo aseguramiento');
        $celda->fromArray($data, '', 'A6');
        $header = 'a5:c'.(count($data)+5)  ;
        $cabecera = 'a5:c5';
        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
        $style = array(
            /*'font' => array('bold' => true,),*/
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,),
            'borders' => array( 'outline' => array( 'style' => \PHPExcel_Style_Border::BORDER_THICK, 'color' => array('argb' => 'FF000000'))),
        );
        $style2 = array(
            'font' => array('bold' => true,),
            'borders' => array( 'outline' => array( 'style' => \PHPExcel_Style_Border::BORDER_THICK, 'color' => array('argb' => 'FF000000'))),
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
        $params['nameArchivo'] = "tipos_aseguramiento";
        $objGenerarExcel = new GenerarExcel();$objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);}

}
