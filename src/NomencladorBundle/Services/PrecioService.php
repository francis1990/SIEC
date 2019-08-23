<?php
/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 1/25/2018
 * Time: 10:38 AM
 */

namespace NomencladorBundle\Services;

use Doctrine\Common\Util\Debug;
use ReporteBundle\Util\Util;
use NomencladorBundle\Entity\NomPrecio;
use Doctrine\ORM\EntityManager;

class PrecioService
{
    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function precioProducto($params, $grupo = false)
    {
        $objPrecio = $this->em->getRepository('NomencladorBundle:NomPrecio')->findByProducto($params['idproducto']);
        if (!$grupo) {
            $objGrupo = $this->em->getRepository('NomencladorBundle:NomGrupointeres')->findByIdentidad($params['identidad']);
        } else {
            $objGrupo = $params['grupo'];
        }

        $res = array();
        if (count($objGrupo) > 0) {
            if (count($objPrecio) > 0) {
                if ($grupo) {
                    if (in_array($objGrupo->getIdgrupointeres(), $objPrecio[0]->getGrupo())) {
                        $res = array(
                            "preciomn" => $objPrecio[0]->getPreciomn(),
                            "preciocuc" => $objPrecio[0]->getPreciocuc(),
                            "impuesto" => $objPrecio[0]->getImpuesto() != null && $objPrecio[0]->getImpuesto() != 0 ? $objPrecio[0]->getImpuesto() : 0
                        );
                    }
                } else {
                    if (in_array($objGrupo[0]->getIdgrupointeres(), $objPrecio[0]->getGrupo())) {
                        $res = array(
                            "preciomn" => $objPrecio[0]->getPreciomn(),
                            "preciocuc" => $objPrecio[0]->getPreciocuc(),
                            "impuesto" => $objPrecio[0]->getImpuesto() != null && $objPrecio[0]->getImpuesto() != 0 ? $objPrecio[0]->getImpuesto() : 0
                        );
                    }
                }
            } else {

                $objProd = $this->em->getRepository('NomencladorBundle:NomProducto')->find($params['idproducto']);
                if ($objProd->getIdpadre() != null) {
                    $padres = array();
                    /*El primer elemento del arreglo es el padre inmediato*/
                    $misPadres = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomproducto')->getProductosPadres($objProd,
                        $padres, $params);

                    if (count($misPadres) > 0) {
                        foreach ($misPadres['id'] as $valuePadre) {
                            $objPrecioPadre = $this->em->getRepository('NomencladorBundle:NomPrecio')->findByProducto($valuePadre);
                            if (count($objPrecioPadre) > 0) {
                                if ($grupo) {
                                    if (in_array($objGrupo->getIdgrupointeres(), $objPrecioPadre[0]->getGrupo())) {
                                        $res = array(
                                            "preciomn" => $objPrecioPadre[0]->getPreciomn(),
                                            "preciocuc" => $objPrecioPadre[0]->getPreciocuc(),
                                            "impuesto" => $objPrecioPadre[0]->getImpuesto() != null && $objPrecioPadre[0]->getImpuesto() != 0 ? $objPrecioPadre[0]->getImpuesto() : 0
                                        );
                                    }
                                } else {
                                    foreach ($objGrupo as $valueGrup) {
                                        if (in_array($valueGrup->getIdgrupointeres(), $objPrecioPadre[0]->getGrupo())) {
                                            $res = array(
                                                "preciomn" => $objPrecioPadre[0]->getPreciomn(),
                                                "preciocuc" => $objPrecioPadre[0]->getPreciocuc(),
                                                "impuesto" => $objPrecioPadre[0]->getImpuesto() != null && $objPrecioPadre[0]->getImpuesto() != 0 ? $objPrecioPadre[0]->getImpuesto() : 0
                                            );
                                            break;
                                        }
                                    }

                                }
                            }
                            if ($res != null) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $res;
    }

    public function preciosEmpresa($grupo,$nuevos){
        $dql = "SELECT pre
            FROM NomencladorBundle:NomPrecio pre
            WHERE pre.grupo like '%,".$grupo.",%' 
            or pre.grupo like '%[".$grupo.",%' 
            or pre.grupo like '%,".$grupo."]%' 
            or pre.grupo like '%[".$grupo."]%'";
        $consulta = $this->em->createQuery($dql);
        $result = $consulta->getResult();
        if(count($result)>0)
        {
            foreach($result as $p){
                $pgru= $p->getGrupo();
                $set = array_merge($pgru, $nuevos);
                $p->setGrupo( array_unique($set));
                $this->em->persist($p);
                $this->em->flush();
            }
        }
        return count($result)>0;
    }

}