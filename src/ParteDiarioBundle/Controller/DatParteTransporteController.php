<?php

namespace ParteDiarioBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use NomencladorBundle\Util\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatParteTransporte;
use ParteDiarioBundle\Form\DatParteTransporteType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatParteTransporte controller.
 *
 */
class DatParteTransporteController extends Controller
{
    /**
     * Lists all DatParteTransporte entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTETRANSPORTE')")
     */
    public function indexAction($fecha,$ueb,$remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $fecha=$fecha==0? 0: str_replace('-','/',$fecha);

        return $this->render('ParteDiarioBundle:datpartetransporte:index.html.twig', array(
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

        $form = $this->createForm('ParteDiarioBundle\Form\DatParteTransporteType', $ent);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($ent);
                $em->flush();
                $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
                if ($form->get('aceptar')->isClicked())
                    return $this->redirectToRoute('partetransporte_index', array(
                        'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                        'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                        'remember'=> 1
                    ));
                else
                    return $this->redirectToRoute('partetransporte_guardar', array(
                        'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                        'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
                    ));
            }
        }
        return $this->render('ParteDiarioBundle:datpartetransporte:new.html.twig', array(
            'datParteTransporte' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * Creates a new DatParteTransporte entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTETRANSPORTE')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datParteTransporte = new DatParteTransporte();
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        return $this->newAuxiliar($params, $datParteTransporte, $em, $request);
    }

    /**
     * Creates a new DatParteTransporte entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTETRANSPORTE')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datParteTransporte = new DatParteTransporte();
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        return $this->newAuxiliar($params, $datParteTransporte, $em, $request);
    }


    /**
     * Displays a form to edit an existing DatParteTransporte entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTETRANSPORTE')")
     */
    public function editAction(Request $request, DatParteTransporte $datparte,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedIncidencias = clone $datparte->getIncidencias();
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteTransporteType', $datparte);
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

                    return $this->redirectToRoute('partetransporte_index',array(
                        'fecha' => $fecha,
                        'ueb' => $ueb,
                        'remember'=> 1
                    ));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }
        return $this->render('ParteDiarioBundle:datpartetransporte:new.html.twig', array(
            'datParteTransporte' => $datparte,
            'form' => $form->createView(),
            'action' => 'Editar',
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTETRANSPORTE')")
     */
    public function deleteAction(Request $request)
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
            $objElim = $em->getRepository('ParteDiarioBundle:DatParteTransporte')->find($f);
            $em->remove($objElim);
            $em->flush();
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTETRANSPORTE')")
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
                if ($sp[0] == 'tipotransporte') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND tt.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'existencia') {
                    $where .= ' AND pp.existencia = ' . $sp[1];
                } else if ($sp[0] == 'disponible') {
                    $where .= ' AND pp.disponible = ' . $sp[1];
                } else if ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND eu.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'cdt') {
                    $where .= ' AND pp.cdt = ' . $sp[1];
                }
            }
        }
        $where .= ($dat[0] != 0) ? (' and eu = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and pp.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]), 'Y-m-d') . "'") : '';

        $dql = 'SELECT pp,tt
            FROM ParteDiarioBundle:DatParteTransporte pp
            JOIN pp.ueb eu
            JOIN pp.tipotransporte tt
            WHERE ' . $where . ' ORDER BY pp.fecha desc,eu.alias,tt.alias';
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
            $eu = $a->getUeb();
            $res[] = $eu->getNombre();
            $tt = $a->getTipotransporte();
            $res[] = $tt->getNombre();
            $res[] = $a->getExistencia();
            $res[] = $a->getDisponible();
            $res[] = $a->getCdt();
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
     * @Security("is_granted('ROLE_LISTAR_PARTETRANSPORTE')")
     */
    public function detalleParteAction(Request $request, DatParteTransporte $parte,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('ParteDiarioBundle:datpartetransporte:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }
}
