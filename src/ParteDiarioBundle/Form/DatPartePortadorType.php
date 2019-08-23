<?php

namespace ParteDiarioBundle\Form;

use ParteDiarioBundle\Entity\DatIncidencia;
use ParteDiarioBundle\Entity\DatPartePortadorMedidor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DatPartePortadorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('portador', null, array(
                'label' => 'Portador:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'choice_attr' => function ($portador) {
                    return array(
                        'data-alcance' => ($portador->getAlcance() == true) ? 1 : 0,
                        'data-dia' => ($portador->getDia() == true) ? 1 : 0,
                        'data-madrugada' => ($portador->getMadrugada() == true) ? 1 : 0,
                        'data-pico' => ($portador->getPico() == true) ? 1 : 0,
                        'data-inventario' => ($portador->getInventario() == true) ? 1 : 0,
                        'data-entrada' => ($portador->getEntrada() == true) ? 1 : 0,
                        'data-existencia' => ($portador->getExistencia() == true) ? 1 : 0,
                        'data-um' => ($portador->getIdunidadmedida() != null) ? $portador->getIdunidadmedida()->getIdunidadmedida() : 0,
                    );
                },
                'attr' => array('class' => 'form-control chosen-select required')
            ))
            ->add('um', null, array(
                'label' => 'Unidad de medida:',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select', 'disabled' => true)
            ))
            ->add('ueb', null, array(
                'label' => 'UEB:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select required')
            ))
            ->add('pico', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Pico:',
                'required' => false,
                'attr' => array('placeholder' => 'Pico', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('madrugada', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Madrugada:',
                'required' => false,
                'attr' => array('placeholder' => 'Madrugada', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('alcance', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Alcance (días):',
                'required' => false,
                'attr' => array('placeholder' => 'Alcance', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('inventario', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Inventario:',
                'required' => false,
                'attr' => array('placeholder' => 'Inventario', 'class' => 'form-control number', 'min' => 0)
            ))->add('existencia', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Existencia:',
                'required' => false,
                'attr' => array('placeholder' => 'Existencia', 'class' => 'form-control number focus', 'min' => 0)
            ))->add('entrada', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Entrada:',
                'required' => false,
                'attr' => array('placeholder' => 'Entrada', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'attr' => ['class' => 'form-control js-datepicker read required'],
                'html5' => false,
            ])
            ->add('incidencias', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'ParteDiarioBundle\Form\DatIncidenciaType',
                'prototype_data' => new DatIncidencia(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'error_bubbling' => false,
                'entry_options' => array(
                    'by_parte' => true,
                ),
            ))
            ->add('listMedidor', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'ParteDiarioBundle\Form\DatPartePortadorMedidorType',
                'prototype_data' => new DatPartePortadorMedidor(),
                'label' => 'Medidores',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => true,
                'error_bubbling' => false,
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_port_aceptar',
                ),
            ));

        if (!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_port_agregar',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatPartePortador'
        ));
    }
}
