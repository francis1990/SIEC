<?php

namespace ParteDiarioBundle\Controller;

use EnumsBundle\Entity\EnumMeses;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ParteDiarioBundle\Entity\DatPlanAseguramiento;
use ParteDiarioBundle\Form\DatPlanAseguramientoType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatPlanAseguramiento controller.
 *
 */
class DatPlanAseguramientoController extends Controller
{
    /**
     * Lists all DatPlanAseguramiento entities.
     *
     */
    private $arreglo = array();
    private static $meses = array(
        'enero',
        'febrero',
        'marzo',
        'abril',
        'mayo',
        'junio',
        'julio',
        'agosto',
        'septiembre',
        'octubre',
        'noviembre',
        'diciembre',
        'cantidad'
    );

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANASEGURAMIENTO')")
     */
    public function indexAction(Request $request, $ejercicio, $tipoPlan, $ueb, $remember)
    {
        $datPlanVentum = new DatPlanAseguramiento();

        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanAseguramientoType', $datPlanVentum);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $sinHijos = $em->getRepository('ParteDiarioBundle:DatPlanAseguramiento')->findByHoja(false);
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        return $this->render('ParteDiarioBundle:datplanaseguramiento/index.html.twig', array(
            'form' => $form->createView(),
            'ueb' => $ueb,
            'tipoplan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'categoriaplan' => 5,
            'validar' => count($sinHijos) > 0,
            'meses' => $meses,
            'remember' => $remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANASEGURAMIENTO')")
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

        return $this->render('ParteDiarioBundle:DatPlanAseguramiento:validar.html.twig', array(
            'data' => $data,
            'ueb' => $ueb ? $ueb->getNombre() : '',
            'tipoplan' => $tipo ? $tipo->getNombre() : '',
            'ejercicio' => $ejer ? $ejer->getNombre() : '',
            'fecha' => date("d-m-Y"),
            'hora' => date("h-i-s")
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANASEGURAMIENTO')")
     */
    public function validarAction(Request $request)
    {

        $dat = $request->query->get('dat');
        $prod = $this->validarAuxiliarAction($request, $dat);

        return new JsonResponse(array('data' => $prod));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANASEGURAMIENTO')")
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
        $dql = 'SELECT g.idplanaseguramiento,pr.idaseguramiento,pr.nombre ase,
                um.abreviatura umed ,md.nombre mond,g.cantidad,
                g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre
                ,g.octubre,g.noviembre,g.diciembre
            FROM ParteDiarioBundle:DatPlanAseguramiento g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idaseguramiento pr
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $padre . $where;
        $consulta = $em->createQuery($dql);
        $DatPlanAseguramiento = $consulta->getResult();

        $hijos = $this->getHijos($DatPlanAseguramiento, $where, $em);

        if (count($hijos) > 0) {
            $this->getArreglo($hijos, $where, $em);
            foreach ($this->arreglo as $pr) {
                $total = 0;
                $res = array();
                if ($pr['idplanaseguramiento'] != 'Diferencia' && $pr['idplanaseguramiento'] != 'Sumatoria') {
                    $res[] = $pr['ase'];
                } else {
                    $res[] = $pr['ase'];
                }
                $res[] = $pr['umed'] != null ? $pr['umed'] : '';
                $res[] = $pr['mond'] != null ? $pr['mond'] : '';
                $meses = self::$meses;
                foreach ($meses as $mes) {
                    if ($pr[$mes] != 0 && $mes != 'cantidad') {
                        $res[] = $pr[$mes];
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

    public function getHijos($DatPlanAseguramiento, $where, $em)//hijos con padre
    {
        $arraR = array();
        foreach ($DatPlanAseguramiento as $pr) {
            $dqlhijos = 'SELECT g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre,g.octubre,g.cantidad,g.noviembre,g.diciembre,
                          g.idplanaseguramiento,pr.idaseguramiento,pr.nombre ase,um.abreviatura umed,md.nombre mond
            FROM ParteDiarioBundle:DatPlanAseguramiento g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idaseguramiento pr
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $where . ' and( g.idpadre = ' . $pr['idplanaseguramiento'] . ' or g.idplanaseguramiento = ' . $pr['idplanaseguramiento'] . ')
            ORDER BY  g.idplanaseguramiento';

            $consulta = $em->createQuery($dqlhijos);
            if (count($consulta->getResult()) > 0) {
                $arraR[] = $consulta->getResult();
            }
        }
        return $arraR;
    }

    public function getDiferencia($arrReal, $arrSuma)
    {
        $arraResult = array();
        $cont = 0;
        $arraResult['idplanaseguramiento'] = '';
        $arraResult['ase'] = 'Diferencia';
        $arraResult['umed'] = '';
        $arraResult['mond'] = '';

        $meses = self::$meses;
        foreach ($meses as $mes) {
            $diferencia = $arrReal[$mes] - $arrSuma[$mes];
            if ($diferencia == 0) {
                $cont++;
            }
            $arraResult[$mes] = $diferencia;
        }
        if ($cont == 13) {
            return -1;
        } else {
            return $arraResult;
        }
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
                $arraAuxi['idplanaseguramiento'] = '';
                $arraAuxi['ase'] = 'Sumatoria';
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

    public function getNietos($DatPlanAseguramiento, $where, $em)
    {
        $arraR = array();
        $dqlhijos = 'SELECT g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre,g.octubre,g.cantidad,
                            g.noviembre,g.diciembre,
                          g.idplanaseguramiento,pr.idaseguramiento,pr.nombre ase,um.abreviatura umed,md.nombre mond
            FROM ParteDiarioBundle:DatPlanAseguramiento g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idaseguramiento pr
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $where . ' and( g.idpadre = ' . $DatPlanAseguramiento['idplanaseguramiento'] . ' or g.idplanaseguramiento = ' . $DatPlanAseguramiento['idplanaseguramiento'] . ')
            ORDER BY  g.idplanaseguramiento';

        $consulta = $em->createQuery($dqlhijos);
        if (count($consulta->getResult()) > 0) {
            $arraR[] = $consulta->getResult();
        }

        return $arraR;
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANASEGURAMIENTO')")
     */
    public function listarPlanPAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $aseg = array();
        /* if ($dat[0] == 0 || $dat[1] == 0 || $dat[2] == 0) {
             return new JsonResponse(array('data' => $aseg, 'total' => 0));
         }*/
        $where = ' 1=1';
        $where .= ' AND g.idpadre=' . $parent;
        $where .= ($dat[2] != 0) ? (' and eje.idejercicio=') . $dat[2] : '';
        $where .= ($dat[0] != 0) ? (' and ueb.idueb = ' . $dat[0]) : '';
        $where .= ($dat[1] != 0) ? (' and pla.idtipoplan = ' . $dat[1]) : '';
        $dql = 'SELECT g.idplanaseguramiento,g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.agosto,g.julio
                  ,g.septiembre,g.octubre,g.noviembre,g.diciembre,
                  g.cantidad,g.hoja,ase.idaseguramiento idaseg,ase.nombre nom,u.abreviatura um,m.nombre md
            FROM ParteDiarioBundle:DatPlanAseguramiento g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idaseguramiento ase
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idunidadmedida u
            LEFT JOIN g.idmonedadestino m
            LEFT JOIN g.idueb ueb
            WHERE' . $where;
        $consulta = $em->createQuery($dql);
        $tol = count($consulta->getResult());

        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $DatPlanAseguramiento = $consulta->getResult();
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        foreach ($DatPlanAseguramiento as $pr) {
            $res = array();
            $res[] = $pr['idplanaseguramiento'];
            $mant = date('m', strtotime('-1 month'));
            $mact = date('m');
            $msig = date('m', strtotime('+1 month'));
            $res[] = $pr['idplanaseguramiento'];
            $res[] = ($pr['nom'] != null && $pr['nom'] != '') ? $pr['nom'] : '';
            $res[] = ($pr['um'] != null && $pr['um'] != '') ? $pr['um'] : '';
            $res[] = ($pr['md'] != null && $pr['md'] != '') ? $pr['md'] : 'Total';
            $cant = $pr['cantidad'];
            $res[] = number_format($cant, 3);
            $res[] = number_format($pr[strtolower($meses[$mant])], 3);
            $res[] = number_format($pr[strtolower($meses[$mact])], 3);
            $res[] = number_format($pr[strtolower($meses[$msig])], 3);
            /*$res[] = number_format($pr['enero'], 3);
            $res[] = number_format($pr['febrero'], 3);
            $res[] = number_format($pr['marzo'], 3);
            $res[] = number_format($pr['abril'], 3);
            $res[] = number_format($pr['mayo'], 3);
            $res[] = number_format($pr['junio'], 3);
            $res[] = number_format($pr['julio'], 3);
            $res[] = number_format($pr['agosto'], 3);
            $res[] = number_format($pr['septiembre'], 3);
            $res[] = number_format($pr['octubre'], 3);
            $res[] = number_format($pr['noviembre'], 3);
            $res[] = number_format($pr['diciembre'], 3);*/
            $total = $pr['enero'] + $pr['febrero'] + $pr['marzo'] + $pr['abril']
                + $pr['mayo'] + $pr['junio'] + $pr['julio'] + $pr['agosto']
                + $pr['septiembre'] + $pr['octubre'] + $pr['noviembre'] + $pr['diciembre'];
            $dif = $cant - $total;
            $res[] = round(floatval($dif), 3, PHP_ROUND_HALF_DOWN);
            $res[] = $pr['hoja'];
            $aseg[] = $res;
        }
        return new JsonResponse(array('data' => $aseg, 'total' => $tol));
    }

    /**
     * Creates a new DatPlanAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PLANASEGURAMIENTO')")
     */
    public function newAction(Request $request, $ejercicio, $tipoPlan, $ueb, $padre)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $ent = new DatPlanAseguramiento();
        $em = $this->getDoctrine()->getManager();
        if ($padre != null) {
            $ppadre = $em->getRepository('ParteDiarioBundle:DatPlanAseguramiento')->find($padre);
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
        if ($padre != null) {
            $hijos = array();
            $aseguramientos = $this->get('nomenclador.nomaseguramiento')->getAseguramientoHijas($ppadre->getIdaseguramiento(),
                $hijos, null, true, true);
        }
        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanAseguramientoType', $ent, array('aseguramientos' => $aseguramientos));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ent->setHoja(1);
            if (is_null($ppadre)) {
                $ent->setIdpadre(0);
            } else {
                $ent->setIdpadre($ppadre->getIdplanaseguramiento());
                $ppadre->setHoja(0);
                $em->persist($ppadre);
            }
            $em->persist($ent);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                $params['tabla'] = 'DatPlanAseguramiento';
                $params['campoId'] = 'idplanaseguramiento';
                $padres = $this->get('planes_partes')->getObjPadres($ent, [], $params);
                $padres = array_reverse($padres);
                array_push($padres, $ent->getIdplanaseguramiento());
                $this->get('session')->set('padres', $padres);
                return $this->redirectToRoute('planaseguramiento_index', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    'remember' => 1
                ));
            } else {
                return $this->redirectToRoute('planaseguramiento_new', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    'padre' => $padre
                ));
            }
        }
        return $this->render('ParteDiarioBundle:datplanaseguramiento/new.html.twig', array(
            'datPlanAseguramiento' => $ent,
            'form' => $form->createView(),
            'title' => 'Adicionar',
            'ejercicio' => $ejercicio,
            'tipoplan' => $tipoPlan,
            'ueb' => $ueb,

        ));
    }

    /**
     * Finds and displays a DatPlanAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PLANASEGURAMIENTO')")
     */
    public function showAction(DatPlanAseguramiento $datPlanAseguramiento)
    {
        $deleteForm = $this->createDeleteForm($datPlanAseguramiento);
        return $this->render('ParteDiarioBundle:datplanaseguramiento/show.html.twig', array(
            'datPlanAseguramiento' => $datPlanAseguramiento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DatPlanAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PLANASEGURAMIENTO')")
     */
    public function editAction(Request $request, DatPlanAseguramiento $datPlanAseguramiento, $ejercicio, $tipoPlan, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatPlanAseguramientoType', $datPlanAseguramiento);
        $editForm->remove('agregar');
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($datPlanAseguramiento);
            $em->flush();
            $params['tabla'] = 'DatPlanAseguramiento';
            $params['campoId'] = 'idplanaseguramiento';
            $padres = $this->get('planes_partes')->getObjPadres($datPlanAseguramiento, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $datPlanAseguramiento->getIdplanaseguramiento());
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('planaseguramiento_index', array(
                'ueb' => $ueb,
                'tipoPlan' => $tipoPlan,
                'ejercicio' => $ejercicio,
                'remember' => 1
            ));
        }

        return $this->render('ParteDiarioBundle:datplanaseguramiento/new.html.twig', array(
            'datPlanAseguramiento' => $datPlanAseguramiento,
            'form' => $editForm->createView(),
            'title' => 'Editar',
            'ueb' => $ueb,
            'tipoplan' => $tipoPlan,
            'ejercicio' => $ejercicio,

        ));
    }

    /**
     * Deletes a DatPlanAseguramiento entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANASEGURAMIENTO')")
     */
    public function deleteAction(Request $request, DatPlanAseguramiento $datPlanAseguramiento)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datPlanAseguramiento);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datPlanAseguramiento);
            $em->flush();
        }

        return $this->redirectToRoute('planaseguramiento_index');
    }

    /**
     * Creates a form to delete a DatPlanAseguramiento entity.
     *
     * @param DatPlanAseguramiento $datPlanAseguramiento The DatPlanAseguramiento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANASEGURAMIENTO')")
     */
    private function createDeleteForm(DatPlanAseguramiento $datPlanAseguramiento)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('planaseguramiento_delete',
                array('id' => $datPlanAseguramiento->getIdplanaseguramiento())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function getAllHijos($id)//solo hijos
    {
        $em = $this->getDoctrine()->getManager();
        $arraR = array();
        $dqlhijos = 'SELECT g.idplanaseguramiento
            FROM ParteDiarioBundle:DatPlanAseguramiento g
            WHERE g.idpadre = ' . $id;

        $consulta = $em->createQuery($dqlhijos);
        if (count($consulta->getResult()) > 0) {
            $arraR = $consulta->getResult();
            foreach ($arraR as $pr) {
                $this->getAllHijos($pr['idplanaseguramiento']);
                try {
                    $this->get('parte_diario.comun_service')->deleteOne('ParteDiarioBundle:DatPlanAseguramiento',
                        $pr['idplanaseguramiento']);
                } catch (ForeignKeyConstraintViolationException $e) {
                    $msg = 'error';
                }
            }
            return;
        } else {
            return;
        }

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANASEGURAMIENTO')")
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
        if (!is_array($id)) {
            $id = [$id];
        }
        foreach ($id as $f) {
            try {
                $this->getAllHijos($f);
                $this->get('parte_diario.comun_service')->deleteOne('ParteDiarioBundle:DatPlanAseguramiento', $f);
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PLANASEGURAMIENTO')")
     */
    public function detallePlanAction(Request $request, DatPlanAseguramiento $plan, $ejercicio, $tipoPlan, $ueb)
    {
        $total = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() +
            $plan->getJunio() + $plan->getJulio() + $plan->getAgosto() + $plan->getSeptiembre() + $plan->getOctubre()
            + $plan->getNoviembre() + $plan->getDiciembre();
        $diferencia = $plan->getCantidad() - $total;
        return $this->render('ParteDiarioBundle:datplanaseguramiento:detalle_plan.html.twig', array(
            'plan' => $plan,
            'ueb' => $ueb,
            'tipoplan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'diferencia' => number_format($diferencia, 3)
        ));
    }
}
