<?php
/**
 * Created by PhpStorm.
 * User: mary
 * Date: 17/11/2016
 * Time: 4:16
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;


class DatPlanProduccionRepository extends EntityRepository
{

    public function findLikeArray($params)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('select cp
           from  ParteDiarioBundle:DatPlanProduccion  cp
           where (cp.idproducto = :producto and cp.idtipoplan=:tipo) or cp.idmonedadestino is null');
        $consulta->setParameters(array('producto' => $params['idproducto'], 'tipo' => $params['idtipoplan']));
        $lista = $consulta->getArrayResult();
        return $lista;
    }

    public function existenciaPlan($params, $producto)
    {
        $where = '1=1';
        if ($params['tipoplan'] != '') {
            $where .= ' AND cp.idtipoplan  =' . $params['tipoplan'];
        }
        if ($params['idueb'] != '') {
            $where .= ' AND cp.idueb =' . $params['idueb'];
        }
        if ($producto != '') {
            $where .= ' AND cp.idproducto =' . $producto;
        }
        if ($params['moneda'] != '') {
            $where .= ' AND cp.idmonedadestino =' . $params['moneda'];
        }

        $em = $this->getEntityManager();
        $consulta = $em->createQuery(
            'select cp
                 from ParteDiarioBundle:DatPlanProduccion cp
                 where ' . $where);
        $result = $consulta->getArrayResult();
        return $result;
    }


    public function verificarSiProdEsTotal($params)
    {
        if (isset($params['monedasImp'])) {
            $params['moneda'] = $params['monedasImp'];
        }
        $where = "1=1";
        $where .= $params['idueb'] != null || $params['idueb'] != '' ? ' AND ue.idueb =' . $params['idueb'] : "";
        if (is_array($params['idproducto'])) {
            $params['idproducto'] = implode(',', $params['idproducto']);
        }
        $where .= $params['idproducto'] != null || $params['idproducto'] != '' ? ' and  p.idproducto IN(' . $params['idproducto'] . ')' : "";
        $where .= $params['moneda'] != null || $params['moneda'] != '' ? ' and md.id IN(' . $params['moneda'] . ')' : "";
        $em = $this->getEntityManager();
        $dql = 'SELECT n
            FROM ParteDiarioBundle:DatPlanProduccion n
            JOIN n.idmonedadestino md
            JOIN n.idproducto p
            JOIN n.idueb ue
            WHERE ' . $where;
        $consulta = $em->createQuery($dql);
        $result = $consulta->getResult();
        return $result;
    }

}
















