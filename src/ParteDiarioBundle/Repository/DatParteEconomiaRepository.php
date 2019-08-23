<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 29/11/2016
 * Time: 22:08
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;


class DatParteEconomiaRepository extends EntityRepository {

    public  function findByMes($mes)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT p,c,u
            FROM ParteDiarioBundle:DatPartediarioEconomia p
            JOIN p.idcuentacontable c
            JOIN p.ueb u
             where p.fecha like :mes";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('mes','%-'.$mes.'-%');
        $lista = $consulta->getArrayResult();
        if(count($lista)==0)
          return [];
        else
            return $lista;

    }
    public  function findCoincidenciaNueva($param)
    {
        $fecha=date_create_from_format('d/m/Y', $param['fecha']);
        $em = $this->getEntityManager();
        $dql = "SELECT count(p)
            FROM ParteDiarioBundle:DatPartediarioEconomia p
            JOIN p.idcuentacontable c
            JOIN p.ueb u
             where u = :ueb and c =:cuenta and p.fecha = :fecha";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fecha',date_format($fecha,'Y-m-d'));
        $consulta->setParameter('ueb',$param['ueb']);
        $consulta->setParameter('cuenta',$param['cuenta']);
        $res = $consulta->getSingleScalarResult();
        return $res;
    }
    public  function buscar($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m,c
            FROM ParteDiarioBundle:DatPartediarioEconomia m
            JOIN m.idcuentacontable c
             where m.idpartecuenta = :id";
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