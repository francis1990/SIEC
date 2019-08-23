<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 17/5/2018
 * Time: 23:38
 */

namespace NomencladorBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NomencladorBundle\Entity\NomEspecifico;
use NomencladorBundle\Entity\NomFormato;
use NomencladorBundle\Entity\NomGenerico;
use NomencladorBundle\Entity\NomSabor;
use NomencladorBundle\Entity\NomSubgenerico;
use NomencladorBundle\Entity\NomTipoespecifico;
use NomencladorBundle\Entity\NomUnidadmedida;

class DatosProducto extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 5;
    }

    public function load(ObjectManager $manager)
    {
        //$this->loadDatosProducto($manager);
    }

    private function loadDatosProducto(ObjectManager $manager)
    {
        $genericos = array(
            array("20", "Leche Fresca", "1"),
            array("22", "Lácteo", "0")
        );
        foreach ($genericos as $index => $gen) {
            $genN = new NomGenerico();
            $genN->setActivo(true);
            $genN->setCodigo($gen[0]);
            $genN->setNombre($gen[1]);
            $genN->setAcopio($gen[2]);
            $manager->persist($genN);
        }
        $manager->flush();

        $subgenerico = array(
            array("01", "Acopio"), array("11", "Leche Pasterizada"), array("12", "Crema de Leche"), array("13", "Suero Lácteo"),
            array("14", "Grasa Láctea"), array("15", "Leche Concentrada"), array("20", "Leche en Polvo"), array("30", "Queso"),
            array("40", "Queso Crema"), array("50", "Yogurt"), array("55", "Yogurt de Soya"), array("60", "Mantequilla"),
            array("70", "Helado"), array("81", "Mezcla para Batido"), array("84", "Lactosoy"), array("85", "Chocole"),
            array("92", "Harina Lacteada"), array("91", "Dulce de Leche")
        );

        $genNoAcopio = $manager->getRepository('NomencladorBundle:NomGenerico')->findByAcopio(0);
        foreach ($subgenerico as $index => $sub) {
            $subN = new NomSubgenerico();
            $subN->setActivo(true);
            $subN->setCodigo($sub[0]);
            $subN->setNombre($sub[1]);
            $subN->setEmpaque(0);
            if ($index == 0) {
                $genAcopio = $manager->getRepository('NomencladorBundle:NomGenerico')->findByAcopio(1);
                $subN->setGenerico($genAcopio[0]);
            } else {
                $subN->setGenerico($genNoAcopio[0]);
            }
            $manager->persist($subN);
        }

        $especificos = array(
            array("01", "de Vaca"), array("02", "de Cabra"), array("03", "de Búfala"), array("04", "de Oveja"),
            array("11", "Condensada"), array("12", "Evaporada"), array("13", "Fortificada"), array("14", "Maternizada"),
            array("15", "Vitaminada"), array("16", "Dietética"), array("21", "Entera"), array("22", "Descremada"),
            array("23", "Fórmula láctea"), array("31", "Análogo"), array("32", "Azúl"), array("33", "Duro"),
            array("34", "Semiduro"), array("35", "Fresco"), array("36", "Pasta Blanda"), array("37", "Pasta Hilada"),
            array("38", "Fundido"), array("41", "Natural"), array("42", "de Soya"), array("43", "Untable"),
            array("51", "Batido"), array("52", "Coágulo"), array("54", "Probiótico"), array("61", "Sin sal"),
            array("62", "Con sal"), array("63", "Margarina"), array("64", "Vegetariana"), array("71", "Especial de crema"),
            array("72", "de Crema"), array("73", "de Leche"), array("74", "Sorbete"), array("75", "Paquetería")
        );

        foreach ($especificos as $esp) {
            $espN = new NomEspecifico();
            $espN->setActivo(true);
            $espN->setCodigo($esp[0]);
            $espN->setNombre($esp[1]);
            $manager->persist($espN);
        }

        $tipoespecifico = array(
            array("01", "Estatal"), array("02", "Privado"), array("05", "Natural"), array("06", "Saborizada"),
            array("11", "Atlántico"), array("12", "Pizarrella"), array("13", "Lonja"), array("14", "Monte Verde"),
            array("18", "Atabey"), array("20", "Azulino"), array("21", "Guaicanamar"), array("22", "Gorgonzola"),
            array("23", "Torre Azúl"), array("24", "Azúl Siboney"), array("25", "Mar Azúl"), array("30", "Caribeño"),
            array("31", "Grana"), array("32", "Granita"), array("33", "Sbrinz"), array("36", "Broodkas"),
            array("37", "Caribe"), array("38", "Danbo"), array("39", "Gratina"), array("40", "Coral"),
            array("41", "Corojito"), array("42", "Gouda"), array("43", "Guama"), array("44", "Fontina"),
            array("45", "Lunch"), array("46", "Svecia"), array("47", "Monumental"), array("48", "Yaguajay"),
            array("49", "Patagrás"), array("50", "Santa Cruz"), array("81", "Frescal"), array("82", "Búfala"),
            array("83", "de Vaca"), array("56", "Reblochón"), array("57", "Camembert"), array("58", "Carre d Lest"),
            array("59", "Monster"), array("64", "Mozzarella"), array("65", "Cottage"), array("66", "Cumanayagua"),
            array("67", "Guamuaya Salame"), array("68", "Cremoso ahumado"), array("69", "Cubanito"), array("94", "Desnatado Beatriz"),
            array("95", " Semidescremado Beatriz"), array("90", "Coppelia"), array("91", "Varadero"), array("92", "Guarina")
        );

        foreach ($tipoespecifico as $tipesp) {
            $tipespN = new NomTipoespecifico();
            $tipespN->setActivo(true);
            $tipespN->setCodigo($tipesp[0]);
            $tipespN->setNombre($tipesp[1]);
            $manager->persist($tipespN);
        }

        $sabor = array(
            array("11", "Azucarado"), array("12", "Albaricoque"), array("13", "Almendra"), array("14", "Aromatizado"),
            array("15", "Avellana"), array("17", "Brandy"), array("18", "Café"), array("19", " Caramelo"),
            array("20", "Cereza"), array("21", "Choco almendra"), array("22", " Choco nela"), array("23", "Choco malta"),
            array("27", "Chocolate"), array("30", "Coco"), array("33", "Crema de guayaba"), array("34", "Crema de vie"),
            array("36", "Frambuesa"), array("37", "Fresa"), array("38", "Fresa bombón"), array("39", "Fresa Piña"),
            array("40", "Fruta bomba"), array("41", "Guanabana"), array("42", "Guayaba"), array("43", "Kiwi"),
            array("44", "Limón"), array("45", "Malta"), array("46", "Mamey"), array("47", "Mandarina"),
            array("48", " Mango"), array("49", "Maní"), array("50", "Mantecado"), array("51", "Mantecado bizcocho"),
            array("52", "Manzana"), array("53", " Melocotón"), array("54", "Melón"), array("55", "Menta chips"),
            array("56", "Miel"), array("57", "Moscatel"), array("58", "Naranja"), array("59", "Naranja Piña"),
            array("60", "Nuez"), array("61", "Pasas al ron"), array("62", "Pera"), array("63", "Piña"),
            array("64", " Piña glace"), array("65", "Plátano"), array("66", "Rizado de chocolate"), array("67", "Rizado de ciruela"),
            array("68", "Rizado de fresa"), array("69", "Rizado de Guayaba"), array("70", "Rizado de Piña"), array("77", "Frutas varias"),
            array("71", "Saborizado"), array("72", "Toronja"), array("73", "Tutty Frutty"), array("74", "Vainilla"),
            array("75", "Vainilla chips"), array("76", "Vainilla pasas"), array("01", "1ra Calidad"), array("02", "2da Calidad"),
            array("03", "3ra Calidad"), array("10", "Natural")
        );

        foreach ($sabor as $sab) {
            $saborN = new NomSabor();
            $saborN->setActivo(true);
            $saborN->setCodigo($sab[0]);
            $saborN->setNombre($sab[1]);
            $manager->persist($saborN);
        }

        $unidadmedida = array(
            array("11", "Toneladas", "tns", "4"), array("12", "Kilogramos", "Kgs", "4"), array("13", "Libras", "Lib", "4"), array("14", "Onzas", "Onz", "4"),
            array("15", "Gramos", "Grs", "4"), array("21", "Miles de galones", "Mgl", "3"), array("22", "Galones", "Gls", "3"),
            array("23", "Miles de Litros", "Mls", "3"), array("24", "Litros", "Lts", "3"), array("31", "Miles de Unidades", "MU", "11"),
            array("32", "Unidades", "Uno", "11"), array("41", "Kilowatt", "Kwt", "7"), array("42", "Watt", "Wat", "7"),
            array("51", "Rollos", "Roll", "1"), array("33", "Miles de CUC", "MCUC", "6"), array("25", "Mililitros", "ml", "3")
        );

        foreach ($unidadmedida as $um) {
            $umN = new NomUnidadmedida();
            $umN->setActivo(true);
            $umN->setCodigo($um[0]);
            $umN->setNombre($um[1]);
            $umN->setAbreviatura($um[2]);
            $umN->setIdTipoUM($um[3]);
            $manager->persist($umN);
        }
        $manager->flush();

        $formatoKG = array(
            array("301", "Bloques de 1 kilogramo", "1"), array("303", "Bloques de 3 kilogramos", "3"), array("305", "Bloques de 5 kilogramos", "5"),
            array("310", "Bloques de10 kilogramos", "10"), array("312", "Bloques de12 kilogramos", "12"), array("315", "Bloques de 15 kilogramos", "15"),
            array("415", "Sacos de 15 kilogramos", "15"), array("420", "Sacos de 20 kilogramos", "20"), array("425", " Sacos de 25 kilogramos", "25")
        );
        $umKG = $manager->getRepository("NomencladorBundle:NomUnidadmedida")->findBy(array("alias" => "kilogramos"));
        foreach ($formatoKG as $formKG) {
            $formNKG = new NomFormato();
            $formNKG->setActivo(true);
            $formNKG->setCodigo($formKG[0]);
            $formNKG->setNombre($formKG[1]);
            $formNKG->setPeso($formKG[2]);
            $formNKG->setIdunidadmedida($umKG[0]);
            $manager->persist($formNKG);
        }

        $formatoGramos = array(
            array("102", "Pastillas de 57.5 gramos", "57.5"), array("106", "Bolsas de 200 gramos", "200"), array("103", "Pastillas de 110 gramos", "110"),
            array("107", "Bolsas de 400 gramos", "400"), array("108", "Bolsas de 500 gramos", "500"), array("104", "Pastillas de 115 gramos", "115"),
            array("105", "Pastillas de 150 gramos", "150"), array("110", "Bolsas de 1000 gramos", "1000"), array("397", "Latas de 397 gramos", "397"),
            array("751", "Minidosis de 10 gramos", "10"), array("772", " Pastillas de 110 gramos", "110"), array("773", " Pastillas de 115 gramos", "115"),
            array("774", " Pastillas de 150 gramos", "150"), array("791", "Paquetes de 48*397", "150"), array("792", "Paquetes de 48*410", "190"),
            array("701", "Potes de 115 gramos", "115"), array("702", "Potes de 250 gramos", "250"), array("703", "Potes de 500 gramos", "500")
        );
        $umG = $manager->getRepository("NomencladorBundle:NomUnidadmedida")->findBy(array("alias" => "gramos"));
        foreach ($formatoGramos as $formG) {
            $formNG = new NomFormato();
            $formNG->setActivo(true);
            $formNG->setCodigo($formG[0]);
            $formNG->setNombre($formG[1]);
            $formNG->setPeso($formG[2]);
            $formNG->setIdunidadmedida($umG[0]);
            $manager->persist($formNG);
        }

        $formatoGalones = array(array("901", "1 Galón", "1"));
        $umGa = $manager->getRepository("NomencladorBundle:NomUnidadmedida")->findBy(array("alias" => "galones"));
        $formNGa = new NomFormato();
        $formNGa->setActivo(true);
        $formNGa->setCodigo($formatoGalones[0][0]);
        $formNGa->setNombre($formatoGalones[0][1]);
        $formNGa->setPeso($formatoGalones[0][2]);
        $formNGa->setIdunidadmedida($umGa[0]);
        $manager->persist($formNGa);

        $formatoMLT = array(
            array("217", "Bolsas de 917 mililitros", "917"), array("612", " Potes 125 mililitros", "125"), array("620", "Potes de 200 mililitros", "200"),
            array("622", "Potes de 225 mililitros", "225"), array("625", "Potes de 250 mililitros", "250"), array("640", "Potes de 400 mililitros", "400"),
            array("650", "Potes de 500 mililitros", "500"), array("680", "Potes de 800 mililitros", "800"),
        );
        $umMLT = $manager->getRepository("NomencladorBundle:NomUnidadmedida")->findBy(array("alias" => "milesdelitros"));
        foreach ($formatoMLT as $formMLT) {
            $formNMLT = new NomFormato();
            $formNMLT->setActivo(true);
            $formNMLT->setCodigo($formMLT[0]);
            $formNMLT->setNombre($formMLT[1]);
            $formNMLT->setPeso($formMLT[2]);
            $formNMLT->setIdunidadmedida($umMLT[0]);
            $manager->persist($formNMLT);
        }

        $formatoLt = array(
            array("504", "Cubos de 4 litros", "4"), array("510", "Cubos de 10 litros", "10"),
            array("520", "Cubos de 20 litros", "20"), array("551", "Tarrinas de 1 Litro", "1"), array("552", "Tarrinas de 2 litros", "2"),
            array("554", "Tarrinas de 4 litros", "4"), array("556", "Tarrinas de 4.75 litros", "4.75"), array("559", "Tarrinas de 1.5 litros", "1.5"),
            array("590", "Cajas de 10 litros", "10"), array("591", "Cajas de 20 litros", "20"), array("601", "Pote de 1 Litro", "1"),
            array("602", "Potes de 2 litros", "2"), array("603", "Potes de 3 litros", "3"), array("605", "Potes de 1.5 litros", "1.5"),
            array("691", "Pomos de 1 Litro", "1"), array("692", "Pomos de 2 litros", "2"), array("693", "Pomos de 3 litros", "3"),
            array("694", "Pomos de 4 litros", "4"), array("695", "Pomos de 5 litros", "5"), array("697", "Pomos de 0.5 litros", "0.5"),
            array("698", "Pomos de 1.5 Litro", "1.5"), array("804", "Cubetas de 4 kilogramos", "4"), array("810", "Cubetas de 10 kilogramos", "10"),
            array("820", "Cubetas de 20 kilogramos", "20"), array("512", "Cubos de 12 litros", "12"), array("519", "Cubos de 19 litros", "19"),
            array("699", "Pomos de 0.250 millilitros", "0.25")
        );

        $umLT = $manager->getRepository("NomencladorBundle:NomUnidadmedida")->findBy(array("alias" => "litros"));
        foreach ($formatoLt as $formLT) {
            $formNLT = new NomFormato();
            $formNLT->setActivo(true);
            $formNLT->setCodigo($formLT[0]);
            $formNLT->setNombre($formLT[1]);
            $formNLT->setPeso($formLT[2]);
            $formNLT->setIdunidadmedida($umLT[0]);
            $manager->persist($formNLT);
        }

        $manager->flush();
    }

}