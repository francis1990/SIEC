<?php
/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 1/29/2018
 * Time: 2:10 PM
 */

namespace NomencladorBundle\Services;

use Doctrine\ORM\EntityManager;
use NomencladorBundle\Entity\NomEntidad;

class EntidadService
{
    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getNombreByIds($ent)
    {
        if (is_array($ent)) {
            $ent = implode(',', $ent);
        }
        $data = array();
        $dql = 'SELECT v
            FROM NomencladorBundle:NomEntidad v
            WHERE v.vinculo=true AND v.identidad IN(' . $ent . ')';
        $consulta = $this->em->createQuery($dql);
        $vinculos = $consulta->getResult();
        foreach ($vinculos as $v) {
            $data[] = $v->getNombre();
        }
        return $data;
    }

    public function getVinculosAll()
    {
        $data = array();
        $dql = 'SELECT v
            FROM NomencladorBundle:NomEntidad v
            WHERE v.vinculo=true';
        $consulta = $this->em->createQuery($dql);
        $vinculos = $consulta->getResult();
        foreach ($vinculos as $v) {
            $data[] = $v->getIdentidad();
        }
        return $data;
    }

    public function getNombreVinculosAll()
    {
        $data = array();
        $dql = 'SELECT v
            FROM NomencladorBundle:NomEntidad v
            WHERE v.vinculo=true';
        $consulta = $this->em->createQuery($dql);
        $vinculos = $consulta->getResult();
        foreach ($vinculos as $v) {
            $data[] = $v->getNombre();
        }
        return $data;
    }

    public function getEntidadesHijasDadoEnt(NomEntidad $entidad, $hijos, $nom = false, $obj = false)
    {
        $where = "";
        if ($entidad->getHoja()) {
            if (!$obj) {
                $hijos[] = $entidad->getIdentidad();
            } else {
                $hijos[] = $entidad;
            }
            return $hijos;
        } else {
            if (!$nom) {
                $where = " AND g.activo = true";
            }
            $arraR = array();
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomEntidad g
            WHERE g.idpadre = :idpadre' . $where;
            $consulta = $this->em->createQuery($dqlhijos);
            $consulta->setParameter('idpadre',$entidad->getIdentidad() );
            if (!$obj) {
                $hijos[] = $entidad->getIdentidad();
            } else {
                $hijos[] = $entidad;
            }
            if (count($consulta->getResult()) > 0) {
                $arraR = $consulta->getResult();
                foreach ($arraR as $pr) {
                    $hijos = $this->getEntidadesHijasDadoEnt($pr, $hijos, $nom, $obj);
                }
            }
            return $hijos;
        }
    }
}