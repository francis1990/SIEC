<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 29/11/2016
 * Time: 22:08
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;


class DatParteAseguramientoRepository extends EntityRepository
{

    public function buscar($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m,mat,u,um
            FROM ParteDiarioBundle:DatParteAseguramiento m
           JOIN m.materiaprima mat
            JOIN m.ueb u
            join m.um um
             where m.idparte = :id";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);
        $lista = $consulta->getArrayResult();
        if (count($lista) == 0)
            return [];
        else {
            return $lista;
        }
    }

    public function getAseguramientosByUeb($params)
    {
        $em = $this->getEntityManager();
        $where = "";
        $asegImpl = "";
        if (isset($params['idueb']) && $params['idueb'] != "") {
            $where = "AND p.ueb = " . $params['idueb'];
        }
        if (isset($params['hijosAseg']) && count($params['hijosAseg']) > 0) {
            $asegImpl = implode(',', $params['hijosAseg']);
        }

        $dql = "SELECT SUM(p.existencia) as existencia, SUM(p.reserva) as reserva 
                FROM ParteDiarioBundle:DatParteAseguramiento p
                JOIN p.ueb u
                JOIN p.materiaprima po
                WHERE p.materiaprima in ($asegImpl) AND p.fecha <= :fecha " . $where . " 
                ORDER BY p.materiaprima,p.ueb ";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fecha', $params['fecha']);
        $result = $consulta->getResult();
        if ($result[0]['existencia'] == null && $result[0]['reserva'] == null) {
            $result = array();
        }

        return $result;
    }


} 