<?php

namespace ParteDiarioBundle\Form;

use NomencladorBundle\Entity\ComunRepository;
use NomencladorBundle\Entity\NomGrupointeres;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VentaAlertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\DateType', [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control js-datepicker read'],
                'html5' => false,
            ])
            ->add('entidad', null, array(
                'label' => 'Empresa/UEB',
                'placeholder' => 'Seleccione...',
                'empty_data' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select')))
            ->add('grupo_interes', null, array(
                'label' => 'Grupo de interés',
                'placeholder' => 'Seleccione...',
                'empty_data' => 'Seleccione...',
                'required' => true,
                'query_builder' => function (ComunRepository $ngr) {
                    return $ngr->createQueryBuilder('g ')
                        ->where('g.activo = true')
                        ->where('g.identidad is null')
                        ->orderBy('g.nombre');
                },
                'attr' => array('class' => 'form-control chosen-select')))
            ->add('cant', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'label' => 'Porciento',
                'attr' => array(
                    'class' => 'form-control ', 'min' => 0
                )
            ))
            ->add('operador', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'placeholder' => 'Seleccione...',
                'choices' => array(
                    '>' => '>',
                    '<' => '<',
                    '=' => '=',
                    '<>' => '<>',
                    '>=' => '>=',
                    '<=' => '<='
                ),
                'attr' => array(
                    'class' => 'form-control chosen-select'
                )
            ])
            ->add('descripcion', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                'label' => 'Texto a Mostrar',
                'attr' => array(
                    'class' => 'form-control focus'
                )
            ))
            ->add('usuarios', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'multiple' => true,
                'placeholder' => 'Seleccione...',
                'attr' => array('class' => 'chosen-select'),
                'class'=>'AdminBundle\Entity\Usuario'
            ))
            ->add('producto', null, array(
                'label' => 'Producto',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'query_builder' => function (ComunRepository  $ngr) {
                    return $ngr->createQueryBuilder('u ')
                        ->innerJoin('u.idgenerico', 'g')
                        ->where('u.activo = true')
                        ->where('g.acopio = false')
                        ->orderBy('u.nombre');
                },
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('tipo_plan', null, array(
                'label' => 'Tipo de Plan',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('ejercicio', null, array(
                'label' => 'Ejercicio',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('moneda', null, array(
                'label' => 'Moneda/Destino',
                'placeholder' => 'Seleccione...',
                'required' => true,
                'attr' => array('class' => 'form-control chosen-select'),))
            ->add('periodo', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'placeholder' => 'Seleccione...',
                'label'=>'Período',
                'choices' => array(
                    'hoy' => 'Hoy',
                    'mes' => 'Mes',
                    'mes_fecha' => 'Acumulado del Mes',
                    'year_fecha' => 'Acumulado del Año hasta la fecha'
                ),
                'attr' => array(
                    'class' => 'form-control chosen-select'
                )

            ]);
            $formModifier = function (FormInterface $form, NomGrupointeres $grupo = null) {
            $clientes = null === $grupo ? array() : $GLOBALS['kernel']->getContainer()->get('nomenclador.nomgrupointeres')->getEntidadesHijas( $grupo, array(),true);
            $form->add('cliente', EntityType::class, array(
                'label' => 'Cliente:',
                'class' => 'NomencladorBundle:NomEntidad',
                'required' => false,
                'placeholder' => 'Seleccione...',
                'choices' => $clientes,
                'attr' => array('class' => 'form-control chosen-select')
            ));
        };
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getGrupoInteres());
            }
        );
        $builder->get('grupo_interes')->addEventListener(
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
            'data_class' => 'ParteDiarioBundle\Entity\DatAlerta'
        ));
    }
}
