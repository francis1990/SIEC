<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 14/03/2016
 * Time: 12:38
 */

namespace AdminBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatConfigEntidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreEntidad', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre:',
                'required' => true,
                'attr' => array('class' => 'form-control letras')
            ))
            ->add('reup_entidad', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'REEUP:',
                'required' => false,
                'attr' => array('class' => 'form-control number', 'minlength' => '2', 'maxlength' => '9')
            ))
            ->add('direccion', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Dirección:',
                'required' => false,
                'attr' => array('class' => 'form-control'),
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_insert_entidad',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\DatConfig'
        ));
    }
}