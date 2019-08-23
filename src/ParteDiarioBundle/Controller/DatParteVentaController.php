<?php

namespace ParteDiarioBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use NomencladorBundle\Util\Util;
use ParteDiarioBundle\Entity\DatVentaProducto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatParteVenta;
use ParteDiarioBundle\Form\DatParteVentaType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatParteVenta controller.
 *
 */
class DatParteVentaController extends Controller
{
    /**
     * Lists all DatParteVenta entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEVENTA')")
     */
    public function indexAction($fecha,$ueb,$remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $fecha=$fecha==0? 0: str_replace('-','/',$fecha);
        return $this->render('ParteDiarioBundle:datparteventa:index.html.twig', array(
            'fecha'=>$fecha,
            'ueb'=>$ueb,
            'remember'=> $remember
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

        $form = $this->createForm('ParteDiarioBundle\Form\DatParteVentaType', $ent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isValid()) {
                $em->persist($ent);
                $em->flush();

                if ($form->get('aceptar')->isClicked()) {
                    return $this->redirectToRoute('parteventa_index', array(
                        'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                        'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                        'remember'=> 1
                    ));
                } else {
                    return $this->redirectToRoute('parteventa_guardar', array(
                        'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                        'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
                    ));
                }
            }
        }

        return $this->render('ParteDiarioBundle:datparteventa:new.html.twig', array(
            'datParteVentum' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * Creates a new DatParteVenta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEVENTA')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datParteVentum = new DatParteVenta();
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        return $this->newAuxiliar($params, $datParteVentum, $em, $request);
    }

    /**
     * Creates a new DatParteVenta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEVENTA')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datParteVentum = new DatParteVenta();
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        return $this->newAuxiliar($params, $datParteVentum, $em, $request);
    }


    /**
     * Displays a form to edit an existing DatParteVenta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTEVENTA')")
     */
    public function editAction(Request $request, DatParteVenta $datParteVentum,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedProducts = clone $datParteVentum->getProductos();
        $savedIncidencias = clone $datParteVentum->getIncidencias();
        $deleteForm = $this->createDeleteForm($datParteVentum);
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatParteVentaType', $datParteVentum);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->beginTransaction();
                try {
                    // Se eliminan los consumos que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedProducts as $savedProduct) {
                        if ($datParteVentum->getProductos()->contains($savedProduct) === false) {
                            $em->remove($savedProduct);
                        }
                    }
                    // Se eliminan las incidencias que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedIncidencias as $savedIncidencia) {
                        if ($datParteVentum->getIncidencias()->contains($savedIncidencia) === false) {
                            $em->remove($savedIncidencia);
                        }
                    }
                    $em->persist($datParteVentum);
                    $em->flush();
                    $em->commit();

                    return $this->redirectToRoute('parteventa_index',
                        array('fecha'=>$fecha,
                            'ueb'=>$ueb,
                            'remember'=> 1
                        ));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }


        return $this->render('ParteDiarioBundle:datparteventa:new.html.twig', array(
            'datParteVentum' => $datParteVentum,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'action' => 'Editar',
            'fecha'=>$fecha,
            'ueb'=>$ueb
        ));
    }

    /**
     * Deletes a DatParteVenta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEVENTA')")
     */
    public function deleteAction(Request $request, DatParteVenta $datParteVentum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datParteVentum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datParteVentum);
            $em->flush();
        }

        return $this->redirectToRoute('parteventa_index');
    }

    /**
     * Creates a form to delete a DatParteVenta entity.
     *
     * @param DatParteVenta $datParteVentum The DatParteVenta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEVENTA')")
     */
    private function createDeleteForm(DatParteVenta $datParteVentum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parteventa_delete', array('id' => $datParteVentum->getIdparte())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEVENTA')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $msg = 'exito';
        $ids = $request->request->get('id');

        if (!is_array($ids)) {
            $ids = [$ids];
        }
        foreach ($ids as $f) {
            $parte = $em->getRepository('ParteDiarioBundle:DatParteVenta')->find($f);
            $em->remove($parte);
            $em->flush();
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEVENTA')")
     */
    public function cancelarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $msg = 'exito';
        $ids = $request->request->get('id');
        $concepto = $request->request->get('concepto');
        foreach ($ids as $f) {
            try {
                $parte = $em->getRepository('ParteDiarioBundle:DatParteVenta')->find($f);
                $parte->setCancelada($concepto);
                $em->persist($parte);
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        $em->flush();
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEVENTA')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $sesion = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $where = '1=1';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'factura') {
                    $where .= ' AND p.factura LIKE \'%' . $sp[1] . '%\'';
                } else {
                    if ($sp[0] == 'cliente') {
                        $alias = Util::getSlug($sp[1]);
                        $where .= ' AND c.alias LIKE \'%' . $alias . '%\'';
                    } else {
                        if ($sp[0] == 'ueb') {
                            $alias = Util::getSlug($sp[1]);
                            $where .= ' AND ue.alias LIKE \'%' . $alias . '%\'';
                        }
                    }
                }
            }
        }
        $where .= ($dat[0] != 0) ? (' and p.ueb = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and p.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]),
                'Y-m-d') . "'") : '';

        $dql = 'SELECT p,c
            FROM ParteDiarioBundle:DatParteVenta p
            JOIN p.cliente c
            JOIN p.ueb ue
            WHERE  ' . $where . ' and p.cancelada is Null ORDER BY p.fecha desc,ue.alias,c.alias';
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $aseg = $consulta->getResult();
        $parte = array();
        foreach ($aseg as $a) {
            $res = array();
            $res[] = $a->getIdparte();
            $fec = $a->getFecha();
            $res[] = $fec->format('d/m/Y');
            $ueb = $a->getUeb();
            $res[] = $ueb->getNombre();
            $res[] = $a->getCliente()->getNombre();
            $res[] = $a->getFactura();
            $parte[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $parte, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEVENTA')")
     */
    public function findPreciosProductoAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $params['idproducto'] = $request->request->get('producto');
        $params['identidad'] = $request->request->get('cliente');
        $arr = $this->get('nomenclador.nomprecio')->precioProducto($params);
        if ($arr != null) {
            return new JsonResponse(array('data' => $arr));
        } else {
            return new JsonResponse(array('data' => []));
        }
    }


    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PARTEVENTA')")
     */
    public function detalleParteAction(Request $request, DatParteVenta $parte,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('ParteDiarioBundle:datparteventa:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha'=>$fecha,
            'ueb'=>$ueb,
        ));
    }
}
