<?php

namespace ParteDiarioBundle\Controller;

use NomencladorBundle\Util\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatParteAseguramiento;
use ParteDiarioBundle\Form\DatParteAseguramientoType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatParteAseguramiento controller.
 *
 */
class DatParteAseguramientoController extends Controller
{
    /**
     * Lists all DatParteAseguramiento entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEASEGURAMIENTO')")
     */
    public function indexAction($fecha,$ueb,$remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $fecha=$fecha==0? 0: str_replace('-','/',$fecha);
         return $this->render('ParteDiarioBundle:datparteaseguramiento:index.html.twig',
             array(
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

        $form = $this->createForm('ParteDiarioBundle\Form\DatParteAseguramientoType', $ent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ent);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('parteaseguramiento_index', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                    'remember'=> 1
                ));
            else
                return $this->redirectToRoute('parteaseguramiento_guardar', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
                ));

        }
        return $this->render('ParteDiarioBundle:datparteaseguramiento:new.html.twig', array(
            'datParteAseguramiento' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * Creates a new DatParteAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEASEGURAMIENTO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $datParteAseguramiento = new DatParteAseguramiento();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        return $this->newAuxiliar($params, $datParteAseguramiento, $em, $request);
    }

    /**
     * Creates a new DatParteAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEASEGURAMIENTO')")
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
        $datParteAseguramiento = new DatParteAseguramiento();
        return $this->newAuxiliar($params, $datParteAseguramiento, $em, $request);
    }


    /**
     * Displays a form to edit an existing DatParteAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTEASEGURAMIENTO')")
     */
    public function editAction(Request $request, DatParteAseguramiento $datparte,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedIncidencias = clone $datparte->getIncidencias();
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteAseguramientoType', $datparte);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->beginTransaction();
                try {
                    // Se eliminan las incidencias que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedIncidencias as $savedIncidencia) {
                        if ($datparte->getIncidencias()->contains($savedIncidencia) === false) {
                            $em->remove($savedIncidencia);
                        }
                    }
                    $em->persist($datparte);
                    $em->flush();
                    $em->commit();

                    return $this->redirectToRoute('parteaseguramiento_index',array(
                        'fecha' => $fecha,
                        'remember'=> 1,
                        'ueb' => $ueb));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }

        return $this->render('ParteDiarioBundle:datparteaseguramiento:new.html.twig', array(
            'datParteAseguramiento' => $datparte,
            'form' => $form->createView(),
            'action' => 'Editar',
            'fecha'=>$fecha,
            'ueb'=>$ueb
        ));
    }

    /**
     * Deletes a DatParteAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEASEGURAMIENTO')")
     */
    public function deleteAction(Request $request, DatParteAseguramiento $datParteAseguramiento)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datParteAseguramiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datParteAseguramiento);
            $em->flush();
        }

        return $this->redirectToRoute('parteaseguramiento_index');
    }

    /**
     * Creates a form to delete a DatParteAseguramiento entity.
     *
     * @param DatParteAseguramiento $datParteAseguramiento The DatParteAseguramiento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEASEGURAMIENTO')")
     */
    private function createDeleteForm(DatParteAseguramiento $datParteAseguramiento)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parteaseguramiento_delete', array('id' => $datParteAseguramiento->getIdparte())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEASEGURAMIENTO')")
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
            $objElim = $em->getRepository('ParteDiarioBundle:DatParteAseguramiento')->find($f);
            $em->remove($objElim);
            $em->flush();
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEASEGURAMIENTO')")
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
                if ($sp[0] == 'materiaprima') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND mp.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'existencia') {
                    $where .= ' AND pp.existencia LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'reserva') {
                    $where .= ' AND pp.reserva LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'cobertura') {
                    $where .= ' AND pp.cobertura LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'entrada') {
                    $where .= ' AND pp.entrada LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'demanda') {
                    $where .= ' AND pp.demanda LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'um') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND (u.abreviatura LIKE \'%' . $sp[1] . '%\' OR u.alias LIKE \'%' . $alias . '%\')';
                } else if ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND eu.alias LIKE \'%' . $alias . '%\'';
                }
            }
        }
        $where .= (!empty($dat['ueb'])) ? (' and eu = ' . $dat['ueb']) : '';
        $where .= ($dat['fecha'] != '') ? (' and pp.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat['fecha']), 'Y-m-d') . "'") : '';

        $dql = 'SELECT pp
            FROM ParteDiarioBundle:DatParteAseguramiento pp
            LEFT JOIN pp.ueb eu
            LEFT JOIN pp.materiaprima mp
            LEFT JOIN pp.um u
            WHERE ' . $where . ' ORDER BY pp.fecha desc,eu.alias,mp.alias';
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
            $ue = $a->getUeb();
            $res[] = $ue->getNombre();
            $ma = $a->getMateriaprima();
            $res[] = $ma->getNombre();
            $um = $a->getUm();
            $res[] = $um->getAbreviatura();
            $res[] = ($a->getExistencia() == null) ? '-' : $a->getExistencia();
            //$res[] = ($a->getEntrada() == null) ? '-' : $a->getEntrada();
            $res[] = ($a->getReserva() == null) ? '-' : $a->getReserva();
            //$res[] = ($a->getCobertura() == null) ? '-' : $a->getCobertura();
            //$res[] = ($a->getDemanda() == null) ? '-' : $a->getDemanda();
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
     * @Security("is_granted('ROLE_LISTAR_PARTEASEGURAMIENTO')")
     */
    public function detalleParteAction(Request $request, DatParteAseguramiento $parte,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('ParteDiarioBundle:datparteaseguramiento:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha'=>$fecha,
            'ueb'=>$ueb
        ));
    }
}
