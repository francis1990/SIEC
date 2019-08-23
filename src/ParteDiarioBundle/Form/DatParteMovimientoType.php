<?php

namespace ParteDiarioBundle\Form;

use NomencladorBundle\Repository\NomConceptoRepository;
use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DatParteMovimientoType extends AbstractType
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
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true,
                'choice_attr' => function ($producto) {
                    return array(
                        'data-um' => ($producto->getUmOperativa() != null) ? $producto->getUmOperativa()->getIdunidadmedida() : 0
                    );
                }
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
            ->add('um', null, array(
                'label' => 'Unidad de medida:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control ', 'readonly' => true,'disabled' => 'disabled')
            ))
            ->add('cantidad', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad:',
                'attr' => array('placeholder' => 'Cantidad', 'class' => 'form-control number mayorcero focus')
            ))
            ->add('almacen', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Almacén:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomAlmacen',
                'attr' => array('class' => 'form-control chosen-select'),
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
                    'id' => 'btn_add_pmov',
                ),
            ));

        if (!$options['data']->getIdParte()) {
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agr_pmov',
                ),
            ))->add('concepto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Conceptos:',
                'class' => 'NomencladorBundle:NomConcepto',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true
            ));
        } else {
            $builder->add('concepto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Conceptos:',
                'class' => 'NomencladorBundle:NomConcepto',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true
            ));
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatParteMovimiento'
        ));
    }
}
