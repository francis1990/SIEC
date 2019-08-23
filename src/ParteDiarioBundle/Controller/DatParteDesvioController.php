<?php

namespace ParteDiarioBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatParteDesvio;
use ParteDiarioBundle\Form\DatParteDesvioType;
/*No notificar errores*/
error_reporting(0);

/**
 * DatParteDesvio controller.
 *
 */
class DatParteDesvioController extends Controller
{
    /**
     * Lists all DatParteDesvio entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $desvio = new DatParteDesvio();
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteDesvioType', $desvio);
        $datParte = $em->getRepository('ParteDiarioBundle:DatParteDesvio')->findAll();
        return $this->render('ParteDiarioBundle:datpartedesvio:index.html.twig', array(
            'datParteDesvios' => $datParte,
            'form' => $form->createView(),
        ));
    }

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
                if ($sp[0] == 'um') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND ( um.abreviatura LIKE \'%' . $sp[1] . '%\' OR um.alias LIKE \'%' . $alias . '%\')';
                } else if ($sp[0] == 'cantidad') {
                    $where .= ' AND a.cantidad LIKE \'%' . $sp[1] . '%\'';
                } else if ($sp[0] == 'destino') {
                    $where .= ' AND (e.nombre LIKE \'%' . $sp[1] . '%\' OR ue.nombre LIKE \'%' . $sp[1] . '%\')';
                }else if ($sp[0] == 'ueb') {
                    $where .= ' AND (ueb.nombre LIKE \'%' . $sp[1] . '%\')';
                }
            }
        }
        /*  if($sesion->has('ueb'))
              $where.=' And ue ='.$sesion->get('ueb')->getIdueb();*/
        $where .= ($dat[0] != 0) ? (' and a.ueb = '.$dat[0]) : '';
        $where .= ($dat[1] != '') ? (' and a.fecha = '."'". date_format(date_create_from_format('d/m/Y', $dat[1]), 'Y-m-d')."'") : '';
        /* if ($sesion->has('fecha_actual'))
             $where .= ' And a.fecha =' . "'" . date_format($sesion->get('fecha_actual'), 'Y-m-d') . "'";*/
        $dql = 'SELECT a,p,um
            FROM ParteDiarioBundle:DatParteDesvio a           
            JOIN a.producto p
            JOIN a.um um
            join a.ueb ueb
            WHERE ' . $where;
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $desvios = $consulta->getResult();
        $parte = array();
        foreach ($desvios as $a) {
            $res = array();
            $res[] = $a->getIdparte();
            $res[] = $a->getProducto()->getNombre();
            $valor = $a->getDestino();
            if ($a->getTipo() == 0) {
                $ent = $em->getRepository('NomencladorBundle:NomEntidad')->find($valor);
            } else {
                $ent = $em->getRepository('NomencladorBundle:NomUeb')->find($valor);
            }
            $res[] = $ent->getNombre();/*($ueb==null)? $ent->getNombre(): $ueb->getNombre();*/
            $res[] = $a->getUm()->getAbreviatura();
            $res[] = $a->getCantidad();
            $fec =$a->getFecha();
            $res[] =$fec->format('d/m/Y');
            $res[] = $a->getUeb()->getNombre();
            $parte[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $parte, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * Creates a new DatParteDesvio entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entsuebs = $em->getRepository('NomencladorBundle:NomEntidad')->getEntidadesTiposAcopioyUEB();
        $datParteDesvio = new DatParteDesvio();
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteDesvioType', $datParteDesvio, array('valores' => $entsuebs));
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($datParteDesvio);
                $em->flush();

                if ($form->get('aceptar')->isClicked())
                    return $this->redirectToRoute('partedesvio_index');
                else
                    return $this->redirectToRoute('partedesvio_new');
            }
        }
//        $enviado = $request->request->get('enviado');
//        if (!empty($enviado)) {
//            $parte = $request->request->get('partediario');
//            $em = $this->getDoctrine()->getManager();
//            $pro = $em->getRepository('NomencladorBundle:NomProducto')->find($parte['producto']);
//            $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($parte['um']);
//            $ueb = $em->getRepository('NomencladorBundle:NomUeb')->find($parte['ueb']);
//            $datParteDesvio->setFecha(date_create_from_format('d/m/Y', $parte['fecha']));
//            $datParteDesvio->setCantidad($parte['cantidad']);
//            $datParteDesvio->setUm($um);
//            $datParteDesvio->setUeb($ueb);
//            $datParteDesvio->setProducto($pro);
//            $datParteDesvio->setDestino($parte['destino']);
//            $datParteDesvio->setTipo($parte['tipo']);
//            $em->persist($datParteDesvio);
//            $em->flush();
//            return new JsonResponse("0");
//        }
        return $this->render('ParteDiarioBundle:datpartedesvio:new.html.twig', array(
            'datParte' => $datParteDesvio,
            'form' => $form->createView(),
            'action' => 'Adicionar',
        ));
    }

    /**
     * Finds and displays a DatParteDesvio entity.
     *
     */
    public function showAction(DatParteDesvio $datParteDesvio)
    {
        $deleteForm = $this->createDeleteForm($datParteDesvio);
        return $this->render('ParteDiarioBundle:datpartedesvio:show.html.twig', array(
            'datParteDesvio' => $datParteDesvio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DatParteDesvio entity.
     *
     */
    public function editAction(Request $request,DatParteDesvio $datparte)
    {
        $savedIncidencias = clone $datparte->getIncidencias();
        $em = $this->getDoctrine()->getManager();
        $entsuebs = $em->getRepository('NomencladorBundle:NomEntidad')->getEntidadesTiposAcopioyUEB();
        $form = $this->createForm('ParteDiarioBundle\Form\DatParteDesvioType', $datparte, array('valores' => $entsuebs));
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

                    return $this->redirectToRoute('partedesvio_index');
                } catch (\Exception $ex) {
                    $em->rollback();
                }
            }
        }



//        $enviado = $request->request->get('enviado');
//        if (!empty($enviado)) {
//            $parte = $request->request->get('partediario');
//
//            $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($parte['um']);
//            $pro = $em->getRepository('NomencladorBundle:NomProducto')->find($parte['producto']);
//            $datparte->setFecha(date_create_from_format('d/m/Y', $parte['fecha']));
//            $datparte->setProducto($pro);
//            $datparte->setUm($um);
//            $datparte->setUeb($parte['ueb']);
//            $datparte->setDestino($parte['destino']);
//            $datparte->setCantidad($parte['cantidad']);
//            $datparte->setTipo($parte['tipo']);
//            $em->flush();
//            return new JsonResponse("0");
//        }
        return $this->render('ParteDiarioBundle:datpartedesvio:new.html.twig', array(
            'datParte' => $datparte,
            'form' => $form->createView(),
            'action' => 'Editar',
        ));
    }

    /**
     * Deletes a DatParteDesvio entity.
     *
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $msg = 'exito';
        $ids = $request->request->get('id');
        if (!is_array($ids))
            $ids = [$ids];
        foreach ($ids as $f) {
            try {
                $consulta = $em->createQuery('
                        DELETE  FROM ParteDiarioBundle:DatParteDesvio cp
                         WHERE cp.idparte = ' . $f . '');
                $consulta->execute();
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * Creates a form to delete a DatParteDesvio entity.
     *
     * @param DatParteDesvio $datParteDesvio The DatParteDesvio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DatParteDesvio $datParteDesvio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('partedesvio_delete', array('id' => $datParteDesvio->getIdparte())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
