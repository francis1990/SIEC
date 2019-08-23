<?php
/**
 * Created by PhpStorm.
 * User: mary
 * Date: 17/11/2016
 * Time: 4:16
 */

namespace ParteDiarioBundle\Repository;

use Doctrine\ORM\EntityRepository;
use EnumsBundle\Entity\EnumMeses;
use ParteDiarioBundle\Entity\DatConsumoProduccion;


class DatIncidenciaRepository extends EntityRepository
{

    public function findByProductos($prod,$mon,$fecha)
    {
        if (is_array($prod)) {
            $prod = implode(",",$prod);
        }
        $em = $this->getEntityManager();
        $dql = "SELECT i,p,cl,t
            FROM ParteDiarioBundle:DatIncidencia i
            JOIN i.parte p
           JOIN i.idcasificacion cl
           JOIN i.idtipo t
             where i.idparte in
             (SELECT pd.idparte
             FROM ParteDiarioBundle:DatPartediarioProduccion pd
              JOIN pd.producto pro
              join pd.moneda mon
              where pro in (".$prod.") and mon.id=:moneda and pd.fecha =:fecha )";
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('moneda', $mon);
        $consulta->setParameter('fecha', $fecha);
        $lista = $consulta->getResult();
        if (count($lista) == 0)
            return [];
        else {
            return $lista;
        }
    }
}
