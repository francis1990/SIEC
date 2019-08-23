<?php
/**
 * Created by PhpStorm.
 * User: Roniel
 * Date: 09/05/2017
 * Time: 15:31
 */

namespace ParteDiarioBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use ParteDiarioBundle\Entity\DatPartediarioProduccion;
use ParteDiarioBundle\Entity\DatParteAseguramiento;
use ParteDiarioBundle\Entity\DatPartediarioEconomia;
use ParteDiarioBundle\Entity\DatPartePortador;
use ParteDiarioBundle\Entity\DatParteTransporte;
use ParteDiarioBundle\Entity\DatParteVenta;
use ParteDiarioBundle\Controller\DatAlertaController;


class AlertListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $alert = new DatAlertaController();
        if (($entity instanceof DatPartediarioProduccion) || ($entity instanceof DatParteAseguramiento) || ($entity instanceof DatParteTransporte) || ($entity instanceof DatPartePortador) || ($entity instanceof DatParteVenta) || ($entity instanceof DatPartediarioEconomia || ($entity instanceof DatParteVenta))) {
            $alert->alertsAction();
        }
    }


    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $alert = new DatAlertaController();
        if (($entity instanceof DatPartediarioProduccion) || ($entity instanceof DatParteAseguramiento) || ($entity instanceof DatParteTransporte) || ($entity instanceof DatPartePortador) || ($entity instanceof DatParteVenta) || ($entity instanceof DatPartediarioEconomia || ($entity instanceof DatParteVenta))) {
            $alert->alertsAction();

        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {

        $entity = $args->getEntity();
        $alert = new DatAlertaController();
        if (($entity instanceof DatPartediarioProduccion) || ($entity instanceof DatParteAseguramiento) || ($entity instanceof DatParteTransporte) || ($entity instanceof DatPartePortador) || ($entity instanceof DatParteVenta) || ($entity instanceof DatPartediarioEconomia || ($entity instanceof DatParteVenta))) {
            $alert->alertsAction();

        }
    }

}