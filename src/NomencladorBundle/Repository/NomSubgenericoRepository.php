<?php

namespace NomencladorBundle\Repository;

use Doctrine\ORM\EntityRepository;

class NomSubgenericoRepository extends EntityRepository{

    public function findCoincidencia($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT s
            FROM NomencladorBundle:NomSubgenerico s
            WHERE (s.nombre = :nombre
            OR s.codigo = :codigo) AND s.generico= :generico AND s.idsubgenerico <> :idsubgenerico';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros['nombre']);
        $consulta->setParameter('codigo', $parametros['codigo']);
        $consulta->setParameter('generico', $parametros['generico']);
        $consulta->setParameter('idsubgenerico', $parametros['id']);
        return $consulta->getResult();
    }

    public function findCoincidenciaNuevo($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT s
            FROM NomencladorBundle:NomSubgenerico s
            WHERE s.nombre = :nombre
            OR s.codigo = :codigo';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros['nombre']);
        $consulta->setParameter('codigo', $parametros['codigo']);
        return $consulta->getResult();
    }
    public function listar($where,$st,$lm)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT g
            FROM NomencladorBundle:NomSubgenerico g
            JOIN g.generico gen WHERE' . $where .' ORDER BY g.codigo ';
        $consulta = $em->createQuery($dql);
        $result['total'] = count($consulta->getResult());
        $consulta->setFirstResult($st);
        $consulta->setMaxResults($lm);
        $result['datos'] = $consulta->getResult();
        return $result;
    }

} 