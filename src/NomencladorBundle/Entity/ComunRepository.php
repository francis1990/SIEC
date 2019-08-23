<?php
/**
 * Created by PhpStorm.
 * User: mary
 * Date: 17/11/2016
 * Time: 4:16
 */

namespace NomencladorBundle\Entity;

use Doctrine\ORM\EntityRepository;
use EnumsBundle\Entity\EnumTipoUnidadMedida;
use NomencladorBundle\Util\Util;


class ComunRepository extends EntityRepository
{
    public function startXLimit($entidad, $start = 0, $limite = 10)
    {
        $result = [];
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('SELECT s FROM ' . $entidad . ' s');
        $result['count'] = count($consulta->getResult());

        $consulta->setMaxResults(intval($limite));
        $consulta->setFirstResult(intval($start));
        if ($result['count'] < 25) {
            $result['cant'] = $result['count'];
        } else {
            $result['cant'] = 25;
        }

        $result['datos'] = $consulta->getResult();
        return $result;
    }

    public function all($entidad, $start = 0, $limite = 10)
    {
        $result = [];
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('SELECT s FROM ' . $entidad . ' s');
        $result['count'] = count($consulta->getResult());

        if ($result['count'] < 25) {
            $result['cant'] = $result['count'];
        } else {
            $result['cant'] = 25;
        }

        $result['datos'] = $consulta->getResult();
        return $result;
    }

    public function codigoExits($entidad, $codigo, $campo = 'codigo', $nombre = null)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('SELECT g.' . $campo . ' FROM ' . $entidad . ' g  WHERE ( g.' . $campo . ' = :codigo)');
        $consulta->setParameter('codigo', $codigo);
        $result = (count($consulta->getResult()) > 0) ? 1 : -1;
        return $result;
    }

    public function codigoExitsXPadre($entidad, $codigo, $campo = 'codigo', $idpadre)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('SELECT g.' . $campo . ' FROM ' . $entidad . ' g  WHERE ( g.' . $campo . ' = ' . $codigo . ') and
        g.idpadre  = ' . $idpadre);
        $result = (count($consulta->getResult()) > 0) ? 1 : -1;
        return $result;
    }

    public function codigoCant($entidad, $codigo, $campo = 'codigo')
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('SELECT g.' . $campo . ' FROM ' . $entidad . ' g  WHERE ( g.' . $campo . ' = :codigo)');
        $consulta->setParameter('codigo', $codigo);
        $result = count($consulta->getResult());
        return $result;
    }

    public function existencia($entidad, $codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('SELECT g FROM ' . $entidad . ' g  WHERE ( g.nombre = :nombre or g.codigo= :codigo)');
        $consulta->setParameter('codigo', $codigo);
        $consulta->setParameter('nombre', $nombre);
        $result = count($consulta->getResult());
        return $result;
    }

    public function existenciaDpa($parametros)
    {

        $w = '';
        if (!empty($parametros['id'])) {
            $w = ' and g.iddpa <> :id';
        }
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('SELECT count(g.codigo) FROM NomencladorBundle:NomDpa g
        WHERE (g.idpadre=:padre  and g.alias= :alias ) or g.codigo = :codigo ' . $w);
        $consulta->setParameters($parametros);
        $result = $consulta->getSingleScalarResult();
        return $result == 0 ? 0 : 1;
    }

    public function update($entidad, $campo, $valor)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('UPDATE NomencladorBundle:NomProducto SET ' . $campo . ' = ' . $valor);
        $r = $consulta->execute();
        return $r;
    }

    public function deleteAll($entidad, $campo_id, $array_ids)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('DELETE  FROM ' . $entidad . ' cp   WHERE cp.' . $campo_id . ' IN  ( ' . $array_ids . ')');
        $consulta->execute();
        return $consulta;
    }

    public function getPadre($entidad, $campo, $id)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp.idpadre
                                         from ' . $entidad . ' cp
                                          where cp.' . $campo . ' =' . $id);
        return $consulta->getResult();
    }

    public function listarEnt($start = 0, $limite = 10, $where)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp, tipo,dpa
                                         from NomencladorBundle:NomEntidad cp
                                         LEFT JOIN cp.iddpa  dpa
                                         LEFT JOIN cp.idtipoentidad  tipo
                                          where ' . $where . '  ORDER BY cp.codigo');
        $result['count'] = count($consulta->getResult());
        $consulta->setMaxResults($limite);
        $consulta->setFirstResult($start);
        $result['datos'] = $consulta->getResult();
        return $result;
    }

    public function listardatosEnt($start = 0, $limite = 10, $where)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp.identidad,cp.codigo,cp.nombre,cp.hoja
                                         from NomencladorBundle:NomEntidad cp
                                          where ' . $where . '  ORDER BY cp.codigo');
        $result['count'] = count($consulta->getResult());
        $consulta->setMaxResults($limite);
        $consulta->setFirstResult($start);
        $result['datos'] = $consulta->getResult();
        return $result;
    }

    public function listarEntiHojas()
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp.identidad,cp.codigo,cp.nombre
                                         from NomencladorBundle:NomEntidad cp
                                          where ( select COUNT(ent.identidad)
                                         from NomencladorBundle:NomEntidad ent where cp.identidad = ent.idpadre )=0 ORDER BY cp.codigo');
        return $consulta->getResult();
    }

    public function listarEntiHijas($identidad)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp
                                         from NomencladorBundle:NomEntidad cp
                                          where cp.idpadre =' . $identidad);
        return $consulta->getResult();
    }

    public function listarGrupoHijos($id)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp.idgrupointeres
                                         from NomencladorBundle:NomGrupointeres cp
                                          where cp.idpadre =' . $id);
        return $consulta->getResult();
    }

    public function listarAsegHijos($id)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp.idaseguramiento
                                         from NomencladorBundle:NomAseguramiento cp
                                          where cp.idpadre =' . $id);
        return $consulta->getResult();
    }

    public function listarHijos($entidad, $id)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('select cp
                                          from  ' . $entidad . '  cp
                                          where cp.idpadre =' . $id);
        return $consulta->getResult();
    }

    public function listarDpaHijos($id)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp
                                         from NomencladorBundle:NomDpa cp
                                          where cp.idpadre =' . $id);
        return $consulta->getResult();
    }

    public function listarGrupoHojas()
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select cp.idgrupointeres,cp.codigo,cp.nombre
                                         from NomencladorBundle:NomGrupointeres cp
                                          where ( select COUNT(gru.idgrupointeres)
                                         from NomencladorBundle:NomGrupointeres gru where cp.idgrupointeres = gru.idpadre )=0 ORDER BY cp.codigo');
        return $consulta->getResult();
    }

    public function listaGrupoNivel1()
    {
        $em = $this->getEntityManager();
        $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE g.idpadre = 0';
        $consulta = $em->createQuery($dqlhijos);

        $gruposinteres = $consulta->getResult();

        return $gruposinteres;
    }

    public function listaAsegNivel1()
    {
        $em = $this->getEntityManager();
        $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            WHERE g.idpadre = 0';
        $consulta = $em->createQuery($dqlhijos);
        $aseg = $consulta->getResult();
        return $aseg;
    }

    public function listaProductoNivel1()
    {
        $em = $this->getEntityManager();

        $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomProducto g
            WHERE g.idpadre = 0 AND g.activo = true';
        $consulta = $em->createQuery($dqlhijos);

        $p = $consulta->getResult();
        return $p;
    }

    public function findCoincidencia($entidad, $parametros, $campo)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT g
            FROM ' . $entidad . ' g
            WHERE (g.nombre = :nombre
            OR g.codigo = :codigo) AND g.' . $campo . ' <> :idg';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros[0]);
        $consulta->setParameter('codigo', $parametros[1]);
        $consulta->setParameter('idg', $parametros[2]);
        return $consulta->getResult();
    }

    public function findCoincidenciaNuevo($entidad, $parametros, $ruta = false)
    {
        if ($ruta) {
            $em = $this->getEntityManager();
            $dql = 'SELECT g
           FROM ' . $entidad . ' g
            WHERE g.nombre = :nombre';
            $consulta = $em->createQuery($dql);
            $consulta->setParameter('nombre', $parametros[0]);
            return $consulta->getResult();
        } else {
            $em = $this->getEntityManager();
            $dql = 'SELECT g
           FROM ' . $entidad . ' g
            WHERE g.nombre = :nombre
            OR g.codigo = :codigo';
            $consulta = $em->createQuery($dql);
            $consulta->setParameter('nombre', $parametros[0]);
            $consulta->setParameter('codigo', $parametros[1]);
            return $consulta->getResult();
        }

    }

    public function getHoja($entidad, $id_val)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT g  FROM ' . $entidad . '  g   WHERE g.idpadre =: ' . $id_val;
        $consulta = $em->createQuery($dql);
        return $consulta->getResult();
    }

    public function buscarConversion($id, $idfin)
    {
        $em = $this->getEntityManager();

        $consulta = $em->createQuery('select count(cp.idconversion)
                                         from NomencladorBundle:NomConversion cp
                                          where cp.iduminicio = ' . $id . ' and cp.idumfin = ' . $idfin);
        return $consulta->getResult();
    }

    public function findProducto($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT g
            FROM NomencladorBundle:NomProducto g
            WHERE g.activo = true AND g.codigo =' . $parametros;

        $consulta = $em->createQuery($dql);
        return $consulta->getArrayResult();
    }

    public function findProductoMod($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT g
            FROM NomencladorBundle:NomProducto g
            WHERE g.activo = true AND g.codigo =' . $parametros[0] . ' and g.idproducto <>' . $parametros[1];
        $consulta = $em->createQuery($dql);
        return $consulta->getArrayResult();
    }

    public function findSuminRuta($ruta)
    {
        $resp = [];
        $em = $this->getEntityManager();
        $dql = 'SELECT g, p,e
            FROM NomencladorBundle:NomRutaSuministrador g
            join g.producto p
            join g.entidad e
             WHERE g.ruta =:ruta';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ruta', $ruta);

        return $consulta->getResult();
//        $dat = $consulta->getResult();
//        if (count($dat) > 0)
//            foreach ($dat as $d) {
//                $temp = array(
//                    'idproducto' => $d->getProducto()->getIdproducto(),
//                    'nomproducto' => $d->getProducto()->getNombre(),
//                    'nomentidad' => $d->getEntidad()->getNombre(),
//                    'identidad' => $d->getEntidad()->getIdentidad());
//                $resp[] = $temp;
//            }
//        return $resp;
    }

    public function updatePrecio($grupo, $prod, $preciomn, $preciocuc)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('UPDATE NomencladorBundle:NomPrecio p SET p.preciomn = ' . $preciomn . ',p.preciocuc = ' . $preciocuc . ' 
                                    WHERE p.grupo = ' . $grupo . ' AND p.producto = ' . $prod . ' ');
        $r = $consulta->execute();
        return $r;
    }

    public function getEntidadesTiposAcopioyUEB()
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository('NomencladorBundle:NomEntidad');
        $query = $repository->createQueryBuilder('ent')
            ->where('ent.acopio = :acopio')
            ->setParameter('acopio', 1)
            ->getQuery();
        $entidades = $query->getResult();
        $cantEnt = count($entidades);
        $repository1 = $em->getRepository('NomencladorBundle:NomUeb');
        $query1 = $repository1->createQueryBuilder('ueb')->getQuery();
        $uebs = $query1->getResult();
        $entsUebs = array_merge($entidades, $uebs);
        $valoresEnt = array();
        $valoresUeb = array();
        $tipos = array();
        foreach ($entsUebs as $index => $value) {
            if ($index < $cantEnt) {
                $valoresEnt[$value->getIdentidad() . '-' . 0] = $value->getNombre();
            } else {
                $valoresUeb[$value->getIdueb() . '-' . 1] = $value->getNombre();
            }
        }
        $resultado['Entidades'] = $valoresEnt;
        $resultado['UEB'] = $valoresUeb;
        return $resultado;
    }

    public function findPreciosDadoIdPadre($idpadre, $pmn, $pcuc)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT p
            FROM NomencladorBundle:NomPrecio p
            WHERE p.idpadre = :idpadre AND p.preciomn=:preciomn AND p.preciocuc=:preciocuc';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('idpadre', $idpadre);
        $consulta->setParameter('preciomn', $pmn);
        $consulta->setParameter('preciocuc', $pcuc);
        $result = $consulta->getResult();
        return $result;
    }

    public function listarEntidadesPrimerNivel()
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT e
            FROM NomencladorBundle:NomEntidad e
            WHERE e.idpadre = 0';
        $consulta = $em->createQuery($dql);
        $result = $consulta->getResult();
        return $result;
    }

    public function listarDelExportarDPA($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomDpa', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data['data'][] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );

            if ($n->getIdpadre() == 0) {
                $data['dataPadres'][] = count($data['data']) - 1;
            }
        }
        return $data;
    }

    public function listarDelExportarUEB($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomUeb', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarTipoEntidad($codigo, $nombre, $abrev)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomTipoEntidad', $codigo, $nombre, $abrev);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'abreviatura' => $n->getAbreviatura(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarEntidad($codigo, $nombre, $siglas, $dpa, $tipoentidad)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrarEntidad($em, 'NomencladorBundle:NomEntidad', $codigo, $nombre, $siglas, $dpa,
            $tipoentidad);
        $data = [];
        foreach ($datas as $d) {
            $data['data'][] = array(
                'codigo' => $d['codigo'],
                'nombre' => $d['nombre'],
                'direccion' => $d['direccion'],
                'siglas' => $d['siglas'],
                'ndpa' => $d['ndpa'],
                'ntipoent' => $d['ntipoent'],
                'vinculo' => $d['vinculo'] == true ? 'Si' : 'No',
                'estatal' => $d['estatal'] == true ? 'Si' : 'No',
                'acopio' => $d['acopio'] == true ? 'Si' : 'No',
                'activo' => $d['activo'] == true ? 'Si' : 'No',
            );

            if ($d['idpadre'] == 0) {
                $data['dataPadres'][] = count($data['data']) - 1;
            }
        }
        return $data;
    }

    private function getHijosGrupos(NomGrupointeres $grupointeres, $hijos)
    {
        $em = $this->getEntityManager();
        if ($grupointeres->getHoja()) {
            $hijos[] = $grupointeres;
        } else {
            $arraR = array();
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE g.idpadre = ' . $grupointeres->getIdgrupointeres();
            $consulta = $em->createQuery($dqlhijos);
            if (count($consulta->getResult()) > 0) {
                $valor[0] = $grupointeres;
                $hijos = array_merge($hijos, $valor);
                $arraR = $consulta->getResult();
                foreach ($arraR as $index => $pr) {
                    $hijos = $this->getHijosGrupos($pr, $hijos);
                }
            } else {
                $valor[0] = $grupointeres;
                $hijos = array_merge($hijos, $valor);
            }
        }
        return $hijos;
    }

    public function listarDelExportarGruposIntereses($codigo, $nombre)
    {
        $gruposPadres = $this->listaGrupoNivel1();
        $valoresGrupos = [];
        foreach ($gruposPadres as $index => $value) {
            $res = array();
            $res = $this->getHijosGrupos($value, $datos = array());
            $valoresGrupos = array_merge($valoresGrupos, $res);
        }

        $data = [];
        foreach ($valoresGrupos as $index => $gru) {
            /*Para los filtros*/
            if (($nombre != null || $nombre != "") && ($codigo == null || $codigo == "")) {
                if (strpos(strtolower($gru->getNombre()), strtolower($nombre)) !== false) {
                    $data['data'][] = array(
                        'codigo' => $gru->getCodigo(),
                        'nombre' => $gru->getNombre(),
                        'activo' => $gru->getActivo() == true ? 'Si' : 'No'
                    );
                    if ($gru->getIdpadre() == 0) {
                        $data['dataPadres'][] = count($data['data']) - 1;
                    }
                }
            } else {
                if (($codigo != null || $codigo != "") && ($nombre == null || $nombre == "")) {
                    if (strpos(strtolower($gru->getCodigo()), strtolower($codigo)) !== false) {
                        $data['data'][] = array(
                            'codigo' => $gru->getCodigo(),
                            'nombre' => $gru->getNombre(),
                            'activo' => $gru->getActivo() == true ? 'Si' : 'No'
                        );
                        if ($gru->getIdpadre() == 0) {
                            $data['dataPadres'][] = count($data['data']) - 1;
                        }
                    }
                } else {
                    if (($codigo != null || $codigo != "") && ($nombre != null || $nombre != "")) {
                        if (strpos(strtolower($gru->getCodigo()),
                                strtolower($codigo)) !== false && strpos(strtolower($gru->getNombre()),
                                strtolower($nombre)) !== false) {
                            $data['data'][] = array(
                                'codigo' => $gru->getCodigo(),
                                'nombre' => $gru->getNombre(),
                                'activo' => $gru->getActivo() == true ? 'Si' : 'No'
                            );
                            if ($gru->getIdpadre() == 0) {
                                $data['dataPadres'][] = count($data['data']) - 1;
                            }
                        }
                    }
                }
            }

            /*Para sino ahi filtros*/
            if ($codigo == null && $nombre == null) {
                if ($gru->getIdpadre() == 0) {
                    $data['dataPadres'][] = $index;
                }
                $data['data'][] = array(
                    'codigo' => $gru->getCodigo(),
                    'nombre' => $gru->getNombre(),
                    'activo' => $gru->getActivo() == true ? 'Si' : 'No'
                );
            }
        }
        return $data;
    }

    public function listarDelExportarUnidadMedida($codigo, $nombre, $abrev, $tipo)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomUnidadmedida', $codigo, $nombre, $abrev, null, $tipo);
        $data = [];
        $objEnum = new EnumTipoUnidadMedida();
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'abreviatura' => $n->getAbreviatura(),
                'idtipoum' => $objEnum->tiposUmToString($n->getIdTipoUM()),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarGenerico($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomGenerico', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarSubGenerico($codigo, $nombre, $generico)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomSubgenerico', $codigo, $nombre, null, $generico);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'generico' => $n->getGenerico()->getNombre(),
                'empaque' => $n->getEmpaque() == true ? 'Si' : 'No',
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarEspecifico($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomEspecifico', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarTipoEspecifico($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomTipoespecifico', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarFormato($codigo, $nombre, $valor, $um)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomFormato', $codigo, $nombre, null, null, null, $valor,
            $um);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'valor' => $n->getPeso(),
                'um' => $n->getIdunidadmedida() != null && $n->getIdunidadmedida() != "" ? $n->getIdunidadmedida()->getNombre() : '',
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarSaboresCualidades($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomSabor', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarTiposaseguramientos($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomDetalle', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    private function getHijosAseguramientos(NomAseguramiento $aseg, $hijos, $um)
    {
        $em = $this->getEntityManager();
        if ($aseg->getHoja()) {
            $hijos[] = $aseg;
        } else {
            $arraR = array();
            $dqlhijos = "SELECT g
            FROM NomencladorBundle:NomAseguramiento g
            JOIN g.idunidadmedida  um
            WHERE g.idpadre = " . $aseg->getIdaseguramiento();

            $consulta = $em->createQuery($dqlhijos);
            if (count($consulta->getResult()) > 0) {
                $valor[0] = $aseg;
                $hijos = array_merge($hijos, $valor);
                $arraR = $consulta->getResult();
                foreach ($arraR as $index => $pr) {
                    $hijos = $this->getHijosAseguramientos($pr, $hijos, $um);
                }
            } else {
                $valor[0] = $aseg;
                $hijos = array_merge($hijos, $valor);
            }
        }
        return $hijos;
    }

    public function listarDelExportarAseguramientos($codigo, $nombre, $tipoase, $um)
    {
        $asegPadres = $this->listaAsegNivel1();
        $valoresAseg = [];
        foreach ($asegPadres as $index => $value) {
            $res = array();
            $res = $this->getHijosAseguramientos($value, $datos = array(), $um);
            $valoresAseg = array_merge($valoresAseg, $res);
        }

        $data = [];
        foreach ($valoresAseg as $index => $aseg) {
            /*Para los filtros*/
            if ($nombre != null && $codigo == null && $tipoase == null && $um == null) {
                if (strpos(strtolower($aseg->getNombre()), strtolower($nombre)) !== false) {
                    $data['data'][] = array(
                        'codigo' => $aseg->getCodigo(),
                        'nombre' => $aseg->getNombre(),
                        'um' => $aseg->getIdunidadmedida()->getAbreviatura(),
                        'activo' => $aseg->getActivo() == true ? 'Si' : 'No'
                    );
                    if ($aseg->getIdpadre() == 0) {
                        $data['dataPadres'][] = count($data['data']) - 1;
                    }
                }
            } else {
                if ($codigo != null && $nombre == null && $tipoase == null && $um == null) {
                    if (strpos(strtolower($aseg->getCodigo()), strtolower($codigo)) !== false) {
                        $data['data'][] = array(
                            'codigo' => $aseg->getCodigo(),
                            'nombre' => $aseg->getNombre(),
                            'um' => $aseg->getIdunidadmedida()->getAbreviatura(),
                            'activo' => $aseg->getActivo() == true ? 'Si' : 'No'
                        );
                        if ($aseg->getIdpadre() == 0) {
                            $data['dataPadres'][] = count($data['data']) - 1;
                        }
                    }
                } else {
                    if ($tipoase != null && $codigo == null && $nombre == null && $um == null) {
                        if (strpos(strtolower($aseg->getIddetalle()->getNombre()), strtolower($tipoase)) !== false) {
                            $data['data'][] = array(
                                'codigo' => $aseg->getCodigo(),
                                'nombre' => $aseg->getNombre(),
                                'um' => $aseg->getIdunidadmedida()->getAbreviatura(),
                                'activo' => $aseg->getActivo() == true ? 'Si' : 'No'
                            );
                            if ($aseg->getIdpadre() == 0) {
                                $data['dataPadres'][] = count($data['data']) - 1;
                            }
                        }
                    } else {
                        if ($um != null && $tipoase == null && $codigo == null && $nombre == null) {
                            if (strpos(strtolower($aseg->getIdunidadmedida()->getNombre()),
                                    strtolower($um)) !== false) {
                                $data['data'][] = array(
                                    'codigo' => $aseg->getCodigo(),
                                    'nombre' => $aseg->getNombre(),
                                    'um' => $aseg->getIdunidadmedida()->getAbreviatura(),
                                    'activo' => $aseg->getActivo() == true ? 'Si' : 'No'
                                );
                                if ($aseg->getIdpadre() == 0) {
                                    $data['dataPadres'][] = count($data['data']) - 1;
                                }
                            }
                        } else {
                            if ($um != null && $tipoase != null && $codigo != null && $nombre != null) {
                                if ((strpos(strtolower($aseg->getIdunidadmedida()->getNombre()),
                                            strtolower($um)) !== false)
                                    && (strpos(strtolower($aseg->getIddetalle()->getNombre()),
                                            strtolower($tipoase)) !== false)
                                    && (strpos(strtolower($aseg->getCodigo()), strtolower($codigo)) !== false)
                                    && (strpos(strtolower($aseg->getNombre()), strtolower($nombre)) !== false)
                                ) {
                                    $data['data'][] = array(
                                        'codigo' => $aseg->getCodigo(),
                                        'nombre' => $aseg->getNombre(),
                                        'um' => $aseg->getIdunidadmedida()->getAbreviatura(),
                                        'activo' => $aseg->getActivo() == true ? 'Si' : 'No'
                                    );
                                    if ($aseg->getIdpadre() == 0) {
                                        $data['dataPadres'][] = count($data['data']) - 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($um == null && $tipoase == null && $codigo == null && $nombre == null) {
                /*Para sino ahi filtros*/
                $data['data'][] = array(
                    'codigo' => $aseg->getCodigo(),
                    'nombre' => $aseg->getNombre(),
                    'um' => $aseg->getIdunidadmedida()->getAbreviatura(),
                    'activo' => $aseg->getActivo() == true ? 'Si' : 'No'
                );
                if ($aseg->getIdpadre() == 0) {
                    $data['dataPadres'][] = count($data['data']) - 1;
                }
            }
        }
        return $data;
    }

    public function listarDelExportarRutas($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrarRuta($em, 'NomencladorBundle:NomRutaSuministrador', $codigo, $nombre);
        $data = [];
        //var_dump($datas);die;
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getRuta()->getCodigo(),
                'nombre' => $n->getRuta()->getNombre(),
                'producto' => $n->getProducto()->getNombre(),
                'entidad' => $n->getEntidad()->getNombre(),
                'activo' => $n->getRuta()->getActivo() == true ? 'Si' : 'No',
            );
        }
        return $data;
    }

    public function listarDelExportarTipoTransporte($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomTipotransporte', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarTipoIncidencia($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomTipoIncidencia', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarClasifIncidencia($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomClasificacionIncidencia', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarPortadores($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomPortador', $codigo, $nombre);
        $data = [];
        foreach ($datas as $d) {
            $data[] = array(
                'codigo' => $d->getCodigo(),
                'nombre' => $d->getNombre(),
                'um' => $d->getIdunidadmedida()->getAbreviatura(),
                'alcance' => $d->getAlcance() == true ? 'Si' : 'No',
                'dia' => $d->getDia() == true ? 'Si' : 'No',
                'madrugada' => $d->getMadrugada() == true ? 'Si' : 'No',
                'pico' => $d->getPico() == true ? 'Si' : 'No',
                'inventario' => $d->getInventario() == true ? 'Si' : 'No',
                'entrada' => $d->getEntrada() == true ? 'Si' : 'No',
                'existencia' => $d->getExistencia() == true ? 'Si' : 'No',
                //'consumo' => $d->getConsumo() == true ? 'Si' : 'No',
                'activo' => $d->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarCuentasContables($numero, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomCuentacontable', null, $nombre, null, null, null, null,
            null, null, null, $numero);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'numero' => $n->getNumero(),
                'nombre' => $n->getNombre(),
                'porcobrar' => $n->getPorcobrar() ? 'Si' : 'No',
                'activo' => $n->getActivo() ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarEjercicios($nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomEjercicio', null, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarTipoPlan($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomTipoPlan', $codigo, $nombre);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }
        return $data;
    }

    public function listarDelExportarProducto($codigo, $nombre, $nivel)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomProducto', $codigo, $nombre, '', '', '', '', '', '', '',
            '', $nivel);
        $data = [];
        foreach ($datas as $n) {
            $data['data'][] = array(
                'codigo' => $n->getCodigo() . ' ',
                'nombre' => $n->getNombre(),
                'generico' => ($n->getIdgenerico() != null) ? $n->getIdgenerico()->getNombre() : "",
                'subgenerico' => ($n->getIdsubgenerico() != null) ? $n->getIdsubgenerico()->getNombre() : "",
                'especifico' => ($n->getIdespecifico() != null) ? $n->getIdespecifico()->getNombre() : "",
                'tipo' => ($n->getIdtipoespecifico() != null) ? $n->getIdtipoespecifico()->getNombre() : "",
                'sabor' => ($n->getIdsabor() != null) ? $n->getIdsabor()->getNombre() : "",
                'formato' => ($n->getIdformato() != null) ? $n->getIdformato()->getNombre() : "",
                'onei' => $n->getCodOnei(),
                'um' => ($n->getUmOperativa() != null) ? $n->getUmOperativa()->getAbreviatura() : "",
                'factor' => $n->getFactor() . ' '
            );

            if ($n->getNivel() == 0) {
                $data['dataPadres'][] = count($data['data']) - 1;
            }
        }
        return $data;
    }

    public function listarDelExportarPrecio($producto, $um, $preciomn, $preciocuc)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrarPrecio($em, 'NomencladorBundle:NomPrecio', $producto, $um, $preciomn, $preciocuc);
        $data = [];
        $gruposAux = array();
        foreach ($datas as $value) {
            $gruposAux = array_merge($gruposAux, $value->getGrupo());
        }
        $grupos = array_unique($gruposAux);
        foreach ($grupos as $valueGrup) {
            $grupoObj = $em->getRepository('NomencladorBundle:NomGrupointeres')->find($valueGrup);
            if (isset($grupoObj) && $grupoObj != null && count($grupoObj) > 0) {
                if (is_null($grupoObj->getIdentidad())) {
                    foreach ($datas as $valueDat) {
                        if (in_array($valueGrup, $valueDat->getGrupo())) {
                            $data[] = array(
                                'idproducto' => $valueDat->getProducto()->getIdproducto(),
                                'producto' => $valueDat->getProducto()->getNombre(),
                                'umProd' => $valueDat->getProducto()->getUmOperativa()->getNombre(),
                                'preciomn' => $valueDat->getPreciomn(),
                                'preciocuc' => $valueDat->getPreciocuc(),
                                'impuesto' => $valueDat->getImpuesto(),
                                'idgrupo' => $grupoObj->getIdgrupointeres(),
                                'codigoGru' => $grupoObj->getCodigo(),
                                'nombreGru' => $grupoObj->getNombre(),
                                'resolucion' => $valueDat->getResolucion(),
                                'fecha' => $valueDat->getFecha(),
                            );
                        }
                    }
                }
            }
        }
        return $data;
    }

    public function findAllOrderedByNombre($tabla)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT p 
            FROM NomencladorBundle:' . $tabla . ' p
          order by  p.nombre ASC';
        $consulta = $em->createQuery($dql);
        $result = $consulta->getResult();
        return $result;
    }

    public function listarDelExportarConversion($filtroOrigen, $filtroDestino, $filtroFactor)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrarConversion($em, 'NomencladorBundle:NomConversion', $filtroOrigen, $filtroDestino,
            $filtroFactor);
        $data = [];
        foreach ($datas as $n) {
            $data[] = array(
                'origen' => $n->getIduminicio()->getNombre(),
                'destino' => $n->getIdumfin()->getNombre(),
                'factor' => $n->getFactor(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No'
            );
        }

        return $data;

    }

    public function comprobarExisProdGrupoPrecios($prod, $preciomn, $preciocuc, $idprecio = null)
    {
        $where = "";
        if ($idprecio != null) {
            $where .= " AND p.id <> :idprecio";
        }
        $em = $this->getEntityManager();
        $dql = 'SELECT p
            FROM NomencladorBundle:NomPrecio p
            WHERE p.producto= :producto and (p.preciomn=:preciomn or p.preciocuc=:preciocuc)' . $where;
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('producto', $prod);
        $consulta->setParameter('preciomn', $preciomn);
        $consulta->setParameter('preciocuc', $preciocuc);
        if ($idprecio != null) {
            $consulta->setParameter('idprecio', $idprecio);
        }
        /*dump($consulta->getParameter('producto'));
        dump($consulta->getParameter('preciomn'));
        dump($consulta->getParameter('preciocuc'));
        dump($consulta->getParameter('idprecio'));
        dump($consulta->getSQL());die;*/
        $res = $consulta->getResult();
        return $res;
    }

    public function getProdPrecios($prod)
    {
        $em = $this->getEntityManager();
        $where = "";
        if ($prod != null) {
            $prods = implode(',', $prod);
            $where .= 'WHERE p.producto IN(' . $prods . ")";
        }
        $dql = 'SELECT p
            FROM NomencladorBundle:NomPrecio p ' . $where;
        $consulta = $em->createQuery($dql);
        $res = $consulta->getResult();
        return $res;
    }

    public function getGruposPadresNombre(NomGrupointeres $grupo, $padres = "")
    {
        $em = $this->getEntityManager();
        if ($grupo->getIdpadre() == 0) {
            return $padres;
        } else {
            $dqlhijos = 'SELECT g
            FROM NomencladorBundle:NomGrupointeres g
            WHERE g.idgrupointeres = ' . $grupo->getIdpadre();
            $consulta = $em->createQuery($dqlhijos);
            $padre = $consulta->getResult();
            $padres .= $padre[0]->getNombre() . " / ";
            return $padres = $this->getGruposPadresNombre($padre[0], $padres);
        }
    }
}
















