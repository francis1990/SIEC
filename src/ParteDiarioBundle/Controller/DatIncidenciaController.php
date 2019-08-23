<?php

namespace ParteDiarioBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * DatIncidencia controller.
 *
 */
class DatIncidenciaController extends Controller
{
    /**
     * Lists all DatIncidencia entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_INCIDENCIA')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $datIncidencias = $em->getRepository('ParteDiarioBundle:DatIncidencia')->findAll();

        return $this->render('ParteDiarioBundle:datincidencia:index.html.twig', array(
            'datIncidencias' => $datIncidencias,
            'remember'=> $remember
        ));
    }

    /**
     * Creates a new DatIncidencia entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_INCIDENCIA')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $em = $this->getDoctrine()->getManager();
        $datIncidencium = new DatIncidencia();
        $hoy = date_create('now');
        $datIncidencium->setFecha($hoy);

        $form = $this->createForm('ParteDiarioBundle\Form\DatIncidenciaType', $datIncidencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() ) {

            $em->persist($datIncidencium);
            $em->flush();

            return $this->redirectToRoute('incidencia_index',array( 'remember'=> 1));
        }

        return $this->render('ParteDiarioBundle:datincidencia:new.html.twig', array(
            'datIncidencium' => $datIncidencium,
            'form' => $form->createView(),
            'accion' => 'Adicionar'
        ));
    }

    /**
     * Finds and displays a DatIncidencia entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_INCIDENCIA')")
     */
    public function showAction(DatIncidencia $datIncidencium)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $deleteForm = $this->createDeleteForm($datIncidencium);

        return $this->render('ParteDiarioBundle:datincidencia:show.html.twig', array(
            'datIncidencium' => $datIncidencium,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DatIncidencia entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_INCIDENCIA')")
     */
    public function editAction(Request $request, DatIncidencia $datIncidencium)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        if (!$datIncidencium->getParte()) {
            $deleteForm = $this->createDeleteForm($datIncidencium);
            $editForm = $this->createForm('ParteDiarioBundle\Form\DatIncidenciaType', $datIncidencium);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($datIncidencium);
                $em->flush();

                return $this->redirectToRoute('incidencia_index',array( 'remember'=> 1));
            }

            return $this->render('ParteDiarioBundle:datincidencia:new.html.twig', array(
                'datIncidencium' => $datIncidencium,
                'form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'accion' => 'Editar'
            ));
        }
        $this->addFlash('danger', 'No es posible modificar la incidencia.');

        return $this->redirectToRoute('incidencia_index');
    }

    /**
     * Deletes a DatIncidencia entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_INCIDENCIA')")
     */
    public function deleteAction(Request $request, DatIncidencia $datIncidencium)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        if (!$datIncidencium->getParte()) {
            $form = $this->createDeleteForm($datIncidencium);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($datIncidencium);
                $em->flush();
            }
        } else
            $this->addFlash('danger', 'No es posible eliminar la incidencia.');

        return $this->redirectToRoute('incidencia_index');
    }

    /**
     * Creates a form to delete a DatIncidencia entity.
     *
     * @param DatIncidencia $datIncidencium The DatIncidencia entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_INCIDENCIA')")
     */
    private function createDeleteForm(DatIncidencia $datIncidencium)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('incidencia_delete', array('id' => $datIncidencium->getIdincidencia())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_INCIDENCIA')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $msg = 'exito';
        $ids = $request->request->get('id');
        if (!is_array($ids))
            $ids = [$ids];
        if(count($ids) > 0){
            $mensaje = "Se eliminaron satisfactoriamente los elementos seleccionados.";
        } else {
            $mensaje = "Se eliminó satisfactoriamente el elemento seleccionado.";
        }

        foreach ($ids as $f) {
            try {
                $objElim = $em->getRepository('ParteDiarioBundle:DatIncidencia')->find($f);
                if ($objElim->getParte() == null) {
                    $em->remove($objElim);
                    $em->flush();
                } else {
                    $msg = 'error';
                }
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        if($msg == "error"){
            if(count($ids) > 1){
                $mensaje = "Las incidencias asociadas a partes, deben ser eliminadas desde sus respectivos partes.";
            } else {
                $mensaje = "La incidencia está asociada a un parte, debe ser eliminada desde su parte.";
            }
        }
        return new JsonResponse(array('respuesta' => $msg,'mensaje' => $mensaje));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_INCIDENCIA')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $where = '1=1';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'clasificacion') {
                    $where .= 'AND i.nombre LIKE \'%' . $sp[1] . '%\'';
                } elseif ($sp[0] == 'tipo') {
                    $where .= 'AND t.nombre LIKE \'%' . $sp[1] . '%\'';
                } elseif ($sp[0] == 'entidad') {
                    $where .= 'AND e.nombre LIKE \'%' . $sp[1] . '%\'';
                } else {
                    $where .= 'AND a.' . $sp[0] . ' LIKE \'%' . $sp[1] . '%\'';
                }
            }
        }
        $dql = 'SELECT a, e, p FROM ParteDiarioBundle:DatIncidencia a JOIN a.idcasificacion i JOIN a.idtipo t JOIN a.entidad e LEFT JOIN a.parte p
            WHERE ' . $where;
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();

        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getidincidencia();
            $res[] = $pr->getentidad()->getnombre();
            $fecha = $pr->getfecha();
            $res[] = $fecha->format("d/m/Y");
            $res[] = $pr->getidcasificacion()->getNombre();
            $res[] = $pr->getidtipo()->getNombre();
            $res[] = $pr->getdescripcion();
            $res[] = $pr->getParte() != null ? true : false;
            $parte = false;
            if ($pr->getparte())
                $parte = true;
            $res[] = $parte;
            $array[] = $res;
        }
        if ($total > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }
}
