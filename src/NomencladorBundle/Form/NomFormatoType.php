<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use NomencladorBundle\Repository\NomSubgenericoRepository;
use NomencladorBundle\Repository\NomUnidadmedidaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



class NomFormatoType extends AbstractType
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
                'attr' => array('placeholder'=>'Código','class' => 'form-control digits focus','minlength'=>'3','maxlength'=>'3')
            ))
            ->add('nombre','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Nombre:',
                'attr' => array('placeholder'=>'Nombre','class' => 'form-control','maxlength' => '100')
            ))
            ->add('peso','Symfony\Component\Form\Extension\Core\Type\NumberType',array(
                'label' => 'Valor:',
                'attr' => array('placeholder'=>'Peso','class' => 'form-control number mayorcero')
            ))
            ->add('idunidadmedida','Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM:',
                'placeholder'=>'Seleccione...',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'attr' => array('class' => 'form-control chosen-select'),
               'query_builder'=>function(ComunRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('idsubgenerico',null,array(
                'label' => 'Sub-genéricos:',
                'required'=>false,
                'attr' => array('class' => 'form-control  chosen-select', 'multiple'=>'true'),
                'query_builder'=>function(NomSubgenericoRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled btn-aceptar",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn-btn_add_formato',
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
            'data_class' => 'NomencladorBundle\Entity\NomFormato'
        ));
    }
}
