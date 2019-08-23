<?php

/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 12/22/2017
 * Time: 3:07 PM
 */

namespace ReporteBundle\Services;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use EnumsBundle\Entity\EnumMeses;
use NomencladorBundle\Entity\NomGrupointeres;
use ReporteBundle\Util\Util;

class ReporteService
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getPlanMes($params)
    {
        $fecha = $params['fechaPlan'];
        /*Esto me hizo falta para el reporte de Vinculo, comprobar si el producto sin buscar sus hijos tiene plan*/
        if (isset($params['idproductoOrig'])) {
            $producto = $params['idproductoOrig'];
        } else {
            $producto = $params['idproducto'];
        }
        $portador = $params['idportador'];
        $um = $params['idum'];
        $md = $params['moneda'];
        $tipo = $params['tipoplan'];
        $grupo = $params['idgrupo'];
        $valor = $params['ufvalor'];
        $acumulado = $params['acumulado'];
        $ejercicio = $params['ejercicio'];
        $tabla = $params['tablaPlan'];
        $ueb = $params['idueb'];
        $vinculo = $params['idvinculo'];
        $result = 0;
        $ejercicioActual = EnumMeses::convertfecha($fecha)['a'];
        if ($tipo != null && (($producto != '' && $producto != null) || ($portador != '' && $portador != null)) && $fecha != '') {

            if ($fecha != '') {
                $fechaformato = EnumMeses::convertfecha($fecha);

                $m = $fechaformato['m'];
                $d = $fechaformato['d'];
                $a = $fechaformato['a'];
                $where = $this->subConsultaWhere($producto, $grupo, $tipo, $md, $um, $valor, $ueb, $tabla, $portador,
                    $vinculo);
                if ($acumulado != 1 && $acumulado != 0 && $acumulado != 3 && $acumulado != 4 && $acumulado != 5 && $acumulado != 6) {
                    $val = $this->planesPorDosFechas($acumulado, $a, $m, $d, $where, $tabla);
                    $result = (count($val) > 0 && $val[0]['valor'] != ' ' && $val != null) ? $val[0]['valor'] : 0;
                } else {
                    $result = $this->acumuladoHastaFecha($acumulado, $a, $m, $d, $where, $ejercicioActual, $tabla);
                }
            }
        } else {
            return 0;
        }
        return (count($result) > 0 && $result != ' ' && $result != null) ? $result : 0;
    }

    public function subConsultaWhere($productoAux, $grupoAux, $tipo, $md, $um, $valor, $ueb, $tabla = null,
                                     $portadores, $vinculo)
    {
        if (is_array($productoAux)) {
            $producto = implode(",", $productoAux);
        } else {
            $producto = $productoAux;
        }

        if (is_array($portadores)) {
            $portador = implode(",", $portadores);
        } else {
            $portador = $portadores;
        }

        if (is_array($grupoAux)) {
            $grupo = implode(",", $grupoAux);
        } else {
            $grupo = $grupoAux;
        }
        $where = ' 1=1';
        $where .= ($producto != null) ? (' and plan.idproducto IN( ' . $producto . ')') : ' ';
        $where .= ($ueb != null) ? (' and plan.idueb in( ' . $ueb . ')') : ' ';
        $where .= ($tipo != '' && $tipo != null) ? (' and plan.idtipoplan = ' . $tipo) : '';
        $where .= ($md != '' && $md != null) ? (' and plan.idmonedadestino = ' . $md) : '';
        //$where .= ($um != '' && $um != null) ? (' and plan.idunidadmedida = ' . $um) : '';

        if ($tabla == 'DatPlanVenta') {
            $where .= ($valor != '' && $valor != null) ? (' and plan.is_val = ' . $valor) : '';
            $where .= ($grupo != null) ? (' and plan.idgrupocliente in( ' . $grupo . ')') : ' ';
            $where .= ($vinculo != null) ? (' and plan.identidad in( ' . $vinculo . ')') : ' ';
        } elseif ($tabla == 'DatPlanAcopioDestino') {//son grupos hijos que son entidades
            $where .= ($grupo != null) ? (' and plan.identidad in( ' . $grupo . ')') : ' ';
        } elseif ($tabla == "DatPlanPortador") {
            $where .= ($portador != null) ? (' and plan.idportador in( ' . $portador . ')') : ' ';
        }
        return $where;
    }

    public function planesPorDosFechas($acumulado, $a, $m, $d, $where, $tabla)
    {
        $fechaformato = EnumMeses::convertfecha($acumulado);
        $mesF = $fechaformato['m'];
        $diaF = $fechaformato['d'];
        $anF = $fechaformato['a'];
        if ($anF != $a) {
            $val = 0;
            $whereInicial = $where;
            $whereInicial .= $this->whereEjercicio($a);
            $planes = $this->subconsultPlan(0, $a, $m, $d);
            $v = $this->getPlanBySubconsulta($whereInicial, $planes, $tabla);
            $val = $v;
            for ($i = $a + 1; $i < $anF; $i++) {
                $whereMedio = $where;
                $whereMedio .= $this->whereEjercicio($i);
                $planes = $this->subconsultPlan(2, $i, null, null);
                $v = $this->getPlanBySubconsulta($whereMedio, $planes, $tabla);
                $val += $v;
            }
            $whereFinal = $where;
            $whereFinal .= $this->whereEjercicio($anF);
            $planes = $this->subconsultPlan(1, $anF, $mesF, $diaF);
            $v = $this->getPlanBySubconsulta($whereFinal, $planes, $tabla);
            $val += $v;
        } else {
            $eje = $this->em->getRepository('NomencladorBundle:NomEjercicio')->findOneBy(array('nombre' => $a));
            $whereAux = $where;
            $whereAux .= $this->whereEjercicio($eje->getidejercicio());
            $planes = $this->subconsultPlan($acumulado, $eje->getidejercicio(), $m, $d);
            $val = $this->getPlanBySubconsulta($whereAux, $planes, $tabla);
        }
        return $val;

    }

    public function acumuladoHastaFecha($acumulado, $a, $m, $d, $where, $ejercicioActual, $tabla)
    {

        if ($a != $ejercicioActual) {
            $val = $this->planEjerciciosDiferentes($ejercicioActual, $a, $m, $d, $where, $acumulado, $tabla);
        } else {
            $eje = $this->em->getRepository('NomencladorBundle:NomEjercicio')->findOneBy(array('nombre' => $a));
            if ($eje != null) {
                $planes = $this->subconsultPlan($acumulado, $eje->getidejercicio(), $m, $d);
                $v = $this->getPlanBySubconsulta($where, $planes, $tabla);
            } else {
                $v = 0;
            }
            return $v;
        }
        return $val;

    }

    public function whereEjercicio($a)
    {
        $where = ' and 1=1 ';
        $eje = $this->em->getRepository('NomencladorBundle:NomEjercicio')->findOneBy(array('nombre' => $a));
        if ($eje) {
            $where .= ($eje != '') ? (' and plan.idejercicio = ' . $eje->getIdejercicio()) : '';
        }
        return $where;
    }

    public function subconsultPlan($acumulado, $a, $m, $d)
    {
        $objMeses = new EnumMeses();
        $meses = $objMeses->getMeses();
        $planes = '0 ';

        if ($acumulado == 1) { //año entero
            foreach ($meses as $mes) {
                if (strtolower($mes) == $meses[$m] && ($m) >= 0) {
                    $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $m, $a);
                    $planes .= ' + ((plan.' . strtolower($mes) . '/' . $cantidadDias . ')*' . $d . ')';
                    break;
                } else {
                    $planes .= ' + plan.' . strtolower($mes) . '';
                }
            }
        } else {
            if ($acumulado == 0) {
                $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $m, $a);
                $planes .= ' + ((plan.' . strtolower($meses[$m]) . '/' . $cantidadDias . ')*' . $d . ')';
            } else {
                if ($acumulado == 3) {//dia especifico
                    $cantidadDias = cal_days_in_month(CAL_GREGORIAN, $m, $a);
                    $planes .= ' + (plan.' . strtolower($meses[$m]) . '/' . $cantidadDias . ')';
                } else {
                    if ($acumulado == 4) {//mes especifico
                        $planes .= ' + (plan.' . strtolower($meses[$m]) . ')';
                    } elseif ($acumulado == 6) { //acumulado del año hasta la fecha
                        foreach ($meses as $indexM => $mes) {
                            $planes .= ' + plan.' . strtolower($mes) . '';
                            if ($indexM == $m) {
                                break;
                            }
                        }
                    } else {
                        foreach ($meses as $mes) {
                            $planes .= ' + plan.' . strtolower($mes) . '';
                        }
                    }
                }
            }
        }
        return $planes;
    }

    private function getPlanBySubconsulta($where, $planes, $tabla)
    {
        $dql = 'SELECT sum(' . $planes . ') as valor
                                  FROM ParteDiarioBundle:' . $tabla . ' plan
                                  WHERE ' . $where;
        $consulta = $this->em->createQuery($dql);
        //dump($consulta->getSQL());die;
        $plan = $consulta->getResult();
        $result = (count($plan) > 0 && $plan[0]['valor'] != ' ' && $plan[0]['valor'] != null) ? $plan[0]['valor'] : 0;
        return $result;
    }

    public function planEjerciciosDiferentes($ejercicioActual, $a, $m, $d, $where, $acumulado, $tabla)
    {
        $val = 0;
        if ($acumulado == 0) {
            $where .= $this->whereEjercicio($a);
            $planes = $this->subconsultPlan($acumulado, $a, $m, $d);
            $val = $this->getPlanBySubconsulta($where, $planes, $tabla);
        } else {
            if ($a < $ejercicioActual) {
                $whereEjerInc = $where;
                $whereEjerInc .= $this->whereEjercicio($a);
                $planes = $this->subconsultPlan(0, $a, $m, $d);
                $v = $this->getPlanBySubconsulta($whereEjerInc, $planes, $tabla);
                $val = $v;
                for ($i = $a + 1; $i < $ejercicioActual; $i++) {
                    $whereAux = $where;
                    $whereAux .= ' ' . $this->whereEjercicio($i);
                    $planes = $this->subconsultPlan(1, $i, $m, $d);
                    $v = $this->getPlanBySubconsulta($whereAux, $planes, $tabla);
                    $val += $v;
                }
            } else {
                if ($a > $ejercicioActual) {
                    for ($i = $ejercicioActual; $i < $a; $i++) {
                        $whereAux = $where;
                        $whereAux .= ' ' . $this->whereEjercicio($i);
                        $planes = $this->subconsultPlan(2, null, null, null);
                        $v = $this->getPlanBySubconsulta($whereAux, $planes, $tabla);
                        $val += $v;
                    }
                    $whereFinal = $where;
                    $whereFinal .= $this->whereEjercicio($a);
                    $planes = $this->subconsultPlan(1, $a, $m, $d);
                    $v = $this->getPlanBySubconsulta($whereFinal, $planes, $tabla);
                    $val += $v;
                }
            }
        }

        return $val;
    }

    public function arbolVinculo()
    {
        $data = array();
        $dqlhijos = 'SELECT v
            FROM NomencladorBundle:NomEntidad v
            WHERE v.vinculo=true';
        $consulta = $this->em->createQuery($dqlhijos);
        $vinculos = $consulta->getResult();
        foreach ($vinculos as $v) {
            $data[] = array(
                'id' => $v->getIdentidad(),
                'text' => $v->getNombre(),
                'icon' => "fa fa-sitemap"
            );
        }
        return $data;
    }

    public function arbolUeb()
    {
        $data = array();
        $dqlhijos = 'SELECT v
            FROM NomencladorBundle:NomUeb v
            WHERE v.activo=true';
        $consulta = $this->em->createQuery($dqlhijos);
        $vinculos = $consulta->getResult();
        foreach ($vinculos as $v) {
            $data[] = array(
                'id' => $v->getIdueb(),
                'text' => $v->getNombre(),
                'icon' => "fa fa-sitemap"
            );
        }
        return $data;
    }

    public function arbolPortador()
    {
        $data = array();
        $dqlhijos = 'SELECT v
            FROM NomencladorBundle:NomPortador v
            WHERE v.activo=true';
        $consulta = $this->em->createQuery($dqlhijos);
        $vinculos = $consulta->getResult();
        foreach ($vinculos as $v) {
            $data[] = array(
                'id' => $v->getIdportador(),
                'text' => $v->getNombre(),
                'icon' => "fa fa-sitemap"
            );
        }
        return $data;
    }
}

