<?php
/**
 * Created by PhpStorm.
 * User: maria.caro
 * Date: 05/06/2017
 * Time: 01:11 PM
 */

namespace NomencladorBundle\Services;

use NomencladorBundle\Entity\NomAseguramiento;
use NomencladorBundle\Entity\NomProducto;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;

class AseguramientoService
{
    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getAsegByIds($array)
    {
        $asegs = implode(",", $array);
        $dql = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE   g.idaseguramiento in (' . $asegs . ') ORDER BY g.idpadre';
        $consulta = $this->em->createQuery($dql);
        $aseg = $consulta->getResult();
        return $aseg;
    }

    public function getAseguramientosAll()
    {
        $dql = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE g.mpb is null OR g.mpb = 0';
        $consulta = $this->em->createQuery($dql);
        $aseg = $consulta->getResult();
        return $aseg;
    }

    public function getHijosAseguramiento(NomAseguramiento $ase)
    {
        $where = '1=1 ';
        $where .= ($ase) ? (' and  g.idpadre =' . $ase->getIdaseguramiento()) : '';
        $data = array(
            'id' => $ase->getIdaseguramiento(),
            'text' => $ase->getNombre(),
            'icon' => "fa fa-sitemap",
            'li_attr' => array(
                'data-nivel' => $ase->getNivel()
            )
        );

        $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE g.idpadre = ' . $ase->getIdaseguramiento();

        $consulta = $this->em->createQuery($dqlhijos);

        $asegs = $consulta->getResult();
        if (count($asegs) > 0) {
            foreach ($asegs as $pr) {
                $data['children'][] = $this->getHijosAseguramiento($pr);
            }
        }
        return $data;
    }

    public function maxNivel()
    {
        $dql = "SELECT max(p.nivel) as nivel
            FROM  NomencladorBundle:NomAseguramiento p";
        $consulta_partes = $this->em->createQuery($dql);
        $re = $consulta_partes->getSingleScalarResult();
        return $re == null ? -1 : $re;
    }

    public function getChildren($node)
    {
        $data = array();
        $hijos = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE   g.idpadre = :padre';
        $consulta = $this->em->createQuery($hijos);
        $consulta->setParameter('padre', $node);
        $aseg = $consulta->getResult();
        foreach ($aseg as $a) {
            $data[] = array(
                'id' => $a->getIdaseguramiento(),
                'text' => $a->getNombre(),
                'icon' => "fa fa-sitemap",
                'li_attr' => array(
                    'data-nivel' => $a->getNivel()
                ),
                'children' => !$a->getHoja()
            );
        }
        return $data;
    }

    public function getChildrenSinAsegMPB($node)
    {
        $data = array();
        $hijos = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE   g.idpadre = :padre AND (g.mpb is null OR g.mpb = 0 OR g.mpb = false)';
        $consulta = $this->em->createQuery($hijos);
        $consulta->setParameter('padre', $node);
        $aseg = $consulta->getResult();
        foreach ($aseg as $a) {
            $data[] = array(
                'id' => $a->getIdaseguramiento(),
                'text' => $a->getNombre(),
                'icon' => "fa fa-sitemap",
                'li_attr' => array(
                    'data-nivel' => $a->getNivel()
                ),
                'children' => !$a->getHoja()
            );
        }
        return $data;
    }

    public function getHijosAseguramiento2($ase)
    {
        $data = array();
        $id = ($ase != null) ? $ase->getIdaseguramiento() : '0';
        $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE g.idpadre = ' . $id;
        $consulta = $this->em->createQuery($dqlhijos);
        $asegs = $consulta->getResult();
        if (count($asegs) > 0) {
            foreach ($asegs as $pr) {
                $node = array();
                $node['id'] = $pr->getIdaseguramiento();
                $node['text'] = $pr->getNombre();
                $node['state'] = !$pr->getHoja() ? 'closed' : 'open';
                $data[] = $node;
            }
        }
        return $data;
    }

    public function getAseguramientosByIds($array)
    {
        $asegs = implode(",", $array);
        $dql = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE g.activo = true AND g.idaseguramiento in (' . $asegs . ') ';
        $consulta = $this->em->createQuery($dql);
        $asegss = $consulta->getResult();
        return $asegss;
    }

    public function getAseguramientosHijosByAsegu($array)
    {
        $arreglo = array();
        $asegs = self::getAseguramientosByIds($array);
        foreach ($asegs as $enti) {
            $arreglo = $this->getAseguramientoHijas($enti, $arreglo,null,false,true);
        }
        return $arreglo;
    }

    public function getAseguramientoHijas(NomAseguramiento $aseg, $hijas, $params = null, $nom = false, $obj = false)
    {

        if ($aseg->getHoja()) {
            if (!$obj) {
                $hijas[] = $aseg->getIdaseguramiento();
            } else {
                $hijas[] = $aseg;
            }
            return $hijas;
        } else {
            $arraR = array();
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE   g.idpadre = ' . $aseg->getIdaseguramiento();
            $consulta = $this->em->createQuery($dqlhijos);
            if (!$obj) {
                $hijas[] = $aseg->getIdaseguramiento();
            } else {
                $hijas[] = $aseg;
            }

            if (count($consulta->getResult()) > 0) {
                $arraR = $consulta->getResult();
                foreach ($arraR as $pr) {
                    $hijas = $this->getAseguramientoHijas($pr, $hijas, $params, $nom, $obj);
                }
            }
        }
        return $hijas;
    }

    public function getAseguramientoPadres(NomAseguramiento $aseg, $padres, $params)
    {
        if ($aseg->getIdpadre() == 0) {
            return $padres;
        } else {
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE   g.idaseguramiento = ' . $aseg->getIdpadre();

            $consulta = $this->em->createQuery($dqlhijos);
            $padre = $consulta->getResult();
            $padres['id'][] = $aseg->getIdpadre();
            $padres['obj'][] = $padre[0];
            return $padres = $this->getAseguramientoPadres($padre[0], $padres, $params);
        }
    }

    public function findByIds($params)
    {
        $aseImpl = implode(',', $params['arbolizq_array']);
        $dql = "SELECT ue,um FROM NomencladorBundle:NomAseguramiento ue
        JOIN ue.idunidadmedida um
         where ue.idaseguramiento in(" . $aseImpl . ")";
        $consulta = $this->em->createQuery($dql);
        $result = $consulta->getResult();
        return $result;
    }

    public function obtenerAsegSegunOrdenMPB()
    {
        $dql = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE g.mpb is not null AND g.ordenmpb is not null
            AND g.mpb != false AND g.ordenmpb != false
            ORDER BY g.ordenmpb';
        $consulta = $this->em->createQuery($dql);
        $aseg = $consulta->getResult();
        return $aseg;
    }

}