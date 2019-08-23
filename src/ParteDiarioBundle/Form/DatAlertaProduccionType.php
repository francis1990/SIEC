<?php

namespace ParteDiarioBundle\Form;

use AdminBundle\AdminBundle;
use AdminBundle\Entity\Usuario;
use Doctrine\ORM\Mapping\Entity;
use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Tests\Extension\Core\ChoiceList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DatAlertaProduccionType extends AbstractType
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
                'attr' => array('class' => 'js-datepicker read'),
                'html5' => false,
            ))
            ->add('cant', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad',
                'attr' => array(
                    'class' => 'form-control number', 'min' => '0'
                )
            ))
            ->add('producto', null, array(
                'label' => 'Producto',
                'required' => true,
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->innerJoin('u.idgenerico', 'g')
                        ->where('u.activo = true')
                        ->where('g.acopio = false')
                        ->orderBy('u.nombre');
                },
                'attr' => array('class' => 'form-control ')))
            ->add('operador', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => array(
                    '>' => '>',
                    '<' => '<',
                    '=' => '=',
                    '!=' => '!=',
                    '>=' => '>=',
                    '<=' => '<='
                ),
                'attr' => array(
                    'class' => 'form-control chosen-select'
                )
            ))
            ->add('planProduccion', null, array(
                'label' => 'Plan',
                'required' => true,
                'attr' => array('class' => 'form-control'),))
            ->add('moneda', null, array(
                'label' => 'Moneda Destino',
                'required' => true,
                'attr' => array('class' => 'form-control'),))
            ->add('descripcion')
            ->add('usuarios', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'multiple' => true,
                'attr' => array('class' => 'form-control chosen-select'),
                'class' => 'AdminBundle\Entity\Usuario',
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.usuario', 'ASC');
                }
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
