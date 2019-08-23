<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 7/5/2018
 * Time: 14:04
 */

namespace AdminBundle\Repository;


use Doctrine\ORM\EntityRepository;

class DatConfigRepository extends EntityRepository
{
    public function obtenerDatosEmpresa()
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT config FROM AdminBundle:DatConfig config';
        $consulta = $em->createQuery($dql);
        $result = $consulta->getResult();
        return $result;
    }

}