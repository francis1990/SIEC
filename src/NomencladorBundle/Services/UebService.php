<?php
/**
 * Created by PhpStorm.
 * User: maria.caro
 * Date: 05/06/2017
 * Time: 01:11 PM
 */
namespace NomencladorBundle\Services;

use NomencladorBundle\Entity\NomUeb;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use ReporteBundle\Util\Util;

class UebService
{
    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getUebByIds($array)
    {
        $uebs = implode(",", $array);
        $dql = 'SELECT g
            FROM NomencladorBundle:NomUeb g
            WHERE   g.idueb in (' . $uebs . ') ORDER BY g.idueb';
        $consulta = $this->em->createQuery($dql);
        $uebsRes = $consulta->getResult();
        foreach($uebsRes as $value){
            $result[] = $value->getIdueb();
        }
        return $result;
    }

    public function getUebAll()
    {
        $resultUeb = $this->em->getRepository('NomencladorBundle:NomUeb')->findAll();
        foreach ($resultUeb as $valueUeb) {
            $uebs[] = $valueUeb->getIdueb();
        }
        return $uebs;
    }

    public function getUebNombre($em, $params)
    {
        $uebImpl = implode(',', $params['arbolder_array']);
        $uebs = array();
        $dql = "SELECT ue FROM NomencladorBundle:NomUeb ue WHERE ue.idueb in($uebImpl)";
        $consulta = $em->createQuery($dql);
        $result = $consulta->getResult();
        $col = 2;
        foreach ($result as $value) {
            $uebs[Util::eliminar_simbolos($value->getNombre())] = $col."-".$value->getIdueb();
            $col++;
        }
        return $uebs;
    }
    public function findByUebs($params)
    {
        $uebImpl = implode(',', $params['arbolder_array']);
        $where = $params['arbolder_array'] != null ? "where ue.idueb in(".$uebImpl.")" : 0;
        $dql = "SELECT ue FROM NomencladorBundle:NomUeb ue ".$where;
        $consulta = $this->em->createQuery($dql);
        $result = $consulta->getArrayResult();
        return $result;
    }


}