<?php

namespace ParteDiarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatIncidenciaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if(!$options['by_parte'])
            $builder
                ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                    'widget' => 'single_text',
                    'label' => 'Fecha:',
                    'format' => 'dd/MM/yyyy',
                    'required'=>true,
                    'attr' => ['class' => 'form-control js-datepicker read required'],
                    'html5' => true,
                    'mapped' => true,
                ])

                ->add('entidad', null, array(
                    'label' => 'UEB',
                    'placeholder' => 'Seleccione...',
                    'required' => true,
                    'attr' => array('class' => 'form-control chosen-select chosen-select-embebed required'),
                    ));

        $builder
            ->add('descripcion','Symfony\Component\Form\Extension\Core\Type\TextareaType',array(
                'label' => 'Texto a Mostrar',
                'attr' => array(
                    'class' => 'form-control required text_descrip_incid focus',
                    'max' => '200',
                    'rows' => 1,
                )
            ))

            ->add('idcasificacion',null,array(
                'label' => 'Clasificación',
                'placeholder' => 'Seleccione...',
                'required'=>true,
                'attr'=>array('class'=>'form-control chosen-select chosen-select-embebed')))

            ->add('idtipo',null,array(
                'label' => 'Tipo',
                'placeholder' => 'Seleccione...',
                'required'=>true,
                'attr'=>array('class'=>'form-control chosen-select chosen-select-embebed'),))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatIncidencia',
            'by_parte' => false,
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dat_incidencias';
    }
}
