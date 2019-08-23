<?php

namespace ParteDiarioBundle\Controller;

use EnumsBundle\Entity\EnumMeses;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use ParteDiarioBundle\Entity\DatPlanProduccion;
use ParteDiarioBundle\Form\DatPlanProduccionType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Symfony\Component\Validator\Constraints\Time;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatPlanProduccion controller.
 *
 */
class DatPlanProduccionController extends Controller
{
    /**
     * Lists all DatPlanProduccion entities.
     *
     */
    private $arreglo = array();
    private static $meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'cantidad');

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPRODUCCION')")
     */
    public function indexAction(Request $request,$ejercicio, $tipoPlan, $ueb,$remember)
    {
        $datPlanVentum = new DatPlanProduccion();
        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanProduccionType', $datPlanVentum);
        $em = $this->getDoctrine()->getManager();
        $sinHijos = $em->getRepository('ParteDiarioBundle:DatPlanProduccion')->findByHoja(false);
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        return $this->render('ParteDiarioBundle:datplanproduccion:index.html.twig', array(
            'form' => $form->createView(),
            'ueb' => $ueb,
            'tipoPlan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'categoriaplan' => 0,
            'validar' => count($sinHijos) > 0,
            'meses' => $meses,
            'remember' => $remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPRODUCCION')")
     */
    public function generarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dat = $request->attributes->get('data');
        $datos = explode(',', $dat);
        $ueb = $em->getRepository('NomencladorBundle:NomUeb')->find($datos[0]);
        $tipo = $em->getRepository('NomencladorBundle:NomTipoPlan')->find($datos[1]);
        $ejer = $em->getRepository('NomencladorBundle:NomEjercicio')->find($datos[2]);
        $data = $this->validarAuxiliarAction($request, explode(',', $dat));

        return $this->render('ParteDiarioBundle:datplanproduccion:validar.html.twig', array(
            'data' => $data,
            'ueb' => $ueb ? $ueb->getNombre() : '',
            'tipoplan' => $tipo ? $tipo->getNombre() : '',
            'ejercicio' => $ejer ? $ejer->getNombre() : '',
            'fecha' => date("d-m-Y"),
            'hora' => date("h-i-s")
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPRODUCCION')")
     */
    public function validarAction(Request $request)
    {

        $dat = $request->query->get('dat');
        $prod = $this->validarAuxiliarAction($request, $dat);

        return new JsonResponse(array('data' => $prod));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPRODUCCION')")
     */
    public function validarAuxiliarAction(Request $request, $dat)
    {
        $em = $this->getDoctrine()->getManager();

        $prod = array();
        $where = ' 1=1';
        $where .= ($dat[0] != 0) ? (' and ueb.idueb = ' . $dat[0]) : '';
        $where .= ($dat[1] != 0) ? (' and pla.idtipoplan = ' . $dat[1]) : '';
        $where .= ($dat[2] != 0) ? (' and eje.idejercicio=') . $dat[2] : '';

        $padre = ' g.idpadre = 0 and ';
        $dql = 'SELECT g.idplanproduccion,pr.idproducto,um.nombre umed ,md.nombre mond,g.cantidad,
                g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre
                ,g.octubre,g.noviembre,g.diciembre
            FROM ParteDiarioBundle:DatPlanProduccion g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $padre . $where;
        $consulta = $em->createQuery($dql);
        $DatPlanProduccion = $consulta->getResult();

        $hijos = $this->getHijos($DatPlanProduccion, $where, $em);

        if (count($hijos) > 0) {
            $this->getArreglo($hijos, $where, $em);
            foreach ($this->arreglo as $pr) {
                $total = 0;
                $res = array();
                if ($pr['idproducto'] != 'Diferencia' && $pr['idproducto'] != 'Sumatoria') {
                    $producto = $em->getRepository('NomencladorBundle:NomProducto')->find($pr['idproducto']);
                    $res[] = $producto ? $producto->getNombre() : '';
                } else {
                    $res[] = $pr['idproducto'];
                }
                $res[] = $pr['umed'];
                $res[] = $pr['mond'];
                $meses = self::$meses;
                foreach ($meses as $mes) {
                    if ($pr[$mes] != 0 && $mes != 'cantidad') {
                        $res[] = number_format(floatval($pr[$mes]), 3);
                    } else {
                        if ($mes != 'cantidad') {
                            $res[] = '';
                        }
                    }
                    $mes != 'cantidad' ? $total += $pr[$mes] : '';
                }
                $res[] = number_format(floatval($pr['cantidad']), 3);
                $res[] = number_format(floatval($total), 3);
                $res[] = round(floatval($pr['cantidad'] - $total), 3, PHP_ROUND_HALF_DOWN);
                $prod[] = $res;
            }
        }
        return $prod;
    }

    public function getHijos($DatPlanProduccion, $where, $em)
    {
        $arraR = array();
        foreach ($DatPlanProduccion as $pr) {
            $dqlhijos = 'SELECT g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre,g.octubre,g.cantidad,g.noviembre,g.diciembre,
                          g.idplanproduccion,pr.idproducto,um.nombre umed,md.nombre mond
            FROM ParteDiarioBundle:DatPlanProduccion g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $where . ' and( g.idpadre = ' . $pr['idplanproduccion'] . ' or g.idplanproduccion = ' . $pr['idplanproduccion'] . ')
            ORDER BY  g.idplanproduccion';

            $consulta = $em->createQuery($dqlhijos);
            if (count($consulta->getResult()) > 0)
                $arraR[] = $consulta->getResult();
        }
        return $arraR;
    }

    public function getDiferencia($arrReal, $arrSuma)
    {
        $arraResult = array();
        $cont = 0;
        $arraResult['idplanproduccion'] = '';
        $arraResult['idproducto'] = 'Diferencia';
        $arraResult['umed'] = '';
        $arraResult['mond'] = '';

        $meses = self::$meses;
        foreach ($meses as $mes) {
            $diferencia = $arrReal[$mes] - $arrSuma[$mes];
            if ($diferencia == 0)
                $cont++;
            $arraResult[$mes] = $diferencia;
        }
        if ($cont == 13)
            return -1;
        else
            return $arraResult;
    }

    public function getArreglo($arrHijos, $where, $em)
    {
        $arraResult = array();
        $meses = self::$meses;
        $total = 0;
        foreach ($arrHijos as $ke => $o) {
            $arraAuxi = array();
            $totalP = 0;
            for ($f = 1; $f < count($o); $f++) {
                $total = 0;
                $arraAuxi['idplanproduccion'] = '';
                $arraAuxi['idproducto'] = 'Sumatoria';
                $arraAuxi['umed'] = '';
                $arraAuxi['mond'] = '';

                foreach ($meses as $mes) {
                    isset($arraAuxi[$mes])
                        ? ($arraAuxi[$mes] += $o[$f][$mes])
                        : $arraAuxi[$mes] = $o[$f][$mes];
                    $mes != 'cantidad' ? $total += $o[$f][$mes] : $total = $total;
                }
            }
            foreach ($meses as $mes) {
                $mes != 'cantidad' ? $totalP += $o[0][$mes] : $totalP = $totalP;
            }
            if (count($o) > 1 || (($totalP - $o[0]['cantidad']) != 0)) {
                if (count($o) > 1) {
                    $diferencia = $this->getDiferencia($o[0], $arraAuxi);
                    if ($diferencia != -1) {
                        $this->arreglo[] = $o[0];
                        $this->arreglo[] = $arraAuxi;
                        $this->arreglo[] = $diferencia;
                    }
                } else {
                    $this->arreglo[] = $o[0];
                }

            }
            for ($t = 1; $t < count($o); $t++) {
                $hij = $this->getNietos($o[$t], $where, $em);
                $this->getArreglo($hij, $where, $em);
            }
            if ((count($o) <= 1 && (($o[0]['cantidad'] - $totalP) == 0))) {
                return;
            }

        }

        return $arraResult;
    }

    public function getNietos($DatPlanProduccion, $where, $em)
    {
        $arraR = array();

        $dqlhijos = 'SELECT g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre,g.octubre,g.cantidad,g.noviembre,g.diciembre,
                          g.idplanproduccion,pr.idproducto,um.nombre umed,md.nombre mond
            FROM ParteDiarioBundle:DatPlanProduccion g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $where . ' and( g.idpadre = ' . $DatPlanProduccion['idplanproduccion'] . ' or g.idplanproduccion = ' . $DatPlanProduccion['idplanproduccion'] . ')
            ORDER BY  g.idplanproduccion';

        $consulta = $em->createQuery($dqlhijos);
        if (count($consulta->getResult()) > 0)
            $arraR[] = $consulta->getResult();

        return $arraR;
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPRODUCCION')")
     */
    public function listarPlanPAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;

        $prod = array();
        /* if ($dat[0] == 0 || $dat[1] == 0 || $dat[2] == 0) {
             return new JsonResponse(array('data' => $prod, 'total' => 0));
         }*/
        $where = ' 1=1';
        $where .= ' AND g.idpadre=' . $parent;
        $where .= ($dat[2] != 0) ? (' and eje.idejercicio=') . $dat[2] : '';
        $where .= ($dat[0] != 0) ? (' and ueb.idueb = ' . $dat[0]) : '';
        $where .= ($dat[1] != 0) ? (' and pla.idtipoplan = ' . $dat[1]) : '';

        $dql = 'SELECT g.idplanproduccion,eje.idejercicio,pr.idproducto,pla.idtipoplan,ueb.idueb,u.idunidadmedida,g.cantidad,g.enero,g.febrero,g.marzo,g.abril,
                       g.mayo,g.junio,g.agosto,g.julio,g.septiembre,g.octubre,g.noviembre,g.diciembre,g.hoja,m.id idmoneda,g.hoja
            FROM ParteDiarioBundle:DatPlanProduccion g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            LEFT JOIN g.idunidadmedida u
            LEFT JOIN g.idmonedadestino m
            WHERE' . $where;
        $consulta = $em->createQuery($dql);//var_dump($em);die;
        $tol = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $DatPlanProduccion = $consulta->getResult();
        $conhijos = false;
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        foreach ($DatPlanProduccion as $pr) {
            $res = array();
            $res[] = $pr['idplanproduccion'];
            $mant = date('m', strtotime('-1 month'));
            $mact = date('m');
            $msig = date('m', strtotime('+1 month'));
            $res[] = $pr['idplanproduccion'];
            $pr['idproducto'] ? $producto = $em->getRepository('NomencladorBundle:NomProducto')->find($pr['idproducto']) : $producto = '';
            $res[] = $producto ? $producto->getNombre() : '';
            $pr['idunidadmedida'] ? $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($pr['idunidadmedida']) : $um = '';
            $res[] = $um ? $um->getAbreviatura() : '';
            $pr['idmoneda'] ? $md = $em->getRepository('NomencladorBundle:NomMonedadestino')->find($pr['idmoneda']) : $md = '';
            $res[] = $md ? $md->getNombre() : 'Total';
            $cantidad = $pr['cantidad'] ? $pr['cantidad'] : 0;
            $res[] = number_format($cantidad, 3);
            $res[] = number_format($pr[strtolower($meses[$mant])], 3);
            $res[] = number_format($pr[strtolower($meses[$mact])], 3);
            $res[] = number_format($pr[strtolower($meses[$msig])], 3);
            $total = $pr['enero'] + $pr['febrero'] + $pr['marzo'] + $pr['abril'] +
                $pr['mayo'] + $pr['junio'] + $pr['julio']
                + $pr['agosto'] + $pr['septiembre'] + $pr['octubre'] +
                $pr['noviembre'] + $pr['diciembre'];
            $dif = $cantidad - $total;
            $res[] = round(floatval($dif), 3, PHP_ROUND_HALF_DOWN);
            $res[] = $pr['hoja'];
            $prod[] = $res;
            if (!$pr['hoja'])
                $conhijos = true;
        }
        return new JsonResponse(array('data' => $prod, 'total' => $tol, 'conhijos' => $conhijos));
    }


    /**
     * Creates a new DatPlanProduccion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PLANPRODUCCION')")
     */
    public function newAction(Request $request, $ejercicio, $tipoPlan, $ueb, $padre)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }

        $ent = new DatPlanProduccion();
        $em = $this->getDoctrine()->getManager();

        if ($padre != null) {
            $ppadre = $em->getRepository('ParteDiarioBundle:DatPlanProduccion')->find($padre);
        }
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

        $prods = $this->get('nomenclador.nomproducto')->getProdHjosPlanParaFormType($ppadre);
        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanProduccionType', $ent, array('productos' => $prods));
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if(  $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $ent->setHoja(1);
            if (is_null($padre))
                $ent->setIdpadre(0);
            else {
                $ent->setIdpadre($ppadre->getidplanproduccion());
                $ppadre->setHoja(0);
                $em->persist($ppadre);
            }
            $em->persist($ent);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                $params['tabla'] = 'DatPlanProduccion';
                $params['campoId'] = 'idplanproduccion';
                $padres = $this->get('planes_partes')->getObjPadres($ent, [], $params);
                $padres = array_reverse($padres);
                array_push($padres, $ent->getidplanproduccion());
                $this->get('session')->set('padres', $padres);
                return $this->redirectToRoute('planproduccion_index', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    'remember' => 1
                ));
            }
            else
                return $this->redirectToRoute('planproduccion_new', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    'padre' => $padre
                ));
        }}
        return $this->render('ParteDiarioBundle:DatPlanProduccion:new.html.twig', array(
            'DatPlanProduccion' => $ent,
            'form' => $form->createView(),
            'title' => 'Adicionar',
            'ejercicio' => $ejercicio,
            'tipoPlan' => $tipoPlan,
            'ueb' => $ueb,
        ));
    }


    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPRODUCCION')")
     */
    public function validarAuxAction(Request $request)
    {
        $where = "1=1";
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT sum(g.enero) as enero,sum(g.febrero) as febrero,sum(g.marzo) as marzo,sum(g.abril) as abril
                    ,sum(g.mayo) as mayo,sum(g.junio) as junio,sum(g.julio) as julio
                    ,sum(g.agosto) as agosto,sum(g.septiembre) as septiembre
                    ,sum(g.octubre) as octubre,sum(g.noviembre) as noviembre,sum(g.diciembre) as diciembre
            FROM ParteDiarioBundle:DatPlanProduccion g WHERE g.idejercicio=4 ' . $where;
        $consulta = $em->createQuery($dql);
        return $consulta->getResult();
    }

    /**
     * Displays a form to edit an existing DatPlanProduccion entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PLANPRODUCCION')")
     */
    public function editAction(Request $request, DatPlanProduccion $DatPlanProduccion, $ejercicio, $tipoPlan, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatPlanProduccionType', $DatPlanProduccion);
        $editForm->remove('agregar');
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($DatPlanProduccion);
            $em->flush();
            $params['tabla'] = 'DatPlanProduccion';
            $params['campoId'] = 'idplanproduccion';
            $padres = $this->get('planes_partes')->getObjPadres($DatPlanProduccion, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $DatPlanProduccion->getidplanproduccion());
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('planproduccion_index',array(
                'ejercicio' => $ejercicio,
                'tipoPlan' =>$tipoPlan,
                'ueb' => $ueb,
                'remember' => 1
            ));
        }
        return $this->render('ParteDiarioBundle:DatPlanProduccion:new.html.twig', array(
            'DatPlanProduccion' => $DatPlanProduccion,
            'form' => $editForm->createView(),
            'title' => 'Editar',
            'ejercicio' => $ejercicio,
            'tipoPlan' =>$tipoPlan,
            'ueb' => $ueb
        ));
    }


    public function getAllHijos($id)
    {
        $em = $this->getDoctrine()->getManager();
        $arraR = array();
        $dqlhijos = 'SELECT g.idplanproduccion
            FROM ParteDiarioBundle:DatPlanProduccion g
            WHERE g.idpadre = ' . $id;

        $consulta = $em->createQuery($dqlhijos);
        if (count($consulta->getResult()) > 0) {
            $arraR = $consulta->getResult();
            foreach ($arraR as $pr) {
                $this->getAllHijos($pr['idplanproduccion']);
                try {
                    $this->get('parte_diario.comun_service')->deleteOne('ParteDiarioBundle:DatPlanProduccion', $pr['idplanproduccion']);
                } catch (ForeignKeyConstraintViolationException $e) {
                    $msg = 'error';
                }
            }
            return;
        } else
            return;

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANPRODUCCION')")
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
                $this->getAllHijos($f);
                $objElim = $em->getRepository('ParteDiarioBundle:DatPlanProduccion')->find($f);
                $em->remove($objElim);
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANPRODUCCION')")
     */
    public function listadoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT g
            FROM ParteDiarioBundle:DatPlanProduccion g  ';
        $consulta = $em->createQuery($dql);
        $cons = $consulta->getResult();
        $result = Array();
        foreach ($cons as $pr) {
            $result['nombre'] = $pr->getNombre();
            $result['idplanproduccion'] = $pr->getidplanproduccion();
        }

        return $this->render('ParteDiarioBundle:DatPlanProduccion:listaplanproduccion.html.twig', array('plans' => $result));
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PLANPRODUCCION')")
     */
    public function detallePlanAction(Request $request, DatPlanProduccion $plan, $ejercicio, $tipoPlan, $ueb)
    {
        $total = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() +
            $plan->getJunio() + $plan->getJulio() + $plan->getAgosto() + $plan->getSeptiembre() + $plan->getOctubre()
            + $plan->getNoviembre() + $plan->getDiciembre();
        $diferencia = $plan->getCantidad() - $total;
        return $this->render('ParteDiarioBundle:datplanproduccion:detalle_plan.html.twig', array(
            'plan' => $plan,
            'ejercicio' => $ejercicio,
            'tipoPlan' =>$tipoPlan,
            'ueb' => $ueb,
            'diferencia' => number_format($diferencia, 3)
        ));
    }

}
