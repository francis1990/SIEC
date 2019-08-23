<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 9/5/2018
 * Time: 09:58
 */

namespace NomencladorBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NomencladorBundle\Entity\NomConcepto;

class ConceptosFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
     return 4;
    }

    public function load(ObjectManager $manager)
    {
        //$this->loadConceptos($manager);
    }

    private function loadConceptos(ObjectManager $manager)
    {
        $conceptos = array(
            array('codigo' => '01', 'nombre' => 'Producción', 'activo' => 1, 'tipo' => 0, 'conceptodefault' => 1),
            array('codigo' => '02', 'nombre' => 'Ventas', 'activo' => 1, 'tipo' => 1, 'conceptodefault' => 2),
            array('codigo' => '03', 'nombre' => 'De Vínculo', 'activo' => 1, 'tipo' => 0, 'conceptodefault' => 3)
        );
        foreach ($conceptos as $concepto) {
            $entidad = new NomConcepto();
            $entidad->setCodigo($concepto['codigo']);
            $entidad->setNombre($concepto['nombre']);
            $entidad->setActivo($concepto['activo']);
            $entidad->setTipo($concepto['tipo']);
            $entidad->setConceptodefault($concepto['conceptodefault']);
            $manager->persist($entidad);
        }
        $manager->flush();
    }

}