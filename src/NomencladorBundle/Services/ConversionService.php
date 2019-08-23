<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 29/5/2018
 * Time: 19:17
 */

namespace NomencladorBundle\Services;


use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use NomencladorBundle\Entity\NomProducto;

class ConversionService
{

    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function obtenerConversion(NomProducto $producto)
    {
        if (!is_null($producto->getIdformato())) {
            $params['inicio'] = $producto->getUmOperativa()->getIdunidadmedida();
            $params['fin'] = $producto->getIdformato()->getIdunidadmedida()->getIdunidadmedida();
            $conversion = $this->em->getRepository('NomencladorBundle:NomConversion')->findByUnidadesMedidas($params);
            return $conversion;
        } else {
            return 1;
        }
    }

}