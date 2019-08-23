<?php

namespace ParteDiarioBundle\Controller;

use NomencladorBundle\Util\Util;
use ParteDiarioBundle\Entity\DatPartePortadorMedidor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatPartePortador;
use ParteDiarioBundle\Form\DatPartePortadorType;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatPartePortador controller.
 *
 */
class DatPartePortadorController extends Controller
{
    /**
     * Lists all DatPartePortador entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEPORTADORES')")
     */
    public function indexAction($fecha,$ueb,$remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $fecha=$fecha==0? 0: str_replace('-','/',$fecha);
        return $this->render('ParteDiarioBundle:datparteportador:index.html.twig', array(
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

        $ent->addListMedidor(new DatPartePortadorMedidor());
        $form = $this->createForm('ParteDiarioBundle\Form\DatPartePortadorType', $ent);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $ent->setConsumo($this->calculateConsumoTotal($ent));
                $em->persist($ent);
                $em->flush();

                if ($form->get('aceptar')->isClicked())
                    return $this->redirectToRoute('parteportador_index', array(
                        'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                        'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0,
                        'remember'=> 1
                    ));
                else
                    return $this->redirectToRoute('parteportador_guardar', array(
                        'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
                        'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
                    ));
            }
        }

        return $this->render('ParteDiarioBundle:datparteportador:new.html.twig', array(
            'datPartePortador' => $ent,
            'form' => $form->createView(),
            'action' => 'Adicionar',
            'fecha' => $params['fecha'] != 0 ? $fechaAux->format('d-m-Y') : 0,
            'ueb' => $params['idueb'] != 0 ? $params['idueb'] : 0
        ));
    }

    /**
     * Creates a new DatPartePortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEPORTADORES')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datPartePortador = new DatPartePortador();
        $em = $this->getDoctrine()->getManager();
        $datos = $this->get('request');
        $params['idueb'] = $datos->get('filtro-ueb');
        $params['fecha'] = $datos->get('filtro-dia');
        return $this->newAuxiliar($params, $datPartePortador, $em, $request);
    }

    /**
     * Creates a new DatPartePortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PARTEPORTADORES')")
     */
    public function guardarAction(Request $request, $fecha, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datPartePortador = new DatPartePortador();
        $em = $this->getDoctrine()->getManager();
        $params['idueb'] = $ueb;
        $params['fecha'] = $fecha;
        return $this->newAuxiliar($params, $datPartePortador, $em, $request);
    }


    /**
     * Displays a form to edit an existing DatPartePortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PARTEPORTADORES')")
     */
    public function editAction(Request $request, DatPartePortador $datPartePortador,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $savedIncidencias = clone $datPartePortador->getIncidencias();
        $savedMedidores = clone $datPartePortador->getListMedidor();
        $deleteForm = $this->createDeleteForm($datPartePortador);
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatPartePortadorType', $datPartePortador);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->beginTransaction();
                try {
                    // Se eliminan los medidores que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedMedidores as $savedMedidor) {
                        if ($datPartePortador->getListMedidor()->contains($savedMedidor) === false) {
                            $em->remove($savedMedidor);
                        }
                    }
                    // Se eliminan las incidencias que están guardados en la base de datos
                    // y fueron quitados en la vista
                    foreach ($savedIncidencias as $savedIncidencia) {
                        if ($datPartePortador->getIncidencias()->contains($savedIncidencia) === false) {
                            $em->remove($savedIncidencia);
                        }
                    }
                    $datPartePortador->setConsumo($this->calculateConsumoTotal($datPartePortador));
                    $em->persist($datPartePortador);
                    $em->flush();
                    $em->commit();

                    return $this->redirectToRoute('parteportador_index',array(
                        'fecha'=>$fecha,
                        'remember'=> 1,
                        'ueb'=>$ueb
                    ));
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }

        return $this->render('ParteDiarioBundle:datparteportador:new.html.twig', array(
            'datPartePortador' => $datPartePortador,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'action' => 'Editar',
            'fecha'=>$fecha,
            'ueb'=>$ueb
        ));
    }

    /**
     * Deletes a DatPartePortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEPORTADORES')")
     */
    public function deleteAction(Request $request, DatPartePortador $datPartePortador)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datPartePortador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datPartePortador);
            $em->flush();
        }

        return $this->redirectToRoute('parteportador_index');
    }

    /**
     * Creates a form to delete a DatPartePortador entity.
     *
     * @param DatPartePortador $datPartePortador The DatPartePortador entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEPORTADORES')")
     */
    private function createDeleteForm(DatPartePortador $datPartePortador)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parteportador_delete', array('id' => $datPartePortador->getIdparte())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PARTEPORTADORES')")
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
            $objElim = $em->getRepository('ParteDiarioBundle:DatPartePortador')->find($f);
            $em->remove($objElim);
            $em->flush();
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PARTEPORTADORES')")
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
                if ($sp[0] == 'portador') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND mp.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'um') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND ( u.abreviatura LIKE \'%' . $sp[1] . '%\' OR u.alias LIKE \'%' . $alias . '%\')';
                } else if ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND eu.alias LIKE \'%' . $alias . '%\'';
                } else if ($sp[0] == 'consumo') {
                    $where .= ' AND pp.consumo = ' . $sp[1];
                } else if ($sp[0] == 'pico') {
                    $where .= ' AND pp.pico = ' . $sp[1];
                } else if ($sp[0] == 'madrugada') {
                    $where .= ' AND pp.madrugada = ' . $sp[1];
                } else if ($sp[0] == 'alcance') {
                    $where .= ' AND pp.alcance = ' . $sp[1];
                } else if ($sp[0] == 'inventario') {
                    $where .= ' AND pp.inventario = ' . $sp[1];
                } else if ($sp[0] == 'entrada') {
                    $where .= ' AND pp.entrada = ' . $sp[1];
                } else if ($sp[0] == 'existencia') {
                    $where .= ' AND pp.existencia = ' . $sp[1];
                }
            }
        }
        $where .= ($dat[0] != 0) ? (' and eu = ' . $dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and pp.fecha = ' . "'" . date_format(date_create_from_format('d/m/Y', $dat[1]), 'Y-m-d') . "'") : '';

        $dql = 'SELECT pp
            FROM ParteDiarioBundle:DatPartePortador pp
            JOIN pp.ueb eu
            JOIN pp.portador mp
            JOIN pp.um u
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
            $ma = $a->getPortador();
            $res[] = $ma->getNombre();
            $um = $a->getUm();
            $res[] = $um->getAbreviatura();
            $res[] = $a->getConsumo() != null ? $a->getConsumo() : '-';
            $res[] = $a->getInventario() != null ? $a->getInventario() : '-';
            $res[] = $a->getExistencia() != null ? $a->getExistencia() : '-';
            $parte[] = $res;
        }
        if ($total > 0) {
            return new JsonResponse(array('data' => $parte, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    private function calculateConsumoTotal($parte)
    {
        $total = 0;
        foreach ($parte->getListMedidor() as $medidor) {
            $total += $medidor->getConsumo();
        }
        return $total;
    }

    public function findLastParteAction(Request $request)
    {
        $idPortador = $request->request->get('idPortador');
        $idUeb = $request->request->get('idUeb');
        $idMedidor = $request->request->get('idMedidor');
        $fecha = $request->request->get('fecha');

        $fechaSearch = explode('/', $fecha);
        $newF = new \DateTime($fechaSearch[2] . '-' . $fechaSearch[1] . '-' . $fechaSearch[0]);

        $lectura = 0;

        $last = $this->getDoctrine()->getRepository('ParteDiarioBundle:DatPartePortador')->findLastMedidorByLastParte($idUeb, $idPortador, $idMedidor, $newF->format('Y-m-d'));
        if ($last) {
            $lectura = $last->getLectura();
        }

        return new JsonResponse($lectura);
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PARTEPORTADORES')")
     */
    public function detalleParteAction(Request $request, DatPartePortador $parte,$fecha,$ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('ParteDiarioBundle:datparteportador:detalle_parte.html.twig', array(
            'parte' => $parte,
            'fecha'=>$fecha,
            'ueb'=>$ueb
        ));
    }

}
