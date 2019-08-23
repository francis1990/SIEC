<?php

namespace ParteDiarioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NomencladorBundle\Entity\ComunRepository;

class DatPlanAcopioDestinoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productos = $options['productos'];
        $builder
            ->add('idtipoplan', null, array(
                'label' => 'Tipo Plan:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('idproducto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType',
                array(
                    'label' => 'Producto:',
                    'placeholder' => 'Seleccione...',
                    'class' => 'NomencladorBundle:NomProducto',
                    'attr' => array('class' => 'form-control  chosen-select'),
                    'choice_attr' => function ($producto) {
                        $data = '';
                        $codigo = $producto->getCodigo();
                        if ($producto->getUmOperativa())
                            $data = $producto->getUmOperativa()->getIdunidadmedida();
                        return array('data-um' => $data, 'data-codigo' => $codigo
                        );
                    },
                    'choices'=>$productos
                ))
            ->add('idueb', null, array(
                'label' => 'UEB:',
                'required' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('idejercicio', null, array(
                'label' => 'Ejercicio:',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select')
            ))
            ->add('idunidadmedida', "entity", array(
                'label' => 'UM:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'attr' => array('class' => 'form-control chosen-select', 'readonly' => true, 'disabled' => 'disabled')))
            ->add('idmonedadestino', "entity", array(
                'label' => 'Moneda/Destino:',
                'placeholder' => 'Seleccione...',
                'required' => false,
                'class' => 'NomencladorBundle:NomMonedadestino',
                'attr' => array('class' => 'form-control chosen-select')))
            ->add('identidad', "entity", array(
                'label' => 'Entidad:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomEntidad',
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder'=>function(ComunRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->where('u.receptor=true');
                }
            ))
            ->add('enero', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'E:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('febrero', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'F:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('marzo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'M:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('abril', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'A:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('mayo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'M:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('junio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'J:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('julio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'J:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('agosto', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'A:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('septiembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'S:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('octubre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'O:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('noviembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'N:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('diciembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'D:',
                'attr' => array('class' => 'form-control number ', 'min' => 0),
               'required'=>false
            ))
            ->add('cantidad', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Cantidad Total:',
                'attr' => array('class' => 'form-control mayorcero  number  focus')
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
            ->add('agregar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Guardar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled",
                    'widget' => 'glyphicon glyphicon-save icon-white',
                    'id' => 'btn_agregar',
                )
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatPlanAcopioDestino',
            'productos' => null
        ));
    }
}
