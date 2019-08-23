<?php

namespace ParteDiarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatPartePortadorMedidorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('medidor', null, array(
                'attr' => array('class' => 'form-control chosen-select chosen-select-embebed medidor required',)
            ))
            ->add('consumo', null, array(
                'read_only' => true,
                'attr' => array('class' => 'form-control required',)
            ))
            ->add('lectura', null, array(
                'attr' => array('class' => 'form-control required changeValue number', "min" => 0)
            ))
            ->add('multiplicador', null, array(
                'attr' => array('class' => 'form-control required changeValue number', "min" => 0)
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatPartePortadorMedidor'
        ));
    }
}
