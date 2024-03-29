<?php

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DatAlertaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DatAlertaRepository extends EntityRepository
{

    public function listar($where,$st,$lm)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT g
            FROM ParteDiarioBundle:DatAlerta g
            JOIN g.entidad ent WHERE' . $where .' ORDER BY g.codigo ';
        $consulta = $em->createQuery($dql);
        $result['total'] = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $result['datos'] = $consulta->getResult();
        return $result;
    }

    public function findSinAccion($user)
    {
        $f=date('Y-m-d');
        $em = $this->getEntityManager();
        $dql = 'SELECT g
            FROM ParteDiarioBundle:DatAlerta g
            JOIN g.entidad ent WHERE g.idalerta not in (
            select alert.idalerta from ParteDiarioBundle:DatAlertaAccion a 
            join a.alerta alert 
            join a.usuario u
            where u.idUsuario='.$user->getId().' and a.fecha =\''.$f.'\' )  ';
        $consulta = $em->createQuery($dql);
        return $consulta->getResult();

    }

}

