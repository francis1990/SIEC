<?php

/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 12/21/2017
 * Time: 2:27 PM
 */

namespace ReporteBundle\Util;

use EnumsBundle\Entity\EnumLetras;
use EnumsBundle\Entity\EnumMeses;
use NomencladorBundle\Entity\ComunRepository;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Repository\NomMonedadestinoRepository;

class DatosReportes
{
    public function __construct()
    {
        $this->em = $GLOBALS['kernel']->getContainer()->get('doctrine');
        $this->pro = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomproducto');
        $this->ser = $GLOBALS['kernel']->getContainer()->get('reporte.services');
        $this->ueb = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomueb');
        $this->gru = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomgrupointeres');
        $this->ase = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomaseguramiento');
        $this->pre = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomprecio');
        $this->port = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomportador');
        $this->ent = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomentidad');
        $this->partediarioconsaseg = $GLOBALS['kernel']->getContainer()->get('parte_diario.dat_consumo_aseguramiento');
        $this->nomencladores = $GLOBALS['kernel']->getContainer()->get('nomencladores');
        $this->datresportes = new DatReportes();
    }

    static public function CrearFormularioReporte($fomBuilder)
    {

        $form = $fomBuilder
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha de cierre:',
                'required' => false,
                'attr' => ['class' => 'form-control js-datepicker read', 'placeholder' => 'Fecha'],
                'html5' => false,
            ])
            ->add('moneda', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Moneda/Destino:',
                'required' => false,
                'class' => 'NomencladorBundle:NomMonedadestino',
                'placeholder' => 'Total',
                'query_builder' => function (NomMonedadestinoRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.nombre', 'ASC');
                },
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('tipoplan', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Tipo plan:',
                'required' => false,
                'class' => 'NomencladorBundle:NomTipoplan',
                'placeholder' => 'Seleccione...',
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.nombre', 'ASC');
                },
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('ufvalor', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'label' => 'UF/Valor:',
                'choices' => array('Unidades físicas', 'Valor'),
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => false
            ))
            ->add('ueb', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UEB:',
                'required' => false,
                'class' => 'NomencladorBundle:NomUeb',
                'placeholder' => 'Seleccione...',
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.nombre', 'ASC');
                },
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('ceros', 'checkbox', array(
                'label' => 'Mostrar ceros:',
                'required' => false,
            ))
            ->add('show_hijos', 'checkbox', array(
                'label' => 'Hijos:',
                'required' => false,
            ))
            ->add('arbolizq', 'hidden')
            ->add('arbolder', 'hidden')
            ->add('nombreReporte', 'hidden')
            ->add('idReporte', 'hidden')
            ->add('monedaNombre', 'hidden')
            ->add('tipoplanNombre', 'hidden')
            ->add('tipoumNombre', 'hidden')
            ->add('ufvalorNombre', 'hidden')
            ->add('existeArbolUeb', 'hidden')
            ->getForm();

        return $form;
    }

    /*Este encabezado es comun entre los reportes 9, 10, 11*/
    static public function encabezadoComun($params)
    {
        $nameAcumulado = 'Acumulado';
        if (!isset($params['nombreMes'])) {
            $objEnumMes = new EnumMeses();
            $mes = $objEnumMes->convertfecha($params['fecha']);
            $nameMes = $objEnumMes->obtenerMesDadoIndice($mes['m']);
        } else {
            $nameMes = $params['nombreMes'];
        }
        if (isset($params['nombreAcumuldo'])) {
            $nameAcumulado = $params['nombreAcumuldo'];
        }
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto/Destino',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'ancho' => 60,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => $nameMes,
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => $nameAcumulado,
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );

        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Dif',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );

        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 9,
            'nombre' => 'Dif',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['ultimaColPintada'] = 9;
        return $params;
    }

    /*Generar el reporte de los Portadores*/
    private function generarDatosPortadores($params)
    {
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $contadorFila = 8;
        $arrayport = $this->port->getPortadoresByIds($params['arbolizq_array']);
        $cantFilaNoPint = 0;
        $cantPortUeb = 0;
        if ($params['empresa']) {
            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => 'Empresa',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 60,
                'negrita' => true
            );
            $contadorFila++;
            foreach ($arrayport as $valuePort) {
                $params['idportador'] = $valuePort->getIdportador();
                $params['idueb'] = null;
                $params['idum'] = $valuePort->getIdunidadmedida()->getIdunidadmedida();
                $params['tablaPlan'] = 'DatPlanPortador';
                $params['acumulado'] = 4;
                $realDia = $this->em->getRepository('ParteDiarioBundle:DatPartePortador')->calcularNivelProd($params);
                $planMes = $this->ser->getPlanMes($params);
                $params['acumulado'] = 3;
                $planDia = $this->ser->getPlanMes($params);
                $diaActual = EnumMeses::convertfecha($params['fecha']);
                $planHF = $planDia * $diaActual['d'];
                $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
                $params['acumulado'] = 1;
                $realHF = $this->em->getRepository('ParteDiarioBundle:DatPartePortador')->calcularNivelProd($params);
                $formato = 3;
                if (!is_null($valuePort->getIdunidadmedida())) {
                    $formato = $valuePort->getIdunidadmedida()->getCantdecimal() != null ? $valuePort->getIdunidadmedida()->getCantdecimal() : 3;
                }
                if ((!isset($params['ceros']) && ($planDia != 0 || $realDia != 0 || $planMes != 0 || $planHF != 0 || $realHF != 0)) || (isset($params['ceros']))) {
                    $params['cuerpoTabla'][] = array(
                        'col' => 0,
                        'valor' => $valuePort->getNombre(),
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 1,
                        'valor' => $valuePort->getIdunidadmedida()->getAbreviatura(),
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'centrar' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 2,
                        'valor' => $planDia,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 3,
                        'valor' => $realDia,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 4,
                        'valor' => '=IF(C' . $contadorFila . ' = 0, 0,((D' . $contadorFila . '*100)/C' . $contadorFila . '))',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => 0,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 5,
                        'valor' => '=C' . $contadorFila . '-D' . $contadorFila,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 6,
                        'valor' => $planMes,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 7,
                        'valor' => $planHF,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 8,
                        'valor' => $realHF,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 9,
                        'valor' => '=IF(H' . $contadorFila . ' = 0, 0,((I' . $contadorFila . '*100)/H' . $contadorFila . '))',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => 0,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 10,
                        'valor' => '=H' . $contadorFila . '-I' . $contadorFila,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $contadorFila++;
                } else {
                    $cantFilaNoPint++;
                }
            }
        } else {
            $arrayueb = $this->ueb->findByUebs($params);
            foreach ($arrayueb as $indexUeb => $valueUeb) {
                if (($cantFilaNoPint == $cantPortUeb) && $indexUeb > 0) {
                    array_splice($params['cuerpoTabla'], -1);
                    $contadorFila--;
                }
                $cantFilaNoPint = 0;
                $cantPortUeb = 0;

                $params['cuerpoTabla'][] = array(
                    'col' => 0,
                    'valor' => $valueUeb['nombre'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFila,
                    'ancho' => 60,
                    'negrita' => true
                );
                $contadorFila++;
                foreach ($arrayport as $valuePort) {
                    $formato = 3;
                    if (!is_null($valuePort->getIdunidadmedida())) {
                        $formato = $valuePort->getIdunidadmedida()->getCantdecimal() != null ? $valuePort->getIdunidadmedida()->getCantdecimal() : 3;
                    }

                    $params['idportador'] = $valuePort->getIdportador();
                    $params['idueb'] = $valueUeb['idueb'];
                    $params['idum'] = $valuePort->getIdunidadmedida()->getIdunidadmedida();
                    $params['tablaPlan'] = 'DatPlanPortador';
                    $params['acumulado'] = 4;
                    $realDia = $this->em->getRepository('ParteDiarioBundle:DatPartePortador')->calcularNivelProd($params);
                    $planMes = $this->ser->getPlanMes($params);
                    $params['acumulado'] = 3;
                    $planDia = $this->ser->getPlanMes($params);
                    $diaActual = EnumMeses::convertfecha($params['fecha']);
                    $planHF = $planDia * $diaActual['d'];
                    $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
                    $params['acumulado'] = 1;
                    $realHF = $this->em->getRepository('ParteDiarioBundle:DatPartePortador')->calcularNivelProd($params);

                    if ((!isset($params['ceros']) && ($planDia != 0 || $realDia != 0 || $planMes != 0 || $planHF != 0 || $realHF != 0)) || (isset($params['ceros']))) {
                        $params['cuerpoTabla'][] = array(
                            'col' => 0,
                            'valor' => $valuePort->getNombre(),
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 1,
                            'valor' => $valuePort->getIdunidadmedida()->getAbreviatura(),
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'centrar' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 2,
                            'valor' => $planDia,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => $formato,
                            'derecha' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 3,
                            'valor' => $realDia,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => $formato,
                            'derecha' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 4,
                            'valor' => '=IF(C' . $contadorFila . ' = 0, 0,((D' . $contadorFila . '*100)/C' . $contadorFila . '))',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => 0,
                            'derecha' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 5,
                            'valor' => '=C' . $contadorFila . '-D' . $contadorFila,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => $formato,
                            'derecha' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 6,
                            'valor' => $planMes,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => $formato,
                            'derecha' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 7,
                            'valor' => $planHF,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => $formato,
                            'derecha' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 8,
                            'valor' => $realHF,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => $formato,
                            'derecha' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 9,
                            'valor' => '=IF(H' . $contadorFila . ' = 0, 0,((I' . $contadorFila . '*100)/H' . $contadorFila . '))',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => 0,
                            'derecha' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 10,
                            'valor' => '=H' . $contadorFila . '-I' . $contadorFila,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'formato' => $formato,
                            'derecha' => true
                        );
                        $contadorFila++;
                    } else {
                        $cantFilaNoPint++;
                    }
                    $cantPortUeb++;
                }
            }
        }

        if (($cantFilaNoPint == $cantPortUeb) && !isset($params['ceros'])) {
            array_splice($params['cuerpoTabla'], -1);
            $contadorFila--;
        }

        $params['ultimaFilaPintada'] = $contadorFila;
        return $params;
    }

    public function generarExcelPortadores($params)
    {
        $params = self::generarDatosPortadores($params);
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Portador',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'ancho' => 10,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Hoy',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Mes HF',
            'mergeRow' => 0,
            'mergeCell' => 5,
            'fila' => 6,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Plan MES',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => 'Plan HF',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 9,
            'nombre' => '% HF.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 10,
            'nombre' => 'Dif. HF',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            "pintar" => true,
            'centrar' => true
        );
        $params['ultimaColPintada'] = 10;
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    /*Generar el reporte de la Produccion por UEB*/
    public function generarDatosProduccionUeb($params)
    {
        $params['em'] = $this->em;

        if ($params['show_hijos']) {
            $arraypro = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $arraypro = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        if (!$params['empresa']) {
            $arrayueb = $this->ueb->findByUebs($params);
        }
        $params['em'] = $this->em;
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $contadorFila = 8;
        $tieneProduc = false;
        $prodPintados = array();
        $params['tablaPlan'] = 'DatPlanProduccion';
        if (!$params['empresa']) {
            foreach ($arrayueb as $indexUeb => $valueUeb) {
                if (!$tieneProduc) {
                    array_splice($params['cuerpoTabla'], -10);
                }
                $tieneProduc = false;
                $params['idueb'] = $valueUeb['idueb'];
                $contadorFilaUeb = $contadorFila;
                $contadorFila++;
                $sumas = array();
                //dump($arraypro);die;
                foreach ($arraypro as $valueProd) {
                    $formato = 3;
                    if (!is_null($valueProd->getUmOperativa())) {
                        $formato = $valueProd->getUmOperativa()->getCantdecimal() != null ? $valueProd->getUmOperativa()->getCantdecimal() : 3;
                    }
                    $padre = $this->em->getRepository('NomencladorBundle:NomProducto')->find($valueProd->getIdproducto());
                    if ($padre->getIdPadre() != null) {
                        $padres = array();
                        $misPadres = $this->pro->getProductosPadres($valueProd, $padres, $params);
                    }

                    /*Aqui verifico que si el producto tiene padre, este ya no este pintado*/
                    if (!Util::existePadre($prodPintados, $misPadres['id']) || $params['show_hijos']) {
                        if ($valueProd->getNivel() != 0) {
                            $params['idum'] = $valueProd->getUmOperativa() != null ? $valueProd->getUmOperativa()->getIdunidadmedida() : '';
                        } else {
                            $params['idum'] = "";
                        }
                        if (!$valueProd->getHoja()) {
                            $hijos = array();
                            $params['idproducto'] = $this->pro->getProductosHijos($valueProd, $hijos);
                            $params['idproducto'][count($params['idproducto'])] = $valueProd->getIdproducto();
                        } else {
                            $params['idproducto'] = $valueProd->getIdproducto();
                        }

                        $params['acumulado'] = 4;
                        $planMes = $this->ser->getPlanMes($params);
                        $params['acumulado'] = 1;
                        $planAno = $this->ser->getPlanMes($params);
                        $params['acumulado'] = 3;
                        $realMes = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params);
                        $params['acumulado'] = 2;
                        $realAnnoHF = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params);
                        if ((!isset($params['ceros']) && ($realMes != 0 || $planMes != 0 || $planAno != 0 || $realAnnoHF != 0)) || (isset($params['ceros']))) {
                            $params['cuerpoTabla'][] = array(
                                'col' => 0,
                                'valor' => $valueProd->getNombre(),
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila
                            );
                            $params['cuerpoTabla'][] = array(
                                'col' => 1,
                                'valor' => $valueProd->getUmOperativa() != null ? $valueProd->getUmOperativa()->getAbreviatura() : '-',
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila
                            );
                            $sumas['mesPlan'] .= 'C' . $contadorFila . '+';
                            $sumas['realMes'] .= 'D' . $contadorFila . '+';
                            $sumas['planAno'] .= 'G' . $contadorFila . '+';
                            $sumas['realAnnoHF'] .= 'H' . $contadorFila . '+';
                            $params['cuerpoTabla'][] = array(
                                'col' => 2,
                                'valor' => $planMes,
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => $formato
                            );
                            $params['cuerpoTabla'][] = array(
                                'col' => 3,
                                'valor' => $realMes,
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => $formato
                            );
                            $params['cuerpoTabla'][] = array(
                                'col' => 4,
                                'valor' => '=IF(C' . $contadorFila . ' = 0, 0,((D' . $contadorFila . '*100)/C' . $contadorFila . '))',
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => 0
                            );
                            $params['cuerpoTabla'][] = array(
                                'col' => 5,
                                'valor' => '=C' . $contadorFila . '-D' . $contadorFila,
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => $formato
                            );
                            $params['cuerpoTabla'][] = array(
                                'col' => 6,
                                'valor' => $planAno,
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => $formato
                            );
                            $params['cuerpoTabla'][] = array(
                                'col' => 7,
                                'valor' => $realAnnoHF,
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => $formato
                            );
                            $params['cuerpoTabla'][] = array(
                                'col' => 8,
                                'valor' => '=IF(G' . $contadorFila . ' = 0, 0,((H' . $contadorFila . '*100)/G' . $contadorFila . '))',
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => 0
                            );
                            $params['cuerpoTabla'][] = array(
                                'col' => 9,
                                'valor' => '=G' . $contadorFila . '-H' . $contadorFila,
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => $formato
                            );
                            $contadorFila++;
                            $prodPintados[] = $valueProd->getIdProducto();
                            $tieneProduc = true;
                        }
                    }
                }
                $params['cuerpoTabla'][] = array(
                    'col' => 0,
                    'valor' => $valueUeb['nombre'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true
                );
                if ($contadorFila == 8) {
                    $params['cuerpoTabla'][] = array(
                        'col' => 1,
                        'valor' => 'UM',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFilaUeb,
                        'negrita' => true,
                        'centrar' => true
                    );
                }
                $params['cuerpoTabla'][] = array(
                    'col' => 2,
                    'valor' => "=" . rtrim($sumas['mesPlan'], "+"),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 3,
                    'valor' => "=" . rtrim($sumas['realMes'], "+"),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 4,
                    'valor' => '=IF(C' . $contadorFilaUeb . ' = 0, 0,((D' . $contadorFilaUeb . '*100)/C' . $contadorFilaUeb . '))',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true,
                    'derecha' => true,
                    'formato' => 0
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 5,
                    'valor' => '=C' . $contadorFilaUeb . '-D' . $contadorFilaUeb,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 6,
                    'valor' => "=" . rtrim($sumas['planAno'], "+"),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 7,
                    'valor' => "=" . rtrim($sumas['realAnnoHF'], "+"),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 8,
                    'valor' => '=IF(G' . $contadorFilaUeb . ' = 0, 0,((H' . $contadorFilaUeb . '*100)/G' . $contadorFilaUeb . '))',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true,
                    'derecha' => true,
                    'formato' => 0
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 9,
                    'valor' => '=G' . $contadorFilaUeb . '-H' . $contadorFilaUeb,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFilaUeb,
                    'negrita' => true,
                    'derecha' => true,
                    'formato' => $formato
                );
            }
            if (!$tieneProduc) {
                array_splice($params['cuerpoTabla'], -10);
            }
        } else {

            $sumas = array();
            foreach ($arraypro as $valueProd) {
                $formato = 3;
                if (!is_null($valueProd->getUmOperativa())) {
                    $formato = $valueProd->getUmOperativa()->getCantdecimal() != null ? $valueProd->getUmOperativa()->getCantdecimal() : 3;
                }
                $padre = $this->em->getRepository('NomencladorBundle:NomProducto')->find($valueProd->getIdproducto());
                if ($padre->getIdPadre() != null) {
                    $padres = array();
                    $misPadres = $this->pro->getProductosPadres($valueProd, $padres, $params);
                }
                /*Aqui verifico que si el producto tiene padre, este ya no este pintado*/
                if (!Util::existePadre($prodPintados, $misPadres['id'])) {
                    if ($valueProd->getNivel() != 0) {
                        $params['idum'] = $valueProd->getUmOperativa() != null ? $valueProd->getUmOperativa()->getIdunidadmedida() : '';
                    } else {
                        $params['idum'] = "";
                    }
                    $params['acumulado'] = 4;
                    $planMes = $this->ser->getPlanMes($params);
                    $params['acumulado'] = 1;
                    $planAno = $this->ser->getPlanMes($params);
                    if ($planMes == 0 && $planAno == 0) {
                        if (!$valueProd->getHoja()) {
                            $hijos = array();
                            $params['idproducto'] = $this->pro->getProductosHijos($valueProd, $hijos);
                            $params['idproducto'][count($params['idproducto'])] = $valueProd->getIdproducto();
                        } else {
                            $params['idproducto'] = $valueProd->getIdproducto();
                        }

                        $params['acumulado'] = 4;
                        $planMes = $this->ser->getPlanMes($params);
                        $params['acumulado'] = 1;
                        $planAno = $this->ser->getPlanMes($params);
                    }

                    /*aqui espara obtener los hijos o no del producto ya que en el parte si hace falta capturarlos todos*/
                    if (!$valueProd->getHoja()) {
                        $hijos = array();
                        $params['idproducto'] = $this->pro->getProductosHijos($valueProd, $hijos);
                        $params['idproducto'][count($params['idproducto'])] = $valueProd->getIdproducto();
                    } else {
                        $params['idproducto'] = $valueProd->getIdproducto();
                    }

                    $params['acumulado'] = 3;
                    $realMes = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params);
                    $params['acumulado'] = 2;
                    $realAnnoHF = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params);

                    if ((!isset($params['ceros']) && ($realMes != 0 || $planMes != 0 || $planAno != 0 || $realAnnoHF != 0)) || (isset($params['ceros']))) {

                        $params['cuerpoTabla'][] = array(
                            'col' => 0,
                            'valor' => $valueProd->getNombre(),
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila
                        );
                        if ($valueProd->getUmOperativa() != null) {
                            $params['cuerpoTabla'][] = array(
                                'col' => 1,
                                'valor' => $valueProd->getUmOperativa()->getAbreviatura(),
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila
                            );
                        } else {
                            $params['cuerpoTabla'][] = array(
                                'col' => 1,
                                'valor' => '-',
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila
                            );
                        }
                        $sumas['mesPlan'] .= 'C' . $contadorFila . '+';
                        $sumas['realMes'] .= 'D' . $contadorFila . '+';
                        $sumas['planAno'] .= 'G' . $contadorFila . '+';
                        $sumas['realAnnoHF'] .= 'H' . $contadorFila . '+';
                        $params['cuerpoTabla'][] = array(
                            'col' => 2,
                            'valor' => $planMes,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'derecha' => true,
                            'formato' => $formato
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 3,
                            'valor' => $realMes,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'derecha' => true,
                            'formato' => $formato
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 4,
                            'valor' => '=IF(C' . $contadorFila . ' = 0, 0,((D' . $contadorFila . '*100)/C' . $contadorFila . '))',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'derecha' => true,
                            'formato' => 0
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 5,
                            'valor' => '=C' . $contadorFila . '-D' . $contadorFila,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'derecha' => true,
                            'formato' => $formato
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 6,
                            'valor' => $planAno,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'derecha' => true,
                            'formato' => $formato
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 7,
                            'valor' => $realAnnoHF,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'derecha' => true,
                            'formato' => $formato
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 8,
                            'valor' => '=IF(G' . $contadorFila . ' = 0, 0,((H' . $contadorFila . '*100)/G' . $contadorFila . '))',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'derecha' => true,
                            'formato' => 0
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 9,
                            'valor' => '=G' . $contadorFila . '-H' . $contadorFila,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'derecha' => true,
                            'formato' => $formato
                        );
                        $prodPintados[] = $valueProd->getIdProducto();
                        $tieneProduc = true;
                        $contadorFila++;
                    }
                }
            }

            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => 'Empresa',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true
            );
            if ($contadorFila == 8) {
                $params['cuerpoTabla'][] = array(
                    'col' => 1,
                    'valor' => 'UM',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFila,
                    'negrita' => true,
                    'centrar' => true
                );
            }
            $params['cuerpoTabla'][] = array(
                'col' => 2,
                'valor' => "=" . rtrim($sumas['mesPlan'], "+"),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 3,
                'valor' => "=" . rtrim($sumas['realMes'], "+"),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 4,
                'valor' => '=IF(C' . $contadorFila . ' = 0, 0,((D' . $contadorFila . '*100)/C' . $contadorFila . '))',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true,
                'derecha' => true,
                'formato' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 5,
                'valor' => '=C' . $contadorFila . '-D' . $contadorFila,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 6,
                'valor' => "=" . rtrim($sumas['planAno'], "+"),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 7,
                'valor' => "=" . rtrim($sumas['realAnnoHF'], "+"),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 8,
                'valor' => '=IF(G' . $contadorFila . ' = 0, 0,((H' . $contadorFila . '*100)/G' . $contadorFila . '))',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true,
                'derecha' => true,
                'formato' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 9,
                'valor' => '=G' . $contadorFila . '-H' . $contadorFila,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true,
                'derecha' => true,
                'formato' => $formato
            );
        }
        $params['ultimaFilaPintada'] = $contadorFila;
        return $params;
    }

    public function generarExcelProduccionUeb($params)
    {
        $params = self::generarDatosProduccionUeb($params);
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'UEB/Producto',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Al Cierre',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Acumulado',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan Mes',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Plan Mes',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 9,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );
        $params['ultimaColPintada'] = 9;
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    private function datosProduccionDiaria($pro, $params, $acopio, $tabla, $cantDias)
    {
        $objEnumLe = new EnumLetras();
        $letra1 = $objEnumLe->letraExcel($cantDias + 1);
        $fila = !$acopio ? $params['filaCuerpo'] : $params['filaCuerpoAcopio'];
        $params['acumulado'] = 3;
        if ($pro->getNivel() != 0) {
            $params['idum'] = $pro->getUmOperativa() != null ? $pro->getUmOperativa()->getIdunidadmedida() : '';
        } else {
            $params['idum'] = '';
        }
        if (!array_key_exists($pro->getIdproducto(), $params['productosGlobal'])) {
            if (!$pro->getHoja()) {
                $params['idproducto'] = $this->pro->getDescendientes($pro, false);
            } else {
                $params['idproducto'] = $pro->getIdproducto();
            }
            $params['productosGlobal'][$pro->getIdproducto()] = $params['idproducto'];
        } else {
            $params['idproducto'] = $params['productosGlobal'][$pro->getIdproducto()];
        }
        $tabla['cuerpoTabla'][] = array(
            'col' => 0,
            'valor' => $pro->getNombre(),
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => ($fila),
            'ancho' => 60
        );
        $tabla['cuerpoTabla'][] = array(
            'col' => 1,
            'valor' => $pro->getUmOperativa() != null ?
                $pro->getUmOperativa()->getAbreviatura() : '',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => ($fila),
            'ancho' => 0,
            'centrar' => true
        );
        if ($acopio) {
            $valoresXDia = $this->em->getRepository('ParteDiarioBundle:DatParteAcopio')->calcularNivelProd($params,
                true);
        } else {
            $valoresXDia = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params,
                true);
        }
        $formato = 3;
        if (!is_null($pro->getUmOperativa())) {
            $formato = $pro->getUmOperativa()->getCantdecimal() != null ? $pro->getUmOperativa()->getCantdecimal() : 3;
        }
        $tabla['cuerpoTabla'][] = array(
            'col' => 2,
            'valor' => '=SUM(D' . $fila . ':' . $letra1 . $fila . ')',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => ($fila),
            'ancho' => 0,
            'derecha' => true,
            'formato' => $formato
        );

        foreach ($valoresXDia as $vdia) {
            $tabla['cuerpoTabla'][] = array(
                'col' => $vdia['fecha']->format('j') + 2,
                'valor' => $vdia['cantidad'],
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => ($fila),
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
        }
        if (empty($params['ceros']) && (($acopio && $valoresXDia == 0) || (!$acopio && $valoresXDia == []))) {
            array_splice($tabla['cuerpoTabla'], -3);
            $tabla['eliminado'] = true;
        }
        return $tabla;

    }

    private function datosAcopioDias($params, $ueb = null)
    {
        $cantDias = EnumMeses::diasdelMes($params['fecha']);
        if ($params['show_hijos']) {
            $params['proAcopio'] = [];
            $params['proLacteos'] = [];
            foreach ($params['arbolizq_array'] as $valueProd) {
                $resultAc = $this->pro->getDescendientes($valueProd, false, true, true, true);
                $params['proAcopio'] = array_unique(array_merge($params['proAcopio'], $resultAc));

                $resultLa = $this->pro->getDescendientes($valueProd, false, true, false, true);
                $params['proLacteos'] = array_unique(array_merge($params['proLacteos'], $resultLa));
            }
            //dump($params);die;
        } else {
            $params['proAcopio'] = !isset($params['proAcopio']) ? $this->pro->productosAcopios($params['arbolizq_array'],
                true) : $params['proAcopio'];
            $params['proLacteos'] = !isset($params['proLacteos']) ? $this->pro->productosAcopios($params['arbolizq_array']) : $params['proLacteos'];
        }

        $cont = 1;
        $separacion = count($params['proAcopio']) == 0 ? 0 : 3;
        $params['filaEncabezadoLacteo'] = $params['filaEncabezado'] + count($params['proAcopio']) + $separacion;
        $tbacopio = array();
        $tblacteo = array();
        $nombreueb = array(
            'colInic' => 0,
            'nombre' => $ueb == null ? 'Empresa' : $ueb['nombre'],
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] - 1,
            'pintar' => true
        );
        while ($cont <= $cantDias) {
            if (count($params['proAcopio']) > 0) {
                if ($cont == 1) {
                    $tbacopio['encabezadotabla'] = array(
                        array(
                            'colInic' => 0,
                            'nombre' => 'Producciones',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['filaEncabezado'],
                            'pintar' => true,
                            'negrita' => true,
                            'centrar' => true
                        ),
                        array(
                            'colInic' => 1,
                            'nombre' => 'UM',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['filaEncabezado'],
                            'pintar' => true,
                            'centrar' => true
                        ),
                        array(
                            'colInic' => 2,
                            'nombre' => 'Total',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['filaEncabezado'],
                            'pintar' => true,
                            'negrita' => true,
                            'centrar' => true
                        )
                    );
                }
                $tbacopio['encabezadotabla'][] = array(
                    'colInic' => $cont + 2,
                    'nombre' => $cont,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaEncabezado'],
                    'ancho' => 0,
                    'pintar' => true,
                    'negrita' => true,
                    'centrar' => true
                );
            }
            if (count($params['proLacteos']) > 0) {
                if ($cont == 1) {
                    $tblacteo['encabezadotabla'] = array(
                        array(
                            'colInic' => 0,
                            'nombre' => 'Producciones',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['filaEncabezadoLacteo'],
                            'pintar' => true,
                            'negrita' => true,
                            'centrar' => true
                        ),
                        array(
                            'colInic' => 1,
                            'nombre' => 'UM',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['filaEncabezadoLacteo'],
                            'pintar' => true,
                            'negrita' => true,
                            'centrar' => true
                        ),
                        array(
                            'colInic' => 2,
                            'nombre' => 'Total',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['filaEncabezadoLacteo'],
                            'pintar' => true,
                            'negrita' => true,
                            'centrar' => true
                        )
                    );
                }
                $tblacteo['encabezadotabla'][] = array(
                    'colInic' => $cont + 2,
                    'nombre' => $cont,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaEncabezadoLacteo'],
                    'ancho' => 0,
                    'pintar' => true,
                    'negrita' => true,
                    'centrar' => true
                );
            }
            $cont++;
        }
        $params['filaCuerpoAcopio'] = $params['filaEncabezado'] + 1;
        foreach ($params['proAcopio'] as $index => $acopio) {
            $tbacopio = self::datosProduccionDiaria($acopio, $params, true, $tbacopio, $cantDias);
            if (empty($tbacopio['eliminado'])) {
                $params['filaCuerpoAcopio']++;
            } else {
                array_pop($tbacopio);
            }
        }
        $params['filaCuerpo'] = $params['filaEncabezadoLacteo'] + 1;
        foreach ($params['proLacteos'] as $index => $lacteo) {
            $tblacteo = self::datosProduccionDiaria($lacteo, $params, false, $tblacteo, $cantDias);
            if (empty($tblacteo['eliminado'])) {
                $params['filaCuerpo']++;
            } else {
                array_pop($tblacteo);
            }
        }

        $params['filaEncabezado'] = $params['filaCuerpo'] + 3;
        if (count($tbacopio) > 0) {
            $tbacopio['encabezadotabla'][] = $nombreueb;
            if (count($tbacopio['cuerpoTabla']) > 0) {
                $params['tablas'][] = $tbacopio;
            }
        }
        if (count($tblacteo) > 0) {
            if (count($tbacopio) == 0) {
                $tblacteo['encabezadotabla'][] = $nombreueb;
                if (count($tblacteo['cuerpoTabla']) > 0) {
                    $params['tablas'][] = $tblacteo;
                }
            } else {
                if (count($tblacteo['cuerpoTabla']) > 0) {
                    $params['tablas'][] = $tblacteo;
                }
            }
        }
        return $params;
    }

    /*Generar el reporte del Acopio por dia*/
    public function generarExcelAcopioDias($params)
    {
        $params['filaEncabezado'] = 7;
        $params['filaCuerpo'] = 8;
        $params['filaCuerpoAcopio'] = 8;
        $params['em'] = $this->em;
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fecha'] = $params['fechaCierre'];
        $params['productosGlobal'] = array();
        if ($params['arbolder_array'] == null) {
            $params = self::datosAcopioDias($params, null);
        } else {
            $uebs = $this->ueb->findByUebs($params);
            foreach ($uebs as $ueb) {
                $params['idueb'] = $ueb['idueb'];
                $params = self::datosAcopioDias($params, $ueb);
            }
        }

        $params['ultimaFilaPintada'] = $params['filaCuerpo'];
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    private function datosProduccionMes($params, $arraypro, $meses, $ueb = null)
    {
        $tabla['title'] = array(
            'colInic' => 0,
            'nombre' => $ueb == null ? 'Empresa' : $ueb['nombre'],
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => ($params['filaEncabezado'] - 2),
            'negrita' => true,
            'pintar' => true
        );
        $sinDatos = true;
        foreach ($arraypro as $pro) {
            $params['idproducto'] = array();
            $tabla['encabezadotabla'][] = array(
                'colInic' => 0,
                'nombre' => $pro->getNombre(),
                'mergeRow' => 0,
                'mergeCell' => 3,
                'fila' => ($params['filaEncabezado'] - 1),
                'negrita' => true,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 0,
                'nombre' => 'MES',
                'mergeRow' => 2,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'],
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 1,
                'nombre' => 'UM',
                'mergeRow' => 2,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'],
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 2,
                'nombre' => 'Mes HF',
                'mergeRow' => 0,
                'mergeCell' => 4,
                'fila' => $params['filaEncabezado'],
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 6,
                'nombre' => 'Año HF',
                'mergeRow' => 0,
                'mergeCell' => 4,
                'fila' => $params['filaEncabezado'],
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 2,
                'nombre' => 'Plan Mes',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'] + 1,
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 3,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'] + 1,
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 4,
                'nombre' => '%',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'] + 1,
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 5,
                'nombre' => 'Dif.',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'] + 1,
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 6,
                'nombre' => 'Plan MES',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'] + 1,
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 7,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'] + 1,
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 8,
                'nombre' => '% HF.',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'] + 1,
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            $tabla['encabezadotabla'][] = array(
                'colInic' => 9,
                'nombre' => 'Dif. HF',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['filaEncabezado'] + 1,
                'ancho' => 0,
                'pintar' => true,
                'centrar' => true
            );
            if ($pro->getNIvel() != 0) {
                $params['idum'] = $pro->getUmOperativa() != null ? $pro->getUmOperativa()->getIdunidadmedida() : '';
            } else {
                $params['idum'] = "";
            }
            if (!array_key_exists($pro->getIdproducto(), $params['productosGlobal'])) {
                if (!$pro->getHoja()) {
                    $params['idproducto'] = $this->pro->getDescendientes($pro, false);
                } else {
                    $params['idproducto'] = $pro->getIdproducto();
                }
                $params['productosGlobal'][$pro->getIdproducto()] = $params['idproducto'];
            } else {
                $params['idproducto'] = $params['productosGlobal'][$pro->getIdproducto()];
            }
            if ($pro->getIdgenerico()->getAcopio()) {
                $params['tablaPlan'] = 'DatPlanAcopio';
                $conp = $this->em->getRepository('ParteDiarioBundle:DatPlanAcopio')->existenciaPlan($params, $pro->getIdproducto());
                $repoparte = 'ParteDiarioBundle:DatParteAcopio';
            } else {
                $params['tablaPlan'] = 'DatPlanProduccion';
                $conp = $this->em->getRepository('ParteDiarioBundle:DatPlanProduccion')->existenciaPlan($params, $pro->getIdproducto());
                $repoparte = 'ParteDiarioBundle:DatPartediarioProduccion';
            }
            $formato = 3;
            if (!is_null($pro->getUmOperativa())) {
                $formato = $pro->getUmOperativa()->getCantdecimal() != null ? $pro->getUmOperativa()->getCantdecimal() : 3;
            }
            $contValores = 0;
            $params['filaold'] = $params['filaCuerpo'];
            foreach ($meses as $index => $mes) {
                $params['acumulado'] = 4;
                $params['fecha'] = substr_replace($params['fecha'], '-' . $index, 4, 3);
                $params['fechaPlan'] = $params['fecha'];
                $exisPlan = $conp != null ? $conp[0][strtolower($mes)] : $this->ser->getPlanMes($params);
                $params['acumulado'] = 3;
                $realMes = $this->em->getRepository($repoparte)->calcularNivelProd($params);
                if ($exisPlan == 0) {
                    $params['acumulado'] = 4;
                    $exisPlan = $this->ser->getPlanMes($params);
                }
                if ($exisPlan != 0 || $realMes != 0)
                    $sinDatos = false;
                $contValores++;
                $tabla['cuerpoTabla'][] = array(
                    'col' => 0,
                    'valor' => $mes,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo']
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 1,
                    'valor' => $pro->getUmOperativa() != null ? $pro->getUmOperativa()->getAbreviatura() : '',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo']
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 2,
                    'valor' => $exisPlan,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'formato' => $formato
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 3,
                    'valor' => $realMes,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'formato' => $formato,
                    'derecha' => true
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 4,
                    'valor' => '=IF(C' . $params['filaCuerpo'] . '=0,0,((D' . $params['filaCuerpo'] . '*100)/C' . $params['filaCuerpo'] . "))",
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'formato' => 0,
                    'derecha' => true
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 5,
                    'valor' => '=(D' . $params['filaCuerpo'] . '-C' . $params['filaCuerpo'] . ')',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'formato' => $formato,
                    'derecha' => true
                );
                $valor = ($mes == "Enero") ? '=C' . $params['filaCuerpo'] : '=(C' . $params['filaCuerpo'] . '+G' . ($params['filaCuerpo'] - 1) . ")";
                $valor1 = ($mes == "Enero") ? '=D' . $params['filaCuerpo'] : '=(D' . $params['filaCuerpo'] . '+H' . ($params['filaCuerpo'] - 1) . ")";
                $tabla['cuerpoTabla'][] = array(
                    'col' => 6,
                    'valor' => $valor,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'formato' => $formato,
                    'derecha' => true
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 7,
                    'valor' => $valor1,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'formato' => $formato,
                    'derecha' => true
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 8,
                    'valor' => '=IF(G' . $params['filaCuerpo'] . '=0,0,((H' . $params['filaCuerpo'] . '*100)/G' . $params['filaCuerpo'] . "))",
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'formato' => 0,
                    'derecha' => true
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 9,
                    'valor' => '=(H' . $params['filaCuerpo'] . '-G' . $params['filaCuerpo'] . ')',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'formato' => $formato,
                    'derecha' => true
                );
                $params['filaCuerpo']++;
                if ($index == EnumMeses::mesFecha($params['fechaCierre'])) {
                    break;
                }
            }
            if (isset($tabla['cuerpoTabla']) && (isset($params['ceros']) || !$sinDatos)) {
                $params['filaEncabezado'] = $params['filaCuerpo'] + 6;
                $params['filaCuerpo'] = $params['filaEncabezado'] + 2;
                $params['tablas'][] = $tabla;
            } else
                $params['filaCuerpo'] = $params['filaold'];

            $tabla = array();
        }
        return $params;
    }

    /*Generar el reporte del Acopio por Mes*/
    public function generarDatosAcopioMes($params)
    {
        $params['filaEncabezado'] = 7;
        $params['filaCuerpo'] = 9;
        $params['em'] = $this->em;
        $fecha = $params['fecha'];
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        if ($params['show_hijos']) {
            $arraypro = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $arraypro = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $enum = new EnumMeses();
        $meses = $enum->getMeses();
        $params['productosGlobal'] = array();
        if ($params['arbolder_array'] == null) {
            $params['idueb'] = null;
            $params = self::datosProduccionMes($params, $arraypro, $meses);
        } else {
            $uebs = $this->ueb->findByUebs($params);
            foreach ($uebs as $ueb) {
                $params['idueb'] = $ueb['idueb'];
                $params = self::datosProduccionMes($params, $arraypro, $meses, $ueb);
            }

        }
        $params['ultimaFilaPintada'] = $params['filaCuerpo'] - 8;
        $params['ultimaColPintada'] = 9;
        $params['fecha'] = $fecha;
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    /*Generar el reporte de los Aseguramientos*/
    public function generarDatosAseguramiento($params)
    {
        $params['filaEncabezado'] = 6;
        $params['filaCuerpo'] = 8;
        $params['acumulado'] = 0;
        if ($params['show_hijos']) {
            $params['asegs'] = $this->ase->getAseguramientosHijosByAsegu($params['arbolizq_array']);
        } else {
            $params['asegs'] = $this->ase->findByIds($params);
        }
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);

        if (!$params['empresa']) {
            $uebs = $this->ueb->findByUebs($params);
            foreach ($uebs as $ueb) {
                $params['uebNombre'] = $ueb['nombre'];
                $params['idueb'] = $ueb['idueb'];
                $params = self::generarDatosAseguramientoAux($params);
            }
        } else {
            $params = self::generarDatosAseguramientoAux($params);
        }

        $params['ultimaFilaPintada'] = $params['filaCuerpo'] - 8;
        $params['ultimaColPintada'] = 13;
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    private function generarDatosAseguramientoAux($params)
    {
        $tabla = array();
        if (!$params['empresa']) {
            $tabla['title'] = array(
                'colInic' => 0,
                'nombre' => $params['uebNombre'],
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => ($params['filaEncabezado'] - 1),
                'negrita' => true,
                'pintar' => true,
                'centrar' => true
            );
        } else {
            $tabla['title'] = array(
                'colInic' => 0,
                'nombre' => 'Empresa',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => ($params['filaEncabezado'] - 1),
                'negrita' => true,
                'pintar' => true,
                'centrar' => true
            );
        }
        $tabla['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Aseguramiento',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'],
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'],
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Mes',
            'mergeRow' => 0,
            'mergeCell' => 6,
            'fila' => $params['filaEncabezado'],
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => 'Año HF',
            'mergeRow' => 0,
            'mergeCell' => 6,
            'fila' => $params['filaEncabezado'],
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Nivel',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'NC',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => 'Ind.Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Con. s/n',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Con. Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => 'Dif',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => 'Nivel',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 9,
            'nombre' => 'NC',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 10,
            'nombre' => 'Ind.Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 11,
            'nombre' => 'Con. s/n',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 12,
            'nombre' => 'Con. Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $tabla['encabezadotabla'][] = array(
            'colInic' => 13,
            'nombre' => 'Dif',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        foreach ($params['asegs'] as $aseg) {
            $formato = 3;
            if (!is_null($aseg->getIdunidadmedida())) {
                $formato = $aseg->getIdunidadmedida()->getCantdecimal() != null ? $aseg->getIdunidadmedida()->getCantdecimal() : 3;
            }
            $params['idaseguramiento'] = array();
            if (!$aseg->getHoja()) {
                $hijas = array();
                $params['idaseguramiento'] = $this->ase->getAseguramientoHijas($aseg, $hijas, null, false, false);
            } else {
                $params['idaseguramiento'][0] = $aseg->getIdaseguramiento();
            }
            $params['acumulado'] = 0;
            $prods = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->findProductosByAseguramiento($params);
            if (count($prods) > 0) {
                $tabla['cuerpoTabla'][] = array(
                    'col' => 0,
                    'valor' => $aseg->getNombre(),
                    'mergeRow' => 2,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 20,
                    'negrita' => true
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 1,
                    'valor' => $aseg->getIdunidadmedida()->getAbreviatura(),
                    'mergeRow' => 2,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0,
                    'negrita' => true,
                    'centrar' => true
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 2,
                    'valor' => '',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 3,
                    'valor' => '',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 4,
                    'valor' => '',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 5,
                    'valor' => (count($prods) == 1 ? '=F' . ($params['filaCuerpo'] + 1) : '=SUM(F' . ($params['filaCuerpo'] + 1) . ':F' . ($params['filaCuerpo'] + count($prods)) . ')'),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => $formato
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 6,
                    'valor' => (count($prods) == 1 ? '=G' . ($params['filaCuerpo'] + 1) : '=SUM(G' . ($params['filaCuerpo'] + 1) . ':G' . ($params['filaCuerpo'] + count($prods)) . ')'),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => $formato
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 7,
                    'valor' => (count($prods) == 1 ? '=H' . ($params['filaCuerpo'] + 1) : '=SUM(H' . ($params['filaCuerpo'] + 1) . ':H' . ($params['filaCuerpo'] + count($prods)) . ')'),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => $formato
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 8,
                    'valor' => '',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 9,
                    'valor' => '',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 10,
                    'valor' => '',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 11,
                    'valor' => (count($prods) == 1 ? '=L' . ($params['filaCuerpo'] + 1) : '=SUM(L' . ($params['filaCuerpo'] + 1) . ':L' . ($params['filaCuerpo'] + count($prods)) . ')'),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => $formato
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 12,
                    'valor' => (count($prods) == 1 ? '=M' . ($params['filaCuerpo'] + 1) : '=SUM(F' . ($params['filaCuerpo'] + 1) . ':M' . ($params['filaCuerpo'] + count($prods)) . ')'),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => $formato
                );
                $tabla['cuerpoTabla'][] = array(
                    'col' => 13,
                    'valor' => (count($prods) == 1 ? '=N' . ($params['filaCuerpo'] + 1) : '=SUM(N' . ($params['filaCuerpo'] + 1) . ':N' . ($params['filaCuerpo'] + count($prods)) . ')'),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['filaCuerpo']++;
                foreach ($prods as $pro) {
                    $producto = $pro[0]->getProducto();
                    $formatoP = 3;
                    if (!is_null($producto->getUmOperativa())) {
                        $formatoP = $producto->getUmOperativa()->getCantdecimal() != null ? $producto->getUmOperativa()->getCantdecimal() : 3;
                    }
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 5,
                        'valor' => (count($prods) == 1 ? '=F' . ($params['filaCuerpo'] + 1) : '=SUM(F' . ($params['filaCuerpo'] + 1) . ':F' . ($params['filaCuerpo'] + count($prods)) . ')'),
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );

                    if (!$producto->getHoja()) {
                        $hijos = array();
                        $params['idproducto'] =
                            $this->pro->getProductosHijos($producto, $hijos);
                    } else {
                        $params['idproducto'] = $pro['idproducto'];
                    }
                    $params['acumulado'] = 1;
                    $nivel = 0;
                    $nivel = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params);
                    $consumo = 0;
                    $consumo = $this->em->getRepository('ParteDiarioBundle:DatParteDiarioConsAseg')->calcularConsumo($params);
                    $params['acumulado'] = 2;
                    $nivelac = 0;
                    $nivelac = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params);
                    $consumoHF = 0;
                    $consumoHF = $this->em->getRepository('ParteDiarioBundle:DatParteDiarioConsAseg')->calcularConsumo($params);
                    $norma = 0;
                    $norma = $this->em->getRepository('NomencladorBundle:NomNorma')->obtenerCanAsegByProdAseg($params['idproducto'],
                        $params['idaseguramiento']);

                    $params['inicio'] = $norma[0]['norma']->getUmnorma()->getIdunidadmedida();
                    $params['fin'] = $producto->getUmoperativa()->getIdunidadmedida();

                    $tabla['cuerpoTabla'][] = array(
                        'col' => 0,
                        'valor' => $producto->getNombre(),
                        'mergeRow' => 2,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 40
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 1,
                        'valor' => $producto->getUmOperativa() != null ? $producto->getUmOperativa()->getAbreviatura() : '',
                        'mergeRow' => 2,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'centrar' => true
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 2,
                        'valor' => $nivel,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 3,
                        'valor' => (isset($norma[0]['cantaseg'])) ? $norma[0]['cantaseg'] : 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 4,
                        'valor' => '=IF(C' . $params['filaCuerpo'] . '=0,0,G' . $params['filaCuerpo'] . '/C' . $params['filaCuerpo'] . ')',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 5,
                        'valor' => '=D' . $params['filaCuerpo'] . '*C' . $params['filaCuerpo'],
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 6,
                        'valor' => $consumo[0]['cantidad'],
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 7,
                        'valor' => '=G' . $params['filaCuerpo'] . '-F' . $params['filaCuerpo'],
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 8,
                        'valor' => $nivelac,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 9,
                        'valor' => (isset($norma[0]['cantaseg'])) ? $norma[0]['cantaseg'] : 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 10,
                        'valor' => '=IF(I' . $params['filaCuerpo'] . '=0,0,M' . $params['filaCuerpo'] . '/I' . $params['filaCuerpo'] . ')',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 11,
                        'valor' => '=J' . $params['filaCuerpo'] . '*I' . $params['filaCuerpo'],
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 12,
                        'valor' => $consumoHF[0]['cantidad'],
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 13,
                        'valor' => '=M' . $params['filaCuerpo'] . '-L' . $params['filaCuerpo'],
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => $formatoP
                    );
                    $params['filaCuerpo']++;
                }
            } else {
                if (!empty($params['ceros'])) {
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 0,
                        'valor' => $aseg->getNombre(),
                        'mergeRow' => 2,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 20,
                        'negrita' => true
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 1,
                        'valor' => $aseg->getIdunidadmedida()->getAbreviatura(),
                        'mergeRow' => 2,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'negrita' => true,
                        'centrar' => true
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 2,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 3,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 4,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 5,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 6,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 7,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 8,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 9,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 10,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 11,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 12,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $tabla['cuerpoTabla'][] = array(
                        'col' => 13,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['filaCuerpo'],
                        'ancho' => 0,
                        'formato' => $formatoP
                    );
                    $params['filaCuerpo']++;
                }
            }
        }
        $params['filaEncabezado'] = $params['filaCuerpo'] + 6;
        $params['filaCuerpo'] = $params['filaEncabezado'] + 2;
        $params['tablas'][] = $tabla;
        return $params;
    }


    private function generarDatosExistenciaDiariaUebAUX($params)
    {
        $objEnumLe = new EnumLetras();
        $letra = $objEnumLe->letraExcel($params['col']);
        $aseguramientoByUEB = array();
        if (!$params['valueAseg']->getHoja()) {
            $hijos = array();
            $params['hijosAseg'] = $this->ase->getAseguramientoHijas($params['valueAseg'], $hijos, $params);
        } else {
            $params['hijosAseg'][0] = $params['valueAseg']->getIdaseguramiento();
        }
        $existencia = 0;
        $reserva = 0;
        $aseguramientoByUEB = $this->em->getRepository('ParteDiarioBundle:DatParteAseguramiento')->getAseguramientosByUeb($params);
        $existencia = (isset($aseguramientoByUEB[0]['existencia']) && $aseguramientoByUEB[0]['existencia'] != null) ? $aseguramientoByUEB[0]['existencia'] : 0;
        $reserva = (isset($aseguramientoByUEB[0]['reserva']) && $aseguramientoByUEB[0]['reserva'] != null) ? $aseguramientoByUEB[0]['reserva'] : 0;
        if (count($aseguramientoByUEB) > 0 || (isset($params['ceros']) && $params['ceros'] != "")) {
            $params['tieneValor'] = true;
            $params['tieneAlgValor'] = true;
            if (!in_array($params['valueAseg']->getIdaseguramiento(), $params['arrayAsegPintados']) && isset($params['pintoPadre']) && !$params['pintoPadre']) {
                $params['pintoPadre'] = true;
                $params['arrayAsegPintados'][] = $params['valueAseg']->getIdaseguramiento();
                if ($params['indexAseg'] == 0) {
                    if ($params['padreActual'] == null || $params['padreActual'] == "") {
                        $params['padreActual'] = $params['valueAseg']->getIdaseguramiento();
                    }
                    $padre = $this->em->getRepository('NomencladorBundle:NomAseguramiento')->find($params['padreActual']);
                    $params['cuerpoTabla'][] = array(
                        'col' => 0,
                        'valor' => $padre->getNombre(),
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'negrita' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 1,
                        'valor' => $padre->getIdunidadmedida()->getAbreviatura(),
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'negrita' => true
                    );
                    $params['contadorFila']++;
                } else {
                    if (!$params['mismoPadre']) {
                        if ($params['padreActual'] == null) {
                            $params['padreActual'] = $params['valueAseg']->getIdaseguramiento();
                        }
                        $padre = $this->em->getRepository('NomencladorBundle:NomAseguramiento')->find($params['padreActual']);
                        $params['cuerpoTabla'][] = array(
                            'col' => 0,
                            'valor' => $padre->getNombre(),
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['contadorFila'],
                            'negrita' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 1,
                            'valor' => $padre->getIdunidadmedida()->getAbreviatura(),
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['contadorFila'],
                            'negrita' => true
                        );
                        $params['contadorFila']++;
                    }
                }
            }

            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'fila' => $params['contadorFila'],
                'valor' => $params['valueAseg']->getNombre(),
                'mergeRow' => 0,
                'mergeCell' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 1,
                'fila' => $params['contadorFila'],
                'valor' => $params['valueAseg']->getIdunidadmedida()->getAbreviatura(),
                'mergeRow' => 0,
                'mergeCell' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => $params['col'],
                'fila' => $params['contadorFila'],
                'valor' => $existencia,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );

            $params['sumas'] .= $letra . $params['contadorFila'] . "+";
            $params['reserva'] += $reserva;
        } else {
            if (count($aseguramientoByUEB) == 0 && !isset($params['ceros'])) {
                if (!$params['empresa']) {
                    if ($params['contUeb'] == 0 && !$params['pintoPadre'] && !$params['mismoPadre'] && $params['padreActual'] != null) {
                        $padre = $this->em->getRepository('NomencladorBundle:NomAseguramiento')->find($params['padreActual']);
                        $params['cuerpoTabla'][] = array(
                            'col' => 0,
                            'valor' => $padre->getNombre(),
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['contadorFila'],
                            'negrita' => true
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => 1,
                            'valor' => $padre->getIdunidadmedida()->getAbreviatura(),
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['contadorFila'],
                            'negrita' => true
                        );
                        $params['contadorFila']++;
                        $params['pintoPadre'] = true;
                    }
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['col'],
                        'fila' => $params['contadorFila'],
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'formato' => $params['formato']
                    );
                }

            }
        }
        return $params;
    }

    /*Generar el reporte de la Existencia diaria por UEB*/
    private function generarDatosExistenciaDiariaUeb($params)
    {
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['contadorFila'] = 7;
        $params['arrayAsegPintados'] = array();
        $aseguramientos = $this->ase->getAsegByIds($params['arbolizq_array']);
        if ($params['show_hijos']) {
            $aseguramientos = $this->ase->getAseguramientosHijosByAsegu($params['arbolizq_array']);
        }

        $params['padreActual'] = "";
        $params['padreOld'] = "";
        if (!$params['empresa']) {
            $colTotalExisUEB = count($params['uebNombre']) + 2;
            $colReserva = count($params['uebNombre']) + 3;
            $colTotalExisReservUEB = count($params['uebNombre']) + 4;
        } else {
            $colTotalExisUEB = 3;
            $colReserva = 4;
            $colTotalExisReservUEB = 5;
        }

        $params['reserva'] = 0;
        $params['mismoPadre'] = false;
        $params['tieneValor'] = false;
        foreach ($aseguramientos as $indexAseg => $valueAseg) {
            $params['valueAseg'] = $valueAseg;
            $params['indexAseg'] = $indexAseg;
            $params['hijosAseg'] = array();
            $params['formato'] = 3;
            if (!is_null($valueAseg->getIdunidadmedida())) {
                $params['formato'] = $valueAseg->getIdunidadmedida()->getCantdecimal() != null ? $valueAseg->getIdunidadmedida()->getCantdecimal() : 3;
            }
            if ((isset($params['sumas']) && $params['sumas'] != "" && $params['tieneValor']) || ($params['reserva'] != 0)) {
                $params['cuerpoTabla'][] = array(
                    'col' => $colTotalExisUEB,
                    'fila' => $params['contadorFila'],
                    'valor' => "=(" . trim($params['sumas'], "+") . ")",
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'formato' => $params['formato'],
                    'derecha' => true
                );
                $params['cuerpoTabla'][] = array(
                    'col' => $colReserva,
                    'fila' => $params['contadorFila'],
                    'valor' => $params['reserva'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'formato' => $params['formato'],
                    'derecha' => true
                );
                $params['cuerpoTabla'][] = array(
                    'col' => $colTotalExisReservUEB,
                    'fila' => $params['contadorFila'],
                    'valor' => "=(" . trim($params['sumas'], "+") . " + " . $params['reserva'] . ")",
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'formato' => $params['formato'],
                    'derecha' => true
                );
                $params['reserva'] = 0;
                $params['tieneValor'] = false;
                $params['tieneAlgValor'] = false;
                $params['contadorFila']++;
            }
            $params['tieneValor'] = false;
            $params['tieneAlgValor'] = false;
            $params['sumas'] = "";
            $misPadres = array();
            $padres = array();
            $misPadres = $this->ase->getAseguramientoPadres($valueAseg, $padres, $params);
            if (count($misPadres) > 0) {
                $params['padreActual'] = $misPadres['id'][count($misPadres['id']) - 1];
            } else {
                $params['mismoPadre'] = false;
            }
            if ($params['padreActual'] != $params['padreOld']) {
                $params['pintoPadre'] = false;
                $params['mismoPadre'] = false;
                $params['padreOld'] = $params['padreActual'] = $misPadres['id'][count($misPadres['id']) - 1];
            } else {
                if (count($misPadres) > 0) {
                    $params['mismoPadre'] = true;
                    if ($params['tieneAseg']) {
                        $params['contadorFila']++;
                    }
                }
            }
            $params['tieneAseg'] = false;
            if (!$params['empresa']) {
                $params['contUeb'] = 0;
                foreach ($params['uebNombre'] as $indexUeb => $valueUeb) {
                    $valor = explode('-', $valueUeb);
                    $params['idueb'] = $valor[1];
                    $params['col'] = $valor[0];
                    $params = $this->generarDatosExistenciaDiariaUebAUX($params);
                    $params['contUeb']++;
                }

                if (!$params['tieneAlgValor'] && !isset($params['ceros'])) {
                    $valor = count($params['cuerpoTabla']) - count($params['uebNombre']);
                    array_splice($params['cuerpoTabla'], $valor);
                }
            } else {
                $params['col'] = 2;
                $params = $this->generarDatosExistenciaDiariaUebAUX($params);
            }
        }
        if ($params['sumas'] != "" && $params['tieneValor']) {
            $params['cuerpoTabla'][] = array(
                'col' => $colTotalExisUEB,
                'fila' => $params['contadorFila'],
                'valor' => "=(" . trim($params['sumas'], "+") . ")",
                'mergeRow' => 0,
                'mergeCell' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => $colReserva,
                'fila' => $params['contadorFila'],
                'valor' => $params['reserva'],
                'mergeRow' => 0,
                'mergeCell' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => $colTotalExisReservUEB,
                'fila' => $params['contadorFila'],
                'valor' => "=(" . trim($params['sumas'], "+") . " + " . $params['reserva'] . ")",
                'mergeRow' => 0,
                'mergeCell' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
        }
        $params['ultimaFilaPintada'] = $params['contadorFila'];
        return $params;
    }

    public function generarExcelExistenciaDiariaUeb($params)
    {
        $cantPintadas = isset($params['uebNombre']) ? count($params['uebNombre']) + 2 : 2;
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Aseguramiento',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'negrita' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 6,
            'ancho' => 10,
            'pintar' => true,
            'negrita' => true,
            'centrar' => true
        );
        if (!$params['empresa']) {
            foreach ($params['uebNombre'] as $index => $valueUeb) {
                $valor = explode('-', $valueUeb);
                $params['encabezadotabla'][] = array(
                    'colInic' => $valor[0],
                    'nombre' => $index,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => 6,
                    'ancho' => 20,
                    'pintar' => true,
                    'negrita' => true,
                    'centrar' => true
                );
            }
            $params['encabezadotabla'][] = array(
                'colInic' => $cantPintadas,
                'nombre' => 'Total',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 6,
                'ancho' => 20,
                'pintar' => true,
                'negrita' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => $cantPintadas + 1,
                'nombre' => 'Reserva',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 6,
                'ancho' => 20,
                'pintar' => true,
                'negrita' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => $cantPintadas + 2,
                'nombre' => 'Total',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 6,
                'ancho' => 20,
                'pintar' => true,
                'negrita' => true,
                'centrar' => true
            );
            $params['ultimaColPintada'] = $cantPintadas + 2;
        } else {
            $params['encabezadotabla'][] = array(
                'colInic' => $cantPintadas,
                'nombre' => 'Empresa',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 6,
                'ancho' => 20,
                'pintar' => true,
                'negrita' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => $cantPintadas + 1,
                'nombre' => 'Total',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 6,
                'ancho' => 20,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => $cantPintadas + 2,
                'nombre' => 'Reserva',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 6,
                'ancho' => 20,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => $cantPintadas + 3,
                'nombre' => 'Total',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 6,
                'ancho' => 20,
                'pintar' => true,
                'centrar' => true
            );
        }

        $params = self::generarDatosExistenciaDiariaUeb($params);
        $params['monedaNombre'] = "";
        $params['tablas'][] = array(
            'encabezadotabla' => isset($params['encabezadotabla']) ? $params['encabezadotabla'] : array(),
            'cuerpoTabla' => isset($params['cuerpoTabla']) ? $params['cuerpoTabla'] : array()
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    /*Generar el reporte de la Producción por Grupos*/
    private function generarDatosProduccionGrupos($params, $ueb = null)
    {
        $params['filaCuerpo'] = $params['filaEncabezado'] + 2;
        $params['cuerpoTabla'] = array();
        $params['encabezadotabla'] = array();
        $params['title'] = array();
        $params['tablaPlan'] = 'DatPlanProduccion';
        $params['tablaParte'] = 'ParteDiarioBundle:DatPartediarioProduccion';
        $tabla = array();
        $tabla['title'] = array(
            'colInic' => 0,
            'nombre' => $ueb == null ? 'Empresa' : $ueb['nombre'],
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] - 1,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Producciones',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'],
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'],
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Día',
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => $params['filaEncabezado'],
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Mes HF',
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => $params['filaEncabezado'],
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => 'Mes',
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => $params['filaEncabezado'],
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 11,
            'nombre' => 'Acumulado Año',
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => $params['filaEncabezado'],
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => 'Dif',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => 'Dif',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 9,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 10,
            'nombre' => 'Dif',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 11,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 12,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 13,
            'nombre' => 'Dif',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $params['filaEncabezado'] + 1,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params = self::pintarAcumuladoMes($params, $params['filaCuerpo'], false, false, true, 2, 3, 4, -1, -1);
        $params = self::pintarAcumuladoMes($params, $params['filaCuerpo'], false, false, true, 5, 0, 1, -1, -1);
        $params = self::pintarAcumuladoMes($params, $params['filaCuerpo'], false, false, true, 8, 4, 3, -1, -1);
        $params = self::pintarAcumuladoMes($params, $params['filaCuerpo'], false, false, true, 11, 1, 2, 0, 1);

        if (count($params['cuerpoTabla']) > 0) {
            $params['tablas'][] = array(
                'encabezadotabla' => $params['encabezadotabla'],
                'cuerpoTabla' => $params['cuerpoTabla'],
                'title' => $tabla['title']
            );
            $params['filaCuerpo'] = $params['contador'];
            $params['filaEncabezado'] = $params['filaCuerpo'] + 3;
            $params['ultimaFilaPintada'] = $params['contador'] + 2;
        }
        return $params;
    }

    public function generarExcelProduccionGrupos($params)
    {
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['filaEncabezado'] = 6;
        $params['productosGlobal'] = array();
        if ($params['arbolder_array'] == null) {
            $params['idueb'] = null;
            $params = self::generarDatosProduccionGrupos($params);
        } else {
            $uebs = $this->ueb->findByUebs($params);
            foreach ($uebs as $ueb) {
                $params['idueb'] = $ueb['idueb'];
                $params = self::generarDatosProduccionGrupos($params, $ueb);
            }
        }
        $params['ultimaColPintada'] = 13;
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    private function generarDatosPlanesProduccionDestinosAUX($params)
    {
        $params['acumulado'] = 1; //mes
        $params['real'] = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params);
        /*Aki compruebo que el producto solo tiene plan, sino busco la sumatoria de sus hijos.*/
        /*Aki busco el valor del producto para esta moneda sino existe entonces la moneda es Total.*/
        if ($params['ambasMoneda']) {
            $productoVerMon = $this->em->getRepository('ParteDiarioBundle:DatPlanProduccion')->verificarSiProdEsTotal($params);
            if (count($productoVerMon) == 0) {
                $params['monedaD'] = $params['moneda'];
                // $params['moneda'] = "";
            }
        }
        $params['acumulado'] = 4; //mes
        $params['idproducto'] = $params['valueProd']->getIdproducto();
        $params['plan'] = $this->ser->getPlanMes($params);
        if ($params['plan'] === 0) {
            if (!$params['valueProd']->getHoja()) {
                $params['idproducto'] = $this->pro->getDescendientes($params['valueProd'], false);
            }
            $params['plan'] = $this->ser->getPlanMes($params);
        }
        if (($params['plan'] != 0 || $params['real'] != 0) || isset($params['ceros'])) {
            $params['tieneValorProd'] = true;
            $params['tieneValorUeb'] = true;
            $params['cuerpoTabla'][] = array(
                'col' => $params['colPlan'],
                'valor' => $params['plan'],
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => $params['colReal'],
                'valor' => $params['real'],
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => $params['colPorciento'],
                'valor' => '=IF(' . $params['colPlanLetra'] . $params['contadorFila'] . '=0,0,(' . $params['colRealLetra'] . $params['contadorFila'] . '*100)/' . $params['colPlanLetra'] . $params['contadorFila'] . "))",
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'ancho' => 0,
                'formato' => 0,
                'derecha' => true
            );
            $params['sumasPlan'] .= $params['colPlanLetra'] . $params['contadorFila'] . "+";
            $params['sumasReal'] .= $params['colRealLetra'] . $params['contadorFila'] . "+";
        } elseif (($params['plan'] == 0 && $params['real'] == 0) && isset($params['ceros'])) {
            $params['tieneValorProd'] = true;
            $params['tieneValorUeb'] = true;
            $params['cuerpoTabla'][] = array(
                'col' => $params['colPlan'],
                'valor' => 0,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => $params['colReal'],
                'valor' => 0,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => $params['colPorciento'],
                'valor' => 0,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'ancho' => 0,
                'formato' => 0,
                'derecha' => true
            );
            $params['sumasPlan'] .= $params['colPlanLetra'] . $params['contadorFila'] . "+";
            $params['sumasReal'] .= $params['colRealLetra'] . $params['contadorFila'] . "+";
        }
        return $params;
    }

    private function pintarProductos($params)
    {
        foreach ($params['arraypro'] as $indexProd => $valueProd) {
            $params['formato'] = 3;
            if (!is_null($valueProd->getUmOperativa())) {
                $params['formato'] = $valueProd->getUmOperativa()->getCantdecimal() != null ? $valueProd->getUmOperativa()->getCantdecimal() : 3;
            }
            $params['sumasPlan'] = "";
            $params['sumasReal'] = "";
            $params['tieneValorProd'] = false;
            $params['colInic'] = 2;
            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => $valueProd->getNombre(),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'ancho' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 1,
                'valor' => $valueProd->getUmOperativa() != null ? $valueProd->getUmOperativa()->getAbreviatura() : "-",
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'ancho' => 0
            );
            $params['valueProd'] = $valueProd;
            $params['idproducto'] = $valueProd->getIdproducto();
            if ($valueProd->getNivel() != 0) {
                $params['idum'] = $valueProd->getUmOperativa() != null ? $valueProd->getUmOperativa()->getIdunidadmedida() : '';
            } else {
                $params['idum'] = "";
            }
            if ($params['moneda'] != "" && !$params['ambasMoneda']) {
                $valueM = $this->em->getRepository('NomencladorBundle:NomMonedadestino')->find($params['moneda']);
                if (!$params['monedasPintadas']) {
                    $params['encabezadotabla'][] = array(
                        'colInic' => 0,
                        'nombre' => 'Producto',
                        'mergeRow' => 2,
                        'mergeCell' => 0,
                        'fila' => 6,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => 1,
                        'nombre' => 'UM',
                        'mergeRow' => 2,
                        'mergeCell' => 0,
                        'fila' => 6,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'],
                        'nombre' => $valueM->getNombre(),
                        'mergeRow' => 0,
                        'mergeCell' => 3,
                        'fila' => 6,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'],
                        'nombre' => 'Plan Mes',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'] + 1,
                        'nombre' => 'Real',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'] + 2,
                        'nombre' => '%',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                }
                $params['colPlan'] = $params['colInic'];
                $params['colPlanLetra'] = $params['objEnumLe']->letraExcel($params['colPlan']);
                $params['colReal'] = $params['colInic'] + 1;
                $params['colRealLetra'] = $params['objEnumLe']->letraExcel($params['colReal']);
                $params['colPorciento'] = $params['colInic'] + 2;
                $params = $this->generarDatosPlanesProduccionDestinosAUX($params);
                $params['colInic'] += 3;
                if ($params['tieneValorProd']) {
                    $params['colPlan'] = $params['colInic'];
                    $params['colPlanLetra'] = $params['objEnumLe']->letraExcel($params['colPlan']);
                    $params['colReal'] = $params['colInic'] + 1;
                    $params['colRealLetra'] = $params['objEnumLe']->letraExcel($params['colReal']);
                    $params['colPorciento'] = $params['colInic'] + 2;
                    $valorPlan = "=(" . trim($params['sumasPlan'], "+") . ")";
                    $valorReal = "=(" . trim($params['sumasReal'], "+") . ")";
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'],
                        'nombre' => "Total",
                        'mergeRow' => 0,
                        'mergeCell' => 3,
                        'fila' => 6,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'],
                        'nombre' => 'Plan Mes',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'] + 1,
                        'nombre' => 'Real',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'] + 2,
                        'nombre' => '%',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['colPlan'],
                        'valor' => $valorPlan,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'formato' => $params['formato'],
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['colReal'],
                        'valor' => $valorReal,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'formato' => $params['formato'],
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['colPorciento'],
                        'valor' => '=IF(' . $params['colPlanLetra'] . $params['contadorFila'] . '=0,0,(' . $params['colRealLetra'] . $params['contadorFila'] . '*100)/' . $params['colPlanLetra'] . $params['contadorFila'] . "))",
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'formato' => 0,
                        'derecha' => true
                    );
                    $params['contadorFila']++;
                } else {
                    array_splice($params['cuerpoTabla'], -2);
                }
                $params['monedasPintadas'] = true;
            } else {
                $params['ambasMoneda'] = true;
                $params['monedas'] = $this->em->getRepository('NomencladorBundle:NomMonedadestino')->findAll();
                $monedasID = array();
                foreach ($params['monedas'] as $valueMo) {
                    $monedasID[] = $valueMo->getId();
                }
                $params['monedasImp'] = implode(",", $monedasID);
                foreach ($params['monedas'] as $valueM) {
                    $params['plan'] = 0;
                    $params['real'] = 0;
                    $params['moneda'] = $valueM->getId();
                    if (!$params['monedasPintadas']) {
                        $params['encabezadotabla'][] = array(
                            'colInic' => 0,
                            'nombre' => 'Producto',
                            'mergeRow' => 2,
                            'mergeCell' => 0,
                            'fila' => 6,
                            'pintar' => true,
                            'centrar' => true
                        );
                        $params['encabezadotabla'][] = array(
                            'colInic' => 1,
                            'nombre' => 'UM',
                            'mergeRow' => 2,
                            'mergeCell' => 0,
                            'fila' => 6,
                            'ancho' => 0,
                            'pintar' => true,
                            'centrar' => true
                        );
                        $params['encabezadotabla'][] = array(
                            'colInic' => $params['colInic'],
                            'nombre' => $valueM->getNombre(),
                            'mergeRow' => 0,
                            'mergeCell' => 3,
                            'fila' => 6,
                            'ancho' => 0,
                            'pintar' => true,
                            'centrar' => true
                        );
                        $params['encabezadotabla'][] = array(
                            'colInic' => $params['colInic'],
                            'nombre' => 'Plan Mes',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => 7,
                            'ancho' => 0,
                            'pintar' => true,
                            'centrar' => true
                        );
                        $params['encabezadotabla'][] = array(
                            'colInic' => $params['colInic'] + 1,
                            'nombre' => 'Real',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => 7,
                            'ancho' => 0,
                            'pintar' => true,
                            'centrar' => true
                        );
                        $params['encabezadotabla'][] = array(
                            'colInic' => $params['colInic'] + 2,
                            'nombre' => '%',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => 7,
                            'ancho' => 0,
                            'pintar' => true,
                            'centrar' => true
                        );
                    }
                    $params['colPlan'] = $params['colInic'];
                    $params['colPlanLetra'] = $params['objEnumLe']->letraExcel($params['colPlan']);
                    $params['colReal'] = $params['colInic'] + 1;
                    $params['colRealLetra'] = $params['objEnumLe']->letraExcel($params['colReal']);
                    $params['colPorciento'] = $params['colInic'] + 2;
                    $params = $this->generarDatosPlanesProduccionDestinosAUX($params);
                    $params['colInic'] += 3;
                }
                if ($params['tieneValorProd']) {
                    $params['colPlan'] = $params['colInic'];
                    $params['colPlanLetra'] = $params['objEnumLe']->letraExcel($params['colPlan']);
                    $params['colReal'] = $params['colInic'] + 1;
                    $params['colRealLetra'] = $params['objEnumLe']->letraExcel($params['colReal']);
                    $params['colPorciento'] = $params['colInic'] + 2;
                    $valorPlan = "=(" . trim($params['sumasPlan'], "+") . ")";
                    $valorReal = "=(" . trim($params['sumasReal'], "+") . ")";
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'],
                        'nombre' => "Total",
                        'mergeRow' => 0,
                        'mergeCell' => 3,
                        'fila' => 6,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'],
                        'nombre' => 'Plan Mes',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'] + 1,
                        'nombre' => 'Real',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['colInic'] + 2,
                        'nombre' => '%',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'ancho' => 0,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['colPlan'],
                        'valor' => $valorPlan,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'formato' => $params['formato'],
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['colReal'],
                        'valor' => $valorReal,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'formato' => $params['formato'],
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['colPorciento'],
                        'valor' => '=IF(' . $params['colPlanLetra'] . $params['contadorFila'] . '=0,0,(' . $params['colRealLetra'] . $params['contadorFila'] . '*100)/' . $params['colPlanLetra'] . $params['contadorFila'] . "))",
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'formato' => 0,
                        'derecha' => true
                    );
                    $params['contadorFila']++;
                } else {
                    array_splice($params['cuerpoTabla'], -2);
                }
                $params['monedasPintadas'] = true;
            }
        }
        return $params;
    }

    public function generarExcelPlanesProduccionDestinos($params)
    {
        $params['objEnumLe'] = new EnumLetras();
        $params['contadorFila'] = 8;
        if (!$params['empresa']) {
            $params['uebs'] = $this->ueb->findByUebs($params);
        }
        if ($params['show_hijos']) {
            $params['arraypro'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['arraypro'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['tablaPlan'] = 'DatPlanProduccion';
        $params['monedasPintadas'] = false;
        if ($params['uebs'] != null) {
            foreach ($params['uebs'] as $valueUeb) {
                $params['idueb'] = $valueUeb['idueb'];
                $params['cuerpoTabla'][] = array(
                    'col' => 0,
                    'valor' => $valueUeb['nombre'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'negrita' => true
                );
                $params['contadorFila']++;
                $params['tieneValorUeb'] = false;
                $params = self::pintarProductos($params);
                if (!$params['tieneValorUeb']) {
                    array_splice($params['cuerpoTabla'], -1);
                    $params['contadorFila']--;
                }
            }
        } else {
            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => 'Empresa',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true
            );
            $params['contadorFila']++;
            $params['tieneValorUeb'] = false;
            $params = self::pintarProductos($params);
            if (!$params['tieneValorUeb']) {
                array_splice($params['cuerpoTabla'], -1);
            }
        }
        $params['ultimaFilaPintada'] = $params['contadorFila'];
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    /*Cumplimiento con la Canasta Familiar Normada (ORC)*/
    public function generarExcelCanastaFamiliar($params)
    {
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);
        $params['ObjetosHijosEntidades'] = $this->gru->getEntidadesHijasByGrupos($params['ObjetosArbolDer']);
        $params['identidades'] = $params['ObjetosHijosEntidades'];
        $params['ObjetosHijosGrupos'] = $this->gru->getGruposHijosByGrupos($params['ObjetosArbolDer'], $params);
        $contadorFila = 7;
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['cuerpoTabla'] = array();
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['tablaPlan'] = 'DatPlanVenta';
        $params['productosGlobal'] = array();
        $params = self::pintarAcumuladoMes($params, $contadorFila, false, false, true, 2, 4, 1, 0, 1, true, true, false);
        $params['ultimaFilaPintada'] = $params['contador'];
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto/Grupo',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['ultimaColPintada'] = 4;
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    /*Resumen del Parte Diario de Ventas en Moneda Nacional*/
    public function generarExcelParteMonedaNacional($params)
    {
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);
        $params['ObjetosHijosEntidades'] = $this->gru->getEntidadesHijasByGrupos($params['ObjetosArbolDer']);
        $params['ObjetosHijosGrupos'] = $this->gru->getGruposHijosByGrupos($params['ObjetosArbolDer']);
        $contadorFila = 8;
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['cuerpoTabla'] = array();
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['tablaPlan'] = "DatPlanVenta";
        if ($params['ueb'] != "") {
            $params['idueb'] = $params['ueb'];
        }

        $params = self::pintarAcumuladoMes($params, $contadorFila, false, false, false, 2, 4, 1, -1, -1, true);
        $params = self::pintarAcumuladoMes($params, $contadorFila, false, false, false, 6, 1, 2, 0, 1, true);
        $params['nombreMes'] = 'Mes';
        $params['nombreAcumuldo'] = 'Acumulado';
        $params = self::encabezadoComun($params);
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $params['ultimaFilaPintada'] = $params['ultimaFilaPintada'];
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    /*Cumplimiento de las Ventas en MN (Parte DG)*/
    public function generarExcelVentasMonedaNacional($params)
    {
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);
        $params['ObjetosHijosEntidades'] = $this->gru->getEntidadesHijasByGrupos($params['ObjetosArbolDer']);
        $params['ObjetosHijosGrupos'] = $this->gru->getGruposHijosByGrupos($params['ObjetosArbolDer']);
        $contadorFila = 8;
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['cuerpoTabla'] = array();
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['tablaPlan'] = 'DatPlanVenta';
        $params = self::pintarAcumuladoMes($params, $contadorFila, false, false, false, 2, 4, 1, 0, 1, true, true,
            true);
        $params = self::pintarAcumuladoMes($params, $contadorFila, false, false, false, 6, 1, 2, 0, 1, true, true, true
            , 'sumaPlanesTotal', 'sumaRealTotal');
        $params = self::encabezadoComun($params);
        $contadorFila = $params['contador'];
        if (count($params['total']) != 0) {
            $totalNombre = $params['ueb'] != "" ? 'Total ' : 'Total Empresa';
            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => $totalNombre,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 60,
                'negrita' => true,
                'pintar' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 1,
                'valor' => 'UM',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'centrar' => true,
                'pintar' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 2,
                'valor' => '=(' . trim($params['total']['sumaPlanes'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'pintar' => true,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 3,
                'valor' => '=(' . trim($params['total']['sumaReal'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'pintar' => true,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 4,
                'valor' => '=IF(C' . $contadorFila . '=0,0,(D' . $contadorFila . '*100)/C' . $contadorFila . "))",
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'pintar' => true,
                'formato' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 5,
                'valor' => '=(C' . $contadorFila . '-D' . $contadorFila . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'pintar' => true,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 6,
                'valor' => '=(' . trim($params['total']['sumaPlanesTotal'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'pintar' => true,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 7,
                'valor' => '=(' . trim($params['total']['sumaRealTotal'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'pintar' => true,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 8,
                'valor' => '=IF(G' . $contadorFila . '=0,0,(H' . $contadorFila . '*100)/G' . $contadorFila . "))",
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'pintar' => true,
                'formato' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 9,
                'valor' => '=(G' . $contadorFila . '-H' . $contadorFila . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'negrita' => true,
                'pintar' => true,
                'derecha' => true,
                'formato' => $params['formato']
            );

        }
        $params['ultimaFilaPintada'] = $contadorFila;
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    public function generarExcelVentasProductoGrupo($params)
    {
        $filaInicial = 6;
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);
        $params['ObjetosHijosEntidades'] = $this->gru->getEntidadesHijasByGrupos($params['ObjetosArbolDer']);
        $params['ObjetosHijosGrupos'] = $this->gru->getGruposHijosByGrupos($params['ObjetosArbolDer']);
        $contadorFila = 10;
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['cuerpoTabla'] = array();
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['productosGlobal'] = array();
        foreach ($params['ObjetosArbolIzq'] as $index => $value) {
            $formato = 3;
            if (!is_null($value->getUmOperativa())) {
                $formato = $value->getUmOperativa()->getCantdecimal() != null ? $value->getUmOperativa()->getCantdecimal() : 3;
            }
            if (count($params['ObjetosHijosGrupos']) > 0) {
                $params['idgrupo'] = $params['ObjetosHijosGrupos'];
            }
            if (count($params['ObjetosHijosEntidades']) > 0) {
                $params['identidades'] = $params['ObjetosHijosEntidades'];
            }

            if (!array_key_exists($value->getIdproducto(), $params['productosGlobal'])) {
                if (!$value->getHoja()) {
                    $hijos = array();
                    $params['idproducto'] = $this->pro->getDescendientes($value, false);
                } else {
                    $params['idproducto'] = $value->getIdproducto();
                }
                $params['productosGlobal'][$value->getIdproducto()] = $params['idproducto'];
            } else {
                $params['idproducto'] = $params['productosGlobal'][$value->getIdproducto()];
            }

            $params['encabezadotabla'][] = array(
                'colInic' => 2,
                'nombre' => $value->getNombre(),
                'mergeRow' => 0,
                'mergeCell' => 6,
                'fila' => $filaInicial,
                'ancho' => 60,
                'negrita' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 11,
                'nombre' => $value->getNombre() . " (En Valores)",
                'mergeRow' => 0,
                'mergeCell' => 6,
                'fila' => $filaInicial,
                'ancho' => 60,
                'negrita' => true
            );
            $filaInicial++;
            $params['encabezadotabla'][] = array(
                'colInic' => 1,
                'nombre' => 'Plan Año',
                'mergeRow' => 2,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 12,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 2,
                'nombre' => 'Mes',
                'mergeRow' => 0,
                'mergeCell' => 3,
                'fila' => $filaInicial,
                'ancho' => 21,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 5,
                'nombre' => 'Acumulado',
                'mergeRow' => 0,
                'mergeCell' => 3,
                'fila' => $filaInicial,
                'ancho' => 21,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 8,
                'nombre' => 'Real Acum. Mes Ant.',
                'mergeRow' => 2,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 20,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 10,
                'nombre' => 'Plan Año',
                'mergeRow' => 2,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 12,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 11,
                'nombre' => 'Mes',
                'mergeRow' => 0,
                'mergeCell' => 3,
                'fila' => $filaInicial,
                'ancho' => 21,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 14,
                'nombre' => 'Acumulado',
                'mergeRow' => 0,
                'mergeCell' => 3,
                'fila' => $filaInicial,
                'ancho' => 21,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 17,
                'nombre' => 'Real Acum. Mes Ant.',
                'mergeRow' => 2,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 20,
                'pintar' => true,
                'centrar' => true
            );
            $filaInicial++;
            $params['encabezadotabla'][] = array(
                'colInic' => 2,
                'nombre' => 'Plan',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 3,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 4,
                'nombre' => '%',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 11,
                'nombre' => 'Plan',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 12,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 13,
                'nombre' => '%',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 5,
                'nombre' => 'Plan',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 6,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 7,
                'nombre' => '%',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 14,
                'nombre' => 'Plan',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 15,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 16,
                'nombre' => '%',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 7,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 0,
                'nombre' => 'Proveedores',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $filaInicial,
                'ancho' => 30,
                'pintar' => true,
                'centrar' => true
            );
            $filaInicial++;
            if ($params['ueb'] != "") {
                $params['idueb'] = $params['ueb'];
            }
            foreach ($params['ObjetosArbolDer'] as $valueGrup) {
                $realMes = 0;
                $realHF = 0;
                $realMes2 = 0;
                $realHF2 = 0;
                $contCol = 0;
                if ($value->getNivel() != 0) {
                    $params['idum'] = $value->getUmOperativa() != null ? $value->getUmOperativa()->getIdunidadmedida() : '';
                } else {
                    $params['idum'] = "";
                }
                $params['tablaPlan'] = 'DatPlanVenta';
                $hijas = array();
                $params['entidadesHijas'] = array();
                $params['identidades'] = array();


                $params['identidades'] = $this->gru->getEntidadesHijas($valueGrup, $hijas, $params);
                if ($valueGrup->getIdentidad() != null) {
                    $params['identidades'][] = $valueGrup->getIdentidad()->getIdentidad();
                }

                $params['idgrupo'] = "";
                if (!$valueGrup->getHoja()) {
                    $hijos = array();
                    $result = $this->gru->getGruposHijos($valueGrup, $hijos, $params);
                    $params['idgrupo'] = $result;
                } else {
                    $params['idgrupo'] = $valueGrup->getIdgrupointeres();
                }
                $params['ufvalor'] = 0;
                $params['acumulado'] = 1;
                $planEjercicioCompleto = $this->ser->getPlanMes($params);
                $params['acumulado'] = 4;
                $planMes = $this->ser->getPlanMes($params);
                $params['acumulado'] = 6;
                $planAcumulado = $this->ser->getPlanMes($params);

                if (count($params['identidades']) != 0 && $params['identidades'] != null) {
                    $params['acumulado'] = 1;
                    $realMes = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
                    $params['acumulado'] = 2;
                    $realHF = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);

                    $params['ufvalor'] = true;
                    $params['acumulado'] = 1;
                    $realMesValor = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
                    $params['acumulado'] = 2;
                    $realHFValor = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
                    $params['ufvalor'] = false;
                }

                $params['ufvalor'] = 1;
                $params['acumulado'] = 1;
                $planEjercicioCompletoAux = $this->ser->getPlanMes($params);
                $params['acumulado'] = 4;
                $planMesAux = $this->ser->getPlanMes($params);
                $params['acumulado'] = 6;
                $planAcumuladoAux = $this->ser->getPlanMes($params);
                if ((!isset($params['ceros']) && ($planMes != 0 || $realMes != 0)) || (isset($params['ceros']))) {

                    $params['cuerpoTabla'][] = array(
                        'col' => 0,
                        'valor' => '' . $valueGrup->getNombre(),
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 60
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 1,
                        'valor' => $planEjercicioCompleto,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 60,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 2,
                        'valor' => $planMes,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 3,
                        'valor' => $realMes,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 4,
                        'valor' => '=IF(C' . $filaInicial . '=0,0,(D' . $filaInicial . '*100)/C' . $filaInicial . "))",
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => 0,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 5,
                        'valor' => $planAcumulado,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 6,
                        'valor' => $realHF,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 7,
                        'valor' => '=IF(F' . $filaInicial . '=0,0,(G' . $filaInicial . '*100)/F' . $filaInicial . "))",
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => 0,
                        'derecha' => true
                    );


                    $params['cuerpoTabla'][] = array(
                        'col' => 10,
                        'valor' => $planEjercicioCompletoAux,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 60,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 11,
                        'valor' => $planMesAux,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 12,
                        'valor' => $realMesValor,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 13,
                        'valor' => '=IF(L' . $filaInicial . '=0,0,(M' . $filaInicial . '*100)/L' . $filaInicial . "))",
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => 0,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 14,
                        'valor' => $planAcumuladoAux,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 15,
                        'valor' => $realHFValor,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => $formato,
                        'derecha' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 16,
                        'valor' => '=IF(O' . $filaInicial . '=0,0,(P' . $filaInicial . '*100)/O' . $filaInicial . "))",
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $filaInicial,
                        'ancho' => 0,
                        'formato' => 0,
                        'derecha' => true
                    );

                    $filaInicial++;
                }
            }
            $filaInicial = $filaInicial + 4;
        }
        $params['ultimaFilaPintada'] = $filaInicial;
        $params['ultimaColPintada'] = 16;
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    public function generarExcelVentaCucMincin($params)
    {
        $params['contadorFila'] = 10;
        $params['contadorFilaInic'] = 6;
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);

        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['tablaPlan'] = 'DatPlanVenta';
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['idueb'] = $params['ueb'];
        $params['productosGlobal'] = array();
        $params['tieneDatosGrupo'] = false;
        foreach ($params['ObjetosArbolDer'] as $indexGru => $valueGru) {
            $params['tieneDatosGrupo'] = false;
            $params['grupo'] = $valueGru;
            $params['idgrupo'] = $valueGru->getIdgrupointeres();

            $params['encabezadotabla'][] = array(
                'colInic' => 0,
                'nombre' => $valueGru->getNombre(),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'],
                "negrita" => true
            );
            $params['contadorFilaInic']++;
            $params['encabezadotabla'][] = array(
                'colInic' => 0,
                'nombre' => 'Producto',
                'mergeRow' => 3,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'],
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 1,
                'nombre' => 'UM',
                'mergeRow' => 3,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'],
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 2,
                'nombre' => 'Precio',
                'mergeRow' => 3,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'],
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 3,
                'nombre' => 'Mes',
                'mergeRow' => 0,
                'mergeCell' => 6,
                'fila' => $params['contadorFilaInic'],
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 9,
                'nombre' => 'Acumulado ',
                'mergeRow' => 0,
                'mergeCell' => 6,
                'fila' => $params['contadorFilaInic'],
                "pintar" => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 3,
                'nombre' => 'Plan',
                'mergeRow' => 0,
                'mergeCell' => 2,
                'fila' => $params['contadorFilaInic'] + 1,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 5,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 4,
                'fila' => $params['contadorFilaInic'] + 1,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 9,
                'nombre' => 'Plan',
                'mergeRow' => 0,
                'mergeCell' => 2,
                'fila' => $params['contadorFilaInic'] + 1,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 11,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 4,
                'fila' => $params['contadorFilaInic'] + 1,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 3,
                'nombre' => 'UF',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 4,
                'nombre' => 'Valores ',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 5,
                'nombre' => 'UF',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 6,
                'nombre' => 'Valores ',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 7,
                'nombre' => '%',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 8,
                'nombre' => 'Dif.',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 9,
                'nombre' => 'UF',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 10,
                'nombre' => 'Valores ',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 11,
                'nombre' => 'UF',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 12,
                'nombre' => 'Valores ',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 13,
                'nombre' => '%',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            $params['encabezadotabla'][] = array(
                'colInic' => 14,
                'nombre' => 'Dif.',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFilaInic'] + 2,
                "pintar" => true,
                'centrar' => true
            );
            if ($indexGru == 0) {
                $params['contadorFilaInic'] = $params['contadorFila'] + 4;
            } else {
                $params['contadorFilaInic'] = $params['contadorFila'] + 3;
            }
            $hijas = array();
            $params['identidades'] = $this->gru->getEntidadesHijas($valueGru, $hijas);
            $params = self::generarVentaCucMincin($params);

            if ($params['tieneDatosGrupo'] || isset($params['ceros'])) {
                $params['contadorFila'] += 6;
            } else {
                $params['contadorFilaInic'] -= 7;
                array_splice($params['encabezadotabla'], count($params['encabezadotabla']) - 22);
            }
        }

        $params['ultimaColPintada'] = 14;
        $params['ultimaFilaPintada'] = $params['contadorFila'];
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    public
    function generarVentaCucMincin($params)
    {
        $sumas = array();
        $tieneSumas = false;
        $params['idueb'] = $params['ueb'];
        $formato = 3;
        foreach ($params['ObjetosArbolIzq'] as $valueProd) {

            if (!is_null($valueProd->getUmOperativa())) {
                $formato = $valueProd->getUmOperativa()->getCantdecimal() != null ? $valueProd->getUmOperativa()->getCantdecimal() : 3;
            }
            $planMes = 0;
            $planAno = 0;
            $realMes = 0;
            $realAno = 0;
            $params['idproducto'] = "";
            if ($valueProd->getNivel() != 0) {
                $params['idum'] = $valueProd->getUmoperativa() != null ? $valueProd->getUmoperativa()->getIdunidadmedida() : '';
            } else {
                $params['idum'] = "";
            }
            $params['idproducto'] = $valueProd->getIdproducto();


            $precio = 0;
            $precioProd = $this->pre->precioProducto($params, true);

            if (count($precioProd) > 0) {
                if ($params['moneda'] != "") {
                    $monedaAlias = $GLOBALS['kernel']->getContainer()->get('nomencladores')->getAliasMoneda($params['moneda']);
                    if ($monedaAlias = "cup") {
                        $precio = $precioProd['preciomn'];
                    } else {
                        $precio = $precioProd['preciocuc'];
                    }
                } else {
                    $precio = $precioProd['preciomn'] + $precioProd['preciocuc'];
                }
            }

            if (count($params['identidades']) > 0) {
                $params['acumulado'] = 4;
                $planMes = $this->ser->getPlanMes($params);
                $params['acumulado'] = 1;
                $planAno = $this->ser->getPlanMes($params);
            }
            if (!array_key_exists($valueProd->getIdproducto(), $params['productosGlobal'])) {
                if (!$valueProd->getHoja()) {
                    $hijos = array();
                    $params['idproducto'] = $this->pro->getDescendientes($valueProd, false);
                } else {
                    $params['idproducto'] = $valueProd->getIdproducto();
                }
                $params['productosGlobal'][$valueProd->getIdproducto()] = $params['idproducto'];
            } else {
                $params['idproducto'] = $params['productosGlobal'][$valueProd->getIdproducto()];
            }

            if (count($params['identidades']) > 0) {
                /*Aki verifico si para el producto sin sus hijos tiene plan sino busco la suma de sus hijos*/
                if ($planMes == 0) {
                    $params['acumulado'] = 4;
                    $planMes = $this->ser->getPlanMes($params);
                    $params['acumulado'] = 1;
                    $planAno = $this->ser->getPlanMes($params);
                }
                $realMes = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
                $params['acumulado'] = 2; //acumulado año
                $realAno = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
                $misPadres = $this->pro->getProductosPadres($valueProd, array(), $params);
            }
            if ((!isset($params['ceros']) && ($planMes != 0 || $planAno != 0 || $realMes != 0 || $realAno != 0)) || (isset($params['ceros']))) {
                $params['tieneDatosGrupo'] = true;
                if (!Util::existePadre($params['arbolizq_array'], $misPadres['id'])) {
                    $sumas['sumaUFPlanMes'] .= "D" . $params['contadorFila'] . "+";
                    $sumas['sumaValorPlanMes'] .= "E" . $params['contadorFila'] . "+";
                    $sumas['sumaUFRealMes'] .= "F" . $params['contadorFila'] . "+";
                    $sumas['sumaValorRealMes'] .= "G" . $params['contadorFila'] . "+";

                    $sumas['sumaUFPlanAno'] .= "J" . $params['contadorFila'] . "+";
                    $sumas['sumaValorPlanAno'] .= "K" . $params['contadorFila'] . "+";
                    $sumas['sumaUFRealAno'] .= "L" . $params['contadorFila'] . "+";
                    $sumas['sumaValorRealAno'] .= "M" . $params['contadorFila'] . "+";
                    $tieneSumas = true;
                }

                if ($params['ufvalor'] == 0) {
                    $params['cuerpoTabla'][] = array(
                        'col' => 4,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'derecha' => true,
                        'negrita' => false,
                        'formato' => $formato
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 6,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'derecha' => true,
                        'negrita' => false,
                        'formato' => $formato
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 10,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'derecha' => true,
                        'negrita' => false,
                        'formato' => $formato
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 12,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'derecha' => true,
                        'negrita' => false,
                        'formato' => $formato
                    );
                } else {
                    $params['cuerpoTabla'][] = array(
                        'col' => 4,
                        'valor' => '=(C' . $params['contadorFila'] . '*D' . $params['contadorFila'] . ')',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'derecha' => true,
                        'negrita' => false,
                        'formato' => $formato
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 6,
                        'valor' => '=(C' . $params['contadorFila'] . '*F' . $params['contadorFila'] . ')',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'derecha' => true,
                        'negrita' => false,
                        'formato' => $formato
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 10,
                        'valor' => '=(C' . $params['contadorFila'] . '*J' . $params['contadorFila'] . ')',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'derecha' => true,
                        'negrita' => false,
                        'formato' => $formato
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 12,
                        'valor' => '=(C' . $params['contadorFila'] . '*L' . $params['contadorFila'] . ')',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $params['contadorFila'],
                        'ancho' => 0,
                        'derecha' => true,
                        'negrita' => false,
                        'formato' => $formato
                    );
                }

                $params['cuerpoTabla'][] = array(
                    'col' => 0,
                    'valor' => $valueProd->getNombre(),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 60,
                    'negrita' => false,
                    'izquierda' => true
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 1,
                    'valor' => $valueProd->getUmoperativa()->getAbreviatura(),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'centrar' => true,
                    'negrita' => false
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 2,
                    'valor' => $precio,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => 2
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 3,
                    'valor' => $planMes,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 5,
                    'valor' => $realMes,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 7,
                    'valor' => '=IF(E' . $params['contadorFila'] . ' = 0, 0,((G' . $params['contadorFila'] . '*100)/E' . $params['contadorFila'] . '))',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => 0
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 8,
                    'valor' => '=IF(E' . $params['contadorFila'] . ' = 0, 0,((G' . $params['contadorFila'] . '*100)/E' . $params['contadorFila'] . '))',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => 0
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 9,
                    'valor' => $planAno,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 11,
                    'valor' => $realAno,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 13,
                    'valor' => '=IF(K' . $params['contadorFila'] . ' = 0, 0,((M' . $params['contadorFila'] . '*100)/K' . $params['contadorFila'] . '))',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => 0
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 14,
                    'valor' => '=(M' . $params['contadorFila'] . '-K' . $params['contadorFila'] . ')',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['contadorFila'],
                    'ancho' => 0,
                    'derecha' => true,
                    'negrita' => false,
                    'formato' => $formato
                );
                $params['contadorFila']++;
            }
        }

        if ($tieneSumas) {
            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => 'Total',
                'mergeRow' => 0,
                'mergeCell' => 3,
                'fila' => $params['contadorFila'],
                'centrar' => true,
                'negrita' => true,
                'ancho' => 0,
                'izquierda' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 3,
                'valor' => '=(' . trim($sumas['sumaUFPlanMes'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 4,
                'valor' => '=(' . trim($sumas['sumaValorPlanMes'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 5,
                'valor' => '=(' . trim($sumas['sumaUFRealMes'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 6,
                'valor' => '=(' . trim($sumas['sumaValorRealMes'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 7,
                'valor' => '=IF(E' . $params['contadorFila'] . ' = 0, 0,((G' . $params['contadorFila'] . '*100)/E' . $params['contadorFila'] . '))',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 8,
                'valor' => '=IF(E' . $params['contadorFila'] . ' = 0, 0,((G' . $params['contadorFila'] . '*100)/E' . $params['contadorFila'] . '))',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 9,
                'valor' => '=(' . trim($sumas['sumaUFPlanAno'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 10,
                'valor' => '=(' . trim($sumas['sumaValorPlanAno'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 11,
                'valor' => '=(' . trim($sumas['sumaUFRealAno'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 12,
                'valor' => '=(' . trim($sumas['sumaValorRealAno'], '+') . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['cuerpoTabla'][] = array(
                'col' => 13,
                'valor' => '=IF(K' . $params['contadorFila'] . ' = 0, 0,((M' . $params['contadorFila'] . '*100)/K' . $params['contadorFila'] . '))',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => 0
            );
            $params['cuerpoTabla'][] = array(
                'col' => 14,
                'valor' => '=(M' . $params['contadorFila'] . '-K' . $params['contadorFila'] . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $params['contadorFila'],
                'negrita' => true,
                'ancho' => 0,
                'derecha' => true,
                'formato' => $formato
            );
            $params['contadorFila']++;
        }
        return $params;
    }


    public function generarExcelResumenVentasCuc($params)
    {
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        /*$params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);
        $params['ObjetosHijosEntidades'] = $this->gru->getEntidadesHijasByGrupos($params['ObjetosArbolDer']);
        $params['ObjetosHijosGrupos'] = $this->gru->getGruposHijosByGrupos($params['ObjetosArbolDer']);*/
        $contadorFila = 8;
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['cuerpoTabla'] = array();
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['tablaPlan'] = 'DatPlanVenta';
        $params = self::pintarAcumuladoMes($params, $contadorFila, false, false, false, 2, 4, 1, 0, 1, false);
        $params = self::pintarAcumuladoMes($params, $contadorFila, false, false, false, 6, 1, 2, 0, 1, false);
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Mes',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Acumulado',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 9,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['ultimaColPintada'] = 9;
        $contadorFila = $params['contador'] + 3;
        $i = 1;
        $params['encabezadoresumen'][] = array(
            'colInic' => 0,
            'nombre' => 'Destinos',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $contadorFila,
            'pintar' => true
        );
        foreach ($params['ObjetosArbolIzq'] as $indexProd => $valueProd) {
            $params['encabezadoresumen'][] = array(
                'colInic' => $i,
                'nombre' => $valueProd->getnombre(),
                'mergeRow' => 0,
                'mergeCell' => 2,
                'fila' => $contadorFila,
                'pintar' => true
            );
            $params['encabezadoresumen'][] = array(
                'colInic' => $i,
                'nombre' => 'Plan',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila + 1,
                'pintar' => true
            );
            $params['encabezadoresumen'][] = array(
                'colInic' => $i + 1,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila + 1,
                'pintar' => true
            );
            $params = self::pintarGrupo($params, $valueProd, $params['ObjetosArbolDer'], $contadorFila + 2,
                false, true, true, $i, 0, -1, 'cuerpoTablaResumen');
            $i += 2;
        }
        $params['ultimaFilaPintada'] = $params['contador'];
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadoresumen'],
            'cuerpoTabla' => $params['cuerpoTablaResumen']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    public
    function generarExcelVentaCucProducto($params)
    {
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }

        $params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);
        $params['ObjetosHijosEntidades'] = $this->gru->getEntidadesHijasByGrupos($params['ObjetosArbolDer']);
        $params['ObjetosHijosGrupos'] = $this->gru->getGruposHijosByGrupos($params['ObjetosArbolDer']);
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['cuerpoTabla'] = array();
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['tablaPlan'] = 'DatPlanVenta';
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto/Destino',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Al Cierre',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Acumulado',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan Mes',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            "pintar" => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 9,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            "pintar" => true,
            'centrar' => true
        );
        $contadorFila = 8;
        $hijasGrupoEnt = $params['ObjetosHijosEntidades'];
        $hijosGrupo = $params['ObjetosHijosGrupos'];
        if (count($hijosGrupo) > 0) {
            $params['idgrupo'] = $hijosGrupo;
        }
        if (count($hijasGrupoEnt) > 0) {
            $params['identidades'] = $hijasGrupoEnt;
        }
        $params['idueb'] = $params['ueb'] != "" ? $params['ueb'] : "";

        foreach ($params['ObjetosArbolIzq'] as $indexProd => $valueProd) {
            $params['formato'] = 3;
            if (!is_null($valueProd->getUmOperativa())) {
                $params['formato'] = $valueProd->getUmOperativa()->getCantdecimal() != null ? $valueProd->getUmOperativa()->getCantdecimal() : 3;
            }
            if (!$valueProd->getHoja()) {
                $hijos = array();
                $params['idproducto'] = $this->pro->getProductosHijos($valueProd, $hijos);
                $prodHijosSinPadreAux = $params['idproducto'];
                unset($prodHijosSinPadreAux[0]);
                $prodHijosSinPadre = array_values($prodHijosSinPadreAux);
            } else {
                $params['idproducto'] = $valueProd->getIdproducto();
                $prodHijosSinPadre = $valueProd->getIdproducto();
            }

            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => $valueProd->getNombre(),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 60,
                'negrita' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 1,
                'valor' => $valueProd->getUmOperativa() != null ?
                    $valueProd->getUmOperativa()->getAbreviatura() : '',
                'mergeRow' => 2,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'negrita' => true
            );
            $params['acumulado'] = 4;
            $plan = $this->ser->getPlanMes($params);
            $params['cuerpoTabla'][] = array(
                'col' => 2,
                'valor' => $plan,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['acumulado'] = 1;
            $realMes = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
            $params['cuerpoTabla'][] = array(
                'col' => 3,
                'valor' => $realMes,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 4,
                'valor' => '=IF(C' . $contadorFila . '=0,0,(D' . $contadorFila . '*100)/C' . $contadorFila . "))",
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'formato' => 0,
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 5,
                'valor' => '=(D' . $contadorFila . '- C' . $contadorFila . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );

            $planHF = $this->ser->getPlanMes($params);
            $params['cuerpoTabla'][] = array(
                'col' => 6,
                'valor' => $planHF,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['acumulado'] = 2;
            $realHF = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
            $params['cuerpoTabla'][] = array(
                'col' => 7,
                'valor' => $realHF,
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 8,
                'valor' => '=IF(G' . $contadorFila . '=0,0,(H' . $contadorFila . '*100)/G' . $contadorFila . "))",
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'formato' => 0,
                'derecha' => true
            );
            $params['cuerpoTabla'][] = array(
                'col' => 9,
                'valor' => '=(H' . $contadorFila . '- G' . $contadorFila . ')',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 0,
                'formato' => $params['formato'],
                'derecha' => true
            );

            if (count(array_intersect($params['arbolizq_array'], $prodHijosSinPadre)) == 0) {
                $params = self::pintarGrupo($params, $valueProd, $params['ObjetosArbolDer'], $contadorFila + 1,
                    false, false, false, 2, 0, 1);
                $params = self::pintarGrupo($params, $valueProd, $params['ObjetosArbolDer'], $contadorFila + 1,
                    false, false, false, 6, 0, 1, 'cuerpoTabla', 1, 2);
                $contadorFila = $params['contador'];
            }

            $contadorFila++;
        }

        $params['ultimaColPintada'] = 9;
        $params['ultimaFilaPintada'] = $params['contador'];
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );

        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    /*Cumplimiento de las ventas en divisa*/

    public
    function generarExcelVentaCuc($params)
    {
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);
        $params['ObjetosHijosEntidades'] = $this->gru->getEntidadesHijasByGrupos($params['ObjetosArbolDer']);
        $params['ObjetosHijosGrupos'] = $this->gru->getGruposHijosByGrupos($params['ObjetosArbolDer']);
        $contadorFila = 8;
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['cuerpoTabla'] = array();
        $params['cuerpoTablaResumen'] = array();
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $objEnumMes = new EnumMeses();
        $params['ultimaColPintada'] = 9;
        $mes = $objEnumMes->convertfecha($params['fecha']);
        $nombremes = $objEnumMes->obtenerMesDadoIndice($mes['m']);
        $params['tablaPlan'] = 'DatPlanVenta';
        $params['productosGlobal'] = array();
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto/Destino',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => $nombremes,
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Acumulado',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => 6,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 8,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 9,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params = self::pintarAcumuladoMes($params, $contadorFila, true, false, false, 2, 4, 1, 0, 1, false);
        $params = self::pintarAcumuladoMes($params, $contadorFila, true, false, false, 6, 1, 2, 0, 1, false);
        /* Encabezado tabla resumen */
        $contadorFila = $params['contador'];
        $contadorFilaresumen = $contadorFila + 2;//dejando tres filas de separacion entre tablas
        $params['encabezadoresumen'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto/Destino',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 2,
            'nombre' => 'Hasta  la Fecha',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 6,
            'nombre' => 'Acumulado del Mes',
            'mergeRow' => 0,
            'mergeCell' => 4,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $contadorFilaresumen++;
        $params['encabezadoresumen'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 5,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 6,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 7,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 8,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 9,
            'nombre' => 'Dif.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => $contadorFilaresumen,
            'ancho' => 0,
            'pintar' => true,
            'negrita' => true
        );
        $params['ultimaFilaPintada'] = $contadorFilaresumen;
        if (count($params['cuerpoTablaResumen']) > 0) {
            $cont = 0;
            foreach ($params['cuerpoTablaResumen'] as $index => $value) {
                if (count($params['cuerpoTablaResumen']) / 2 == $index) {
                    $cont = 0;
                }
                if ($index % 6 == 0) {
                    $cont++;
                }
                $params['cuerpoTablaResumen'][$index]['fila'] = $contadorFilaresumen + $cont;
                $params['ultimaFilaPintada'] = $contadorFilaresumen + $cont;
            }
        }
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadoresumen'],
            'cuerpoTabla' => $params['cuerpoTablaResumen']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    public
    function generarExcelResumenVentaMes($params)
    {
        $fecha2 = EnumMeses::convertirFechaParaBD($params['fecha']);
        $emes = new EnumMeses();
        if ($params['show_hijos']) {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $params['ObjetosArbolIzq'] = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['ObjetosArbolDer'] = $this->gru->getGruposByIds($params['arbolder_array']);
        $params['ObjetosHijosEntidades'] = $this->gru->getEntidadesHijasByGrupos($params['ObjetosArbolDer']);
        $params['ObjetosHijosGrupos'] = $this->gru->getGruposHijosByGrupos($params['ObjetosArbolDer']);
        $contadorFila = 8;
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['tablaPlan'] = 'DatPlanVenta';
        $params['cuerpoTabla'] = array();
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $fecha = $emes->convertfecha($fecha2);
        $params['idueb'] = $params['ueb'];
        $params['cuerpoTablaResumen'] = array();
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Mes',
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => 6,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Acumulado',
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => 6,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 5,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 6,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 7,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'centrar' => true,
            'pintar' => true
        );
        $params = self::pintarAcumuladoMes($params, $contadorFila, false, true, false, 2, 4, 1, 0, 1, true);
        $params = self::pintarAcumuladoMes($params, $contadorFila, false, true, false, 5, 1, 2, 0, 1, true);
        /* Encabezado tabla resumen */
        $indexresumen = $params['contador'] + 7;//dejando dos filas de separacion entre tablas
        $params['inicioResumen'] = $indexresumen;
        $meses = $emes->getMeses();
        $params['nameResumen'] = 'Ventas Totales en Cuc';
        $params['encabezadoresumen'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $indexresumen,
            'centrar' => true,
            'pintar' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => $indexresumen,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 2,
            'nombre' => 'Acumulado',
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => $indexresumen,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 2,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 1 + $indexresumen,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 3,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 1 + $indexresumen,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadoresumen'][] = array(
            'colInic' => 4,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 1 + $indexresumen,
            'pintar' => true,
            'centrar' => true
        );
        $i = 5;
        foreach ($meses as $clave => $valor) {
            $params['fecha'] = substr_replace($params['fecha'], '-' . $clave, 4, 3);
            $params['fechaPlan'] = substr_replace($params['fecha'], '-' . $clave, 4, 3);
            if ($clave == 1) {
                $params = self::pintarAcumuladoMes($params, $indexresumen + 2, false, true, false, 2, 1, 2, 0, 1, true);
            }
            $params['encabezadoresumen'][] = array(
                'colInic' => $i,
                'nombre' => $valor,
                'mergeRow' => 0,
                'mergeCell' => 3,
                'fila' => 0 + $indexresumen,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadoresumen'][] = array(
                'colInic' => $i,
                'nombre' => 'Plan',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 1 + $indexresumen,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadoresumen'][] = array(
                'colInic' => $i + 1,
                'nombre' => 'Real',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 1 + $indexresumen,
                'pintar' => true,
                'centrar' => true
            );
            $params['encabezadoresumen'][] = array(
                'colInic' => $i + 2,
                'nombre' => '%',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 1 + $indexresumen,
                'pintar' => true,
                'centrar' => true
            );
            $params = self::pintarAcumuladoMes($params, $indexresumen + 2, true, true, false, $i, 4, 3, 0, 1, true);
            $i += 3;
            if ($clave == $fecha['m']) {
                break;
            }
        }
        $params['ultimaColPintada'] = $i;
        $params['ultimaFilaPintada'] = $params['contador'];
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadoresumen'],
            'cuerpoTabla' => $params['cuerpoTablaResumen']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    public
    function pintarGrupo(
        $params,
        NomProducto $valueProd,
        $arraygrupos,
        $contadorFila,
        $resumen = false,
        $sindif = false,
        $sinpor = false,
        $columna,
        $posNombre = -1,
        $posUm = -1,
        $indice = 'cuerpoTabla',
        $acumuladoPlan = 4,
        $acumuladoReal = 1
    )
    {
        if ($resumen)
            $indice = 'cuerpoTablaResumen';
        //$formato = !isset($params['formato']) ? 'cantidad' : $params['formato'];
        $sepEspacio = " ";
        $objEnumLe = new EnumLetras();
        $letraI = $objEnumLe->letraExcel($columna);
        $letraF = $objEnumLe->letraExcel($columna + 1);
        foreach ($arraygrupos as $valueGrup) {
            $realMes = 0;
            $realHF = 0;
            $contCol = 0;
            if ($valueProd->getNivel() != 0) {
                $params['idum'] = $valueProd->getUmOperativa() != null ? $valueProd->getUmOperativa()->getIdunidadmedida() : '';
            } else {
                $params['idum'] = "";
            }
            $params['tablaPlan'] = 'DatPlanVenta';
            $hijas = array();
            $params['entidadesHijas'] = array();
            $params['identidades'] = array();
            $params['identidades'] = $this->gru->getEntidadesHijas($valueGrup, $hijas, $params);
            if ($valueGrup->getIdentidad() != null) {
                $params['identidades'][] = $valueGrup->getIdentidad()->getIdentidad();
            }
            $params['idgrupo'] = "";
            if (!$valueGrup->getHoja()) {
                $hijos = array();
                $result = $this->gru->getGruposHijos($valueGrup, $hijos, $params);
                $params['idgrupo'] = $result;
            } else {
                $params['idgrupo'] = $valueGrup->getIdgrupointeres();
            }
            $params['acumulado'] = $acumuladoPlan;
            $planMes = $this->ser->getPlanMes($params);
            $params['acumulado'] = $acumuladoReal;

            if (count($params['identidades']) != 0 && $params['identidades'] != null) {
                $realMes = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
            }
            if ((!isset($params['ceros']) && ($planMes != 0 || $realMes != 0)) || (isset($params['ceros']))) {
                if ($posNombre != -1) {
                    $params[$indice][] = array(
                        'col' => $posNombre,
                        'valor' => $sepEspacio . $valueGrup->getNombre(),
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 60
                    );
                }
                if ($posUm != -1) {
                    $params[$indice][] = array(
                        'col' => $posUm,
                        'valor' => ($valueProd->getUmOperativa() != null) ?
                            $valueProd->getUmOperativa()->getAbreviatura() : '',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0,
                        'centrar' => true
                    );
                }
                if ($sinpor) {
                    $contCol++;
                }
                if ($sindif) {
                    $contCol++;
                }
                if (!$sinpor) {
                    $params[$indice][] = array(
                        'col' => $columna + 2,
                        'valor' => '=IF(' . $letraI . $contadorFila . '=0,0,(D' . $contadorFila . '*100)/' . $letraI . $contadorFila . "))",
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0,
                        'formato' => 0,
                        'derecha' => true
                    );
                }
                if (!$sindif) {
                    $params[$indice][] = array(
                        'col' => $columna + 3 - $contCol,
                        'valor' => '=' . $letraF . $contadorFila . '-' . $letraI . $contadorFila,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0,
                        'formato' => $params['formato'],
                        'derecha' => true
                    );
                }
                $params[$indice][] = array(
                    'col' => $columna,
                    'valor' => $planMes,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFila,
                    'ancho' => 0,
                    'formato' => $params['formato'],
                    'derecha' => true
                );
                $params[$indice][] = array(
                    'col' => $columna + 1,
                    'valor' => $realMes,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFila,
                    'ancho' => 0,
                    'formato' => $params['formato'],
                    'derecha' => true
                );
                $contadorFila++;
            }
        }
        $params['contador'] = $contadorFila;
        return $params;
    }

    private
    function pintarAcumuladoMes(
        $params,
        $contadorFila,
        $resumen = false,
        $sindif = false,
        $sinpor = false,
        $columna = 2,
        $acumuladoplan,
        $acumuladoparte,
        $posNombre = -1,
        $posum = -1,
        $congrupos = false,
        $conproducto = true,
        $total = false,
        $indicePlan = 'sumaPlanes',
        $indiceReal = 'sumaReal',
        $sindifG = null,
        $sinporG = null,
        $pintarNeg = false
    )
    {
        //$formato = !isset($params['formato']) ? 'cantidad' : $params['formato'];
        $arraypro = $params['ObjetosArbolIzq'];
        $arraygrupos = $params['ObjetosArbolDer'];
        $hijasGrupoEnt = $params['ObjetosHijosEntidades'];
        $hijosGrupo = $params['ObjetosHijosGrupos'];
        $filas = array();
        foreach ($arraypro as $indexProd => $valueProd) {
            $params['formato'] = 3;
            if (!is_null($valueProd->getUmOperativa())) {
                $params['formato'] = $valueProd->getUmOperativa()->getCantdecimal() != null ? $valueProd->getUmOperativa()->getCantdecimal() : 3;
            }
            if (count($hijosGrupo) > 0) {
                $params['idgrupo'] = $hijosGrupo;
            }
            if (count($hijasGrupoEnt) > 0) {
                $params['identidades'] = $hijasGrupoEnt;
            }
            $plan = 0;
            $real = 0;
            $params['idum'] = "";
            if ($valueProd->getNivel() != 0) {
                $params['idum'] = $valueProd->getUmoperativa() != null ? $valueProd->getUmoperativa()->getIdunidadmedida() : "";
            }
            $prodHijosSinPadre = array();
            if (!array_key_exists($valueProd->getIdproducto(), $params['productosGlobal'])) {
                if (!$valueProd->getHoja()) {
                    $params['idproducto'] = $this->pro->getDescendientes($valueProd, false);
                } else {
                    $params['idproducto'] = $valueProd->getIdproducto();
                }
                $params['productosGlobal'][$valueProd->getIdproducto()] = $params['idproducto'];
            } else {
                $params['idproducto'] = $params['productosGlobal'][$valueProd->getIdproducto()];
            }
            if (!$valueProd->getHoja()) {
                $prodHijosSinPadre = array_reverse($params['idproducto']);
                unset($prodHijosSinPadre[count($prodHijosSinPadre) - 1]);
            }
            if ($conproducto) {
                $objEnumLe = new EnumLetras();
                $letraI = $objEnumLe->letraExcel($columna);
                $letraF = $objEnumLe->letraExcel($columna + 1);
                $contCol = 0;
                $params['acumulado'] = $acumuladoplan;
                $params['idproductoOrig'] = $valueProd->getIdproducto();
                $plan = $this->ser->getPlanMes($params);
                unset($params['idproductoOrig']);
                if ($plan == 0) {
                    $plan = $this->ser->getPlanMes($params);
                }
                $params['acumulado'] = $acumuladoparte;
                if (isset($params['tablaParte'])) {
                    $real = $this->em->getRepository($params['tablaParte'])->calcularNivelProd($params);
                } else {
                    $real = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
                }
                if ((!isset($params['ceros']) && ($plan != 0 || $real != 0)) || (isset($params['ceros']))) {
                    if ($posNombre != -1) {
                        $filas[] = array(
                            'col' => 0,
                            'valor' => $valueProd->getNombre(),
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'ancho' => 0,
                            'negrita' => $pintarNeg
                        );
                    }
                    if ($posum != -1) {
                        $filas[] = array(
                            'col' => 1,
                            'valor' => $valueProd->getUmOperativa() != null ?
                                $valueProd->getUmOperativa()->getAbreviatura() : '-',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'ancho' => 0,
                            'centrar' => true,
                            'negrita' => $pintarNeg
                        );
                    }
                    $filas[] = array(
                        'col' => $columna,
                        'valor' => $plan,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0,
                        'negrita' => $pintarNeg,
                        'formato' => $params['formato'],
                        'derecha' => true
                    );
                    $filas[] = array(
                        'col' => $columna + 1,
                        'valor' => $real,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0,
                        'negrita' => $pintarNeg,
                        'formato' => $params['formato'],
                        'derecha' => true
                    );
                    if ($sinpor) {
                        $contCol++;
                    }
                    if ($sindif) {
                        $contCol++;
                    }
                    if (!$sinpor) {
                        $filas[] = array(
                            'col' => $columna + 2,
                            'valor' => '=IF(' . $letraI . $contadorFila . ' = 0, 0,((' . $letraF . $contadorFila . '*100)/' . $letraI . $contadorFila . '))',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'ancho' => 0,
                            'negrita' => $pintarNeg,
                            'formato' => 0,
                            'derecha' => true
                        );
                    }
                    if (!$sindif) {
                        $filas[] = array(
                            'col' => $columna + 3 - $contCol,
                            'valor' => '=' . $letraF . $contadorFila . '-' . $letraI . $contadorFila,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'ancho' => 0,
                            'negrita' => $pintarNeg,
                            'formato' => $params['formato'],
                            'derecha' => true
                        );
                    }
                    if ($total) {
                        $misPadres = $this->pro->getProductosPadres($valueProd, array(), $params);
                        if (!Util::existePadre($params['arbolizq_array'], $misPadres['id'])) {
                            $params['total'][$indicePlan] .= $letraI . $contadorFila . "+";
                            $params['total'][$indiceReal] .= $letraF . $contadorFila . "+";
                        }
                    }
                    $contadorFila++;
                }
            }
            if (count(array_intersect($params['arbolizq_array'], $prodHijosSinPadre)) == 0 && $congrupos) {
                if ($sindifG != null) {
                    $sindif = $sindifG;
                }
                if ($sinporG != null) {
                    $sinpor = $sinporG;
                }
                $params = self::pintarGrupo($params, $valueProd, $params['ObjetosArbolDer'], $contadorFila,
                    $resumen, $sindif, $sinpor, $columna, $posNombre, $posum, 'cuerpoTabla', $acumuladoplan,
                    $acumuladoparte);
                $contadorFila = $params['contador'];
            }
        }

        if ($resumen) {
            $params['cuerpoTablaResumen'] = array_merge($params['cuerpoTablaResumen'], $filas);
        }
        $params['cuerpoTabla'] = array_merge($params['cuerpoTabla'], $filas);
        $params['contador'] = $contadorFila;

        return $params;
    }

    public
    function generarDatosParteVentaCad(
        $params
    )
    {
        $contadorFila = 6;
        $params['cuerpoTabla'] = array();
        $params['ultimaFilaPintada'] = $contadorFila;
        return $params;
    }

    public
    function generarExcelParteVentaCad(
        $params
    )
    {
        $params = self::generarDatosParteVentaCad($params);
        $params['ultimaColPintada'] = 6;
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
    }

    private function exportarDatosCumpVinculos($params)
    {
        if ($params['show_hijos']) {
            $arraypro = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $arraypro = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaPlan'] = $params['fecha'];
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['tablaPlan'] = 'DatPlanVenta';
        $contadorFila = 8;
        $tieneVinculo = false;
        $objEnumLe = new EnumLetras();
        $sumas = array();
        $pintarPlanRealPor = false;
        $vinculosPint = array();
        $params['ultimaColPintada'] = 4;
        $params['idueb'] = $params['ueb'];
        $formato = 3;
        foreach ($arraypro as $indexProd => $valueProd) {
            if (!is_null($valueProd->getUmOperativa())) {
                $formato = $valueProd->getUmOperativa()->getCantdecimal() != null ? $valueProd->getUmOperativa()->getCantdecimal() : 3;
            }
            if (!$tieneVinculo) {
                array_splice($params['cuerpoTabla'], -1);
            }
            $tieneVinculo = false;
            $tieneVinculoGene = false;
            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => $valueProd->getNombre(),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => $contadorFila,
                'ancho' => 60
            );

            if (!array_key_exists($valueProd->getIdproducto(), $params['productosGlobal'])) {
                if (!$valueProd->getHoja()) {
                    $hijos = array();
                    $params['idproducto'] = $this->pro->getDescendientes($valueProd, false);
                } else {
                    $params['idproducto'] = $valueProd->getIdproducto();
                }
                $params['productosGlobal'][$valueProd->getIdproducto()] = $params['idproducto'];
            } else {
                $params['idproducto'] = $params['productosGlobal'][$valueProd->getIdproducto()];
            }
            $sumas = array();
            foreach ($params['arbolder_array'] as $indexVinc => $valueVinc) {
                $pintarPlanRealPor = false;
                $planMes = 0;
                $realMes = 0;
                if ($valueProd->getNivel() != 0) {
                    $params['idum'] = $valueProd->getUmoperativa() != null ? $valueProd->getUmoperativa()->getIdunidadmedida() : '';
                } else {
                    $params['idum'] = "";
                }

                $params['identidad'] = $valueVinc;
                $params['acumulado'] = 4;
                $params['idvinculo'] = $valueVinc;
                $planMes = $this->ser->getPlanMes($params);
                $params['acumulado'] = 1;
                $realMes = $this->em->getRepository('ParteDiarioBundle:DatParteVenta')->calcularNivelProd($params);
                if ($indexProd == 0) {
                    $params['ColByIdVinc'][$params['arbolder_array'][$indexVinc]] = $params['ultimaColPintada'];
                }

                if ((!isset($params['ceros']) && !in_array($valueVinc, $vinculosPint) && ($planMes != 0 || $realMes != 0))
                    || (isset($params['ceros']) && !in_array($valueVinc, $vinculosPint) && ($planMes != 0 || $realMes != 0))
                    || (isset($params['ceros']) && !in_array($valueVinc, $vinculosPint) && ($planMes == 0 && $realMes == 0))) {
                    $nombre = $params['arboldernombre_array'][$indexVinc];
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['ultimaColPintada'],
                        'nombre' => Util::eliminar_simbolos($nombre),
                        'mergeRow' => 0,
                        'mergeCell' => 3,
                        'fila' => 6,
                        'ancho' => 30,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['ultimaColPintada'],
                        'nombre' => 'Plan',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['ultimaColPintada']++;
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['ultimaColPintada'],
                        'nombre' => 'Real',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $params['ultimaColPintada']++;
                    $params['encabezadotabla'][] = array(
                        'colInic' => $params['ultimaColPintada'],
                        'nombre' => '%',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => 7,
                        'pintar' => true,
                        'centrar' => true
                    );
                    $pintarPlanRealPor = true;
                    if (($planMes == 0 && $realMes == 0) && isset($params['ceros'])) {
                        $pintarPlanRealPor = false;
                    }
                    $vinculosPint[] = $valueVinc;
                    $params['ultimaColPintada']++;
                } else {
                    if (in_array($valueVinc, $vinculosPint) && ($planMes != 0 || $realMes != 0)) {
                        $pintarPlanRealPor = true;
                    } else {
                        $pintarPlanRealPor = false;
                    }
                }

                if ($pintarPlanRealPor) {
                    $letra = $objEnumLe->letraExcel($params['ColByIdVinc'][$valueVinc]);
                    $letra1 = $objEnumLe->letraExcel($params['ColByIdVinc'][$valueVinc] + 1);
                    $sumas['sumaPlan'] .= $letra . $contadorFila . '+';
                    $sumas['sumaReal'] .= $letra1 . $contadorFila . '+';

                    $params['cuerpoTabla'][] = array(
                        'col' => $params['ColByIdVinc'][$valueVinc],
                        'valor' => $planMes,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0,
                        'formato' => $formato
                    );

                    //$porCiento = '=(' . $letra1 . $contadorFila . '*100)/' . $letra . $contadorFila;
                    $porCiento = '=IF(' . $letra . $contadorFila . '=0,0,(D' . $contadorFila . '*100)/' . $letra . $contadorFila . "))";
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['ColByIdVinc'][$valueVinc] + 1,
                        'valor' => $realMes,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0,
                        'formato' => $formato
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => $params['ColByIdVinc'][$valueVinc] + 2,
                        'valor' => $porCiento,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0,
                        'derecha' => true,
                        'formato' => 0
                    );
                    $tieneVinculo = true;
                } else {
                    if (isset($params['ceros'])) {
                        $params['cuerpoTabla'][] = array(
                            'col' => $params['ColByIdVinc'][$valueVinc],
                            'valor' => 0,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'ancho' => 0
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => $params['ColByIdVinc'][$valueVinc] + 1,
                            'valor' => 0,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'ancho' => 0
                        );
                        $params['cuerpoTabla'][] = array(
                            'col' => $params['ColByIdVinc'][$valueVinc] + 2,
                            'valor' => 0,
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $contadorFila,
                            'ancho' => 0
                        );
                        $tieneVinculo = true;
                    } else {
                        $tieneVinculo = false;
                    }
                }

                if ($tieneVinculo && !$tieneVinculoGene) {
                    $tieneVinculoGene = true;
                }
            }

            if ($tieneVinculoGene && count($sumas) > 0) {
                $sumas['sumaPlan'] = trim($sumas['sumaPlan'], '+');
                $sumas['sumaReal'] = trim($sumas['sumaReal'], '+');
                $porcientoSum = '=IF(B' . $contadorFila . '=0,0,(C' . $contadorFila . '*100)/B' . $contadorFila . "))";
                $params['cuerpoTabla'][] = array(
                    'col' => 1,
                    'valor' => '=(' . $sumas['sumaPlan'] . ')',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFila,
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 2,
                    'valor' => '=(' . $sumas['sumaReal'] . ')',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFila,
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['cuerpoTabla'][] = array(
                    'col' => 3,
                    'valor' => $porcientoSum,
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFila,
                    'ancho' => 0,
                    'derecha' => true,
                    'formato' => 0
                );
                $contadorFila++;
            } else {
                if (isset($params['ceros'])) {
                    $params['cuerpoTabla'][] = array(
                        'col' => 1,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 2,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 3,
                        'valor' => 0,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'ancho' => 0
                    );
                    $contadorFila++;
                }
            }

        }
        if (!$tieneVinculo) {
            array_splice($params['cuerpoTabla'], -1);
        }
        return $params;
    }

    public function generarExcelCumpVinculos($params)
    {
        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Producto',
            'mergeRow' => 2,
            'mergeCell' => 0,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'Total de Vinculos Recibidos',
            'mergeRow' => 0,
            'mergeCell' => 3,
            'fila' => 6,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'Plan',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Real',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => '%',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 0,
            'pintar' => true,
            'centrar' => true
        );
        $params['productosGlobal'] = array();
        $params = self::exportarDatosCumpVinculos($params);
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    /*Reporte del Consumo de Materia Prima*/
    public
    function exportarDatosMateriaPrimaBalance($params)
    {
        $contadorFila = 8;
        $colAsegPintadas = array();
        $params['cuerpoTabla'] = array();
        $params['fecha'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $objEnumLe = new EnumLetras();
        $prodPintados = array();
        //$params['productosGlobal'] = array();
        $formato = 3;
        if ($params['show_hijos']) {
            $arraypro = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $arraypro = $this->pro->getProductosByIds($params['arbolizq_array']);
        }

        foreach ($arraypro as $indexProd => $valueProd) {
            if (!is_null($valueProd->getUmOperativa())) {
                $formato = $valueProd->getUmOperativa()->getCantdecimal() != null ? $valueProd->getUmOperativa()->getCantdecimal() : 3;
            }
            $params['idueb'] = $params['ueb'];
            $params['acumulado'] = 1;
            $params['idproducto'] = array();
            $tieneAseg = false;

            if (!array_key_exists($valueProd->getIdproducto(), $params['productosGlobal'])) {
                if (!$valueProd->getHoja()) {
                    $hijos = array();
                    $params['idproducto'] = $this->pro->getDescendientes($valueProd, false);
                } else {
                    $params['idproducto'] = $valueProd->getIdproducto();
                }
                $params['productosGlobal'][$valueProd->getIdproducto()] = $params['idproducto'];
            } else {
                $params['idproducto'] = $params['productosGlobal'][$valueProd->getIdproducto()];
            }

            $nivelActv = 0;
            $nivelActv = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->calcularNivelProd($params);

            if (count($params['idproducto']) > 0) {
                if (!in_array($valueProd->getIdproducto(), $prodPintados)) {
                    $prodPintados[] = $valueProd->getIdproducto();
                    $params['cuerpoTabla'][] = array(
                        'col' => 0,
                        'valor' => $valueProd->getNombre(),
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 1,
                        'valor' => $valueProd->getUmOperativa() != null ? $valueProd->getUmOperativa()->getAbreviatura() : '-',
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'centrar' => true
                    );
                    $params['cuerpoTabla'][] = array(
                        'col' => 2,
                        'valor' => $nivelActv,
                        'mergeRow' => 0,
                        'mergeCell' => 0,
                        'fila' => $contadorFila,
                        'derecha' => true,
                        'formato' => $formato
                    );
                    foreach ($params['todosAseg'] as $valueAseg) {
                        if (!$valueAseg->getHoja()) {
                            $hijas = array();
                            $params['idaseguramiento'] = $this->ase->getAseguramientoHijas($valueAseg, $hijas);
                        } else {
                            $params['idaseguramiento'][0] = $valueAseg->getIdaseguramiento();
                        }

                        $aseguramientos = $this->partediarioconsaseg->obtenerDatosConsumoAseg($params);

                        if ($aseguramientos['cantidad'] != null || isset($params['ceros'])) {
                            $colaseg = $params['asegDefault'][$valueAseg->getIdaseguramiento()];
                            $letraSuma = $objEnumLe->letraExcel($colaseg);
                            $params['sumAsegDefault'][$colaseg] .= $letraSuma . $contadorFila . '+';
                            $aseguramientos['cantidad'] = $aseguramientos['cantidad'] != null ? $aseguramientos['cantidad'] : 0;
                            $params['cuerpoTabla'][] = array(
                                'col' => $colaseg,
                                'valor' => $aseguramientos['cantidad'],
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $contadorFila,
                                'derecha' => true,
                                'formato' => $formato
                            );
                            $tieneAseg = true;
                        }
                    }
                }
            }

            if (!$tieneAseg) {
                array_splice($params['cuerpoTabla'], -3);
            } else {
                $contadorFila++;
            }
        }

        $params['sumAsegDefault'] = array_filter($params['sumAsegDefault']);
        if (count($params['cuerpoTabla']) > 0) {
            $params['cuerpoTabla'][] = array(
                'col' => 0,
                'valor' => 'Total',
                'mergeRow' => 0,
                'mergeCell' => 3,
                'fila' => $contadorFila,
                'negrita' => true
            );
            foreach ($params['sumAsegDefault'] as $index => $sumas) {
                $params['cuerpoTabla'][] = array(
                    'col' => $index,
                    'valor' => '=(' . trim($sumas, '+') . ')',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $contadorFila,
                    'negrita' => true,
                    'derecha' => true
                );
            }
        }

        $params['encabezadotabla'] = array_values($params['encabezadotabla']);
        $params['ultimaFilaPintada'] = $contadorFila;
        return $params;
    }

    public
    function generarExcelMateriaPrimaBalance($params)
    {
        $params['todosAseg'] = $this->ase->obtenerAsegSegunOrdenMPB();
        if (count($params['arbolder_array']) > 0) {
            $params['todosAseg'] = array_merge($params['todosAseg'], $params['arbolder_array']);
        }

        $params['encabezadotabla'][] = array(
            'colInic' => 0,
            'nombre' => 'Niveles Productivos',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 1,
            'nombre' => 'UM',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 2,
            'nombre' => 'Niv. Act.',
            'mergeRow' => 0,
            'mergeCell' => 0,
            'fila' => 7,
            'ancho' => 15,
            'pintar' => true,
            'centrar' => true
        );
        $params['encabezadotabla'][] = array(
            'colInic' => 3,
            'nombre' => 'Consumo Real',
            'mergeRow' => 0,
            'mergeCell' => count($params['todosAseg']),
            'fila' => 6,
            'negrita' => true,
            'pintar' => true,
            'centrar' => true
        );

        $objEnumLe = new EnumLetras();
        $params['productosGlobal'] = array();
        foreach ($params['todosAseg'] as $index => $value) {
            $params['encabezadotabla'][] = array(
                'colInic' => $index + 3,
                'nombre' => $value->getNombre(),
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 7,
                'ancho' => 40,
                'centrar' => true,
                'pintar' => true
            );
            $params['asegDefault'][$value->getIdaseguramiento()] = $index + 3;
            $params['sumAsegDefault'][$index + 3] = "";
        }

        $params['em'] = $this->em;

        $params = self::exportarDatosMateriaPrimaBalance($params);
        $params['tablas'][] = array(
            'encabezadotabla' => $params['encabezadotabla'],
            'cuerpoTabla' => $params['cuerpoTabla']
        );
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }

    private
    function datProdFormato($params, $arraypro, $arrayTablas, $ue)
    {
        $pintado = false;
        $formato = 3;
        foreach ($arraypro as $pro) {
            if (!is_null($pro->getUmOperativa())) {
                $formato = $pro->getUmOperativa()->getCantdecimal() != null ? $pro->getUmOperativa()->getCantdecimal() : 3;
            }
            if ($pro->getIdformato() != null && $pro->getIdsabor() != null) {
                $dat = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->produccionPorFormatoSabor($pro->getIdformato()->getId(),
                    $pro->getIdSabor()->getIdsabor(), $params['fechaCierre'], $ue['idueb'], $params['moneda'], $pro->getAlias());
                $arrayTablas['encabezadotabla'][] = array(
                    'colInic' => 0,
                    'nombre' => $pro->getNombre(),
                    'mergeRow' => 0,
                    'mergeCell' => 2,
                    'fila' => $params['filaEncabezado']
                );
                $params['filaEncabezado']++;
                $arrayTablas['encabezadotabla'][] = array(
                    'colInic' => 0,
                    'nombre' => '',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaEncabezado'],
                    'pintar' => true,
                    'centrar' => true
                );
                $arrayTablas['encabezadotabla'][] = array(
                    'colInic' => 1,
                    'nombre' => $pro->getIdformato()->getNombre(),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaEncabezado'],
                    'ancho' => 20,
                    'pintar' => true,
                    'centrar' => true
                );
                $arrayTablas['encabezadotabla'][] = array(
                    'colInic' => 2,
                    'nombre' => 'Total',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaEncabezado'],
                    'ancho' => 20,
                    'pintar' => true,
                    'centrar' => true
                );
                $params['filaCuerpo'] = $params['filaEncabezado'] + 1;
                $arrayTablas['cuerpoTabla'][] = array(
                    'col' => 0,
                    'valor' => $pro->getIdSabor()->getNombre(),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'pintar' => false
                );
                $arrayTablas['cuerpoTabla'][] = array(
                    'col' => 1,
                    'valor' => $dat[0]['entrega'] == '' ? 0 : $dat[0]['entrega'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'pintar' => false,
                    'derecha' => true,
                    'formato' => $formato
                );
                $arrayTablas['cuerpoTabla'][] = array(
                    'col' => 2,
                    'valor' => $dat[0]['cantidad'] == '' ? 0 : $dat[0]['cantidad'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'],
                    'pintar' => false,
                    'derecha' => true,
                    'formato' => $formato
                );
                $arrayTablas['cuerpoTabla'][] = array(
                    'col' => 0,
                    'valor' => 'Total de unidades',
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'] + 1,
                    'negrita' => true,
                    'pintar' => false
                );
                $arrayTablas['cuerpoTabla'][] = array(
                    'col' => 1,
                    'valor' => '=B' . $params['filaCuerpo'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'] + 1,
                    'pintar' => false,
                    'derecha' => true,
                    'formato' => $formato
                );
                $arrayTablas['cuerpoTabla'][] = array(
                    'col' => 2,
                    'valor' => '=C' . $params['filaCuerpo'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaCuerpo'] + 1,
                    'pintar' => false,
                    'derecha' => true,
                    'formato' => $formato
                );
                $params['filaCuerpo']++;
                $pintado = true;
            } else {
                $arrayTablas['encabezadotabla'][] = array(
                    'colInic' => 0,
                    'nombre' => $pro->getNombre(),
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaEncabezado'],
                    'pintar' => true,
                    'centrar' => true
                );
                if (!$pro->getHoja()) {
                    $params['hijos'] = $this->pro->getDescendientes($pro, true, true);
                    if (count($params['hijos']) > 0) {
                        $sabores = \NomencladorBundle\Util\Util::array_unique_callback($params['hijos'],
                            function ($criterio) {
                                return $criterio['idsabor'];
                            });
                        $formatos = \NomencladorBundle\Util\Util::array_unique_callback($params['hijos'],
                            function ($criterio) {
                                return $criterio['idformato'];
                            });
                        $colin = 1;
                        $total = 0;
                        $params['filaCuerpo'] = $params['filaEncabezado'] + 1;
                        $pintados = array();
                        foreach ($sabores as $sab) {
                            $colin = 1;
                            $arrayTablas['cuerpoTabla'][] = array(
                                'col' => 0,
                                'valor' => $sab['idsabor']['nombre'],
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $params['filaCuerpo'],
                                'pintar' => false,
                                'izquierda' => true
                            );
                            foreach ($formatos as $for) {
                                $dat = $this->em->getRepository('ParteDiarioBundle:DatPartediarioProduccion')->produccionPorFormatoSabor(
                                    $for['idformato']['id'], $sab['idsabor']['idsabor'], $params['fechaCierre'],
                                    $ue['idueb'],
                                    $params['moneda'], $pro->getAlias(), $pro->getIdgenerico()->getIdgenerico());
                                if (!in_array($for['idformato']['id'], $pintados)) {
                                    $arrayTablas['encabezadotabla'][] = array(
                                        'colInic' => $colin,
                                        'nombre' => $for['idformato']['nombre'],
                                        'mergeRow' => 0,
                                        'mergeCell' => 0,
                                        'fila' => $params['filaEncabezado'],
                                        'ancho' => 20,
                                        'pintar' => true,
                                        'centrar' => true
                                    );
                                    $pintados[] = $for['idformato']['id'];
                                }
                                $arrayTablas['cuerpoTabla'][] = array(
                                    'col' => $colin,
                                    'valor' => $dat[0]['entrega'] == null ? '0' : $dat[0]['entrega'],
                                    'mergeRow' => 0,
                                    'mergeCell' => 0,
                                    'fila' => $params['filaCuerpo'],
                                    'pintar' => false,
                                    'derecha' => true,
                                    'formato' => $formato
                                );
                                $total += $dat[0]['cantidad'];
                                $colin++;
                            }
                            $arrayTablas['cuerpoTabla'][] = array(
                                'col' => $colin,
                                'valor' => $total,
                                'mergeRow' => 0,
                                'mergeCell' => 0,
                                'fila' => $params['filaCuerpo'],
                                'pintar' => false,
                                'derecha' => true,
                                'formato' => $formato
                            );
                            $params['filaCuerpo']++;
                            $total = 0;
                        }
                        $arrayTablas['encabezadotabla'][] = array(
                            'colInic' => $colin,
                            'nombre' => 'Total',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['filaEncabezado'],
                            'ancho' => 15,
                            'pintar' => true,
                            'centrar' => true
                        );
                        $arrayTablas['cuerpoTabla'][] = array(
                            'col' => 0,
                            'valor' => 'Total de unidades',
                            'mergeRow' => 0,
                            'mergeCell' => 0,
                            'fila' => $params['filaCuerpo'],
                            'negrita' => true,
                            'pintar' => false
                        );
                    }
                }
            }

            $params['filaEncabezado'] = $params['filaCuerpo'] + 3;
            $params['filaCuerpo'] = $params['filaEncabezado'] + 1;
            $params['tablas'][] = $arrayTablas;
            $params['ultimaFilaPintada'] = $params['filaCuerpo'] + 2;


            return $params;

        }
    }

    public
    function generarProduccionFormato($params)
    {
        $params['filaEncabezado'] = 7;
        $params['filaCuerpo'] = 8;
        $objEnumLe = new EnumLetras();
        $params['em'] = $this->em;
        $params['fechaCierre'] = EnumMeses::convertirFechaParaBD($params['fecha']);
        $params['fecha'] = $params['fechaCierre'];
        if ($params['show_hijos']) {
            $arraypro = $this->pro->getProductosHijosByProductos($params['arbolizq_array'], true);
        } else {
            $arraypro = $this->pro->getProductosByIds($params['arbolizq_array']);
        }
        $uebs = $params['empresa'] == true ? array() : $this->ueb->findByUebs($params);
        if (count($uebs) == 0) {
            $ue['idueb'] = null;
            $arrayTablas['encabezadotabla'][] = array(
                'colInic' => 0,
                'nombre' => 'Empresa',
                'mergeRow' => 0,
                'mergeCell' => 0,
                'fila' => 6,
                'negrita' => true,
                'pintar' => true,
                'titulo' => true
            );
            $params = self::datProdFormato($params, $arraypro, $arrayTablas, $ue);
        } else {
            foreach ($uebs as $ue) {
                $arrayTablas = array();
                $arrayTablas['encabezadotabla'][] = array(
                    'colInic' => 0,
                    'nombre' => $ue['nombre'],
                    'mergeRow' => 0,
                    'mergeCell' => 0,
                    'fila' => $params['filaEncabezado'],
                    'pintar' => true,
                    'centrar' => true
                );
                $params['filaEncabezado']++;
                $params = self::datProdFormato($params, $arraypro, $arrayTablas, $ue);
            }
        }
        $objGenerarExcel = new GenerarExcel();
        $objGenerarExcel->exportarExcel($params);
    }
}