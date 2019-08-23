<?php

namespace ParteDiarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatEntidadAcopioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cantidad', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => true,
                'attr' => array('class' => 'required form-control number', 'min' => 0)
            ))
            ->add('entidad', null, array(
                'attr' => array('class' => 'hidden')
            ))
            ->add('producto', null, array(
                'attr' => array('class' => 'hidden')
            ))
            ->add('um', null, array(
                'attr' => array('class' => 'hidden')
            ))
            ->add('acidez', 'text', array(
                'label' => 'Porciento de acidez:',
                'attr' => array('placeholder' => '%', 'class' => 'form-control number ', 'min' => 0)
            ));

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatEntidadAcopio'
        ));
    }
}
