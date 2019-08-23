<?php

namespace ParteDiarioBundle\Form;

use ParteDiarioBundle\Controller\DatParteDesvioController;
use ParteDiarioBundle\Entity\DatIncidencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DatParteDesvioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $valores = $options['valores'];
        $builder
            ->add('cantidad', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Cantidad:',
                'attr' => array('placeholder' => 'Cantidad', 'class' => 'form-control number ')
            ))
            ->add('destino', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'label' => 'Destino:',
                'choices' => $valores,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true
            ))
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'label' => 'Fecha:',
                'format' => 'dd/MM/yyyy',
                'required'=>true,
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false,
            ])



            ->add('producto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Producto:',
                'class' => 'NomencladorBundle:NomProducto',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true,
                'query_builder'=>function(ComunRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->join('u.idgenerico','g')
                        /*->join('u.idformato','f')
                        ->join('u.idsabor','s')
                        ->join('u.idtipoespecifico','te')*/
                        ->where('u.activo = true and g.acopio = true and u.idformato is null and u.idsabor is null and u.idtipoespecifico is null')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('um', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM:',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'required' => true,
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.idtipoum = 3');
                }
            ))
            ->add('ueb','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'label' => 'UEB:',
                'required' => true,
                'class' => 'NomencladorBundle:NomUeb',
                'placeholder'=>'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
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
                    'id' => 'btn_add_pdesv',
                ),
            ))
        ;
        if(!$options['data']->getIdParte())
            $builder->add('guardar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agr_pdesv',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatParteDesvio',
            'valores' => null,
            'tipo' => null
        ));
    }
}
