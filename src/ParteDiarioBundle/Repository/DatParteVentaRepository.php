<?php
/**
 * Created by PhpStorm.
 * User: mary
 * Date: 17/11/2016
 * Time: 4:16
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use EnumsBundle\Entity\EnumMeses;
use Doctrine\Common\Util\Debug;


class DatParteVentaRepository extends EntityRepository
{

    public function findProByVenta($entidad, $id)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('select cp
                                          from  ' . $entidad . '  cp
                                          where cp.idpadre =' . $id);
        return $consulta->getResult();
    }

    public function buscar($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m,alm,um,pro,parte
            FROM ParteDiarioBundle:DatVentaProducto m
            JOIN m.producto pro
            JOIN m.parte parte
            join m.almacen alm
            join m.um um
             where m.id = :id";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);
        $lista = $consulta->getResult();
        if (count($lista) == 0) {
            return [];
        } else {
            return $lista;
        }
    }

    public function calcularNivelProd($params)
    {
        $em = $this->getEntityManager();
        if (is_array($params['idproducto'])) {
            $params['idproducto'] = implode(',', $params['idproducto']);
        }
        if (is_object($params['identidades'][0])) {
            $entidadesAux = $params['identidades'];
            $params['identidades'] = [];
            foreach ($entidadesAux as $valueEnt) {
                $params['identidades'][] = $valueEnt->getIdentidad();
            }
        }

        if (is_array($params['identidades'])) {
            $params['identidades'] = implode(",", $params['identidades']);
        }

        $selectDef = " sum(g.cantidad) as cantidad ";
        if ($params['ufvalor'] && $params['moneda'] != '' && $params['moneda'] != null) {
            $moneda = $em->getRepository('NomencladorBundle:NomMonedadestino')->find($params['moneda']);
            if ($moneda->getAlias() == 'cuc') {
                $selectDef = " sum(g.importecuc) as importe ";
            } elseif ($moneda->getAlias() == 'cup') {
                $selectDef = " sum(g.importemn) as importe ";
            }
        } else {
            if ($params['ufvalor']) {
                $selectDef = " (sum(g.importecuc) + sum(g.importemn)) as importe ";
            }
        }

        $where = '1=1';
        $where .= ($params['idueb'] != '' && $params['idueb'] != null) ? (' and ue in (' . $params['idueb'] . ')') : '';
        $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and pa.fecha <=' . "'" . $params['fecha'] . "'" . '') : '';
        $where .= ($params['idum'] != '' && $params['idum'] != null) ? (' and g.um IN(' . $params['idum'] . ')') : '';
        $where .= ($params['identidades'] != '' && $params['identidades'] != null) ? (' and pa.cliente in(' . $params['identidades'] . ')') : '';
        $where .= ($params['idvinculo'] != '' && $params['idvinculo'] != null) ? (' and g.origen in(' . $params['idvinculo'] . ')') : '';
        $explodeFecha = EnumMeses::convertfecha($params['fecha']);
        if ($params['acumulado'] == 0) {
            $cad = substr($params['fecha'], 0, -3);
            $where .= ' and pa.fecha >= \'' . $cad . '-01\'';
        }
        if ($params['acumulado'] == 1) {//acumulado del mes
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and pa.fecha >=' . "'" . $explodeFecha['a'] . '-' . $explodeFecha['m'] . '-01' . "'") : '';
        }
        $primerDiaAno = EnumMeses::primerDiaAno($params['fecha']);
        if ($params['acumulado'] == 2) {//acumulado del año
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and pa.fecha >=' . "'" . $primerDiaAno . "'") : '';
        }
        if ($params['acumulado'] == 3) {// mes completo
            $enum = new EnumMeses();
            $range = $enum->intervaloMes($params['fecha']);
            $where .= ' and pa.fecha <=' . "'" . $range['fin'] . "'" . ' and pa.fecha >=' . "'" . $range['inicio'] . "'" . '';
        }
        $primerDiaAno = EnumMeses::primerDiaAno($params['fecha']);
        if ($params['acumulado'] == 2) {
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ? (' and pa.fecha >=' . "'" . $primerDiaAno . "'") : '';
        }
        $where .= ($params['idproducto'] != '' && $params['idproducto'] != null && $params['idproducto'] != ' ') ? (' and g.producto in(' . $params['idproducto'] . ')') : '';
        $dql = "SELECT " . $selectDef .
            "FROM ParteDiarioBundle:DatVentaProducto g
            JOIN g.parte pa 
            JOIN g.producto p 
            JOIN pa.cliente cl
            JOIN pa.ueb ue
            WHERE " . $where;
        $consulta_partes = $em->createQuery($dql);
        $partes = $consulta_partes->getResult();
        if ($params['ufvalor']) {
            $result = (count($partes) > 0 && $partes[0]['importe'] != '') ? $partes[0]['importe'] : 0;
        } else {
            $result = (count($partes) > 0 && $partes[0]['cantidad'] != '') ? $partes[0]['cantidad'] : 0;
        }

        return $result;
    }

}
















