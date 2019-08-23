<?php
/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 12/22/2017
 * Time: 2:36 PM
 */

namespace ReporteBundle\Util;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\ORM\EntityManager;
use EnumsBundle\Entity\EnumMeses;
use Doctrine\Common\Util\Debug;
use NomencladorBundle\Entity\NomGrupointeres;

class DatReportes
{
    public function getPortadoresByUeb($params)
    {
        $uebImpl = implode(',', $params['arbolder_array']);
        $portaImpl = implode(',', $params['arbolizq_array']);
        $dql = "SELECT p,po,u FROM ParteDiarioBundle:DatPartePortador p 
                JOIN p.ueb u
                JOIN p.portador po
                WHERE p.ueb in($uebImpl) AND p.portador in ($portaImpl) AND p.fecha <= :fecha ORDER BY p.ueb, p.portador";
        $consulta = $this->em->createQuery($dql);
        $consulta->setParameter('fecha', $params['fecha']);
        $result = $consulta->getResult();
        return $result;
    }

    public function productosByGrupos($params)
    {
        $grupImpl = implode(',', $params['arbolder_array_ent']);
        $prodImpl = implode(',', $params['arbolizq_array']);
        $dql = "SELECT vp,p,pro,cl FROM ParteDiarioBundle:DatVentaProducto vp
                JOIN vp.parte p
                JOIN vp.producto pro
                JOIN p.cliente cl
                WHERE vp.producto in ($prodImpl) AND p.cliente in($grupImpl) AND p.fecha <= :fecha ORDER BY vp.producto,p.cliente";
        $consulta = $this->em->createQuery($dql);
        $consulta->setParameter('fecha', $params['fecha']);
        $result = $consulta->getResult();
        return $result;
    }

    public function calcularNivelProd($params)
    {
        $join = "";
        $join .= 'JOIN g.ueb ue ';
        $campo = 'sum(g.cantidad)';

        /*$params['acumulado'] si es 0 no tiene, si es 1 del mes y si es 2 del año*/
        $where = '1=1';

        $where .= ($params['idueb'] != '' && $params['idueb'] != null) ? (' and ue =' . $params['idueb'] . '') : '';
        $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and g.fecha <=' . "'" . $params['fecha'] . "'" . '') : '';

        $explodeFecha = EnumMeses::convertfecha($params['fecha']);
        if ($params['acumulado'] == 0) {
            $cad = substr($params['fecha'], 0, -3);
            $where .= ' and g.fecha >= \'' . $cad . '-01\'';
        }
        if ($params['acumulado'] == 1) {//acumulado del mes
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and g.fecha >=' . "'" . $explodeFecha['a'] . '-' . $explodeFecha['m'] . '-01' . "'") : '';
        }

        $primerDiaAno = EnumMeses::primerDiaAno($params['fecha']);
        if ($params['acumulado'] == 2) {//acumulado del año
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and g.fecha >=' . "'" . $primerDiaAno . "'") : '';
        }
        if ($params['acumulado'] == 3) {// mes completo
            $enum = new EnumMeses();
            $range = $enum->intervaloMes($params['fecha']);
            $where .= ' and g.fecha <=' . "'" . $range['fin'] . "'" . ' and g.fecha >=' . "'" . $range['inicio'] . "'" . '';
        }
        if ($params['idReporte'] == '13') {
            $campo = 'sum(g.consumo)';
            $join .= 'join g.portador p';
            $where .= ($params['idportador'] != '' && $params['idportador'] != null && $params['idportador'] != ' ') ? (' and g.portador in(' . $params['idportador'] . ')') : '';
        } else if ($params['idReporte'] == '16') {
            $campo = 'sum(g.cantidad)';
            $join .= 'join g.producto p JOIN g.moneda md ';
            $where .= ($params['idproducto'] != '' && $params['idproducto'] != null && $params['idproducto'] != ' ') ? (' and p.idproducto in(' . $params['idproducto'] . ')') : '';
        } else if ($params['idReporte'] == '2') {
            $where = '1=1 ';
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and pa.fecha <=' . "'" . $params['fecha'] . "'" . '') : '';
            if ($params['acumulado'] == 1) {
                $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and pa.fecha >=' . "'" . $explodeFecha['a'] . '-' . $explodeFecha['m'] . '-01' . "'") : '';
            }

            $primerDiaAno = EnumMeses::primerDiaAno($params['fecha']);
            if ($params['acumulado'] == 2) {
                $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and pa.fecha >=' . "'" . $primerDiaAno . "'") : '';
            }

            $campo = 'sum(g.cantidad)';
            $join = ' JOIN g.parte pa JOIN g.producto p JOIN pa.cliente cl ';
            $where .= ($params['idproducto'] != '' && $params['idproducto'] != null && $params['idproducto'] != ' ') ? (' and p.idproducto in(' . $params['idproducto'] . ')') : '';
        }

        $dql = "SELECT " . $campo . "  as cantidad
            FROM   ParteDiarioBundle:" . $params['tabla'] . " g
            " . $join . "
             WHERE " . $where;
        $consulta_partes = $this->em->createQuery($dql);
        $partes = $consulta_partes->getResult();

        $result = (count($partes) > 0 && $partes[0]['cantidad'] != '') ? $partes[0]['cantidad'] : 0;

        return $result;
    }

    public function planMesTotal($params)
    {
        $fechaformato = EnumMeses::convertfecha($params['fecha']);
        $mes_parte = $fechaformato['m'];
        switch ($params['tablaPlan']) {
            case 'DatPlanProduccion':
                $plan = $this->em->getRepository('ParteDiarioBundle:' . $params['tablaPlan'])->findOneBy(array('idproducto' => $params['idproducto'],
                    'idtipoplan' => $params['tipoplan'], 'idueb' => $params['idueb'], 'idunidadmedida' => $params['idum']));
                break;
            case 'DatPlanVenta':
                $plan = $this->em->getRepository('ParteDiarioBundle:' . $params['tablaPlan'])->findOneBy(array('idproducto' => $params['idproducto'],
                    'idtipoplan' => $params['tipoplan'], 'idgrupocliente' => $params['idgrupo'], 'idunidadmedida' => $params['idum']));
                break;
            default:
                $plan = $this->em->getRepository('ParteDiarioBundle:' . $params['tablaPlan'])->findOneBy(array('idportador' => $params['idportador'],
                    'idtipoplan' => $params['tipoplan'], 'idueb' => $params['idueb'], 'idunidadmedida' => $params['idum']));
                break;
        }
        $cant = 0;
        if ($plan != null) {
            if ($mes_parte == "1") {
                $cant = $plan->getEnero();
            } elseif ($mes_parte == "2") {
                $cant = $plan->getFebrero();
            } elseif ($mes_parte == "3") {
                $cant = $plan->getMarzo();
            } elseif ($mes_parte == "4") {
                $cant = $plan->getAbril();
            } elseif ($mes_parte == "5") {
                $cant = $plan->getMayo();
            } elseif ($mes_parte == "6") {
                $cant = $plan->getJunio();
            } elseif ($mes_parte == "7") {
                $cant = $plan->getJulio();
            } elseif ($mes_parte == "8") {
                $cant = $plan->getAgosto();
            } elseif ($mes_parte == "9") {
                $cant = $plan->getSeptiembre();
            } elseif ($mes_parte == "10") {
                $cant = $plan->getOctubre();
            } elseif ($mes_parte == "11") {
                $cant = $plan->getNoviembre();
            } elseif ($mes_parte == "12") {
                $cant = $plan->getDiciembre();
            }
            if ($params['tablaPlan'] == 'DatPlanVenta') {
                $cantTotalAno = $plan->getValor();
            } else {
                $cantTotalAno = $plan->getCantidad();
            }

        }

        return array('cantMes' => $cant, 'cantAno' => $cantTotalAno);
    }

}