<?php

namespace ParteDiarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatPlanPortadorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idtipoplan', null, array(
                'label' => 'Tipo de Plan:',
                'required' => true,
                'attr' => array('placeholder' => '', 'class' => 'form-control chosen-select')
            ))
            ->add('idportador', "entity", array(
                'label' => 'Portador:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomPortador',
                'choice_attr' => function ($portador) {
                    $data = '';
                    if ($portador->getIdunidadmedida())
                        $data = $portador->getIdunidadmedida()->getIdunidadmedida();
                    return array('data-um' => $data
                    );
                },
                'attr' => array('class' => 'form-control chosen-select')))
            ->add('idueb', null, array(
                'label' => 'UEB:',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('idejercicio', null, array(
                'label' => 'Ejercicio:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('idunidadmedida', "entity", array(
                'label' => 'UM:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'attr' => array('class' => 'form-control chosen-select', 'readonly' => true, 'disabled' => 'disabled')))
            ->add('cantidad', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Cantidad Total:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number mayorcero  focus')
            ))
            ->add('enero', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'E:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('febrero', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'F:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('marzo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'M:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('abril', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'A:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('mayo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'M:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('junio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'J:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('julio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'J:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('agosto', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'A:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('septiembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'S:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('octubre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'O:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('noviembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'N:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('diciembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'D:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number ', 'min' => 0),
                'required'=>false
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_aceptar',
                ),
            ))
            ->add('agregar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => 'Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agregar',
                )
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatPlanPortador'
        ));
    }
}
