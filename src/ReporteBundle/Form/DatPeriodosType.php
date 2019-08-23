<?php

namespace ReporteBundle\Form;

use ReporteBundle\Entity\NomPeriodos;
use ReporteBundle\Repository\DatPeriodosRepository;
use ReporteBundle\Repository\NomPeriodosRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatPeriodosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ident', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre',
                'attr' => array(
                    'class' => 'form-control alfanumerico focus','maxLength' => '50'
                )
            ))
            ->add('descripcion','Symfony\Component\Form\Extension\Core\Type\TextareaType',array(
                'label' => 'Descripción',
                'attr' => array(
                    'class' => 'form-control textarea')
            ))
            ->add('diai','entity',array(
                'class'=>'ReporteBundle:NomPeriodos',
                'label' => 'Día inicial:',
                'attr' => array('placeholder'=>'Valor día inicial','class' => 'form-control chosen-select mayorcero'),
                'query_builder' => function (NomPeriodosRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->where('u.activo = true')
                        ->where('u.frecuencia = 1')
                        ->orderBy('u.ident');
                },
            ))
            ->add('mesi','entity',array(
                'class'=>'ReporteBundle:NomPeriodos',
                'label' => 'Mes inicial:',
                'attr' => array('placeholder'=>'Valor mes inicial','class' => 'form-control chosen-select mayorcero'),
                'query_builder' => function (NomPeriodosRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->where('u.activo = true')
                        ->where('u.frecuencia = 2')
                        ->orderBy('u.ident');
                },
            ))
            ->add('anoi','entity',array(
                'class'=>'ReporteBundle:NomPeriodos',
                'label' => 'Año inicial:',
                'attr' => array('placeholder'=>'Valor año inicial','class' => 'form-control chosen-select mayorcero'),
                'query_builder' => function (NomPeriodosRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->where('u.activo = true')
                        ->where('u.frecuencia = 3')
                        ->orderBy('u.ident');
                },
            ))
            ->add('diaf','entity',array(
                'class'=>'ReporteBundle:NomPeriodos',
                'label' => 'Día final:',
                'attr' => array('placeholder'=>'Valor día final','class' => 'form-control chosen-select mayorcero'),
                'query_builder' => function (NomPeriodosRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->where('u.activo = true')
                        ->where('u.frecuencia = 1')
                        ->orderBy('u.ident');
                },
            ))
            ->add('mesf','entity',array(
                'class'=>'ReporteBundle:NomPeriodos',
                'label' => 'Mes final:',
                'attr' => array('placeholder'=>'Valor mes final','class' => 'form-control chosen-select mayorcero'),
                'query_builder' => function (NomPeriodosRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->where('u.activo = true')
                        ->where('u.frecuencia = 2')
                        ->orderBy('u.ident');
                },
            ))
            ->add('anof','entity',array(
                'class'=>'ReporteBundle:NomPeriodos',
                'label' => 'Año final:',
                'attr' => array('placeholder'=>'Valor año final','class' => 'form-control chosen-select mayorcero'),
                'query_builder' => function (NomPeriodosRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->where('u.activo = true')
                        ->where('u.frecuencia = 3')
                        ->orderBy('u.ident');
                },
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_aceptar',
                ),
            ));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ReporteBundle\Entity\DatPeriodos'
        ));
    }
}
