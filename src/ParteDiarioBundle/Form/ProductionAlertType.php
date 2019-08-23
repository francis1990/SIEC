<?php

namespace ParteDiarioBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductionAlertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false,
            ])
            ->add('entidad', null, array(
                'label' => 'UEB',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('usuarios', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'multiple' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'chosen-select'),
                'class' => 'AdminBundle\Entity\Usuario'
            ))
            ->add('moneda', null, array(
                'label' => 'Moneda/Destino',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('tipo_plan', null, array(
                'label' => 'Tipo de Plan',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('ejercicio', null, array(
                'label' => 'Ejercicio',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('producto', null, array(
                'label' => 'Producto',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->innerJoin('u.idgenerico', 'g')
                        ->where('u.activo = true')
                        ->where('g.acopio = false')
                        ->orderBy('u.nombre');
                },
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('operador', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'placeholder' => 'Seleccione...',
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
            ])
            ->add('periodo', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'placeholder' => 'Seleccione...',
                'label'=>'Período',
                'choices' => array(
                    'hoy' => 'Hoy',
                    'mes' => 'Mes',
                    'mes_fecha' => 'Acumulado del Mes',
                    'year_fecha' => 'Acumulado del Año hasta la fecha'
                ),
                'attr' => array(
                    'class' => 'form-control chosen-select'
                )
            ])
            ->add('cant', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Porciento',
                'attr' => array(
                    'class' => 'form-control', 'min' => 0
                )
            ))
            ->add('descripcion', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                'label' => 'Texto a Mostrar',
                'attr' => array(
                    'class' => 'form-control focus'
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
