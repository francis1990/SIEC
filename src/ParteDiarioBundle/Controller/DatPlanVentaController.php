<?php

namespace ParteDiarioBundle\Controller;

use EnumsBundle\Entity\EnumMeses;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use ParteDiarioBundle\Entity\DatPlanVenta;
use ParteDiarioBundle\Form\DatPlanVentaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PHPExcel;
use PHPExcel_IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/*No notificar errores*/
error_reporting(0);

date_default_timezone_set("America/Havana");

/**
 * DatPlanVenta controller.
 *
 */
class DatPlanVentaController extends Controller
{
    /**
     * Lists all DatPlanVenta entities.
     *
     */
    private $arreglo = array();
    private static $meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre', 'valor');

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANVENTA')")
     */
    public function indexAction(Request $request, $ejercicio, $tipoPlan, $ueb, $fisicoval, $remember)
    {
        $datPlanVentum = new DatPlanVenta();
        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanVentaType', $datPlanVentum);
        $em = $this->getDoctrine()->getManager();
        $sinHijos = $em->getRepository('ParteDiarioBundle:DatPlanVenta')->findByHoja(false);
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        return $this->render('ParteDiarioBundle:datplanventa/index.html.twig', array(
            'form' => $form->createView(),
            'ueb' => $ueb,
            'tipoplan' => $tipoPlan,
            'ejercicio' => $ejercicio,
            'categoriaplan' => 0,
            'validar' => count($sinHijos) > 0,
            'meses' => $meses,
            'fisicoval' => $fisicoval,
            'remember' => $remember
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANVENTA')")
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

        return $this->render('ParteDiarioBundle:datplanventa:validar.html.twig', array(
            'data' => $data,
            'ueb' => $ueb ? $ueb->getNombre() : '',
            'tipoplan' => $tipo ? $tipo->getNombre() : '',
            'ejercicio' => $ejer ? $ejer->getNombre() : '',
            'fecha' => date("d-m-Y"),
            'hora' => date("h-i-s")
        ));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANVENTA')")
     */
    public function validarAction(Request $request)
    {

        $dat = $request->query->get('dat');
        $prod = $this->validarAuxiliarAction($request, $dat);

        return new JsonResponse(array('data' => $prod));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANVENTA')")
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
        $dql = 'SELECT g.idplanventa,pr.idproducto,um.abreviatura umed ,md.nombre mond,g.valor,cli.nombre cliente,
                  cli.idgrupointeres,enti.nombre origen,enti.identidad,g.is_val,
                g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre
                ,g.octubre,g.noviembre,g.diciembre
            FROM ParteDiarioBundle:DatPlanVenta g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idgrupocliente cli
            LEFT JOIN g.identidad enti
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $padre . $where;
        $consulta = $em->createQuery($dql);
        $DatPlanVenta = $consulta->getResult();

        $hijos = $this->getHijos($DatPlanVenta, $where, $em);

        if (count($hijos) > 0) {
            $this->getArreglo($hijos, $where, $em);
            foreach ($this->arreglo as $pr) {
                $total = 0;
                $res = array();
                $res[] = $pr['cliente'];
                $producto = $em->getRepository('NomencladorBundle:NomProducto')->find($pr['idproducto']);
                $res[] = $producto ? $producto->getNombre() : '';
                $res[] = $pr['umed'];
                $res[] = $pr['origen'];
                $res[] = $pr['mond'];
                $res[] = $pr['is_val'];
                $meses = self::$meses;
                foreach ($meses as $mes) {
                    if ($pr[$mes] != 0 && $mes != 'valor') {
                        $res[] = $pr[$mes];
                    } else {
                        if ($mes != 'valor') {
                            $res[] = '';
                        }
                    }
                    $mes != 'valor' ? $total += $pr[$mes] : '';
                }
                $res[] = $pr['valor'];
                $res[] = $total;
                $res[] = $pr['valor'] - $total;
                $prod[] = $res;
            }
        }
        return $prod;
    }

    public function getHijos($DatPlanVenta, $where, $em)
    {
        $arraR = array();
        foreach ($DatPlanVenta as $pr) {
            $dqlhijos = 'SELECT g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,
                            g.septiembre,g.octubre,g.valor,g.noviembre,g.diciembre,
                            cli.nombre cliente,cli.idgrupointeres,enti.nombre origen,enti.identidad,g.is_val,
                          g.idplanventa,pr.idproducto,um.abreviatura umed,md.nombre mond
            FROM ParteDiarioBundle:DatPlanVenta g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idgrupocliente cli
            LEFT JOIN g.identidad enti
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $where . ' and( g.idpadre = ' . $pr['idplanventa'] . ' or g.idplanventa = ' . $pr['idplanventa'] . ')
            ORDER BY  g.idplanventa';

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
        $arraResult['idplanventa'] = '';
        $arraResult['cliente'] = 'Diferencia';
        $arraResult['idproducto'] = '';
        $arraResult['umed'] = '';
        $arraResult['origen'] = '';
        $arraResult['mond'] = '';
        $arraResult['is_val'] = '';

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
                $arraAuxi['idplanventa'] = '';
                $arraAuxi['cliente'] = 'Sumatoria';
                $arraAuxi['idproducto'] = '';
                $arraAuxi['umed'] = '';
                $arraAuxi['origen'] = '';
                $arraAuxi['mond'] = '';
                $arraAuxi['is_val'] = '';

                foreach ($meses as $mes) {
                    isset($arraAuxi[$mes])
                        ? ($arraAuxi[$mes] += $o[$f][$mes])
                        : $arraAuxi[$mes] = $o[$f][$mes];
                    $mes != 'valor' ? $totalP += $o[$f][$mes] : $total = $total;
                }
            }
            foreach ($meses as $mes) {
                $mes != 'valor' ? $totalP += $o[0][$mes] : $totalP = $totalP;
            }
            if (count($o) > 1 || (($totalP - $o[0]['valor']) != 0)) {
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
            if ((count($o) <= 1 && (($o[0]['valor'] - $totalP) == 0))) {
                return;
            }
        }
        return $arraResult;
    }

    public function getNietos($DatPlanVenta, $where, $em)
    {
        $arraR = array();
        $dqlhijos = 'SELECT g.enero,g.febrero,g.marzo,g.abril,g.mayo,g.junio,g.julio,g.agosto,g.septiembre,g.octubre,
                            g.valor,g.noviembre,g.diciembre,
                             cli.nombre cliente,cli.idgrupointeres,enti.nombre origen,enti.identidad,g.is_val,
                          g.idplanventa,pr.idproducto,um.abreviatura umed,md.nombre mond
            FROM ParteDiarioBundle:DatPlanVenta g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idunidadmedida um
            LEFT JOIN g.idgrupocliente cli
            LEFT JOIN g.identidad enti
            LEFT JOIN g.idmonedadestino md
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            WHERE' . $where . ' and( g.idpadre = ' . $DatPlanVenta['idplanventa'] . ' or g.idplanventa = ' . $DatPlanVenta['idplanventa'] . ')
            ORDER BY  g.idplanventa';

        $consulta = $em->createQuery($dqlhijos);
        if (count($consulta->getResult()) > 0)
            $arraR[] = $consulta->getResult();
        return $arraR;
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANVENTA')")
     */
    public function listarPlanPAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $dat = $request->query->get('dat');
        $parent = $request->query->get('parent') ? $request->query->get('parent') : 0;
        $prod = array();
        /*if ($dat[0] == 0 || $dat[1] == 0 || $dat[2] == 0) {
            return new JsonResponse(array('data' => $prod, 'total' => 0));
        }*/

        $where = ' 1=1';
        $where .= ' AND g.idpadre=' . $parent;
        $where .= ($dat['ejercicio'] != 0) ? (' and eje.idejercicio=') . $dat['ejercicio'] : '';
        $where .= ($dat['ueb'] != 0) ? (' and ueb.idueb = ' . $dat['ueb']) : '';
        $where .= ($dat['plan'] != 0) ? (' and pla.idtipoplan = ' . $dat['plan']) : '';
        $where .= ($dat['valor'] != -1) ? (' and g.is_val = ' . $dat['valor']) : '';

        $dql = 'SELECT g.idplanventa,eje.idejercicio,pr.idproducto,pla.idtipoplan,ueb.idueb,u.idunidadmedida,g.valor,g.enero,g.febrero,g.marzo,g.abril,
                       g.mayo,g.junio,g.agosto,g.julio,g.septiembre,g.octubre,g.noviembre,g.diciembre,g.hoja,m.id idmoneda,g.hoja,g.is_val,gru.idgrupointeres,ent.identidad
            FROM ParteDiarioBundle:DatPlanVenta g
            LEFT JOIN g.idtipoplan pla
            LEFT JOIN g.idproducto pr
            LEFT JOIN g.idejercicio eje
            LEFT JOIN g.idueb ueb
            LEFT JOIN g.idunidadmedida u
            LEFT JOIN g.idmonedadestino m
            LEFT JOIN g.idgrupocliente gru
            LEFT JOIN g.identidad ent
            WHERE' . $where;
        $consulta = $em->createQuery($dql);
        $tol = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $DatPlanventa = $consulta->getResult();
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        foreach ($DatPlanventa as $pr) {
            $res = array();
            $res[] = $pr['idplanventa'];
            $mant = date('m', strtotime('-1 month'));
            $mact = date('m');
            $msig = date('m', strtotime('+1 month'));
            $res[] = $pr['idplanventa'];

            $pr['idgrupointeres'] ? $cliente = $em->getRepository('NomencladorBundle:NomGrupointeres')->find($pr['idgrupointeres']) : $cliente = '';
            $res[] = $cliente ? $cliente->getNombre() : '';

            $pr['idproducto'] ? $producto = $em->getRepository('NomencladorBundle:NomProducto')->find($pr['idproducto']) : $producto = '';
            $res[] = $producto ? $producto->getNombre() : '';

            $pr['idunidadmedida'] ? $um = $em->getRepository('NomencladorBundle:NomUnidadmedida')->find($pr['idunidadmedida']) : $um = '';
            $res[] = $um ? $um->getAbreviatura() : '';

            $pr['identidad'] ? $entidad = $em->getRepository('NomencladorBundle:NomEntidad')->find($pr['identidad']) : $entidad = '';
            $res[] = $entidad ? $entidad->getNombre() : '';

            $pr['idmoneda'] ? $md = $em->getRepository('NomencladorBundle:NomMonedadestino')->find($pr['idmoneda']) : $md = '';
            $res[] = $md ? $md->getNombre() : 'Total';

            $numde = $pr['is_val'] ? 5 : 3;

            $res[] = $pr['is_val'];
            $valor = $pr['valor'] ? $pr['valor'] : 0;
            $res[] = $valor;
            $res[] = number_format($pr[strtolower($meses[$mant])], 3);
            $res[] = number_format($pr[strtolower($meses[$mact])], 3);
            $res[] = number_format($pr[strtolower($meses[$msig])], 3);
            /*$res[] = $pr->getEnero();
            $res[] = $pr->getFebrero();
            $res[] = $pr->getMarzo();
            $res[] = $pr->getAbril();
            $res[] = $pr->getMayo();
            $res[] = $pr->getJunio();
            $res[] = $pr->getJulio();
            $res[] = $pr->getAgosto();
            $res[] = $pr->getSeptiembre();
            $res[] = $pr->getOctubre();
            $res[] = $pr->getNoviembre();
            $res[] = $pr->getDiciembre();*/
            $total = $pr['enero'] + $pr['febrero'] + $pr['marzo'] + $pr['abril'] +
                $pr['mayo'] + $pr['junio'] + $pr['julio']
                + $pr['agosto'] + $pr['septiembre'] + $pr['octubre'] +
                $pr['noviembre'] + $pr['diciembre'];
            $dif = $valor - $total;
            $res[] = round(floatval($dif), $numde, PHP_ROUND_HALF_DOWN);
            $res[] = $pr['hoja'];
            $prod[] = $res;
        }
        //  $tol = count($prod);
        return new JsonResponse(array('data' => $prod, 'total' => $tol));
    }

    /**
     * Creates a new DatPlanVenta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_PLANVENTA')")
     */
    public function newAction(Request $request, $ejercicio, $tipoPlan, $ueb, $fisicoval, $padre)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $ent = new DatPlanVenta();
        $em = $this->getDoctrine()->getManager();

        if ($padre != null) {
            $ppadre = $em->getRepository('ParteDiarioBundle:DatPlanVenta')->find($padre);
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

        if ($fisicoval != 2) {
            $ent->setIsVal($fisicoval == 1 ? true : false);
        }

        $prods = $this->get('nomenclador.nomproducto')->getProdHjosPlanParaFormType($ppadre);
        $form = $this->createForm('ParteDiarioBundle\Form\DatPlanVentaType', $ent, array('productos' => $prods));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ent->setHoja(1);
            if (is_null($padre))
                $ent->setIdpadre(0);
            else {
                $ent->setIdpadre($ppadre->getIdplanventa());
                $ppadre->setHoja(0);
                $em->persist($ppadre);
            }
            $em->persist($ent);
            $em->flush();
            $this->addFlash('success', 'Se ha insertado el elemento correctamente.');
            if ($form->get('aceptar')->isClicked()) {
                $params['tabla'] = 'DatPlanVenta';
                $params['campoId'] = 'idplanventa';
                $padres = $this->get('planes_partes')->getObjPadres($ent, [], $params);
                $padres = array_reverse($padres);
                array_push($padres, $ent->getIdplanventa());
                $this->get('session')->set('padres', $padres);
                return $this->redirectToRoute('planventa_index', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    'fisicoval' => $fisicoval,
                    'remember' => 1
                ));
            } else
                return $this->redirectToRoute('planventa_new', array(
                    'ejercicio' => $ejercicio,
                    'tipoPlan' => $tipoPlan,
                    'ueb' => $ueb,
                    'padre' => $padre,
                    'fisicoval' => $fisicoval
                ));
        }
        return $this->render('ParteDiarioBundle:DatPlanVenta:new.html.twig', array(
            'datPlanVentum' => $ent,
            'form' => $form->createView(),
            'title' => 'Adicionar',
            'ejercicio' => $ejercicio,
            'tipoPlan' => $tipoPlan,
            'ueb' => $ueb,
            'fisicoval' => $fisicoval
        ));
    }

    /**
     * Finds and displays a DatPlanVenta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_PLANVENTA')")
     */
    public function showAction(DatPlanVenta $datPlanVentum)
    {
        $deleteForm = $this->createDeleteForm($datPlanVentum);
        return $this->render('ParteDiarioBundle:datplanventa/show.html.twig', array(
            'datPlanVentum' => $datPlanVentum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DatPlanVenta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_PLANVENTA')")
     */
    public function editAction(Request $request, DatPlanVenta $DatPlanVenta, $ejercicio, $tipoPlan, $ueb, $fisicoval)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $editForm = $this->createForm('ParteDiarioBundle\Form\DatPlanVentaType', $DatPlanVenta);
        $editForm->remove('agregar');
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($DatPlanVenta);
            $em->flush();
            $params['tabla'] = 'DatPlanVenta';
            $params['campoId'] = 'idplanventa';
            $padres = $this->get('planes_partes')->getObjPadres($DatPlanVenta, [], $params);
            $padres = array_reverse($padres);
            array_push($padres, $DatPlanVenta->getIdplanventa());
            $this->get('session')->set('padres', $padres);
            return $this->redirectToRoute('planventa_index', array(
                'ejercicio' => $ejercicio,
                'tipoPlan' => $tipoPlan,
                'ueb' => $ueb,
                'fisicoval' => $fisicoval,
                'remember' => 1
            ));
        }
        return $this->render('ParteDiarioBundle:DatPlanVenta:new.html.twig', array(
            'form' => $editForm->createView(),
            'title' => 'Editar',
            'ejercicio' => $ejercicio,
            'tipoPlan' => $tipoPlan,
            'ueb' => $ueb,
            'fisicoval' => $fisicoval,
        ));
    }

    /**
     * Deletes a DatPlanVenta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANVENTA')")
     */
    public function deleteAction(Request $request, DatPlanVenta $datPlanVentum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datPlanVentum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datPlanVentum);
            $em->flush();
        }

        return $this->redirectToRoute('planventa_index');
    }

    /**
     * Creates a form to delete a DatPlanVenta entity.
     *
     * @param DatPlanVenta $datPlanVentum The DatPlanVenta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANVENTA')")
     */
    private function createDeleteForm(DatPlanVenta $datPlanVentum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('planventa_delete', array('id' => $datPlanVentum->getIdplanventa())))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function getAllHijos($id)//solo hijos
    {
        $em = $this->getDoctrine()->getManager();
        $arraR = array();
        $dqlhijos = 'SELECT g.idplanventa
            FROM ParteDiarioBundle:DatPlanVenta g
            WHERE g.idpadre = ' . $id;

        $consulta = $em->createQuery($dqlhijos);
        if (count($consulta->getResult()) > 0) {
            $arraR = $consulta->getResult();
            foreach ($arraR as $pr) {
                $this->getAllHijos($pr['idplanventa']);
                try {
                    $this->get('parte_diario.comun_service')->deleteOne('ParteDiarioBundle:DatPlanVenta', $pr['idplanventa']);
                } catch (ForeignKeyConstraintViolationException $e) {
                    $msg = 'error';
                }
            }
            return;
        } else
            return;

    }

    /**
     * @Security("is_granted('ROLE_ELIMINAR_PLANVENTA')")
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
                $this->get('parte_diario.comun_service')->deleteOne('ParteDiarioBundle:DatPlanVenta', $f);
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * @Security("is_granted('ROLE_LISTAR_PLANVENTA')")
     */
    public function listarHijos($entidad, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $consulta = $em->createQuery('select cp
                                          from  ' . $entidad . '  cp
                                          where cp.idpadre =' . $id);
        return $consulta->getResult();
    }

    /**
     *
     * @Security("is_granted('ROLE_LISTAR_PLANVENTA')")
     */
    public function detallePlanAction(Request $request, DatPlanVenta $plan, $ejercicio, $tipoPlan, $ueb, $fisicoval)
    {
        $total = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() +
            $plan->getJunio() + $plan->getJulio() + $plan->getAgosto() + $plan->getSeptiembre() + $plan->getOctubre()
            + $plan->getNoviembre() + $plan->getDiciembre();
        $diferencia = $plan->getValor() - $total;
        return $this->render('ParteDiarioBundle:datplanventa:detalle_plan.html.twig', array(
            'plan' => $plan,
            'ejercicio' => $ejercicio,
            'tipoPlan' => $tipoPlan,
            'ueb' => $ueb,
            'fisicoval' => $fisicoval,
            'diferencia' => number_format($diferencia, 3)
        ));
    }
}
