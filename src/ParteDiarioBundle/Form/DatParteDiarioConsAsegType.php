<?php

namespace ParteDiarioBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomUeb;
use ParteDiarioBundle\Entity\DatConsumoAseguramiento;
use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;

class DatParteDiarioConsAsegType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nivelact', "Symfony\Component\Form\Extension\Core\Type\NumberType", array(
                'label' => 'Nivel Actividad:',
                'required' => false,
                'attr' => array('class' => 'form-control idnivelactv', 'readonly' => true)
            ))
            ->add('ueb', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UEB:',
                'required' => true,
                'class' => 'NomencladorBundle:NomUeb',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select required')
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'attr' => array('class' => 'form-control js-datepicker read'),
                'html5' => false,
            ))
            ->add('consumos', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'ParteDiarioBundle\Form\DatConsumoAseguramientoType',
                'prototype_data' => new DatConsumoAseguramiento(),
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'by_reference' => false,
                'required' => false,
                'mapped' => true,
            ))
            ->add('tiponorma', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Tipo NC:',
                'attr' => array('class' => 'form-control chosen-select'),
                'class' => 'NomencladorBundle:NomTipoNorma',
                'placeholder' => 'Seleccione...',
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->where('u.activo = true')
                        ->orderBy('u.nombre');
                }
            ))
            ->add('cantidadxnc', "Symfony\Component\Form\Extension\Core\Type\NumberType", array(
                'label' => 'Cantidad x NC',
                'required' => false,
                'attr' => array('class' => 'form-control tresdecimales')
            ))
            ->add('grasa', "Symfony\Component\Form\Extension\Core\Type\NumberType", array(
                'label' => 'Grasa',
                'required' => false,
                'attr' => array('class' => 'form-control ochodecimales'),
                'scale' => 8
            ))
            ->add('sng', "Symfony\Component\Form\Extension\Core\Type\NumberType", array(
                'label' => 'SNG',
                'required' => false,
                'attr' => array('class' => 'form-control ochodecimales'),
                'scale' => 8
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
                    'id' => 'btn_add_pconsaseg',
                ),
            ));

        if (!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agregar_pconsaseg',
                ),
            ));
        $formModifier = function (FormInterface $form, $ueb = null, $fecha = null/*, $tiponorma = null*/) {
            $productos = null == $ueb || $fecha == null ? array() : $GLOBALS['kernel']->getContainer()->get('parte_diarionivel_actv')->getProdNivelActv(
                array('idueb' => $ueb, 'fecha' => $fecha/*, 'tiponorma' => $tiponorma*/));
            $form->add('producto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Producto:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomProducto',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select required'),
                'choices' => $productos
            ));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getUeb() != null ? $data->getUeb()->getIdueb() : null,
                    $data->getFecha() != "" && $data->getFecha() != 0 ? $data->getFecha() : null/*,
                    $data->getTipoNorma() != null ? $data->getTipoNorma()->getId() : null*/);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data['ueb'], $data['fecha']/*, $data['tiponorma']*/);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatParteDiarioConsAseg',
            'productos' => null
        ));
    }
}
