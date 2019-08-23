<?php

namespace ParteDiarioBundle\Controller;

use NomencladorBundle\Util\Util;
use ParteDiarioBundle\Entity\DatEntidadAcopio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatParteAcopio;
use ParteDiarioBundle\Form\DatParteAcopioType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use DateTime;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatParteAcopio controller.
 *
 */
class DatParteAcopioController extends Controller
{
    /**
     * Lists all DatParteAcopio entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEACOPIO')")
     */
    public function indexAction($fecha, $ueb, $remember)
    {
        $fecha = $fecha == 0 ? 0 : str_replace('-', '/', $fecha);
        return $this->render('ParteDiarioBundle:datparteacopio:index.html.twig',
            array(
                'fecha' => $fecha,
                'ueb' => $ueb,
                'remember' => $remember
            ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEACOPIO')")
     */
    public function listarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $where = '1=1';
        $where .= ($dat[0] != 0) ? (' and a.ueb = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and a.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]), 'Y-m-d') . "'") : '';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'ruta') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= " AND r.alias LIKE '%" . $alias . "%'";
                } else if ($sp[0] == 'cantidad') {
                    $where .= " AND acop.cantidad = " . $sp[1];
                } else if ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= " AND ueb.alias LIKE '%" . $alias . "%'";
                }
            }
        }
        $dql = 'SELECT a
            FROM ParteDiarioBundle:DatParteAcopio a
            JOIN a.ruta r
            join a.ueb ueb
            join a.acopio acop
            WHERE ' . $where . 'ORDER BY a.fecha desc,ueb.alias,r.alias';
        $consulta = $em->createQuery($dql);

        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults(10);
        $acopio = $consulta->getResult();

        $parte = array();
        foreach ($acopio as $a) {
            $res = array();
            $res[] = $a->getIdparte();
            $fec = $a->getFecha();
            $res[] = $fec->format('d/m/Y');
            $res[] = $a->getUeb()->getNombre();
            $ruta = $a->getRuta();
            $res[] = $ruta->getNombre();
            $destinoRes = $a->getDestino();

            $destinoAux = explode("-", $destinoRes);
            if ($destinoAux[1] == "0") {
                $destino = $em->getRepository('NomencladorBundle:NomEntidad')->find($destinoAux[0]);
            } else {
                $destino = $em->getRepository('NomencladorBundle:NomUeb')->find($destinoAux[0]);
            }
            $res[] = $destino->getNombre();
            //suministrador
            $res[] = $em->getRepository('ParteDiarioBundle:DatParteAcopio')->cantidadParte(['id' => $a->getIdparte(), 'ruta' => $ruta]);
            $parte[] = $res;
        }

        if (count($total) > 0) {
            return new JsonResponse(array('data' => $parte, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
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

        $entsuebs = $em->getRepository('NomencladorBundle:NomEntidad')->getEntidadesTiposAcopioyUEB();
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteAcopioType', $ent, array(
            'valores' => $entsuebs
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ent);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('parteacopio_index', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                    'remember' => 1
                ));
            else
                return $this->redirectToRoute('parteacopio_guardar', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
                ));
        }
        return $this->render('ParteDiarioBundle:datparteacopio:nuevo.html.twig', array(
            'datParteAcopio' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * Creates a new DatParteAcopio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEACOPIO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        $datParteAcopio = new DatParteAcopio();
        return $this->newAuxiliar($params, $datParteAcopio, $em, $request);
    }

    /**
     * Creates a new DatParteAcopio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEACOPIO')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        $datParteAcopio = new DatParteAcopio();
        return $this->newAuxiliar($params, $datParteAcopio, $em, $request);
    }


    /**
     * Displays a form to edit an existing DatParteAcopio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTEACOPIO')")
     */
    public function editAction(Request $request, DatParteAcopio $datParteAcopio, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $savedIncidencias = clone $datParteAcopio->getIncidencias();
        $savedAcopios = clone $datParteAcopio->getAcopio();

        $deleteForm = $this->createDeleteForm($datParteAcopio);
        $entsuebs = $em->getRepository('NomencladorBundle:NomEntidad')->getEntidadesTiposAcopioyUEB();
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatParteAcopioType', $datParteAcopio, array(
            'valores' => $entsuebs,
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($editForm->isValid()) {
                $em->beginTransaction();
                try {
                    // Se eliminan los acopio que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedAcopios as $savedAcopio) {
                        if ($datParteAcopio->getAcopio()->contains($savedAcopio) === false) {
                            $em->remove($savedAcopio);
                        }
                    }
                    // Se eliminan las incidencias que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedIncidencias as $savedIncidencia) {
                        if ($datParteAcopio->getIncidencias()->contains($savedIncidencia) === false) {
                            $em->remove($savedIncidencia);
                        }
                    }
                    $em->persist($datParteAcopio);
                    $em->flush();
                    $em->commit();

                    return $this->redirectToRoute('parteacopio_index', array(
                        'fecha' => $fecha,
                        'ueb' => $ueb,
                        'remember' => 1
                    ));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }

        return $this->render('ParteDiarioBundle:datparteacopio:nuevo.html.twig', array(
            'datParteAcopio' => $datParteAcopio,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'action' => 'Editar',
            'fecha' => $fecha,
            'ueb' => $ueb,
        ));
    }

    /**
     * Deletes a DatParteAcopio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEACOPIO')")
     */
    public function deleteAction(Request $request, DatParteAcopio $datParteAcopio)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datParteAcopio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datParteAcopio);
            $em->flush();
        }

        return $this->redirectToRoute('parteacopio_index');
    }

    /**
     * Creates a form to delete a DatParteAcopio entity.
     *
     * @param DatParteAcopio $datParteAcopio The DatParteAcopio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEACOPIO')")
     */
    private function createDeleteForm(DatParteAcopio $datParteAcopio)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parteacopio_delete', array('id' => $datParteAcopio->getIdparte())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEACOPIO')")
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
        if (!is_array($ids))
            $ids = [$ids];
        foreach ($ids as $f) {
            $objElim = $em->getRepository('ParteDiarioBundle:DatParteAcopio')->find($f);
            $em->remove($objElim);
            $em->flush();
        }

        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEACOPIO')")
     */
    public function findSuminByRutaAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $idruta = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $rutas = $em->getRepository('NomencladorBundle:NomRuta')->findSuminRuta($idruta);
        $parte = new DatParteAcopio();
        foreach ($rutas as $ruta) {
            $datAcopio = new DatEntidadAcopio();
            $datAcopio->setProducto($ruta->getProducto());
            $datAcopio->setEntidad($ruta->getEntidad());
            $datAcopio->setUm($ruta->getProducto()->getUmOperativa());
            $parte->addAcopio($datAcopio);
        }
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteAcopioType', $parte);
        $form->handleRequest($request);

        return $this->render('@ParteDiario/datparteacopio/suministro.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PARTEACOPIO')")
     */
    public function detalleParteAction(Request $request, DatParteAcopio $parte, $fecha, $ueb)
    {
        $em = $this->getDoctrine()->getManager();
        $destinoRes = $parte->getDestino();
        $destinoAux = explode("-", $destinoRes);
        if ($destinoAux[1] == "0") {
            $destino = $em->getRepository('NomencladorBundle:NomEntidad')->find($destinoAux[0]);
        } else {
            $destino = $em->getRepository('NomencladorBundle:NomUeb')->find($destinoAux[0]);
        }
        return $this->render('ParteDiarioBundle:datparteacopio:detalle_parte.html.twig', array(
            'parte' => $parte,
            'destino' => $destino,
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }


}
