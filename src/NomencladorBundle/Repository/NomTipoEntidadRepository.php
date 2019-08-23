<?php

namespace NomencladorBundle\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;

/**
 * NomTipoEntidadRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NomTipoEntidadRepository extends EntityRepository
{
    public function insertarTipoEntidad($nomTipoEnt)
    {
        $em = $this->getEntityManager();
        $em->persist($nomTipoEnt);
        $em->flush();
    }

    public function listarTiposEnt($params)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT te FROM NomencladorBundle:NomTipoEntidad te ORDER BY te.nombre';
        $consulta = $em->createQuery($dql);
        $result['total'] = count($consulta->getResult());
        $consulta->setFirstResult($params['start']);
        $consulta->setMaxResults($params['limit']);
        $resultado = $consulta->getResult();
        $result['datos'] = array();
        foreach ($resultado as $pr) {
            $res = array();
            $res[] = $pr->getIdtipoentidad();
            $res[] = $pr->getNombre();
            $res[] = $pr->getAbreviatura();
            $result['datos'][] = $res;
        }

        return $result;
    }
    public function findCoincidenciaEditar($params)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT te FROM NomencladorBundle:NomTipoEntidad te WHERE te.nombre = :nombre AND te.abreviatura = :abreviatura';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $params['nombre']);
        $consulta->setParameter('abreviatura', $params['abreviatura']);
        $resultado = $consulta->getResult();
        return $resultado;
    }
}
