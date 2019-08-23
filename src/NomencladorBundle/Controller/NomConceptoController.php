<?php

namespace NomencladorBundle\Controller;

use EnumsBundle\Entity\EnumAreas;
use NomencladorBundle\Entity\NomConcepto;
use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomConcepto controller.
 *
 */
class NomConceptoController extends Controller
{
    /**
     * Lists all NomConcepto entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_CONCEPTO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        return $this->render('NomencladorBundle:nomconcepto:index.html.twig',array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CONCEPTO')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
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
            FROM NomencladorBundle:NomConcepto g
            WHERE ' . $where . ' ORDER BY g.codigo ';
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $for = array();

        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIdconcepto();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            // $res[] = !$pr->getTipo() ? true : false;
            $res[] = !$pr->getTipo() ? 'Entrada' : 'Salida';
            $res[] = $pr->getActivo();
            $res[] = $pr->getConceptodefault() != 0 ? true : false;
            $for[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $for, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }

    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_CONCEPTO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomConcepto = new NomConcepto();
        $form = $this->createForm('NomencladorBundle\Form\NomConceptoType', $nomConcepto);
        $form->handleRequest($request);
        $datos = $request->request->get('nom_concepto');
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomConcepto->setActivo(true);
            $nomConcepto->setConceptodefault(0);
            $em->persist($nomConcepto);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('concepto_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('concepto_new');
        }

        return $this->render('NomencladorBundle:nomconcepto:new.html.twig', array(
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_CONCEPTO')")
     */
    public function editarAction(Request $request, NomConcepto $nomConcepto)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomConcepto);
        $editForm = $this->createForm('NomencladorBundle\Form\NomConceptoType', $nomConcepto);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomConcepto->setConceptodefault(0);
            $em->persist($nomConcepto);
            $em->flush();
            return $this->redirectToRoute('concepto_index',array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:nomconcepto:new.html.twig', array(
            'form' => $editForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_CONCEPTO')")
     */
    private function createDeleteForm(NomConcepto $nomConcep)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('concepto_delete', array('id' => $nomConcep->getIdconcepto())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_CONCEPTO')")
     */
    public function deleteAction(Request $request, NomConcepto $nomConcep)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomConcep);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomConcep);
            $em->flush();
        }
        return $this->redirectToRoute('concepto_index');
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_CONCEPTO')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $concepto = $request->request->get('id');

        $params['campoId'] = "idconcepto";
        $params['tabla'] = "NomConcepto";
        $params['valor'] = $concepto;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Conceptos que no están en uso. Verifique en el Parte Diaro de Movimiento de Almacén.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Concepto seleccionado ya que está en uso. Verifique en el Parte Diaro de Movimiento de Almacén.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CONCEPTO')")
     */
    public function setActivoAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $elementos = $request->request->get('id');
        $accion = $request->request->get('accion');
        $em = $this->getDoctrine()->getManager();
        for ($i = 0; $i < count($elementos); $i++) {
            $tipo = $em->getRepository('NomencladorBundle:NomConcepto')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipo->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CONCEPTO')")
     */
    public function exportarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $filtroCodigo = $request->query->get("codigo");
        $filtroNombre = $request->query->get("nombre");

        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomConcepto')->listarDelExportarConcepto($filtroCodigo, $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Tipo entidad')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador concepto sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador conceptos')
            ->setCategory('exportar');
        // Agregar Informacion
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Conceptos');

        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Entrada')
            ->setCellValue('D5', 'Salida')
            ->setCellValue('E5', 'Activo');
        $celda->setTitle('Conceptos');
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
        $params['nameArchivo'] = "concepto";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}
