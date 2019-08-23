<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use NomencladorBundle\Entity\ComunRepository;
use EnumsBundle\Entity\EnumTipoUnidadMedida;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class NomUnidadmedidaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tipos = new EnumTipoUnidadMedida();
        $builder
            ->add('codigo', 'text', array(
                'label' => 'Código:',
                'attr' => array('placeholder' => 'Código', 'class' => 'form-control digits focus', 'maxlength' => '2')
            ))
            ->add('nombre', 'text', array(
                'label' => 'Nombre:',
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control', 'maxlength' => '100')
            ))
            ->add('abreviatura', 'text', array(
                'label' => 'Abreviatura:',
                'attr' => array('placeholder' => 'Abreviatura', 'class' => 'form-control ', 'maxlength' => '10')
            ))
            ->add('idtipoum', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => $tipos->getVolumen(),
                'label' => 'Tipo:',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('cantdecimal', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad decimales:',
                'required' => false,
                'attr' => array('placeholder' => 'Cantidad', 'class' => 'form-control digits', 'min' => 1)
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_addunidadmedida',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomUnidadmedida'
        ));
    }
}
