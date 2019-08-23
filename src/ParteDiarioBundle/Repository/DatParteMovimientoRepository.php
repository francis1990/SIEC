<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 29/11/2016
 * Time: 22:08
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;


class DatParteMovimientoRepository extends EntityRepository
{

    public function buscarExistencia($params)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m.existencia
            FROM ParteDiarioBundle:DatParteMovimiento m
            JOIN m.producto pro
            JOIN m.ueb u
            join m.almacen alm
            join m.um um
             where m.fecha <= :fecha and pro = :producto and alm=:almacen and u=:ueb ORDER BY m.fecha asc";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fecha', $params['fecha']);
        $consulta->setParameter('producto', $params['producto']);
        $consulta->setParameter('almacen', $params['almacen']);
        $consulta->setParameter('ueb', $params['ueb']);
        $lista = $consulta->getResult();
        if (count($lista) == 0)
            return 0;
        else {
            return $lista[count($lista) - 1]['existencia'];
        }
    }

    public function actualizarExistencia($params)
    {
        $em = $this->getEntityManager();
        $dql = "UPDATE ParteDiarioBundle:DatParteMovimiento m SET m.existencia = m.existencia +" . $params['cantidad'] . "
             where  m.producto = :producto and m.almacen=:almacen and m.ueb=:ueb and m.fecha >:fecha  ";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fecha', $params['fecha']);
        $consulta->setParameter('producto', $params['producto']);
        $consulta->setParameter('almacen', $params['almacen']);
        $consulta->setParameter('ueb', $params['ueb']);
        $consulta->getResult();
    }

    public function calcularExistencia($params)
    {

        $em = $this->getEntityManager();
        $dql = "SELECT m.cantidad
            FROM ParteDiarioBundle:DatParteMovimiento m
             where m.idparte=:idparte";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('idparte', $params['id']);
        $lista = $consulta->getResult();

        if (count($lista) > 0) {
            $params['cantidad'] = abs($lista[count($lista) - 1]['cantidad'] - $params['newcant']) * $params['factor'];
        }

    }

    public function buscar($id)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT alm.idalmacen as almacen,um.idunidadmedida as umedida,m.cantidad as cantidad,
            m.concepto as concepto, m.idparte
,pro.idproducto as producto,u.idueb as ueb
            FROM ParteDiarioBundle:DatParteMovimiento m
            JOIN m.producto pro
            JOIN m.ueb u
            join m.almacen alm
            join m.um um
             where m.idparte = :id";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('id', $id);
        $lista = $consulta->getArrayResult();
        if (count($lista) == 0)
            return [];
        else {
            return $lista;
        }


    }

    public function buscarMovInsetado($param)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT m,pro,u,alm,um
            FROM ParteDiarioBundle:DatParteMovimiento m
            JOIN m.producto pro
            JOIN m.ueb u
            join m.almacen alm
            join m.um um
            WHERE m.concepto=:concepto and  m.cantidad =:cantidad and alm =:almacen
            and u=:ueb and pro =:producto
           ";
        //
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('concepto',$param['concepto']);
        $consulta->setParameter('producto', $param['producto']);
        $consulta->setParameter('ueb', $param['ueb']);
        $consulta->setParameter('almacen', $param['almacen']);
        $consulta->setParameter('cantidad', $param['cantidad']);
        $lista = $consulta->getResult();
        if (count($lista) == 0)
            return [];
        else {
            return $lista;
        }


    }


} 