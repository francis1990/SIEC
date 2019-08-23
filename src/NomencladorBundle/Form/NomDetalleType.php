<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomDetalleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', 'text', array(
                'label' => 'Código:',
                'attr' => array('placeholder' => 'Código', 'class' => 'form-control focus digits' , 'maxlength' => '6')
            ))
            ->add('nombre', 'text', array(
                'label' => 'Nombre:',
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control' , 'maxlength' => '255')
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomDetalle'
        ));
    }
}
