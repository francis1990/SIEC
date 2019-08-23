<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use NomencladorBundle\Entity\ComunRepository;

class NomConversionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('factor', 'text', array(
                'label' => 'Factor:',
                'attr' => array('placeholder' => 'Factor', 'class' => 'form-control number mayorcero focus','maxlength' => '11')
            ))
            ->add('iduminicio', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM inicial:',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('idumfin', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM final:',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'attr' => array('class' => 'form-control chosen-select '),
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_addconversion',
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
            'data_class' => 'NomencladorBundle\Entity\NomConversion'
        ));
    }
}
