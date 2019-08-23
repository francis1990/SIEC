<?php

namespace NomencladorBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class NomAlmacenType extends AbstractType
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
                'required' => true,
                'attr' => array('class' => 'form-control digits focus', 'placeholder' => 'código', 'maxlength' => '5')

            ))->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre:',
                'required' => true,
                'attr' => array('class' => 'form-control', 'placeholder' => 'nombre', 'maxLength' => '100')

            ))
            ->add('ueb', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UEB:',
                'class' => 'NomencladorBundle\Entity\NomUeb',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('placeholder' => 'UEB', 'class' => 'form-control chosen-select'),
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('nevera', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'label' => 'Nevera:',
                'required' => false,
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled btn-aceptar",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_alm',
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
            'data_class' => 'NomencladorBundle\Entity\NomAlmacen'
        ));
    }
}
