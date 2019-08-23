<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 29/11/2016
 * Time: 22:08
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;


class DatParteDesvioRepository extends EntityRepository {

    public  function findByMes($params)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT p,c,u
            FROM ParteDiarioBundle:DatParteMercanciaVinculo m
            JOIN m.producto pro
            JOIN m.ueb u
            join m.um um
             where m.fecha <= :fecha and pro = :producto";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fecha',$params['fecha']);
        $consulta->setParameter('producto',$params['producto']);
        $lista = $consulta->getArrayResult();
        if(count($lista)==0)
          return [];
        else
        {
            return $lista;
           /* foreach($lista as $l);
            {

            }*/

        }


    }

    public  function buscar($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m,um,pro
            FROM ParteDiarioBundle:DatParteDesvio m
            JOIN m.producto pro
            join m.um um
             where m.idparte = :id";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id',$id);
        $lista = $consulta->getArrayResult();
        if(count($lista)==0)
          return [];
        else
        {
            return $lista;
        }


    }


} 