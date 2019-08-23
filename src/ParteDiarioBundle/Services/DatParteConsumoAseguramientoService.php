<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 3/13/2018
 * Time: 10:55 PM
 */

namespace ParteDiarioBundle\Services;


use Doctrine\ORM\EntityManager;
use EnumsBundle\Entity\EnumMeses;
use NomencladorBundle\Entity\NomProducto;
use Doctrine\Common\Util\Debug;

class DatParteConsumoAseguramientoService
{
    private $em;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function obtenerDatosConsumoAseg($params)
    {
        if(!is_array($params['idproducto']))
            $params['idproducto'] = [$params['idproducto']];
        $params['idproducto'] = implode(",", $params['idproducto']);

        $params['idaseguramiento'] = implode(",", $params['idaseguramiento']);
        $where = "";
        $where .= ($params['fecha'] != '' && $params['fecha'] != null)?' and part.fecha <=' . "'" . $params['fecha']. "'" . '':'';
        $where .= ($params['ueb'] != '') ? (' and part.ueb = ' . $params['ueb']) : '';
        $where .= ($params['idaseguramiento'] != '') ? (' and nom_aseg.idaseguramiento IN (' . $params['idaseguramiento'].')') : '';
        $dql = "SELECT sum(g.realbruto) as cantidad
                FROM ParteDiarioBundle:DatConsumoAseguramiento g
                JOIN g.aseguramiento aseg
                JOIN aseg.aseguramiento nom_aseg
                JOIN g.parte part
                WHERE part.producto IN (" . $params['idproducto'] . ")".$where;
        $consulta = $this->em->createQuery($dql);

        $result = $consulta->getSingleResult();
       return $result;
    }

    public function updateDatosConsumoAseg($params)
    {
        $calculo = "";
        $fecha = EnumMeses::convertirFechaParaBD($params['fecha']);
        if ($params['diferenciaNivActv'] < 0) {
            $calculo = "SET g.nivelact = g.nivelact + " . ($params['diferenciaNivActv']) * -1;
        } else {
            $calculo = "SET g.nivelact = g.nivelact - " . ($params['diferenciaNivActv']);
        }
        $dql = "UPDATE ParteDiarioBundle:DatParteDiarioConsAseg g " . $calculo . " WHERE g.producto IN (" . $params['productos'] . ")
        AND g.ueb = " . $params['ueb'] . " AND g.fecha = " . "'" . $fecha . "'";

        $consulta = $this->em->createQuery($dql);
        $result = $consulta->getResult();
        return $result;
    }
}