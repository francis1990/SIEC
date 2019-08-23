<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 28/04/2017
 * Time: 15:40
 */

namespace ParteDiarioBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use ParteDiarioBundle\Entity\DatPartediarioProduccion;
use ParteDiarioBundle\Entity\DatParteVenta;
use ParteDiarioBundle\Entity\DatParteMercanciaVinculo;
use ParteDiarioBundle\Entity\DatVentaProducto;
use ParteDiarioBundle\Entity\DatParteMovimiento;


class MovimientoSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
        );
    }
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }
    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if(!$entity instanceof DatParteMovimiento) {

            $mov=new DatParteMovimiento();
            $mov->setFecha($entity->getFecha());
            $mov->setUeb($entity->getUeb());
            $mov->setProducto($entity->getProducto());
            $mov->setAlmacen($entity->getAlmacen());
            $mov->setUm($entity->getUm());
            $mov->setCantidad($entity->getCantidad());
            if ($entity instanceof DatPartediarioProduccion) {
                $mov->setConcepto('produccion');


            }/* elseif ($entity instanceof DatVentaProducto) {
                $mov->setConcepto('ventas');

            } elseif (!$entity instanceof DatParteMercanciaVinculo) {
                $mov->setConcepto('de_vinculo');

            }*/
            else {
                return;
            }
            $em->persist($mov);
            $em->flush();
        }


    }
}