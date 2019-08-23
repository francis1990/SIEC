<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class NomDpaType extends AbstractType
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
                'attr' => array('placeholder' => 'Código', 'class' => 'form-control focus digits',
                    'maxlength' => '6')
            ))
            ->add('nombre', 'text', array(
                'label' => 'Nombre:',
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control')
            ))
            ->add('padre', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Dpa Superior:',
                'class' => 'NomencladorBundle:NomDpa',
                'placeholder' => 'Seleccione...',
                'required' => false,
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true and u.nivel = 0')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_adddpa',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomDpa'
        ));
    }
}
