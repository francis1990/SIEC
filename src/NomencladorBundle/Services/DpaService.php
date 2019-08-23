<?php
/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 2/8/2018
 * Time: 3:11 PM
 */

namespace NomencladorBundle\Services;

use Doctrine\ORM\EntityManager;
use NomencladorBundle\Entity\NomDpa;

class DpaService
{
    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getDpaHijos(NomDpa $dpa, $hijos, $nom = false, $obj = false)
    {
        if ($activar) {
            $dpa->setActivo($estado);
            $this->em->persist($dpa);
        }
        $where = "";
        if ($dpa->getHoja()) {
            if (!$obj) {
                $hijos[] = $dpa->getIddpa();
            } else {
                $hijos[] = $dpa;
            }
            return $hijos;
        } else {
            if (!$nom) {
                $where = " AND g.activo = true";
            }
            $arraR = array();
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomDpa g
            WHERE  g.idpadre = ' . $dpa->getIddpa() . $where;
            $consulta = $this->em->createQuery($dqlhijos);
            if (!$obj) {
                $hijos[] = $dpa->getIddpa();
            } else {
                $hijos[] = $dpa;
            }
            if (count($consulta->getResult()) > 0) {
                $arraR = $consulta->getResult();
                foreach ($arraR as $pr) {
                    $hijos = $this->getDpaHijos($pr, $hijos, $nom, $obj, $activar, $estado);
                }
            }
            return $hijos;
        }
    }

    public function getDpaPadres(NomDpa $dpa, $padres, $params = null)
    {
        if ($dpa->getIdpadre() == 0) {
            return $padres;
        } else {
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomDpa g
            WHERE   g.iddpa = ' . $dpa->getIdpadre();

            $consulta = $this->em->createQuery($dqlhijos);
            $padre = $consulta->getResult();
            $padres[] = $dpa->getIdpadre();
            return $padres = $this->getDpaPadres($padre[0], $padres, $params);
        }
    }
}