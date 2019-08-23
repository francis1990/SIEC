<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Repository\NomGenericoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class NomSubgenericoType extends AbstractType
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
                'attr' => array('placeholder'=>'Código','class' => 'form-control hasDatepicker digits focus','minlength'=>'2','maxlength'=>'2')
            ))
            ->add('nombre','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Nombre:',
                'attr' => array('placeholder'=>'Nombre','class' => 'form-control','maxlength' => '100')
            ))
            ->add('generico','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'label' => 'Genérico:',
                'placeholder'=>'Seleccione...',
                'class' => 'NomencladorBundle:NomGenerico',
                'empty_data'=>'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select' ,'placeholder'=>'Genérico' ),
                'query_builder'=>function(NomGenericoRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('empaque','Symfony\Component\Form\Extension\Core\Type\CheckboxType',array(
                'label' => 'Empaque:',
                'required' => false,
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled btn-aceptar",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn-addsubgenerico',
                    'title'=>' Aceptar'
                ),
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomSubgenerico'
        ));
    }
}
