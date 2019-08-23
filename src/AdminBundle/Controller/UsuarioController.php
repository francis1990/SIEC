<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 02/03/2016
 * Time: 09:58
 */

namespace AdminBundle\Controller;


use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use NomencladorBundle\Util\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AdminBundle\Entity\Usuario;
use AdminBundle\Form\UsuarioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;


class UsuarioController extends Controller
{

    /**
     * @Security("is_granted('ROLE_LISTAR_USUARIO')")
     */
    public function indexAction($remember)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        return $this->render('AdminBundle:Usuario:listarUsuario.html.twig', array('remember'=>$remember));
    }

    /**
     * Autenticar usuario.
     *
     */
    public function loginAction()
    {
        $cont = 0;
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
        // obtiene el error de inicio de sesión si lo hay
        if ($peticion->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $peticion->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $sesion->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        $em = $this->getDoctrine()->getManager();
        /*Obtengo el último usuario*/
        $lastUser = $sesion->get(SecurityContext::LAST_USERNAME);

        $user = $this->getDoctrine()->getRepository("AdminBundle:Usuario")->findByUsuario(strtolower($lastUser));

        /*Validar intentos fallidos*/
        if ($error != null) {
            if ($error->getMessage() == "Bad credentials.") {
                if (isset($user[0])) {
                    if (count($user) > 0) {
                        $contador = $user[0]->getContbloqueo() + 1;
                        $user[0]->setContbloqueo($contador);
                        $em->persist($user[0]);
                        $em->flush();
                    }
                }
            }
        }
        return $this->render('AdminBundle:Default:login.html.twig',
            array('last_username' => $sesion->get(SecurityContext::LAST_USERNAME), 'error' => $error)
        );
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_USUARIO')")
     */
    public function listarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
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
                if ($sp[0] == 'usuario') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND a.usuario LIKE \'%' . $alias . '%\' ';
                } elseif ($sp[0] == 'correo') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND a.correo LIKE \'%' . $alias . '%\' ';
                } elseif ($sp[0] == 'ueb') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND u.alias LIKE \'%' . $alias . '%\' ';
                } elseif ($sp[0] == 'rol') {
                    $alias = Util::getSlug($sp[1]);
                    $where .= ' AND x.descRol LIKE \'%' . $alias . '%\' ';
                }
            }
        }
        $dql = 'SELECT a.idUsuario, a.usuario, a.correo, u.nombre AS ueb, x.descRol AS rol, a.activo,a.contbloqueo
            FROM AdminBundle:Usuario a  
            JOIN a.rol x 
            JOIN a.ueb u
            WHERE ' . $where;

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();

        $array = array();


        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr['idUsuario'];
            $res[] = $pr['usuario'];
            $res[] = $pr['correo'];
            $res[] = $pr['ueb'];
            $res[] = $pr['rol'];
            $res[] = $pr['activo'];
            $res[] = $pr['contbloqueo'];
            $array[] = $res;
        }

        if ($total > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }

    }

    /**
     * @Security("is_granted('ROLE_ADICIONAR_USUARIO')")
     */
    public function crearAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $usuario = new Usuario();
        $form = $this->createForm(new UsuarioType(), $usuario);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
            $passwordCodificado = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
            $usuario->setPassword($passwordCodificado);
            //$usuario->setLogueado(false);
            $usuario->setUsuario(strtolower($usuario->getUsuario()));
            $usuario->setContbloqueo(0);
            $em->persist($usuario);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('admin_usuario_lista',array('remember'=> true));
            else
                return $this->redirectToRoute('admin_usuario_new');

        }
        return $this->render('@Admin/Usuario/crearUsuario.html.twig',
            array('form' => $form->createView(), "accion" => "Adicionar"));
    }

    /**
     * @Security("is_granted('ROLE_MODIFICAR_USUARIO')")
     */
    public function editarAction(Request $request, $id)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('AdminBundle:Usuario')->find($id);

        if (!$usuario->getLogueado()) {
            $form = $this->createForm(new UsuarioType(), $usuario);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
                $passwordCodificado = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
                $usuario->setPassword($passwordCodificado);

                $em = $this->getDoctrine()->getManager();
                $em->persist($usuario);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_usuario_lista',array('remember'=> true)));
            }

            return $this->render('AdminBundle:Usuario:crearUsuario.html.twig',
                array(
                    'usuario' => $usuario,
                    'form' => $form->createView(),
                    "accion" => "Editar"
                )
            );
        } else {
            $this->addFlash('warning', 'El usuario no se puede modificar porque está en uso.');
            return $this->redirect($this->generateUrl('admin_usuario_lista'));
        }

    }

    public function setActivoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $request->request->get('id');
        $accion = $request->request->get('accion');
        $accion == 'btn_activar' ? $estado = 1 : $estado = 0;
        $result['enUso'] = false;
        $result['msg'] = "";
        $result['mensaje'] = "";
        foreach ($usuarios as $valueUser) {
            $objUsuario = $em->getRepository('AdminBundle:Usuario')->find($valueUser);
            if (!$objUsuario->getLogueado()) {
                $objUsuario->setActivo($estado);
                $em->persist($objUsuario);
                $em->flush();
            } else {
                $result['enUso'] = true;
            }
        }

        if ($result['enUso'] && count($usuarios) > 1) {
            $result['msg'] = 'error';
            $result['mensaje'] = 'Se modificaron solo los Usuarios que no están en uso.';
        } else if ($result['enUso'] && count($usuarios) == 1) {
            $result['msg'] = 'error';
            $result['mensaje'] = 'No se pudo modificar el Usuario seleccionado ya que está en uso.';
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }

    public function desbloquearUsuarioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $request->request->get('id');
        foreach ($usuarios as $valueUser) {
            $objUsuario = $em->getRepository('AdminBundle:Usuario')->find($valueUser);
            $objUsuario->setContbloqueo(0);
            $em->persist($objUsuario);
            $em->flush();
        }

        return new JsonResponse(array('success' => true));
    }

}