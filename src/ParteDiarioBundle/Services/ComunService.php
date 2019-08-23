<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 30/5/2018
 * Time: 22:19
 */

namespace ParteDiarioBundle\Services;


use Doctrine\ORM\EntityManager;

class ComunService
{
    private $em;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function deleteOne($entidad, $id)
    {
        $objElim = $this->em->getRepository($entidad)->find($id);
        $this->em->remove($objElim);
        $this->em->flush();
    }

    public function getObjPadres($obj, $padres, $params = null)
    {
        $params['bundle'] = isset($params['bundle']) ? $params['bundle'] : "ParteDiarioBundle:";
        if ($obj->getIdpadre() == 0) {
            return $padres;
        } else {
            $dqlhijos = 'SELECT g
            FROM ' .$params['bundle'] . $params['tabla'] . ' g
            WHERE   g.' . $params['campoId'] . ' = ' . $obj->getIdpadre();

            $consulta = $this->em->createQuery($dqlhijos);
            $padre = $consulta->getResult();
            $padres[] = $obj->getIdpadre();
            return $padres = $this->getObjPadres($padre[0], $padres, $params);
        }
    }

}