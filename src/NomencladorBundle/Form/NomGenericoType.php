<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class NomGenericoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Código:',
                'required' => true,
                'attr' => array('placeholder'=>'Código','class' => 'form-control focus digits','minlength'=>'2','maxlength'=>'2')
            ))
            ->add('nombre','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Nombre:',
                'required' => true,
                'attr' => array('placeholder'=>'Nombre','class' => 'form-control','maxlength' => '100')
            ))
            ->add('acopio','Symfony\Component\Form\Extension\Core\Type\CheckboxType',array(
                'label' => 'Es acopio:',
                'required' => false
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_generico',
                ),
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomGenerico'
        ));
    }
}
