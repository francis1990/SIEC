<?php

namespace NomencladorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function nomencladores_indexAction()
    {
        return $this->render('NomencladorBundle:Default:nom_base.html.twig');
    }

    public function delete_allAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $error = false;

        $params = $request->request->get('params');

        return new JsonResponse(array('respuesta' => 'exito'));
    }


}
