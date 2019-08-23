<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomMedidorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disabled = '';
        if(!$options['data']->getId())
            $disabled = 'disabled';
        $builder
            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'attr' => array('class' => 'form-control focus','maxLength' => '100'),
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary ",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_medidor',
                ),
            ));
        if(!$options['data']->getId())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary ",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agr_medidor',
                ),
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomMedidor'
        ));
    }
}
