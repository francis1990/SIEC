<?php

namespace ReporteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ReporteBundle\Entity\DatPeriodos;
use ReporteBundle\Form\DatPeriodosType;

/**
 * DatPeriodos controller.
 *
 * @Route("/datperiodos")
 */
class DatPeriodosController extends Controller
{
    /**
     * Lists all DatPeriodos entities.
     *
     * @Route("/listar", name="datperiodos_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $datPeriodos = $em->getRepository('ReporteBundle:DatPeriodos')->findAll();

        return $this->render('ReporteBundle:datperiodos:index.html.twig', array(
            'datPeriodos' => $datPeriodos,
        ));
    }

    /**
     * Creates a new DatPeriodos entity.
     *
     * @Route("/nuevo", name="datperiodos_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $datPeriodo = new DatPeriodos();
        $form = $this->createForm('ReporteBundle\Form\DatPeriodosType', $datPeriodo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datPeriodo->setActivo(true);
            $datPeriodo->setDiaiv(1);
            $datPeriodo->setMesiv(1);
            $datPeriodo->setAnoiv(1);
            $datPeriodo->setDiafv(1);
            $datPeriodo->setMesfv(1);
            $datPeriodo->setAnofv(1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($datPeriodo);
            $em->flush();
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('datperiodos_index');
            else
                return $this->redirectToRoute('datperiodos_new');
        }

        return $this->render('ReporteBundle:datperiodos:new.html.twig', array(
            'datPeriodo' => $datPeriodo,
            'form' => $form->createView(),
            'accion'=>'Adicionar'
        ));
    }

    /**
     * Finds and displays a DatPeriodos entity.
     *
     * @Route("/mostrar/{id}", name="datperiodos_show")
     * @Method("GET")
     */
    public function showAction(DatPeriodos $datPeriodo)
    {
        $deleteForm = $this->createDeleteForm($datPeriodo);

        return $this->render('ReporteBundle:datperiodos:show.html.twig', array(
            'datPeriodo' => $datPeriodo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DatPeriodos entity.
     *
     * @Route("/edit/{id}", name="datperiodos_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DatPeriodos $datPeriodo)
    {
        $editForm = $this->createForm('ReporteBundle\Form\DatPeriodosType', $datPeriodo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($datPeriodo);
            $em->flush();

            return $this->redirectToRoute('datperiodos_edit', array('id' => $datPeriodo->getId()));
        }

        return $this->render('ReporteBundle:datperiodos:new.html.twig', array(
            'datPeriodo' => $datPeriodo,
            'form' => $editForm->createView(),
            'accion'=>'Editar'
        ));
    }

    /**
     * Deletes a DatPeriodos entity.
     *
     * @Route("/eliminar/{id}", name="datperiodos_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, DatPeriodos $datPeriodo)
    {
        $form = $this->createDeleteForm($datPeriodo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datPeriodo);
            $em->flush();
        }

        return $this->redirectToRoute('datperiodos_index');
    }

    /**
     * Creates a form to delete a DatPeriodos entity.
     *
     * @param DatPeriodos $datPeriodo The DatPeriodos entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DatPeriodos $datPeriodo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('datperiodos_delete', array('id' => $datPeriodo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
