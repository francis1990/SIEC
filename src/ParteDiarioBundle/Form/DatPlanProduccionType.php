<?php

namespace ParteDiarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use NomencladorBundle\Entity\ComunRepository;


class DatPlanProduccionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productos = $options['productos'];
        $builder
            ->add('idproducto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Producto:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomProducto',
                'attr' => array('class' => 'form-control chosen-select'),
                'choice_attr' => function ($producto) {
                    $data = '';
                    $codigo = $producto->getCodigo();
                    if ($producto->getUmOperativa() != null) {
                        $data = $producto->getUmOperativa()->getIdunidadmedida();
                    }
                    return array(
                        'data-um' => $data,
                        'data-codigo' => $codigo
                    );
                },
                'choices' => $productos
            ))
            ->add('idmonedadestino', "entity", array(
                'label' => 'Moneda/Destino:',
                'class' => 'NomencladorBundle:NomMonedadestino',
                'placeholder' => 'Seleccione...',
                'required' => false,
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('idunidadmedida', "entity", array(
                'label' => 'UM:',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select', 'readonly' => true, 'disabled' => 'disabled')
            ))
            ->add('idueb', null, array(
                'label' => 'UEB:',
                'placeholder' => 'Seleccione...',
                'required'=> true,
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('idtipoplan', null, array(
                'label' => 'Tipo de Plan:',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('placeholder' => '', 'class' => 'form-control chosen-select')
            ))
            ->add('idejercicio', null, array(
                'label' => 'Ejercicio:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('enero', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'E:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('febrero', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'F:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('marzo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'M:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('abril', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'A:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('mayo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'M:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('junio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'J:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('julio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'J:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('agosto', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'A:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('septiembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'S:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('octubre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'O:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('noviembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'N:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('diciembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'D:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number ',
                    'min' => 0
                ),
               'required'=>false
            ))
            ->add('cantidad', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Cantidad Total:',
                'attr' => array(
                    'placeholder' => '',
                    'class' => 'form-control number mayorcero  focus'
                )
            ))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_aceptar',
                ),
            ))
            ->add('excluir', 'hidden')
            ->add('agregar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agregar',
                )
            )

            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatPlanProduccion',
            'productos' => null
        ));
    }
}
