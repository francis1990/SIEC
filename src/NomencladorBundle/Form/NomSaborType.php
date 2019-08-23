<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Repository\NomSubgenericoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class NomSaborType extends AbstractType
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
                'attr' => array('class' => 'form-control digits focus',/*'minlength'=>'3',*/'maxlength'=>'3','placeholder'=>'Código')

            ))
            ->add('nombre','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Nombre:',
                'required' => true,
                'attr' => array('class' => 'form-control','placeholder'=>'nombre','maxlength' => '100')

            ))
            ->add('idsubgenerico',null,array(
                'label' => 'Sub-génericos:',
                'required' => true,
                'attr' => array('placeholder'=>'Sub-génerico','class' => 'form-control chosen-select', 'multiple'=>'true'),
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
                    'id' => 'btn_add_sabor',
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
            'data_class' => 'NomencladorBundle\Entity\NomSabor'
        ));
    }
}
