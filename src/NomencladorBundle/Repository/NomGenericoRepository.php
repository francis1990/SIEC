<?php

namespace NomencladorBundle\Repository;

use Doctrine\ORM\EntityRepository;

class NomGenericoRepository extends EntityRepository
{
    public function findCoincidencia($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT g
            FROM NomencladorBundle:NomGenerico g
            WHERE (g.nombre = :nombre
            OR g.codigo = :codigo) AND g.idgenerico <> :idgenerico';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros['nombre']);
        $consulta->setParameter('codigo', $parametros['codigo']);
        $consulta->setParameter('idgenerico', $parametros['id']);
        return $consulta->getResult();
    }
    public function findCoincidenciaNuevo($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT g
            FROM NomencladorBundle:NomGenerico g
            WHERE g.nombre = :nombre
            OR g.codigo = :codigo';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros['nombre']);
        $consulta->setParameter('codigo', $parametros['codigo']);
        return $consulta->getResult();
    }
}
