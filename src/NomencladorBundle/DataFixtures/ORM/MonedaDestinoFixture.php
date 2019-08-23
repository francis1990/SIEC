<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 9/5/2018
 * Time: 16:28
 */

namespace NomencladorBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NomencladorBundle\Entity\NomMonedadestino;

class MonedaDestinoFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        //$this->loadMonedas($manager);
    }

    private function loadMonedas(ObjectManager $manager)
    {
        $monedas = array(
            array('codigo' => '1', 'nombre' => 'Cup'),
            array('codigo' => '2', 'nombre' => 'Cuc')
        );
        foreach ($monedas as $moneda) {
            $entidad = new NomMonedadestino();
            $entidad->setCodigo($moneda['codigo']);
            $entidad->setNombre($moneda['nombre']);
            $entidad->setActivo(1);
            $manager->persist($entidad);
        }
        $manager->flush();
    }

}