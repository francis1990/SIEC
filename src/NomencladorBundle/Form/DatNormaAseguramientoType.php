<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatNormaAseguramientoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('aseguramiento', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'required' => true,
                'label' => 'UM Norma:',
                'class' => 'NomencladorBundle:NomAseguramiento',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select select-aseg'),
                'choice_attr' => function ($aseguramiento) {
                    return array('data-um' => $aseguramiento->getIdunidadmedida()->getIdunidadmedida()
                    );
                },
                'query_builder' => function ($ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('umaseg', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'required' => true,
                'label' => 'Unidad de Medida:',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'placeholder' => 'UM',
                'attr' => array('class' => 'form-control chosen-select', 'disabled' => 'disabled')
            ))
            ->add('moneda', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'required' => true,
                'label' => 'Unidad de Medida:',
                'class' => 'NomencladorBundle:NomMonedadestino',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('cantaseg', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Norma Bruta:',
                'required' => true,
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('norma_neta', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Norma Neta:',
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('perdida', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => '% de Pérdida:',
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('cantaseg_grasa', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Norma Bruta:',
                'required' => true,
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('norma_neta_grasa', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Norma Neta:',
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('perdida_grasa', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => '% de Pérdida:',
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('cantaseg_sng', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Norma Bruta:',
                'required' => true,
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('norma_neta_sng', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Norma Neta:',
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
            ->add('perdida_sng', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => '% de Pérdida:',
                'attr' => array( 'class' => 'form-control focus number ochodecimales')
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\DatNormaAseguramiento'
        ));
    }
}
