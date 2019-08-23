<?php

namespace ParteDiarioBundle\Form;

use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NomencladorBundle\Entity\ComunRepository;

class DatPartediarioProduccionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('producto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Producto:',
                'class' => 'NomencladorBundle:NomProducto',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select required'),
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->innerJoin('u.idformato', 'f')
                        ->innerJoin('u.idgenerico', 'g')
                        ->where('u.activo = true')
                        ->where('u.hoja = true')
                        ->where('g.acopio = false')
                        ->where('f is not null')
                        ->orderBy('u.nombre');
                },
                'choice_attr' => function ($producto) {

                    $conv = $GLOBALS['kernel']->getContainer()->get('nomenclador.nomconversion')->obtenerConversion($producto);

                    if ($producto->getIdformato() != null && $conv != 0) {
                        $conv = $conv * $producto->getIdformato()->getPeso();
                    } else {
                        $conv = 1;
                    }
                    $emp = 'false';
                    if ($producto->getIdsubgenerico() != null)
                        $emp = $producto->getIdsubgenerico()->getEmpaque();

                    return array(
                        'data-empaque' => $emp,
                        'data-um' => ($producto->getUmOperativa() != null) ? $producto->getUmOperativa()->getIdunidadmedida() : 0,
                        'data-factor' => $producto->getFactor() == 0 ? $conv : $producto->getFactor(),
                    );
                },
            ))
            ->add('ueb', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UEB:',
                'required' => true,
                'class' => 'NomencladorBundle:NomUeb',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select required')
            ))
            ->add('um', null, array(
                'label' => 'Unidad de medida:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control ', 'readonly' => true, 'disabled' => 'disabled')
            ))
            ->add('cantidad', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad UM Inf:',
                'attr' => array('placeholder' => 'Cantidad', 'class' => 'form-control required number')
            ))
            ->add('cantproceso', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad proceso:',
                'required' => false,
                'attr' => array('placeholder' => 'Cantidad', 'class' => 'form-control number')
            ))
            ->add('cantempaque', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad empacada:',
                'required' => false,
                'attr' => array('placeholder' => 'Empaque', 'class' => 'form-control number')
            ))
            ->add('moneda', null, array(
                'label' => 'Moneda/Destino:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select required')
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'attr' => ['class' => 'form-control js-datepicker read required'],
                'html5' => false,
            ])
            ->add('almacen', null, array(
                'label' => 'Almacén:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select required')
            ))
            ->add('incidencias', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'ParteDiarioBundle\Form\DatIncidenciaType',
                'prototype_data' => new DatIncidencia(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'error_bubbling' => false,
                'entry_options' => array(
                    'by_parte' => true,
                ),
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_prod',
                ),
            ))
            ->add('updateCons', 'hidden', array(
                'mapped' => false
            ))
            ->add('diferenciaNivActv', 'hidden', array(
                'mapped' => false
            ))
            ->add('entrega', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad física :',
                'attr' => array('placeholder' => 'Entrega', 'class' => 'form-control number focus')
            ));

        if (!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agregar_prod',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatPartediarioProduccion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dat_partediario_produccion';
    }
}
