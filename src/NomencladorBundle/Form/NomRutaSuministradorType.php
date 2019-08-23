<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class NomRutaSuministradorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('producto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'required' => true,
                'label' => 'Producto:',
                'class' => 'NomencladorBundle:NomProducto',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select prod-select'),
                'query_builder'=>function(ComunRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->join('u.idgenerico','g')
                        ->where('u.hoja = true and u.activo = true and g.acopio = true')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('entidad', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'required' => true,
                'label' => 'Suministradores:',
                'class' => 'NomencladorBundle:NomEntidad',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select ent-select'),
                'query_builder'=>function(ComunRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->where('u.hoja = true and u.acopio=true');
                }
            ))
           ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomRutaSuministrador'
        ));
    }
}
