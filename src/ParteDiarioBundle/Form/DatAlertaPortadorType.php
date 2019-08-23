<?php

namespace ParteDiarioBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DatAlertaPortadorType extends AbstractType
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
                'attr' => array('class' => 'form-control js-datepicker read'),
                'html5' => false,
            ))
            ->add('entidad', null, array(
                'label' => 'Empresa/UEB',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select')))
            ->add('cant', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad',
                'attr' => array(
                    'class' => 'form-control number', 'min' => '0'
                )
            ))
            ->add('portador', null, array(
                'label' => 'Portador',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select')))
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
                    'class' => 'form-control focus'
                )
            ))
            ->add('tipo_plan', null, array(
                'label' => 'Tipo de Plan',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select campo_oculto')))
            ->add('ejercicio', null, array(
                'label' => 'Ejercicio',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select campo_oculto')))
            ->add('periodo', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'placeholder' => 'Seleccione...',
                'label' => 'Período',
                'choices' => array(
                    'hoy' => 'Hoy',
                    'mes' => 'Mes',
                    'mes_fecha' => 'Acumulado del Mes',
                    'year_fecha' => 'Acumulado del Año hasta la fecha'
                ),
                'attr' => array(
                    'class' => 'form-control chosen-select campo_oculto'
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
            ->add('consumo_inventario', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'placeholder' => 'Seleccione...', 'label' => 'Calcular',
                'choices' => array(
                    'consumo' => 'Consumo',
                    'inventario' => 'Inventario',
                    'porciento' => 'Porciento',
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
