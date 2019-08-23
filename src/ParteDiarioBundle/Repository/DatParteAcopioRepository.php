<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 29/11/2016
 * Time: 22:08
 */

namespace ParteDiarioBundle\Repository;

use EnumsBundle\Entity\EnumMeses;
use Doctrine\ORM\EntityRepository;


class DatParteAcopioRepository extends EntityRepository {

    public  function cantidadParte($parametro)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT sum(ea.cantidad)
            FROM ParteDiarioBundle:DatEntidadAcopio ea
            JOIN ea.parte p
             where p.idparte= :id  and p.ruta=:ruta ";
        $consulta = $em->createQuery($dql);
        $consulta->setParameters(
            array(
                'id'=>$parametro['id'],
                'ruta'=>$parametro['ruta']
            ));
       // $lista = $consulta->getSingleScalarResult();
        $cant = $consulta->getSingleScalarResult();
        if(is_null($cant))
            return 0;
        else
            return $cant;
    }
    public  function cantidadRutas($parametro)
    {
        $em = $this->getEntityManager();
        $dql = "SELECT sum(ea.cantidad)
            FROM ParteDiarioBundle:DatEntidadAcopio ea
            JOIN ea.parte p
             where p.ruta=:ruta and ea.destino=:destino and p.fecha like :fecha ";

        $consulta = $em->createQuery($dql);
        $consulta->setParameters(
            array(
                'fecha'=>'%'.$parametro['fecha'],
                'destino'=>$parametro['destino'],
                'ruta'=>$parametro['ruta']
            ));
        // $lista = $consulta->getSingleScalarResult();
        $cant = $consulta->getSingleScalarResult();
        if(is_null($cant))
            return 0;
        else
            return $cant;
    }
    public function calcularNivelProd($params, $dia = false)
    {
        $em = $this->getEntityManager();
        $select = $dia == true ? ', part.fecha ' : '';
        $group = $dia == true ? ' GROUP BY part.fecha ORDER BY part.fecha ' : '';
        if (is_array($params['idproducto'])) {
            $idproducto = implode(",", $params['idproducto']);
        } else {
            $idproducto = $params['idproducto'];
        }

        $where = '1=1 ';
        $where .= (isset($params['idueb']) && $params['idueb'] != null) ? (' and part.ueb =' . $params['idueb']) : '';
        $where .= (isset($params['um']) && $params['um'] != null) ? (' and g.um =' . $params['um']) : '';
        $where .= (isset($params['ent']) &&$params['ent'] != '' && $params['ent']  != null) ? (' and e.identidad in(' . $params['ent']  . ')') : '';
        $where .= (isset($idproducto) && $idproducto != '' && $idproducto != null && $idproducto != ' ') ? (' and p.idproducto in(' . $idproducto . ')') : '';
        $explodeFecha = EnumMeses::convertfecha($params['fecha']);
        $where .= ($params['fecha'] != '' && $params['fecha'] != null)?' and part.fecha <=' . "'" . $params['fecha']. "'" . '':'';

        if ($params['acumulado'] == 1) {//acumulado en el mes
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ?
                (' and part.fecha >=' . "'" . $explodeFecha['a'] . '-' . $explodeFecha['m'] . '-01' . "'" ) : '';
        }
        $primerDiaAno = EnumMeses::primerDiaAno($params['fecha']);
        if ($params['acumulado']== 2) {//acumulado en el año
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ?
                (' and part.fecha >=' . "'" . $primerDiaAno . "'")  : '';
        }
        if ($params['acumulado'] == 3) {// mes completo
            $enum = new EnumMeses();
            $range = $enum->intervaloMes($params['fecha']);
            $where .= ' and part.fecha <=' . "'" . $range['fin'] . "'" . ' and part.fecha >=' . "'" . $range['inicio'] . "'" . '';
        }
        if ($params['acumulado'] == 4) {//dia en especifico
            $where .= ' and part.fecha =' . "'" .$params['fecha'] . "'" . '';
        }

        $dql = "SELECT sum(g.cantidad)  as cantidad ". $select . "
            FROM   ParteDiarioBundle:DatEntidadAcopio g
            JOIN g.entidad e
            join g.producto p
          join g.parte part
            JOIN g.um u
             WHERE " . $where. $group ;

        $consulta_partes = $em->createQuery($dql);
        $partes = $consulta_partes->getResult();
        if ($dia) {
            $result = $consulta_partes->getArrayResult();
        } else {
            $result = (count($partes) > 0 && $partes[0]['cantidad'] != '') ? $partes[0]['cantidad'] : 0;
        }
        return $result;
    }




} 