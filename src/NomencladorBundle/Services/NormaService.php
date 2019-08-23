<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 25/09/2018
 * Time: 11:41
 */

namespace NomencladorBundle\Services;


use Doctrine\ORM\EntityManager;

class NormaService
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getProdNormaCons()
    {
        $dql = 'SELECT n,p
            FROM NomencladorBundle:NomNorma n
            JOIN n.producto p';
        $consulta = $this->em->createQuery($dql);
        $prods = $consulta->getResult();
        $valores = [];
        foreach ($prods as $value) {
            $valores[] = $value->getProducto();
        }
        return $valores;
    }

    public function getNormaAseg($producto)
    {
        $dql = 'SELECT n
            FROM NomencladorBundle:DatNormaAseguramiento n
            JOIN n.norma nor WHERE nor.producto = '.$producto->getIdproducto();
        $consulta = $this->em->createQuery($dql);
        dump($consulta->getResult());die;
        $prods = $consulta->getResult();
    }

}