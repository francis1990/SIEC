<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NomPrecioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('preciomn', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Precio CUP:',
                'required' => false,
                'attr' => array('class' => 'form-control number focus')
            ))
            ->add('preciocuc', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Precio CUC:',
                'required' => false,
                'attr' => array('class' => 'form-control number')
            ))
            ->add('producto', 'hidden')
            ->add('grupo', 'hidden')
            ->add('gruposdeselect', 'hidden', array('mapped' => false))
            ->add('preciocucold', 'hidden', array('mapped' => false))
            ->add('preciomnold', 'hidden', array('mapped' => false))
            ->add('action', 'hidden', array('mapped' => false))
            ->add('um', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM:',
                'required' => true,
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo');
                }
            ))
            ->add('enviado', 'hidden', array(
                'mapped' => false
            ))
            ->add('grupoexistentes', 'hidden', array(
                'mapped' => false
            ))
            ->add('productoexistente', 'hidden', array(
                'mapped' => false
            ))
            ->add('data-accion', 'hidden', array(
                'mapped' => false
            ))
            ->add('impuesto', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Impuesto(%):',
                'required' => false,
                'attr' => array('class' => 'form-control number')
            ))
            ->add('resolucion', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Resolución:',
                'required' => false,
                'attr' => array('class' => 'form-control', 'maxlength' => '20')
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required' => false,
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomPrecio'
        ));
    }
}
