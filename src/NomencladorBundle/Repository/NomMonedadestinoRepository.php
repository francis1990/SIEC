<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 17/11/2016
 * Time: 4:16
 */

namespace NomencladorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use NomencladorBundle\Util\Util;


class NomMonedadestinoRepository extends EntityRepository {

    public function findCoincidencia($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT m
            FROM NomencladorBundle:NomMonedadestino m
            WHERE (m.nombre = :nombre
            OR m.codigo = :codigo) AND m.id <> :id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros['nombre']);
        $consulta->setParameter('codigo', $parametros['codigo']);
        $consulta->setParameter('id', $parametros['id']);
        return $consulta->getResult();
    }

    public function findCoincidenciaNuevo($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT m
            FROM NomencladorBundle:NomMonedadestino m
            WHERE m.nombre = :nombre
            OR m.codigo = :codigo';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros['nombre']);
        $consulta->setParameter('codigo', $parametros['codigo']);
        return $consulta->getResult();
    }
    public function findAllOrderedByCodigo()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM NomencladorBundle:NomMonedadestino p ORDER BY p.codigo ASC'
            )
            ->getResult();
    }

    public function listarDelExportarMonedaDestino($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomMonedadestino', $codigo, $nombre);
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
} 