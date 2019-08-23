<?php
/**
 * Created by PhpStorm.
 * User: mary
 * Date: 17/11/2016
 * Time: 4:16
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;


class DatPlanAcopioRepository extends EntityRepository
{

    public function findLikeArray($params)
    {
        if (is_array($params['idproducto'])) {
            $producto = implode(",", $params['idproducto']);
        } else {
            $producto = $params['idproducto'];
        }
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('select cp
           from  ParteDiarioBundle:DatPlanAcopio  cp
           where  cp.idtipoplan=:tipo and cp.idproducto in('.$producto.')');
        $consulta->setParameter(
                'tipo' ,$params['idtipoplan']
           );
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
             from ParteDiarioBundle:DatPlanAcopio cp
             where ' . $where);
        $result = $consulta->getArrayResult();

        return $result;

    }


}
















