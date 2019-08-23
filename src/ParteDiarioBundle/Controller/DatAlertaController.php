<?php

namespace ParteDiarioBundle\Controller;


use AdminBundle\Entity\Usuario;
use DoctrineExtensions\Query\Mysql\Now;
use ParteDiarioBundle\Entity\DatAlertaAccion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use ParteDiarioBundle\Entity\DatAlerta;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\DateTime;

/*No notificar errores*/
error_reporting(0);


/**
 * DatAlerta controller.
 *
 */
class DatAlertaController extends Controller
{
    /**
     * Lists all DatAlerta entities.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ALERTA')")
     */
    public function indexAction($suprimida)
    {
        $em = $this->getDoctrine()->getManager();

        $datAlertas = $em->getRepository('ParteDiarioBundle:DatAlerta')->findAll();

        return $this->render('ParteDiarioBundle:datalerta:index.html.twig', array(
            'datAlertas' => $datAlertas,
            'suprimida' => $suprimida == null ? 0 : $suprimida
        ));
    }

    /**
     * Creates a new DatAlerta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ADICIONAR_ALERTA')")
     */
    public function newalertAction(Request $request, $tipo)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $datAlertum = new DatAlerta();
        $datAlertum->setActividad($tipo);
        $hoy = $this->ultimo_parte_produccion();
        $datAlertum->setFecha($hoy);
        $form = null;
        if ($tipo == 'Produccion') {
            $form = $this->createForm('ParteDiarioBundle\Form\ProductionAlertType', $datAlertum);

        } elseif ($tipo == 'Aseguramiento') {
            $form = $this->createForm('ParteDiarioBundle\Form\AseguramientoAlertType', $datAlertum);
            $tipo = 'Aseguramientos';

        } elseif ($tipo == 'Venta') {
            $form = $this->createForm('ParteDiarioBundle\Form\VentaAlertType', $datAlertum);
            $tipo = 'Ventas';

        } elseif ($tipo == 'Transporte') {
            $form = $this->createForm('ParteDiarioBundle\Form\DatAlertaTransporteType', $datAlertum);
            $tipo = 'Transportes';

        } elseif ($tipo == 'Portadores') {
            $form = $this->createForm('ParteDiarioBundle\Form\DatAlertaPortadorType', $datAlertum);
            $tipo = "Portadores energéticos";

        } elseif ($tipo == 'Economia') {
            $form = $this->createForm('ParteDiarioBundle\Form\DatAlertaEconomiaType', $datAlertum);

        }
        $form->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
            'label' => 'Aceptar',
            'label_format' => 'btn-add',
            'attr' => array(
                'class' => "btn btn-primary  disabled",
                'widget' => 'glyphicon glyphicon-ok icon-white',
                'id' => 'btn_aceptar',
            ),
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($datAlertum);
            $em->flush();
            /*  if ($form->get('aceptar')->isClicked())*/
            return $this->redirectToRoute('parte_alerta_index');
            /*else
                return $this->redirectToRoute('parte_alerta_new',array($request,$tipo));*/
        }
        return $this->render('ParteDiarioBundle:datalerta:new.html.twig', array(
            'form' => $form->createView(),
            'name' => $form->getName(),
            'tipo' => $tipo,
            'accion' => 'Adicionar'
        ));
    }

    /**
     * Finds and displays a DatAlerta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ALERTA')")
     */
    public function showAction(DatAlerta $datAlertum)
    {
        $deleteForm = $this->createDeleteForm($datAlertum);

        return $this->render('ParteDiarioBundle:datalerta:show.html.twig', array(
            'datAlertum' => $datAlertum,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Finds and displays a DatAlerta entity.
     *
     */
    public function show_by_userAction(DatAlerta $datAlertum)
    {
        $deleteForm = $this->createDeleteForm($datAlertum);

        return $this->render('ParteDiarioBundle:datalerta:show.html.twig', array(
            'datAlertum' => $datAlertum,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DatAlerta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_MODIFICAR_ALERTA')")
     */
    public function editAction(Request $request, DatAlerta $datAlertum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $tipo = $datAlertum->getActividad();
        $deleteForm = $this->createDeleteForm($datAlertum);

        if ($tipo == 'Produccion') {
            $editForm = $this->createForm('ParteDiarioBundle\Form\ProductionAlertType', $datAlertum);
        } elseif ($tipo == 'Aseguramiento') {
            $editForm = $this->createForm('ParteDiarioBundle\Form\AseguramientoAlertType', $datAlertum);
        } elseif ($tipo == 'Venta') {
            $editForm = $this->createForm('ParteDiarioBundle\Form\VentaAlertType', $datAlertum);
        } elseif ($tipo == 'Transporte') {
            $editForm = $this->createForm('ParteDiarioBundle\Form\DatAlertaTransporteType', $datAlertum);
        } elseif ($tipo == 'Portadores') {
            $editForm = $this->createForm('ParteDiarioBundle\Form\DatAlertaPortadorType', $datAlertum);
        } elseif ($tipo == 'Economia') {
            $editForm = $this->createForm('ParteDiarioBundle\Form\DatAlertaEconomiaType', $datAlertum);
        }
        $editForm->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
            'label' => 'Aceptar',
            'label_format' => 'btn-add',
            'attr' => array(
                'class' => "btn btn-primary  disabled",
                'widget' => 'glyphicon glyphicon-ok icon-white',
                'id' => 'btn_aceptar',
            ),
        ));
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($datAlertum);
            $em->flush();
            return $this->redirectToRoute('parte_alerta_index');
        }
        return $this->render('ParteDiarioBundle:datalerta:new.html.twig', array(
            'datAlertum' => $datAlertum,
            'form' => $editForm->createView(),
            'name' => $editForm->getName(),
            'tipo' => $tipo,
            'accion' => 'Editar'
        ));
    }

    /**
     * Deletes a DatAlerta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ALERTA')")
     */
    public function deleteAction(Request $request, DatAlerta $datAlertum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $form = $this->createDeleteForm($datAlertum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($datAlertum);
            $em->flush();
        }

        return $this->redirectToRoute('parte_alerta_index');
    }


    /**
     * Creates a form to delete multiple a NomEspecifico entity.
     *
     * @param DatAlerta $datalerta The DatAlerta entity
     *
     * @return \
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ALERTA')")
     */
    public function alertaEliminarAction(Request $request)

    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        $em = $this->getDoctrine()->getManager();
        $msg = 'exito';
        $ids = $request->request->get('id');
        if (!is_array($ids))
            $ids = [$ids];
        foreach ($ids as $f) {
            try {
                $objElim = $em->getRepository('ParteDiarioBundle:DatAlerta')->find($f);
                $em->remove($objElim);
                $em->flush();
            } catch (ForeignKeyConstraintViolationException $e) {
                $msg = 'error';
            }
        }
        return new JsonResponse(array('respuesta' => $msg));
    }

    /**
     * Creates a form to delete a DatAlerta entity.
     *
     * @param DatAlerta $datAlertum The DatAlerta entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /**
     * @Security("is_granted('ROLE_ELIMINAR_ALERTA')")
     */
    private function createDeleteForm(DatAlerta $datAlertum)
    {
        /*Validar que un mismo usuario no este autenticado en PC distintas.*/
        if (!$this->get('admin.service.check.security')->ChequearSession($this->getRequest()->getSession())) {
            return $this->redirectToRoute('portada');
        }
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parte_alerta_delete', array('id' => $datAlertum->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Render the alerts for an user
     */
    public function alertsAction($user)
    {
        $em = $this->getDoctrine()->getManager();
        $alerta = $em->getRepository('ParteDiarioBundle:DatAlerta')->findSinAccion($user);
        $arl = array();
        foreach ($alerta as $alert) {
            if ($alert->getUsuarios()->contains($user)) {

                if ($alert->getActividad() == 'Aseguramiento' && $this->aseguramiento_operation($alert))
                    $arl[] = array('color' => 'green', 'texto' => $alert->getDescripcion(), 'id' => $alert->getId());
                if ($alert->getActividad() == 'Produccion' && $this->produccion_operation($alert))
                    $arl[] = array('color' => 'red', 'texto' => $alert->getDescripcion(), 'id' => $alert->getId());
                if ($alert->getActividad() == 'Venta' && $this->venta_operation($alert))
                    $arl[] = array('color' => 'orange', 'texto' => $alert->getDescripcion(), 'id' => $alert->getId());
                if ($alert->getActividad() == 'Transporte' && $this->transporte_operation($alert))
                    $arl[] = array('color' => 'blue', 'texto' => $alert->getDescripcion(), 'id' => $alert->getId());

                if ($alert->getActividad() == 'Portadores' && $this->portador_operation($alert) ){

                    $arl[] = array('color' => 'black', 'texto' => $alert->getDescripcion(), 'id' => $alert->getId());
                }
                if ($alert->getActividad() == 'Economia' && $this->economia_operation($alert))
                    $arl[] = array('color' => 'black', 'texto' => $alert->getDescripcion(), 'id' => $alert->getId());
            }
        }
        return $this->render('@ParteDiario/datalerta/listadoalerta.html.twig', array('alertas' => $arl, 'count' => count($arl)));
    }

    private function listar_usuarios($alert)
    {
        $em = $this->getDoctrine()->getManager();
        $where = '1=1';
        $where .= ($alert != '') ? (' AND a.idalerta =' . $alert) : '';
        $dql = 'SELECT u.usuario
            FROM AdminBundle:Usuario u JOIN u.alertas a
            WHERE ' . $where;
        $consulta = $em->createQuery($dql);
        return $consulta->getResult();
    }

    private function ultimo_parte()
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT a.fecha
            FROM ParteDiarioBundle:DatParteAseguramiento AS a ORDER BY a.fecha desc ';
        $consulta = $em->createQuery($dql);
        $partes[] = $consulta->getResult();
        $cont = count($partes[0]);
        if ($cont > 0) {
            $fecha = ($partes[0][$cont - 1]);
            return $fecha["fecha"];
        } else {
            return date('d/m/Y');
        }


    }

    private function ultimo_parte_produccion()
    {

        $em = $this->getDoctrine()->getManager();

        $dql = 'SELECT a.fecha
            FROM ParteDiarioBundle:DatPartediarioProduccion AS a ORDER BY a.fecha desc';

        $consulta = $em->createQuery($dql);

        $partes[] = $consulta->getResult();


        $cont = count($partes[0]);
        if ($cont > 0) {
            $fecha = ($partes[0][$cont - 1]);
            return $fecha["fecha"];
        } else {
            return null;
        }


    }

    private function ultimo_parte_economia()
    {

        $em = $this->getDoctrine()->getManager();

        $dql = 'SELECT a.fecha
            FROM ParteDiarioBundle:DatPartediarioEconomia AS a ORDER BY a.fecha desc';

        $consulta = $em->createQuery($dql);

        $partes[] = $consulta->getResult();

        $cont = count($partes[0]);
        if ($cont > 0) {
            $fecha = ($partes[0][$cont - 1]);
            return $fecha["fecha"];
        } else {
            return null;
        }


    }

    public function getGrupos($id, $result)
    {
        $em = $this->getDoctrine()->getManager();
        $arreglo = array();

        $dql = 'SELECT ent.identidad, a.hoja, a.idgrupointeres
            FROM NomencladorBundle:NomGrupointeres a
            JOIN a.identidad ent
            WHERE a.idpadre = ' . $id;

        $consulta = $em->createQuery($dql);
        if (count($consulta->getResult()) > 0) {
            $arreglo = $consulta->getResult();
            foreach ($arreglo as $pr) {
                if ($pr['hoja'] == true)

                    $result[] = $pr['identidad'];
                else

                    $this->getGrupos($pr['idgrupointeres']);
            }
        }
        return $result;

    }

    public function getHijosGrupo($id)
    {
        $em = $this->getDoctrine()->getManager();
        $arreglo = array();
        $result = array();
        $dql = 'SELECT a.idgrupointeres
            FROM NomencladorBundle:NomGrupointeres a
            WHERE  a.idpadre = ' . $id;

        $consulta = $em->createQuery($dql);
        if (count($consulta->getResult()) > 0) {
            $arreglo = $consulta->getResult();
            foreach ($arreglo as $pr) {

                $result[] = $this->getGrupos($pr['idgrupointeres'], $result);
            }
        }

        return $result;

    }

    private function partes_produccion_mes($mes, $ano, $toYear)
    {


        $em = $this->getDoctrine()->getManager();
        if (!$toYear) {
            $sql = 'SELECT a
               FROM ParteDiarioBundle:DatPartediarioProduccion AS a WHERE YEAR(a.fecha) = ?1 AND MONTH(a.fecha) = ?2';
        } else {
            $sql = 'SELECT a
               FROM ParteDiarioBundle:DatPartediarioProduccion AS a WHERE YEAR(a.fecha) = ?1 AND MONTH(a.fecha) < = ?2';
        }

        $consulta = $em->createQuery($sql);
        $consulta->setParameter(2, $mes);
        $consulta->setParameter(1, $ano);

        $partes = $consulta->getResult();


        $cont = count($partes);


        if ($cont > 0) {
            return $partes;
        } else {
            return null;
        }
    }



    private function partes_venta_mes($mes, $ano, $toYear)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$toYear) {
            $sql = 'SELECT a
               FROM ParteDiarioBundle:DatParteVenta AS a WHERE YEAR(a.fecha) = ?1 AND MONTH(a.fecha) = ?2';
        } else {
            $sql = 'SELECT a
               FROM ParteDiarioBundle:DatParteVenta AS a WHERE YEAR(a.fecha) = ?1 AND MONTH(a.fecha) <= ?2';
        }

        $consulta = $em->createQuery($sql);
        $consulta->setParameter(2, $mes);
        $consulta->setParameter(1, $ano);

        $partes = $consulta->getResult();

        foreach ($partes as $parte) {

            $dql = 'SELECT a
               FROM ParteDiarioBundle:DatVentaProducto AS a WHERE a.parte = ?1';
            $consulta1 = $em->createQuery($dql);
            $consulta1->setParameter(1, $parte->getIdparte());

            $venta_producto[] = $consulta1->getResult();
        }

        $cont = count($venta_producto);


        if ($cont > 0) {
            return $venta_producto;
        } else {
            return null;
        }


    }

    private function ultimo_parte_transporte()
    {

        $em = $this->getDoctrine()->getManager();

        $dql = 'SELECT a.fecha
            FROM ParteDiarioBundle:DatParteTransporte AS a ORDER BY a.fecha desc';

        $consulta = $em->createQuery($dql);

        $partes[] = $consulta->getResult();

        $cont = count($partes[0]);
        if ($cont > 0) {
            $fecha = ($partes[0][$cont - 1]);
            return $fecha["fecha"];
        } else {
            return null;
        }


    }

    private function ultimo_parte_venta()
    {

        $em = $this->getDoctrine()->getManager();

        $dql = 'SELECT p.fecha
            FROM ParteDiarioBundle:DatVentaProducto AS a JOIN a.parte AS p ORDER BY p.fecha desc';

        $consulta = $em->createQuery($dql);

        $partes[] = $consulta->getResult();

        $cont = count($partes[0]);
        if ($cont > 0) {
            $fecha = ($partes[0][$cont - 1]);

            return $fecha["fecha"];
        } else {
            return null;
        }


    }

    private function dias_del_mes($mes, $ano)
    {
        $month = $mes;
        $year = $ano;

        return date("t", mktime(0, 0, 0, $month, 1, $year));
    }

    private function aseguramiento_operation($alert)
    {
        $operador = $alert->getOperador();
        $em = $this->getDoctrine()->getManager();
        $insumo = $alert->getInsumo();
        $this->ultimo_parte();
        if ($this->ultimo_parte() != null) {
            $existencia = $em->getRepository('ParteDiarioBundle:DatParteAseguramiento')->findOneBy(array('materiaprima' => (int)$insumo->getIdaseguramiento(), 'fecha' => $this->ultimo_parte()));
            if ($existencia != null) {
                if ($operador == '>') {
                    if ($existencia->getExistencia() > $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '<') {
                    if ($existencia->getExistencia() < $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '=') {
                    if ($existencia->getExistencia() == $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '!=') {
                    if ($existencia->getExistencia() != $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '>=') {
                    if ($existencia->getExistencia() >= $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '<=') {
                    if ($existencia->getExistencia() <= $alert->getCant())
                        return true;
                    else
                        return false;
                }
            }
        } else {
            return null;
        }
    }

    public function acumulado_Plan($plan, $month, $day)
    {

        $plan_acumulado = null;

        if ($month == "01") {
            $plan_acumulado = $plan->getEnero();
        } else if ($month == "02") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero();
        } else if ($month == "03") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo();
        } else if ($month == "04") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril();
        } else if ($month == "05") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo();
        } else if ($month == "06") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() + $plan->getJunio();
        } else if ($month == "07") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() + $plan->getJunio() + $plan->getJulio();
        } else if ($month == "08") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() + $plan->getJunio() + $plan->getJulio() + $plan->getAgosto();
        } else if ($month == "09") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() + $plan->getJunio() + $plan->getJulio() + $plan->getAgosto() + $plan->getSeptiembre();
        } else if ($month == "10") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() + $plan->getJunio() + $plan->getJulio() + $plan->getAgosto() + $plan->getSeptiembre() + $plan->getOctubre();
        } else if ($month == "11") {
            $plan_acumulado = $plan->getEnero() + $plan->getFebrero() + $plan->getMarzo() + $plan->getAbril() + $plan->getMayo() + $plan->getJunio() + $plan->getJulio() + $plan->getAgosto() + $plan->getSeptiembre() + $plan->getOctubre() + $plan->getNoviembre();
        } else if ($month == "12") {
            $plan_acumulado = $plan->getCantidad();
        }

        return $plan_acumulado;

    }

    private function produccion_operation($alert)
    {
        $operador = $alert->getOperador();
        $em = $this->getDoctrine()->getManager();
        $producto = $alert->getProducto();
        $ejercicio = $alert->getEjercicio();
        $tipo_plan = $alert->getTipoPlan();
        $moneda = $alert->getMoneda();
        $plan = $em->getRepository('ParteDiarioBundle:DatPlanProduccion')->findOneBy(array('idproducto' => $producto, 'idejercicio' => $ejercicio, 'idtipoplan' => $tipo_plan, 'idmonedadestino' => $moneda));
        $periodo = $alert->getPeriodo();

        if ($this->ultimo_parte_produccion() != null && $plan != null) {
            $mes_parte = date_format($this->ultimo_parte_produccion(), "m");
            $year_parte = date_format($this->ultimo_parte_produccion(), "Y");
            $dia_parte = date_format($this->ultimo_parte_produccion(), "d");

            $sistema = $em->getRepository('AdminBundle:DatConfig')->findAll();

            $mes_sistema = date_format($sistema[0]->getFechaTrabajo(), "m");
            $year_sistema = date_format($sistema[0]->getFechaTrabajo(), "Y");
            $dia_sistema = date_format($sistema[0]->getFechaTrabajo(), "d");


            $ultimo = $this->ultimo_parte_produccion();
            $cant = 0;
            if ($mes_parte == "01") {
                $cant = $plan->getEnero();
            } elseif ($mes_parte == "02") {
                $cant = $plan->getFebrero();
            } elseif ($mes_parte == "03") {
                $cant = $plan->getMarzo();
            } elseif ($mes_parte == "04") {
                $cant = $plan->getAbril();
            } elseif ($mes_parte == "05") {
                $cant = $plan->getMayo();
            } elseif ($mes_parte == "06") {
                $cant = $plan->getJunio();
            } elseif ($mes_parte == "07") {
                $cant = $plan->getJulio();
            } elseif ($mes_parte == "08") {
                $cant = $plan->getAgosto();
            } elseif ($mes_parte == "09") {
                $cant = $plan->getSeptiembre();
            } elseif ($mes_parte == "10") {
                $cant = $plan->getOctubre();
            } elseif ($mes_parte == "11") {
                $cant = $plan->getNoviembre();
            } elseif ($mes_parte == "12") {
                $cant = $plan->getDiciembre();
            }

            if ($cant == 0) {
                return null;
            }


            $real = $em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->findOneBy(array('producto' => $producto, 'moneda' => $moneda, 'fecha' => $ultimo));
            $real_sistema = $em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->findOneBy(array('producto' => $producto, 'moneda' => $moneda, 'fecha' => $sistema[0]->getFechaTrabajo()));


            if ($periodo == 'hoy') {
                if ($real_sistema != null) {
                    $cant = $cant / $this->dias_del_mes($mes_sistema, $year_sistema);

                    $porciento = ($real_sistema->getCantidad() * 100) / $cant;
                } else {
                    $porciento = 0;
                }


            }
            if ($periodo == 'mes') {
                $toYear = false;
                $total = 0;
                $partes_del_mes = $this->partes_produccion_mes($mes_parte, $year_parte, $toYear);
                foreach ($partes_del_mes as $select) {
                    if ($select->getProducto() == $producto && $select->getMoneda() == $moneda) {
                        $total += $select->getCantidad();
                    }
                }
                $porciento = ($total * 100) / $cant;

            }
            if ($periodo == 'mes_fecha') {
                $toYear = false;
                $total = 0;
                $cant = $cant / $this->dias_del_mes($mes_parte, $year_parte);
                $partes_del_mes = $this->partes_produccion_mes($mes_parte, $year_parte, $toYear);

                foreach ($partes_del_mes as $select) {
                    if ($select->getProducto() == $producto && $select->getMoneda() == $moneda) {
                        $total += $select->getCantidad();

                    }
                }


                $porciento = ($total * 100) / ($cant * $dia_parte);


            }

            if ($periodo == 'year_fecha') {
                $toYear = true;
                $total = 0;
                $plan_acumulado = 0;

                $cant = $this->acumulado_Plan($plan, $mes_parte, $dia_parte);
                $partes_del_mes = $this->partes_produccion_mes($mes_parte, $year_parte, $toYear);
                foreach ($partes_del_mes as $select) {
                    if ($select->getProducto() == $producto && $select->getMoneda() == $moneda) {
                        $total += $select->getCantidad();
                    }

                }

                $porciento = ($total * 100) / $cant;

            }

            if ($operador == '>') {
                if ($porciento > $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<') {
                if ($porciento < $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '=') {
                if ($porciento == $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<>') {
                if ($porciento != $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '>=') {
                if ($porciento >= $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<=') {
                if ($porciento <= $alert->getCant())
                    return true;
                else
                    return false;
            }

        } else {
            return null;
        }

    }

    private function venta_operation($alert)
    {
        $operador = $alert->getOperador();
        $em = $this->getDoctrine()->getManager();
        $producto = $alert->getProducto();
        $ejercicio = $alert->getEjercicio();
        $tipo_plan = $alert->getTipoPlan();
        $moneda = $alert->getMoneda();
        $grupo_interes = $alert->getGrupoInteres();
        $cliente = $alert->getCliente();
        $entidades[] = $this->getHijosGrupo($grupo_interes->getIdgrupointeres());
        $periodo = $alert->getPeriodo();
        $ueb = $alert->getEntidad();

        $plan = $em->getRepository('ParteDiarioBundle:DatPlanVenta')->findOneBy(array('idproducto' => $producto, 'idejercicio' => $ejercicio, 'idtipoplan' => $tipo_plan, 'idmonedadestino' => $moneda));

        if ($this->ultimo_parte_venta() != null && $plan != null) {
            $mes_parte = date_format($this->ultimo_parte_venta(), "m");
            $year_parte = date_format($this->ultimo_parte_venta(), "Y");
            $dia_parte = date_format($this->ultimo_parte_venta(), "d");
            $ultimo = $this->ultimo_parte_venta();
            $cant = 0;
            if ($mes_parte == "01") {
                $cant = $plan->getEnero();
            } elseif ($mes_parte == "02") {
                $cant = $plan->getFebrero();
            } elseif ($mes_parte == "03") {
                $cant = $plan->getMarzo();
            } elseif ($mes_parte == "04") {
                $cant = $plan->getAbril();
            } elseif ($mes_parte == "05") {
                $cant = $plan->getMayo();
            } elseif ($mes_parte == "06") {
                $cant = $plan->getJunio();
            } elseif ($mes_parte == "07") {
                $cant = $plan->getJulio();
            } elseif ($mes_parte == "08") {
                $cant = $plan->getAgosto();
            } elseif ($mes_parte == "09") {
                $cant = $plan->getSeptiembre();
            } elseif ($mes_parte == "10") {
                $cant = $plan->getOctubre();
            } elseif ($mes_parte == "11") {
                $cant = $plan->getNoviembre();
            } elseif ($mes_parte == "12") {
                $cant = $plan->getDiciembre();
            }
            if ($cant == 0) {
                return null;
            }
            if (is_null($cliente))
                $parte = $em->getRepository('ParteDiarioBundle:DatParteVenta')->findOneBy(array('fecha' => $ultimo, 'ueb' => $ueb));
            else {
                $parte = $em->getRepository('ParteDiarioBundle:DatParteVenta')->findOneBy(array('fecha' => $ultimo, 'ueb' => $ueb, 'cliente' => $cliente));

            }
//            $real = $em->getRepository('ParteDiarioBundle:DatVentaProducto')->findOneBy(array('parte'=> $parte->getIdparte(), 'producto' => $producto));


            if ($periodo == 'hoy') {
                $cont = 0;
                $toYear = false;
                $cant = $cant / $this->dias_del_mes($mes_parte, $year_parte);
                $total = 0;
                $partes_del_mes = $this->partes_venta_mes($mes_parte, $year_parte, $toYear);
                foreach ($partes_del_mes as $select) {
                    foreach ($select as $part)
                        $padre = $part->getParte();
                    if ($part->getProducto() == $producto && $padre->getIdparte() == $parte->getIdparte()) {
                        $total = $part->getCantidad();
                    }

                }
                $porciento = ($total * 100) / $cant;


            }
            if ($periodo == 'mes') {
                $toYear = false;
                $total = 0;
                $partes_del_mes = $this->partes_venta_mes($mes_parte, $year_parte, $toYear);
                foreach ($partes_del_mes as $select) {
                    foreach ($select as $part) {
                        if ($part->getProducto() == $producto) {
                            $total += $part->getCantidad();
                        }
                    }
                }
                $porciento = ($total * 100) / $cant;

            }
            if ($periodo == 'mes_fecha') {
                $toYear = false;
                $total = 0;
                $cant = $cant / $this->dias_del_mes($mes_parte, $year_parte);
                $partes_del_mes = $this->partes_venta_mes($mes_parte, $year_parte, $toYear);
                foreach ($partes_del_mes as $select) {
                    foreach ($select as $part) {
                        if ($part->getProducto() == $producto) {
                            $total += $part->getCantidad();
                        }
                    }
                }
                $porciento = ($total * 100) / ($cant * $dia_parte);

            }

            if ($periodo == 'year_fecha') {
                $toYear = true;
                $total = 0;
                $plan_acumulado = 0;

                $cant = $this->acumulado_Plan($plan, $mes_parte, $dia_parte);
                $partes_del_mes = $this->partes_venta_mes($mes_parte, $year_parte, $toYear);
                foreach ($partes_del_mes as $select) {
                    foreach ($select as $part) {
                        if ($part->getProducto() == $producto) {
                            $total += $part->getCantidad();
                        }
                    }

                }

                $porciento = ($total * 100) / $cant;

            }

            if ($operador == '>') {
                if ($porciento > $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<') {
                if ($porciento < $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '=') {
                if ($porciento == $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<>') {
                if ($porciento != $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '>=') {
                if ($porciento >= $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<=') {
                if ($porciento <= $alert->getCant())
                    return true;
                else
                    return false;
            }

        } else {
            return null;
        }
    }

    private function transporte_operation($alert)
    {
        $operador = $alert->getOperador();
        $em = $this->getDoctrine()->getManager();
        $tipo_transporte = $alert->getTipoTransporte();
        $ultimo = $this->ultimo_parte_transporte();
        if ($ultimo != null) {
            $parte = $em->getRepository('ParteDiarioBundle:DatParteTransporte')->findOneBy(array('tipotransporte' => (int)$tipo_transporte->getId(), 'fecha' => $ultimo));

            if ($operador == '>') {
                if ($parte->getCdt() > $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<') {
                if ($parte->getCdt() < $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '=') {
                if ($parte->getCdt() == $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<>') {
                if ($parte->getCdt() != $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '>=') {
                if ($parte->getCdt() >= $alert->getCant())
                    return true;
                else
                    return false;
            } elseif ($operador == '<=') {
                if ($parte->getCdt() <= $alert->getCant())
                    return true;
                else
                    return false;
            }
        } else {
            return null;
        }
    }

    private function portador_operation($alert)
    {
        $operador = $alert->getOperador();
        $em = $this->getDoctrine()->getManager();
        $portador = $alert->getPortador();
        $ultimo = $em->getRepository('ParteDiarioBundle:DatPartePortador')->ultimo_parte_portador();
        $periodo = $alert->getPeriodo() ? $alert->getPeriodo() : null;
        $tipo_plan = $alert->getTipoPlan();
        $ejercicio = $alert->getEjercicio();
        $ueb = $alert->getEntidad();
        $plan = "";
        $respuesta = false;
        $parte = "";
        if ($ultimo != null) {
            if (!is_null($tipo_plan) && !is_null($ejercicio)) {

                $plan = $em->getRepository('ParteDiarioBundle:DatPlanPortador')->findOneBy(array(
                    'idportador' => $portador,
                    'idueb' => $ueb->getIdueb(),
                    'idejercicio' => $ejercicio,
                    'idtipoplan' => $tipo_plan
                ));

            } else {
                $plan = $em->getRepository('ParteDiarioBundle:DatPlanPortador')->findOneBy(array('idportador' => $portador, 'idueb' =>  $ueb->getIdueb()));
            }
            $parte = $em->getRepository('ParteDiarioBundle:DatPartePortador')->findOneBy(array('portador' => $portador, 'fecha' => $ultimo));
            if ($alert->getConsumoInventario() == 'inventario') {

                if (count($parte) > 0) {
                    if ($operador == '>') {
                        if ($parte->getInventario() > $alert->getCant())
                            $respuesta = true;
                    } elseif ($operador == '<') {
                        if ($parte->getInventario() < $alert->getCant())
                            $respuesta = true;
                    } elseif ($operador == '=') {
                        if ($parte->getInventario() == $alert->getCant())
                            $respuesta = true;
                    } elseif ($operador == '!=') {
                        if ($parte->getInventario() != $alert->getCant())
                            $respuesta = true;
                    } elseif ($operador == '>=') {
                        if ($parte->getInventario() >= $alert->getCant())
                            $respuesta = true;
                    } elseif ($operador == '<=') {
                        if ($parte->getInventario() <= $alert->getCant())
                            $respuesta = true;
                    }
                }
            }
            elseif ($alert->getConsumoInventario() == 'consumo') {
                if (count($parte) > 0) {
                    if ($operador == '>' && $parte->getConsumo() > $alert->getCant()) {
                        $respuesta = true;
                    } elseif ($operador == '<' && $parte->getConsumo() < $alert->getCant()) {
                        $respuesta = true;
                    } elseif ($operador == '=' && $parte->getConsumo() == $alert->getCant()) {
                        $respuesta = true;
                    } elseif ($operador == '!=' && $parte->getConsumo() != $alert->getCant()) {
                        $respuesta = true;
                    } elseif ($operador == '>=' && $parte->getConsumo() >= $alert->getCant()) {
                        $respuesta = true;
                    } elseif ($operador == '<=' && $parte->getConsumo() <= $alert->getCant()) {
                        $respuesta = true;
                    }
                }
            }
            elseif ($alert->getConsumoInventario() == 'porciento') {
                /*% respecto al PLAN*/
                $dia_parte = date_format($ultimo, "d");
                $mes_parte = date_format($ultimo, "m");
                $year_parte = date_format($ultimo, "Y");
                $sistema = $em->getRepository('AdminBundle:DatConfig')->findAll();

                $dia_sistema = date_format($sistema[0]->getFechaTrabajo(), "d");
                $mes_sistema = date_format($sistema[0]->getFechaTrabajo(), "m");
                $year_sistema = date_format($sistema[0]->getFechaTrabajo(), "Y");
                $cant = 0;
                if ($plan != null) {
                    if ($mes_parte == "01") {
                        $cant = $plan->getEnero();
                    } elseif ($mes_parte == "02") {
                        $cant = $plan->getFebrero();
                    } elseif ($mes_parte == "03") {
                        $cant = $plan->getMarzo();
                    } elseif ($mes_parte == "04") {
                        $cant = $plan->getAbril();
                    } elseif ($mes_parte == "05") {
                        $cant = $plan->getMayo();
                    } elseif ($mes_parte == "06") {
                        $cant = $plan->getJunio();
                    } elseif ($mes_parte == "07") {
                        $cant = $plan->getJulio();
                    } elseif ($mes_parte == "08") {
                        $cant = $plan->getAgosto();
                    } elseif ($mes_parte == "09") {
                        $cant = $plan->getSeptiembre();
                    } elseif ($mes_parte == "10") {
                        $cant = $plan->getOctubre();
                    } elseif ($mes_parte == "11") {
                        $cant = $plan->getNoviembre();
                    } elseif ($mes_parte == "12") {
                        $cant = $plan->getDiciembre();
                    }
                }
                if ($cant == 0) {
                    return null;
                }
                if ($periodo == 'hoy') {
                    if ($parte != null) {
                        $cant = $cant / $this->dias_del_mes($mes_sistema, $year_sistema);
                        $porciento = $alert->getConsumoInventario() == 'consumo' ? ($parte->getConsumo() * 100) / $cant : ($parte->getInventario() * 100) / $cant;
                    } else {
                        $porciento = 0;
                    }
                }
                if ($periodo == 'mes') {
                    $toYear = false;
                    $total = 0;
                    $total = $em->getRepository('ParteDiarioBundle:DatPartePortador')->partes_portador_mes($mes_parte, $year_parte, $toYear,$portador->getIdportador(),$ueb->getIdueb(),$alert->getConsumoInventario());
                    $porciento = ($total * 100) / $cant;
                }
                if ($periodo == 'mes_fecha') {
                    $toYear = false;
                    $total = 0;
                    $cant = $cant / $this->dias_del_mes($mes_parte, $year_parte);
                    $total = $em->getRepository('ParteDiarioBundle:DatPartePortador')->partes_portador_mes($mes_parte, $year_parte, $toYear,$portador->getIdportador(),$ueb->getIdueb(),$alert->getConsumoInventario());
                    $porciento = ($total * 100) / ($cant * $dia_parte);
                }

                if ($periodo == 'year_fecha') {
                    $toYear = true;
                    $total = 0;
                    $plan_acumulado = 0;
                    $cant = $this->acumulado_Plan($plan, $mes_parte, $dia_parte);
                    $total = $em->getRepository('ParteDiarioBundle:DatPartePortador')->partes_portador_mes($mes_parte, $year_parte, $toYear,$portador->getIdportador(),$ueb->getIdueb(),$alert->getConsumoInventario());
                    $porciento = ($total * 100) / $cant;
                }
                if ($operador == '>') {
                    if ($porciento > $alert->getCant())
                        return  true;
                } elseif ($operador == '<') {
                    if ($porciento < $alert->getCant())
                        return  true;
                } elseif ($operador == '=') {
                    if ($porciento == $alert->getCant())
                        return  true;
                } elseif ($operador == '<>') {
                    if ($porciento != $alert->getCant())
                        return  true;
                } elseif ($operador == '>=') {
                    if ($porciento >= $alert->getCant())
                        return  true;
                } elseif ($operador == '<=') {
                    if ($porciento <= $alert->getCant())
                        return  true;
                }
            }
            }
        return $respuesta;
    }

    private function economia_operation($alert)
    {
        $operador = $alert->getOperador();
        $em = $this->getDoctrine()->getManager();
        $cuenta = $alert->getCuenta();
        $ultimo = $this->ultimo_parte_economia();

        if ($alert->getVencidaCuenta() == 'cuenta') {
            $parte = $em->getRepository('ParteDiarioBundle:DatPartediarioEconomia')->findOneBy(array('idcuentacontable' => (int)$cuenta->getIdcuentacontable(), 'fecha' => $ultimo));
            if ($parte != null) {
                if ($operador == '>') {
                    if ($parte->getSaldo() > $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '<') {
                    if ($parte->getSaldo() < $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '=') {
                    if ($parte->getSaldo() == $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '!=') {
                    if ($parte->getSaldo() != $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '>=') {
                    if ($parte->getSaldo() >= $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '<=') {
                    if ($parte->getSaldo() <= $alert->getCant())
                        return true;
                    else
                        return false;
                }
            }
        } elseif ($alert->getVencidaCuenta() == 'vencida') {

            $parte = $em->getRepository('ParteDiarioBundle:DatParteCuentasCobrar')->findOneBy(array('idcuentacontable' => (int)$cuenta->getIdcuentacontable(), 'fecha' => $ultimo));
            if ($parte != null) {
                if ($operador == '>') {
                    if ($parte->getDiasvencido() > $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '<') {
                    if ($parte->getDiasvencido() < $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '=') {
                    if ($parte->getDiasvencido() == $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '!=') {
                    if ($parte->getDiasvencido() != $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '>=') {
                    if ($parte->getDiasvencido() >= $alert->getCant())
                        return true;
                    else
                        return false;
                } elseif ($operador == '<=') {
                    if ($parte->getDiasvencido() <= $alert->getCant())
                        return true;
                    else
                        return false;
                }
            }


        } else {
            return null;
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ALERTA')")
     */
    public function listarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $where = '1=1';
        $dat = $request->query->get('dat');
      /*  if ($dat == 1)
            $where .= ' and a.fechaeliminacion is not null' ;
        else if ($dat == 2)
            $where .=  ' and a.fechaeliminacion is null and a.revisada = 1';
        else
            $where .=  ' and a.fechaeliminacion is null and a.revisada = 0';*/

        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'entidad') {
                    $where .= 'AND e.nombre LIKE \'%' . $sp[1] . '%\'';

                } else {
                    $where .= 'AND a.' . $sp[0] . ' LIKE \'%' . $sp[1] . '%\'';
                }
            }
        }
        $dql = 'SELECT a.idalerta, a.fecha, a.operador, a.cant, a.actividad, e.nombre AS entidad, a.descripcion
            FROM ParteDiarioBundle:DatAlerta a  JOIN a.entidad e
            WHERE ' . $where;
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr['idalerta'];
            $fecha = $pr['fecha'];
            if ($fecha != null) {
                $res[] = $fecha->format("d/m/Y");
            } else {
                $res[] = date('d/m/Y');
            }
            $res[] = $pr['entidad'];
            if ($pr['actividad'] == 'Economia')
                $res[] = 'Economía';
            else if ($pr['actividad'] == 'Produccion')
                $res[] = 'Producción';
            else
                $res[] = $pr['actividad'];
            $res[] = $pr['operador'];
            $res[] = $pr['cant'];
            $res[] = $pr['descripcion'];

            $array[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }
    }

    /**
     * Disabled DatAlerta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ALERTA')")
     */
    public function suprimirAction(Request $request, DatAlerta $datAlertum)
    {
        $user = $this->getUser(); // usuario de la session
        $em = $this->getDoctrine()->getManager();
        $aaccion=new DatAlertaAccion();
        $aaccion->setAlerta($datAlertum);
        $aaccion->setActividad($datAlertum->getActividad());
        $aaccion->setDescripcion($datAlertum->getDescripcion());
        $aaccion->setFecha(new \DateTime());
        $aaccion->setUeb($datAlertum->getEntidad());
        $aaccion->setUsuario($user);
        $aaccion->setAccion('Suprimir');
       /* $datAlertum->setFechaeliminacion(new \DateTime('now'));
        $em->persist($datAlertum);*/
        $em->persist($aaccion);
        $em->flush();

        return $this->alertsAction($user);
    }

    /**
     * Disabled DatAlerta entity.
     *
     */
    /**
     * @Security("is_granted('ROLE_LISTAR_ALERTA')")
     */
    public function revisarAction(Request $request, DatAlerta $datAlertum)
    {
        $user = $this->getUser(); // usuario de la session
        $em = $this->getDoctrine()->getManager();
        $aaccion=new DatAlertaAccion();
        $aaccion->setAlerta($datAlertum);
        $aaccion->setActividad($datAlertum->getActividad());
        $aaccion->setDescripcion($datAlertum->getDescripcion());
        $aaccion->setFecha(new \DateTime());
        $aaccion->setUeb($datAlertum->getEntidad());
        $aaccion->setUsuario($user);
        $aaccion->setAccion('Revisar');

     /*   $datAlertum->setRevisada(1);
        $em->persist($datAlertum);*/
        $em->persist($aaccion);
        $em->flush();

        return $this->alertsAction($user);
    }


    public function listaraccionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $st = $request->query->get('start') ? $request->query->get('start') : 0;
        $lm = $request->query->get('limit') ? $request->query->get('limit') : 10;
        $user = $this->getUser(); // usuario de la session
        $where = '1=1 and a.usuario ='.$user->getId();
        $dat = $request->query->get('dat');
        if ($dat == 1)
            $where .= ' and a.accion LIKE \'%Suprimir%\'' ;
        else
            $where .= ' and a.accion LIKE \'%Revisar%\'' ;

        $filters_raw = $request->query->get('filters');
        if ($filters_raw) {
            foreach ($filters_raw as $f) {
                $sp = explode(':', $f);
                if ($sp[0] == 'entidad') {
                    $where .= ' AND e.nombre LIKE \'%' . $sp[1] . '%\'';

                } else {
                    $where .= ' AND a.' . $sp[0] . ' LIKE \'%' . $sp[1] . '%\'';
                }
            }
        }

        $dql = 'SELECT a.id,a.fecha,a.actividad,e.nombre AS entidad,a.descripcion  
            FROM ParteDiarioBundle:DatAlertaAccion a  JOIN a.ueb e
            WHERE ' . $where;
        $consulta = $em->createQuery($dql);
        $total = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $nom = $consulta->getResult();
        $array = array();
        foreach ($nom as $pr) {
            $res = array();
            $res[] = $pr['id'];
            $fecha = $pr['fecha'];
            if ($fecha != null) {
                $res[] = $fecha->format("d/m/Y");
            } else {
                $res[] = date('d/m/Y');
            }
            if ($pr['actividad'] == 'Economia')
                $res[] = 'Economía';
            else if ($pr['actividad'] == 'Produccion')
                $res[] = 'Producción';
            else
                $res[] = $pr['actividad'];
            $res[] = $pr['entidad'];
            $res[] = $pr['descripcion'];
            $array[] = $res;
        }
        if (count($total) > 0) {
            return new JsonResponse(array('data' => $array, 'total' => $total));
        } else {
            return new JsonResponse(array('data' => [], 'total' => 0));
        }

    }


}
