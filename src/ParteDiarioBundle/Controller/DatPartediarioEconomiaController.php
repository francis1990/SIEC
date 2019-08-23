<?php

namespace ParteDiarioBundle\Controller;

use NomencladorBundle\Util\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatPartediarioEconomia;
use ParteDiarioBundle\Form\DatPartediarioEconomiaType;
use Symfony\Component\Validator\Constraints\Date;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatPartediarioEconomia controller.
 *
 */
class DatPartediarioEconomiaController extends Controller
{
    /**
     * Lists all DatPartediarioEconomia entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEECONOMIA')")
     */
    public function indexAction($fecha,$ueb,$remember)
    {
        $fecha=$fecha==0? 0: str_replace('-','/',$fecha);
        return $this->render('ParteDiarioBundle:datpartediarioeconomia:index.html.twig', array(
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
        //dump($fechaAux);die('23333');
        $form = $this->createForm('ParteDiarioBundle\Form\DatPartediarioEconomiaType', $ent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ent);
            $em->flush();

            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('parteeconomia_index', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb']!=""? $params['idueb']:0,
                    'remember'=> 1
                ));
            else
                return $this->redirectToRoute('parteeconomia_guardar', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' =>$params['idueb']!=""? $params['idueb']:0
                ));
        }

        return $this->render('ParteDiarioBundle:datpartediarioeconomia:new.html.twig', array(
            'datPartediarioEconomium' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' =>$params['idueb']!=""? $params['idueb']:0
        ));
    }

    /**
     * Creates a new DatPartediarioEconomia entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEECONOMIA')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datPartediarioEconomium = new DatPartediarioEconomia();
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        //dump($params);die('-----');
        return $this->newAuxiliar($params, $datPartediarioEconomium, $em, $request);
    }

    /**
     * Creates a new DatPartediarioEconomia entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEECONOMIA')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datPartediarioEconomium = new DatPartediarioEconomia();
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        return $this->newAuxiliar($params, $datPartediarioEconomium, $em, $request);
    }


    /**
     * Displays a form to edit an existing DatPartediarioEconomia entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTEECONOMIA')")
     */
    public function editAction(Request $request, DatPartediarioEconomia $datparte,$fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedIncidencias = clone $datparte->getIncidencias();
        $form = $this->createForm('ParteDiarioBundle\Form\DatPartediarioEconomiaType', $datparte, array(
            'action' => $this->generateUrl('parteeconomia_edit', array('id' => $datparte->getIdparte(),'fecha'=> $fecha,'ueb'=>$ueb))
        ));
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
                    return $this->redirectToRoute('parteeconomia_index',
                        array('fecha'=>$fecha,
                            'ueb'=>$ueb,
                            'remember'=> 1
                        ));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }

        return $this->render('ParteDiarioBundle:datpartediarioeconomia:new.html.twig', array(
            'datPartediarioEconomium' => $datparte,
            'form' => $form->createView(),
            'action' => 'Editar',
            'fecha'=>$fecha,
            'ueb'=>$ueb
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEECONOMIA')")
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
            $objElim = $em->getRepository('ParteDiarioBundle:DatPartediarioEconomia')->find($f);
            $em->remove($objElim);
            $em->flush();
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEECONOMIA')")
     */
    public function listarAction(Request $request)
    {
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
                if ($sp[0] == 'numero') {
                    $where .= ' AND tt.numero LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'cuenta') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND tt.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'saldo') {
                    $where .= ' AND pp.saldo LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND eu.alias LIKE \'%' . $alias . '%\'';
                }
            }
        }
        $where .= ($dat[0] != 0) ? (' and eu = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and pp.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]), 'Y-m-d') . "'") : '';

        $dql = 'SELECT pp
            FROM ParteDiarioBundle:DatPartediarioEconomia pp
            JOIN pp.ueb eu
            JOIN pp.idcuentacontable tt
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
            $ue = $a->getUeb();
            $res[] = $ue->getNombre();
            $tt = $a->getIdcuentacontable();
            //$res[] = $tt->getNumero();
            $res[] = $tt->getNombre();
            $res[] = $a->getSaldo();
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
     * @Security("is_granted('ROLE_LISTAR_PARTEECONOMIA')")
     */
    public function detalleParteAction(Request $request, DatPartediarioEconomia $parte,$fecha,$ueb)
    {
        return $this->render('ParteDiarioBundle:datpartediarioeconomia:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }
}
