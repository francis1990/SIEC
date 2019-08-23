<?php
/**
 * Created by PhpStorm.
 * User: mary
 * Date: 17/11/2016
 * Time: 4:16
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;


class DatPlanVentaRepository extends EntityRepository
{

    public function listarHijos($entidad,$id)
    {
        $em = $this->getEntityManager();print_r('select cp
                                          from  ' . $entidad . '  cp
                                          where cp.idpadre =' . $id);

        $consulta = $em->createQuery('select cp
                                          from  ' . $entidad . '  cp
                                          where cp.idpadre =' . $id);
        return $consulta->getResult();
    }


}
















