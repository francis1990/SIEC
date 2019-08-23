<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use NomencladorBundle\Entity\ComunRepository;

class NomAseguramientoType extends AbstractType
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
                'attr' => array('placeholder' => 'Código', 'class' => 'form-control focus digits', 'maxlength' => '18')
            ))
            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre:',
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control', 'maxlength' => '100')
            ))
            ->add('idunidadmedida', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM:',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select '),
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('padre', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Aseguramiento superior:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomAseguramiento',
                'attr' => array('placeholder' => 'Aseguramiento superior', 'class' => 'form-control chosen-select'),
                'required' => false,
                'mapped' => false
            ))
            ->add('mpb', 'checkbox', array(
                'label' => 'MPB:',
                'required' => false,
            ))
            ->add('ordenmpb', 'text', array(
                'label' => 'Orden MPB:',
                'required' => false,
                'attr' => array('placeholder' => 'Orden', 'class' => 'form-control focus digits', 'maxlength' => '2', 'min' => 0, 'disabled' => true)
            ))
            ->add('precio_cup', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Precio CUP:',
                'required' => false,
                'attr' => array('placeholder' => 'Precio CUP', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('precio_cuc', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Precio CUC:',
                'required' => false,
                'attr' => array('placeholder' => 'Precio CUC', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_aceptar',
                ),
            ))
            ->add('agregar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agregar',
                ),
            ));;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomAseguramiento'
        ));
    }
}
