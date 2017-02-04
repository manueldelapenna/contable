<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;


class PurchaseOrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('customer', Select2EntityType::class, [
                      'multiple' => false,
                      'required' => true,
                      'remote_route' => 'customer_findall',
                      'class' => '\AppBundle\Entity\Customer',
                      'primary_key' => 'id',
                      'text_property' => 'name',
                      'minimum_input_length' => 2,
                      'allow_clear' => true,
                      'cache' => true,
                      'cache_timeout' => 60000, // if 'cache' is true
                      'placeholder' => 'Nombre del cliente',
                     ])
            ->add('date', DateTimeType::class, [
                                                'widget' => 'single_text',
                                                'format' => 'dd/MM/yyyy hh:mm:ss',
                                                'attr' => [
                                                            'readonly' => 'readonly',

                                                           ]
                                                ])
            ->add('orderState', EntityType::class, array(
                                                        'class' => 'AppBundle:OrderState',
                                                        'placeholder' => 'Seleccione un estado',
                                                        'empty_data'  => null,
                                                        'required' => true
                                                        ))
            ->add('salesPoint', EntityType::class, array(
                                                        'class' => 'AppBundle:SalesPoint',
                                                        'placeholder' => 'Seleccione un punto de venta',
                                                        'empty_data'  => null,
                                                        'required' => true
                                                        ))
            ->add('orderItems', CollectionType::class, array(
                  'entry_type'   => OrderItemType::class,
                  'allow_add' => true,
                  'allow_delete' => true,
                  'prototype' => true,
                  ))
            ->add('subtotal', MoneyType::class, array('currency' => 'USD', 'scale' => 2, 'attr' => array('autocomplete' => 'off', 'readonly' => true, 'class' => 'text-right')))
            ->add('discountAmount', MoneyType::class, array('currency' => 'USD', 'scale' => 2, 'attr' => array('autocomplete' => 'off', 'class' => 'text-right', 'onchange' => 'updateTotals()', 'onkeyup' => 'updateTotals()')))
            ->add('shippingAmount', MoneyType::class, array('currency' => 'USD', 'scale' => 2, 'attr' => array('autocomplete' => 'off', 'class' => 'text-right', 'onchange' => 'updateTotals()', 'onkeyup' => 'updateTotals()')))
            ->add('total', MoneyType::class, array('currency' => 'USD', 'scale' => 2, 'attr' => array('autocomplete' => 'off', 'class' => 'text-right', 'readonly' => true)));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PurchaseOrder'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_purchaseorder';
    }


}
