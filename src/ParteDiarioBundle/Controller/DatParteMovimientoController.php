<?php

namespace ParteDiarioBundle\Controller;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use EnumsBundle\Entity\EnumClasificacionConcepto;
use NomencladorBundle\Util\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatParteMovimiento;
use ParteDiarioBundle\Form\DatParteMovimientoType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatParteMovimiento controller.
 *
 */
class DatParteMovimientoController extends Controller
{
    /**
     * Lists all DatParteMovimiento entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEMOVIMIENTO')")
     */
    public function indexAction($fecha, $ueb, $remember)
    {
        $fecha = $fecha == 0 ? 0 : str_replace('-', '/', $fecha);
        return $this->render('ParteDiarioBundle:datpartemovimiento:index.html.twig', array(
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

        $form = $this->createForm('ParteDiarioBundle\Form\DatParteMovimientoType', $ent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ent);
            $em->flush();

            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('partemovimiento_index', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                    'remember' => 1
                ));
            else
                return $this->redirectToRoute('partemovimiento_guardar', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
                ));
        }
        return $this->render('ParteDiarioBundle:datpartemovimiento:new.html.twig', array(
            'datParteMovimiento' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * Creates a new DatParteMovimiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEMOVIMIENTO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datParteMovimiento = new DatParteMovimiento();
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        return $this->newAuxiliar($params, $datParteMovimiento, $em, $request);
    }

    /**
     * Creates a new DatParteMovimiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEMOVIMIENTO')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datParteMovimiento = new DatParteMovimiento();
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        return $this->newAuxiliar($params, $datParteMovimiento, $em, $request);
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTEMOVIMIENTO')")
     */
    public function editAction(Request $request, DatParteMovimiento $datParteMovimiento, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedIncidencias = clone $datParteMovimiento->getIncidencias();
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteMovimientoType', $datParteMovimiento);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->beginTransaction();
                try {
                    // Se eliminan las incidencias que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedIncidencias as $savedIncidencia) {
                        if ($datParteMovimiento->getIncidencias()->contains($savedIncidencia) === false) {
                            $em->remove($savedIncidencia);
                        }
                    }
                    $em->persist($datParteMovimiento);
                    $em->flush();
                    $em->commit();

                    return $this->redirectToRoute('partemovimiento_index', array(
                        'fecha' => $fecha,
                        'ueb' => $ueb,
                        'remember' => 1
                    ));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }
        return $this->render('ParteDiarioBundle:datpartemovimiento:new.html.twig', array(
            'datParteMovimiento' => $datParteMovimiento,
            'form' => $form->createView(),
            'action' => 'Editar',
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEMOVIMIENTO')")
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
            $objElim = $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->find($f);
            $em->remove($objElim);
            $em->flush();
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEMOVIMIENTO')")
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
                if ($sp[0] == 'almacen') {
                    $where .= ' AND (a.nombre LIKE \'%' . $sp[1] . '%\'  or a.codigo like \'%' . $sp[1] . '%\') ';
                } else if ($sp[0] == 'producto') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND pro.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'um') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND ( u.abreviatura LIKE \'%' . $sp[1] . '%\' OR u.alias LIKE \'%' . $alias . '%\')';
                } else if ($sp[0] == 'concepto') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND con.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'cantidad') {
                    $where .= ' AND pp.cantidad LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND eu.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'existencia') {
                    $where .= ' AND pp.existencia LIKE \'%' . $sp[1] . '%\'';
                }
            }
        }
        $where .= ($dat[0] != 0) ? (' and eu = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and pp.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]), 'Y-m-d') . "'") : '';

        $dql = 'SELECT pp,eu,a, pro, u
            FROM ParteDiarioBundle:DatParteMovimiento pp
            JOIN pp.ueb eu
            JOIN pp.um u
            JOIN pp.almacen a
            JOIN pp.producto pro
            JOIN pp.concepto con
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
            $eu = $a->getUeb();
            $res[] = $eu->getNombre();
            $pro = $a->getProducto();
            $res[] = $pro->getNombre();
            $um = $a->getUm();
            $res[] = $um->getAbreviatura();
            $res[] = $a->getCantidad();
            $res[] = $a->getExistencia();
            $alm = $a->getAlmacen();
            $res[] = $alm->getCodigo() . ' ' . $alm->getNombre();
            $res[] = $a->getConcepto()->getNombre();
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
     * @Security("is_granted('ROLE_LISTAR_PARTEMOVIMIENTO')")
     */
    public function detalleParteAction(Request $request, DatParteMovimiento $parte, $fecha, $ueb)
    {
        return $this->render('ParteDiarioBundle:datpartemovimiento:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }

}
