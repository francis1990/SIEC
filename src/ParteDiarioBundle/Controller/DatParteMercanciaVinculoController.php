<?php

namespace ParteDiarioBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use NomencladorBundle\Util\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatParteMercanciaVinculo;
use ParteDiarioBundle\Form\DatParteMercanciaVinculoType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatParteMercanciaVinculo controller.
 *
 */
class DatParteMercanciaVinculoController extends Controller
{
    /**
     * Lists all DatParteMercanciaVinculo entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEMERCANCIA')")
     */
    public function indexAction($fecha,$ueb,$remember)
    {
        $fecha=$fecha==0? 0: str_replace('-','/',$fecha);
        return $this->render('ParteDiarioBundle:datpartemercanciavinculo:index.html.twig', array(
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

        $form = $this->createForm('ParteDiarioBundle\Form\DatParteMercanciaVinculoType', $ent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
          //  $ent->setAnno($ent->getFecha()->format('Y'));
            $em->persist($ent);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('partemercanciavinculo_index', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                    'remember'=> 1
                ));
            else
                return $this->redirectToRoute('partemercanciavinculo_guardar', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
                ));
        }
        return $this->render('ParteDiarioBundle:datpartemercanciavinculo:new.html.twig', array(
            'datpartemercanciavinculo' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * Creates a new DatParteMercanciaVinculo entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEMERCANCIA')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datpartemercanciavinculo = new DatParteMercanciaVinculo();
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        return $this->newAuxiliar($params, $datpartemercanciavinculo, $em, $request);
    }

    /**
     * Creates a new DatParteMercanciaVinculo entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEMERCANCIA')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datpartemercanciavinculo = new DatParteMercanciaVinculo();
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        return $this->newAuxiliar($params, $datpartemercanciavinculo, $em, $request);
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTEMERCANCIA')")
     */
    public function editAction(Request $request, DatParteMercanciaVinculo $datparte,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedIncidencias = clone $datparte->getIncidencias();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteMercanciaVinculoType', $datparte);
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

                    return $this->redirectToRoute('partemercanciavinculo_index',
                        array(
                            'fecha'=>$fecha,
                            'ueb'=>$ueb,
                            'remember'=> 1
                        ));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }
        return $this->render('ParteDiarioBundle:datpartemercanciavinculo:new.html.twig', array(
            'datpartemercanciavinculo' => $datparte,
            'form' => $form->createView(),
            'action' => 'Editar',
            'fecha'=>$fecha,
            'ueb'=>$ueb
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEMERCANCIA')")
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
            $objElim = $em->getRepository('ParteDiarioBundle:DatParteMercanciaVinculo')->find($f);
            $em->remove($objElim);
            $em->flush();
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEMERCANCIA')")
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
                if ($sp[0] == 'producto') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND pro.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'um') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND ( u.abreviatura LIKE \'%' . $sp[1] . '%\' OR u.alias LIKE \'%' . $alias . '%\')';
                } else if ($sp[0] == 'almacen') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND a.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'entidad') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND e.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'cantidad') {
                    $where .= ' AND pp.cantidad LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND eu.alias LIKE \'%' . $alias . '%\'';
                }
            }
        }
        $where .= ($dat[0] != 0) ? (' and eu = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and pp.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]), 'Y-m-d') . "'") : '';

        $dql = 'SELECT pp,eu,a,e, pro
            FROM ParteDiarioBundle:DatParteMercanciaVinculo pp
            JOIN pp.ueb eu
            LEFT JOIN pp.almacen a
            JOIN pp.um u
            LEFT JOIN pp.entidad e
            JOIN pp.producto pro
            WHERE ' . $where . 'ORDER BY pp.fecha desc,eu.alias,pro.alias';
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
            $res[] = $a->getUeb()->getNombre();
            $res[] = $a->getFactura();
            $res[] = $a->getProducto()->getNombre();
            $res[] = $a->getUm()->getAbreviatura();
            $res[] = $a->getCantidad();
            /*$res[] = $a->getAlmacen() ? $a->getAlmacen()->getCodigo() . ' ' . $a->getAlmacen()->getNombre() : '';
            $res[] = $a->getEntidad()->getNombre();*/
            $parte[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $parte, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEMERCANCIA')")
     */
    public function getTreeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $node = ($request->get('id') === '#' || $request->get('id') == null) ? null :
            $em->getRepository('NomencladorBundle:NomAseguramiento')->find($request->get('id'));;
        $data = $this->get('nomenclador.nomaseguramiento')->getHijosAseguramiento2($node);
        return new JsonResponse($data);
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PARTEMERCANCIA')")
     */
    public function detalleParteAction(Request $request, DatParteMercanciaVinculo $parte,$fecha,$ueb)
    {
        return $this->render('ParteDiarioBundle:datpartemercanciavinculo:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha'=>$fecha,
            'ueb'=>$ueb
        ));
    }

}
