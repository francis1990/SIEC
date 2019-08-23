<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use NomencladorBundle\Entity\DatNormaAseguramiento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomNormaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('producto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Producto:',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),
                'class' => 'NomencladorBundle:NomProducto',
                'placeholder' => 'Seleccione...',
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->innerJoin('u.idgenerico', 'g')
                        ->where('u.activo = true')
                        ->where('g.acopio = false')
                        ->orderBy('u.nombre');
                }
            ))
            ->add('tiponorma', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Tipo NC:',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),
                'class' => 'NomencladorBundle:NomTipoNorma',
                'placeholder' => 'Seleccione...',
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->where('u.activo = true')
                        ->orderBy('u.nombre');
                }
            ))
            ->add('valornorma', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cada:',
                'required' => true,
                'attr' => array('placeholder' => 'Cantidad para norma...', 'class' => 'form-control focus number mayorcero ')
            ))
            ->add('umnorma', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'required' => true,
                'label' => 'UM:',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('grasa', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Grasa:',
                'required' => false,
                'attr' => array('placeholder' => 'Grasa...', 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('sng', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'SNG:',
                'required' => false,
                'attr' => array('placeholder' => 'Sng...', 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('aseguramientos', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'NomencladorBundle\Form\DatNormaAseguramientoType',
                'prototype_data' => new DatNormaAseguramiento(),
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
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_norma',
                ),
            ));

        if (!$options['data']->getIdnorma())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agregar_norma',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomNorma'
        ));
    }
}
