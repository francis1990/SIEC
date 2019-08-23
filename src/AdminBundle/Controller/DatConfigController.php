<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 02/03/2016
 * Time: 09:58
 */

namespace AdminBundle\Controller;

use AdminBundle\Entity\DatConfig;
use AdminBundle\Entity\Usuario;
use AdminBundle\Form\UsuarioType;
use NomencladorBundle\Util\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class DatConfigController extends Controller
{
    /**
     * @Security("is_granted('ROLE_LISTAR_ADMINENTIDAD')")
     */
    public function indexAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $datos = $em->getRepository('AdminBundle:DatConfig')->findAll();
        return $this->render('AdminBundle:datconfig:index.html.twig', array("datos" => $datos));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ADMINENTIDAD')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $dql = 'SELECT g FROM AdminBundle:DatConfig g';
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $nom = $consulta->getResult();
        $for = array();

        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getId();
            $res[] = $pr->getReupEntidad();
            $res[] = $pr->getNombreEntidad();
            $res[] = $pr->getDireccion();
            $for[] = $res;
        }
        if ($total > 0) {
            return new JsonResponse(array('data' => $for, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_ADMINENTIDAD')")
     */
    public function newAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $sesion = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $datconfig = new DatConfig();
        $form = $this->createForm('AdminBundle\Form\DatConfigEntidadType', $datconfig);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fecha = new DateTime();
            $datconfig->setFechaTrabajo($fecha);
            $em->persist($datconfig);
            $em->flush();
            $sesion->set('config', $datconfig);
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('config_general');
            else
                return $this->redirectToRoute('config_entidad_new');
        }
        return $this->render('AdminBundle:datconfig:new.html.twig', array(
            'form' => $form->createView(),
            "accion" => "Adicionar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_ADMINENTIDAD')")
     */
    public function editarAction(Request $request, DatConfig $datconfig)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $sesion = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('AdminBundle\Form\DatConfigEntidadType', $datconfig);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($datconfig);
            $em->flush();
            $sesion->set('config', $datconfig);
            return $this->redirectToRoute('config_general');
        }
        return $this->render('AdminBundle:datconfig:new.html.twig', array(
            'form' => $form->createView(),
            "accion" => "Editar"
        ));
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_ADMINENTIDAD')")
     */
    public function deleteAction($datos)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $datconfig = $em->getRepository('AdminBundle:DatConfig')->find(1);
        if ($datconfig == null) {
            $datconfig = new DatConfig();

        } else {
            if (!empty($datos)) {
                if ($datos == 'fecha')
                    $datconfig->setFechaTrabajo(null);
                else {
                    $datconfig->setNombreEntidad('');
                    $datconfig->setDireccion('');
                    $datconfig->setReupEntidad('');
                }
                $em->persist($datconfig);
                $em->flush();
                return new JsonResponse(0);
            }
        }
        return new JsonResponse(1);

    }

    public function setUebFiltroAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $u = $request->request->get('ueb');
        if ($u != 0) {
            $ueb = $em->getRepository('NomencladorBundle:NomUeb')->find($u);
            $session->set('ueb', $ueb);
        } else
            $session->remove('ueb');
        return new JsonResponse(0);
    }

    public function setFechaFiltroAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $d = $request->request->get('fecha');
        $session->set('fecha_actual', date_create_from_format('d/m/Y', $d));
        return new JsonResponse(0);
    }

    public function newFechaAction(Request $request)
    {
        $sesion = $request->getSession();
        $fec = $request->request->get('fecha');
        $enviado = $request->request->get('enviado');
        $em = $this->getDoctrine()->getManager();
        $datconfig = $em->getRepository('AdminBundle:DatConfig')->find(1);
        if ($datconfig == null) {
            $datconfig = new DatConfig();
        }
        $form = $this->createForm('AdminBundle\Form\DatConfigFechaType', $datconfig);
        $form->handleRequest($request);
        if (!empty($enviado)) {
            $dat = new DateTime($fec);
            $datconfig->setFechaTrabajo($dat);
            $em->persist($datconfig);
            $em->flush();
            $fec = $datconfig->getFechaTrabajo()->format('d/m/Y');
            $a = ['id' => $datconfig->getId(), 'fecha' => $fec];
            $sesion->set('config', $datconfig);
            return new JsonResponse(array('respuesta' => $a));
        }
        return $this->render('AdminBundle:datconfig:index.html.twig', array(
            'config' => $datconfig,
            'form' => $form->createView(),
        ));
    }


}