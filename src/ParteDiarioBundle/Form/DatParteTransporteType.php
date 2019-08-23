<?php

namespace ParteDiarioBundle\Form;

use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DatParteTransporteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('existencia', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Parque:',
                'attr' => array('placeholder' => 'Existencia', 'class' => 'form-control digits focus')
            ))
            ->add('disponible', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Disponible:',
                'attr' => array('placeholder' => 'Disponible', 'class' => 'form-control menorparte digits')
            ))
            ->add('cdt', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Coeficiente de disponibilidad:',
                'required' => false,
                'attr' => array('placeholder' => 'Coeficiente de disponibilidad', 'class' => 'form-control number' ,'readonly'=>true)
            ))

            ->add('ueb',null,array(
                'label' => 'UEB:',
                'required'=>true,
                'placeholder'=>'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('tipotransporte',null,array(
                'label' => 'Tipo transporte:',
                'required'=>true,
                'placeholder'=>'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required'=>true,
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
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_ptrans',
                ),
            ))
        ;
        if(!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agr_ptrans',
                ),
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatParteTransporte'
        ));
    }
}
