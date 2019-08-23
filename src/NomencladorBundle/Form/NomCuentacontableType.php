<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomCuentacontableType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Número de cuenta:',
                'required' => true,
                'attr' => array('placeholder' => 'Número', 'class' => 'form-control focus digits', 'maxlength' => '16')
            ))
            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre:',
                'required' => true,
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control', 'maxlength' => '100')
            ))
            ->add('porcobrar', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'label' => 'Por cobrar:',
                'required' => false
            ))
            ->add('finanzas', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'label' => 'Finanzas:',
                'required' => false
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_aceptar_cuenta',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomCuentacontable'
        ));
    }
}
