<?php

namespace ParteDiarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use     Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AseguramientoAlertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'widget' => 'single_text',
                'attr' => array('class' => 'form-control js-datepicker read'),
                'html5' => false,
            ))
            ->add('entidad', null, array(
                'label' => 'UEB',
                'empty_value' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select')))
            ->add('cant', 'Symfony\Component\Form\Extension\Core\Type\NumberType',array(
                'label' => 'Existencia',
                'attr' => array(
                    'class' => 'form-control number',
                    'min'=>'0'
                )
            ))
            ->add('operador', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => array(
                    '>' => '>',
                    '<' => '<',
                    '=' => '=',
                    '<>' => '<>',
                    '>=' => '>=',
                    '<=' => '<='
                ),
                'attr' => array(
                    'class' => 'form-control chosen-select'
                )
            ))
            ->add('descripcion','Symfony\Component\Form\Extension\Core\Type\TextareaType',array(
                'label' => 'Texto a Mostrar',
                'attr' => array(
                    'class' => 'form-control textarea tag focus '
                )
            ))
            ->add('usuarios', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'multiple' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'chosen-select'),
                'class'=>'AdminBundle\Entity\Usuario',
                'required' => true
            ))
            ->add('insumo',null,array(
                'label' => 'Aseguramiento',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select')))

        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatAlerta'
        ));
    }
}
