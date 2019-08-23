<?php

namespace NomencladorBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use NomencladorBundle\Repository\NomGenericoRepository;
use NomencladorBundle\Repository\NomSubgenericoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;


class NomProductoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accion', 'hidden', array(
                'mapped' => false,
            ))
            ->add('codigo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Código:',
                'required' => true,
                'attr' => array('class' => 'form-control digits focus readonly', 'placeholder' => 'Código', 'disabled' => 'disabled')
            ))
            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre:',
                'required' => true,
                'attr' => array('class' => 'form-control readonly', 'placeholder' => 'Nombre', 'disabled' => 'disabled')
            ))
            ->add('factor', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Factor conversión:',
                'required' => false,
                'attr' => array('placeholder' => 'Factor', 'class' => 'form-control number')
            ))
            ->add('codOnei', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Código ONEI:',
                'required' => false,
                'attr' => array('placeholder' => 'Código ONEI', 'class' => 'form-control codigoOnei', 'maxlength' => 20)
            ))
            ->add('umOperativa', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'UM oficial:',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder'=>function(ComunRepository $ngr){
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo');
                }
            ))
            ->add('idgenerico', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Génerico:',
                'class' => 'NomencladorBundle:NomGenerico',
                'attr' => array('class' => 'form-control focus '),
                'choice_attr' => function ($idgenerico) {
                    return array('data-val' => $idgenerico->getCodigo()
                    );
                },
                'query_builder' => function (NomGenericoRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }
            ))
            ->add('idsubgenerico', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Sub-genérico:',
                'class' => 'NomencladorBundle:NomSubgenerico',//chosen-select
                'attr' => array('class' => 'form-control'),
                'required' => false,
                'choice_attr' => function ($idsubgenerico) {
                    return array('data-val' => $idsubgenerico->getCodigo(),
                        'data-aso' => $idsubgenerico->getGenerico()->getIdgenerico()
                    );
                },
                'query_builder' => function (NomSubgenericoRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('idespecifico', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Específico:',
                'required' => false,
                'class' => 'NomencladorBundle:NomEspecifico',
                'attr' => array('class' => 'form-control',
                    'data-val' => 'idespecifico'),
                'choice_attr' => function ($idespecifico) {
                    return array('data-val' => $idespecifico->getCodigo());
                },
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('idtipoespecifico', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Tipo Específico:',
                'required' => false,
                'class' => 'NomencladorBundle:NomTipoespecifico',
                'attr' => array('class' => 'form-control'),
                'choice_attr' => function ($idtipoespecifico) {
                    return array('data-val' => $idtipoespecifico->getCodigo(),
                    );
                },
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('idsabor', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Sabor:',
                'required' => false,
                'class' => 'NomencladorBundle:NomSabor',
                'attr' => array('class' => 'form-control'),
                'choice_attr' => function ($idsabor) {
                    return array('data-val' => $idsabor->getCodigo());
                },
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('idformato', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Formato:',
                'required' => false,
                'choice_attr' => function ($idformato) {
                    return array('data-val' => $idformato->getCodigo());
                },
                'class' => 'NomencladorBundle:NomFormato',
                'attr' => array('class' => 'form-control '),
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.activo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('aceptar', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => ' Aceptar',
                'label_format' => 'btn-add',
                'attr' => array(
                    'class' => "btn btn-primary  disabled btn-aceptar",
                    'widget' => 'glyphicon glyphicon-ok icon-white',
                    'id' => 'btn_add_producto',
                    'title' => ' Aceptar'
                ),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NomencladorBundle\Entity\NomProducto'
        ));
    }
}
