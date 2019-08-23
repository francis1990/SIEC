<?php
/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 1/25/2018
 * Time: 4:19 PM
 */

namespace NomencladorBundle\Services;

use ReporteBundle\Util\Util;
use NomencladorBundle\Entity\NomPortador;
use Doctrine\ORM\EntityManager;

class PortadorService
{
    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getPortadoresByIds($array)
    {
        $ports = implode(",", $array);
        $dql = 'SELECT g
            FROM NomencladorBundle:NomPortador g
            WHERE  g.idportador in (' . $ports . ') ';
        $consulta = $this->em->createQuery($dql);
        $prores = $consulta->getResult();

        return $prores;
    }

}