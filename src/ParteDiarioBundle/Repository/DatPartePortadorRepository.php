<?php
/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 1/25/2018
 * Time: 4:55 PM
 */

namespace ParteDiarioBundle\Repository;


use Doctrine\ORM\EntityRepository;
use EnumsBundle\Entity\EnumMeses;
use ParteDiarioBundle\Entity\DatPartePortador;

class DatPartePortadorRepository extends EntityRepository
{

    public function calcularNivelProd($params)
    {
        $em = $this->getEntityManager();
        /*$params['acumulado'] si es 0 no tiene, si es 1 del mes y si es 2 del año*/
        $where = '1=1 ';
        $where .= ($params['idportador'] != '' && $params['idportador'] != null) ? (' and g.portador =' . $params['idportador'] . '') : '';
        $where .= ($params['idueb'] != '' && $params['idueb'] != null) ? (' and g.ueb =' . $params['idueb'] . '') : '';
        $where .= ($params['fechaCierre'] != '' && $params['fechaCierre'] != null) ? ' and g.fecha <=' . "'" . $params['fechaCierre'] . "'" . '' : '';
        $explodeFecha = EnumMeses::convertfecha($params['fecha']);
        if ($params['acumulado'] == 1) {//acumulado en el mes
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ?
                (' and g.fecha >=' . "'" . $explodeFecha['a'] . '-' . $explodeFecha['m'] . '-01' . "'") : '';
        }
        $primerDiaAno = EnumMeses::primerDiaAno($params['fecha']);
        if ($params['acumulado'] == 2) {//acumulado en el año
            $where .= ($params['fecha'] != '' && $params['fecha'] != null) ?
                (' and g.fecha >=' . "'" . $primerDiaAno . "'") : '';
        }
        if ($params['acumulado'] == 3) {// mes completo
            $enum = new EnumMeses();
            $range = $enum->intervaloMes($params['fecha']);
            $where .= ' and g.fecha <=' . "'" . $range['fin'] . "'" . ' and g.fecha >=' . "'" . $range['inicio'] . "'" . '';
        }
        if ($params['acumulado'] == 4) {//dia en especifico
            $where .= ' and g.fecha =' . "'" . $params['fecha'] . "'" . '';
        }

        $dql = "SELECT sum(g.consumo)  as cantidad
            FROM ParteDiarioBundle:DatPartePortador g
            JOIN g.ueb ue
            JOIN g.portador por
            JOIN g.um ume
            WHERE " . $where;
        $consulta_partes = $em->createQuery($dql);
        $partes = $consulta_partes->getResult();
        $result = (count($partes) > 0 && $partes[0]['cantidad'] != '') ? $partes[0]['cantidad'] : 0;
        return $result;
    }

    public function findLastMedidorByLastParte($idUeb, $idPortador, $idMedidor, $fecha){
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder()
            ->select('lm')
            ->from('ParteDiarioBundle:DatPartePortadorMedidor', 'lm')
            ->join('lm.parte', 'pp')
            ->join('pp.portador', 'p')
            ->join('pp.ueb', 'u')
            ->join('lm.medidor', 'm')
            ->where('p.idportador = :portador')
            ->andWhere('u.idueb = :ueb')
            ->andWhere('m.id = :medidor')
            ->andWhere('pp.fecha < :date')
            ->orderBy('pp.fecha', 'desc')
            ->setMaxResults(1)
            ->setParameters(array(
                'portador' => $idPortador,
                'ueb' => $idUeb,
                'medidor' => $idMedidor,
                'date' => $fecha,
                ));

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function ultimo_parte_portador()
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT max(a.fecha) as fecha
            FROM ParteDiarioBundle:DatPartePortador AS a ';
        $consulta = $em->createQuery($dql);
        $partes = $consulta->getResult();
        $cont = count($partes);
        return $cont > 0 ? new \DateTime($partes[0]['fecha']): null;
    }

    public function partes_portador_mes($mes, $ano, $toYear,$portador,$ueb,$campo)
    {
        $em = $this->getEntityManager();
        $select = $campo == 'inventario' ? 'sum(a.inventario) as suma' : 'sum(a.consumo) as suma' ;
        $wr = !$toYear ? ' and YEAR(a.fecha) = ?1 AND MONTH(a.fecha) = ?2' : 'and YEAR(a.fecha) = ?1 AND MONTH(a.fecha) < = ?2' ;
            $sql = 'SELECT '.$select.' FROM ParteDiarioBundle:DatPartePortador AS a WHERE a.portador= ?3 AND a.ueb = ?4'.$wr;
        $consulta = $em->createQuery($sql);
        $consulta->setParameter(2, $mes);
        $consulta->setParameter(1, $ano);
        $consulta->setParameter(3, $portador);
        $consulta->setParameter(4, $ueb);
        return $consulta->getSingleScalarResult();
    }
}