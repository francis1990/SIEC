<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 14/03/2016
 * Time: 12:38
 */

namespace AdminBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatConfigFechaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaTrabajo', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label' => 'Fecha:',
                'widget' => 'single_text',
                'attr' => array('class' => 'js-datepicker read'),
                'html5' => false,
                'required'=>true
            ))  ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\DatConfig'
        ));
    }
}