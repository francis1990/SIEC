<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Empresa;
use AdminBundle\Entity\Traza;
use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class DefaultController extends Controller
{
    public function loginAction()
    {
        return $this->render('AdminBundle:Default:login.html.twig');
    }

    public function menuAction()
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager();
        $this->cargarCnfGralAction($session, $em);
        return $this->render('AdminBundle:Default:portada.html.twig');
    }


    public function inicioAction()
    {
        return $this->render('AdminBundle:Default:admin.html.twig');
    }

    public function configAction()
    {

        $em = $this->getDoctrine()->getManager();

        $empresa = $em->getRepository('AdminBundle:Empresa')->find(1);

        if ($empresa != null) {
            $form = $this->createForm(new EmpresaType(), $empresa);
            $form->setData($empresa);

            $peticion = $this->getRequest();

            if ($peticion->getMethod() == 'POST') {
                $form->handleRequest($peticion);

                if ($form->isValid()) {
                    $em->persist($empresa);
                    $em->flush();

                    $this->addFlash('success', 'Se ha insertado el elemento correctamente.');

                    $this->forward('AdminBundle:Admin:registrarTraza', array(
                            'string' => "Se configuraron los parámetros generales del sistema",
                        )
                    );
                }
            }

            return $this->render('AdminBundle:Default:config.html.twig',
                array(
                    'formNew' => $form->createView(),
                ));
        } else {
            $empresa = new Empresa();
            $form = $this->createForm(new EmpresaType(), $empresa);

            $peticion = $this->getRequest();

            if ($peticion->getMethod() == 'POST') {
                $form->handleRequest($peticion);

                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($empresa);
                    $em->flush();

                    $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
                }
            }

            return $this->render('AdminBundle:Default:config.html.twig',
                array(
                    'formNew' => $form->createView(),
                ));
        }
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_HISTORIAL')")
     */
    public function indexAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $trazas = $em->getRepository('AdminBundle:Traza')->findAll();
        return $this->render('AdminBundle:Default:traza.html.twig', array('trazas' => $trazas));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_HISTORIAL')")
     */
    public function trazaAction(Request $request)
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
                if ($sp[0] == 'descripcion') {
                    $where .= ' AND g.descTraza LIKE \'%' . $sp[1] . '%\'';
                } elseif ($sp[0] == 'usuario') {
                    $where .= ' AND us.usuario LIKE \'%' . $sp[1] . '%\'';
                } elseif ($sp[0] == 'ueb') {
                    $where .= ' AND u.alias LIKE \'%' . $sp[1] . '%\'';
                }
            }
        }
        $dql = 'SELECT g FROM AdminBundle:Traza g
            JOIN g.usuario us
            JOIN g.ueb u
            WHERE ' . $where . ' ORDER BY g.fechaCreacion,us.usuario, u.alias ';

        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $for = array();

        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr->getId();
            $res[] = $pr->getDescTraza();
            $res[] = $pr->getUsuario()->getUsuario();
            $res[] = $pr->getUeb()->getNombre();
            $res[] = $pr->getFechaCreacion()->format('Y-m-d H:i:s');
            $for[] = $res;
        }

        if ($total > 0) {
            return new JsonResponse(array('data' => $for, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    public function registrarTrazaAction($string)
    {

        $em = $this->getDoctrine()->getManager();
        $registro = new Traza();
        $registro->setUsuario($this->getUser());
        $registro->setUeb($this->getUser()->getUeb());
        $registro->setDescTraza($string);
        $registro->setFechaCreacion(new \DateTime());

        $em->persist($registro);
        $em->flush();

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_HISTORIAL')")
     */
    public function eliminarTodasTrazaAction()
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $dql = 'DELETE FROM AdminBundle:Traza ';
        $consulta = $em->createQuery($dql);
        $consulta->execute();

        return $this->redirect($this->generateUrl('admin_traza_index'));
    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_HISTORIAL')")
     */
    public function eliminarTrazaAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if(!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $trazas = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        if (!is_array($trazas))
            $trazas = [$trazas];

        foreach ($trazas as $valueObj) {
            $objTraza = $em->getRepository('AdminBundle:Traza')->find($valueObj);
            $em->remove($objTraza);
            $em->flush();
        }
        $result['msg'] = 'exito';
        if (count($trazas) > 1) {
            $result['mensaje'] = "Se han eliminado los elementos seleccionados.";
        } else if (count($trazas) == 1) {
            $result['mensaje'] = "se ha eliminado el elemento seleccionado.";
        }

        return new JsonResponse(array('respuesta' => $result['msg'], 'mensaje' => $result['mensaje']));
    }


    /**
     * @Security("is_granted('ROLE_SALVAR_BD')")
     */
    public function salvaBDAction()
    {
        $filename = "coppelia-db_" . date("d-m-Y") . ".sql";
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: binary");
        header("Content-type: application/force-download");
        header("Content-Disposition: attachment; filename=$filename");


        $usuario = "root";
        $passwd = "";
        $bd = "db_coppelia";
        $dirRaiz = $_SERVER['DOCUMENT_ROOT'];
        $executa = $dirRaiz."\coppelia\src\AdminBundle\Controller\mysqldump.exe -u $usuario --password=$passwd --opt $bd";
        system($executa, $resultado);
        exit(0);


//        if ($resultado) { echo "<H1>Error ejecutando comando: $executa</H1>\n"; }
    }

    public function listaCompletaAction(Request $request)
    {
        $entidad = $request->request->get('entidad');

        $em = $this->getDoctrine()->getManager();

        $listado = $em->getRepository('AdminBundle:' . $entidad)->findAll();

        $arrayResult = array();

        foreach ($listado as $obj)
            $arrayResult[] = $obj->getId();

        return new JsonResponse($arrayResult);
    }

    public function importacionAction()
    {

        return $this->render('AdminBundle:Default:importacion.html.twig');

    }

    public function importarFicheroAction()
    {

        return $this->render('AdminBundle:Default:importacion.html.twig');

    }

    public function exportarAction(Request $request)
    {
        ini_set('memory_limit', '3072M');
        set_time_limit(3600);
        $arrayIds = json_decode($request->request->get('arregloDatos')); //convertir a json lo q viene por la vista

        $entidad = $request->request->get('entidad');

        $em = $this->getDoctrine()->getManager();

        $empresa = $em->getRepository('AdminBundle:Empresa')->find(1);

        $forma_exportar = $request->request->get('forma');

        if ($forma_exportar == 'PDF')
            return $this->exportPdfAction($arrayIds, $entidad, $empresa);

        if ($forma_exportar == 'Excel')
            return $this->exportExcelAction($arrayIds, $entidad, $empresa);

        return new JsonResponse('fracaso');
    }

    //METODO PARA EXPORTAR A EXCEL
    public function exportExcelAction($arrayIds, $entidad, $empresa)
    {

        $arrayDatos = array();
        $i = 0;
        foreach ($arrayIds as $id) {
            $elemento = $this->getDoctrine()->getRepository("AdminBundle:$entidad")->find($id);

            $arrayDatos[$i] = $elemento;
            $i++;
        }

        header("Content-Disposition: attachment; filename=$entidad.xls");

        if ($entidad == 'Traza') {

            return $this->render('AdminBundle:Reportes:exportarTrazas.html.twig', array(
                'elementos' => $arrayDatos,
                'empresa' => $empresa,
            ));
        } elseif ($entidad == 'Usuario') {

            return $this->render('AdminBundle:Reportes:exportarUsuarios.html.twig', array(
                'elementos' => $arrayDatos,
                'empresa' => $empresa,
            ));
        } elseif ($entidad == 'Rol') {

            return $this->render('AdminBundle:Reportes:exportarRoles.html.twig', array(
                'elementos' => $arrayDatos,
                'empresa' => $empresa,
            ));
        } else
            return false;
    }

    //METODO PARA EXPORTAR A PDF
    public function exportPdfAction($arrayIds, $entidad, $empresa)
    {
        $arrayDatos = array();
        $i = 0;
        foreach ($arrayIds as $id) {
            $elemento = $this->getDoctrine()->getRepository("AdminBundle:$entidad")->find($id);

            $arrayDatos[$i] = $elemento;
            $i++;
        }

        if ($entidad == 'Traza') {

            $html = $this->renderView('AdminBundle:Reportes:exportarTrazas.html.twig', array(
                'elementos' => $arrayDatos,
                'empresa' => $empresa,
            ));
        } elseif ($entidad == 'Usuario') {

            $html = $this->renderView('AdminBundle:Reportes:exportarUsuarios.html.twig', array(
                'elementos' => $arrayDatos,
                'empresa' => $empresa,
            ));
        } elseif ($entidad == 'Rol') {

            $html = $this->renderView('AdminBundle:Reportes:exportarRoles.html.twig', array(
                'elementos' => $arrayDatos,
                'empresa' => $empresa,
            ));
        } elseif ($entidad == 'Trabajador') {

            $html = $this->renderView('AdminBundle:Reportes:exportarTrabajadores.html.twig', array(
                'elementos' => $arrayDatos,
                'empresa' => $empresa,
            ));
        } else
            $html = "SIN DATOS";

        $dompdf = $this->get('slik_dompdf');

        // Generate the pdf
        $dompdf->getpdf($html);

        // Either stream the pdf to the browser
        $dompdf->stream("$entidad.pdf");

        // Or get the output to handle it yourself
        $pdfoutput = $dompdf->output();

        return new Response($pdfoutput, 200, array(
            'Content-Type' => 'application/pdf',
        ));

    }

    private function cargarCnfGralAction($session, $em)
    {
        $datconfig = $em->getRepository('AdminBundle:DatConfig')->find(1);
        if (!empty($datconfig)) {
            $session->set('config', $datconfig);
            $session->set('fecha_actual', $datconfig->getFechaTrabajo());
        }
        $ueb = $em->getRepository('NomencladorBundle:NomUeb')->findAll();
        if (!empty($ueb)) {
            $session->set('ueb', $ueb[0]);
        }
    }
}
