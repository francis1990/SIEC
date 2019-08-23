<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomTipoEntidadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre:',
                'required' => true,
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control', 'maxlength' => '100')
            ))
            ->add('codigo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Código:',
                'required' => true,
                'attr' => array('placeholder' => 'Código', 'class' => 'form-control digits focus', 'maxlength' => '2')
            ))
            ->add('abreviatura', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Abreviatura:',
                'required' => true,
                'attr' => array('placeholder' => 'Abreviatura', 'class' => 'form-control', 'maxlength' => '10')
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_aceptar_tipoent',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomTipoEntidad'
        ));
    }
}
