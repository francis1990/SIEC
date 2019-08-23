<?php

namespace ParteDiarioBundle\Controller;

use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use NomencladorBundle\Util\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatPartediarioProduccion;
use ParteDiarioBundle\Entity\DatConsumoProduccion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);


/**
 * DatPartediarioProduccion controller.
 *
 */
class DatPartediarioProduccionController extends Controller
{
    /**
     * Lists all DatPartediarioProduccion entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTENIVELACTV')")
     */
    public function indexAction($fecha, $ueb, $remember)
    {
        $fecha = $fecha == 0 ? 0 : str_replace('-', '/', $fecha);
        return $this->render('ParteDiarioBundle:datpartediarioproduccion:index.html.twig', array(
            'fecha' => $fecha,
            'ueb' => $ueb,
            'remember' => $remember
        ));
    }

    private function newAuxiliar($params, $ent, $em, $request)
    {
        if ($params['idueb'] != 0) {
            $ueb = $em->getRepository('NomencladorBundle:NomUeb')->find($params['idueb']);
            $ent->setUeb($ueb);
        }
        if ($params['fecha'] != 0) {
            if (date_create_from_format('d-m-Y', $params['fecha'])) {
                $fechaAux1 = date_create_from_format('d-m-Y', $params['fecha']);
                $fechaAux2 = $fechaAux1->format('d/m/Y');
                $fechaAux = date_create_from_format('d/m/Y', $fechaAux2);
            } else {
                $fechaAux = date_create_from_format('d/m/Y', $params['fecha']);
            }
            $ent->setFecha($fechaAux);
        }

        $form = $this->createForm('ParteDiarioBundle\Form\DatPartediarioProduccionType', $ent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ent->setUm($ent->getProducto()->getUmOperativa());
            $em->persist($ent);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                return $this->redirectToRoute('parteproduccion_index', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                    'remember' => 1
                ));
            } else {
                return $this->redirectToRoute('parteproduccion_guardar', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
                ));
            }
        }
        return $this->render('ParteDiarioBundle:datpartediarioproduccion:new.html.twig', array(
            'datPartediarioProduccion' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * Creates a new DatPartediarioProduccion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTENIVELACTV')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datPartediarioProduccion = new DatPartediarioProduccion();
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        return $this->newAuxiliar($params, $datPartediarioProduccion, $em, $request);
    }

    /**
     * Creates a new DatPartediarioProduccion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTENIVELACTV')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datPartediarioProduccion = new DatPartediarioProduccion();
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        return $this->newAuxiliar($params, $datPartediarioProduccion, $em, $request);
    }


    /**
     * Displays a form to edit an existing DatPartediarioProduccion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTENIVELACTV')")
     */
    public function editAction(Request $request, DatPartediarioProduccion $datPartediarioProduccion, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedIncidencias = clone $datPartediarioProduccion->getIncidencias();
        $deleteForm = $this->createDeleteForm($datPartediarioProduccion);
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatPartediarioProduccionType', $datPartediarioProduccion,
            array(
                'action' => $this->generateUrl('parteproduccion_edit',
                    array('id' => $datPartediarioProduccion->getIdparte(), 'fecha' => $fecha, 'ueb' => $ueb))
            ));
        $em = $this->getDoctrine()->getManager();
        $datos = $request->request->get('dat_partediario_produccion');

        if ($datos['updateCons']) {
            $prod = $em->getRepository('NomencladorBundle:NomProducto')->find($datos['producto']);
            $padres = array();
            $productosPadres = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomproducto')->getProductosPadres($prod,
                $padres);
            $productosUpdate = implode(',', $productosPadres['id']);
            $params['productos'] = $productosUpdate;
            $params['diferenciaNivActv'] = $datos['diferenciaNivActv'];
            $params['ueb'] = $datos['ueb'];
            $params['fecha'] = $datos['fecha'];
            $GLOBALS['kernel']->getContainer()->get('parte_diario.dat_consumo_aseguramiento')->updateDatosConsumoAseg($params);
        }

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $em->beginTransaction();
                try {
                    // Se eliminan las incidencias que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedIncidencias as $savedIncidencia) {
                        if ($datPartediarioProduccion->getIncidencias()->contains($savedIncidencia) === false) {
                            $em->remove($savedIncidencia);
                        }
                    }
                    $em->persist($datPartediarioProduccion);
                    $em->flush();
                    $em->commit();

                    return $this->redirectToRoute('parteproduccion_index', array(
                        'fecha' => $fecha,
                        'ueb' => $ueb,
                        'remember' => 1));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }
        return $this->render('ParteDiarioBundle:datpartediarioproduccion:new.html.twig', array(
            'datPartediarioProduccion' => $datPartediarioProduccion,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'action' => 'Editar',
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }

    /**
     * Deletes a DatPartediarioProduccion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTENIVELACTV')")
     */
    public function deleteAction(Request $request, DatPartediarioProduccion $datPartediarioProduccion)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datPartediarioProduccion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datPartediarioProduccion);
            $em->flush();
        }

        return $this->redirectToRoute('parteproduccion_index');
    }

    /**
     * Creates a form to delete a DatPartediarioProduccion entity.
     *
     * @param DatPartediarioProduccion $datPartediarioProduccion The DatPartediarioProduccion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTENIVELACTV')")
     */
    private function createDeleteForm(DatPartediarioProduccion $datPartediarioProduccion)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parteproduccion_delete',
                array('id' => $datPartediarioProduccion->getIdparte())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTENIVELACTV')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $result['msg'] = 'exito';
        $result['mensaje'] = "";
        $uso = false;
        $ids = $request->request->get('id');
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        foreach ($ids as $f) {
            $objElim = $em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->find($f);
            $params['producto'] = $objElim->getProducto();
            $params['ueb'] = $objElim->getUeb();
            $params['fecha'] = $objElim->getFecha();
            $existeParteCons = $em->getRepository('ParteDiarioBundle:DatParteDiarioConsAseg')->verificarExistParteConsmatPrim($params);
            if (count($existeParteCons) == 0) {
                $em->remove($objElim);
                $em->flush();
            } else {
                $result['msg'] = 'error';
                $uso = true;
                if (count($ids) > 0) {
                    $result['mensaje'] = "Algunos de los elementos seleccionados no se eliminaron. Elimine los Partes de Consumos de Materias Primas relacionado con este producto, ueb y fecha seleccionado.";
                } else {
                    $result['mensaje'] = "El elemento seleccionado no se eliminó. Elimine el Parte de Consumos de Materias Primas relacionado con este producto, ueb y fecha seleccionado.";
                }
            }
        }

        if (!$uso) {
            if (count($ids) > 0) {
                $result['mensaje'] = "Se eliminaron satisfactoriamente los elementos seleccionados.";
            } else {
                $result['mensaje'] = "Se eliminó satisfactoriamente el elemento seleccionado.";
            }
        }


        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTENIVELACTV')")
     */
    public function listarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sesion = $request->getSession();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $where = '1=1';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'entrega') {
                    $where .= ' AND pp.entrega = ' . $sp[1];
                } else {
                    if ($sp[0] == 'producto') {
                        $alias = Util::getSlug($sp[1]);
                        $where .= ' AND p.alias LIKE \'%' . $alias . '%\'';
                    } else {
                        if ($sp[0] == 'um') {
                            $alias = Util::getSlug($sp[1]);
                            $where .= ' AND ( u.abreviatura LIKE \'%' . $sp[1] . '%\' OR u.alias LIKE \'%' . $alias . '%\')';
                        } else {
                            if ($sp[0] == 'almacen') {
                                $alias = Util::getSlug($sp[1]);
                                $where .= ' AND alm.alias LIKE \'%' . $alias . '%\'';
                            } else {
                                if ($sp[0] == 'moneda') {
                                    $alias = Util::getSlug($sp[1]);
                                    $where .= ' AND mon.alias LIKE \'%' . $alias . '%\'';
                                } else {
                                    if ($sp[0] == 'ueb') {
                                        $alias = Util::getSlug($sp[1]);
                                        $where .= ' AND eu.alias LIKE \'%' . $alias . '%\'';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $where .= ($dat[0] != 0) ? (' and eu = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and pp.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]),
                'Y-m-d') . "'") : '';

        $dql = 'SELECT pp, mon,eu,p,u,alm
            FROM ParteDiarioBundle:DatPartediarioProduccion pp
            JOIN pp.ueb eu
            JOIN pp.producto p
            JOIN pp.moneda mon
            JOIN pp.um u
            JOIN pp.almacen alm
            WHERE ' . $where . ' ORDER BY pp.fecha desc,eu.alias,p.alias';
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $produccion = $consulta->getResult();

        $parte = array();
        foreach ($produccion as $p) {
            $res = array();
            $res[] = $p->getIdparte();
            $fec = $p->getFecha();
            $res[] = $fec->format('d/m/Y');
            $ue = $p->getUeb();
            $res[] = $ue->getNombre();
            $pro = $p->getProducto();
            $res[] = $pro->getNombre();
            $res[] = $pro->getUmOperativa() != null ? $pro->getUmOperativa()->getAbreviatura() : "";
            $res[] = $p->getCantidad();
            $res[] = $p->getMoneda()->getNombre();
            $parte[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $parte, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PARTENIVELACTV')")
     */
    public function detalleParteAction(Request $request, DatPartediarioProduccion $parte, $fecha, $ueb)
    {
        return $this->render('ParteDiarioBundle:datpartediarioproduccion:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }

}
