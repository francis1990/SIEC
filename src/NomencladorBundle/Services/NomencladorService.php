<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 2/23/2018
 * Time: 11:31 PM
 */

namespace NomencladorBundle\Services;

use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\NomencladorBundle\Entity\NomDpa;

class NomencladorService
{
    private $em;

    /**
     * @param $config_path : direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function eliminarObjEntidad($params)
    {
        $enUso = false;
        $msg = "";
        $mensaje = "";
        $params['bundle'] = isset($params['bundle']) ? $params['bundle'] : "NomencladorBundle:";
        $enUsoParcial = false;
        $hijos = array();
        $hijosObj = array();
        $idpadre = "";
        $elementosPadresNoEliminar = array();
        $padresObj = array();
        if (!is_array($params['valor']))
            $params['valor'] = [$params['valor']];

        if (count($params['valor']) > 1) {
            foreach ($params['valor'] as $f) {
                if (!$this->em->isOpen()) {
                    $GLOBALS['kernel']->getContainer()->get('doctrine')->resetManager();
                    $this->em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager();
                }
                $objEnt = $this->em->getRepository($params['bundle'] . $params['tabla'])->find($f);
                if (isset($params['arbol']) && !$objEnt->getHoja()) {
                    $enUsoPadre = false;
                    $enUsoParcial = false;
                    $hijos = array();
                    $hijosObj = array();
                    $idpadre = "";
                    $cantHijosElim = 0;
                    if ($params['servicio'] == "nomenclador.nomdpa") {
                        $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getDpaHijos($objEnt, $hijos);
                    } else if ($params['servicio'] == "nomenclador.nomentidad") {
                        $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getEntidadesHijasDadoEnt($objEnt, $hijos);
                    } else if ($params['servicio'] == "nomenclador.nomgrupointeres") {
                        $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getGruposHijos($objEnt, $hijos);
                    } else if ($params['servicio'] == "nomenclador.nomproducto") {
                        $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getProductosHijos($objEnt, $hijos);
                    }

                    /*Esto es para comprobar si el padre esta incluido en el arreglo de los hijos*/
                    if (in_array($f, $hijosObj)) {
                        /*Elimino el padre del listar de los hijos y lo guardo en una variable*/
                        $idpadre = $hijosObj[0];
                        unset($hijosObj[0]);
                        /*Aki reorganizo los indices del array*/
                        $hijosObj = array_values($hijosObj);
                        /*Aki ordeno los elementos en orden inveso*/
                        $hijosObj = array_reverse($hijosObj);
                    } else {
                        $idpadre = $f;
                    }

                    if (count($hijosObj) > 0) {
                        foreach ($hijosObj as $valueObj) {
                            if (!$this->em->isOpen()) {
                                $GLOBALS['kernel']->getContainer()->get('doctrine')->resetManager();
                                $this->em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager();
                                /* $user = $GLOBALS['kernel']->getContainer()->get('security.context')->getToken()->getUser();
                                 $this->em->merge($user);*/
                            }
                            $params['hijo'] = $valueObj;
                            try {
                                if (!in_array($valueObj, $elementosPadresNoEliminar)) {
                                    $params['hijo'] = $valueObj;
                                    $this->consultaEliminarObj($params);
                                    $cantHijosElim++;
                                }
                            } catch (ForeignKeyConstraintViolationException $e) {
                                $enUsoParcial = true;
                                $enUso = true;
                                /*Aki en caso de que un hijo no pueda eliminarse busco todos sus padres y lo inserto en un arreglo
                                para no eliminarlos*/
                                $padres = array();
                                $objEnt = $this->em->getRepository($params['bundle'] . $params['tabla'])->find($valueObj);
                                $padresObj = $this->getObjPadres($objEnt, $padres, $params);
                                $elementosPadresNoEliminar = array_merge($elementosPadresNoEliminar, $padresObj);
                                $padresObj = array();
                            }
                        }
                    }

                    /*Aki verifico que si todos los hijos no estan en uso entonces borro el padre*/
                    if (!$enUsoParcial) {
                        try {
                            $params['hijo'] = $idpadre;
                            $this->consultaEliminarObj($params);
                        } catch (ForeignKeyConstraintViolationException $e) {
                            $enUso = true;
                        }
                    }
                } else {
                    try {
                        $params['hijo'] = $f;
                        $this->consultaEliminarObj($params);
                    } catch (ForeignKeyConstraintViolationException $e) {
                        $enUso = true;
                    }
                }
            }

        } else {
            $cantHijosElim = 0;
            $objEnt = $this->em->getRepository($params['bundle'] . $params['tabla'])->find($params['valor'][0]);
            if (isset($params['arbol']) && !$objEnt->getHoja()) {
                if ($params['servicio'] == "nomenclador.nomdpa") {
                    $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getDpaHijos($objEnt, $hijos);
                } else if ($params['servicio'] == "nomenclador.nomentidad") {
                    $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getEntidadesHijasDadoEnt($objEnt, $hijos);
                } else if ($params['servicio'] == "nomenclador.nomgrupointeres") {
                    $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getGruposHijos($objEnt, $hijos);
                } else if ($params['servicio'] == "nomenclador.nomproducto") {
                    $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getProductosHijos($objEnt, $hijos);
                } else if ($params['servicio'] == "nomenclador.nomaseguramiento") {
                    $hijosObj = $GLOBALS['kernel']->getContainer()->get($params['servicio'])->getAseguramientoHijas($objEnt, $hijos);
                }


                /*Esto es para comprobar si el padre esta incluido en el arreglo de los hijos*/
                if (in_array($params['valor'][0], $hijosObj)) {
                    $idpadre = $hijosObj[0];
                    unset($hijosObj[0]);
                    $hijosObj = array_values($hijosObj);
                    /*Aki ordeno los elementos en orden inveso*/
                    $hijosObj = array_reverse($hijosObj);
                } else {
                    $idpadre = $params['valor'][0];
                }

                if (count($hijosObj) > 0) {
                    foreach ($hijosObj as $valObj) {
                        if (!$this->em->isOpen()) {
                            $GLOBALS['kernel']->getContainer()->get('doctrine')->resetManager();
                            $this->em = $GLOBALS['kernel']->getContainer()->get('doctrine')->getManager();
                            /* $user = $GLOBALS['kernel']->getContainer()->get('security.context')->getToken()->getUser();
                             $this->em->merge($user);*/
                        }
                        $params['hijo'] = $valObj;
                        try {
                            if (!in_array($valObj, $elementosPadresNoEliminar)) {
                                $params['hijo'] = $valObj;
                                $this->consultaEliminarObj($params);
                                $cantHijosElim++;
                            }
                        } catch (ForeignKeyConstraintViolationException $e) {
                            $enUsoParcial = true;
                            $enUso = true;
                            /*Aki en caso de que un hijo no pueda eliminarse busco todos sus padres y lo inserto en un arreglo
                            para no eliminarlos*/
                            $padres = array();
                            $objEnt = $this->em->getRepository($params['bundle'] . $params['tabla'])->find($valObj);
                            $padresObj = $this->getObjPadres($objEnt, $padres, $params);
                            $elementosPadresNoEliminar = array_merge($elementosPadresNoEliminar, $padresObj);
                            $padresObj = array();
                        }
                    }
                }

                if (!$enUsoParcial) {
                    try {
                        $params['hijo'] = $idpadre;
                        $this->consultaEliminarObj($params);
                    } catch (ForeignKeyConstraintViolationException $e) {
                        $enUso = true;
                        if (count($hijosObj) == $cantHijosElim) {
                            $objPadre = $this->em->getRepository($params['bundle'] . $params['tabla'])->find($idpadre);
                            $objPadre->setHoja(1);
                            $this->em->persist($objPadre);
                        }
                    }
                }

            } else {
                try {
                    $params['hijo'] = $params['valor'][0];
                    $this->consultaEliminarObj($params);
                } catch (ForeignKeyConstraintViolationException $e) {
                    $enUso = true;
                }
            }
        }

        if ($enUso) {
            $msg = 'error';
        } else {
            $msg = 'exito';
        }

        if (!$enUso && count($params['valor']) > 1) {
            $mensaje = "Se eliminaron satisfactoriamente " . $params['nomenclador'] . " ";
        } else if (!$enUso && count($params['valor']) == 1) {
            $mensaje = "Se eliminó satisfactoriamente " . $params['nomenclador'] . " ";
        }

        return array('msg' => $msg, 'mensaje' => $mensaje, 'enUso' => $enUso);
    }

    public function consultaEliminarObj($params)
    {
        $params['bundle'] = isset($params['bundle']) ? $params['bundle'] : "NomencladorBundle:";
        $objElim = $this->em->getRepository($params['bundle'] . $params['tabla'])->find($params['hijo']);
       if(!is_null($objElim)) {
           $this->em->remove($objElim);
           $this->em->flush();
       }
    }

    public function getObjPadres($obj, $padres, $params = null)
    {
        $params['bundle'] = isset($params['bundle']) ? $params['bundle'] : "NomencladorBundle:";
        if ($obj->getIdpadre() == 0) {
            return $padres;
        } else {
            $dqlhijos = 'SELECT g
            FROM ' .$params['bundle'] . $params['tabla'] . ' g
            WHERE   g.' . $params['campoId'] . ' = ' . $obj->getIdpadre();

            $consulta = $this->em->createQuery($dqlhijos);
            $padre = $consulta->getResult();
            $padres[] = $obj->getIdpadre();
            return $padres = $this->getObjPadres($padre[0], $padres, $params);
        }
    }

    public function getAliasMoneda($moneda)
    {
        $dql = 'SELECT g.alias
            FROM NomencladorBundle:NomMonedadestino g
            WHERE   g.id = ' . $moneda;
        $consulta = $this->em->createQuery($dql);
        return $consulta->getSingleResult();
    }

}