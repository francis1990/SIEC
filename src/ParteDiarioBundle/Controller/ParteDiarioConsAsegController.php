<?php

namespace ParteDiarioBundle\Controller;

use AdminBundle\Entity\Traza;
use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use NomencladorBundle\Entity\NomNorma;
use NomencladorBundle\Util\Util;
use ParteDiarioBundle\Entity\DatConsumoAseguramiento;
use ParteDiarioBundle\Entity\DatParteDiarioConsAseg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

class ParteDiarioConsAsegController extends Controller
{
    /**
     * Lists all DatParteConsAseg entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTECONSUMO')")
     */
    public function indexAction($fecha, $ueb, $remember)
    {
        $em = $this->getDoctrine()->getManager();
        $parteConsAseg = $em->getRepository('ParteDiarioBundle:DatParteDiarioConsAseg')->findAll();

        $fecha = $fecha == 0 ? 0 : str_replace('-', '/', $fecha);
        return $this->render('ParteDiarioBundle:datparteconsaseg:index.html.twig', array(
            'partes' => $parteConsAseg,
            'fecha' => $fecha,
            'ueb' => $ueb,
            'remember' => $remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTECONSUMO')")
     */
    public function listarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $where = '1=1';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) { //dump($f);die;
                $sp = explode(':', $f);
                if ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND eu.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'nombre') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND pro.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'codigo') {
                    $where .= ' AND pro.codigo LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'um') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND ( um.abreviatura LIKE \'%' . $sp[1] . '%\' OR um.alias LIKE \'%' . $alias . '%\')';
                } else if ($sp[0] == 'nactv') {
                    $where .= ' AND pp.nivelact = ' . $sp[1];
                }
            }
        }

        $dat = $request->query->get('dat');
        $where .= ($dat[0] != 0) ? (' and eu = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and pp.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]), 'Y-m-d') . "'") : '';

        $dql = 'SELECT pp
            FROM ParteDiarioBundle:DatParteDiarioConsAseg pp
            JOIN pp.ueb eu
            JOIN pp.producto pro
            JOIN pro.umOperativa um
            WHERE ' . $where . ' ORDER BY pp.fecha desc,eu.alias,pro.alias';
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $consAseg = $consulta->getResult();
        $parte = array();
        foreach ($consAseg as $p) {
            $res = array();
            $res[] = $p->getIdParte();
            $fec = $p->getFecha();
            $res[] = $fec->format('d/m/Y');
            $res[] = $p->getUeb()->getNombre();
            $res[] = $p->getProducto()->getNombre();
            $res[] = $p->getProducto()->getUmOperativa() != null ? $p->getProducto()->getUmOperativa()->getAbreviatura() : "";
            $res[] = $p->getNivelact();
            $res[] = $p->getTiponorma()->getNombre();
            $parte[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $parte, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => array(), 'total' => 0));
        }
    }

    private function newAuxiliar($params, $ent, $em, $request)
    {
        if ($params['idueb'] != 0) {
            $ueb = $em->getRepository('NomencladorBundle:NomUeb')->find($params['idueb']);
            $ent->setUeb($ueb);
        }
        if ($params['fecha'] != 0 && $params['fecha'] != "") {
            if (date_create_from_format('d-m-Y', $params['fecha'])) {
                $fechaAux1 = date_create_from_format('d-m-Y', $params['fecha']);
                $fechaAux2 = $fechaAux1->format('d/m/Y');
                $fechaAux = date_create_from_format('d/m/Y', $fechaAux2);
            } else {
                $fechaAux = date_create_from_format('d/m/Y', $params['fecha']);
            }
            $ent->setFecha($fechaAux);
        }

        $form = $this->createForm('ParteDiarioBundle\Form\DatParteDiarioConsAsegType', $ent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('cantidadxnc')->getData() == "") {
                $ent->setCantidadxnc($form->get('nivelact')->getData());
            }
            $em->persist($ent);
            $em->flush();
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('parte_consaseg_index', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                    'remember' => 1
                ));
            else {
                return $this->redirectToRoute('parte_consaseg_guardar', array(
                    'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                    'ueb' => $params['fecha'] != 0 ? $params['idueb'] : 0
                ));
            }
        }

        return $this->render('ParteDiarioBundle:datparteconsaseg:new.html.twig', array(
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTECONSUMO')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $parteConsAseg = new DatParteDiarioConsAseg();
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        return $this->newAuxiliar($params, $parteConsAseg, $em, $request);
    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTECONSUMO')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $parteConsAseg = new DatParteDiarioConsAseg();
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        return $this->newAuxiliar($params, $parteConsAseg, $em, $request);
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTECONSUMO')")
     */
    public function editarAction(Request $request, DatParteDiarioConsAseg $parteConsAseg, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $usuario = $this->get('security.context')->getToken()->getUser();
        $savedConsumos = clone $parteConsAseg->getConsumos();
        $savedIncidencias = clone $parteConsAseg->getIncidencias();
        $deleteForm = $this->createDeleteForm($parteConsAseg);
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatParteDiarioConsAsegType', $parteConsAseg/*, array(
            'action' => $this->generateUrl('parte_consaseg_edit', array('id' => $parteConsAseg->getIdparte()))
        )*/);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->beginTransaction();
                try {
                    // Se eliminan los consumos que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedConsumos as $savedConsumo) {
                        if ($parteConsAseg->getConsumos()->contains($savedConsumo) === false) {
                            $em->remove($savedConsumo);
                        }
                    }
                    // Se eliminan las incidencias que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedIncidencias as $savedIncidencia) {
                        if ($parteConsAseg->getIncidencias()->contains($savedIncidencia) === false) {
                            $em->remove($savedIncidencia);
                        }
                    }
                    $em->persist($parteConsAseg);

                    $traza = new Traza();
                    $traza->setUsuario($usuario);
                    $traza->setUeb($usuario->getUeb());
                    $traza->setFechaCreacion(new \DateTime());
                    $traza->setDescTraza("Se ha editado el parte de consumo de materia prima: Producto: " . $parteConsAseg->getProducto()->getNombre());
                    $em->persist($traza);
                    $em->flush();
                    $em->commit();

                    return $this->redirectToRoute('parte_consaseg_index', array(
                        'fecha' => $fecha,
                        'ueb' => $ueb,
                        'parteConsAseg' => $parteConsAseg,
                        'remember' => 1
                    ));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }
        return $this->render('ParteDiarioBundle:datparteconsaseg:new.html.twig', array(
            'parteConsAseg' => $parteConsAseg,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'action' => 'Editar',
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTECONSUMO')")
     */
    private function createDeleteForm(DatParteDiarioConsAseg $parteConsAseg)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parte_consaseg_delete', array('id' => $parteConsAseg->getIdparte())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTECONSUMO')")
     */
    public function deleteAction(Request $request, DatParteDiarioConsAseg $parteConsAseg)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($parteConsAseg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($parteConsAseg);
            $em->flush();
        }
        return $this->redirectToRoute('parte_consaseg_index');
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTECONSUMO')")
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
            $objElim = $em->getRepository('ParteDiarioBundle:DatParteDiarioConsAseg')->find($f);
            $em->remove($objElim);
            $em->flush();
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTECONSUMO')")
     */
    public function findNormaByProAction(Request $request)
    {
        $idpro = $request->request->get('id');
        $tipoNC = $request->request->get('tiponc');
        $em = $this->getDoctrine()->getManager();
        $norma = $em->getRepository('NomencladorBundle:NomNorma')->findBy(['tiponorma' => $tipoNC, 'producto' => $idpro]);
        //dump($norma);die;
        if (count($norma) != 0) {
            $parte = new DatParteDiarioConsAseg();

            if (count($norma[0]->getAseguramientos()) > 0) {
                foreach ($norma[0]->getAseguramientos() as $aseg) {
                    $datConsumo = new DatConsumoAseguramiento();
                    $datConsumo->setAseguramiento($aseg);
                    $parte->addConsumo($datConsumo);
                }
            }

            $form = $this->createForm('ParteDiarioBundle\Form\DatParteDiarioConsAsegType', $parte);
            $form->handleRequest($request);

            return $this->render('@ParteDiario/datparteconsaseg/consumoaseg.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            return new JsonResponse(array('respuesta' => true));
        }
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTECONSUMO')")
     */
    public function obtenerNivelActividadAction(Request $request)
    {
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $params['id'] = $request->request->get('id');
        $params['ueb'] = $request->request->get('ueb');
        $params['fecha'] = $request->request->get('fecha');
        $em = $this->getDoctrine()->getManager();
        $params['producto'] = $em->getRepository('NomencladorBundle:NomProducto')->find($params['id']);
        $nivelAct = $em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->getNivelActividad($params);
        return new JsonResponse(array('nivelact' => $nivelAct[1]));

    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PARTECONSUMO')")
     */
    public function detalleParteAction(Request $request, DatParteDiarioConsAseg $parte, $fecha, $ueb)
    {
        return $this->render('ParteDiarioBundle:datparteconsaseg:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha' => $fecha,
            'ueb' => $ueb
        ));
    }
}
