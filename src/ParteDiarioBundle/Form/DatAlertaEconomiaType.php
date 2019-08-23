<?php

namespace ParteDiarioBundle\Form;

use AdminBundle\AdminBundle;
use AdminBundle\Entity\Usuario;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Tests\Extension\Core\ChoiceList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DatAlertaEconomiaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker read'],
                'html5' => false,
            ))
            ->add('entidad', null, array(
                'label' => 'UEB',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.nombre', 'ASC');
                }
            ))
            ->add('cant', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Valor',
                'attr' => array(
                    'class' => 'form-control number',
                    'min' => '0'
                )
            ))
            ->add('operador', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => array(
                    '>' => '>',
                    '<' => '<',
                    '=' => '=',
                    '<>' => '<>',
                    '>=' => '>=',
                    '<=' => '<='
                ),
                'attr' => array(
                    'class' => 'form-control chosen-select'
                )
            ))
            ->add('descripcion', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                'label' => 'Texto a Mostrar',
                'attr' => array(
                    'class' => 'form-control textarea tag focus '
                )
            ))
            ->add('usuarios', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'multiple' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'chosen-select'),
                'class' => 'AdminBundle\Entity\Usuario',
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.usuario', 'ASC');
                }
            ))
            ->add('cuenta', null, array(
                'label' => 'Cuenta',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('vencidaCuenta', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'placeholder' => 'Seleccione...', 'label' => 'Cuenta/Vencida',
                'choices' => array(
                    'vencida' => 'Vencida',
                    'cuenta' => 'Cuenta',
                ),
                'attr' => array(
                    'class' => 'form-control chosen-select'
                )
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatAlerta'
        ));
    }
}
