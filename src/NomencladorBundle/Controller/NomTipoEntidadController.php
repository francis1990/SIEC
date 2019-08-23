<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 10/10/2017
 * Time: 1:50 PM
 */

namespace NomencladorBundle\Controller;

use NomencladorBundle\Entity\NomTipoEntidad;
use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

class NomTipoEntidadController extends Controller
{
    /**
     * @Security("is_granted('ROLE_LISTAR_TIPOENTIDAD')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('NomencladorBundle:nomtipoentidad:index.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_TIPOENTIDAD')")
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
                if ($sp[0] == "codigo") {
                    $where .= 'AND g.codigo LIKE \'%' . $sp[1] . '%\'';
                } elseif ($sp[0] == "nombre") {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND g.alias LIKE \'%' . $alias . '%\'';
                } elseif ($sp[0] == "abreviatura") {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND g.abreviatura LIKE \'%' . $sp[1] . '%\'';
                } elseif ($sp[0] == "activo") {
                    if ($sp[1] < 2) {
                        $where .= 'AND g.activo = ' . $sp[1];
                    }
                }
            }
        }
        $dql = 'SELECT g
            FROM NomencladorBundle:NomTipoEntidad g
            WHERE ' . $where . ' ORDER BY g.codigo ';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $for = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getIdtipoentidad();
            $res[] = $pr->getCodigo();
            $res[] = $pr->getNombre();
            $res[] = $pr->getAbreviatura();
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
     * @Security("is_granted('ROLE_ADICIONAR_TIPOENTIDAD')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomtipo = new NomTipoEntidad();
        $form = $this->createForm('NomencladorBundle\Form\NomTipoEntidadType', $nomtipo);
        $form->handleRequest($request);
        $tipoent = $request->request->get('nom_tipo_entidad');
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomtipo->setActivo(true);
            $em->persist($nomtipo);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('tipoentidad_index',array('remember'=> 1));
            else
                return $this->redirectToRoute('tipoentidad_new');
        }
        return $this->render('NomencladorBundle:nomtipoentidad:new.html.twig', array(
            'nomTipoEntidad' => $nomtipo,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_TIPOENTIDAD')")
     */
    public function editAction(Request $request, NomTipoEntidad $nomTipoEnt)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomTipoEnt);
        $editForm = $this->createForm('NomencladorBundle\Form\NomTipoEntidadType', $nomTipoEnt);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomTipoEnt);
            $em->flush();
            return $this->redirectToRoute('tipoentidad_index',array('remember'=> 1));
        }
        return $this->render('NomencladorBundle:nomtipoentidad:new.html.twig', array(
            'form' => $editForm->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_TIPOENTIDAD')")
     */
    private function createDeleteForm(NomTipoEntidad $nomTipoEnt)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipoentidad_delete', array('id' => $nomTipoEnt->getIdtipoentidad())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_TIPOENTIDAD')")
     */
    public function deleteAction(Request $request, NomTipoEntidad $nomTipoEntidad)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomTipoEntidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomTipoEntidad);
            $em->flush();
        }
        return $this->redirectToRoute('tipoentidad_index');
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_TIPOENTIDAD')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $tipoentidad = $request->request->get('id');
        $params['campoId'] = "idtipoentidad";
        $params['tabla'] = "NomTipoEntidad";
        $params['valor'] = $tipoentidad;
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elementos seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Tipos de Entidades que no están en uso. Verifique en el Nomenclador de Entidades.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Tipo de Entidad seleccionado ya que está en uso. Verifique en el Nomenclador de Entidades.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_TIPOENTIDAD')")
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
        foreach ($elementos as $value) {
            $tipo = $em->getRepository('NomencladorBundle:NomTipoEntidad')->find($value);
            $accion == 'btn_activar' ? $estado = true : $estado = false;
            $tipo->setActivo($estado);
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => 'exito'));

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_TIPOENTIDAD')")
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
        $filtroAbrev = $request->query->get("abreviatura");

        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomTipoEntidad')->listarDelExportarTipoEntidad($filtroCodigo, $filtroNombre, $filtroAbrev);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Tipo entidad')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador tipo entidad sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador tipo entidad')
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
        $celda->setCellValue('A3', 'Reporte: Nomenclador Tipo de entidades');

        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Abreviatura')
            ->setCellValue('D5', 'Activo');
        $celda->setTitle('Tipo Entidad');
        $celda->fromArray($data, '', 'A6');
        $objPHPExcel->setActiveSheetIndex(0);
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
        $params['nameArchivo'] = "tipo_entidad";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

}