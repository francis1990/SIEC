<?php

namespace NomencladorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;


class NomEntidadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo', 'text', array(
                'label' => 'REEUP:',
                'attr' => array('placeholder' => 'REEUP', 'class' => 'form-control focus digits', 'maxlength' => '14')
            ))
            ->add('nombre', 'text', array(
                'label' => 'Nombre:',
                'attr' => array('placeholder' => 'Nombre', 'class' => 'form-control', 'maxlength' => '150')))
            ->add('direccion', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                'label' => 'Dirección:',
                'required' => false,
                'attr' => array('placeholder' => 'Dirección', 'class' => 'form-control')
            ))
            ->add('siglas', 'text', array(
                'label' => 'Siglas:',
                'required' => false,
                'attr' => array('placeholder' => 'Siglas', 'class' => 'form-control', 'maxlength' => '10')
            ))
            ->add('iddpa', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Dpa:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomDpa',
                'attr' => array('class' => 'form-control   chosen-select'),
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.prioridad, u.codigo', 'ASC');
                }
            ))
            ->add('idtipoentidad', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Tipo de Entidad:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomTipoEntidad',
                'attr' => array('class' => 'form-control   chosen-select'),
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('te')
                        ->where('te.activo = true')
                        ->orderBy('te.nombre', 'ASC');
                }
            ))
            ->add('vinculo', 'checkbox', array(
                'label' => 'Vínculo:',
                'required' => false,
            ))
            ->add('entsuperior', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Entidad superior:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomEntidad',
                'attr' => array('placeholder' => 'Entidad superior', 'class' => 'form-control chosen-select'),
                'required' => false,
                'mapped' => false
            ))
            ->add('estatal', 'checkbox', array(
                'label' => 'Estatal:',
                'required' => false,
            ))
            ->add('acopio', 'checkbox', array(
                'label' => 'Acopio:',
                'required' => false,
            ))
            ->add('receptor', 'checkbox', array(
                'label' => 'Receptor Leche:',
                'required' => false,
            ))
            ->add('idpadre', 'hidden')
            ->add('diasvencidos', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Días vencidos:',
                'required' => false,
                'attr' => array('placeholder' => 'Días', 'class' => 'form-control digits valorentre0-366')
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_addentidad',
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomEntidad'
        ));
    }
}
