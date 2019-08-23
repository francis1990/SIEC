<?php

namespace ParteDiarioBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatVentaProductoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('origen', "Symfony\Bridge\Doctrine\Form\Type\EntityType", array(
                'label' => 'del Vínculo:',
                'class' => 'NomencladorBundle:NomEntidad',
                'placeholder' => 'Seleccione...',

                'required' => true,
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('e')
                        ->where('e.vinculo = 1');
                },
                'attr' => array('title' => 'Del Vínculo', 'class' => 'form-control chosen-select chosen-select-embebed')
            ))
            ->add('producto', "Symfony\Bridge\Doctrine\Form\Type\EntityType", array(
                'label' => 'Producto:',
                'class' => 'NomencladorBundle:NomProducto',
                'placeholder' => 'Seleccione...',

                'required' => true,
                'attr' => array('title' => 'Producto', 'class' => 'form-control chosen-select select-product chosen-select-embebed'),
                'choice_attr' => function ($producto) {
                    $data = '';
                    $formato = $producto->getIdformato();
                    if ($formato != null)
                        $data = $producto->getIdformato()->getIdunidadmedida()->getIdunidadmedida();
                    return array('data-um' => $data);
                },
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->innerJoin('u.idgenerico', 'g')
                        ->where('u.activo = true')
                        ->where('g.acopio = false')
                        ->where('u.hoja = true');
                }
            ))
            ->add('almacen', "Symfony\Bridge\Doctrine\Form\Type\EntityType", array(
                'label' => 'Almacén:',
                'class' => 'NomencladorBundle:NomAlmacen',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('title' => 'Almacén', 'class' => 'form-control chosen-select chosen-select-embebed')
            ))
            ->add('cantfisica', "Symfony\Component\Form\Extension\Core\Type\NumberType", array(
                'label' => 'Cantidad:',
                'required' => true,
                'attr' => array('class' => 'form-control product-cant number tresdecimales', 'max' => 9999999999),
                'precision' => 3
            ))
            ->add('preciomn', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Precio CUP:',
                'required' => false,
                'attr' => array('title' => 'Precio CUP', 'class' => 'form-control number cincodecimales', 'readonly' => true),
                'precision' => 5
            ))
            ->add('importemn', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Importe CUP:',
                'required' => false,
                'attr' => array('title' => 'Importe CUP', 'class' => 'form-control number impuestomn cincodecimales', 'readonly' => true, 'max' => 9999999999),
                'precision' => 5
            ))
            ->add('importecuc', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Importe CUC:',
                'required' => false,
                'attr' => array('title' => 'Importe CUC', 'class' => 'form-control number impuestocuc cincodecimales', 'readonly' => true, 'max' => 9999999999),
                'precision' => 5
            ))->add('preciocuc', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Precio CUC:',
                'required' => false,
                'attr' => array('title' => 'Precio CUC', 'class' => 'form-control digits cincodecimales', 'readonly' => true),
                'precision' => 5
            ))
            ->add('impuesto', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => false,
                'attr' => array('title' => 'Impuesto', 'class' => 'form-control number cincodecimales', 'readonly' => true),
                'precision' => 5
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatVentaProducto'
        ));
    }
}
