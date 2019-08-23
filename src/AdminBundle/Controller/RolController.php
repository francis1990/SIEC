<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 10/03/2016
 * Time: 12:36
 */

namespace AdminBundle\Controller;

use AdminBundle\Entity\Permiso;
use AdminBundle\Entity\Rol;
use AdminBundle\Form\RolType;
use Doctrine\Common\Util\Debug;
use NomencladorBundle\Util\Util;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class RolController extends Controller
{
    /**
     * @Security("is_granted('ROLE_LISTAR_ROL')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->render('AdminBundle:Rol:listarRol.html.twig', array('remember'=>$remember));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ROL')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $where = '1=1';
        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'descripcion') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND g.descRol LIKE \'%' . $alias . '%\' ';
                }
            }
        }
        $dql = 'SELECT g FROM AdminBundle:Rol g WHERE ' . $where;
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $nom = $consulta->getResult();
        $for = array();

        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getId();
            $res[] = $pr->getDescRol();
            $res[] = $pr->getActivo();
            $for[] = $res;
        }
        if ($total > 0) {
            return new JsonResponse(array('data' => $for, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }


    /**
     * @Security("is_granted('ROLE_ADICIONAR_ROL')")
     */
    public
    function crearAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $rol = new Rol();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('AdminBundle\Form\RolType', $rol);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $datos = $request->request->get('rol');
            $permisosConce = json_decode($datos['listaPermisos']);
            $rol->setActivo(1);
            $em->persist($rol);
            $em->flush();
            if (count($permisosConce) > 0) {
                foreach ($permisosConce as $perm) {
                    $permExist = $em->getRepository('AdminBundle:Permiso')->find($perm);
                    $rol->addPermiso($permExist);
                }
                $em->flush();
            }

            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('admin_rol_lista',array('remember'=> true));
            else
                return $this->redirectToRoute('admin_rol_new');
        } else {
            $permisos = $em->getRepository('AdminBundle:Permiso')->findAll();
            $permisosArray = array();
            foreach ($permisos as $perm) {
                $permisosArray[] = [
                    'id' => $perm->getId(),
                    'descPermiso' => $perm->getDescpermiso()
                ];
            }

            return $this->render('AdminBundle:Rol:crearRol.html.twig',
                array(
                    'permisos' => $permisosArray,
                    'form' => $form->createView(),
                    "accion" => "Adicionar"
                ));
        }
    }


    /**
     * @Security("is_granted('ROLE_MODIFICAR_ROL')")
     */
    public
    function editarAction(Request $request, Rol $rol)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('AdminBundle\Form\RolType', $rol);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $datos = $request->request->get('rol');
            $permisosConce = json_decode($datos['listaPermisos']);
            foreach ($rol->getPermisos() as $permRol) {
                $rol->removePermiso($permRol);
                $em->flush();
            }
            if (count($permisosConce) > 0) {
                foreach ($permisosConce as $perm) {
                    $permExist = $em->getRepository('AdminBundle:Permiso')->find($perm);
                    $rol->addPermiso($permExist);
                }
                $em->flush();
            }
            return $this->redirectToRoute('admin_rol_lista',array('remember'=> true));
        } else {
            $permisosArray = array();
            $permisos = $em->getRepository('AdminBundle:Permiso')->findAll();
            foreach ($permisos as $perm) {
                $permiso = $em->getRepository('AdminBundle:Permiso')->find($perm->getId());
                $relation = $em->getRepository('AdminBundle:Rol')->findRolPermiso($rol->getId(), $perm->getId());
                $permisosArray[] = [
                    'id' => $permiso->getId(),
                    'descPermiso' => $permiso->getDescpermiso(),
                    'relation' => $relation,
                    'alias' => $perm->getAlias()
                ];
            }
            return $this->render('AdminBundle:Rol:crearRol.html.twig',
                array(
                    'permisos' => $permisosArray,
                    'form' => $form->createView(),
                    "accion" => "Editar"
                ));
        }

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_ROL')")
     */
    public
    function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())){
            return $this->redirectToRoute('portada');
        }

        $rolElim = $request->request->get('id');

        $params['campoId'] = "idRol";
        $params['tabla'] = "Rol";
        $params['valor'] = $rolElim;
        $params['bundle'] = "AdminBundle:";
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            $params['nomenclador'] = "los elementos seleccionados.";
        } else if (count($params['valor']) == 1) {
            $params['nomenclador'] = "el elemento seleccionado.";
        }
        $result = $this->get('nomencladores')->eliminarObjEntidad($params);

        if ($result['enUso'] && count($params['valor']) > 1) {
            $result['mensaje'] = 'Se eliminaron solo los elementos que no están en uso. Verifique en el Componente de Usuarios.';
        } else if ($result['enUso'] && count($params['valor']) == 1) {
            $result['mensaje'] = 'No se pudo eliminar el elemento seleccionado ya que está en uso. Verifique en el Componente de Usuarios.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_ROL')")
     */
    public
    function setEstadoAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $user = $this->getUser();
        $elementos = $request->request->get('id');
        $accion = $request->request->get('accion');
        $em = $this->getDoctrine()->getManager();
        $accion == 'btn_activar' ? $estado = 1 : $estado = 0;
        $error = 'exito';
        if (!is_array($elementos))
            $elementos = [$elementos];
        foreach ($elementos as $value) {
            if ($user->getRol()->getId() != $value) {
                $userAct = $em->getRepository('AdminBundle:Rol')->find($value);
                $userAct->setActivo($estado);
                $em->persist($userAct);
            } else {
                $error = 'error';
            }
        }

        $em->flush();
        return new JsonResponse(array('respuesta' => $error));
    }
}