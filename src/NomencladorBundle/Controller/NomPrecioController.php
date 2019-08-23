<?php

namespace NomencladorBundle\Controller;

use Doctrine\Common\Util\Debug;
use NomencladorBundle\Util\Util;
use ReporteBundle\Util\GenerarExcel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use NomencladorBundle\Entity\NomPrecio;
use NomencladorBundle\Form\NomPrecioType;
use PHPExcel;
use PHPExcel_IOFactory;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);


/**
 * NomPrecio controller.
 *
 */
class NomPrecioController extends Controller
{
    /**
     * Lists all NomPrecio entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PRECIO')")
     */
    public function indexAction($remember)
    {
        return $this->render('NomencladorBundle:nomprecio:index.html.twig',array('remember'=>$remember));
    }


    /**
     * Creates a new NomPrecio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PRECIO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $nomPrecio = new NomPrecio();
        $form = $this->createForm('NomencladorBundle\Form\NomPrecioType', $nomPrecio);
        $em = $this->getDoctrine()->getManager();
        $datos = $request->request->get('nom_precio');
        $dataGrupos = json_decode($datos['grupo']);
        $dataProd = json_decode($datos['producto']);
        $enviado = $datos['enviado'];
        $datos['preciocuc'] = $datos['preciocuc'] != "" ? $datos['preciocuc'] : 0;
        $datos['preciomn'] = $datos['preciomn'] != "" ? $datos['preciomn'] : 0;
        $datos['impuesto'] = $datos['impuesto'] != "" ? $datos['impuesto'] : 0;
        $gru = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomgrupointeres');
        $pro = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomproducto');
        if ($enviado == 'enviado') {
            $dataGrupos = $gru->getGruposByIds($dataGrupos);
            $gruposTodos = $gru->getGruposHijosByGrupos($dataGrupos);
            $existen = false;
            $inserto = false;
            foreach ($dataProd as $valueProd) {
                $gruposInser = [];
                $prod = $em->getRepository('NomencladorBundle:Nomproducto')->find($valueProd);
                $padres = $pro->getProductosPadres($prod, array());
                $padres['id'] != null ? array_push($padres['id'], (int)$valueProd) : $padres['id'][0] = (int)$valueProd;

                foreach ($padres['id'] as $value) {
                    $res = $em->getRepository('NomencladorBundle:NomGrupointeres')->comprobarExisProdGrupoPrecios($value, $datos['preciomn'], $datos['preciocuc']);

                    $gruposComp = [];
                    if (count($res) > 1) {
                        foreach ($res as $valueRes) {
                            $gruposComp = array_merge($valueRes->getGrupo(), $gruposComp);
                        }
                    } else if (count($res) > 0) {
                        $gruposComp = $res[0]->getGrupo();
                    } else {
                        $gruposComp = $gruposTodos;
                    }

                    if (count(array_intersect($gruposTodos, $gruposComp)) != count($gruposTodos)) {
                        foreach ($gruposTodos as $grupo) {
                            $pos = array_search($grupo, $gruposComp);
                            if ($pos) {
                                $pos1 = array_search($grupo, $gruposInser);
                                if (in_array($grupo, $gruposInser)) {
                                    unset($gruposInser[$pos1]);
                                }
                                $existen = true;
                            } else {
                                $pos2 = array_search($grupo, $gruposInser);
                                if (in_array($grupo, $gruposInser)) {
                                    unset($gruposInser[$pos2]);
                                }
                            }
                        }
                    } else {
                        $gruposInser = $gruposTodos;
                    }
                }
                $todosGruposAsocProd = $em->getRepository('NomencladorBundle:NomPrecio')->getProdPrecios($padres['id']);

                $gruposComp1 = [];
                foreach ($todosGruposAsocProd as $valueRes) {
                    $gruposComp1 = array_merge($valueRes->getGrupo(), $gruposComp1);
                }
                foreach (array_unique($gruposComp1) as $grupo1) {
                    $pos3 = array_search($grupo1, $gruposInser);
                    if (in_array($grupo1, $gruposInser)) {
                        unset($gruposInser[$pos3]);
                    }
                }

                if (count($gruposInser) > 0) {
                    $nomPrecio = new NomPrecio();
                    $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($datos['um']);
                    $nomPrecio->setPreciocuc($datos['preciocuc']);
                    $nomPrecio->setPreciomn($datos['preciomn']);
                    $nomPrecio->setUm($um);
                    $nomPrecio->setProducto($prod);
                    $nomPrecio->setImpuesto($datos['impuesto']);
                    $nomPrecio->setIdpadre($valueProd);
                    $nomPrecio->setGrupo($gruposInser);
                    $em->persist($nomPrecio);
                    $inserto = true;
                } else {
                    $existen = true;
                }
            }

            $em->flush();
            if (!$existen) {
                $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
                if ($datos['action'] == "aceptar")
                    return $this->redirectToRoute('precio_index', array('remember' => 1));
                else
                    return $this->redirectToRoute('precio_new');
            } else {
                $this->addFlash('warning', 'Algunos de los elementos no se insertaron porque existen.');
                if ($inserto) {
                    if ($datos['action'] == "aceptar")
                        return $this->redirectToRoute('precio_index', array('remember' => 1));
                    else
                        return $this->redirectToRoute('precio_new');
                }
                return $this->redirectToRoute('precio_new');
            }
        }

        return $this->render('NomencladorBundle:nomprecio:new.html.twig', array(
            'nomPrecio' => $nomPrecio,
            'form' => $form->createView(),
            'accion' => 'Adicionar'
        ));
    }

    /**
     * Finds and displays a NomPrecio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PRECIO')")
     */
    public function showAction(NomPrecio $nomPrecio)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($nomPrecio);

        return $this->render('NomencladorBundle:nomprecio:show.html.twig', array(
            'nomPrecio' => $nomPrecio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomPrecio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PRECIO')")
     */
    public function editAction(Request $request, NomPrecio $nomPrecio)
    {
        $editForm = $this->createForm('NomencladorBundle\Form\NomPrecioType', $nomPrecio);
        $datos = $request->request->get('nom_precio');

        $em = $this->getDoctrine()->getManager();
        $enviado = $datos['enviado'];
        $dataGrupos = json_decode($datos['grupo']);
        $dataGruposDeselect = json_decode($datos['gruposdeselect']);
        $dataProd = json_decode($datos['producto']);
        $dataProdExis = json_decode($datos['productoexistente']);
        $resolucion = $datos['resolucion'] != "" ? $datos['resolucion'] : null;
        $fecha = $datos['fecha'] != "" ? is_string($datos['fecha']) ? date_create_from_format('d/m/Y', $datos['fecha']) : $datos['fecha'] : null;
        $gru = $this->get('nomenclador.nomgrupointeres');
        $pro = $this->get('nomenclador.nomproducto');
        if (count($dataGrupos) == 0) {
            $gruposTodos = $nomPrecio->getGrupo();
        } else {
            $dataGruposAux = $gru->getGruposByIds($dataGrupos);
            $gruposSel = $gru->getGruposHijosByGrupos($dataGruposAux);
            $gruposTodos = array_values(array_unique(array_merge($gruposSel, $nomPrecio->getGrupo())));
        }
        $gruposActuales = [];
        if ($enviado == "enviado") {
            array_push($dataProd, $dataProdExis);
            foreach ($dataProd as $valueProd) {
                $prod = $em->getRepository('NomencladorBundle:Nomproducto')->find($valueProd);
                $objPrecio = $em->getRepository('NomencladorBundle:Nomprecio')->findBy(['producto' => $valueProd]);

                if (count($objPrecio) == 0) {
                    /*Aki elimino los grupos que fueron deseleccionados*/
                    if (count($dataGruposDeselect) > 0) {
                        $dataGrupos1 = $gru->getGruposByIds($dataGruposDeselect);
                        $gruposTodosDesel = $gru->getGruposHijosByGrupos($dataGrupos1);
                        foreach ($gruposTodosDesel as $grupo) {
                            $pos = array_search($grupo, $gruposTodos);
                            if (in_array($grupo, $gruposTodos)) {
                                unset($gruposTodos[$pos]);
                            }
                        }
                        /*Aki reorganizo los indices del arreglo*/
                        $gruposTodos = array_values($gruposTodos);
                    }
                    $newPrecio = new NomPrecio();
                    $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($datos['um']);
                    $newPrecio->setPreciocuc($datos['preciocuc']);
                    $newPrecio->setPreciomn($datos['preciomn']);
                    $newPrecio->setUm($um);
                    $newPrecio->setProducto($prod);
                    $newPrecio->setImpuesto($datos['impuesto']);
                    $newPrecio->setIdpadre($valueProd);
                    $newPrecio->setGrupo($gruposTodos);
                    $newPrecio->setResolucion($resolucion);
                    $newPrecio->setFecha($fecha);
                    $em->persist($newPrecio);
                } elseif (count($objPrecio) > 0 && $nomPrecio->getId() != $objPrecio[0]->getId()) {
                    /*En esta condicional compruebo que si el elemento existe en BD lo modifico con los valores actuales*/
                    /*Aki elimino los grupos que fueron deseleccionados*/
                    if (count($dataGruposDeselect) > 0) {
                        $dataGrupos1 = $gru->getGruposByIds($dataGruposDeselect);
                        $gruposTodosDesel = $gru->getGruposHijosByGrupos($dataGrupos1);
                        foreach ($gruposTodosDesel as $grupo) {
                            $pos = array_search($grupo, $gruposTodos);
                            if (in_array($grupo, $gruposTodos)) {
                                unset($gruposTodos[$pos]);
                            }
                        }
                        /*Aki reorganizo los indices del arreglo*/
                        $gruposTodos = array_values($gruposTodos);
                    }
                    $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($datos['um']);
                    $objPrecio[0]->setPreciocuc($datos['preciocuc']);
                    $objPrecio[0]->setPreciomn($datos['preciomn']);
                    $objPrecio[0]->setUm($um);
                    $objPrecio[0]->setProducto($prod);
                    $objPrecio[0]->setImpuesto($datos['impuesto']);
                    $objPrecio[0]->setIdpadre($valueProd);
                    $objPrecio[0]->setGrupo($gruposTodos);
                    $objPrecio[0]->setResolucion($resolucion);
                    $objPrecio[0]->setFecha($fecha);
                    $em->persist($objPrecio[0]);
                } else {

                    if (count($dataGrupos) > 0) {
                        $dataGrupos2 = $gru->getGruposByIds($dataGrupos);
                        $gruposTodosSelecc = $gru->getGruposHijosByGrupos($dataGrupos2);
                        foreach ($gruposTodosSelecc as $grupo1) {
                            //$pos = array_search($grupo1, $gruposActuales);
                            if (!in_array($grupo1, $gruposActuales)) {
                                array_push($gruposActuales, (int)$grupo1);
                            }
                        }
                    }

                    $padres = $pro->getProductosPadres($prod, array());
                    if (count($padres) > 0) {
                        foreach ($padres['id'] as $valuePadr) {
                            $objPrecioPadre = $em->getRepository('NomencladorBundle:NomGrupointeres')->getProdPrecios([$valuePadr]);
                            if (count($objPrecioPadre) > 0) {
                                foreach ($objPrecioPadre[0]->getGrupo() as $grupo2) {
                                    $pos = array_search($grupo2, $gruposActuales);
                                    if (in_array($grupo2, $gruposActuales)) {
                                        unset($gruposActuales[$pos]);
                                    }
                                }
                            }
                        }
                    }
                    //dump($gruposActuales);
                    $todosGruposAsocProd = $em->getRepository('NomencladorBundle:NomGrupointeres')->getProdPrecios([$prod->getIdproducto()]);
                    //$gruposActuales = $nomPrecio->getGrupo();

                    if (count($todosGruposAsocProd) > 0) {
                        $gruposComp = [];
                        foreach ($todosGruposAsocProd as $valueRes) {
                            $gruposComp = array_merge($valueRes->getGrupo(), $gruposComp);
                        }

                        foreach (array_unique($gruposComp) as $grupo1) {
                            $pos = array_search($grupo1, $gruposActuales);
                            if (in_array($grupo1, $gruposActuales)) {
                                unset($gruposActuales[$pos]);
                            }
                        }
                        /*Aki reorganizo los indices del arreglo*/
                        $gruposActuales = array_values($gruposActuales);
                    }

                    /*Aki elimino los grupos que fueron deseleccionados*/
                    if (count($dataGruposDeselect) > 0) {
                        $dataGrupos1 = $gru->getGruposByIds($dataGruposDeselect);
                        $gruposTodosDesel = $gru->getGruposHijosByGrupos($dataGrupos1);
                        foreach ($gruposTodosDesel as $grupo) {
                            $pos = array_search($grupo, $gruposActuales);
                            if (in_array($grupo, $gruposActuales)) {
                                unset($gruposActuales[$pos]);
                            }
                        }
                        /*Aki reorganizo los indices del arreglo*/
                        $gruposActuales = array_values($gruposActuales);
                    }

                    $gruposActAux = array_merge($gruposActuales, $nomPrecio->getGrupo());
                    /*Aki reorganizo los indices del arreglo*/
                    $gruposActuales = array_values($gruposActAux);
                    $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($datos['um']);
                    $nomPrecio->setPreciocuc($datos['preciocuc']);
                    $nomPrecio->setPreciomn($datos['preciomn']);
                    $nomPrecio->setImpuesto($datos['impuesto']);
                    $nomPrecio->setProducto($prod);
                    $nomPrecio->setIdpadre($prod->getIdproducto());
                    $nomPrecio->setGrupo($gruposActuales);
                    $nomPrecio->setUm($um);
                    $nomPrecio->setResolucion($resolucion);
                    $nomPrecio->setFecha($fecha);
                    $em->persist($nomPrecio);
                }
            }

            $em->flush();
            return $this->redirectToRoute('precio_index', array('remember' => 1));


        }

        $params['producto'] = $nomPrecio->getProducto()->getIdproducto();
        $params['preciomn'] = $nomPrecio->getPreciomn();
        $params['preciocuc'] = $nomPrecio->getPreciocuc();
        $params['um'] = $nomPrecio->getUm();
        $res = $em->getRepository('NomencladorBundle:NomPrecio')->find($nomPrecio->getId());
        $gruposInteres = $res->getGrupo();
        $producto = $res->getProducto()->getIdproducto();

        return $this->render('NomencladorBundle:nomprecio:edit.html.twig', array(
            'nomPrecio' => $nomPrecio,
            'form' => $editForm->createView(),
            'pro' => $producto,
            'gru' => $gruposInteres,
            'preciomn' => $params['preciomn'],
            'preciocuc' => $params['preciocuc'],
            'accion' => 'Editar'
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PRECIO')")
     */
    public function deleteAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $ids = $request->request->get('id');
        $msg = 'exito';
        if (!is_array($ids))
            $ids = [$ids];
        foreach ($ids as $f) {
            $objPrecio = $em->getRepository('NomencladorBundle:NomPrecio')->find($f);
            $em->remove($objPrecio);
            $em->flush();
        }

        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRECIO')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        set_time_limit(3600000);
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $where = '1=1';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'um') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND (u.abreviatura LIKE \'%' . $alias . '%\' OR u.alias LIKE \'%' . $alias . '%\')';
                }
                if ($sp[0] == 'producto') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND p.alias LIKE \'%' . $alias . '%\'';
                }
                if ($sp[0] == 'preciomn') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND g.preciomn LIKE \'%' . $alias . '%\'';
                }
                if ($sp[0] == 'preciocuc') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND g.preciocuc LIKE \'%' . $alias . '%\'';
                }
                if ($sp[0] == 'impuesto') {
                    if ($sp[1] == 0) {
                        $where .= ' AND g.impuesto is null';
                    } else {
                        $alias = Util::getSlug($sp[1]);
                        $where .= ' AND g.impuesto LIKE \'%' . $alias . '%\'';
                    }
                }
            }
        }
        $dql = 'SELECT p,g,u
            FROM NomencladorBundle:NomPrecio g
            JOIN g.producto p
            JOIN g.um u
            WHERE ' . $where . ' AND p.activo = true
            GROUP BY p.idproducto,g.preciomn,g.preciocuc';
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nomprecio = $consulta->getResult();
        $gene = array();

        foreach ($nomprecio as $pr) {
            $res = array();
            $res[] = $pr->getId();
            $res[] = $pr->getProducto()->getNombre();
            $res[] = $pr->getUm()->getNombre();
            $res[] = $pr->getPreciomn();
            $res[] = $pr->getPreciocuc();
            $res[] = ($pr->getImpuesto() != null) ? $pr->getImpuesto() : 0;
            $res[] = $pr->getProducto()->getIdproducto();
            $gene[] = $res;
        }

        if (count($total) > 0) {
            return new JsonResponse(array('data' => $gene, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRECIO')")
     */
    public function exportarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600000);
        $filtroProducto = $request->query->get("producto");
        $filtroUm = $request->query->get("um");
        $filtroPreciomn = $request->query->get("preciomn");
        $filtroPreciocuc = $request->query->get("preciocuc");
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository('NomencladorBundle:NomPrecio')->listarDelExportarPrecio($filtroProducto, $filtroUm, $filtroPreciomn, $filtroPreciocuc);
        $datosEmp = $em->getRepository('AdminBundle:DatConfig')->obtenerDatosEmpresa();

        // solicitamos el servicio 'phpexcel' y creamos el objeto vacío...
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        // ...y le asignamos una serie de propiedades
        $phpExcelObject->getProperties()
            ->setCreator("Coppelia")
            ->setLastModifiedBy("Coppelia")
            ->setTitle("Precios")
            ->setSubject("Nomenclador Precios")
            ->setDescription("Listado del Nomenclador Precios del Sistema coppelia.")
            ->setKeywords("exportar excel precios");

        $celda = $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(100);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', "Habana");
        $celda->mergeCells('A4:B4');
        $celda->setCellValue('A4', 'Reporte: Nomenclador Precios');
        $style3 = array(
            'font' => array('bold' => true, '')
        );

        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
        $encabezado2 = 'A4:D4';
        $celda->getStyle($encabezado)->applyFromArray($style3);
        $celda->getStyle($encabezado1)->applyFromArray($style3);
        $celda->getStyle($encabezado2)->applyFromArray($style3);
        $pro = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomproducto');
        $contFilasPintadas = 6;
        $hojas = 0;
        $grupoActual = $data[0]['codigoGru'];
        foreach ($data as $index => $valuePrecio) {
            if ($index == 0) {
                $celda = self::encabExportarPrecio($phpExcelObject, $hojas, $datosEmp);
                $contFilasPintadas = self::encabTabla($phpExcelObject, $celda, $contFilasPintadas, $valuePrecio);
                $contFilasPintadas = self::datosProductos($phpExcelObject, $celda, $contFilasPintadas, $valuePrecio);
            } else if (($contFilasPintadas % 10000 == 0) && $index > 0) {
                $hojas++;
                $contFilasPintadas = 5;
                $phpExcelObject->createSheet();
                $celda = self::encabExportarPrecio($phpExcelObject, $hojas, $datosEmp);
                $contFilasPintadas = self::encabTabla($phpExcelObject, $celda, $contFilasPintadas, $valuePrecio);
                $contFilasPintadas = self::datosProductos($phpExcelObject, $celda, $contFilasPintadas, $valuePrecio);
            } else {
                $contFilasPintadas = self::datosProductos($phpExcelObject, $celda, $contFilasPintadas, $valuePrecio);
            }
        }

        // se crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'precios.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;

    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRECIO')")
     */
    private function datosProductos($phpExcelObject, $celda, $contFilasPintadas, $valuePrecio)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $styleLeft = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
        );

        /*Datos productos asociados al grupo de interes*/
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $contFilasPintadas, $valuePrecio['codigoGru']);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $contFilasPintadas, $valuePrecio['nombreGru']);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $contFilasPintadas, $valuePrecio['producto']);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, $contFilasPintadas, $valuePrecio['umProd']);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(4, $contFilasPintadas, $valuePrecio['preciomn']);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(5, $contFilasPintadas, $valuePrecio['preciocuc']);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(6, $contFilasPintadas, $valuePrecio['impuesto']);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(7, $contFilasPintadas, $valuePrecio['resolucion']);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(8, $contFilasPintadas, is_string($valuePrecio['fecha']) ? date_create_from_format('d/m/Y', $valuePrecio['fecha']) :
            !is_null($valuePrecio['fecha']) ? $valuePrecio['fecha']->format('d/m/Y') : "");
        $coordDatos = 'A' . $contFilasPintadas . ':I' . $contFilasPintadas;
        $coordDatos1 = 'A' . $contFilasPintadas . ':A' . $contFilasPintadas;
        $phpExcelObject->getActiveSheet()->getStyle($coordDatos)->getFont()->setName('Arial');
        $phpExcelObject->getActiveSheet()->getStyle($coordDatos)->getFont()->setSize(10);

        $celda->getStyle($coordDatos1)->applyFromArray($styleLeft);
        $contFilasPintadas++;
        return $contFilasPintadas;
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRECIO')")
     */
    private function encabTabla($phpExcelObject, $celda, $contFilasPintadas, $valuePrecio)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $styleCentrar = array(
            'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'font' => array('bold' => true, '')
        );
        /*Encabezado de la tabla*/
        $coordGrupos = 'A' . $contFilasPintadas . ':B' . $contFilasPintadas;
        $coord = 'B' . $contFilasPintadas . ':I' . $contFilasPintadas;
        $coord1 = 'A' . $contFilasPintadas . ':A' . $contFilasPintadas;
        $celda->mergeCells($coordGrupos);
        //$celda->getStyle($coordGrupos)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('54ae86');
        $phpExcelObject->getActiveSheet()->getStyle($coord)->getFont()->setName('Arial');
        $phpExcelObject->getActiveSheet()->getStyle($coord)->getFont()->setSize(10);
        $celda->getStyle($coord)->applyFromArray($styleCentrar);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('H')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $contFilasPintadas, 'Grupo Interés');
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $contFilasPintadas, 'Producto');
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, $contFilasPintadas, 'UM');
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(4, $contFilasPintadas, 'Precio CUP');
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(5, $contFilasPintadas, 'Precio CUC');
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(6, $contFilasPintadas, 'Impuesto (%)');
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(7, $contFilasPintadas, 'Resolución');
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(8, $contFilasPintadas, 'Fecha');
        $contFilasPintadas++;
        $coord1 = 'A' . $contFilasPintadas . ':I' . $contFilasPintadas;
        $celda->getStyle($coord1)->applyFromArray($styleCentrar);
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $contFilasPintadas, 'Código');
        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $contFilasPintadas, 'Nombre');
        $contFilasPintadas++;
        return $contFilasPintadas;
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PRECIO')")
     */
    private function encabExportarPrecio($phpExcelObject, $hojas, $datosEmp)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $celda = $phpExcelObject->setActiveSheetIndex($hojas);
        $hojas++;
        $phpExcelObject->getActiveSheet()->setTitle('Precio ' . $hojas);
        $phpExcelObject->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $phpExcelObject->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $celda->mergeCells('A2:B2');
        $celda->setCellValue('A2', count($datosEmp) > 0 ? 'Empresa: ' . $datosEmp[0]->getNombreEntidad() : "Empresa: ");
        $celda->mergeCells('A3:B3');
        $celda->setCellValue('A3', "Habana");
        $celda->mergeCells('A4:B4');
        $celda->setCellValue('A4', 'Reporte: Nomenclador Precios');
        $style3 = array(
            'font' => array('bold' => true, '')
        );

        $encabezado = 'A2:D2';
        $encabezado1 = 'A3:D3';
        $encabezado2 = 'A4:D4';
        $celda->getStyle($encabezado)->applyFromArray($style3);
        $celda->getStyle($encabezado1)->applyFromArray($style3);
        $celda->getStyle($encabezado2)->applyFromArray($style3);
        return $celda;
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PRECIO')")
     */
    public function mostrarGruposAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $idproducto = $request->query->get("precio");
        $em = $this->getDoctrine()->getManager();
        $nomprecio = $em->getRepository('NomencladorBundle:NomPrecio')->findByProducto($idproducto);

        $preciosByGrupos = array();
        foreach ($nomprecio[0]->getGrupo() as $pr) {
            $grupo = $em->getRepository('NomencladorBundle:NomGrupointeres')->find($pr);
            $grupos = $em->getRepository('NomencladorBundle:NomGrupointeres')->getGruposPadresNombre($grupo);
            $res = array();
            $res[] = $nomprecio[0]->getProducto()->getNombre();
            $res[] = $grupo->getCodigo();
            $res[] = $grupos . $grupo->getNombre();
            $res[] = $nomprecio[0]->getId();
            $res[] = $grupo->getIdgrupointeres();
            $preciosByGrupos[] = $res;
        }
        return $this->render('NomencladorBundle:nomprecio:mostrar_grupos.html.twig', array('gruposByPrecios' => $preciosByGrupos));
    }

    /**
     *
     * @Security("is_granted('ROLE_ELIMINAR_PRECIO')")
     */
    public function desociarProdGrupoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $idprecio = $request->request->get("idprecio");
        $idgrupo = $request->request->get("idgrupo");
        $objPrecio = $em->getRepository('NomencladorBundle:NomPrecio')->find($idprecio);
        $grupos = $objPrecio->getGrupo();
        $pos = array_search($idgrupo, $grupos);
        if ($pos) {
            unset($grupos[$pos]);
            $grupos = array_values($grupos);
        }
        $objPrecio->setGrupo($grupos);
        $em->persist($objPrecio);
        $em->flush();

        return new JsonResponse(array('result' => true));
    }

}
