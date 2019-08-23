<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 25/12/2016
 * Time: 23:49
 */

namespace NomencladorBundle\Util;

use EnumsBundle\Entity\EnumTipoUnidadMedida;

class Util
{
    static public function getSlug($cadena, $separador = '')
    {
        $cadena_formateada = trim($cadena);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena_formateada);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, $separador));
        $slug = preg_replace("/[\/_|+ -]+/", $separador, $slug);
        return $slug;
    }

    static public function getSlugUpper($cadena, $separador = '')
    {
        $cadena_formateada = trim($cadena);
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $cadena_formateada);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtoupper(trim($slug, $separador));
        $slug = preg_replace("/[\/_|+ -]+/", $separador, $slug);
        return $slug;
    }

    /*Solo es para generar los alias en todos los nomencladores, luego borrar*/
    public static function generarAlias($em, $entidad, $nombre)
    {
        $alias = self::getSlug($nombre);
        $consulta = $em->createQuery('UPDATE NomencladorBundle:' . $entidad . ' g SET g.alias =' . "'" . $alias . "'");
        $result = $consulta->getResult();
    }

    static public function comprobarAliasExiste($em, $entidad, $nombre)
    {
        $alias = self::getSlug($nombre);
        $consulta = $em->createQuery('SELECT g FROM ' . $entidad . ' g  WHERE g.alias = :alias');
        $consulta->setParameter('alias', $alias);
        $result = (count($consulta->getResult()) > 0) ? 1 : -1;
        return $result;
    }

    static public function whereFiltrar($em, $tabla, $codigo, $nombre, $abrev = "", $generico = "", $tipoum = "", $valor = "", $um = "", $ueb = "", $producto = "", $nro = "", $nivel = "", $area = "", $tienecodigo = true)
    {
        $where = "";
        $ordenar = "";
        if ($tienecodigo) {
            $ordenar .= ' ORDER BY gen.codigo';
        } else {
            $ordenar .= ' ORDER BY gen.nombre';
        }

        if ($codigo != "") {
            $where .= "WHERE gen.codigo LIKE  '%$codigo%' ";
        }

        if ($nombre != "" && $codigo == "") {
            $where .= " WHERE gen.nombre LIKE '%$nombre%'";
        } else if ($nombre != "") {
            $where .= " AND gen.nombre LIKE '%$nombre%'";
        }

        if ($abrev != "" && $codigo == "" && $nombre == "") {
            $where .= " WHERE gen.abreviatura LIKE '%$abrev%'";
        } else if ($abrev != "") {
            $where .= " AND gen.abreviatura LIKE '%$abrev%'";
        }

        if ($tipoum != "" && $abrev == "" && $codigo == "" && $nombre == "") {
            $objEnum = new EnumTipoUnidadMedida();
            $resultIds = $objEnum->obtenerIdUMDadoName($tipoum);
            $where .= " WHERE gen.idtipoum in($resultIds)";
        } else if ($tipoum != "") {
            $objEnum = new EnumTipoUnidadMedida();
            $resultIds = $objEnum->obtenerIdUMDadoName($tipoum);
            $where .= " AND gen.idtipoum in($resultIds)";
        }

        if ($valor != "" && $tipoum == "" && $abrev == "" && $codigo == "" && $nombre == "") {
            $where .= " WHERE gen.peso = $valor";
        } else if ($valor != "") {
            $where .= " AND gen.peso = '%$valor%'";
        }

        $join = "";
        if ($um != "" && $valor == "" && $tipoum == "" && $abrev == "" && $codigo == "" && $nombre == "") {
            $join = "JOIN gen.idunidadmedida um";
            $where .= " WHERE um.nombre LIKE '%$um%'";
        } else if ($um != "") {
            $join = "JOIN gen.idunidadmedida um";
            $where .= " AND um.nombre LIKE '%$um%'";
        }

        if ($generico != "" && $abrev == "" && $codigo == "" && $nombre == "" && $tipoum == "" && $valor == "" && $um == "") {
            $join = "JOIN gen.generico g";
            $where .= "WHERE g.nombre LIKE '%$generico%'";
        } else if ($generico != "") {
            $join = "JOIN gen.generico g";
            $where .= " AND g.nombre LIKE '%$generico%'";
        }

        if ($ueb != "" && $generico == "" && $abrev == "" && $codigo == "" && $nombre == "" && $tipoum == "" && $valor == "" && $um == "") {
            $join = "JOIN gen.ueb ub";
            $where .= "WHERE ub.nombre LIKE '%$ueb%'";
        } else if ($ueb != "") {
            $join = "JOIN gen.ueb ub";
            $where .= " AND ub.nombre LIKE '%$ueb%'";
        }

        if ($nro != "" && $ueb == "" && $generico == "" && $abrev == "" && $codigo == "" && $nombre == "" && $tipoum == "" && $valor == "" && $um == "") {
            $where .= "WHERE gen.numero LIKE '%$nro%'";
        } else if ($nro != "") {
            $where .= " AND gen.numero LIKE '%$nro%'";
        }


        if ($tabla == "NomencladorBundle:NomCuentacontable") {
            $ordenar = " ORDER BY gen.numero";
        }

        if ($tabla == "NomencladorBundle:NomEjercicio") {
            $ordenar = " ORDER BY gen.nombre";
        }

        if ($where == '' && $nivel != '' && $nivel != 4)
            $where .= " WHERE gen.nivel =" . $nivel;
        elseif ($nivel != '' && $nivel != 4)
            $where .= " AND gen.nivel =" . $nivel;


        $dql = 'SELECT gen
            FROM ' . $tabla . ' gen 
            ' . $join . '
            ' . $where . '
            ' . $ordenar;

        $consulta = $em->createQuery($dql);
        $datas = $consulta->getResult();
        return $datas;
    }

    static public function whereFiltrarNorma($em, $tabla, $producto)
    {
        $ordenar = "producto";
        $where = "";
        $join = "";
        $join .= "JOIN gen.producto pro ";
        if ($producto != "") {
            $where .= "WHERE pro.nombre LIKE '%$producto%'";
        }

        $dql = 'SELECT gen
            FROM ' . $tabla . ' gen 
            ' . $join . '
            ' . $where . '
            order by gen.' . $ordenar;
        $consulta = $em->createQuery($dql);
        $datas = $consulta->getResult();
        return $datas;
    }

    static public function whereFiltrarRuta($em, $tabla, $codigo, $nombre)
    {
        $ordenar = "codigo";
        $where = "";
        $join = "";
        $join .= "JOIN gen.ruta r ";
        $join .= "JOIN gen.entidad e ";
        $join .= "JOIN gen.producto p ";
        if ($codigo != "") {
            $where .= "WHERE r.codigo LIKE  '%$codigo%' ";
        }

        if ($nombre != "" && $codigo == "") {
            $where .= " WHERE r.nombre LIKE '%$nombre%'";
        } else if ($nombre != "") {
            $where .= " AND r.nombre LIKE '%$nombre%'";
        }

        $dql = 'SELECT gen, r
            FROM ' . $tabla . ' gen 
            ' . $join . '
            ' . $where . '
            order by r.' . $ordenar;
        $consulta = $em->createQuery($dql);
        $datas = $consulta->getResult();
        return $datas;
    }

    static public function whereFiltrarEntidad($em, $tabla, $codigo, $nombre, $siglas, $dpa, $tipoentidad)
    {
        $ordenar = "codigo";
        $where = "";
        $join = "";
        $join .= "LEFT JOIN gen.iddpa dpa ";
        $join .= "LEFT JOIN gen.idtipoentidad tp";
        if ($codigo != "") {
            $where .= " WHERE gen.codigo LIKE  '%$codigo%' ";
        }

        if ($nombre != "" && $codigo == "") {
            $where .= " WHERE gen.nombre LIKE '%$nombre%'";
        } else if ($nombre != "") {
            $where .= " AND gen.nombre LIKE '%$nombre%'";
        }

        if ($siglas != "" && $codigo == "" && $nombre == "") {
            $where .= " WHERE gen.siglas LIKE '%$siglas%'";
        } else if ($siglas != "") {
            $where .= " AND gen.siglas LIKE '%$siglas%'";
        }

        if ($dpa != "" && $siglas == "" && $codigo == "" && $nombre == "") {
            $where .= " WHERE dpa.nombre LIKE '%$dpa%'";
        } else if ($dpa != "") {
            $where .= " AND dpa.nombre LIKE '%$dpa%'";
        }

        if ($tipoentidad != "" && $dpa == "" && $siglas == "" && $codigo == "" && $nombre == "") {
            $where .= " WHERE tp.nombre LIKE '%$tipoentidad%'";
        } else if ($tipoentidad != "") {
            $where .= " AND tp.nombre LIKE '%$tipoentidad%'";
        }

        $dql = 'SELECT gen.idpadre,gen.codigo,gen.nombre,gen.direccion,gen.siglas,dpa.nombre as ndpa,gen.vinculo,gen.estatal,gen.acopio,gen.activo,tp.nombre as ntipoent
            FROM ' . $tabla . ' gen
            ' . $join . '
            ' . $where . ' order by gen.' . $ordenar;

        $consulta = $em->createQuery($dql);
        $datas = $consulta->getResult();

        return $datas;
    }

    static public function whereFiltrarPrecio($em, $tabla, $producto, $um, $preciomn, $preciocuc)
    {
        $where = "";
        $join = "";
        $join .= "JOIN gen.producto pro ";
        if ($producto != "") {
            $where .= "WHERE pro.nombre LIKE '%$producto%'";
        }

        if ($preciomn != "" && $producto == "") {
            $where .= " WHERE gen.preciomn = $preciomn";
        } else if ($preciomn != "") {
            $where .= " AND gen.preciomn = $preciomn";
        }

        if ($preciocuc != "" && $preciomn == "" && $producto == "") {
            $where .= " WHERE gen.preciomn = $preciocuc";
        } else if ($preciocuc != "") {
            $where .= " WHERE gen.preciocuc = $preciocuc";
        }

        if ($um != "" && $preciocuc == "" && $preciomn == "" && $producto == "") {
            $join = "JOIN gen.um ume";
            $where .= " WHERE ume.nombre LIKE '%$um%'";
        } else if ($um != "") {
            $join = "JOIN gen.um ume";
            $where .= " AND ume.nombre LIKE '%$um%'";
        }

        $dql = 'SELECT gen
            FROM ' . $tabla . ' gen 
            ' . $join . '
            ' . $where;
        $consulta = $em->createQuery($dql);
        $datas = $consulta->getResult();
        return $datas;
    }

    static public function whereFiltrarConversion($em, $tabla, $filtroOrigen, $filtroDestino, $filtroFactor)
    {
        $ordenar = "idconversion";
        $where = "";
        $join = "";
        if ($filtroOrigen != "") {
            $join .= "JOIN gen.iduminicio um ";
            $where .= "WHERE um.nombre LIKE '%$filtroOrigen%'";
        }

        if ($filtroDestino != "" && $filtroOrigen == "" && $filtroFactor == "") {
            $join .= "JOIN gen.idumfin umf ";
            $where .= "WHERE umf.nombre LIKE '%$filtroDestino%'";
        } elseif ($filtroDestino != "") {
            $join .= "JOIN gen.idumfin umf ";
            $where .= " AND umf.nombre LIKE '%$filtroDestino%'";
        }

        if ($filtroFactor != "" && $filtroDestino == "" && $filtroOrigen == "") {
            $where .= "WHERE gen.factor = " . $filtroFactor;
        } elseif ($filtroDestino != "") {
            $where .= " AND gen.factor = " . $filtroFactor;
        }


        $dql = 'SELECT gen
            FROM ' . $tabla . ' gen 
            ' . $join . '
            ' . $where . '
            order by gen.' . $ordenar;

        $consulta = $em->createQuery($dql);
        $datas = $consulta->getResult();
        return $datas;
    }

    static public function array_unique_callback(array $arr, callable $callback, $strict = false)
    {
        return array_filter(
            $arr,
            function ($item) use ($strict, $callback) {
                static $haystack = array();
                $needle = $callback($item);
                if (in_array($needle, $haystack, $strict)) {
                    return false;
                } else {
                    $haystack[] = $needle;
                    return true;
                }
            }
        );
    }

    static public function get_value_from_array($needle, $arr, $key_of_search, $name_of_key_to_return_value = false)
    {
        $res = array();
        if (is_array($arr)) {
            for ($i = 0; $i < sizeof($arr); $i++) {
                $row = $arr[$i];
                if ($row[$key_of_search] === $needle) {
                    if ($name_of_key_to_return_value) {
                        $res[] = $i;
                    } else {
                        $res[] = $arr[$i];
                    }

                }
            }
            return $res;
        }
        return false;
    }

} 