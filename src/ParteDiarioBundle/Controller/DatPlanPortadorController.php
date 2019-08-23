<?php

namespace ParteDiarioBundle\Controller;

use EnumsBundle\Entity\EnumMeses;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use ParteDiarioBundle\Entity\DatPlanPortador;
use ParteDiarioBundle\Form\DatPlanPortadorType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatPlanPortador controller.
 *
 */
class DatPlanPortadorController extends Controller
{
    /**
     * Lists all DatPlanPortador entities.
     *
     */
    private $arreglo = array();
    private static $meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'cantidad');

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPORTADORES')")
     */
    public function indexAction(Request $request, $ejercicio, $tipoPlan, $ueb,$remember)
    {
        $datPlanVentum = new DatPlanPortador();
        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanPortadorType', $datPlanVentum);
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        return $this->render('ParteDiarioBundle:DatPlanPortador:index.html.twig', array(
            'form' => $form->createView(),
            'ueb' => $ueb,
            'tipoPlan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'categoriaplan' => 4,
            'meses' => $meses,
            'remember' => $remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPORTADORES')")
     */
    public function validarPlanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 25;
        $dat = $request->query->get('dat');
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $where = ' 1=1';
        $tol = 0;
        $result = array();
        if ($parent != -1) {
            $ueb_obj = $em->getRepository('NomencladorBundle:NomUeb')->findAll();
            $md_obj = $em->getRepository('NomencladorBundle:NomMonedadestino')->findAll();
            if ($parent == 0) {
                $result = $this->listarPlanes($dat[1], null);//---------------------------TOTAL DE LA EMPRESA---------------------------

            }
            foreach ($ueb_obj as $ueb) {//---------------------------TOTALES POR UEBS---------------------------
                if ($parent == 0) {
                    $listUeb = $this->listarPlanes($dat[1], $ueb, null);
                    $result = array_merge($result, $listUeb);
                    foreach ($md_obj as $er) { //---------------------------TOTALES POR UEBS Y MONEDAS---------------------------
                        $listM = $this->listarPlanes($dat[1], $ueb, $er);
                        $result = array_merge($result, $listM);
                    }
                }
                $datos = $this->listarDatos($st, $lm, $dat, $parent);
                $tol = $tol + $datos['total'];
                $result = array_merge($result, $datos['datos']);
            }

        } else {
            if ($parent == -1) {
                $md_obj = $em->getRepository('NomencladorBundle:NomMonedadestino')->findAll();
                foreach ($md_obj as $er) {//---------------------------TOTALES POR MONEDAS de la EMPRESA---------------------------
                    $result1 = $this->listarPlanes($dat[1], null, $er);
                    $result = array_merge($result1, $result);
                }
            }
            $tol = 0;
        }
        return new JsonResponse(array('data' => $result, 'total' => $tol));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPORTADORES')")
     */
    public function listarPlanPAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        //$parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $port = array();
        /*if ($dat[0] == 0 || $dat[1] == 0 || $dat[2] == 0) {
            return new JsonResponse(array('data' => $port, 'total' => 0));
        }*/
        $where = ' 1=1';
        //$where .= ' AND g.idpadre=' . $parent;
        $where .= ($dat[2] != 0) ? (' and eje.idejercicio=') . $dat[2] : '';
        $where .= ($dat[0] != 0) ? (' and ueb.idueb = ' . $dat[0]) : '';
        $where .= ($dat[1] != 0) ? (' and pla.idtipoplan = ' . $dat[1]) : '';
        $dql = 'SELECT g.idplanportador,eje.idejercicio,pr.idportador,pla.idtipoplan,ueb.idueb,u.idunidadmedida,g.cantidad,g.enero,g.febrero,g.marzo,g.abril,
                       g.mayo,g.junio,g.agosto,g.julio,g.septiembre,g.octubre,g.noviembre,g.diciembre,g.hoja
            FROM ParteDiarioBundle:DatPlanPortador g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idportador pr
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            LEFT JOIN g.idunidadmedida u
            WHERE' . $where;
        $consulta = $em->createQuery($dql);
        $tol = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);

        $DatPlanPortador = $consulta->getResult();
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        foreach ($DatPlanPortador as $pr) {
            $res = array();
            $res[] = $pr['idplanportador'];
            $mant = date('m', strtotime('-1 month'));
            $mact = date('m');
            $msig = date('m', strtotime('+1 month'));
            $res[] = $pr['idplanportador'];
            $pr['idportador'] ? $portador = $em->getRepository('NomencladorBundle:NomPortador')->find($pr['idportador']) : $portador = '';
            $res[] = $portador ? $portador->getNombre() : '';
            $pr['idunidadmedida'] ? $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($pr['idunidadmedida']) : $um = '';
            $res[] = $um ? $um->getAbreviatura() : '';
            $cantidad = $pr['cantidad'] ? $pr['cantidad'] : 0;
            $cantidad = number_format($cantidad, 3);
            $res[] = $cantidad;
            $res[] = number_format($pr[strtolower($meses[$mant])], 3);
            $res[] = number_format($pr[strtolower($meses[$mact])], 3);
            $res[] = number_format($pr[strtolower($meses[$msig])], 3);
            /*$res[] = number_format($pr->getEnero(), 3);
            $res[] = number_format($pr->getFebrero(), 3);
            $res[] = number_format($pr->getMarzo(), 3);
            $res[] = number_format($pr->getAbril(), 3);
            $res[] = number_format($pr->getMayo(), 3);
            $res[] = number_format($pr->getJunio(), 3);
            $res[] = number_format($pr->getJulio(), 3);
            $res[] = number_format($pr->getAgosto(), 3);
            $res[] = number_format($pr->getSeptiembre(), 3);
            $res[] = number_format($pr->getOctubre(), 3);
            $res[] = number_format($pr->getNoviembre(), 3);
            $res[] = number_format($pr->getDiciembre(), 3);*/
            $total = $pr['enero'] + $pr['febrero'] + $pr['marzo'] + $pr['abril'] +
                $pr['mayo'] + $pr['junio'] + $pr['julio']
                + $pr['agosto'] + $pr['septiembre'] + $pr['octubre'] +
                $pr['noviembre'] + $pr['diciembre'];
            $dif = $cantidad - $total;
            $res[] = round(floatval($dif), 3, PHP_ROUND_HALF_DOWN);
            $res[] = $pr['hoja'];
            $port[] = $res;
        }
        return new JsonResponse(array('data' => $port, 'total' => $tol));
    }

    /**
     * Creates a new DatPlanPortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PLANPORTADORES')")
     */
    public function newAction(Request $request, $ejercicio, $tipoPlan, $ueb /*$padre*/)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $ent = new DatPlanPortador();
        $em = $this->getDoctrine()->getManager();
        /* if ($padre != null) {
             $ppadre = $em->getRepository('ParteDiarioBundle:DatPlanPortador')->find($padre);
         }*/
        if ($ueb != 0) {
            $pueb = $em->getRepository('NomencladorBundle:NomUeb')->find($ueb);
            $ent->setIdueb($pueb);
        }

        if ($tipoPlan != 0) {
            $ptipoPlan = $em->getRepository('NomencladorBundle:NomTipoplan')->find($tipoPlan);
            $ent->setIdtipoplan($ptipoPlan);
        }

        if ($ejercicio != 0) {
            $pejercicio = $em->getRepository('NomencladorBundle:NomEjercicio')->find($ejercicio);
            $ent->setIdejercicio($pejercicio);
        }

        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanPortadorType', $ent);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ent->setHoja(1);
            $ent->setIdpadre(0);
            /* if(is_null($padre))
                 $ent->setIdpadre(0);
             else
             {
                 $ent->setIdpadre($ppadre->getIdplanportador());
                 $ppadre->setHoja(0);
                 $em->persist($ppadre);
             }*/
            $em->persist($ent);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked())
                return $this->redirectToRoute('planportador_index',
                    array(
                        'ejercicio' => $ejercicio,
                        'tipoPlan' => $tipoPlan,
                        'ueb' => $ueb,
                        'remember' => 1
                    ));
            else
                return $this->redirectToRoute('planportador_new', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    // 'padre' => $padre
                ));
        }
        return $this->render('ParteDiarioBundle:DatPlanPortador:new.html.twig', array(
            'DatPlanPortador' => $ent,
            'form' => $form->createView(),
            'title' => 'Adicionar',
            'ejercicio' => $ejercicio,
            'tipoPlan' => $tipoPlan,
            'ueb' => $ueb,
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPORTADORES')")
     */
    public function validarAction(Request $request)
    {
        $where = "1=1";
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT sum(g.enero) as enero,sum(g.febrero) as febrero,sum(g.marzo) as marzo,sum(g.abril) as abril
                    ,sum(g.mayo) as mayo,sum(g.junio) as junio,sum(g.julio) as julio
                    ,sum(g.agosto) as agosto,sum(g.septiembre) as septiembre
                    ,sum(g.octubre) as octubre,sum(g.noviembre) as noviembre,sum(g.diciembre) as diciembre
            FROM ParteDiarioBundle:DatPlanPortador g WHERE g.idejercicio=4 ' . $where;
        $consulta = $em->createQuery($dql);
        return $consulta->getResult();
    }

    /**
     * Displays a form to edit an existing DatPlanPortador entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PLANPORTADORES')")
     */
    public function editAction(Request $request, DatPlanPortador $DatPlanPortador, $ejercicio, $tipoPlan, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatPlanPortadorType', $DatPlanPortador);
        $editForm->remove('agregar');
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($DatPlanPortador);
            $em->flush();
            $padres = [$DatPlanPortador->getIdplanportador()];
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('planportador_index', array(
                'ueb' => $ueb,
                'tipoPlan' => $tipoPlan,
                'ejercicio' => $ejercicio,
                'remember' => 1
            ));
        }

        return $this->render('ParteDiarioBundle:DatPlanPortador:new.html.twig', array(
            'DatPlanPortador' => $DatPlanPortador,
            'form' => $editForm->createView(),
            'title' => 'Editar',
            'ueb' => $ueb,
            'tipoPlan' => $tipoPlan,
            'ejercicio' => $ejercicio,
        ));
    }


    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANPORTADORES')")
     */
    public function eliminarAction(Request $request)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $msg = 'exito';
        if (!is_array($id))
            $id = [$id];
        foreach ($id as $f) {
            try {
                $objElim = $em->getRepository('ParteDiarioBundle:DatPlanPortador')->find($f);
                $em->remove($objElim);
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PLANPORTADORES')")
     */
    public function detallePlanAction(Request $request, DatPlanPortador $plan, $ejercicio, $tipoPlan, $ueb)
    {
        $total = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() +
            $plan->getJunio() + $plan->getJulio() + $plan->getAgosto() + $plan->getSeptiembre() + $plan->getOctubre()
            + $plan->getNoviembre() + $plan->getDiciembre();
        $diferencia = $plan->getCantidad() - $total;
        return $this->render('ParteDiarioBundle:datplanportador:detalle_plan.html.twig', array(
            'plan' => $plan,
            'ueb' => $ueb,
            'tipoPlan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'diferencia' => number_format($diferencia, 3)
        ));
    }

}
