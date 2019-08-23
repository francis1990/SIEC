<?php

namespace ParteDiarioBundle\Form;

use NomencladorBundle\Entity\NomGrupointeres;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use NomencladorBundle\Entity\ComunRepository;

class DatPlanVentaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productos = $options['productos'];
        $builder
            ->add('grupopadre', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Destino:',
                'class' => 'NomencladorBundle:NomGrupointeres',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select')))
            ->add('idproducto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Producto:',
                'class' => 'NomencladorBundle:NomProducto',
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select'),
                'choice_attr' => function ($producto) {
                    $data = '';
                    $codigo = $producto->getCodigo();
                    if ($producto->getUmOperativa() != null)
                        $data = $producto->getUmOperativa()->getIdunidadmedida();
                    return array('data-um' => $data, 'data-codigo' => $codigo
                    );
                },
                'choices' => $productos
            ))
            ->add('idmonedadestino', "entity", array(
                'label' => 'Moneda/Destino:',
                'required' => false,
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomMonedadestino',
                'attr' => array('class' => 'form-control chosen-select')))
            ->add('identidad', "entity", array(
                'label' => 'Origen:',
                'required' => false,
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomEntidad',
                'attr' => array('class' => 'form-control chosen-select'),
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('u')
                        ->where('u.vinculo = true')
                        ->orderBy('u.codigo', 'ASC');
                }))
            ->add('idunidadmedida', "entity", array(
                'label' => 'UM:',
                'placeholder' => 'Seleccione...',
                'class' => 'NomencladorBundle:NomUnidadmedida',
                'attr' => array('class' => 'form-control chosen-select', 'readonly' => true, 'disabled' => 'disabled')))
            ->add('idueb', null, array(
                'label' => 'UEB:',
                'placeholder' => 'Seleccione...',
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
                'required' => false, 'label' => 'E:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('febrero', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'F:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('marzo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'M:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('abril', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'A:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('mayo', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'M:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('junio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'J:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('julio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'J:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('agosto', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'A:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('septiembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'S:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('octubre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'O:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('noviembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'N:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
            ))
            ->add('diciembre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false, 'label' => 'D:',
                'attr' => array('placeholder' => '', 'class' => 'form-control number', 'min' => 0)
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
            ))
            ->add('valor', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Cantidad total:',
                'attr' => array( 'class' => 'form-control mayorcero number focus')
            ))
            ->add('is_val', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'label' => 'Es valor:',
                'required' => false,
            ))
            ->add('idgrupocliente', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Entidad:',
                'class' => 'NomencladorBundle:NomGrupointeres',
                'required' => false,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'form-control chosen-select select-client')
            ));
        $formModifier = function (FormInterface $form, NomGrupointeres $grupo = null) {
            $clientes = null === $grupo ? array() : $GLOBALS['kernel']->getContainer()->get('nomenclador.nomgrupointeres')->getEntidadesHijas($grupo, array(), true, true);

            $form->add('grupoentidad', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'label' => 'Entidad:',
                'class' => 'NomencladorBundle:NomGrupointeres',
                'required' => false,
                'placeholder' => 'Seleccione...',
                'choices' => $clientes,
                'attr' => array('class' => 'form-control chosen-select select-client')
            ));
        };
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getGrupopadre());
            }
        );
        $builder->get('grupopadre')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $grupo = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $grupo);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParteDiarioBundle\Entity\DatPlanVenta',
            'productos' => null
        ));
    }
}
