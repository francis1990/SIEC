<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UsuarioType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('usuario', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Usuario',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control alfanumerico focus','maxLength' => '50'
                )
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Debe coincidir el valor de la contraseña.',
                'options' => array('attr' => array('class' => 'form-control')),
                'required' => true,
                'first_options' => array('label' => 'Contraseña:', 'attr'=> array('minLength' => 6, 'class' => 'form-control validarPassword')),
                'second_options' => array('label' => 'Repita Contraseña:')
            ))
            ->add('correo', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
                'label' => 'Correo',
                'required' => true,
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('rol', null, array(
                'label' => 'Rol',

                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('ueb', null, array(
                'label' => 'Ueb',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))

            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btnInsertarUsuario',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\Usuario'
        ));
    }
}