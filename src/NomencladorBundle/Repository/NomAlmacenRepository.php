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

class NomAlmacenRepository extends EntityRepository
{

    public function findCoincidencia($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT m
            FROM NomencladorBundle:NomAlmacen m
            JOIN m.ueb u
            WHERE (m.nombre = :nombre or m.codigo = :codigo)
            AND u.idueb = :ueb AND m.idalmacen <> :id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros['nombre']);
        $consulta->setParameter('ueb', $parametros['ueb']);
        $consulta->setParameter('codigo', $parametros['codigo']);
        $consulta->setParameter('id', $parametros['id']);
        return $consulta->getResult();
    }

    public function findCoincidenciaNuevo($parametros)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT m
            FROM NomencladorBundle:NomAlmacen m
            WHERE (m.nombre = :nombre or m.codigo = :codigo)
             AND m.ueb = :ueb';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('nombre', $parametros['nombre']);
        $consulta->setParameter('codigo', $parametros['codigo']);
        $consulta->setParameter('ueb', $parametros['ueb']);
        $result = count($consulta->getResult());
        return $result;
    }

    public function listarDelExportarAlmacen($codigo, $nombre, $ueb)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em,'NomencladorBundle:NomAlmacen', $codigo, $nombre,null,null,null,null,null,$ueb);
        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'ueb' => $n->getUeb()->getNombre(),
                'nevera' => $n->getNevera() == true ? 'Si' : 'No',
                'activo' => $n->getActivo() == true ? 'Si' : 'No');
        }

        return $data;
    }
} 