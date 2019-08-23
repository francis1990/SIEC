<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

use NomencladorBundle\Entity\NomCuentacontable;
use NomencladorBundle\Form\NomCuentacontableType;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomCuentacontable controller.
 *
 */
class NomCuentacontableController extends Controller
{
    /**
     * Lists all NomCuentacontable entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_CUENTACONTABLE')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomcuentacontable:index.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CUENTACONTABLE')")
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
                        $where .= ' AND g.activo = ' . $sp[1];
                    }
                }
            }
        }
        $dql = 'SELECT g
            FROM NomencladorBundle:NomCuentacontable g
            WHERE ' . $where . 'ORDER BY g.numero';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);

        $nom = $consulta->getResult();

        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIdcuentacontable();
            $res[] = $pr->getNumero();
            $res[] = $pr->getNombre();
            $res[] = $pr->getPorcobrar();
            $res[] = $pr->getFinanzas();
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
     * Creates a new NomCuentacontable entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_CUENTACONTABLE')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomCuentacontable = new NomCuentacontable();
        $form = $this->createForm('NomencladorBundle\Form\NomCuentacontableType', $nomCuentacontable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomCuentacontable->setActivo(1);
            $em->persist($nomCuentacontable);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('cuentacontable_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('cuentacontable_new');
        }

        return $this->render('NomencladorBundle:nomcuentacontable:new.html.twig', array(
            'nomCuentacontable' => $nomCuentacontable,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * Finds and displays a NomCuentacontable entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_CUENTACONTABLE')")
     */
    public function showAction(NomCuentacontable $nomCuentacontable)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $deleteForm = $this->createDeleteForm($nomCuentacontable);

        return $this->render('NomencladorBundle:nomcuentacontable:show.html.twig', array(
            'nomCuentacontable' => $nomCuentacontable,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomCuentacontable entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_CUENTACONTABLE')")
     */
    public function editAction(Request $request, NomCuentacontable $nomCuentacontable)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm('NomencladorBundle\Form\NomCuentacontableType', $nomCuentacontable);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($nomCuentacontable);
            $em->flush();
            return $this->redirectToRoute('cuentacontable_index',array('remember'=> 1));
        }

        return $this->render('NomencladorBundle:nomcuentacontable:new.html.twig', array(
            'nomCuentacontable' => $nomCuentacontable,
            'form' => $editForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * Deletes a NomCuentacontable entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_CUENTACONTABLE')")
     */
    public function deleteAction(Request $request, NomCuentacontable $nomCuentacontable)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $form = $this->createDeleteForm($nomCuentacontable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomCuentacontable);
            $em->flush();
        }

        return $this->redirectToRoute('cuentacontable_index');
    }

    /**
     * Creates a form to delete a NomCuentacontable entity.
     *
     * @param NomCuentacontable $nomCuentacontable The NomCuentacontable entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_CUENTACONTABLE')")
     */
    private function createDeleteForm(NomCuentacontable $nomCuentacontable)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cuentacontable_delete', array('id' => $nomCuentacontable->getIdcuentacontable())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_CUENTACONTABLE')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $cuenta = $request->request->get('id');

        $params['campoId'] = "idcuentacontable";
        $params['tabla'] = "NomCuentacontable";
        $params['valor'] = $cuenta;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo las Cuentas Contables que no están en uso. Verifique en el Parte Diaro de Cuentas por cobrar.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar la Cuenta Contable seleccionada ya que está en uso. Verifique en el Parte Diaro de Cuentas por cobrar.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CUENTACONTABLE')")
     */
    public function setActivoAction(Request $request)
    {

        $elementos = $request->request->get('id');
        $accion = $request->request->get('accion');
        $em = $this->getDoctrine()->getManager();
        for ($i = 0; $i < count($elementos); $i++) {
            $tipespecifico = $em->getRepository('NomencladorBundle:NomCuentacontable')->find($elementos[$i]);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipespecifico->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_CUENTACONTABLE')")
     */
    public function exportarAction(Request $request)
    {
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);

        $filtroNumero = $request->query->get("numero");
        $filtroNombre = $request->query->get("nombre");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomCuentacontable')->listarDelExportarCuentasContables($filtroNumero, $filtroNombre);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Cuenta contable')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador cuenta sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador cuenta')
            ->setCategory('exportar');
        // Agregar Informacion

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador Cuentas Contable');
        $celda->setCellValue('A5', 'Número')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Por cobrar')
            ->setCellValue('D5', 'Activo');
        $celda->setTitle('Cuenta contable');
        $celda->fromArray($data, '', 'A6');
        $header = 'a5:d' . (count($data) + 5);
        $cabecera = 'a5:d5';
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
        $params['nameArchivo'] = "cuentas_contables";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}
