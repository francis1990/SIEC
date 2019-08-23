<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Entity\NomRutaSuministrador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use NomencladorBundle\Entity\ComunRepository;

class NomRutaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo','Symfony\Component\Form\Extension\Core\Type\NumberType',array(
                'label' => ' Código:',
                'required'=>false,
                'attr' => array('placeholder'=>'Código','class' => 'form-control focus digits' , 'maxlength' => '2')
            ))
            ->add('nombre','text',array(
                'label' => 'Nombre:',
                'attr' => array('placeholder'=>'Nombre','class' => 'form-control','maxlength' => '100')
            ))

            ->add('datos_pro', 'hidden', array(
                'mapped'=>false,
            ))
            ->add('elim_pro', 'hidden', array(
                'mapped'=>false,
            ))
            ->add('suministradores', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'NomencladorBundle\Form\NomRutaSuministradorType',
                'prototype_data' => new NomRutaSuministrador(),
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'by_reference' => false,
                'required' => false,
                'mapped' => true,
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled btn-aceptar",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_ruta',
                    'title' => ' Aceptar'
                ),
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomRuta'
        ));
    }
}
