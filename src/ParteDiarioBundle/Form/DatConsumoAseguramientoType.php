<?php

namespace ParteDiarioBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use ParteDiarioBundle\Form\DataTransformer\DatNormaAseguramientoTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatConsumoAseguramientoType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('realbruto', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => false,
                'attr' => array('class' => 'form-control number ochodecimales'
                ),
                'scale' => 8
            ))
            ->add('sng', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => false,
                'attr' => array('class' => 'form-control number ochodecimales'
                ),
                'scale' => 8
            ))
            ->add('grasa', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => false,
                'attr' => array('class' => 'form-control number ochodecimales'
                ),
                'scale' => 8
            ))
            ->add('aseguramiento', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'attr' => array('style' => 'display: none'),
                'label' => false
            ));
        $builder->get('aseguramiento')
            ->addModelTransformer(new DatNormaAseguramientoTransformer($this->manager));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatConsumoAseguramiento'
        ));
    }
}
