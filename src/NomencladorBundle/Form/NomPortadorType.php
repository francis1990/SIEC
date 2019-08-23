<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\BooleanType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use NomencladorBundle\Entity\ComunRepository;

class NomPortadorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo','text',array(
                'label' => 'Código:',
                'attr' => array('placeholder'=>'Código','class' => 'form-control focus digits', 'maxlength' => '2')
            ))
            ->add('nombre','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Nombre:',
                'attr' => array('placeholder'=>'Nombre','class' => 'form-control','maxlength' => '100')
            ))
            ->add('alcance', 'checkbox', array(
                'label' => 'Alcance:',
                'required' => false,
            ))
            ->add('dia', 'checkbox', array(
                'label' => 'Día:',
                'required' => false,
            ))
            ->add('madrugada', 'checkbox', array(
                'label' => 'Madrugada:',
                'required' => false,
            ))
            ->add('pico', 'checkbox', array(
                'label' => 'Pico:',
                'required' => false,
            ))
            ->add('inventario', 'checkbox', array(
                'label' => 'Inventario:',
                'required' => false,
            ))
            ->add('entrada', 'checkbox', array(
                'label' => 'Entrada:',
                'required' => false,
            ))
            ->add('existencia', 'checkbox', array(
                'label' => 'Existencia:',
                'required' => false,
            ))
            ->add('entrada', 'checkbox', array(
                'label' => 'Entrada:',
                'required' => false,
            ))
            ->add('existencia', 'checkbox', array(
                'label' => 'Existencia:',
                'required' => false,
            ))
            ->add('idunidadmedida', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM:',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'attr' => array('class' => 'form-control chosen-select '),
                'query_builder'=>function(ComunRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo');
                }
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_addportador',
                ),
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomPortador'
        ));
    }
}
