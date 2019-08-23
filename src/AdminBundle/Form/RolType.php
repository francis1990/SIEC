<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 14/03/2016
 * Time: 12:38
 */

namespace AdminBundle\Form;


use AdminBundle\Repository\PermisoRepository;
use AdminBundle\Repository\RolRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descRol', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Descripción:',
                'attr' => array('class' => 'form-control focus letras','maxLength'=>'100'
                )))
            ->add('listaPermisos', 'hidden', array('mapped' => false))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled btn-aceptar",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btnInsertarRol',
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
            'data_class' => 'AdminBundle\Entity\Rol'
        ));
    }
}