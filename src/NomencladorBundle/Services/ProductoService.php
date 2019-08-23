<?php
/**
 * Created by PhpStorm.
 * User: maria.caro
 * Date: 05/06/2017
 * Time: 01:11 PM
 */

namespace NomencladorBundle\Services;

use Doctrine\ORM\EntityManager;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Util\Util;

class ProductoService
{

    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getProductosByIds($array)
    {
        $prods = implode(",", $array);
        $dql = 'SELECT g,ge,um
            FROM NomencladorBundle:NomProducto g
            JOIN g.idgenerico ge
            LEFT JOIN g.umOperativa um
            WHERE g.activo = true AND g.idproducto in (' . $prods . ') ';
        $consulta = $this->em->createQuery($dql);
        $prores = $consulta->getResult();
        return $prores;
    }

    public function getProductosHijosByProductos($array, $obj = null, $acopio = false)
    {
        $arreglo = array();
        $prods = self::getProductosByIds($array);
        if ($obj) {
            foreach ($prods as $enti) {
                $arreglo = $this->getProductosHijos($enti, $arreglo, false, true, false, false, $acopio);
            }
        } else {
            foreach ($prods as $enti) {
                $arreglo = $this->getProductosHijos($enti, $arreglo, false, $obj, false, false, $acopio);
            }
        }
        return $arreglo;
    }

    public function getArrays($array)
    {
        $prores = self::getProductosByIds($array);;
        $arrayAcopio = $arrayProd = array();
        foreach ($prores as $p) {
            if ($p->getIdgenerico()->getAcopio() == 1) {
                $arrayAcopio[] = $p;
            } else {
                $arrayProd[] = $p;
            }
        }
        return array('acopio' => $arrayAcopio, 'producto' => $arrayProd);
    }

    public function getProductosHijos(
        NomProducto $producto,
        $hijos,
        $nom = false,
        $obj = false,
        $sabor = false,
        $nivelsabor = false,
        $acopio = false
    )
    {
        $where = "";
        $join = "";
        $where .= !$acopio ? ' AND p.acopio = false' : ' AND p.acopio = true';
        $join .= !$acopio ? ' JOIN g.idgenerico p' : ' JOIN g.idgenerico p';
        if ($producto->getHoja() || ($nivelsabor && $producto->getIdsabor() != null)) {
            if (!$obj) {
                $hijos[] = $producto->getIdproducto();
            } else {
                $hijos[] = $producto;
            }
            return $hijos;
        } else {
            if (!$nom) {
                $where .= " AND g.activo = true";
            }

            if ($sabor) {
                $where .= " AND f.id is null AND gen.acopio = false";
                $join .= " LEFT JOIN g.idformato f";
                $join .= " JOIN g.idgenerico gen";
            }

            $arraR = array();
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomProducto g' . $join . '
            WHERE  g.idpadre = ' . $producto->getIdproducto() . $where;
            $consulta = $this->em->createQuery($dqlhijos);
            if (!$obj) {
                $hijos[] = $producto->getIdproducto();
            } else {
                $hijos[] = $producto;
            }
            if (count($consulta->getResult()) > 0) {
                $arraR = $consulta->getResult();
                foreach ($arraR as $pr) {
                    $hijos = $this->getProductosHijos($pr, $hijos, $nom, $obj);
                }
            }
            return $hijos;
        }
    }

    public function getProductosPadres(NomProducto $prod, $padres, $params = null)
    {
        if ($prod->getIdpadre() == 0) {
            return $padres;
        } else {
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomProducto g
            WHERE g.idproducto = ' . $prod->getIdpadre();
            $consulta = $this->em->createQuery($dqlhijos);
            $padre = $consulta->getResult();
            $padres['id'][] = $prod->getIdpadre();
            $padres['obj'][] = $padre[0];
            return $padres = $this->getProductosPadres($padre[0], $padres, $params);
        }
    }

    public function maxNivel()
    {
        $dql = "SELECT max(p.nivel) as nivel
            FROM  NomencladorBundle:NomProducto p";
        $consulta_partes = $this->em->createQuery($dql);
        $resp = $consulta_partes->getSingleScalarResult();
        return $resp;
    }

    public function getChildren($node, $where = null)
    {
        $wr = '';
        if ($where != null) {
            $wr = ' and ' . $where;
        }
        $data = array();
        $hijos = 'SELECT g
            FROM NomencladorBundle:NomProducto g
            WHERE g.activo = true AND g.idpadre = :padre' . $wr;
        $consulta = $this->em->createQuery($hijos);
        $consulta->setParameter('padre', $node);
        $productos = $consulta->getResult();
        foreach ($productos as $p) {
            $nodo = array();
            $nodo['id'] = $p->getIdproducto();
            $nodo['text'] = $p->getNombre();
            $nodo['icon'] = "fa fa-sitemap";
            $nodo['li_attr'] = array(
                'data-acopio' => $p->getIdgenerico()->getAcopio() == true ? 1 : 0,
                'data-nivel' => $p->getNivel()
            );
            $nodo['children'] = !$p->getHoja();
            $data[] = $nodo;
        }
        return $data;
    }

    public function getParents(NomProducto $producto, $parents)
    {
        if ($producto->getIdpadre() == 0) {
            $parents[] = $producto->getIdproducto();
            return $parents;
        } else {
            $padre = $this->em->getRepository('NomencladorBundle:NomProducto')->find($producto->getIdpadre());
            return $this->getParents($padre, $parents);
        }
    }

    public function getHijosDadoProducto($params)
    {
        $resultado = array();
        $sql = 'SELECT p,g,pr
            FROM NomencladorBundle:NomPrecio p
            JOIN p.grupo g
            JOIN p.producto pr
            WHERE p.idpadre = :idpadre AND p.preciomn = :preciomn AND p.preciocuc = :preciocuc AND p.um = :um';
        $consulta = $this->em->createQuery($sql);
        $consulta->setParameter('idpadre', $params['producto']);
        $consulta->setParameter('preciomn', $params['preciomn']);
        $consulta->setParameter('preciocuc', $params['preciocuc']);
        $consulta->setParameter('um', $params['um']);
        $result = $consulta->getArrayResult();

        foreach ($result as $index => $v) {
            $resultado[$index]['producto'] = $v['producto']['idproducto'];
            $resultado[$index]['idprecio'] = $v['id'];
            $resultado[$index]['idpadre'] = $v['idpadre'];
        }

        $result = Util::array_unique_callback($resultado, function ($criterio) {
            return $criterio['producto'];
        });
        return $result;
    }

    public function getDescendientes($producto, $nivel = true, $array = false, $acopio = false, $obj = false)
    {
        $producto = is_object($producto) ? $producto : $this->em->getRepository('NomencladorBundle:NomProducto')->find($producto);
        $alias = $producto->getAlias();
        $sel = $array ? 'g,s,f' : 'g.idproducto';
        $where = ' g.alias like \'' . $alias . '%\' ';
        if ($producto->getNivel() == 0) {
            $where = ' g.idgenerico=' . $producto->getIdgenerico()->getIdgenerico();
        }
        if ($nivel) {
            $where .= ' and s.idsabor is not null and f.id is not null';
        }

        $join = "";
        if ($acopio) {
            $where .= !$acopio ? ' AND p.acopio = false' : ' AND p.acopio = true';
            $join .= !$acopio ? ' JOIN g.idgenerico p' : ' JOIN g.idgenerico p';
        }

        $dql = 'SELECT ' . $sel . ' FROM NomencladorBundle:NomProducto g 
        LEFT JOIN g.idsabor s
        LEFT JOIN g.idformato f ' . $join .
            ' where ' . $where;
        //dump($dql);die;
        $consulta_partes = $this->em->createQuery($dql);
        if ($array && !$obj) {
            return $consulta_partes->getArrayResult();
        } else {
            $partesAux = $consulta_partes->getResult();
        }
        $partes = array();
        foreach ($partesAux as $value) {
            $partes[] = $obj ? $value : $value['idproducto'];
        }
        return $partes;
    }

    public function productosAcopios($productos, $acopio = false)
    {
        $prods = implode(",", $productos);
        $where = $acopio == false ? ' and g.acopio = false' : ' and g.acopio = true';
        $dql = 'SELECT p,g,um
          FROM NomencladorBundle:NomProducto p
          JOIN p.idgenerico g 
          LEFT JOIN p.umOperativa um
          WHERE  p.idproducto in (' . $prods . ') ' . $where;
        $consulta_partes = $this->em->createQuery($dql);
        $partes = $consulta_partes->getResult();

        return $partes;
    }

    public function getProdHjosPlanParaFormType($padre, $acopio = false)
    {
        $where = "";
        if ($acopio) {
            $where .= " AND g.acopio = true";
        } else {
            $where .= " AND g.acopio = false";
        }
        if ($padre != null && $padre != "") {
            $where .= " AND cp.alias LIKE '" . $padre->getIdproducto()->getAlias() . "%'";
        }

        $consulta = $this->em->createQuery("select cp
           from NomencladorBundle:NomProducto  cp
           JOIN cp.idgenerico g
           where cp.activo = true " . $where);
        $lista = $consulta->getResult();
        return $lista;
    }

}