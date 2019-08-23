<?php

namespace ParteDiarioBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DatParteCuentasCobrarType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idcuentacontable', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Cuenta Contable:',
                'class' => 'NomencladorBundle:NomCuentacontable',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->where('u.porcobrar = true and u.finanzas = true')
                        ->orderBy('u.numero', 'ASC');
                }
            ))
            ->add('montovencido', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Monto vencido:',
                'required' => false,
                'attr' => array('placeholder' => 'Monto vencido', 'class' => 'form-control number mayorcero')
            ))
            ->add('valor', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Valor:',
                'attr' => array('placeholder' => 'Valor', 'class' => 'form-control number mayorcero focus')
            ))
            ->add('diasvencido', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Días vencidos:',
                'required' => false,
                'attr' => array('placeholder' => 'Días vencidos', 'class' => 'form-control digits', 'min' => 0)
            ))

            ->add('cliente', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Cliente:',
                'class' => 'NomencladorBundle:NomEntidad',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('moneda', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Moneda/Destino:',
                'class' => 'NomencladorBundle:NomMonedadestino',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('fecha_reclama', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Fecha reclamación:',
                'required' => false,
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false,
            ])
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false
            ])
            ->add('fechadocumento', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha documento:',
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false
            ])
            ->add('ueb', null, array(
                'label' => 'UEB:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('factura', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Factura:',
                'required' => true,
                'attr' => array('placeholder' => 'Factura', 'class' => 'form-control', 'maxlength' => '50')
            ))
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
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_pccobrar',
                ),
            ));
        if (!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agr_pccobrar',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatParteCuentasCobrar'
        ));
    }
}
