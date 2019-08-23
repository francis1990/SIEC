<?php

namespace ParteDiarioBundle\Form;

use NomencladorBundle\Entity\NomEntidad;
use NomencladorBundle\Entity\NomGrupointeres;
use ParteDiarioBundle\Entity\DatIncidencia;
use ParteDiarioBundle\Entity\DatVentaProducto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DatParteVentaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('ueb', null, array(
                'label' => 'UEB:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('factura', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Factura:',
                'attr' => array('placeholder' => 'Factura', 'class' => 'form-control focus', 'maxlength' => '20')
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required' => true,
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false,
            ])
            ->add('grupo', 'entity', array(
                'label' => 'Grupo de interés:',
                'required' => true,
                'class'=>'NomencladorBundle\Entity\NomGrupointeres',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('g ')
                        ->where('g.activo = true')
                        ->where('g.identidad is null')
                        ->orderBy('g.nombre');
                },
            ))
            ->add('productos', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'entry_type' => 'ParteDiarioBundle\Form\DatVentaProductoType',
                'prototype_data' => new DatVentaProducto(),
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'by_reference' => false,
                'required' => true,
                'mapped' => true,
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
                    'id' => 'btn_add_pventa',
                ),
            ))
            ->add('importefinalmn', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Importe Final CUP:',
                'required' => false,
                'attr' => array('placeholder' => 'Importe Final', 'class' => 'form-control cincodecimales', 'readonly' => true, 'max' => 9999999999),
                'precision' => 5
            ))
            ->add('importefinalcuc', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Importe Final CUC:',
                'required' => false,
                'attr' => array('placeholder' => 'Importe Final', 'class' => 'form-control cincodecimales', 'readonly' => true, 'max' => 9999999999),
                'precision' => 5

            ));
        if (!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agr_pventa',
                ),
            ));
        $formModifier = function (FormInterface $form, NomGrupointeres $grupo = null) {
            $clientes = null === $grupo ? array() : $GLOBALS['kernel']->getContainer()->get('nomenclador.nomgrupointeres')->getEntidadesHijas( $grupo, array(),true);
            $form->add('cliente', EntityType::class, array(
                    'label' => 'Cliente:',
                    'class' => 'NomencladorBundle:NomEntidad',
                    'required' => true,
                    'placeholder' => 'Seleccione...',
                    'choices' => $clientes,
                    'attr' => array('class' => 'form-control chosen-select select-client')
                ));
        };
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getGrupo());
            }
        );
        $builder->get('grupo')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $grupo = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $grupo);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatParteVenta'
        ));
    }
}
