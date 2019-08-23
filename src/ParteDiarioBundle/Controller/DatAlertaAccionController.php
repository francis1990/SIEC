<?php

namespace ParteDiarioBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ParteDiarioBundle\Entity\DatAlertaAccion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * DatAlertaAccion controller.
 *
 * @Route("/datalertaaccion")
 */
class DatAlertaAccionController extends Controller
{
    /**
     * Lists all DatAlertaAccion entities.
     *
     * @Route("/{accion}", name="datalertaaccion_index")
     * @Method("GET")
     */
    public function indexAction($accion)
    {
        $em = $this->getDoctrine()->getManager();

        $datAlertaAccions = $em->getRepository('ParteDiarioBundle:DatAlertaAccion')->findAll();

        return $this->render('ParteDiarioBundle:datalerta:accionindex.html.twig', array(
            'datAlertaAccions' => $datAlertaAccions,
            'title'=>$accion==1? 'Suprimida':'Aceptada',
            'accion'=>$accion
        ));
    }



}
