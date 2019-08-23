<?php

namespace NomencladorBundle\Controller;

use NomencladorBundle\Util\Util;
use PHPExcel;
use PHPExcel_IOFactory;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use NomencladorBundle\Entity\NomProducto;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Common\Util\Debug;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * NomProducto controller.
 *
 */
class NomProductoController extends Controller
{
    /**
     * Lists all NomProducto entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        return $this->render('NomencladorBundle:nomproducto:index.html.twig', array('remember' => $remember));
    }

    /**
     * Creates a new NomProducto entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PRODUCTO')")
     */
    public function newAction(Request $request, NomProducto $nomPadre)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomProducto = new NomProducto();
        $form = $this->createForm('NomencladorBundle\Form\NomProductoType', $nomProducto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($nomPadre->getIdproducto() != 0) {
                ($nomPadre->getNivel() != null) ? $nomProducto->setNivel($nomPadre->getNivel() + 1) : $nomProducto->setNivel(1);
                $nomPadre->setHoja(0);
                $em->persist($nomPadre);
            } else {
                $nomProducto->setNivel(0);
            }
            $nomProducto->generarCodigo();
            $nomProducto->generarNombre();
            $nomProducto->setActivo(true);
            $nomProducto->setHoja(true);
            $nomProducto->setIdpadre($nomPadre->getIdproducto());
            $em->persist($nomProducto);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                $params['tabla'] = 'NomProducto';
                $params['campoId'] = 'idproducto';
                $padres = $this->get('nomencladores')->getObjPadres($nomProducto, [], $params);
                $padres = array_reverse($padres);
                array_push($padres, $nomProducto->getIdproducto());
                $this->get('session')->set('padres', $padres);
                return $this->redirectToRoute('producto_index', array('remember' => 1));
            } else
                return $this->redirectToRoute('producto_new', array('id' => $nomPadre->getIdproducto() != null ? $nomPadre->getIdproducto() : null));
        }
        return $this->render('NomencladorBundle:nomproducto:new.html.twig', array(
            'nomProducto' => $nomProducto,
            'form' => $form->createView(),
            'nomPadre' => $nomPadre,
            "accion" => "Adicionar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_PRODUCTO')")
     */
    public function newIdAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomProducto = new NomProducto();
        $form = $this->createForm('NomencladorBundle\Form\NomProductoType', $nomProducto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $nomProducto->setActivo(true);
            $nomProducto->setHoja(true);
            $nomProducto->setNivel(0);
            $nomProducto->setIdpadre(0);
            $em->persist($nomProducto);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                $params['tabla'] = 'NomProducto';
                $params['campoId'] = 'idproducto';
                $padres = $this->get('nomencladores')->getObjPadres($nomProducto, [], $params);
                $padres = array_reverse($padres);
                array_push($padres, $nomProducto->getIdproducto());
                $this->get('session')->set('padres', $padres);
                return $this->redirectToRoute('producto_index', array('remember' => 1));
            } else
                return $this->redirectToRoute('producto_newid');
        }
        return $this->render('NomencladorBundle:nomproducto:new.html.twig', array(
            'nomProducto' => $nomProducto,
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * Finds and displays a NomProducto entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function showAction(NomProducto $nomProducto)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomProducto);
        return $this->render('NomencladorBundle:nomproducto:show.html.twig', array(
            'nomProducto' => $nomProducto,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomProducto entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PRODUCTO')")
     */
    public function editAction(Request $request, NomProducto $nomProducto)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($nomProducto);
        $editForm = $this->createForm('NomencladorBundle\Form\NomProductoType', $nomProducto);
        $param = array(
            'nomProducto' => $nomProducto,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            "accion" => "Editar"
        );
        if ($nomProducto->getIdpadre() != 0) {
            $npadre = $em->getRepository('NomencladorBundle:NomProducto')->find($nomProducto->getIdpadre());
            $param['nomPadre'] = $npadre;
        }
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($nomProducto);
            $em->flush();
            $params['tabla'] = 'NomProducto';
            $params['campoId'] = 'idproducto';
            $padres = $this->get('nomencladores')->getObjPadres($nomProducto, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $nomProducto->getIdproducto());
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('producto_index', array('remember' => 1));
        }
        return $this->render('NomencladorBundle:nomproducto:new.html.twig', $param);
    }

    /**
     * Deletes a NomProducto entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PRODUCTO')")
     */
    public function deleteAction(Request $request, NomProducto $nomProducto)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($nomProducto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomProducto);
            $em->flush();
        }

        return $this->redirectToRoute('producto_index');
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PRODUCTO')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $grupo = $request->request->get('id');

        $params['campoId'] = "idproducto";
        $params['tabla'] = "NomProducto";
        $params['valor'] = $grupo;
        $params['arbol'] = true;
        $params['servicio'] = 'nomenclador.nomproducto';
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }

        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los Productos que no están en uso. Verifique en el Nomenclador Normas de Consumo, Precios, Rutas así
                                  como en los Planes y Partes Diarios.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el Producto seleccionado ya que está en uso. Verifique en el Nomenclador Normas de Consumo, Precios, Rutas así
                                  como en los Planes y Partes Diarios.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $filters = array();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $where = '1=1 ';
        $filters_raw = explode('|', $request->query->get('filters'));
        if (!in_array("activo:0", $filters_raw) && !in_array("activo:1", $filters_raw) && !in_array("activo:2", $filters_raw)) {
            $where .= " AND g.activo = 1";
        }
        if ($request->query->get('filters')) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'codigo') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND g.codigo LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'nombre') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= 'AND g.alias LIKE \'%' . $alias . '%\'';
                } elseif ($sp[0] == "activo") {
                    if ($sp[1] < 2) {
                        $where .= 'AND g.activo = ' . $sp[1];
                    }
                }
            }
        }
        if ($dat != '-1' && $dat != 13 && $parent == 0) {
            /*Por el momento para ver que solucion se busca cuando se marca en el filtro nivel la opción todos*/
            if ($dat == 4) {
                $where .= $request->query->get('filters') ? "" : " AND  g.nivel = 0";
            } else {
                $where .= $request->query->get('filters') ? "" : " AND  g.nivel = " . $dat . "";
            }

        }
        if ($parent != 0)
            $where .= ' AND g.idpadre=' . $parent;
        $dql = 'SELECT g
            FROM NomencladorBundle:NomProducto g
            WHERE ' . $where . ' order by g.codigo asc';

        $consulta = $em->createQuery($dql);
        $tol = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nomProductos = $consulta->getResult();
        $prod = array();

        foreach ($nomProductos as $pr) {
            $res = array();
            $res[] = $pr->getIdproducto();
            $res[] = $pr->generarCodigo();
            $res[] = $pr->generarNombre();
            $res[] = $pr->getActivo();
            $res[] = $request->query->get('filters') ? 1 : $pr->getHoja();
            $prod[] = $res;
        }

        if ($tol > 0) {
            return new JsonResponse(array('data' => $prod, 'total' => $tol));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * Creates a form to delete a NomProducto entity.
     *
     * @param NomProducto $nomProducto The NomProducto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PRODUCTO')")
     */
    private function createDeleteForm(NomProducto $nomProducto)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('producto_delete', array('id' => $nomProducto->getIdproducto())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
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

        if (count($elementos) > 0) {
            $elementosHijos = array();
            $accion == 'btn_activar' ? $estado = true : $estado = false;

            foreach ($elementos as $value) {
                $hijos = array();
                $producto = $em->getRepository('NomencladorBundle:NomProducto')->find($value);
                $elementosHijos = $this->get('nomenclador.nomproducto')->getProductosHijos($producto, $hijos, true, true);
                foreach ($elementosHijos as $index => $valueHijos) {
                    $valueHijos->setActivo($estado);
                }
            }
            $em->flush();
            return new JsonResponse(array('respuesta' => 'exito'));
        } else {
            $accion == 'btn_activar' ? $estado = 1 : $estado = 0;
            $em->getRepository('NomencladorBundle:NomProducto')->update('NomencladorBundle:NomProducto', 'activo', $estado);
            $em->flush();
            return new JsonResponse(array('respuesta' => 'exito'));
        }

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function saborByIdSubAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $sub = $em->getRepository('NomencladorBundle:NomSubgenerico')->find($id);
        $sabor = $sub->getIdsabor();
        $lista = [];
        foreach ($sabor as $e) {
            $lista[] = [
                'id' => $e->getIdsabor(),
                'nombre' => $e->getNombre(),
                'codigo' => $e->getCodigo()
            ];
        }
        return new JsonResponse($lista);
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function formatoByIdSubAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $id = $request->request->get('id');
        $lista = $this->elemByIdSubAction($id);
        return new JsonResponse($lista);
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function elemByIdSubAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $sub = $em->getRepository('NomencladorBundle:NomSubgenerico')->find($id);
        $format = $sub->getIdformato();
        $sabor = $sub->getIdsabor();
        $espec = $sub->getIdespecifico();
        $lista = [];
        $lista['formato'] = $this->elemToArrayAction($format, 'getId');
        $lista['sabor'] = $this->elemToArrayAction($sabor, 'getIdsabor');
        $lista['especifico'] = $this->elemToArrayAction($espec, 'getIdespecifico');
        return new JsonResponse($lista);
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    private function elemToArrayAction($elems, $tipo)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $lista = [];
        foreach ($elems as $e) {
            $lista[] = [
                'id' => $e->$tipo(),
                'nombre' => $e->getNombre(),
                'codigo' => $e->getCodigo()
            ];
        }
        return $lista;
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function especificosByIdSubAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $sub = $em->getRepository('NomencladorBundle:NomSubgenerico')->find($id);
        $format = $sub->getIdespecifico();
        $lista = [];
        foreach ($format as $e) {
            $lista[] = [
                'id' => $e->getIdespecifico(),
                'nombre' => $e->getNombre(),
                'codigo' => $e->getCodigo()
            ];
        }
        return new JsonResponse($lista);
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PRODUCTO')")
     */
    public function deleteSelectAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $elemento = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        try {
            $em->getRepository('NomencladorBundle:NomProducto')->deleteAll('NomencladorBundle:NomProducto', 'idproducto', $elemento);
            return new JsonResponse(array('respuesta' => 'exito'));
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse(array('respuesta' => 'error'));
        }

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function listadoAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();
        $nompro = $em->getRepository('NomencladorBundle:NomProducto')->findByActivo('true');
        return $this->render('NomencladorBundle:nomproducto:listaproducto.html.twig', array('productos' => $nompro));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
     */
    public function listadoAcopioAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $nompro = $em->getRepository('NomencladorBundle:NomProducto')->findByActivo('true');
        return $this->render('NomencladorBundle:nomproducto:listaproducto.html.twig', array('productos' => $nompro));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRODUCTO')")
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
        $filtroNivel = $request->query->get("nivel");

        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomProducto')->listarDelExportarProducto($filtroCodigo, $filtroNombre, $filtroNivel);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // Crea un nuevo objeto PHPExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator('Coppelia')
            ->setTitle('Productos')
            ->setLastModifiedBy('Coppelia')
            ->setDescription('Nomenclador producto sistema coppelia')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setKeywords('exportar nomenclador producto')
            ->setCategory('exportar');
        // Agregar Informacion

        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(70);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $celda = $objPHPExcel->setActiveSheetIndex(0);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', 'Reporte: Nomenclador de Productos');
        $celda->setCellValue('A5', 'Código')
            ->setCellValue('B5', 'Nombre')
            ->setCellValue('C5', 'Genérico')
            ->setCellValue('D5', 'Subgenérico')
            ->setCellValue('E5', 'Específico')
            ->setCellValue('F5', 'Tipo específico')
            ->setCellValue('G5', 'Sabor')
            ->setCellValue('H5', 'Formato')
            ->setCellValue('I5', 'Onei')
            ->setCellValue('J5', 'UM')
            ->setCellValue('K5', 'Factor');
        $celda->setTitle('Genéricos');
        $celda->fromArray($data['data'], '', 'A6');
        $header = 'a5:k' . (count($data['data']) + 5);
        $cabecera = 'a5:k5';
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

        if (isset($data['dataPadres'])) {
            foreach ($data['dataPadres'] as $value) {
                $posicion = $value + 6;
                $pintarPadreSuperior = 'A' . $posicion . ':K' . $posicion;
                //$celda->getStyle($pintarPadreSuperior)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('52d689');
            }
        }

        $params['nameArchivo'] = "productos";
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->salidaExportarExcel($objPHPExcel, $params);
    }

    public function getTreeAction(Request $request)
    {

        $node = ($request->get('id') === '#' || $request->get('id') == null) ? 0 : $request->get('id');
        $data = $this->get('nomenclador.nomproducto')->getChildren($node);

        return new JsonResponse($data);
    }


}
