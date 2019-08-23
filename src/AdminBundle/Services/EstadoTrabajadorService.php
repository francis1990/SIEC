<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 20/04/2016
 * Time: 11:33
 */

namespace AdminBundle\Services;

use AdminBundle\Enumerados\EstadoTrabajador;
use Doctrine\ORM\EntityManager;
use RHumanosBundle\Entity\Rehabilitacion;

class EstadoTrabajadorService
{
    private $em;

    /**
     * @param $config_path: direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function CambiarEstadoTrabajadores(){

        $trabajadores = $this->em->getRepository('AdminBundle:Trabajador')->findBy(['estado'=>EstadoTrabajador::Suspendido]);

        foreach ($trabajadores as $trab) {
            $suspension = $trab->getSuspensiones()->last();
            if ($suspension->getFechaFin() < new \DateTime('today')){

                //Cambiando el estado del trabajador a Rehabilitado
                $trab->setEstado(EstadoTrabajador::Rehabilitado);

                $this->em->persist($trab);

                //Creando el registro de la Rehabilitacion
                $rehab = new Rehabilitacion();
                $rehab->setFechaRehab($suspension->getFechaFin()->modify('+1 days'));
                $rehab->setTrabajador($trab);

                $this->em->persist($rehab);

                $this->em->flush();
            }
        }

    }

    public function CalcularEdad($ci)
    {
        $anno = substr($ci,0,2);
        $mes = substr($ci,2,2);
        $dia = substr($ci,4,2);

        $annoActual = (new \DateTime('today'))->format('Y');
        $mesActual = (new \DateTime('today'))->format('m');
        $diaActual = (new \DateTime('today'))->format('d');

        $annoNac = 0;
        if ($anno > $annoActual)
            $annoNac = $anno + 2000;
        else
            $annoNac = $anno + 1900;

        $edad = $annoActual - $annoNac;

        if (($mesActual < $mes) || ($mesActual == $mes && $diaActual < $dia)) {
            $edad--;
        }

        return $edad;
    }
}