<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 28/04/2017
 * Time: 15:31
 */

namespace ParteDiarioBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use ParteDiarioBundle\Entity\DatPartediarioProduccion;
use ParteDiarioBundle\Entity\DatParteVenta;
use ParteDiarioBundle\Entity\DatParteMercanciaVinculo;
use ParteDiarioBundle\Entity\DatVentaProducto;
use ParteDiarioBundle\Entity\DatParteMovimiento;


class MovimientoListener
{

    private $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function getUsu()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        return $user;
    }

    private function trans($source, $placeholders = array())
    {
        return $this->container->get('translator')->trans($source, $placeholders);
    }

    private function getEntityName($entity)
    {
        $reflection = new \ReflectionClass($entity);
        return $reflection->getShortName();
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->saveLog($args, 'Se ha creado ');
        $this->newMovimiento($args);
    }

    public function prePersist(LifecycleEventArgs $args)
    {

        $this->saveLog($args, 'Se ha creado ');
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if ($entity instanceof DatParteMovimiento) {
            $cant=$entity->getCantidad();
            $params=['ueb'=>$entity->getUeb(),'producto'=>$entity->getProducto(),'almacen'=>$entity->getAlmacen(),
                'fecha'=>$entity->getFecha()];
            $existencia=$em->getRepository('ParteDiarioBundle:DatParteMovimiento')->buscarExistencia($params);
            if($entity->getConcepto()=='ventas' || $entity->getConcepto()=='para_vinculo' ||
                $entity->getConcepto()=='perdida' || $entity->getConcepto()=='merma'||
                $entity->getConcepto()=='deterioro' || $entity->getConcepto()=='salida_otros')
                     $entity->setExistencia($existencia - $cant);
            else
                $entity->setExistencia($existencia + $cant);
        }
        else{
            return;
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if ($entity instanceof DatParteMovimiento) {

            $cant=$entity->getCantidad();
            $params=['ueb'=>$entity->getUeb(),'producto'=>$entity->getProducto(),'almacen'=>$entity->getAlmacen(),
                'fecha'=>$entity->getFecha(),'id'=>$entity->getIdparte()];
            if($entity->getConcepto()=='ventas' || $entity->getConcepto()=='para_vinculo' ||
                $entity->getConcepto()=='perdida' || $entity->getConcepto()=='merma'||
                $entity->getConcepto()=='deterioro' || $entity->getConcepto()=='salida_otros')
                $params['cantidad']=$cant;
            else
                $params['cantidad']=-1*$cant;
            $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->actualizarExistencia($params);
        }
        else if($entity instanceof DatPartediarioProduccion) {
            $mov= $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->findBy(array(
                //'fecha'=>date_format($entity->getFecha(),'Y-m-d'),
                'concepto'=>'produccion',
                'cantidad'=>$entity->getCantidad(),
                'ueb'=>$entity->getUeb(),
                'almacen'=>$entity->getAlmacen(),
                'producto'=>$entity->getProducto()
            ));
            if(!empty($mov)){
                $em->remove($mov);
                $em->flush();
            }
        }
        else if($entity instanceof DatVentaProducto) {
            $mov= $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->findBy(array(
                //'fecha'=>date_format($entity->getParte()->getFecha(),'Y-m-d'),
                'concepto'=>'ventas',
                'cantidad'=>$entity->getCantidad(),
                'ueb'=>$entity->getParte()->getUeb(),
                'almacen'=>$entity->getAlmacen(),
                'producto'=>$entity->getProducto()
            ));
            if(!empty($mov)){
                $em->remove($mov);
                $em->flush();
            }
        }
        else if($entity instanceof DatParteMercanciaVinculo) {
            $mov= $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->findBy(array(
                //'fecha'=>date_format($entity->getFecha(),'Y-m-d'),
                'concepto'=>'de_vinculo',
                'cantidad'=>$entity->getCantidad(),
                'ueb'=>$entity->getUeb(),
                'almacen'=>$entity->getAlmacen(),
                'producto'=>$entity->getProducto()
            ));
            if(!empty($mov)){
                $em->remove($mov);
                $em->flush();
            }
        }
        else
            return;

    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if ($entity instanceof DatParteMovimiento) {
            $params = ['ueb' => $entity->getUeb(), 'producto' => $entity->getProducto(), 'almacen' => $entity->getAlmacen(),
                'fecha' => $entity->getFecha(), 'newcant' => $entity->getCantidad(), 'id' => $entity->getIdparte()];
            if ($entity->getConcepto() == 'ventas' || $entity->getConcepto() == 'para_vinculo' ||
                $entity->getConcepto() == 'perdida' || $entity->getConcepto() == 'merma' ||
                $entity->getConcepto() == 'deterioro' || $entity->getConcepto() == 'salida_otros'
            )
                $params['factor'] = 1;
            else
                $params['factor'] = -1;
            $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->calcularExistencia($params);
        }
        else if (($entity instanceof DatPartediarioProduccion) || ($entity instanceof DatVentaProducto) ||
            ($entity instanceof DatParteMercanciaVinculo)
        ) {
            $this->updateMovimiento($args);
         }
        else{
            return;
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {

    }

    public function postRemove(LifecycleEventArgs $args){


    }

    private function saveLog(LifecycleEventArgs $args, $action)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if (!($entity instanceof Traza)) {
            $traza = new Traza();
            $traza->setUsuario($this->getUsu());
            $traza->setFechaCreacion(new \DateTime());

            if ($entity instanceof Usuario) {
                $traza->setDescTraza($action . "el usuario " . $entity->getUsername());
            } else if ($entity instanceof Rol) {
                $traza->setDescTraza($action . "el rol " . $entity->getdescRol());
            }
            if ($entity instanceof DatAlerta) {
                $traza->setDescTraza($action . "la alerta " . $entity->getActividad());
            }
            else{
                return;
            }

            $em->persist($traza);
            $em->flush();
        }
    }

    public function newMovimiento(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(($entity instanceof DatPartediarioProduccion) || ($entity instanceof DatVentaProducto)||
            ($entity instanceof DatParteMercanciaVinculo) )
        {
            $em = $args->getEntityManager();
            $mov = new DatParteMovimiento();
            $mov->setProducto($entity->getProducto());
            $mov->setAlmacen($entity->getAlmacen());
            $mov->setUm($entity->getUm());
            $mov->setCantidad($entity->getCantidad());
            $mov->setExistencia(0);
            if ($entity instanceof DatPartediarioProduccion) {
                $mov->setFecha($entity->getFecha());
                $mov->setUeb($entity->getUeb());
                $mov->setConcepto('produccion');

            }
            elseif ($entity instanceof DatVentaProducto) {
                $mov->setConcepto('ventas');
                $mov->setUeb($entity->getParte()->getUeb());
                $mov->setFecha($entity->getParte()->getFecha());

            }
            elseif ($entity instanceof DatParteMercanciaVinculo) {
                $mov->setFecha($entity->getFecha());
                $mov->setUeb($entity->getUeb());
                $mov->setConcepto('de_vinculo');

            }
            $em->persist($mov);
            $em->flush();
        }
        else
            return;
    }

    public function deleteMovimiento(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(($entity instanceof DatPartediarioProduccion) || ($entity instanceof DatVentaProducto)||
            ($entity instanceof DatParteMercanciaVinculo) )
        {
            $em = $args->getEntityManager();
            $mov = new DatParteMovimiento();
            $mov->setProducto($entity->getProducto());
            $mov->setAlmacen($entity->getAlmacen());
            $mov->setUm($entity->getUm());
            $mov->setCantidad($entity->getCantidad());
            $mov->setExistencia(0);
            if ($entity instanceof DatPartediarioProduccion) {
                $mov->setFecha($entity->getFecha());
                $mov->setUeb($entity->getUeb());
                $mov->setConcepto('produccion');

            }
            elseif ($entity instanceof DatVentaProducto) {
                $mov->setConcepto('ventas');
                $mov->setUeb($entity->getParte()->getUeb());
                $mov->setFecha($entity->getParte()->getFecha());

            }
            elseif ($entity instanceof DatParteMercanciaVinculo) {
                $mov->setFecha($entity->getFecha());
                $mov->setUeb($entity->getUeb());
                $mov->setConcepto('de_vinculo');

            }
            $em->persist($mov);
            $em->flush();
        }
        else
            return;
    }

    public function updateMovimiento(LifecycleEventArgs $args)
    {
            $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $param=[];
        if ($entity instanceof DatPartediarioProduccion) {
        $pro=$em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->buscar($entity->getIdparte());
        //$param['fecha']=$pro[0]->getFecha();
            if(count($pro)>0) {
                $param['producto'] = $pro[0]->getProducto()->getIdproducto();
                $param['almacen'] = $pro[0]->getAlmacen()->getIdalmacen();
                $param['cantidad'] = $pro[0]->getCantidad();
                $param['ueb'] = $pro[0]->getUeb();
                $param['concepto'] = 'produccion';
            }
        }
        else if($entity instanceof DatVentaProducto) {
            $ven=$em->getRepository('ParteDiarioBundle:DatVentaProducto')->buscar($entity->getId());
            if(count($ven)>0){
            $param['producto']=$ven[0]->getProducto()->getIdproducto();
            $param['almacen']=$ven[0]->getAlmacen()->getIdalmacen();
            $param['cantidad']=$ven[0]->getCantidad();
            $param['fecha']=$ven[0]->getParte()->getFecha();
            $param['ueb']=$ven[0]->getParte()->getUeb()->getIdueb();
            $param['concepto']='ventas';
            }
        }
        /*else if($entity instanceof DatParteMercanciaVinculo) {
            $vin=$em->getRepository('ParteDiarioBundle:DatParteMercanciaVinculo')->buscar($entity->getIdparte());
            if(count($vin)>0){
                $param['producto']=$vin[0]['producto']['idproducto'];
                $param['almacen']=$vin[0]['almacen']['idalmacen'];
                $param['cantidad']=$vin[0]['cantidad'];
                //$param['fecha']=$entity->getFecha();
                $param['ueb']=$vin[0]['ueb']['idueb'];
                $param['concepto']='de_vinculo';
            }
        }*/
        if(count($param)>0){
            $mov = $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->buscarMovInsetado($param);
            if(count($mov)>0)
            {
                $mov[0]->setProducto($entity->getProducto());
                $mov[0]->setAlmacen($entity->getAlmacen());
                $mov[0]->setUm($entity->getUm());
                $mov[0]->setCantidad($entity->getCantidad());
                $em->persist($mov[0]);
                $em->flush();
            }
        }
    }

}