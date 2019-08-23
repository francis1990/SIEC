<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 29/11/2016
 * Time: 22:08
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use DateTime;


class DatParteCuentasCobrarRepository extends EntityRepository {


    public  function buscar($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT cli,cu,mon,cc
            FROM ParteDiarioBundle:DatParteCuentasCobrar cc
            JOIN cc.cliente cli
            JOIN cc.moneda mon
            join cc.idcuentacontable cu
             where cc.idparte = :id";
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
    public  function pagar($id)
    {
        $date=date('Y-m-d');
        $em = $this->getEntityManager();
        $dql = "UPDATE ParteDiarioBundle:DatParteCuentasCobrar cc SET cc.montovencido = 0, cc.diasvencido = 0,
          cc.fecha_pagada=:date
             where cc.idparte = :id";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id',$id);
        $consulta->setParameter('date',$date);
        $lista = $consulta->getResult();
        if(count($lista)==0)
            return [];
        else
        {
            return $lista;
        }
    }
} 