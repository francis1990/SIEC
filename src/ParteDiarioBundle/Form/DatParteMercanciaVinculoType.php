<?php

namespace ParteDiarioBundle\Form;

use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\Validator\Constraints\DateTime;

class DatParteMercanciaVinculoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entidad', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Entidad:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomEntidad',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true,
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where(' u.vinculo=true and u.activo=true');
                }
            ))
            ->add('um', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'attr' => array('class' => 'form-control chosen-select', 'readonly' => true, 'disabled' => 'disabled'),
                'required' => true
            ))
            ->add('almacen', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Almacén :',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomAlmacen',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true
            ))
            ->add('cantidad', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad:',
                'attr' => array('placeholder' => 'Cantidad', 'class' => 'form-control number tresdecimales mayorcero focus'),
                'precision' => 3
            ))
            ->add('factura', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Factura:',
                'attr' => array('placeholder' => 'Factura', 'class' => 'form-control', 'maxlength' => '20')
            ))
            ->add('preciomn', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Precio CUP:',
                'attr' => array('class' => 'form-control number mayorcero cincodecimales',),
                'precision' => 5,
                'required' => false
            ))
            ->add('importemn', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Importe CUP:',
                'required' => false,
                'attr' => array('class' => 'form-control number ', 'readonly' => true),
                'precision' => 5
            ))
            ->add('importecuc', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Importe CUC:',
                'required' => false,
                'attr' => array('class' => 'form-control number cincodecimales', 'readonly' => true),
                'precision' => 5
            ))
            ->add('preciocuc', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Precio CUC:',
                'attr' => array('placeholder' => 'Cantidad', 'class' => 'form-control number mayorcero cincodecimales',),
                'precision' => 5,
                'required' => false
            ))
            ->add('producto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Producto:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomProducto',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true,
                'choice_attr' => function ($producto) {
                    return array('data-um' => $producto->getUmOperativa()->getIdunidadmedida());
                },
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
            ))
            ->add('ueb', null, array(
                'label' => 'UEB:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false,
            ])
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
            ->add('anno', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_pmov',
                ),
            ));
        if (!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agr_pmov',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatParteMercanciaVinculo'
        ));
    }
}
