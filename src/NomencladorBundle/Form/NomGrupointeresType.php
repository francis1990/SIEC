<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use NomencladorBundle\Entity\ComunRepository;

class NomGrupointeresType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $labelBtn = " Adicionar";
        if ($options['data']->getIdgrupointeres()) {
            $labelBtn = " Editar";
        }
        $builder
            ->add('codigo', 'text', array(
                'label' => 'Código:',
                'attr' => array('placeholder' => 'Código', 'class' => 'form-control focus digits', 'maxlength' => '12')
            ))
            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre:',
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control', 'maxlength' => '100')
            ))
            ->add('idpadre', 'hidden', array(
                'mapped' => false
            ))
            ->add('grupos', 'hidden', array(
                'mapped' => false
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => $labelBtn,
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_agregrupo',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomGrupointeres'
        ));
    }
}
