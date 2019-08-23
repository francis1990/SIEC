<?php

namespace ParteDiarioBundle\Form;

use ParteDiarioBundle\Entity\DatEntidadAcopio;
use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DatParteAcopioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$disabled = '';
        if (!$options['data']->getIdParte())
            $disabled = 'disabled';*/
        $valores = $options['valores'];
        $builder
            ->add('ruta', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Ruta:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomRuta',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true
            ))
            ->add('destino', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'label' => 'UEB/Destino:',
                'choices' => $valores,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false,
            ])
            ->add('ueb', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UEB:',
                'required' => true,
                'class' => 'NomencladorBundle:NomUeb',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
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
            ->add('acopio', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'ParteDiarioBundle\Form\DatEntidadAcopioType',
                'prototype_data' => new DatEntidadAcopio(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'error_bubbling' => false,
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_acopio',
                ),
            ));
        if (!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agr_acopio',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatParteAcopio',
            'valores' => null,
        ));
    }
}
