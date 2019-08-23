<?php

namespace NomencladorBundle\Repository;

use Doctrine\ORM\EntityRepository;

class NomUnidadmedidaRepository extends EntityRepository
{
    public function convertir($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT c
            FROM NomencladorBundle:NomConversion c
            WHERE (c.iduminicio = :inicio AND  a.idumfin =:fin) or (c.idumfin = :inicio AND  a.iduminicio =:fin)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('inicio', $parametros['inicio']);
        $consulta->setParameter('fin', $parametros['fin']);
        $resp= $consulta->getArrayResult();
        if($resp==null)
            return 1;
        elseif($resp['iduminicio']==$parametros['inicio'])
                return $resp[0]['valor'];
            else
                return 1/ $resp[0]['valor'];

    }
}
