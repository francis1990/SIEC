<?php

namespace NomencladorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use NomencladorBundle\Entity\NomTipoPortador;
use NomencladorBundle\Form\NomTipoPortadorType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
/*No notificar errores*/
error_reporting(0);

/**
 * NomTipoPortador controller.
 *
 */
class NomTipoPortadorController extends Controller
{
    /**
     * Lists all NomTipoPortador entities.
     *
     */
    public function indexAction($remember)
    {
        $em = $this->getDoctrine()->getManager();

        $nomTipoPortadors = $em->getRepository('NomencladorBundle:NomTipoPortador')->findAll();

        return $this->render('NomencladorBundle:nomtipoportador/index.html.twig', array(
            'nomResults' => $nomTipoPortadors,
            'remember'=>$remember
        ));
    }

    /**
     * Creates a new NomTipoPortador entity.
     *
     */
    public function newAction(Request $request)
    {

        $nomTipoPortador = new NomTipoPortador();
        $form = $this->createForm('NomencladorBundle\Form\NomTipoPortadorType', $nomTipoPortador);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($nomTipoPortador);
            $em->flush();

            return $this->redirectToRoute('tipoportador_index', array('remember'=> 1));
        }
        return $this->render('NomencladorBundle:nomtipoportador/new.html.twig', array(
            'nomTipoPortador' => $nomTipoPortador,
            'form' => $form->createView(),
        ));
    }
    public function insertAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $tipoportadorRe = $request->request->get('tipoportador');
        $Codigo = $em->getRepository('NomencladorBundle:NomTipoPortador')
            ->codigoExits('NomencladorBundle:NomTipoPortador', $tipoportadorRe[0]);
        if ($Codigo == -1) {//no existe
            $tipoportador= new NomTipoPortador();
            $tipoportador->setCodigo($tipoportadorRe[0]);
            $tipoportador->setNombre($tipoportadorRe[1]);
            $em->persist($tipoportador);
            $em->flush();

            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            return new JsonResponse("exito");
        }else{
            return new JsonResponse(array('respuesta'=>$Codigo));
        }

    }
    /**
     * Finds and displays a NomTipoPortador entity.
     *
     */
    public function showAction(NomTipoPortador $nomTipoPortador)
    {

        $deleteForm = $this->createDeleteForm($nomTipoPortador);
        return $this->render('NomencladorBundle:nomtipoportador/show.html.twig', array(
            'nomTipoPortador' => $nomTipoPortador,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing NomTipoPortador entity.
     *
     */
    public function editAction(Request $request, NomTipoPortador $nomTipoPortador)
    {

        $em = $this->getDoctrine()->getManager();
        $val = $em->getRepository('NomencladorBundle:NomTipoPortador')->find($nomTipoPortador->getId());
        $cod = $val->getCodigo();
        $deleteForm = $this->createDeleteForm($nomTipoPortador);
        $editForm = $this->createForm('NomencladorBundle\Form\NomTipoPortadorType', $nomTipoPortador);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $ccCod = $em->getRepository('NomencladorBundle:NomTipoPortador')->codigoCant('NomencladorBundle:NomTipoPortador',$val->getCodigo());
            if (($ccCod > 0 && $cod != $nomTipoPortador->getCodigo())) {
                $this->addFlash('danger', 'Existe un elemento con el mismo código.');
                return $this->redirectToRoute('tipoportador_edit', array('id' => $nomTipoPortador->getId()));
            } else {
                $em = $this->getDoctrine()->getManager();
                $em->persist($nomTipoPortador);
                $em->flush();            
            }

            return $this->redirectToRoute('tipoportador_index', array('remember'=> 1));
        }
        return $this->render('NomencladorBundle:nomtipoportador/new.html.twig', array(
            'nomTipoPortador' => $nomTipoPortador,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a NomTipoPortador entity.
     *
     */
    public function deleteAction(Request $request, NomTipoPortador $nomTipoPortador)
    {

        $form = $this->createDeleteForm($nomTipoPortador);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nomTipoPortador);
            $em->flush();
        }
        return $this->redirectToRoute('tipoportador_index');
    }

    /**
     * Creates a form to delete a NomTipoPortador entity.
     *
     * @param NomTipoPortador $nomTipoPortador The NomTipoPortador entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(NomTipoPortador $nomTipoPortador)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipoportador_delete', array('id' => $nomTipoPortador->getIdtipoportador())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    public function eliminarAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $idtipoportador= $request->request->get('idtipoportador');
        $tipoportador =  $em->getRepository('NomencladorBundle:NomTipoPortador')->find($idtipoportador);
        $em->remove($tipoportador);
        $em->flush();
        return new JsonResponse(array('respuesta'=>'exito'));
    }
    public function deleteSelectAction(Request $request)
    {

        $elemento = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        if ($elemento != '') {
            try {
                $GLOBALS['kernel']->getContainer()->get('parte_diario.comun_service')->deleteOne('NomencladorBundle:NomTipoPortador', $elemento);
                return new JsonResponse(array('respuesta' => 'exito'));
            } catch (ForeignKeyConstraintViolationException $e) {
                return new JsonResponse(array('respuesta' => 'error'));
            }
        } else {
            return new JsonResponse(array('respuesta' => 'error'));
        }
    }
}
