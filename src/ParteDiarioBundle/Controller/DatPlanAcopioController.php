<?php

namespace ParteDiarioBundle\Controller;

use EnumsBundle\Entity\EnumMeses;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ParteDiarioBundle\Entity\DatPlanAcopio;
use ParteDiarioBundle\Form\DatPlanAcopioType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

/**
 * DatPlanAcopio controller.
 *
 */
class DatPlanAcopioController extends Controller
{
    /**
     * Lists all DatPlanAcopio entities.
     *
     */
    private $arreglo = array();
    private static $meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'cantidad');

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANACOPIO')")
     */
    public function indexAction(Request $request, $ejercicio, $tipoPlan, $ueb, $remember)
    {
        $em = $this->getDoctrine()->getManager();
        $sinHijos = $em->getRepository('ParteDiarioBundle:DatPlanAcopio')->findByHoja(false);
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        return $this->render('ParteDiarioBundle:datplanacopio/index.html.twig', array(
            'ueb' => $ueb,
            'tipoplan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'categoriaplan' => 2,
            'validar' => count($sinHijos) > 0,
            'meses' => $meses,
            'remember' => $remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANACOPIO')")
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
        return $this->render('ParteDiarioBundle:datplanacopio:validar.html.twig', array(
            'data' => $data,
            'ueb' => $ueb ? $ueb->getNombre() : '',
            'tipoplan' => $tipo ? $tipo->getNombre() : '',
            'ejercicio' => $ejer ? $ejer->getNombre() : '',
            'fecha' => date("d-m-Y"),
            'hora' => date("h-i-s")
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANACOPIO')")
     */
    public function validarAction(Request $request)
    {
        $dat = $request->query->get('dat');
        $prod = $this->validarAuxiliarAction($request, $dat);
        return new JsonResponse(array('data' => $prod));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANACOPIO')")
     */
    public function validarAuxiliarAction(Request $request, $dat)
    {
        $em = $this->getDoctrine()->getManager();
        $prod = array();
        $where = ' 1=1';
        /* $where .= ($dat[0] != 0) ? (' and ueb.idueb = ' . $dat[0]) : '';*/
        $where .= ($dat[1] != 0) ? (' and pla.idtipoplan = ' . $dat[1]) : '';
        $where .= ($dat[2] != 0) ? (' and eje.idejercicio=') . $dat[2] : '';
        $padre = ' g.idpadre = 0 and ';
        $dql = 'SELECT g
            FROM ParteDiarioBundle:DatPlanAcopio g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $padre . $where;

        $consulta = $em->createQuery($dql);
        $DatPlanAcopio = $consulta->getArrayResult();

        $hijos = $this->getHijos($DatPlanAcopio, $where, $em);

        if (count($hijos) > 0) {
            $this->getArreglo($hijos, $where, $em);

            foreach ($this->arreglo as $pr) {
                $total = 0;
                $res = array();
                if ($pr['idproducto'] != 'Diferencia' && $pr['idproducto'] != 'Sumatoria') {
                    if ($pr['idproducto'] != '') {
                        $producto = $em->getRepository('NomencladorBundle:NomProducto')->find($pr['idproducto']);
                        $res[] = $producto ? $producto->getNombre() : '';
                    }
                } else {
                    $res[] = $pr['idproducto'];
                }
                $res[] = $pr['umed'];
                $res[] = $pr['mond'];
                $meses = self::$meses;
                foreach ($meses as $mes) {
                    if ($pr[$mes] != 0 && $mes != 'cantidad') {
                        $res[] = number_format($pr[$mes], 3, '.', ',');
                    } else {
                        if ($mes != 'cantidad') {
                            $res[] = '';
                        }
                    }
                    $mes != 'cantidad' ? $total += $pr[$mes] : '';
                }
                $res[] = $pr['cantidad'];
                $res[] = $total;
                $res[] = number_format($pr['cantidad'] - $total, 3, '.', ',');
                $prod[] = $res;
            }
        }

        return $prod;
    }

    public function getHijos($DatPlanAcopio, $where, $em)
    {
        $arraR = array();
        foreach ($DatPlanAcopio as $pr) {
            $dqlhijos = 'SELECT g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre,g.octubre,g.cantidad,g.noviembre,g.diciembre,
                          g.idplanacopio,pr.idproducto,um.abreviatura umed,md.nombre mond
            FROM ParteDiarioBundle:DatPlanAcopio g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $where . ' and( g.idpadre = ' . $pr['idplanacopio'] . ' or g.idplanacopio = ' . $pr['idplanacopio'] . ')
            ORDER BY  g.idplanacopio';

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
        $arraResult['idplanacopio'] = '';
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
                $arraAuxi['idplanacopio'] = '';
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

    public function getNietos($DatPlanAcopio, $where, $em)
    {
        $arraR = array();
        $dqlhijos = 'SELECT g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre,g.octubre,g.cantidad,g.noviembre,g.diciembre,
                          g.idplanacopio,pr.idproducto,um.abreviatura umed,md.nombre mond
            FROM ParteDiarioBundle:DatPlanAcopio g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $where . ' and( g.idpadre = ' . $DatPlanAcopio['idplanacopio'] . ' or g.idplanacopio = ' . $DatPlanAcopio['idplanacopio'] . ')
            ORDER BY  g.idplanacopio';

        $consulta = $em->createQuery($dqlhijos);
        if (count($consulta->getResult()) > 0)
            $arraR[] = $consulta->getResult();

        return $arraR;
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANACOPIO')")
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

        $dql = 'SELECT g.idplanacopio,g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.agosto,g.julio,g.septiembre,g.octubre,g.noviembre,g.diciembre,
                g.cantidad,g.hoja,prod.idproducto producto,u.abreviatura um,m.nombre md
            FROM ParteDiarioBundle:DatPlanAcopio g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto prod
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idunidadmedida u
            LEFT JOIN g.idmonedadestino m
            LEFT JOIN g.idueb ueb
            WHERE' . $where;
        $consulta = $em->createQuery($dql);
        $tol = count($consulta->getResult());//

        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $DatPlanAcopio = $consulta->getResult();
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();

        foreach ($DatPlanAcopio as $pr) {
            $res = array();
            $res[] = $pr['idplanacopio'];
            $mant = date('m', strtotime('-1 month'));
            $mact = date('m');
            $msig = date('m', strtotime('+1 month'));
            $res[] = $pr['idplanacopio'];
            $pr['producto'] ? $p = $em->getRepository('NomencladorBundle:NomProducto')->find($pr['producto']) : $p = '';
            ($p != '') ? $res[] = $p->generarNombre() : $res[] = '';
            $res[] = $pr['um'];
            $res[] = $pr['md'] == null ? 'Total' : $pr['md'];
            $cant = $pr['cantidad'];
            $res[] = number_format($cant, 3);
            $res[] = number_format($pr[strtolower($meses[$mant])], 3);
            $res[] = number_format($pr[strtolower($meses[$mact])], 3);
            $res[] = number_format($pr[strtolower($meses[$msig])], 3);
            /*   $res[] = number_format($pr['abril'], 3);
               $res[] = number_format($pr['mayo'], 3);
               $res[] = number_format($pr['junio'], 3);
               $res[] = number_format($pr['julio'], 3);
               $res[] = number_format($pr['agosto'], 3);
               $res[] = number_format($pr['septiembre'], 3);
               $res[] = number_format($pr['octubre'], 3);
               $res[] = number_format($pr['noviembre'], 3);
               $res[] = number_format($pr['diciembre'], 3);*/
            $total = $pr['enero'] + $pr['febrero'] + $pr['marzo'] + $pr['abril'] +
                $pr['mayo'] + $pr['junio'] + $pr['julio']
                + $pr['agosto'] + $pr['septiembre'] + $pr['octubre'] +
                $pr['noviembre'] + $pr['diciembre'];
            $dif = $cant - $total;
            $res[] = round(floatval($dif), 3, PHP_ROUND_HALF_DOWN);
            $res[] = $pr['hoja'];
            $prod[] = $res;
        }
        $tol = count($prod);
        return new JsonResponse(array('data' => $prod, 'total' => $tol));
    }


    /**
     * Creates a new DatPlanAcopio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PLANACOPIO')")
     */
    public function newAction(Request $request, $ejercicio = 0, $tipoPlan = 0, $ueb = 0, $padre = null)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $datPlanAcopio = new DatPlanAcopio();
        $em = $this->getDoctrine()->getManager();
        if ($padre != null)
            $ppadre = $em->getRepository('ParteDiarioBundle:DatPlanAcopio')->find($padre);
        if ($ueb != 0) {
            $pueb = $em->getRepository('NomencladorBundle:NomUeb')->find($ueb);
            $datPlanAcopio->setIdueb($pueb);
        }
        if ($tipoPlan != 0) {
            $ptipoPlan = $em->getRepository('NomencladorBundle:NomTipoplan')->find($tipoPlan);
            $datPlanAcopio->setIdtipoplan($ptipoPlan);
        }
        if ($ejercicio != 0) {
            $pejercicio = $em->getRepository('NomencladorBundle:NomEjercicio')->find($ejercicio);
            $datPlanAcopio->setIdejercicio($pejercicio);
        }
        $prods = $this->get('nomenclador.nomproducto')->getProdHjosPlanParaFormType($ppadre, true);
        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanAcopioType', $datPlanAcopio, array('productos' => $prods));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $datPlanAcopio->setHoja(1);
            if (is_null($ppadre))
                $datPlanAcopio->setIdpadre(0);
            else {
                $datPlanAcopio->setIdpadre($ppadre->getIdplanacopio());
                $ppadre->setHoja(0);
                $em->persist($ppadre);
            }
            $em->persist($datPlanAcopio);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                $params['tabla'] = 'DatPlanAcopio';
                $params['campoId'] = 'idplanacopio';
                $padres = $this->get('planes_partes')->getObjPadres($datPlanAcopio, [], $params);
                $padres = array_reverse($padres);
                array_push($padres, $datPlanAcopio->getIdplanacopio());
                $this->get('session')->set('padres', $padres);
                return $this->redirectToRoute('planacopio_index', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    'remember' => 1
                ));
            } else
                return $this->redirectToRoute('planacopio_new', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    'padre' => $padre
                ));
        }
        return $this->render('ParteDiarioBundle:datplanacopio/new.html.twig', array(
            'datPlanAcopio' => $datPlanAcopio,
            'form' => $form->createView(),
            'title' => 'Adicionar',
            'ueb' => $ueb,
            'tipoplan' => $tipoPlan,
            'ejercicio' => $ejercicio

        ));
    }

    /**
     * Finds and displays a DatPlanAcopio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PLANACOPIO')")
     */
    public function showAction(DatPlanAcopio $datPlanAcopio)
    {
        $deleteForm = $this->createDeleteForm($datPlanAcopio);
        return $this->render('ParteDiarioBundle:datplanacopio/show.html.twig', array(
            'datPlanAcopio' => $datPlanAcopio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DatPlanAcopio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PLANACOPIO')")
     */
    public function editAction(Request $request, DatPlanAcopio $datPlanAcopio, $ejercicio, $tipoPlan, $ueb)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatPlanAcopioType', $datPlanAcopio);
        $editForm->remove('agregar');
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($datPlanAcopio);
            $em->flush();
            $params['tabla'] = 'DatPlanAcopio';
            $params['campoId'] = 'idplanacopio';
            $padres = $this->get('planes_partes')->getObjPadres($datPlanAcopio, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $datPlanAcopio->getIdplanacopio());
            $this->get('session')->set('padres', $padres);

            return $this->redirectToRoute('planacopio_index', array(
                'ueb' => $ueb,
                'tipoplan' => $tipoPlan,
                'ejercicio' => $ejercicio,
                'remember' => 1
            ));
        }

        return $this->render('ParteDiarioBundle:datplanacopio/new.html.twig', array(
            'datPlanAcopio' => $datPlanAcopio,
            'form' => $editForm->createView(),
            'ueb' => $ueb,
            'tipoplan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'title' => 'Editar'
        ));
    }

    /**
     * Deletes a DatPlanAcopio entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANACOPIO')")
     */
    public function deleteAction(Request $request, DatPlanAcopio $datPlanAcopio)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datPlanAcopio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datPlanAcopio);
            $em->flush();
        }

        return $this->redirectToRoute('planacopio_index');
    }

    /**
     * Creates a form to delete a DatPlanAcopio entity.
     *
     * @param DatPlanAcopio $datPlanAcopio The DatPlanAcopio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANACOPIO')")
     */
    private function createDeleteForm(DatPlanAcopio $datPlanAcopio)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('planacopio_delete', array('id' => $datPlanAcopio->getIdplanacopio())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function getAllHijos($id)//solo hijos
    {
        $em = $this->getDoctrine()->getManager();
        $arraR = array();
        $dqlhijos = 'SELECT g.idplanacopio
            FROM ParteDiarioBundle:DatPlanAcopio g
            WHERE g.idpadre = ' . $id;

        $consulta = $em->createQuery($dqlhijos);
        if (count($consulta->getResult()) > 0) {
            $arraR = $consulta->getResult();
            foreach ($arraR as $pr) {
                $this->getAllHijos($pr['idplanacopio']);
                try {
                    $this->get('parte_diario.comun_service')->deleteOne('ParteDiarioBundle:DatPlanAcopio', $pr['idplanacopio']);
                } catch (ForeignKeyConstraintViolationException $e) {
                    $msg = 'error';
                }
            }
            return;
        } else
            return;

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANACOPIO')")
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
                $this->get('parte_diario.comun_service')->deleteOne('ParteDiarioBundle:DatPlanAcopio', $f);
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PLANACOPIO')")
     */
    public function detallePlanAction(Request $request, DatPlanAcopio $plan, $ejercicio, $tipoPlan, $ueb)
    {
        $total = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() +
            $plan->getJunio() + $plan->getJulio() + $plan->getAgosto() + $plan->getSeptiembre() + $plan->getOctubre()
            + $plan->getNoviembre() + $plan->getDiciembre();
        $diferencia = $plan->getCantidad() - $total;
        return $this->render('ParteDiarioBundle:datplanacopio:detalle_plan.html.twig', array(
            'plan' => $plan,
            'ueb' => $ueb,
            'tipoplan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'diferencia' => number_format($diferencia, 3)
        ));
    }
}
