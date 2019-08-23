<?php
/**
 * Created by PhpStorm.
 * User: maria.caro
 * Date: 05/06/2017
 * Time: 01:11 PM
 */

namespace NomencladorBundle\Services;

use NomencladorBundle\Entity\NomGrupointeres;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;

class GrupoService
{
    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getGruposByIds($array)
    {
        $grups = implode(",", $array);
        $dql = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE   g.idgrupointeres in (' . $grups . ') ';
        $consulta = $this->em->createQuery($dql);
        $prores = $consulta->getResult();

        return $prores;
    }

    public function getHijosGrupos(NomGrupointeres $grupointeres)
    {
        $data = array(
            'id' => $grupointeres->getIdgrupointeres(),
            'text' => $grupointeres->getNombre(),
            'icon' => "fa fa-sitemap",
            'li_attr' => array(
                'data-nivel' => $grupointeres->getNivel()
            )
        );

        $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE g.idpadre = ' . $grupointeres->getIdgrupointeres();

        $consulta = $this->em->createQuery($dqlhijos);
        $grupos = $consulta->getResult();

        if (count($grupos) > 0) {
            foreach ($grupos as $pr) {
                $data['children'][] = $this->getHijosGrupos($pr);
            }
        }
        return $data;
    }

    public function maxNivel()
    {
        $dql = "SELECT max(p.nivel) as nivel
            FROM  NomencladorBundle:NomGrupointeres p";
        $consulta_partes = $this->em->createQuery($dql);
        $resp = $consulta_partes->getSingleScalarResult();
        return $resp;
    }

    public function getChildren($node)
    {
        $data = array();
        $hijos = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE   g.idpadre = :padre';
        $consulta = $this->em->createQuery($hijos);
        $consulta->setParameter('padre', $node);
        $productos = $consulta->getResult();
        foreach ($productos as $p) {
            $data[] = array('id' => $p->getIdgrupointeres(), 'text' => $p->getNombre(), 'icon' => "fa fa-sitemap",
                'li_attr' => array(
                    'data-nivel' => $p->getNivel()
                ), 'children' => !$p->getHoja()
            );
        }
        return $data;
    }


    public function precioProductoOtro($pro, $ent)
    {
        $grupo = $this->em->getRepository('NomencladorBundle:NomGrupointeres')->findByIdentidad($ent);
        if (count($grupo) == 0)
            return null;
        else {

        }
        $dql = 'SELECT p.preciomn,p.preciocuc
            FROM NomencladorBundle:NomPrecio p
            JOIN p.grupo g
            join p.producto pro
            WHERE   pro.idproducto= :producto and g.identidad=:entidad ';
        $consulta = $this->em->createQuery($dql);
        $consulta->setParameter('producto', $pro);
        $consulta->setParameter('entidad', $ent);
        $precio = $consulta->getResult();
        return $precio;

    }

    public function getParents(NomGrupointeres $grupo, $parents)
    {
        if ($grupo->getIdpadre() == 0) {
            $parents[] = $grupo->getIdgrupointeres();
            return $parents;
        } else {
            $padre = $this->em->getRepository('NomencladorBundle:NomGrupointeres')->find($grupo->getIdpadre());
            return $this->getParents($padre, $parents);
        }
    }

    public function comprobarExisGrupoProd($prod, $grupo)
    {
        $dql = 'SELECT p
            FROM NomencladorBundle:NomPrecio p
            WHERE   p.producto= :producto and p.grupo=:grupo ';
        $consulta = $this->em->createQuery($dql);
        $consulta->setParameter('producto', $prod);
        $consulta->setParameter('grupo', $grupo);
        $cantResult = count($consulta->getArrayResult());
        return $cantResult;
    }

    public function getGruposInteresDadoProducto($params)
    {
        $resultado = array();
        $sql = 'SELECT p,g,pr
            FROM NomencladorBundle:NomPrecio p
            JOIN p.grupo g
            JOIN p.producto pr
            WHERE p.producto = :producto AND p.preciomn = :preciomn AND p.preciocuc = :preciocuc AND p.um = :um';
        $consulta = $this->em->createQuery($sql);
        $consulta->setParameter('producto', $params['producto']);
        $consulta->setParameter('preciomn', $params['preciomn']);
        $consulta->setParameter('preciocuc', $params['preciocuc']);
        $consulta->setParameter('um', $params['um']);
        $result = $consulta->getArrayResult();

        foreach ($result as $index => $v) {
            $resultado[$index]['grupo'] = $v['grupo']['idgrupointeres'];
            $resultado[$index]['idprecio'] = $v['id'];
            $resultado[$index]['idpadre'] = $v['idpadre'];
            $resultado[$index]['producto'] = $v['producto']['idproducto'];
        }
        return $resultado;
    }

    public function getEntidadesHijasByGrupos($arreglo)
    {
        $result = array();
        foreach ($arreglo as $enti) {
            ($enti != null) ? $result = $this->getEntidadesHijas($enti, $result) : '';
        }
        return $result;
    }

    public function getEntidadesHijas(NomGrupointeres $grupointeres, $hijas, $obj = null,$tipogrupo = false)
    {
        if (($grupointeres->getHoja()) && $grupointeres->getIdentidad() != null) {
            if ($obj == null)
                $hijas[] = $grupointeres->getIdentidad()->getIdentidad();
            else {
                $hijas[] = !$tipogrupo ? $grupointeres->getIdentidad(): $grupointeres;
            }
        } else {
            $arraR = array();
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE   g.idpadre = ' . $grupointeres->getIdgrupointeres();
            $consulta = $this->em->createQuery($dqlhijos);
            if (count($consulta->getResult()) > 0) {
                $arraR = $consulta->getResult();
                foreach ($arraR as $pr) {
                    $hijas = $this->getEntidadesHijas($pr, $hijas, $obj,$tipogrupo);
                }
            }
        }
        return array_unique($hijas);
    }

    public function getGruposHijosByGrupos($arreglo, $params = null)
    {
        $result = array();
        foreach ($arreglo as $gru) {
            ($gru != null) ? $result = $this->getGruposHijos($gru, $result, $params) : '';
        }
        return $result;
    }

    public function getGruposHijos(NomGrupointeres $grupointeres, $hijas, $params = null, $nom = false, $obj = false)
    {
        if ($grupointeres->getHoja()) {
            if (!$obj) {
                $hijas[] = $grupointeres->getIdgrupointeres();
            } else {
                $hijas[] = $grupointeres;
            }
            return $hijas;
        } else {
            if (!$nom) {
                $where = " AND g.activo = true";
            }
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE   g.idpadre = :idpadre ' . $where;
            $consulta = $this->em->createQuery($dqlhijos);
            $consulta->setParameter('idpadre', $grupointeres->getIdgrupointeres());
            if (!$obj) {
                $hijas[] = $grupointeres->getIdgrupointeres();
            } else {
                $hijas[] = $grupointeres;
            }
            if (count($consulta->getResult()) > 0) {
                $arraR = $consulta->getResult();
                foreach ($arraR as $pr) {
                    $hijas = $this->getGruposHijos($pr, $hijas, $params, $nom, $obj);
                }
            }
        }
        return $hijas;
    }

    public function getGruposPadres(NomGrupointeres $grupo, $padres, $params = null)
    {
        if ($grupo->getIdpadre() == 0) {
            return $padres;
        } else {
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE   g.idgrupointeres = ' . $grupo->getIdpadre();

            $consulta = $this->em->createQuery($dqlhijos);
            $padre = $consulta->getResult();
            $padres[] = $grupo->getIdpadre();
            return $padres = $this->getGruposPadres($padre[0], $padres, $params);
        }
    }
}