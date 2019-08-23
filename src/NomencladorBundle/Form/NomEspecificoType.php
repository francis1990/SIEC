<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Repository\NomSubgenericoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class NomEspecificoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Código:',
                'attr' => array('placeholder' => 'Código', 'class' => 'form-control focus digits','minlength'=>'2','maxlength'=>'2')
            ))
            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre:',
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control','maxlength' => '100')
            ))
            ->add('idsubgenerico', null, array(
                'label' => 'Sub-genéricos:',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select', 'multiple' => ''),
                'query_builder' => function (NomSubgenericoRepository $ngr) {
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
                    'id' => 'btn-especifico',
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
            'data_class' => 'NomencladorBundle\Entity\NomEspecifico'
        ));
    }
}
