<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 25/09/2018
 * Time: 11:38
 */

namespace ParteDiarioBundle\Services;


use Doctrine\ORM\EntityManager;
use NomencladorBundle\Entity\EntidadRepository;

class DatParteNivelActvService
{

    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getProdNivelActv($params)
    {
        $where = "";
        if ($params['idueb'] != 0 || $params['fecha'] != "") {
            if ($params['idueb'] != 0 && $params['fecha'] == "") {
                $where .= "WHERE par.ueb = " . $params['idueb'];
            } else if ($params['fecha'] != "" && $params['idueb'] == 0) {
                $fecha = is_string($params['fecha']) ? date_create_from_format('d/m/Y', $params['fecha']) : $params['fecha'];
                $where .= "WHERE par.fecha = " . "'" . $fecha->format('Y-m-d') . "'";
            } else {
                $fecha = is_string($params['fecha']) ? date_create_from_format('d/m/Y', $params['fecha']) : $params['fecha'];
                $where .= "WHERE par.fecha = " . "'" . $fecha->format('Y-m-d') . "'" . " AND par.ueb = " . $params['idueb'];
            }
        }
        $joinTNC = "";
        $whereTNC = "";
        if (!is_null($params['tiponorma']) && $params['tiponorma'] != "") {
            $joinTNC = " JOIN n.tiponorma tnc";
            $whereTNC = " WHERE tnc.id = " . $params['tiponorma'];
        }

        $dql = "SELECT pro
                FROM NomencladorBundle:NomProducto pro
                WHERE pro.idproducto IN(
                SELECT p.idproducto
                FROM NomencladorBundle:NomNorma n " . $joinTNC . "
                JOIN n.producto p " . $whereTNC . "
                ) AND pro.idproducto IN(
                SELECT p1.idproducto
                FROM ParteDiarioBundle:DatPartediarioProduccion par
                JOIN par.producto p1 " . $where . ")";
        $consulta = $this->em->createQuery($dql);
        if (count($consulta->getResult()) == 0)
            return array();
        else
            return $consulta->getResult();

    }

}