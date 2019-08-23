<?php

namespace AdminBundle\Services;


use AdminBundle\Controller\DefaultController;
use AdminBundle\Entity\DatConfig;
use Doctrine\Common\Util\Debug;
use AdminBundle\Entity\Rol;
use AdminBundle\Entity\Traza;
use AdminBundle\Entity\Usuario;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use EnumsBundle\Entity\EnumClasificacionConcepto;
use NomencladorBundle\Entity\NomProducto;
use ParteDiarioBundle\Entity\DatAlerta;
use ParteDiarioBundle\Entity\DatIncidencia;
use ParteDiarioBundle\Entity\DatParteAcopio;
use ParteDiarioBundle\Entity\DatParteAseguramiento;
use ParteDiarioBundle\Entity\DatParteCuentasCobrar;
use ParteDiarioBundle\Entity\DatParteDesvio;
use ParteDiarioBundle\Entity\DatPartediarioProduccion;
use ParteDiarioBundle\Entity\DatParteVenta;
use ParteDiarioBundle\Entity\DatPlanProduccion;
use ReporteBundle\Util\Util;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use ParteDiarioBundle\Entity\DatParteMercanciaVinculo;
use ParteDiarioBundle\Entity\DatVentaProducto;
use ParteDiarioBundle\Entity\DatParteMovimiento;
use NomencladorBundle\Entity\NomEspecifico;
use NomencladorBundle\Entity\NomFormato;
use NomencladorBundle\Entity\NomGenerico;
use NomencladorBundle\Entity\NomSabor;
use NomencladorBundle\Entity\NomSubgenerico;
use NomencladorBundle\Entity\NomTipoespecifico;


class EntityEventListener
{


    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    private function getUsu()
    {
        $token = $this->securityContext->getToken();

        if (null !== $token) {
            $user = $token->getUser();
            return null !== $user ? $user : null;
        }

        return $this->redirectToRoute('portada');
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
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if ($entity instanceof DatParteMovimiento) {
            $cant = $entity->getCantidad();
            $params = [
                'ueb' => $entity->getUeb()->getIdueb(),
                'producto' => $entity->getProducto()->getIdproducto(),
                'almacen' => $entity->getAlmacen()->getIdalmacen(),
                'fecha' => $entity->getFecha()->format('Y-m-d H:i')/*,'id'=>$entity->getIdparte()*/,
                'cantidad' => $entity->getCantidad()
            ];
            $existencia = $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->buscarExistencia($params);
            /*Si $entity->getTipomovimiento() devuelve false es porque es de salida, sino es de entrada*/
            if ($entity->getConcepto()->getTipo()) {
                $cant = $cant * -1;
                $params['cantidad'] = $params['cantidad'] * -1;
            }
            $entity->setExistencia($existencia + $cant);
            $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->actualizarExistencia($params);
        } else {
            return;
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $this->delMovimineto($args);
        $this->updateConsumoMatPrima($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof NomProducto) {
            $uow = $em->getUnitOfWork();
            $cambios = $uow->getEntityChangeSet($entity);
            if (isset($cambios['codigo']) || isset($cambios['nombre'])) {
                if (count($cambios) > 3) {
                    $this->saveLog($args, 'Se ha editado ');
                } else {
                    return;
                }
            }
        } else {
            $this->updateProducto($args);
        }
        $this->updateMovimiento($args);
        $this->saveLog($args, 'Se ha editado ');
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->saveLog($args, 'Se ha eliminado ');
    }

    /**
     * Función que salva en el log del sistema
     * @param LifecycleEventArgs $args
     * @param $action
     */
    private function saveLog(LifecycleEventArgs $args, $action)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $usuerLog = $GLOBALS['kernel']->getContainer()->get('security.context')->getToken()->getUser();
        /*Es necesario preguntar por el usuario para comprobar los intentos fallidos al loguearse.*/
        if ($usuerLog !== "anon.") {
            $usuario = $em->getRepository('AdminBundle:Usuario')->find($usuerLog->getId());
            if (!($entity instanceof Traza) && null !== $usuario) {
                $traza = new Traza();
                $traza->setUsuario($usuario);
                $traza->setUeb($usuario->getUeb());
                $traza->setFechaCreacion(new \DateTime());
                if (method_exists($entity, 'getNombreEntidad')) {
                    if ($entity instanceof DatConfig) {
                        $traza->setDescTraza($action . " el nombre de la Empresa: " . $entity->getNombreEntidad());
                    } else {
                        $traza->setDescTraza($action . $entity->getNombreEntidad());
                    }
                    $em->persist($traza);
                    $em->flush();
                }
            }
        }
    }

    public function delMovimineto(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        if ($entity instanceof DatParteMovimiento) {
            $cant = $entity->getCantidad();
            $params = [
                'ueb' => $entity->getUeb()->getIdueb(),
                'producto' => $entity->getProducto()->getIdproducto(),
                'almacen' => $entity->getAlmacen()->getIdalmacen(),
                'fecha' => $entity->getFecha(),
                'id' => $entity->getIdparte()
            ];
            if ($entity->getConcepto()->getTipo()) {
                $params['cantidad'] = $cant;
            } else {
                $params['cantidad'] = -1 * $cant;
            }
            $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->actualizarExistencia($params);
        } else {
            $mov = array();
            if ($entity instanceof DatPartediarioProduccion) {
                $mov = $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->findBy(array(
                    'fecha' => $entity->getFecha(),
                    'concepto' => 1,
                    'cantidad' => $entity->getCantidad(),
                    'ueb' => $entity->getUeb()->getIdueb(),
                    'almacen' => $entity->getAlmacen()->getIdalmacen(),
                    'producto' => $entity->getProducto()->getIdproducto()
                ));
            } else {
                if ($entity instanceof DatVentaProducto) {
                    $mov = $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->findBy(array(
                        'fecha' => $entity->getFecha(),
                        'concepto' => 2,
                        'cantidad' => $entity->getCantidad(),
                        'ueb' => $entity->getParte()->getUeb(),
                        'almacen' => $entity->getAlmacen()->getIdalmacen(),
                        'producto' => $entity->getProducto()->getIdproducto()
                    ));
                } else {
                    if ($entity instanceof DatParteMercanciaVinculo) {
                        $mov = $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->findBy(array(
                            'fecha' => $entity->getFecha(),
                            'concepto' => 3,
                            'cantidad' => $entity->getCantidad(),
                            'ueb' => $entity->getUeb()->getIdueb(),
                            'almacen' => $entity->getAlmacen()->getIdalmacen(),
                            'producto' => $entity->getProducto()->getIdproducto()
                        ));
                    }
                }
            }
            if (count($mov) > 0) {
                $em->remove($mov[0]);
                $em->flush();
            }
        }
    }

    public function newMovimiento(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (($entity instanceof DatPartediarioProduccion) || ($entity instanceof DatVentaProducto) ||
            ($entity instanceof DatParteMercanciaVinculo)
        ) {

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
                $concepto = $em->getRepository("NomencladorBundle:NomConcepto")->findByConceptodefault(1);
                $mov->setConcepto($concepto[0]);
            } elseif ($entity instanceof DatVentaProducto) {
                $mov->setUeb($entity->getParte()->getUeb());
                $mov->setFecha($entity->getParte()->getFecha());
                $concepto = $em->getRepository("NomencladorBundle:NomConcepto")->findByConceptodefault(2);
                $mov->setConcepto($concepto[0]);
            } elseif ($entity instanceof DatParteMercanciaVinculo) {
                $mov->setFecha($entity->getFecha());
                $mov->setUeb($entity->getUeb());
                $concepto = $em->getRepository("NomencladorBundle:NomConcepto")->findByConceptodefault(3);
                $mov->setConcepto($concepto[0]);
            }
            $em->persist($mov);
            $em->flush();
        } else {
            return;
        }
    }


    public function updateMovimiento(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof DatPartediarioProduccion || $entity instanceof DatVentaProducto ||
            $entity instanceof DatParteMercanciaVinculo || $entity instanceof DatParteMovimiento) {
            $em = $args->getEntityManager();
            $uow = $em->getUnitOfWork();
            $param = $uow->getOriginalEntityData($entity);
            $cambios = $uow->getEntityChangeSet($entity);
            foreach ($cambios as $key => $c) {
                $param[$key] = $c[0];
            }

            if ($entity instanceof DatPartediarioProduccion) {
                $param['concepto'] = 1;
            } else {
                if ($entity instanceof DatVentaProducto) {
                    $param['concepto'] = 2;
                } else {
                    if ($entity instanceof DatParteMercanciaVinculo) {
                        $param['concepto'] = 3;
                    }
                }
            }
            if (count($param) > 0) {
                $mov = $em->getRepository('ParteDiarioBundle:DatParteMovimiento')->buscarMovInsetado($param);
                if (count($mov) > 0) {
                    $data = $uow->getOriginalEntityData($entity);
                    $em->remove($mov[0]);
                    $em->flush();
                    $movNew = new DatParteMovimiento();
                    $movNew->setProducto($data['producto']);
                    $movNew->setAlmacen($data['almacen']);
                    $movNew->setUm($data['um']);
                    $movNew->setCantidad($data['cantidad']);
                    $con = $em->getRepository('NomencladorBundle:NomConcepto')->find($param['concepto']);
                    $movNew->setConcepto($con);
                    $movNew->setFecha($data['fecha']);
                    $movNew->setUeb($data['ueb']);
                    $em->persist($movNew);
                    $em->flush();
                }
            }
        }
        return;

    }

    public function updateProducto(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();
        $productos = array();
        if ($entity instanceof NomGenerico) {
            $productos = $em->getRepository('NomencladorBundle:NomProducto')->findByIdgenerico($entity->getIdgenerico());
        } else if ($entity instanceof NomSubgenerico) {
            $productos = $em->getRepository('NomencladorBundle:NomProducto')->findByIdsubgenerico($entity->getIdsubgenerico());
        } else if ($entity instanceof NomEspecifico) {
            $productos = $em->getRepository('NomencladorBundle:NomProducto')->findByIdespecifico($entity->getIdespecifico());
        } else if ($entity instanceof NomTipoespecifico) {
            $productos = $em->getRepository('NomencladorBundle:NomProducto')->findByIdtipoespecifico($entity->getIdtipoespecifico());
        } else if ($entity instanceof NomSabor) {
            $productos = $em->getRepository('NomencladorBundle:NomProducto')->findByIdsabor($entity->getIdsabor());
        } else if ($entity instanceof NomFormato) {
            $productos = $em->getRepository('NomencladorBundle:NomProducto')->findByIdformato($entity->getId());
        } else {
            return;
        }
        if (count($productos) > 0) {
            foreach ($productos as $p) {
                $p->setNombre($p->generarNombre());
                $p->setCodigo($p->generarCodigo());
                $em->persist($p);
            }
            $em->flush();
        }
    }

    public function updateConsumoMatPrima(LifecycleEventArgs $args)
    {
        $servPro = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomproducto');
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if ($entity instanceof DatPartediarioProduccion) {
            $padres = array();
            $misPadres = $servPro->getProductosPadres($entity->getProducto(), $padres);
            foreach ($misPadres['obj'] as $valuePadre) {
                $params['producto'] = $valuePadre->getIdproducto();
                $params['ueb'] = $entity->getUeb();
                $params['fecha'] = $entity->getFecha();
                $parte = $em->getRepository('ParteDiarioBundle:DatParteDiarioConsAseg')->verificarExistParteConsmatPrim($params);
                if (count($parte) > 0) {
                    $valor = $parte[0]->getParte()->getNivelact() - $entity->getCantidad();
                    $parte[0]->getParte()->setNivelact($valor);
                    $em->flush();
                }
            }
        }
    }

}