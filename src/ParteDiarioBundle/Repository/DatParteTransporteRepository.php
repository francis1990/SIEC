<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 29/11/2016
 * Time: 22:08
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;


class DatParteTransporteRepository extends EntityRepository {

    public  function buscar($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m,c
            FROM ParteDiarioBundle:DatParteTransporte m
            JOIN m.tipotransporte c
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

    public function existe($parte, $id = null)
    {
        $where = '';
        $dat = array(
            'tipo' => $parte['tipo'],
            'fecha' => date_format( $parte['fecha'], 'Y-m-d'),
            'ueb' => $parte['ueb'],

        );
        if ($id != null) {
            $dat['id'] = $id;
            $where = ' and m.idparte <> :id';
        }
        $em = $this->getEntityManager();
        $dql = "SELECT count(m)
            FROM ParteDiarioBundle:DatParteTransporte m
            join m.tipotransporte t
            JOIN m.ueb ueb
             where t = :tipo and ueb=:ueb and  m.fecha=:fecha " . $where;
        $consulta = $em->createQuery($dql);
        $consulta->setParameters($dat);
        $existe = $consulta->getSingleScalarResult();
        return $existe;
    }


} 