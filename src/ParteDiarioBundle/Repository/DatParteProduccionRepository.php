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
use ParteDiarioBundle\Entity\DatConsumoProduccion;


class DatParteProduccionRepository extends EntityRepository
{

    public function buscar($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m,alm,um,pro,ueb
            FROM ParteDiarioBundle:DatPartediarioProduccion m
            JOIN m.producto pro
            JOIN m.ueb ueb
            join m.almacen alm
            join m.um um
             where m.idparte = :id";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);
        $lista = $consulta->getResult();
        if (count($lista) == 0)
            return [];
        else {
            return $lista;
        }


    }

    public function buscarConsumo($idparte)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT pp, parte, norma
            FROM ParteDiarioBundle:DatConsumoProduccion pp
            JOIN pp.parte parte
            join pp.norma norma
            WHERE parte.idparte=:idparte';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('idparte', $idparte);
        $consumo = $consulta->getResult();

        return $consumo;
    }


    public function findProductosByAseguramiento($params)
    {
        $em = $this->getEntityManager();
        $where = '1=1 ';
        $where .= ($params['fecha'] != '') ? (' and part.fecha <= \'' . $params['fecha'] . '\'') : '';
        $where .= ($params['idueb'] != '' && $params['idueb'] != null) ? (' and part.ueb =' . $params['idueb'] . '') : '';
        $where .= ($params['acumulado'] == 1) ? ' and part.fecha >=\'' . EnumMeses::primerDiaMes($params['fecha']) . '\'' : '';

        $aseg = implode(",", $params['idaseguramiento']);

        $dql = "SELECT DISTINCT(pro.idproducto) as idproducto ,pro,part
            FROM ParteDiarioBundle:DatParteDiarioConsAseg part
            JOIN part.consumos cons
            join part.producto pro
            JOIN cons.aseguramiento normaaseg
            JOIN normaaseg.aseguramiento aseg 
            WHERE " . $where . " AND aseg.idaseguramiento IN ( " . $aseg . ")";
        $consulta_partes = $em->createQuery($dql);
        $partes = $consulta_partes->getResult();
        return (count($partes) > 0) ? $partes : [];
    }

    public function findNorma($prod, $aseg, $moneda)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT n.valor
            FROM NomencladorBundle:NomNorma n
            JOIN n.producto p
            JOIN n.aseguramiento a
            JOIN n.moneda m
            WHERE p.idproducto = :producto and a.idaseguramiento=:aseg and m.id=:mon';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('producto', $prod);
        $consulta->setParameter('aseg', $aseg);
        $consulta->setParameter('mon', $moneda);
        return $consulta->getArrayResult();
    }

    public function existe($parte, $id = null)
    {
        $where = '';
        $dat = array(
            'producto' => $parte['producto'],
            'fecha' => date_format(date_create_from_format('d/m/Y', $parte['fecha']), 'Y-m-d'),
            'almacen' => $parte['almacen'],
            'ueb' => $parte['ueb'],
            'moneda' => $parte['moneda']
        );
        if ($id != null) {
            $dat['id'] = $id;
            $where = ' and m.idparte <> :id';
        }
        $em = $this->getEntityManager();
        $dql = "SELECT count(m)
            FROM ParteDiarioBundle:DatPartediarioProduccion m
            JOIN m.producto pro
            JOIN m.ueb ueb
            join m.almacen alm
            join m.moneda mon
             where pro = :producto and ueb=:ueb and alm=:almacen and mon=:moneda and m.fecha=:fecha " . $where;
        $consulta = $em->createQuery($dql);
        $consulta->setParameters($dat);
        $existe = $consulta->getSingleScalarResult();
        return $existe;
    }

    public function calcularNivelProd($params, $dia = false)
    {
        $em = $this->getEntityManager();
        $where = '1=1 ';
        $select = $dia == true ? ', g.fecha ' : '';
        $group = $dia == true ? ' GROUP BY g.fecha ORDER BY g.fecha ' : '';
        $where .= ($params['idueb'] != '' && $params['idueb'] != null) ?
            (' and ue =' . $params['idueb'] . '') : '';
        $where .= ($params['fechaCierre'] != '' && $params['fechaCierre'] != null) ?
            ' and g.fecha <=' . "'" . $params['fechaCierre'] . "'" . '' : '';
        $where .= ($params['moneda'] != '' && $params['moneda'] != null) ? ' and g.moneda =' . $params['moneda'] . '' : '';
        $explodeFecha = EnumMeses::convertfecha($params['fecha']);
        if ($params['acumulado'] == 1) {//acumulado en el mes
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ?
                (' and g.fecha >=' . "'" . $explodeFecha['a'] . '-' . $explodeFecha['m'] . '-01' . "'") : '';
        }
        $primerDiaAno = EnumMeses::primerDiaAno($params['fecha']);
        if ($params['acumulado'] == 2) {//acumulado en el año
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ?
                (' and g.fecha >=' . "'" . $primerDiaAno . "'") : '';
        }
        if ($params['acumulado'] == 3) {// mes completo
            $enum = new EnumMeses();
            $range = $enum->intervaloMes($params['fecha']);
            $where .= ' and g.fecha <=' . "'" . $range['fin'] . "'" . ' and g.fecha >=' . "'" . $range['inicio'] . "'" . '';
        }
        if ($params['acumulado'] == 4) {//dia en especifico
            $where .= ' and g.fecha =' . "'" . $params['fecha'] . "'" . '';
        }
        if (is_array($params['idproducto'])) {
            $producto = implode(",", $params['idproducto']);
        } else {
            $producto = $params['idproducto'];
        }
        $where .= ($producto != '' && $producto != null && $producto != ' ') ? (' and p.idproducto in(' . $producto . ')') : '';

        $dql = "SELECT sum(g.cantidad)  as cantidad " . $select . "
            FROM   ParteDiarioBundle:DatPartediarioProduccion g
            join g.producto p
            JOIN g.moneda md
            JOIN g.ueb ue
            WHERE " . $where . $group;
        $consulta_partes = $em->createQuery($dql);

        if ($dia) {
            $result = $consulta_partes->getArrayResult();
        } else {
            $partes = $consulta_partes->getResult();
            $result = (count($partes) > 0 && $partes[0]['cantidad'] != '') ? $partes[0]['cantidad'] : 0;
        }

        return $result;
    }


    public function getNivelActividad($params)
    {
        $hijos = array();
        $hijos = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomproducto')->getProductosHijos($params['producto'], $hijos);
        $hijosProd = implode(',', $hijos);
        $em = $this->getEntityManager();
        $fecha = EnumMeses::convertirFechaParaBD($params['fecha']);

        $dql = 'SELECT SUM(n.cantidad)
            FROM ParteDiarioBundle:DatPartediarioProduccion n
            JOIN n.producto p
            WHERE p.idproducto IN(' . $hijosProd . ') AND n.ueb =' . $params['ueb'] . " AND n.fecha = " . "'" . $fecha . "'";
        $consulta = $em->createQuery($dql);
        $result = $consulta->getSingleResult();
        return $result;
    }

    public function produccionPorFormatoSabor($formato, $sabor, $fecha, $ueb = null, $md, $alias = '', $idgenerico = null)
    {
        $em = $this->getEntityManager();
        $where = '';
        if ($alias != '')
            $where = ' and p.alias like \'' . $alias . '%\' ';
        if ($idgenerico != null)
            $where .= ' and p.idgenerico =' . $idgenerico;
        $where .= ($fecha != ' ') ? (' and g.fecha = \'' . $fecha . ' \'') : '';
        $where .= ($ueb != '' && $ueb != null) ? (' and ue =' . $ueb . '') : '';
        $where .= ($md != '' && $md != null) ? (' and md.id = ' . $md) : '';

        $dql = "SELECT sum(g.cantidad) as cantidad, sum(g.entrega) as entrega
            FROM   ParteDiarioBundle:DatPartediarioProduccion g
            JOIN g.ueb ue
            join g.producto p JOIN g.moneda md
             WHERE p.idproducto in (SELECT pro.idproducto From NomencladorBundle:NomProducto pro 
             where pro.idformato =" . $formato . ' and  pro.idsabor=' . $sabor . ')' . $where;
        $consulta_partes = $em->createQuery($dql);
        //dump($consulta_partes->getSQL());die;
        $partes = $consulta_partes->getArrayResult();
        return $partes;
    }


}
